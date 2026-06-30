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

## P-med consultation form (editable base questions)

A lighter sibling of the GLP-1 checker for the wider P-medicine range.
The in-depth GLP-1 funnel above is untouched; this reuses the same
plumbing (options API, AJAX + nonce, dbDelta) with a single-page form.

- **Shortcode:** `[smart_pharmacy_consultation]` (optionally
  `[smart_pharmacy_consultation product="123"]`, or pass `?product=123`
  on the page URL so the consultation is linked to that product).
- **Editable questions:** WP Admin → **Eligibility → Consultation Form**.
  The 9 base questions (DOB, who for, what for, what tried, other meds
  incl. OTC, other conditions, pregnant/breastfeeding, previous use,
  free-text) live as code defaults in `class-consultation-questions.php`
  and are overlaid by admin edits stored in the `spe_consultation_*`
  options. Admins can reword each question, toggle it on/off, mark it
  required, reorder it, and edit choice options — plus the intro and
  pharmacist disclaimer copy. Question `key` + input `type` stay in code
  (structural); everything a client would change is editable.
- **AJAX:** action `spe_consultation_submit`, nonce
  `spe_consultation_nonce`. Required-field + choice + date rules are
  enforced **server-side** (client JS is UX only). Stored in
  `wp_spe_consultations` (UUID-keyed, JSON answers + promoted dob /
  who_for / product_id), ready to bridge to an order.

### Per-product additional questions

On any WooCommerce product edit screen there's an **"Additional
consultation questions"** repeater (ACF). Murtaza adds product-specific
questions there as he launches each P-medicine — question text, answer
type (text / long text / radio / dropdown / checkboxes), help text,
required, and options for choice types. They render after the base set
and before the disclaimer, and are validated server-side just like the
base questions. The form must be reached with the product in scope
(`[smart_pharmacy_consultation product="123"]` or `?product=123`) for a
product's extras to appear. Registered (guarded) via ACF in
`class-consultation-product-questions.php`; no ACF → base form still works.

### Pharmacist review (order side)

- **Order status:** a custom **Awaiting Clinical Review**
  (`wc-awaiting-review`) WooCommerce status — amber pill in the admin
  order list, in the status dropdown, HPOS-aware. It's a *holding*
  status (not paid/processing): payment is only authorised at this
  point; approving captures it (that's the Stripe card).
- **Clinician review panel:** a print-friendly **Clinical Review —
  Consultation** meta box on the order edit screen showing submitted
  time, who-for, DOB (+ age), product, and every question→answer. Only
  appears on orders that have a linked consultation, so normal orders
  aren't cluttered. Reads the consultation linked via the
  `_spe_consultation_id` order meta.
- **Customer email:** `SPE_Email_Consultation_Received`, a proper
  `WC_Email` (toggle/edit under WooCommerce → Settings → Emails) sent
  when an order enters Awaiting Clinical Review — reassures the customer
  that a pharmacist will review and that they haven't been charged yet.
- **The seam:** `SPE_Consultation_Order::attach( $order, $consultation_id )`
  links a consultation to its order, stores the meta, and moves the
  order into Awaiting Clinical Review. The deferred (Stripe-dependent)
  payment/checkout card calls this once it creates the order — nothing
  populates the panel or fires the email until then.
- **Customer view:** a **Consultations** tab under WooCommerce → My
  Account lists the customer's consultations with a plain-English status
  — Pending review / Approved / Not approved — and timestamps. The
  status is derived from the linked order's status, so it tracks the
  pharmacist's approve/reject automatically. Adds a rewrite endpoint
  (`/my-account/consultations/`); rewrite rules are flushed once on
  first load after deploy.

### Checkout handoff + pharmacist approve/reject

The full loop is now wired:

1. Submitting a consultation for a product empties the basket, unlocks
   the POM gate for that product, adds it with the consultation id in
   hidden cart meta, and redirects to checkout
   (`class-consultation-checkout.php`).
2. At checkout the **configured WooCommerce Stripe gateway** authorises
   payment (set it to *"Issue authorization on checkout, and capture
   later"* — that's a Stripe-settings toggle, no keys live in this
   plugin). On order placement the consultation is stamped onto the
   order and it enters **Awaiting Clinical Review**.
3. The pharmacist's **Approve / Reject** buttons in the review panel
   (`class-consultation-review-actions.php`) drive capture/void through
   standard status transitions:
   - **Approve → `processing`** → the gateway captures the held payment;
     customer gets WooCommerce's Processing email.
   - **Reject → `cancelled`** → the gateway voids the authorisation (no
     charge); customer is emailed that we couldn't approve it.
   Decisions are recorded as order meta (`_spe_review_decision/by/at`),
   capability-gated (`manage_woocommerce`) and nonce-protected, and are
   idempotent (no double-capture on resubmit).

> ⚠️ **Must be verified on staging once Stripe manual-capture is
> configured.** Capture/void is delegated to the gateway via status
> transitions; the exact post-authorisation status (`awaiting-review`
> vs the gateway landing it `on-hold`) needs confirming. The review
> actions already treat awaiting-review / on-hold / pending alike so the
> flow works either way.

### Extension points

- `apply_filters( 'spe_consultation_questions', $questions, $args )` —
  where per-product questions are appended (see above); also open to
  any future question source.
- `do_action( 'spe_consultation_submitted', $consultation_id, $answers, $product_id )`
  — fires after a consultation is stored and validated.
- `apply_filters( 'spe_consultation_redirect', $url, $consultation_id, $product_id )`
  — the checkout handoff hooks here to return the checkout URL.
- `do_action( 'spe_after_consultation_review', $order, $row )` — render
  point for the pharmacist decision actions on the review panel.

**Deferred to their own ClickUp cards:** final disclaimer wording,
branded transactional email styling, and the ID-upload step.

## Notes / not yet built

- GDPR: data is stored in the WP database (Kinsta-managed
  encryption at rest). Retention + DSAR tooling is a separate
  workstream before launch.
- Per-treatment dose picker on screen 21: each card currently
  represents the starter dose. A dropdown for "increase to higher
  dose" can be added without changing the schema.
