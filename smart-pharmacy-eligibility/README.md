# Smart Pharmacy Eligibility Checker (WP Plugin)

A self-contained WordPress plugin that ports the bolt.new GLP-1
eligibility checker into a production-ready WP/WooCommerce flow.

## What it does

- Renders a multi-step medical assessment via the
  `[smart_pharmacy_eligibility]` shortcode.
- Saves partial assessments early (after the first 4 inputs) so
  abandoned funnels are still captured for follow-up.
- Re-runs UK clinical eligibility rules **server-side** on every
  final submission (defence-in-depth — client-side JS is for UX only).
- On eligible submission: adds the chosen treatment + dose to the
  WooCommerce cart and redirects to checkout.
- Stamps the full assessment payload onto the resulting WC order
  as both a flat field set (`_tc_elig_*`) and a JSON blob
  (`_tc_eligibility_raw`).
- Admin screen for clinicians to review assessments by status
  (partial / complete / ineligible), with search + order link.
- Admin settings screen for mapping (treatment, dose) → WC product ID.

## Install

1. Zip the `smart-pharmacy-eligibility/` folder.
2. WP Admin → **Plugins → Add New → Upload Plugin** → upload the zip → activate.
3. WP Admin → **Eligibility → Settings**: map each treatment + dose to
   its WooCommerce product ID (Wegovy 0.25mg → 123, etc.).
4. Create a new WP Page (suggested slug `/start-consultation/`) with
   only this in the content: `[smart_pharmacy_eligibility]`.
5. Point the "Start Consultation" CTAs on Mounjaro / Wegovy product
   cards at that page URL.

## AJAX contract

All actions accept POST + a `nonce` field (action: `tc_eligibility_nonce`).

| Action                          | Purpose                                                                 |
| ------------------------------- | ----------------------------------------------------------------------- |
| `tc_eligibility_save_partial`   | Stash first-touch capture (name/email/phone), returns `assessment_id`   |
| `tc_eligibility_save`           | Final submission: runs rules, updates row, adds product to WC cart      |
| `tc_eligibility_add_to_cart`    | Explicit add-to-cart (used post-confirmation if the user changes dose)  |
| `tc_eligibility_ineligible`     | Audit log when a client-side rule fails                                 |

## Eligibility rules (v0.1)

Mirror the JS for parity. Source of truth is
`includes/class-eligibility-rules.php`:

- Age < 18 or > 74 → ineligible
- BMI < 27 (or < 23 for South Asian ethnicity) → ineligible
- Female + (pregnant | breastfeeding | trying to conceive) → ineligible
- Bariatric surgery within last 6 months → ineligible

## Database

One table: `wp_spe_assessments`. Schema created on activation via
`dbDelta`. Custom columns mirror the public payload; full raw JSON
goes into `raw_payload`.

## Deploy

The Kinsta deploy workflow currently only ships the theme. Two
options:

- **Manual install** (recommended for now): zip and upload via WP
  admin (see Install above).
- **CI install** (future): add a step to
  `.github/workflows/deploy-smart-pharmacy-to-kinsta.yml` that
  SCPs `smart-pharmacy-eligibility/` into `~/public/wp-content/plugins/`.

## Screens (v0.2)

Full clinical funnel: agreement → early capture → age → ethnicity →
sex → female screening (if female) → weight → height → BMI →
diabetes → contraindicated conditions (→ bariatric timing + details
if ticked) → weight-related conditions (→ mental health details if
ticked) → other conditions (Y/N → free text) → previous weight-loss
meds (→ prior weight per med, iterative) → current prescription
meds (→ full list if "other") → allergies (Y/N → free text) →
goal weight (Y/N → input) → DOB → address → GP + consents →
treatment selection → submit → WC checkout.

Treatment cards on screen 21 pull title + price live from
WooCommerce (via the configured product map), so when admins
update a Wegovy 0.25mg price in WC the checker card reflects it
automatically. No double-entry.

## Notes / not yet built

- GDPR: data is stored in the WP database (Kinsta-managed
  encryption at rest). Retention + DSAR tooling is a separate
  workstream before launch.
- Per-treatment dose picker on screen 21: each card currently
  represents the starter dose. A dropdown for "increase to higher
  dose" can be added without changing the schema.
