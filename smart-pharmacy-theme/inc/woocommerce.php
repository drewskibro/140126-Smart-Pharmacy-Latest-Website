<?php
/**
 * WooCommerce integration: style dequeue, content wrapper, shop-loop tidy.
 *
 * Stage 4a foundation. Subsequent stages build on this:
 *   - Stage 4a-2: archive-product.php + content-product.php overrides
 *   - Stage 4a-3: single-product.php override + tabbed product info
 *   - Stage 4a-4: cart + checkout brand pass
 *   - Stage 4b:   sidebar filters, search refinement
 *   - Stage 4c:   POM gating (hide Add-to-Cart for prescription products,
 *                 swap in "Start Consultation" routing to the linked
 *                 treatment landing page via B4 Treatment Meta)
 *
 * Loaded unconditionally from functions.php; the class_exists guard at the
 * top makes the file a no-op when WooCommerce is deactivated.
 *
 * @package SmartPharmacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// File is a no-op without WooCommerce. The hooks below would all silently
// fail to fire, but bailing early makes the intent obvious to any reader
// and avoids the (tiny) cost of registering callbacks that never run.
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/* ===============================================================
 * 1. STYLE DEQUEUE
 *
 * WooCommerce ships ~5 frontend stylesheets that target Storefront-
 * style markup and fight Tailwind for specificity (e.g. the default
 * `.product` flex layout overrides our card grid). We strip them
 * all -- the brand styling is built directly into our overridden
 * templates with Tailwind classes.
 *
 * The wc-blocks-* stylesheets that power the modern cart / checkout
 * blocks are enqueued via a separate code path (block-editor asset
 * registration, NOT the woocommerce_enqueue_styles filter) so they
 * are NOT affected by the empty-array return below. That is exactly
 * the behaviour we want -- modern cart / checkout depend on those
 * styles for interactivity. Stage 4a-4 will style around them.
 * =============================================================== */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/* ===============================================================
 * 2. CONTENT WRAPPER
 *
 * WooCommerce wraps every WC page (shop, single product, cart,
 * checkout, my-account) with its own `<div id="primary"><main id="main">`
 * via the woocommerce_output_content_wrapper action. We already have
 * a <main id="content"> opened by header.php, so nesting another
 * <main> would be invalid HTML and break landmark semantics.
 *
 * Strip WC's wrapper and replace with our brand <section> + max-width
 * container -- the same outer pattern used by every Stage 3 template
 * part (treatment pages, A8 Bestsellers, etc.).  WC pages now sit
 * inside the same visual frame as the rest of the site.
 * =============================================================== */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Open the brand wrapper around WooCommerce content.
 */
function sp_wc_wrapper_open() {
	echo '<section class="relative bg-white box-border break-words overflow-hidden py-12 md:py-16">';
	echo '<div class="relative box-border max-w-[1400px] break-words z-10 mx-auto px-6 md:px-16">';
}
add_action( 'woocommerce_before_main_content', 'sp_wc_wrapper_open', 10 );

/**
 * Close the brand wrapper around WooCommerce content.
 */
function sp_wc_wrapper_close() {
	echo '</div></section>';
}
add_action( 'woocommerce_after_main_content', 'sp_wc_wrapper_close', 10 );

/* ===============================================================
 * 3. SHOP LOOP TIDY
 *
 * Strip the default result-count ("Showing 1-12 of 47 products")
 * and the catalog ordering dropdown from above the shop archive.
 * Stage 4a-2 will re-add brand-styled equivalents inside the
 * archive-product.php override.
 *
 * Notices (priority 10) are KEPT -- they handle add-to-cart success
 * messages, low-stock alerts, etc., and are styled by WC's blocks
 * which are still loaded.
 * =============================================================== */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

/* ===============================================================
 * 4. LOOP DIMENSIONS
 *
 * 3 columns matches our card-grid heuristic used everywhere else
 * (B3 Treatment Options, A8 Bestsellers, E1 Related Products) and
 * fits the max-w-[1400px] container cleanly with 6 of horizontal
 * gutter on each side. 12 products per page = 4 rows of 3, a
 * comfortable archive length without scroll fatigue.
 * =============================================================== */
add_filter(
	'loop_shop_columns',
	function () {
		return 3;
	}
);

add_filter(
	'loop_shop_per_page',
	function () {
		return 12;
	}
);
