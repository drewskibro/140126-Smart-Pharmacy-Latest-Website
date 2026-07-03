#!/usr/bin/env python3
"""
Turn products-clean.csv into WooCommerce Product CSV Importer files.

Run after clean_products.py. Produces, in this folder:
  products-woo-import-sample.csv   100-row representative sample (sign-off)
  products-woo-import-full.csv     all rows (bulk, after sign-off)

Column mapping (WooCommerce's importer recognises these headers):
  ean         -> SKU
  name        -> Name
  price       -> Regular price      (blank stays blank -> not purchasable)
  vat rate    -> Tax class          0.2->Standard("") · 0.05->reduced-rate · 0->zero-rate
  regulation  -> Meta: _sp_regulation  (parked for later POM gating)

Deliberate defaults (change here if needed):
  Published = 0  -> imported as DRAFT (nothing goes live until you publish)
  In stock? = 0, Stock = 0  -> matches "stock 0 on import, activate manually"
  Type = simple, Tax status = taxable

The actual VAT %s are configured later in WooCommerce → Settings → Tax;
this only assigns each product to the right tax *class*.
"""
import csv
import os
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, HERE)
from classify_categories import classify  # noqa: E402  (same-folder helper)
from describe_products import describe    # noqa: E402  (same-folder helper)
SRC = os.path.join(HERE, "products-clean.csv")
FULL = os.path.join(HERE, "products-woo-import-full.csv")
SAMPLE = os.path.join(HERE, "products-woo-import-sample.csv")
SAMPLE_SIZE = 100

HEADERS = [
    "Type",
    "SKU",
    "Name",
    "Published",
    "In stock?",
    "Stock",
    "Regular price",
    "Tax status",
    "Tax class",
    "Categories",
    "Description",
    "Images",
    "Meta: _sp_regulation",
]

TAX_CLASS = {
    "0.2": "",             # Standard rate (WooCommerce slug is empty)
    "0.20": "",
    "0": "zero-rate",
    "0.0": "zero-rate",
    "0.05": "reduced-rate",
}


def load_images():
    """SKU -> image URL from products-woo-images.csv, if the image fetch
    has been run. Restricted/in-store barcodes (prefix 2) are skipped —
    Open Food Facts returns junk matches for dummy codes like 2222222222222."""
    path = os.path.join(HERE, "products-woo-images.csv")
    out = {}
    if os.path.exists(path):
        with open(path, newline="", encoding="utf-8") as f:
            for r in csv.DictReader(f):
                sku = (r.get("SKU") or "").strip()
                url = (r.get("Images") or "").strip()
                if sku and url and not sku.startswith("2"):
                    out[sku] = url
    return out


IMAGES = load_images()


def row_out(r):
    vat = (r.get("vat") or "").strip()
    name = (r.get("name") or "").strip()
    reg = (r.get("regulation") or "").strip()
    sku = (r.get("ean") or "").strip()
    cat = classify(name, reg)
    return {
        "Type": "simple",
        "SKU": sku,
        "Name": name,
        "Published": "0",          # draft
        "In stock?": "0",
        "Stock": "0",
        "Regular price": (r.get("price") or "").strip(),
        "Tax status": "taxable",
        "Tax class": TAX_CLASS.get(vat, ""),
        "Categories": cat,
        "Description": describe(name, cat, reg),
        "Images": IMAGES.get(sku, ""),
        "Meta: _sp_regulation": reg,
    }


def main():
    with open(SRC, newline="", encoding="utf-8") as f:
        rows = list(csv.DictReader(f))

    with open(FULL, "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=HEADERS)
        w.writeheader()
        for r in rows:
            w.writerow(row_out(r))

    # Representative sample: evenly spaced across the whole catalogue
    # (the source is price-sorted, so first-N would be all high-price
    # treatments — a stride gives a real spread of price/regulation).
    stride = max(1, len(rows) // SAMPLE_SIZE)
    sample = rows[::stride][:SAMPLE_SIZE]
    with open(SAMPLE, "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=HEADERS)
        w.writeheader()
        for r in sample:
            w.writerow(row_out(r))

    # quick stats
    from collections import Counter
    regs = Counter((r.get("regulation") or "").strip() for r in rows)
    priced = sum(1 for r in rows if (r.get("price") or "").strip())
    print(f"Rows in           : {len(rows)}")
    print(f"-> {os.path.basename(FULL)} ({len(rows)} rows)")
    print(f"-> {os.path.basename(SAMPLE)} ({len(sample)} rows, every {stride}th)")
    print(f"Priced            : {priced} | no price: {len(rows)-priced}")
    print(f"Regulation        : {dict(regs)}")
    print(f"Sample regulation : {dict(Counter((r.get('regulation') or '').strip() for r in sample))}")


if __name__ == "__main__":
    main()
