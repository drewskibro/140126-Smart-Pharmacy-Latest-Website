#!/usr/bin/env python3
"""
Fetch product images by barcode (ClickUp card: "Fetch product images by
barcode"). Open Food Facts + Open Beauty Facts, EAN-keyed.

RUN THIS ON A MACHINE WITH NORMAL INTERNET. The Claude Code sandbox
blocks outbound access to these APIs, so it has to be run from your own
terminal:

    python3 docs/catalogue/fetch_images.py

Resumable: every lookup is cached to products-image-cache.jsonl, so if
it's interrupted (or you re-run after new products), it skips what's
already done. Be patient — ~14k barcodes with a polite delay takes a
while.

Outputs (in this folder):
  products-woo-images.csv     SKU,Images  — re-import into WooCommerce to
                              attach the found images by SKU (barcode)
  products-missing-images.csv Barcode,Product name,Regulation — the ones
                              with no image found, for Murtaza / photography

Note: these databases skew to groceries, supplements and cosmetics, so
expect good coverage on the everyday range and low coverage on actual
medicines. Images are community-contributed under open licences (check
attribution before commercial use).
"""
import csv
import json
import os
import time
import urllib.request
import urllib.error

HERE = os.path.dirname(os.path.abspath(__file__))
SRC = os.path.join(HERE, "products-clean.csv")
CACHE = os.path.join(HERE, "products-image-cache.jsonl")
FOUND = os.path.join(HERE, "products-woo-images.csv")
MISSING = os.path.join(HERE, "products-missing-images.csv")

USER_AGENT = "SmartPharmacy-catalogue/1.0 (oliver@conversionlab.io)"
HOSTS = ["world.openfoodfacts.org", "world.openbeautyfacts.org"]
DELAY = 0.2      # seconds between lookups (be polite)
TIMEOUT = 8


def lookup(barcode):
    """Return an image URL for a barcode, or '' if none found."""
    for host in HOSTS:
        url = f"https://{host}/api/v2/product/{barcode}.json?fields=image_front_url,image_url"
        req = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
        try:
            with urllib.request.urlopen(req, timeout=TIMEOUT) as r:
                d = json.load(r)
            if d.get("status") == 1:
                p = d.get("product", {})
                img = p.get("image_front_url") or p.get("image_url")
                if img:
                    return img
        except (urllib.error.URLError, urllib.error.HTTPError, TimeoutError, ValueError):
            continue
    return ""


def load_cache():
    cache = {}
    if os.path.exists(CACHE):
        with open(CACHE, encoding="utf-8") as f:
            for line in f:
                try:
                    rec = json.loads(line)
                    cache[rec["ean"]] = rec["url"]
                except (ValueError, KeyError):
                    pass
    return cache


def main():
    with open(SRC, newline="", encoding="utf-8") as f:
        rows = [r for r in csv.DictReader(f) if r.get("ean", "").strip()]

    cache = load_cache()
    todo = [r for r in rows if r["ean"] not in cache]
    print(f"{len(rows)} products, {len(cache)} cached, {len(todo)} to look up.")

    with open(CACHE, "a", encoding="utf-8") as cf:
        for i, r in enumerate(todo, 1):
            ean = r["ean"]
            url = lookup(ean)
            cache[ean] = url
            cf.write(json.dumps({"ean": ean, "url": url}) + "\n")
            cf.flush()
            if i % 100 == 0:
                got = sum(1 for v in cache.values() if v)
                print(f"  {i}/{len(todo)}  running hits: {got}")
            time.sleep(DELAY)

    # Write outputs from the full cache.
    found = missing = 0
    with open(FOUND, "w", newline="", encoding="utf-8") as ff, \
         open(MISSING, "w", newline="", encoding="utf-8") as mf:
        fw = csv.writer(ff)
        mw = csv.writer(mf)
        fw.writerow(["SKU", "Images"])
        mw.writerow(["Barcode", "Product name", "Regulation"])
        for r in rows:
            url = cache.get(r["ean"], "")
            if url:
                fw.writerow([r["ean"], url])
                found += 1
            else:
                mw.writerow([r["ean"], r["name"], r["regulation"]])
                missing += 1

    total = found + missing
    print(f"\nDone. Images found: {found}/{total} ({found * 100 // total}%)")
    print(f"-> {os.path.basename(FOUND)}  (re-import to attach images by SKU)")
    print(f"-> {os.path.basename(MISSING)}  ({missing} for Murtaza / photography)")


if __name__ == "__main__":
    main()
