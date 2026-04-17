# WooCommerce Admin Seeding Checklist

**Purpose:** Populate the WooCommerce admin on Kinsta staging with enough
data that the brand-styled shop pages built in Stage 4a have something
to display during the upcoming client meeting. Without this, even a
perfectly-styled `/shop/` archive renders "No products found."

**Aim:** Roughly 6–9 test products spread across 3–4 categories, with
at least one Mounjaro placeholder so we can show how the shop archive
will look once the GLP-1 product line is published. Should take ~30
minutes end to end.

**Order matters** — settings first, then categories, then products.

---

## Pre-flight

Confirm in WP Admin:

- [ ] WooCommerce plugin is active (Plugins → Installed Plugins)
- [ ] You're on the **staging** site, not production (URL contains
      `staging` or matches the Kinsta staging hostname)
- [ ] You have **Administrator** role (needed for WC settings)

---

## Step 1 — Verify WooCommerce auto-created pages exist

WC creates these on first activation. Confirm they're present under
**Pages → All Pages**:

- [ ] **Shop** — slug `/shop/`
- [ ] **Cart** — slug `/cart/`
- [ ] **Checkout** — slug `/checkout/`
- [ ] **My Account** — slug `/my-account/`

If any are missing, go to **WooCommerce → Status → Tools → Create default WooCommerce pages** and click *Create pages*.

---

## Step 2 — General settings

**WooCommerce → Settings → General:**

- [ ] **Selling location(s)**: *Sell to specific countries* → United Kingdom
- [ ] **Shipping location(s)**: *Ship to specific countries* → United Kingdom
- [ ] **Default customer location**: *Geolocate*
- [ ] **Currency**: Pound sterling (£)
- [ ] **Currency position**: Left (£99)
- [ ] **Thousand separator**: `,`
- [ ] **Decimal separator**: `.`
- [ ] **Number of decimals**: 2

**WooCommerce → Settings → Products → General:**

- [ ] **Shop page**: confirm set to "Shop"
- [ ] **Add to cart behaviour**: leave defaults
- [ ] **Placeholder image**: leave default (the brand-styled product
      template falls back to category bg colour if no image is set)

**WooCommerce → Settings → Products → Inventory:**

- [ ] **Manage stock**: enabled (so the demo can show stock levels)
- [ ] **Hold stock (minutes)**: 60
- [ ] **Out of stock visibility**: *Hide out of stock items from the
      catalog* (cleaner demo)

---

## Step 3 — Product categories

**Products → Categories.** Create these four parent categories. Add a
short description to each (appears in the brand archive header):

| Name | Slug | Description |
|---|---|---|
| Pain Relief | `pain-relief` | Fast-acting OTC pain and fever relief, GPhC-approved. |
| Vitamins & Supplements | `vitamins` | Daily wellness essentials and targeted supplements. |
| First Aid | `first-aid` | Plasters, bandages, and home first-aid kits. |
| Weight Management | `weight-management` | GLP-1 treatments and weight-loss programmes (consultation required). |

For *Weight Management*, leave it the placeholder for now — Stage 4c
will gate it with consultation routing. We're seeding it so the demo
shows the visual treatment of a category that will eventually be POM-only.

---

## Step 4 — Test products

**Products → Add New** for each of the rows below. Use **Simple product**
type unless noted. Stock status = *In stock* and stock quantity = `100`
unless noted. Category as listed. Tax status = *None* (UK OTC meds are
mostly zero-rated; we'll fix this properly later).

### GSL / OTC products (sellable now, no consultation needed)

| Name | SKU | Price | Category | Short description |
|---|---|---|---|---|
| Paracetamol 500mg (16 tablets) | `SP-PAIN-PARA-500-16` | £2.99 | Pain Relief | Fast-acting pain and fever relief. UK-licensed generic. |
| Ibuprofen 200mg (16 tablets) | `SP-PAIN-IBU-200-16` | £3.49 | Pain Relief | Anti-inflammatory pain relief for headaches, muscular aches, and period pain. |
| Vitamin D3 1000IU (60 capsules) | `SP-VIT-D3-1000-60` | £8.99 | Vitamins & Supplements | Essential daily supplement. Supports bone, muscle, and immune health. |
| Vitamin C 1000mg (30 effervescent tablets) | `SP-VIT-C-1000-30` | £5.99 | Vitamins & Supplements | High-strength immune support. Orange-flavoured fizzy tablets. |
| Standard First Aid Kit | `SP-FA-KIT-STD` | £14.99 | First Aid | Complete home first-aid kit: 87 pieces, GPhC-recommended contents. |
| Assorted Plasters (30-pack) | `SP-FA-PLAST-30` | £3.49 | First Aid | Hypoallergenic mixed-size plasters for everyday cuts and grazes. |

### POM placeholders (NOT sellable yet — Stage 4c will gate these)

These are weight-loss treatments. The B4 Treatment Meta should already
flag them as POM. Set them up now so the shop archive has the GLP-1
range visible; the consultation gate comes in Stage 4c.

| Name | SKU | Price | Category | Short description |
|---|---|---|---|---|
| Mounjaro 2.5mg (4-week supply) | `SP-WL-MOUN-2.5-4W` | £149.00 | Weight Management | Starter dose of tirzepatide for weight management. Prescription-only — consultation required. |
| Mounjaro 5mg (4-week supply) | `SP-WL-MOUN-5-4W` | £179.00 | Weight Management | Intermediate dose of tirzepatide. Prescription-only — consultation required. |
| Mounjaro 7.5mg (4-week supply) | `SP-WL-MOUN-7.5-4W` | £209.00 | Weight Management | Higher dose of tirzepatide. Prescription-only — consultation required. |
| Mounjaro 10mg (4-week supply) | `SP-WL-MOUN-10-4W` | £229.00 | Weight Management | Maximum standard dose of tirzepatide. Prescription-only — consultation required. |

**For each product, also fill in:**

- **Long description** (the big editor at the top): 2–3 sentences. For
  GSL products, mention key benefits and any warnings. For POM products,
  mention "Available after a free online consultation with our
  GPhC-registered prescriber."
- **Featured image**: see Step 5.
- **Categories**: select the assigned category from the right sidebar.
- **Tags**: optional.

---

## Step 5 — Featured images

The product cards lean on featured images heavily. Two approaches:

**Option A (fastest, demo-quality):** Pull free stock photos from
Unsplash. Search terms to use:

- Paracetamol → "tablets pills white background"
- Ibuprofen → "ibuprofen blister pack"
- Vitamin D3 → "vitamin supplement bottle"
- Vitamin C → "vitamin c effervescent"
- First aid kit → "first aid kit white background"
- Plasters → "bandages plasters"
- Mounjaro → "injection pen medical white background" (real Mounjaro
  product photos are trademark-restricted; use a generic injection-pen
  stock photo and we'll swap in licensed product photography post-meeting)

Download → upload via the product's Featured Image panel.

**Option B (production-quality):** Get the client's actual product
photography from suppliers / their existing site. Better long-term but
slower for the meeting.

For the meeting, **Option A is fine**. Flag to the client that we'll
swap to licensed product photos before launch.

---

## Step 6 — Payment gateway (demo-only)

**WooCommerce → Settings → Payments:**

- [ ] **Cash on delivery**: enable
  - Title: "Pay on Delivery"
  - Description: "Demo gateway — no real payment is taken. Stripe / PayPal will be configured before launch."

Stripe / PayPal / Klarna can be configured properly post-meeting. Cash
on Delivery is enough to walk a checkout flow end-to-end during the
demo without entering real card details.

---

## Step 7 — Shipping zone

**WooCommerce → Settings → Shipping → Add shipping zone:**

- **Zone name**: United Kingdom
- **Zone regions**: United Kingdom (UK)
- **Shipping methods**: add two
  1. **Free shipping** — minimum order amount £30
  2. **Flat rate** — cost £3.99, name "Standard delivery (1–3 working days)"

---

## Step 8 — Smoke test before the meeting

Walk the demo path yourself in an incognito window. Stage 4a-4 adds
the branded header (eyebrow pill + gradient title) to every WC page,
so watch for that on cart / checkout / my-account as well as shop.

- [ ] Visit `/shop/` — see 6+ product cards in a 3-column grid, branded styling
- [ ] Click into a product (e.g. Paracetamol 500mg) — see single-product
      page with image, price, short description, Add to Cart button
- [ ] Click *Add to Cart* — success notice appears
- [ ] Click cart icon / visit `/cart/` — **branded "Shopping Cart" header**,
      line item with correct price, rounded card totals panel
- [ ] Click *Proceed to Checkout* — **branded "Checkout" header**, form in a
      rounded card, pill-shaped Place Order button
- [ ] Fill in test details, place order — **branded thank-you page** with
      green "Order received" heading inside a rounded card
- [ ] Visit `/my-account/` while logged out — **branded "Sign In" header**,
      login / register split in two rounded cards
- [ ] Log in, revisit `/my-account/` — **branded "My Account" header**, side
      nav with active-tab pill + content panel in a rounded card
- [ ] Visit **WooCommerce → Orders** — confirm the test order is there

If any of these break visually or functionally, ping me and I'll
investigate before the meeting.

---

## What to flag to the client

When showing the demo, be explicit about what's real and what's
placeholder:

- ✅ **Visual design / brand consistency** is final-quality
- ✅ **Shop browsing, search, cart, checkout flow** all work end-to-end
- ⚠️ **Product photography** is stock — will be swapped for licensed
      product photos before launch
- ⚠️ **Payment gateway** is "Cash on Delivery" placeholder — Stripe /
      PayPal go in once we have the merchant account credentials
- ⚠️ **POM products (Mounjaro)** currently appear in the shop with
      "Add to Cart" — Stage 4c adds the consultation gate before launch
      so prescription products route to the treatment landing page
      consultation flow instead of the cart
- ⚠️ **Tax handling** is set to "None" for now — UK VAT logic configured
      pre-launch with the client's accountant
- ⚠️ **Shipping prices** are placeholder — confirm with client and
      Royal Mail / DPD pricing before launch
- 🔜 **Google Merchant Center / Shopping ads** — Stage 4e adds the feed
      so the GSL products (vitamins, OTC meds) can run Shopping ads.
      POM products are excluded by Google's healthcare ads policy.

---

## Step 9 — Shop sidebar (Stage 4b)

Stage 4b registers a `Shop Sidebar` widget area that renders to the
left of the product grid when populated. If you leave it empty, the
grid stays full-width, so this step is optional for the first demo.
Populate it for a more impressive filtering demo.

**Appearance → Widgets → Shop Sidebar** (or the Customize screen).
Add these in order:

- [ ] **Active Filters** (block or classic widget) — shows current
      filters as removable chips
- [ ] **Filter by Price** — slider + two number inputs
- [ ] **Filter by Stock** — in-stock / out-of-stock checkboxes
- [ ] **Product Categories** — hierarchical list of `product_cat` terms

Smoke-test after saving:

- [ ] `/shop/` now shows a sidebar on the left, grid on the right
- [ ] Click a category filter → grid updates, URL gains query params
- [ ] Active-filters widget shows a removable chip for the applied filter
- [ ] Clear all filters → back to the full grid

---

## What we're explicitly NOT doing in this seeding pass

- Variable products (e.g. Vitamin D3 with multiple bottle sizes) —
  Stage 4 templates support these but we don't need them for the demo
- Product attributes (size / form / dose) — Stage 4b sidebar styles the
  Filter-by-Attribute widget but we haven't declared attributes yet
- Customer reviews — needs a few days of real reviews to look good;
  Stage 4 covers the markup
- Email transactional templates — Stage 4d
- GDPR / cookie consent — separate workstream
- Schema.org product markup for SEO — automated by Yoast WC plugin
  if installed; otherwise Stage 4 includes it

---

## When to come back to this doc

After the meeting, if the client greenlights launch, the next round
needs:

1. Replace stock images with licensed product photos
2. Swap COD for Stripe (or whichever gateway the client chooses)
3. Configure UK VAT properly
4. Run **Stage 4c** to gate POM products
5. Run **Stage 4e** to publish the Google Merchant Center feed
