# Smart Pharmacy — WordPress Theme

A WooCommerce-ready classic PHP theme for a UK online pharmacy, with treatment
landing pages (CPT), consultation-gated checkout (planned), and a bespoke teal
brand design powered by Tailwind CSS.

Version: `0.1.0` (Stage 1 — skeleton only, no visuals ported yet).

## Requirements

- WordPress 6.4+
- PHP 8.0+
- WooCommerce 8.0+
- Node.js 18+ (for the Tailwind build)

## Install

1. Copy this folder to `wp-content/themes/smart-pharmacy/` on your WordPress install.
2. From inside the theme folder, install build dependencies:
   ```bash
   npm install
   ```
3. Build the Tailwind CSS (one-off, minified):
   ```bash
   npm run build
   ```
   Or watch for changes during development:
   ```bash
   npm run dev
   ```
4. In WP Admin, activate **Smart Pharmacy** under *Appearance → Themes*.
5. Flush permalinks: *Settings → Permalinks → Save Changes* (required so the
   `treatment` CPT URL `/treatments/…` works).

## What's in Stage 1

- `style.css` — WordPress theme header (metadata only).
- `functions.php` — theme supports, asset enqueues, WooCommerce HPOS
  compatibility, and the `treatment` custom post type registration.
- `header.php`, `footer.php`, `index.php`, `front-page.php` — skeleton
  templates. No visual markup yet.
- `assets/css/styles.css` — custom animations & overrides (copied from the
  static prototype).
- `assets/js/search-animation.js` — rotating search placeholder and popular
  searches dropdown (copied from the static prototype).
- `src/input.css` + `tailwind.config.js` + `postcss.config.js` +
  `package.json` — Tailwind build pipeline.

Activating the theme at this point will render blank pages with the correct
`<head>`, enqueued stylesheets, fonts, and scripts. This is intentional —
Stage 2 ports the homepage and treatment landing page markup.

## Upcoming stages

- **Stage 2** — Port `index.html` into `front-page.php` + `template-parts/`.
- **Stage 3** — Port `weight-loss.html` into `single-treatment.php`, using ACF
  (or native custom fields) for structured content.
- **Stage 4** — WooCommerce template overrides (shop, product, cart, checkout)
  styled in the brand design; selective dequeue of default WC styles.
- **Stage 5** — Remaining treatment pages (hair loss, ED, women's health) and
  the consultation eligibility checker integration.

## Project layout

```
smart-pharmacy/
├── assets/
│   ├── css/
│   │   ├── styles.css         (custom animations; tracked in git)
│   │   └── tailwind.css       (compiled output; gitignored)
│   └── js/
│       └── search-animation.js
├── src/
│   └── input.css              (Tailwind entry point)
├── template-parts/            (partials land here in Stage 2)
├── woocommerce/               (WC template overrides land here in Stage 4)
├── .gitignore
├── footer.php
├── front-page.php
├── functions.php
├── header.php
├── index.php
├── package.json
├── postcss.config.js
├── README.md
├── style.css
└── tailwind.config.js
```
