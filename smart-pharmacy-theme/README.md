# Smart Pharmacy — WordPress Theme

A WooCommerce-ready classic PHP theme for a UK online pharmacy, built on the
PharmoDigital pharmacy-theme conventions (ACF Pro, three-tier field fallback,
GitHub Actions → Kinsta SCP deploy) with Tailwind CSS and WooCommerce on top.

Version: `0.1.0` (Stage 1 — skeleton, no visuals ported yet).

For the full architectural playbook, see `../CLAUDE-smart-pharmacy.md`
at the repo root.

## Requirements

- WordPress 6.4+
- PHP 8.0+
- **Advanced Custom Fields Pro** (required — theme content is ACF-driven)
- WooCommerce 8.0+
- Node.js 18+ (only for local development; CI handles production builds)

## Local development

From inside the theme folder:

```bash
npm install
npm run dev         # watch mode: rebuilds tailwind.css on changes
# or:
npm run build       # one-off minified build
```

You do **not** need to run `npm` to deploy — GitHub Actions does that for you.
Run it locally only when you want to preview Tailwind changes on a local WP
install (e.g. via Local by WP Engine or DevKinsta).

## How deployment works

Push to `main` → `.github/workflows/deploy-smart-pharmacy-to-kinsta.yml` runs:

1. Installs Node and theme dependencies.
2. Compiles Tailwind (`npm run build` → `assets/css/tailwind.css`).
3. Strips dev-only files (`node_modules/`, `src/`, `package.json`, etc).
4. SCP's the clean theme folder to Kinsta staging at
   `~/public/wp-content/themes/smart-pharmacy-theme/`.
5. SSH verifies `style.css` and `tailwind.css` landed.

GitHub Secrets required (already configured):
`KINSTA_SSH_HOST`, `KINSTA_SSH_USER`, `KINSTA_SSH_PASSWORD`, `KINSTA_SSH_PORT`.

## What's in Stage 1

- `style.css` — WordPress theme header.
- `functions.php` — theme supports, asset enqueues with `filemtime()` cache-
  busting, WooCommerce HPOS compatibility, `treatment` CPT registration, ACF
  admin notice guard.
- `header.php`, `footer.php`, `index.php`, `front-page.php` — skeleton
  templates. No visual markup yet.
- `inc/helpers.php` — `sp_field()` three-tier fallback and escaping wrappers.
- `inc/acf-options.php` — Theme Settings → Branding / Contact / Compliance /
  Social / Navigation options pages.
- `inc/acf-fields.php` — field group registration stub (populated Stage 2+).
- `assets/css/styles.css` — custom animations (copied from the static prototype).
- `assets/js/search-animation.js` — search dropdown JS (copied from the prototype).
- `src/input.css` + `tailwind.config.js` + `postcss.config.js` + `package.json`
  — Tailwind build pipeline.
- `page-templates/`, `template-parts/`, `woocommerce/` — empty, populated in
  later stages.

## First-time setup on the staging site

After the first successful deploy:

1. In WP Admin, activate **Smart Pharmacy** under *Appearance → Themes*.
2. Flush permalinks: *Settings → Permalinks → Save Changes* (so the
   `/treatments/…` CPT URLs work).
3. Install and activate **Advanced Custom Fields Pro** if not already.
4. Install and activate **WooCommerce** if not already.
5. Visit any page — you'll see blank placeholders and the correct `<head>` /
   enqueued stylesheets / fonts. This is expected until Stage 2.

## Stage roadmap

See `../CLAUDE-smart-pharmacy.md` at the repo root for the full plan.

- ✅ **Stage 1** — Theme skeleton + ACF bootstrap + CI deploy.
- **Stage 2** — Port homepage from `index.html` into `front-page.php` +
  partials. Register A1–A4 ACF field groups.
- **Stage 3** — Port `weight-loss.html` into `single-treatment.php`. Register
  B1–E1 ACF field groups.
- **Stage 4** — WooCommerce template overrides; dequeue clashing WC styles.
- **Stage 5** — Remaining treatment pages + consultation eligibility checker.
