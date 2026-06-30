# Smart Pharmacy — Handover Brief (read me first)

This is the narrative that ties the individual ClickUp tasks together.
If you only read one doc before starting, read this one, then
`CLAUDE-smart-pharmacy.md` (codebase conventions) and
`smart-pharmacy-eligibility/README.md` (the consultation plugin).

---

## What we're building

A UK online pharmacy on WordPress + WooCommerce, with a custom theme
and a custom consultation plugin. It sells two kinds of product, and
the whole architecture exists to handle the difference between them:

1. **Everyday products** (GSL / unregulated) — vitamins, skincare,
   oral care, first aid, OTC medicines. Sold like any normal shop:
   add to basket → checkout → pay → dispatch.

2. **Pharmacist-controlled products** (P-regulated medicines + the
   GLP-1 weight-loss range like Mounjaro / Wegovy). These **cannot**
   legally be sold with a plain "Add to Cart". They must go through a
   consultation form, be reviewed by a pharmacist, and only then be
   charged and dispatched.

Everything in the build serves that second flow. The first flow is
standard WooCommerce.

---

## The three pieces and how they link

This is the bit that makes the individual tasks make sense:

```
  Products.xlsx (9,767 SKUs)         <-- Murtaza's catalogue (the source)
        │  columns: ean, name, regulation, price, vat
        │
        ▼  (clean → categorise → images → descriptions → import)
  WooCommerce products               <-- the live shop catalogue
        │  the "regulation" column decides behaviour:
        │     "No regulation" / GSL  → normal Add to Cart
        │     "P" (incl. GLP-1)      → must use the consultation
        ▼
  Consultation form (eligibility plugin)
        │  customer answers questions; payment is AUTHORISED (held, not charged)
        ▼
  Order = "Awaiting Clinical Review"
        │  pharmacist reads the answers in the order screen
        ▼
  Approve → capture payment → dispatch      Reject → void payment → notify
```

So: **the spreadsheet becomes the products; each product's `regulation`
value decides whether it sells directly or routes to the consultation;
the consultation holds payment until a pharmacist approves it.** That's
the whole system in one sentence.

---

## The customer journeys

**Journey A — everyday product (e.g. toothpaste, vitamins)**
Browse shop → Add to Basket → Checkout → Pay (Stripe) → confirmed →
dispatched. Nothing special.

**Journey B — pharmacist-controlled product (e.g. Mounjaro, a P-med)**
Browse → open product → button says **"Start Consultation"** (not Add
to Cart) → fill the consultation form → at checkout the card is
**authorised but not charged** → order lands as **"Awaiting Clinical
Review"** → pharmacist reviews → **Approve** (payment captured,
dispatched) or **Reject** (payment voided, customer told).

**Journey C — the pharmacist (every working day)**
Logs into WP admin → sees orders "Awaiting Clinical Review" → opens
each → reads the consultation answers in the clinician panel → may
contact the customer → Approves or Rejects. Approving is what captures
the held payment.

---

## The consultation form — the important details

Murtaza confirmed the spec:

- **One standard form** for all P-meds, with these base questions:
  DOB, who it's for, what for, what they've tried, other medications
  (incl. OTC), other conditions, pregnant/breastfeeding, previous use,
  free-text "anything else".
- **Questions must be editable, not hardcoded.** Murtaza needs to
  reword the base questions in admin, AND add product-specific
  questions per product as he launches them. Build it data-driven:
  a base question set (editable) + an optional "additional questions"
  field on each WooCommerce product.
- **Disclaimer** at the end: "reviewed by a pharmacist who may contact
  you with further questions before dispatch".
- The in-depth GLP-1 weight-loss consultation already exists in the
  plugin (`smart-pharmacy-eligibility/`). The new lighter P-med form
  reuses the same engine — fewer questions, same plumbing.

- **Payment:** Stripe "authorise on checkout, capture later" (this is
  a config toggle in the WC Stripe settings, NOT a custom build). The
  approve/reject buttons fire the capture or void.

---

## How the milestones map to all this

The ClickUp lists are sequenced to build the flow above, in order:

1. **Products in the Shop** — get Products.xlsx cleaned and imported so
   there's a real catalogue (Journey A works end-to-end).
2. **Can Take Money** — Stripe + VAT + shipping, so checkout takes real
   payment.
3. **Consultation & Pharmacist Review** — the form + payment hold +
   pharmacist approve/reject. This powers Journeys B and C. Build it
   once here; it's reused for the 550 P-meds later.
4. **Launch Phase 1** — go live selling GSL products + the GLP-1 range.
5. **Add the 550 P-meds** — switch the remaining P-products onto the
   same consultation form.

Phasing decision still pending from Murtaza: whether Phase 1 launches
with the P-meds included, or P-meds come in a later phase. Until he
says otherwise, build Milestones 1–3 — they're needed either way.

---

## Working in this codebase (you'll be in Claude Code)

- **Theme:** `smart-pharmacy-theme/` — Tailwind compiled in CI,
  ACF-driven content, classic editor (no Gutenberg) on pages/CPTs.
- **Plugin:** `smart-pharmacy-eligibility/` — the consultation engine.
  Its `README.md` documents the AJAX contract + data model.
- **Read first:** `CLAUDE-smart-pharmacy.md` (conventions, what never
  to do). Claude Code will pick these docs up as context automatically.
- **Git:** branch + PR per ClickUp card. `main` auto-deploys to Kinsta
  staging. Production deploys are gated — Drew approves, then you deploy.
- **Hard rule learned the hard way:** any call across the
  plugin↔theme boundary must guard with BOTH `class_exists` AND
  `method_exists` (see commit `ee6c8a6` — a missing guard took the
  site down mid-demo).
- **Conventions:** `sp_` prefix in the theme, `spe_` in the plugin,
  `sp_field()` not `get_field()`, never bump version for cache-busting
  (mtime handles it).

---

## Where to start

ClickUp → **Smart Pharmacy** folder → List **"1. Products in the Shop"**
→ first card **"Clean product CSV"**. Work milestones 1 and 2 in
parallel; 3 follows; 4 and 5 are later. Ignore the Backlog list for now.

Questions or anything ambiguous → ask Drew. Compliance / scope / budget
calls → always Drew, never the client directly.
