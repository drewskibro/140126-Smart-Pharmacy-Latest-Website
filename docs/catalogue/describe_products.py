#!/usr/bin/env python3
"""
Generate short, FACTUAL product descriptions (ClickUp card: "Generate
factual product descriptions").

Strictly factual — brand/format/size only. NO medical or efficacy claims
(MHRA / CAP compliance): nothing about treating, relieving, helping or
being "for" a condition. Medicines get the standard "Always read the
label" wording rather than any claim.

Reads products-clean.csv + the category from classify_categories.py.
By default writes a 50-product SAMPLE for Murtaza to sign off:
  products-descriptions-sample.csv   ean,name,category,regulation,description

Run with `--full` to generate all of them once the style is approved:
  products-woo-descriptions.csv       SKU,Description  (re-import by SKU)
"""
import csv
import os
import re
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, HERE)
from classify_categories import classify  # noqa: E402

SRC = os.path.join(HERE, "products-clean.csv")
SAMPLE = os.path.join(HERE, "products-descriptions-sample.csv")
FULL = os.path.join(HERE, "products-woo-descriptions.csv")
SAMPLE_SIZE = 50

# Categories that are medicines / medical devices — kept claim-free and
# given the standard label wording instead of a category descriptor.
MEDICINE_CATEGORIES = {
    "Pain Relief", "Cold & Flu", "Digestive", "First Aid", "Eye Care",
    "Weight Management", "Sexual Wellness", "Prescription Medicines",
    "Testing & Monitoring",
}

# Neutral, factual descriptor for non-medicine categories (no claims).
CATEGORY_PHRASE = {
    "Skincare": "A skincare product",
    "Hair Care": "A hair care product",
    "Oral Care": "An oral care product",
    "Beauty": "A health and beauty product",
    "Vitamins": "A vitamins and supplements product",
    "Baby & Child": "A baby and child product",
    "Continence": "A continence care product",
    "Stop Smoking": "A stop-smoking product",
    "Supports & Braces": "A support and mobility product",
    "Men's Grooming": "A men's grooming product",
    "Men's Health": "A men's health product",
    "Women's Health": "A women's health product",
    "Pet Care": "A pet care product",
    "Home & Household": "A home and household product",
    "General Health": "A health and wellbeing product",
}

SIZE_RE = re.compile(
    r"(\d[\d.,]*(?:\s?x\s?\d[\d.,]*)?\s?"
    r"(?:ml|l|g|kg|mg|mcg|iu|litre|tablets?|tabs?|capsules?|caps|softgels?|"
    r"sachets?|pack|packs|gummies|pastilles?|lozenges?|wipes?|patches?|"
    r"pads?|drops?|suppositories|effervescent|chewable|film|sheets?))\b",
    re.IGNORECASE,
)


def extract_size(name):
    matches = SIZE_RE.findall(name)
    if not matches:
        return ""
    # Use the last size-like token (usually the pack/quantity).
    m = list(SIZE_RE.finditer(name))[-1]
    return m.group(1).strip()


def describe(name, category, regulation):
    name = name.strip()
    out = [name + "."]

    if regulation == "P" or category in MEDICINE_CATEGORIES:
        if regulation == "P":
            out.append("Prescription-only medicine, supplied after an online consultation with our pharmacist.")
        out.append("Always read the label and any leaflet supplied. If symptoms persist, speak to your pharmacist or doctor.")
    else:
        phrase = CATEGORY_PHRASE.get(category)
        if phrase:
            out.append(phrase + ".")

    size = extract_size(name)
    if size:
        out.append(f"Supplied as {size}.")

    return " ".join(out)


def main():
    full = "--full" in sys.argv
    with open(SRC, newline="", encoding="utf-8") as f:
        rows = [r for r in csv.DictReader(f)]

    if full:
        with open(FULL, "w", newline="", encoding="utf-8") as f:
            w = csv.writer(f)
            w.writerow(["SKU", "Description"])
            for r in rows:
                cat = classify(r["name"], r["regulation"])
                w.writerow([r["ean"], describe(r["name"], cat, r["regulation"])])
        print(f"-> {os.path.basename(FULL)} ({len(rows)} rows)")
        return

    stride = max(1, len(rows) // SAMPLE_SIZE)
    sample = rows[::stride][:SAMPLE_SIZE]
    with open(SAMPLE, "w", newline="", encoding="utf-8") as f:
        w = csv.writer(f)
        w.writerow(["ean", "name", "category", "regulation", "description"])
        for r in sample:
            cat = classify(r["name"], r["regulation"])
            w.writerow([r["ean"], r["name"], cat, r["regulation"], describe(r["name"], cat, r["regulation"])])
    print(f"-> {os.path.basename(SAMPLE)} ({len(sample)} products for sign-off)")


if __name__ == "__main__":
    main()
