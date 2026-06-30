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

HERE = os.path.dirname(os.path.abspath(__file__))
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
    "Meta: _sp_regulation",
]

TAX_CLASS = {
    "0.2": "",             # Standard rate (WooCommerce slug is empty)
    "0.20": "",
    "0": "zero-rate",
    "0.0": "zero-rate",
    "0.05": "reduced-rate",
}


def row_out(r):
    vat = (r.get("vat") or "").strip()
    return {
        "Type": "simple",
        "SKU": (r.get("ean") or "").strip(),
        "Name": (r.get("name") or "").strip(),
        "Published": "0",          # draft
        "In stock?": "0",
        "Stock": "0",
        "Regular price": (r.get("price") or "").strip(),
        "Tax status": "taxable",
        "Tax class": TAX_CLASS.get(vat, ""),
        "Categories": "",
        "Meta: _sp_regulation": (r.get("regulation") or "").strip(),
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
