#!/usr/bin/env python3
"""
Clean Products.xlsx -> products-clean.csv  (ClickUp card: "Clean product CSV")

Source workbook columns:  ean, name, regulation, pip, price, google_category, vat
Deliverable columns:      ean, name, regulation, price, vat   (pip + google_category dropped)

What this fixes
---------------
1. EANs were stored as Excel NUMBERS, so leading zeros were stripped (a 13-digit
   EAN beginning 0/00 reads back as 12/11 digits, and floats add a ".0"). We
   restore them using the GTIN check digit as ground truth:
     - already a valid GTIN-8/12/13  -> trusted
     - shorter, but valid once left-padded to 13 -> leading zeros restored
   EAN-8 codes are kept at 8 digits (a distinct GS1 allocation; padding them to
   13 would create a different, unlookup-able barcode). UPC-A (12) is normalised
   to its canonical 13-digit EAN form (prepend 0 — check digit is preserved).
2. Names: non-breaking spaces (\xa0) and friends -> normal space; whitespace
   collapsed; trimmed. (Commas in names are fine — the CSV writer quotes them.)
3. Price -> 2dp string, blank where missing.
4. VAT -> rate preserved verbatim (0 / 0.05 / 0.2).

Outputs (RFC-4180, all fields quoted as needed, UTF-8):
  docs/catalogue/products-clean.csv            full clean catalogue
  docs/catalogue/products-needs-attention.csv  rows missing an EAN or a price

NOTE: products-clean.csv stores EANs as text WITH leading zeros. WooCommerce's
CSV importer reads them as text, so they import correctly. Do NOT re-open and
re-save this file in Excel — Excel will re-strip the leading zeros. Edit the
source .xlsx instead and re-run this script.
"""
import csv
import os
import re
import sys

import openpyxl

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.abspath(os.path.join(HERE, "..", ".."))
SRC = os.path.join(ROOT, "Products.xlsx")
OUT = os.path.join(HERE, "products-clean.csv")
EXC = os.path.join(HERE, "products-needs-attention.csv")

# whitespace characters to fold to a normal space
_WS = {"\xa0", " ", " ", " ", " ", "﻿"}


def gtin_check_ok(s):
    """Validate a numeric GTIN-8/12/13/14 by its trailing check digit."""
    if not s.isdigit() or len(s) not in (8, 12, 13, 14):
        return False
    d = [int(c) for c in s]
    chk = d[-1]
    body = d[:-1][::-1]
    total = sum(x * (3 if i % 2 == 0 else 1) for i, x in enumerate(body))
    return (10 - (total % 10)) % 10 == chk


def clean_ean(v):
    """Return (ean_text, status). status is used for the QA report."""
    if v is None:
        return "", "blank"
    if isinstance(v, float):
        s = format(int(v), "d") if v == int(v) else repr(v)
    elif isinstance(v, int):
        s = format(v, "d")
    else:
        s = str(v).strip()
    if s == "":
        return "", "blank"
    if not s.isdigit():
        return s, "nonnumeric"
    if gtin_check_ok(s):
        if len(s) == 8:
            return s, "ean8"                 # keep native EAN-8
        if len(s) == 12:
            return s.zfill(13), "upca_to_ean13"
        if len(s) == 13:
            return s, "ean13"
        return s, "gtin14"
    if len(s) < 13 and gtin_check_ok(s.zfill(13)):
        return s.zfill(13), "recovered_leading_zeros"
    # could not validate at any standard length -> pad to 13 but flag for review
    return (s.zfill(13) if len(s) < 13 else s), "invalid_checkdigit"


def clean_name(v):
    if v is None:
        return ""
    s = str(v)
    for ws in _WS:
        s = s.replace(ws, " ")
    s = re.sub(r"\s+", " ", s).strip()
    return s


def clean_price(v):
    try:
        return f"{float(v):.2f}"
    except (TypeError, ValueError):
        return ""


def clean_vat(v):
    try:
        return "%g" % float(v)
    except (TypeError, ValueError):
        return ""


def main():
    wb = openpyxl.load_workbook(SRC, read_only=True, data_only=True)
    ws = wb.active
    rows = [list(r) for r in ws.iter_rows(values_only=True)]

    # header is the first row whose first cell == 'ean'
    hdr = next(i for i, r in enumerate(rows) if r and r[0] == "ean")
    src_cols = [c for c in rows[hdr]]
    idx = {name: i for i, name in enumerate(src_cols)}
    data = [r for r in rows[hdr + 1:]
            if any(c is not None and str(c).strip() != "" for c in r)]

    stats = {
        "rows_in": len(data),
        "ean_status": {},
        "vat": {},
        "regulation": {},
        "blank_ean": 0,
        "blank_price": 0,
        "comma_names": 0,
        "names_normalised": 0,
    }
    needs = []

    with open(OUT, "w", newline="", encoding="utf-8") as fout:
        w = csv.writer(fout, quoting=csv.QUOTE_MINIMAL)
        w.writerow(["ean", "name", "regulation", "price", "vat"])
        for r in data:
            raw_ean = r[idx["ean"]]
            raw_name = r[idx["name"]]
            ean, status = clean_ean(raw_ean)
            name = clean_name(raw_name)
            reg = (str(r[idx["regulation"]]).strip()
                   if r[idx["regulation"]] is not None else "")
            price = clean_price(r[idx["price"]])
            vat = clean_vat(r[idx["vat"]])

            stats["ean_status"][status] = stats["ean_status"].get(status, 0) + 1
            stats["vat"][vat] = stats["vat"].get(vat, 0) + 1
            stats["regulation"][reg] = stats["regulation"].get(reg, 0) + 1
            if ean == "":
                stats["blank_ean"] += 1
            if price == "":
                stats["blank_price"] += 1
            if "," in name:
                stats["comma_names"] += 1
            if name != (str(raw_name) if raw_name is not None else ""):
                stats["names_normalised"] += 1

            w.writerow([ean, name, reg, price, vat])

            issues = []
            if ean == "":
                issues.append("missing EAN")
            if price == "":
                issues.append("missing price")
            if status == "invalid_checkdigit":
                issues.append("EAN failed check digit")
            if issues:
                needs.append([ean, name, reg, price, vat, "; ".join(issues)])

    with open(EXC, "w", newline="", encoding="utf-8") as fexc:
        w = csv.writer(fexc, quoting=csv.QUOTE_MINIMAL)
        w.writerow(["ean", "name", "regulation", "price", "vat", "issue"])
        w.writerows(needs)

    # ---- console report ----
    def show(d):
        return ", ".join(f"{k or '<blank>'}={v}" for k, v in sorted(d.items(), key=lambda x: -x[1]))

    print(f"Source columns : {src_cols}")
    print(f"Rows in        : {stats['rows_in']}")
    print(f"Rows out       : {stats['rows_in']}  (no rows dropped)")
    print(f"-> {OUT}")
    print(f"-> {EXC}  ({len(needs)} rows need attention)")
    print()
    print("EAN status     :", show(stats["ean_status"]))
    print("Regulation     :", show(stats["regulation"]))
    print("VAT rate       :", show(stats["vat"]))
    print(f"Blank EANs     : {stats['blank_ean']}")
    print(f"Blank prices   : {stats['blank_price']}")
    print(f"Comma in name  : {stats['comma_names']} (quoted in CSV)")
    print(f"Names cleaned  : {stats['names_normalised']} (nbsp/whitespace)")


if __name__ == "__main__":
    sys.exit(main())
