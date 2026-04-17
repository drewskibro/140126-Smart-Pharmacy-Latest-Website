<?php
/**
 * WooCommerce integration: style dequeue, content wrapper, shop-loop tidy.
 *
 * Stage 4a foundation. Subsequent stages build on this:
 *   - Stage 4a-2: archive-product.php + content-product.php overrides
 *   - Stage 4a-3: single-product.php override + tabbed product info
 *   - Stage 4a-4: cart + checkout + my-account brand pass
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

/* ===============================================================
 * 5. BRANDED PAGE HEADER (Stage 4a-4)
 *
 * Every WooCommerce page except the single product renders the
 * same eyebrow-pill + gradient title + subheading header used on
 * the shop archive, so cart / checkout / my-account visually sit
 * inside the same brand frame.
 *
 * Hooked onto woocommerce_before_main_content at priority 15 so
 * it fires immediately after sp_wc_wrapper_open (priority 10) and
 * before WC's own content.  is_product() is excluded -- the PDP
 * lays out the product title inside its own two-column summary.
 * is_order_received_page() is also excluded so the thank-you
 * screen's "Order received" block stands on its own.
 * =============================================================== */

/**
 * Resolve (icon, eyebrow, title, subheading) for the current WC page.
 *
 * Returns null when no branded header should render (single product,
 * order-received, unrecognised contexts).
 *
 * @return array{icon:string,eyebrow:string,title:string,subheading:string}|null
 */
function sp_wc_page_header_context() {
	if ( is_product() || is_order_received_page() ) {
		return null;
	}

	if ( is_cart() ) {
		return array(
			'icon'       => 'check_circle',
			'eyebrow'    => __( 'Your', 'smart-pharmacy' ),
			'eyebrow_em' => __( 'Basket', 'smart-pharmacy' ),
			'title'      => __( 'Shopping Cart', 'smart-pharmacy' ),
			'subheading' => __( 'Review your items before heading to secure checkout. Free UK delivery on orders over £30.', 'smart-pharmacy' ),
		);
	}

	if ( is_checkout() ) {
		return array(
			'icon'       => 'lock',
			'eyebrow'    => __( 'Secure', 'smart-pharmacy' ),
			'eyebrow_em' => __( 'Checkout', 'smart-pharmacy' ),
			'title'      => __( 'Checkout', 'smart-pharmacy' ),
			'subheading' => __( 'Discreet packaging, tracked dispatch, and your payment details encrypted end-to-end.', 'smart-pharmacy' ),
		);
	}

	if ( is_account_page() ) {
		return array(
			'icon'       => 'person',
			'eyebrow'    => __( 'Your', 'smart-pharmacy' ),
			'eyebrow_em' => __( 'Account', 'smart-pharmacy' ),
			'title'      => is_user_logged_in() ? __( 'My Account', 'smart-pharmacy' ) : __( 'Sign In', 'smart-pharmacy' ),
			'subheading' => is_user_logged_in()
				? __( 'Manage your orders, addresses, prescriptions, and consultation history.', 'smart-pharmacy' )
				: __( 'Sign in to access your orders and consultations, or create a new account in under a minute.', 'smart-pharmacy' ),
		);
	}

	if ( is_shop() || is_product_taxonomy() ) {
		$title    = woocommerce_page_title( false );
		$subhead  = '';
		if ( is_product_category() || is_product_tag() ) {
			$term    = get_queried_object();
			$subhead = ( $term && ! empty( $term->description ) )
				? wp_strip_all_tags( $term->description )
				: '';
		} elseif ( is_shop() ) {
			$subhead = __( 'Browse our full pharmacy range. Genuine UK-licensed products, dispatched discreetly with free delivery on orders over £30.', 'smart-pharmacy' );
		}

		return array(
			'icon'       => 'truck',
			'eyebrow'    => __( 'Smart Pharmacy', 'smart-pharmacy' ),
			'eyebrow_em' => __( 'Shop', 'smart-pharmacy' ),
			'title'      => $title,
			'subheading' => $subhead,
		);
	}

	return null;
}

/**
 * Emit the branded WooCommerce page header.
 *
 * Echoes nothing if sp_wc_page_header_context() returns null. The
 * archive-product.php override also picks up the WC archive
 * description via the woocommerce_archive_description hook inside
 * this markup, so category long-form copy still renders on first
 * page load only (is_paged() guards against duplication).
 */
function sp_wc_page_header() {
	$ctx = sp_wc_page_header_context();
	if ( null === $ctx ) {
		return;
	}
	?>
	<header class="box-border break-words text-center mb-12 md:mb-16">
		<div class="items-center backdrop-blur-sm bg-white/80 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border gap-x-3 inline-flex break-words gap-y-3 border border-gray-100 mb-6 px-6 py-3 rounded-full border-solid">
			<div class="items-center bg-[linear-gradient(to_right_bottom,rgb(59,155,159),rgb(44,122,126))] box-border flex h-10 justify-center break-words w-10 rounded-full">
				<?php echo sp_icon( $ctx['icon'], 'w-5 h-5 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<span class="text-neutral-900 text-base font-bold box-border block leading-6 break-words">
				<?php echo esc_html( $ctx['eyebrow'] ); ?>
				<span class="text-teal-500"> <?php echo esc_html( $ctx['eyebrow_em'] ); ?></span>
			</span>
			<div class="bg-teal-500 box-border h-2 break-words w-2 rounded-full"></div>
		</div>

		<h1 class="text-neutral-900 text-4xl font-black box-border leading-[1.1] break-words mb-4 md:text-6xl">
			<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( $ctx['title'] ); ?></span>
		</h1>

		<?php if ( $ctx['subheading'] ) : ?>
			<p class="text-neutral-600 text-lg box-border leading-[1.6] break-words max-w-3xl mx-auto md:text-xl"><?php echo esc_html( $ctx['subheading'] ); ?></p>
		<?php endif; ?>

		<?php
		// Category / tag long-form description (HTML from the term editor).
		// Kept here so archive taxonomies show it under the branded header.
		if ( ( is_product_category() || is_product_tag() ) && ! is_paged() ) {
			echo '<div class="prose mx-auto mt-6 text-neutral-600">';
			do_action( 'woocommerce_archive_description' );
			echo '</div>';
		}
		?>
	</header>
	<?php
}
add_action( 'woocommerce_before_main_content', 'sp_wc_page_header', 15 );

/* ===============================================================
 * 6. SHOP SIDEBAR (Stage 4b)
 *
 * Registers a `shop-sidebar` widget area that appears to the left
 * of the product grid on /shop/ and /product-category/{slug}/ when
 * populated.  Left empty, archive-product.php falls through to a
 * full-width grid -- so the shop still looks right before an admin
 * ever opens Appearance -> Widgets.
 *
 * Intended population (for the client during admin setup):
 *   - Filter Products by Price           (WC block / widget)
 *   - Filter Products by Attribute       (WC block / widget)
 *   - Filter Products by Stock           (WC block / widget)
 *   - Active Filters                     (WC block / widget)
 *   - Product Categories                 (WC or WP block)
 *
 * Classic widgets AND Gutenberg blocks both render into this area
 * because register_sidebar() is agnostic to either.
 * =============================================================== */

/**
 * Register the shop sidebar.
 */
function sp_wc_register_shop_sidebar() {
	register_sidebar(
		array(
			'name'          => __( 'Shop Sidebar', 'smart-pharmacy' ),
			'id'            => 'shop-sidebar',
			'description'   => __( 'Filter widgets shown beside the product grid on the shop and category archives. Typical widgets: Filter by Price, Filter by Stock, Active Filters, Product Categories.', 'smart-pharmacy' ),
			'before_widget' => '<section id="%1$s" class="sp-shop-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="sp-shop-widget__title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'sp_wc_register_shop_sidebar' );

/**
 * Is the shop sidebar populated on the current archive?
 *
 * Centralised so the archive template can decide between a two-
 * column layout and a full-width layout without re-querying the
 * widget state in two places.
 *
 * @return bool
 */
function sp_wc_has_shop_sidebar() {
	return ( is_shop() || is_product_taxonomy() )
		&& is_active_sidebar( 'shop-sidebar' );
}

/* ===============================================================
 * 7. SHOP LOOP HEADER: RESULT COUNT + ORDERING (Stage 4b)
 *
 * Re-add the hooks stripped in Stage 4a-1 so the archive shows
 * "Showing 1-12 of 47 products" and a sort dropdown above the
 * grid, now wrapped in a branded flex row.  The outputs themselves
 * still come from woocommerce_result_count / woocommerce_catalog_
 * ordering -- we only handle the wrapper + the brand styling in
 * styles.css.
 *
 * Priority 15 for the opener (before result_count@20 and
 * catalog_ordering@30), priority 35 for the closer (after both).
 * =============================================================== */
add_action( 'woocommerce_before_shop_loop', 'sp_wc_shop_loop_header_open', 15 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'sp_wc_shop_loop_header_close', 35 );

/**
 * Open the flex wrapper around result count + ordering.
 */
function sp_wc_shop_loop_header_open() {
	if ( ! woocommerce_product_loop() ) {
		return;
	}
	echo '<div class="sp-shop-loop-header">';
}

/**
 * Close the flex wrapper.
 */
function sp_wc_shop_loop_header_close() {
	if ( ! woocommerce_product_loop() ) {
		return;
	}
	echo '</div>';
}
