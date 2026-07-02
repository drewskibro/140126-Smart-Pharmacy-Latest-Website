# Auto-classify products into shop categories — QA report

**Card:** `1. Products in the Shop → Auto-classify products into shop categories`
**Script:** `classify_categories.py` (re-runnable; reads `products-clean.csv`)

## Result: every product categorised

**13,918 / 13,918 (100%)** — nothing is left blank. Keyword rules over
the product name are checked in priority order (specific categories
first); anything the rules don't catch is inferred from its form and
regulation so every product ends up in a category.

The categories are now folded directly into the WooCommerce import file
(`products-woo-import-full.csv`), so a single import brings in products
*and* categories. `products-woo-categories.csv` (SKU + Categories only)
is kept in case you ever want to update just categories later.

## Categories used (22)

The original 13, plus categories the real catalogue clearly needed:
Baby & Child, Eye Care, Continence, Stop Smoking, Testing & Monitoring,
Supports & Braces, Men's Grooming, Pet Care, Home & Household. Two final
homes catch anything unmatched: **Prescription Medicines** (unmatched P
products) and **General Health** (unmatched general products).

Distribution (largest first): Skincare 2,864 · Vitamins 2,828 · Beauty
1,723 · Hair Care 1,321 · General Health 1,231 · Oral Care 681 · Women's
Health 424 · Baby & Child 414 · Cold & Flu 334 · Pain Relief 300 · First
Aid 235 · Sexual Wellness 210 · Digestive 205 · Prescription Medicines
188 · Supports & Braces 184 · Eye Care 150 · Men's Grooming 149 · Pet
Care 110 · Testing & Monitoring 99 · Continence 91 · Men's Health 66 ·
Stop Smoking 46 · Home & Household 45 · Weight Management 20.

## Notes for sign-off

- This is a keyword first pass, meant to be **spot-checked**. Names are
  fuzzy, so expect some to need moving.
- Generic form-words ("tablets", "capsules", "cream") are deliberately
  *not* primary signals, so prescription medicines don't get swept into
  "Vitamins"/"Skincare". Unmatched P products go to "Prescription
  Medicines", unmatched general products to "General Health".
- `Pet Care` and `Home & Household` surface items that arguably don't
  belong in a pharmacy catalogue at all (flea treatments, disinfectant,
  reed diffusers) — worth a look with Murtaza.
- Products import as **drafts**, so categories can be reviewed and
  bulk-edited in WooCommerce before publishing.
