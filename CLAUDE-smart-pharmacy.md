# CLAUDE-smart-pharmacy.md

Project-specific guidance for future Claude sessions working on the Smart
Pharmacy theme. Read this before making changes.

## What this project is

A WordPress + WooCommerce theme for a UK online pharmacy client. The site
sells treatments (Wegovy, Finasteride, Viagra, HRT, etc.) alongside
educational treatment landing pages.

Repo: `drewskibro/140126-smart-pharmacy-latest-website`
Host: Kinsta (managed WordPress, premium plan, staging environment enabled).

## Architecture summary

**Pattern:** PharmoDigital pharmacy theme conventions (see
`drewskibro/pharmodigital-pharmacy-templates` for prior art: Denton, Easy
Pharmacy, Bowland). Smart Pharmacy follows the same conventions with two
deliberate divergences.

**Shared with other PharmoDigital themes:**
- Theme folder lives at repo root (`smart-pharmacy-theme/`), not a subfolder.
- ACF Pro is the content engine. Three-tier field resolution: page → options
  → hardcoded default.
- Field groups registered in code via `acf_add_local_field_group()`, letter-
  coded (A1, A2, B1, ...) in `inc/acf-fields.php`.
- **Where ACF fields live — decision tree:**
  - *Page-specific content* (homepage, individual pages, CPT posts) → page-level
    fields using `page_type == front_page`, `post == <id>`, `page_template == foo.php`,
    or `post_type == treatment` location rules. Gives clients the expected "edit
    this page" flow and gets revisions / previews / scheduling for free.
  - *Truly global content* (branding, nav, contact info, compliance, social URLs)
    → options pages. Changing once updates site-wide.
  - Options page registrations live in `inc/acf-options.php`. Currently:
    Branding, Contact, Compliance, Social, Navigation. No "Homepage" options
    page — homepage fields bind to the front page post directly.
- Page-specific CSS prefix: `sp_` (matching `dp_` for Denton, `ep_` for Easy).
- Deployed via GitHub Actions → SCP to Kinsta staging.
- Cache-busting uses `filemtime()` on CSS/JS files, never on `functions.php`.

**Divergences — Smart Pharmacy only:**
- **Tailwind CSS** is used alongside custom CSS. Tailwind is compiled in the
  GitHub Actions workflow before SCP, because Kinsta Managed WP does not run
  `npm`. The compiled output (`assets/css/tailwind.css`) is gitignored.
- **WooCommerce** support is enabled. First theme in the PharmoDigital suite
  with WC. Product landing page overrides will live in `woocommerce/`.
- **Treatment CPT** (`/treatments/…`) is registered. Treatment landing pages
  are educational; they link to one or more WooCommerce products (the actual
  SKUs) via an ACF relationship field.

## Critical patterns

### The three-tier helper

```php
sp_field( $name, $default = null, $post_id = null )
```

Used everywhere. Returns:
1. `get_field($name, $post_id)` if not null.
2. Otherwise `get_field($name, 'option')` if not null.
3. Otherwise `$default`.

**Strict `!== null` comparison is mandatory.** ACF `true_false` fields return
integer `0` for "No"; loose `empty()` / `!` would clobber an intentional "No"
with the options-tier or default value.

Escaping wrappers: `sp_field_e()` (HTML), `sp_field_attr()` (attrs),
`sp_field_url()` (URLs).

### Asset enqueuing

Always use `sp_asset_version( 'assets/css/foo.css' )` for the version arg.
This wraps `filemtime()` with a fallback to `SMART_PHARMACY_VERSION`.

Never bump `SMART_PHARMACY_VERSION` to force a cache bust — mtime handles it.

### ACF Pro is required, but degrade gracefully

`functions.php` shows an admin notice if ACF is missing. `sp_field()` falls
through to the default. Never call `get_field()` directly in templates —
always go through `sp_field()` so ACF-missing doesn't fatal-error the site.

## Deployment

Push to `main` → `.github/workflows/deploy-smart-pharmacy-to-kinsta.yml` fires:

1. Checkout repo.
2. Install Node 20.
3. `npm install` inside `smart-pharmacy-theme/`.
4. `npm run build` to compile Tailwind.
5. **Strip dev-only files** (`node_modules/`, `src/`, `package.json`,
   `tailwind.config.js`, `postcss.config.js`, `.gitignore`) — they must not
   ship to production.
6. SCP `smart-pharmacy-theme/` → `~/public/wp-content/themes/` on Kinsta.
7. SSH verify: check `style.css` and `tailwind.css` landed.

Kinsta SSH credentials live in GitHub Secrets:
`KINSTA_SSH_HOST`, `KINSTA_SSH_USER`, `KINSTA_SSH_PASSWORD`, `KINSTA_SSH_PORT`.
All four point at the **staging** environment. A parallel set of `*_PROD_*`
secrets + a separate workflow will be added when we're ready to go live.

**Never run `npm install` on the Kinsta server.** Managed WP doesn't allow it
and the workflow handles it.

## Stage plan

- ✅ **Stage 1** — Skeleton theme + Tailwind build + ACF bootstrapping +
  GitHub Actions deploy workflow.
- **Stage 2** — Port `index.html` (static prototype at repo root) into
  `front-page.php` + `template-parts/`. Register the A1–A4 ACF field groups
  for editable hero / grid / testimonials / stats.
- **Stage 3** — Port `weight-loss.html` into `single-treatment.php`. Register
  B1–E1 ACF field groups for treatment meta, benefits, FAQ, related products.
- **Stage 4** — WooCommerce template overrides under `woocommerce/`. Selective
  dequeue of default WC stylesheets that clash with Tailwind.
- **Stage 5** — Remaining treatment landing pages (hair loss, ED, women's
  health) + consultation eligibility checker integration (client has an
  existing pattern to adapt from prior sites).

## Static prototype at repo root

`index.html`, `weight-loss.html`, `styles.css`, `search-animation.js` at the
repo root are the **design prototype** for this theme. They are the source of
truth for visuals and get ported into PHP templates stage by stage. Do not
deploy these to Kinsta (the workflow path filter excludes them).

## Things to never do

- Never `git clone` on the Kinsta server. Deploy only via the workflow SCP.
- Never call `get_field()` directly in a template — use `sp_field()`.
- Never use `!empty()` / `!` / `== ''` to check ACF values — use `!== null`.
- Never bump `SMART_PHARMACY_VERSION` for cache busting — use `sp_asset_version()`.
- Never commit `tailwind.css` or `node_modules/` — both are gitignored.
- Never commit Kinsta SSH credentials to the repo.
- Never push directly to `main` without PR review once staging deploy is
  confirmed working.
