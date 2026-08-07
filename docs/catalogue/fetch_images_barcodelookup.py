#!/usr/bin/env python3
"""
Fetch product images for the FULL catalogue from the Barcode Lookup API,
and build a WooCommerce re-import that attaches them by SKU.

The products are already in WooCommerce (SKU = EAN). This does NOT touch
products -- it only produces an image-attachment CSV you re-import with
"Update existing products" ticked, matched on SKU. Keeping images out of
the original product import is deliberate: bundling image downloads into
the product import is what stalled the first import run.

USAGE
    export BARCODE_LOOKUP_KEY=your_key_here
    # smoke test first -- 40 lookups, proves the run end to end:
    python3 docs/catalogue/fetch_images_barcodelookup.py --limit 40 --download
    # then the full run:
    python3 docs/catalogue/fetch_images_barcodelookup.py

The key is read from the environment (or --key) and never written to any
output or committed file. Do not paste it into the CSV or the repo.

NOTES
- No `formatted=y`: the provider says not to use it for API calls.
- Resumable: every response is cached to barcode-images-cache.jsonl, so a
  re-run costs zero lookups for barcodes already seen. If the plan's call
  quota is hit (HTTP 429), the run stops cleanly and resumes next time --
  it is never recorded as "no image".
- The Barcode Lookup plan has a finite number of calls. The catalogue is
  ~13,907 products. If the plan is smaller, the run fills what it can,
  caches it, and you resume when the quota resets.

OUTPUTS (in this folder)
  products-images-import.csv        SKU,Images  -- the WooCommerce re-import
  products-images-chunks/part-*.csv same, split into <=2500-row chunks
  products-still-missing-images.csv barcodes with no image, for photography
  barcode-images-report.md          coverage + cost summary
  barcode-images/                   downloaded images (only with --download)
  barcode-images-cache.jsonl        raw responses (resume-safe, git-ignored)
"""
import argparse
import csv
import json
import os
import re
import ssl
import sys
import time
import urllib.error
import urllib.parse
import urllib.request

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(HERE, "products-woo-import-FINAL.csv")
CACHE = os.path.join(HERE, "barcode-images-cache.jsonl")
IMPORT_CSV = os.path.join(HERE, "products-images-import.csv")
CHUNK_DIR = os.path.join(HERE, "products-images-chunks")
MISSING = os.path.join(HERE, "products-still-missing-images.csv")
REPORT = os.path.join(HERE, "barcode-images-report.md")
IMGDIR = os.path.join(HERE, "barcode-images")

API = "https://api.barcodelookup.com/v3/products"
USER_AGENT = "SmartPharmacy-catalogue/1.0 (oliver@conversionlab.io)"
TIMEOUT = 15
DELAY = 0.3
MAX_RETRIES = 3
CHUNK_SIZE = 2500


class AuthError(Exception):
    """Credentials / quota problem — abort, never count as 'no image'."""


def _get(url, timeout=TIMEOUT):
    req = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    return urllib.request.urlopen(req, timeout=timeout, context=ssl.create_default_context())


def lookup(barcode, key):
    """
    Return {images:[...], title, category, error}.
    Raises AuthError on 401/403/quota so a credentials issue can never be
    silently recorded as a product having no image.
    """
    qs = urllib.parse.urlencode({"barcode": barcode, "key": key})
    url = f"{API}?{qs}"
    for attempt in range(MAX_RETRIES):
        try:
            with _get(url) as r:
                payload = json.load(r)
            products = payload.get("products") or []
            if not products:
                return {"images": [], "title": "", "category": "", "error": ""}
            p = products[0]
            return {
                "images": [u for u in (p.get("images") or []) if u],
                "title": p.get("title", ""),
                "category": p.get("category", ""),
                "error": "",
            }
        except urllib.error.HTTPError as e:
            if e.code == 404:
                return {"images": [], "title": "", "category": "", "error": ""}
            if e.code in (401, 403):
                raise AuthError(f"HTTP {e.code} — check BARCODE_LOOKUP_KEY / plan status.")
            if e.code == 429:
                if attempt == MAX_RETRIES - 1:
                    raise AuthError("HTTP 429 — call quota exhausted. Progress is cached; "
                                    "resume when the quota resets.")
                time.sleep(2 ** attempt * 2)
                continue
            if attempt == MAX_RETRIES - 1:
                return {"images": [], "title": "", "category": "", "error": f"http_{e.code}"}
            time.sleep(2 ** attempt)
        except (urllib.error.URLError, TimeoutError, ValueError, OSError) as e:
            if attempt == MAX_RETRIES - 1:
                return {"images": [], "title": "", "category": "", "error": type(e).__name__}
            time.sleep(2 ** attempt)
    return {"images": [], "title": "", "category": "", "error": "retries_exhausted"}


def download(url, dest):
    try:
        with _get(url) as r:
            data = r.read()
        if not data:
            return False
        with open(dest, "wb") as f:
            f.write(data)
        return True
    except (urllib.error.URLError, urllib.error.HTTPError, TimeoutError, OSError):
        return False


def load_products():
    rows = []
    with open(SRC, newline="", encoding="utf-8") as f:
        r = csv.reader(f)
        header = next(r)
        i_sku = header.index("SKU")
        i_name = header.index("Name")
        for row in r:
            if row and len(row) > i_sku and row[i_sku].strip():
                rows.append({"ean": row[i_sku].strip(),
                             "name": row[i_name].strip() if len(row) > i_name else ""})
    return rows


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


def main():
    ap = argparse.ArgumentParser(description="Fetch all product images via Barcode Lookup.")
    ap.add_argument("--key", default=os.environ.get("BARCODE_LOOKUP_KEY", ""))
    ap.add_argument("--limit", type=int, default=0,
                    help="max NEW lookups this run (0 = all). Use a small value to smoke-test.")
    ap.add_argument("--all-images", action="store_true",
                    help="put every image in the gallery (default: first image only).")
    ap.add_argument("--download", action="store_true",
                    help="also save images to barcode-images/ for quality-checking.")
    args = ap.parse_args()

    if not args.key:
        sys.exit("No API key. Do:  export BARCODE_LOOKUP_KEY=your_key_here")

    products = load_products()
    cache = load_cache()
    todo = [p for p in products if p["ean"] not in cache]
    if args.limit:
        todo = todo[:args.limit]

    print(f"Catalogue : {len(products):,} products")
    print(f"Cached    : {len(cache):,} already looked up (free)")
    print(f"This run  : {len(todo):,} new lookups"
          f"{' (limited)' if args.limit else ''}\n")

    if args.download:
        os.makedirs(IMGDIR, exist_ok=True)

    calls = 0
    found = 0
    try:
        for n, p in enumerate(todo, 1):
            rec = lookup(p["ean"], args.key)
            rec["ean"] = p["ean"]
            calls += 1
            if rec["images"]:
                found += 1
            with open(CACHE, "a", encoding="utf-8") as f:
                f.write(json.dumps(rec) + "\n")

            if args.download and rec["images"]:
                safe = re.sub(r"[^A-Za-z0-9._-]", "", p["ean"]) or f"row{n}"
                ext = os.path.splitext(urllib.parse.urlparse(rec["images"][0]).path)[1][:5] or ".jpg"
                dest = os.path.join(IMGDIR, f"{safe}{ext}")
                if not os.path.exists(dest):
                    download(rec["images"][0], dest)

            time.sleep(DELAY)
            if n % 50 == 0 or n == len(todo):
                print(f"  {n:>5}/{len(todo)}  images found: {found} ({100*found/n:.0f}%)")
    except AuthError as e:
        print(f"\nSTOPPED: {e}", file=sys.stderr)
        print("Partial results are cached — re-run to resume.", file=sys.stderr)
        build_outputs(products, load_cache(), args, calls, stopped=True)
        sys.exit(2)
    except KeyboardInterrupt:
        print("\nInterrupted — progress cached, re-run to resume.", file=sys.stderr)
        build_outputs(products, load_cache(), args, calls, stopped=True)
        sys.exit(130)

    build_outputs(products, load_cache(), args, calls, stopped=False)


def build_outputs(products, cache, args, calls, stopped):
    rows_with = []
    missing = []
    for p in products:
        rec = cache.get(p["ean"])
        if rec is None:
            continue  # not looked up yet (partial run)
        imgs = rec.get("images") or []
        if imgs:
            images = ",".join(imgs) if args.all_images else imgs[0]
            rows_with.append((p["ean"], images))
        else:
            missing.append((p["ean"], p["name"], rec.get("error", "")))

    # Main import CSV
    with open(IMPORT_CSV, "w", newline="", encoding="utf-8") as f:
        w = csv.writer(f)
        w.writerow(["SKU", "Images"])
        w.writerows(rows_with)

    # Chunks for safe re-import
    os.makedirs(CHUNK_DIR, exist_ok=True)
    for old in os.listdir(CHUNK_DIR):
        if old.startswith("part-") and old.endswith(".csv"):
            os.remove(os.path.join(CHUNK_DIR, old))
    for i in range(0, len(rows_with), CHUNK_SIZE):
        part = rows_with[i:i + CHUNK_SIZE]
        with open(os.path.join(CHUNK_DIR, f"part-{i // CHUNK_SIZE + 1:02d}.csv"),
                  "w", newline="", encoding="utf-8") as f:
            w = csv.writer(f)
            w.writerow(["SKU", "Images"])
            w.writerows(part)

    # Still-missing list
    with open(MISSING, "w", newline="", encoding="utf-8") as f:
        w = csv.writer(f)
        w.writerow(["barcode", "name", "error"])
        w.writerows(missing)

    looked_up = len([p for p in products if p["ean"] in cache])
    got = len(rows_with)
    cov = 100 * got / looked_up if looked_up else 0

    lines = [
        "# Barcode Lookup — full image fetch\n",
        ("**Run stopped early (quota/interrupt) — this is a partial result. "
         "Re-run to continue.**\n" if stopped else ""),
        f"- Catalogue: **{len(products):,}** products",
        f"- Looked up so far: **{looked_up:,}**",
        f"- Images found: **{got:,}**  ({cov:.1f}% of looked-up)",
        f"- Still missing: **{looked_up - got:,}**",
        f"- API calls this run: **{calls:,}**\n",
        "## Next step",
        "Re-import `products-images-chunks/part-*.csv` in WooCommerce → Products → "
        "Import, mapping `Images` to Images and **ticking \"Update existing products\"** "
        "(match on SKU). Do the chunks one at a time; each sideloads its image batch "
        "without touching product data.\n",
        f"`products-still-missing-images.csv` ({len(missing):,} rows) is the list that "
        "needs photography or another source.\n",
    ]
    with open(REPORT, "w", encoding="utf-8") as f:
        f.write("\n".join(x for x in lines if x != "") + "\n")

    print(f"\n{'='*54}")
    print(f"  Looked up : {looked_up:,}/{len(products):,}")
    print(f"  Images    : {got:,}  ({cov:.1f}%)")
    print(f"  Missing   : {looked_up - got:,}")
    print(f"{'='*54}")
    print(f"Import CSV -> {IMPORT_CSV}")
    print(f"Chunks     -> {CHUNK_DIR}/ ({(len(rows_with)+CHUNK_SIZE-1)//CHUNK_SIZE} files)")
    print(f"Missing    -> {MISSING}")
    print(f"Report     -> {REPORT}")


if __name__ == "__main__":
    main()
