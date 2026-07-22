#!/usr/bin/env python3
"""
Barcode Lookup API — paid-image-source VALIDATION run.

Purpose: spend a few pounds to answer three questions before committing to
the full 13,907-product catalogue:

    1. COVERAGE  — what % of our products return a usable image?
    2. QUALITY   — are those images big enough to sell with?
    3. COST      — what does it cost per 1,000, and for the full range?

RUN THIS ON A MACHINE WITH NORMAL INTERNET. The Claude Code sandbox blocks
outbound API access, so run it from your own terminal:

    export BARCODE_LOOKUP_KEY=your_key_here
    python3 docs/catalogue/validate_barcode_images.py --sample 400

Get a key at https://www.barcodelookup.com/api

WHY A SAMPLE, NOT THE FULL RANGE
A 400-product sample estimates catalogue coverage to about +/-5 percentage
points at 95% confidence. That is precise enough to make a scale-or-stop
decision, for ~3% of the cost of running everything.

WHY STRATIFIED
Skincare + Vitamins alone are 41% of the catalogue. A naive random sample
would mostly measure those two and tell us little about Prescription
Medicines or Eye Care. So we sample every category, then re-weight each
category back to its true share when computing the headline number. The
report shows both the raw sample rate and the weighted catalogue estimate.

SAFETY
Resumable: every API response is cached to a .jsonl, so re-runs cost
nothing for barcodes already looked up. Auth/quota failures ABORT rather
than being recorded as "no image found" — silently counting a bad API key
as zero coverage would produce a confidently wrong recommendation.

Outputs (in this folder):
  barcode-validation-report.md     the three numbers, for the client
  barcode-validation-detail.csv    row per sampled product
  barcode-validation-images/       the downloaded images, to eyeball
  barcode-validation-cache.jsonl   raw responses (resume-safe, git-ignored)
"""
import argparse
import csv
import json
import math
import os
import random
import re
import ssl
import struct
import sys
import time
import urllib.error
import urllib.parse
import urllib.request

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(HERE, "products-woo-import-FINAL.csv")
CACHE = os.path.join(HERE, "barcode-validation-cache.jsonl")
IMGDIR = os.path.join(HERE, "barcode-validation-images")
REPORT = os.path.join(HERE, "barcode-validation-report.md")
DETAIL = os.path.join(HERE, "barcode-validation-detail.csv")

API = "https://api.barcodelookup.com/v3/products"
USER_AGENT = "SmartPharmacy-catalogue/1.0 (oliver@conversionlab.io)"
TIMEOUT = 15
DELAY = 0.35          # be polite between calls
MAX_RETRIES = 3

# A product image needs to be reasonably large to sell with. WooCommerce
# single-product images render ~600px wide, and zoom wants more.
GOOD_PX = 800         # min side >= this  -> "good"
USABLE_PX = 400       # min side >= this  -> "usable"


# --------------------------------------------------------------------------
# Image dimensions without Pillow (stdlib only, like the rest of the pipeline)
# --------------------------------------------------------------------------
def image_dimensions(data):
    """(width, height) from raw image bytes, or (0, 0) if unreadable."""
    try:
        # PNG
        if data[:8] == b"\x89PNG\r\n\x1a\n":
            w, h = struct.unpack(">II", data[16:24])
            return w, h
        # GIF
        if data[:6] in (b"GIF87a", b"GIF89a"):
            w, h = struct.unpack("<HH", data[6:10])
            return w, h
        # WEBP
        if data[:4] == b"RIFF" and data[8:12] == b"WEBP":
            chunk = data[12:16]
            if chunk == b"VP8X":
                w = int.from_bytes(data[24:27], "little") + 1
                h = int.from_bytes(data[27:30], "little") + 1
                return w, h
            if chunk == b"VP8 ":
                return struct.unpack("<HH", data[26:30])[0] & 0x3FFF, \
                       struct.unpack("<HH", data[28:32])[0] & 0x3FFF
            if chunk == b"VP8L":
                b = data[21:25]
                n = int.from_bytes(b, "little")
                return (n & 0x3FFF) + 1, ((n >> 14) & 0x3FFF) + 1
        # JPEG — walk the segment markers to the start-of-frame
        if data[:2] == b"\xff\xd8":
            i, n = 2, len(data)
            while i < n - 9:
                if data[i] != 0xFF:
                    i += 1
                    continue
                marker = data[i + 1]
                # SOF0..SOF15, excluding DHT/JPG/DAC
                if marker in (0xC0, 0xC1, 0xC2, 0xC3, 0xC5, 0xC6, 0xC7,
                              0xC9, 0xCA, 0xCB, 0xCD, 0xCE, 0xCF):
                    h, w = struct.unpack(">HH", data[i + 5:i + 9])
                    return w, h
                if marker in (0xD8, 0xD9) or 0xD0 <= marker <= 0xD7:
                    i += 2
                    continue
                seglen = struct.unpack(">H", data[i + 2:i + 4])[0]
                i += 2 + seglen
    except (struct.error, IndexError, ValueError):
        pass
    return 0, 0


# --------------------------------------------------------------------------
# API
# --------------------------------------------------------------------------
class AuthError(Exception):
    """Bad key, exhausted quota, or forbidden — must abort, never count as a miss."""


def _get(url, timeout=TIMEOUT):
    req = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    ctx = ssl.create_default_context()
    return urllib.request.urlopen(req, timeout=timeout, context=ctx)


def lookup(barcode, key):
    """
    Look a barcode up.

    Returns a dict: {found, title, brand, category, images[], error}
    Raises AuthError on 401/403/429-exhausted so a credentials problem can
    never masquerade as "this product has no image".
    """
    qs = urllib.parse.urlencode({"barcode": barcode, "formatted": "y", "key": key})
    url = f"{API}?{qs}"

    for attempt in range(MAX_RETRIES):
        try:
            with _get(url) as r:
                payload = json.load(r)
            products = payload.get("products") or []
            if not products:
                return {"found": False, "images": [], "error": ""}
            p = products[0]
            return {
                "found": True,
                "title": p.get("title", "") or p.get("product_name", ""),
                "brand": p.get("brand", ""),
                "category": p.get("category", ""),
                "images": [u for u in (p.get("images") or []) if u],
                "error": "",
            }

        except urllib.error.HTTPError as e:
            code = e.code
            # 404 genuinely means "no product for this barcode".
            if code == 404:
                return {"found": False, "images": [], "error": ""}
            # These are OUR problem, not the product's. Abort loudly.
            if code in (401, 403):
                raise AuthError(
                    f"HTTP {code} from Barcode Lookup. Check BARCODE_LOOKUP_KEY "
                    f"and that the plan has calls remaining."
                )
            if code == 429:
                if attempt == MAX_RETRIES - 1:
                    raise AuthError(
                        "HTTP 429 rate limit / quota exhausted after retries. "
                        "Stopping so the run is not recorded as zero coverage."
                    )
                time.sleep(2 ** attempt * 2)
                continue
            if attempt == MAX_RETRIES - 1:
                return {"found": False, "images": [], "error": f"http_{code}"}
            time.sleep(2 ** attempt)

        except (urllib.error.URLError, TimeoutError, ValueError, OSError) as e:
            if attempt == MAX_RETRIES - 1:
                return {"found": False, "images": [], "error": type(e).__name__}
            time.sleep(2 ** attempt)

    return {"found": False, "images": [], "error": "retries_exhausted"}


def download(url, dest):
    """Download an image. Returns (bytes, width, height) or (0, 0, 0)."""
    try:
        with _get(url, timeout=TIMEOUT) as r:
            data = r.read()
        if not data:
            return 0, 0, 0
        w, h = image_dimensions(data)
        with open(dest, "wb") as f:
            f.write(data)
        return len(data), w, h
    except (urllib.error.URLError, urllib.error.HTTPError, TimeoutError, OSError):
        return 0, 0, 0


# --------------------------------------------------------------------------
# Sampling
# --------------------------------------------------------------------------
def load_products():
    rows = []
    with open(SRC, newline="", encoding="utf-8") as f:
        r = csv.reader(f)
        header = next(r)
        i_sku = header.index("SKU")
        i_name = header.index("Name")
        i_cat = header.index("Categories")
        i_reg = next(i for i, x in enumerate(header) if "regulation" in x.lower())
        for row in r:
            if not row or len(row) <= i_reg:
                continue
            rows.append({
                "ean": row[i_sku].strip(),
                "name": row[i_name].strip(),
                "cat": row[i_cat].strip() or "(uncategorised)",
                "reg": row[i_reg].strip(),
            })
    return rows


def stratified_sample(rows, target, min_per_cat, seed):
    """
    Proportional-with-a-floor sample across categories.

    The floor means tiny categories (Weight Management has 9 products) still
    produce a signal. That deliberately over-samples them relative to the
    catalogue, which is why the report re-weights by true category share for
    the headline figure.
    """
    rnd = random.Random(seed)
    by_cat = {}
    for r in rows:
        by_cat.setdefault(r["cat"], []).append(r)

    total = len(rows)
    picked = []
    for cat, items in by_cat.items():
        share = len(items) / total
        want = max(min_per_cat, int(round(target * share)))
        want = min(want, len(items))
        picked.extend(rnd.sample(items, want))

    rnd.shuffle(picked)
    return picked


def load_cache():
    cache = {}
    if os.path.exists(CACHE):
        with open(CACHE, encoding="utf-8") as f:
            for line in f:
                try:
                    rec = json.loads(line)
                    cache[rec["ean"]] = rec
                except (ValueError, KeyError):
                    pass
    return cache


def wilson(k, n):
    """
    95% Wilson score interval — behaves sensibly at extreme rates and small
    n, unlike the normal approximation (which can give a negative lower
    bound at, say, 2 hits out of 30).
    """
    if n == 0:
        return 0.0, 0.0, 0.0
    z = 1.959963985
    p = k / n
    d = 1 + z * z / n
    centre = (p + z * z / (2 * n)) / d
    half = z * math.sqrt(p * (1 - p) / n + z * z / (4 * n * n)) / d
    return p, max(0.0, centre - half), min(1.0, centre + half)


# --------------------------------------------------------------------------
def main():
    ap = argparse.ArgumentParser(description="Validate Barcode Lookup image coverage.")
    ap.add_argument("--sample", type=int, default=400,
                    help="target sample size (default 400, ~+/-5%% at 95%%)")
    ap.add_argument("--min-per-cat", type=int, default=5,
                    help="minimum products sampled per category (default 5)")
    ap.add_argument("--seed", type=int, default=20260710,
                    help="RNG seed, so the sample is reproducible")
    ap.add_argument("--cost-per-1000", type=float, default=None,
                    help="your plan's price per 1,000 lookups, for the projection")
    ap.add_argument("--key", default=os.environ.get("BARCODE_LOOKUP_KEY", ""),
                    help="API key (better: export BARCODE_LOOKUP_KEY)")
    ap.add_argument("--no-download", action="store_true",
                    help="measure coverage only, skip downloading images")
    args = ap.parse_args()

    if not args.key:
        sys.exit("No API key. Do:  export BARCODE_LOOKUP_KEY=your_key_here\n"
                 "Get one at https://www.barcodelookup.com/api")

    rows = load_products()
    sample = stratified_sample(rows, args.sample, args.min_per_cat, args.seed)
    cache = load_cache()
    os.makedirs(IMGDIR, exist_ok=True)

    # True catalogue share per category, for re-weighting.
    cat_totals = {}
    for r in rows:
        cat_totals[r["cat"]] = cat_totals.get(r["cat"], 0) + 1

    print(f"Catalogue : {len(rows):,} products")
    print(f"Sample    : {len(sample):,} across {len(cat_totals)} categories")
    print(f"Cached    : {sum(1 for s in sample if s['ean'] in cache):,} (free to re-run)")
    print(f"To call   : {sum(1 for s in sample if s['ean'] not in cache):,}\n")

    results = []
    calls = 0
    try:
        for n, item in enumerate(sample, 1):
            ean = item["ean"]
            if ean in cache:
                rec = cache[ean]
            else:
                rec = lookup(ean, args.key)
                rec["ean"] = ean
                calls += 1
                with open(CACHE, "a", encoding="utf-8") as f:
                    f.write(json.dumps(rec) + "\n")
                time.sleep(DELAY)

            img_url = (rec.get("images") or [""])[0]
            size = w = h = 0
            if img_url and not args.no_download:
                safe = re.sub(r"[^A-Za-z0-9._-]", "", ean) or f"row{n}"
                ext = os.path.splitext(urllib.parse.urlparse(img_url).path)[1][:5] or ".jpg"
                dest = os.path.join(IMGDIR, f"{safe}{ext}")
                if os.path.exists(dest):
                    size = os.path.getsize(dest)
                    with open(dest, "rb") as f:
                        w, h = image_dimensions(f.read())
                else:
                    size, w, h = download(img_url, dest)

            results.append({**item, "found": bool(img_url), "url": img_url,
                            "bytes": size, "w": w, "h": h,
                            "api_title": rec.get("title", ""),
                            "error": rec.get("error", "")})

            if n % 25 == 0 or n == len(sample):
                hits = sum(1 for x in results if x["found"])
                print(f"  {n:>4}/{len(sample)}  images so far: {hits} ({100*hits/n:.1f}%)")

    except AuthError as e:
        print(f"\nABORTED: {e}", file=sys.stderr)
        print("Nothing has been miscounted — partial results are cached and "
              "re-running will resume.", file=sys.stderr)
        sys.exit(2)
    except KeyboardInterrupt:
        print("\nInterrupted — progress is cached, re-run to resume.", file=sys.stderr)
        sys.exit(130)

    write_outputs(results, cat_totals, len(rows), calls, args)


def write_outputs(results, cat_totals, catalogue_n, calls, args):
    n = len(results)
    hits = sum(1 for r in results if r["found"])
    raw_p, lo, hi = wilson(hits, n)

    # Re-weight to true catalogue composition.
    by_cat = {}
    for r in results:
        d = by_cat.setdefault(r["cat"], {"n": 0, "hit": 0})
        d["n"] += 1
        d["hit"] += 1 if r["found"] else 0
    weighted = sum((d["hit"] / d["n"]) * (cat_totals[c] / catalogue_n)
                   for c, d in by_cat.items() if d["n"])

    sized = [r for r in results if r["found"] and r["w"] and r["h"]]
    good = sum(1 for r in sized if min(r["w"], r["h"]) >= GOOD_PX)
    usable = sum(1 for r in sized if USABLE_PX <= min(r["w"], r["h"]) < GOOD_PX)
    small = sum(1 for r in sized if min(r["w"], r["h"]) < USABLE_PX)

    with open(DETAIL, "w", newline="", encoding="utf-8") as f:
        w = csv.writer(f)
        w.writerow(["barcode", "name", "category", "regulation",
                    "image_found", "width", "height", "bytes",
                    "image_url", "api_title", "error"])
        for r in results:
            w.writerow([r["ean"], r["name"], r["cat"], r["reg"],
                        "yes" if r["found"] else "no", r["w"], r["h"],
                        r["bytes"], r["url"], r["api_title"], r["error"]])

    proj = int(round(weighted * catalogue_n))
    L = []
    L.append("# Barcode Lookup — image coverage validation\n")
    L.append(f"Sample of **{n:,}** products, stratified across "
             f"**{len(by_cat)}** categories, from a catalogue of "
             f"**{catalogue_n:,}**. Seed `{args.seed}` (reproducible).\n")

    L.append("## 1. Coverage\n")
    L.append(f"- **Catalogue-weighted estimate: {weighted*100:.1f}%** "
             f"— the headline number, correcting for the fact that small "
             f"categories are deliberately over-sampled.")
    L.append(f"- Raw sample rate: {raw_p*100:.1f}% ({hits:,}/{n:,}), "
             f"95% CI {lo*100:.1f}%–{hi*100:.1f}%.")
    L.append(f"- **Projected images across the full range: ~{proj:,} "
             f"of {catalogue_n:,} products.**")
    L.append(f"- Projected still without an image: ~{catalogue_n - proj:,}.\n")

    L.append("## 2. Quality\n")
    if sized:
        L.append(f"Of {len(sized):,} images measured:\n")
        L.append(f"| Bucket | Count | Share |")
        L.append(f"|---|---:|---:|")
        L.append(f"| Good (>={GOOD_PX}px min side) | {good:,} | {100*good/len(sized):.1f}% |")
        L.append(f"| Usable ({USABLE_PX}-{GOOD_PX}px) | {usable:,} | {100*usable/len(sized):.1f}% |")
        L.append(f"| Too small (<{USABLE_PX}px) | {small:,} | {100*small/len(sized):.1f}% |")
        avg = sum(r["bytes"] for r in sized) / len(sized) / 1024
        L.append(f"\nAverage file size {avg:.0f} KB. "
                 f"Images saved to `barcode-validation-images/` — eyeball them "
                 f"before scaling; correct-but-ugly is still a problem.\n")
    else:
        L.append("_No images downloaded (ran with --no-download)._\n")

    L.append("## 3. Cost\n")
    L.append(f"- API calls made this run: **{calls:,}** "
             f"(cached lookups were free).")
    if args.cost_per_1000 is not None:
        c = args.cost_per_1000
        L.append(f"- At £{c:.2f} per 1,000: this run cost about "
                 f"**£{calls * c / 1000:.2f}**.")
        L.append(f"- Full catalogue ({catalogue_n:,} lookups): about "
                 f"**£{catalogue_n * c / 1000:.2f}**.")
        if proj:
            L.append(f"- That works out at about **£{catalogue_n * c / 1000 / proj:.3f} "
                     f"per image actually obtained**.")
    else:
        L.append("- _Re-run with `--cost-per-1000 X` to get the cost projection._")

    L.append("\n## Coverage by category\n")
    L.append("| Category | Catalogue | Sampled | Images | Rate |")
    L.append("|---|---:|---:|---:|---:|")
    for c in sorted(by_cat, key=lambda x: -cat_totals[x]):
        d = by_cat[c]
        L.append(f"| {c} | {cat_totals[c]:,} | {d['n']} | {d['hit']} | "
                 f"{100*d['hit']/d['n']:.0f}% |")

    L.append("\n## Coverage by regulation\n")
    by_reg = {}
    for r in results:
        d = by_reg.setdefault(r["reg"] or "(none)", {"n": 0, "hit": 0})
        d["n"] += 1
        d["hit"] += 1 if r["found"] else 0
    L.append("| Regulation | Sampled | Images | Rate |")
    L.append("|---|---:|---:|---:|")
    for k, d in sorted(by_reg.items(), key=lambda kv: -kv[1]["n"]):
        L.append(f"| {k} | {d['n']} | {d['hit']} | {100*d['hit']/d['n']:.0f}% |")

    errs = [r for r in results if r["error"]]
    if errs:
        L.append(f"\n> {len(errs)} lookups errored (network/HTTP, not "
                 f"'no image'). They are counted as misses, so the true "
                 f"coverage may be marginally higher.\n")

    with open(REPORT, "w", encoding="utf-8") as f:
        f.write("\n".join(L) + "\n")

    print(f"\n{'='*58}")
    print(f"  Weighted coverage : {weighted*100:.1f}%  (~{proj:,} of {catalogue_n:,})")
    if sized:
        print(f"  Good quality      : {100*good/len(sized):.0f}% of images found")
    print(f"  API calls used    : {calls:,}")
    print(f"{'='*58}")
    print(f"\nReport  -> {REPORT}")
    print(f"Detail  -> {DETAIL}")
    print(f"Images  -> {IMGDIR}/")


if __name__ == "__main__":
    main()
