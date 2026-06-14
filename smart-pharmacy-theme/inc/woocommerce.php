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
 *                 treatment landing page via B4 Treatment Meta + E1
 *                 relationship)
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

	// /shop/ gets the rich Stage 4d hero (template-parts/shop/hero.php)
	// instead of the compact gradient header.
	if ( is_shop() ) {
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

	if ( is_product_taxonomy() ) {
		$title = woocommerce_page_title( false );
		$term  = get_queried_object();
		$subhead = ( $term && ! empty( $term->description ) )
			? wp_strip_all_tags( $term->description )
			: '';

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
 * 6. SHOP SIDEBAR (Stage 4b → revisited Stage 4b-r)
 *
 * Originally registered a widget area and only showed the sidebar
 * when editors populated it. Stage 4b-revisit ships a hardcoded
 * branded filter panel (template-parts/shop/filters.php) that is
 * always visible on the shop / category archives, because relying
 * on admin widget setup for the filter UX turned out to be both
 * fragile and visually inconsistent.
 *
 * The widget area stays registered -- it renders BELOW the hardcoded
 * filter panel if populated, so the client can still drop extra
 * widgets (e.g. a promo banner, custom HTML block) without touching
 * code. Just no longer required for filters to appear.
 *
 * Intended population (optional now; for the client during admin setup):
 *   - Promo banner (e.g. "Free NHS prescriptions")
 *   - Custom HTML / text blocks
 *   - Any Gutenberg block the admin wants in the sidebar
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
 * Should the two-column sidebar layout render on this archive?
 *
 * Always true on /shop/ and /product-category/{slug}/ now -- the
 * hardcoded filter panel doesn't need any admin setup to appear.
 * Kept as a helper so archive-product.php stays readable and so
 * Stage 4c+ can introduce device-specific rules (e.g. hide the
 * sidebar on /product-tag/ pages) in one place.
 *
 * @return bool
 */
function sp_wc_has_shop_sidebar() {
	return is_shop() || is_product_taxonomy();
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

/* ===============================================================
 * 8. POM GATING (Stage 4c)
 *
 * Prescription-Only Medicines can't be sold without a consultation.
 * A product is treated as POM when the Treatment it's linked to
 * (via ACF E1 `tx_related_products`) has B4 `tx_meta_legal_class`
 * set to "POM" OR `tx_meta_requires_consultation` ticked.
 *
 * When flagged:
 *   - content-product.php and PDP hide the Add-to-Cart button and
 *     swap in a gradient "Start Consultation" button that deep-links
 *     to the linked treatment's landing page.
 *   - The product is marked non-purchasable server-side so direct
 *     ?add-to-cart=ID URLs and AJAX calls also bounce.
 *   - An orange "Prescription only" eyebrow chip surfaces everywhere
 *     a product card renders (archive, Bestsellers, Related).
 *
 * Data flow
 * ---------
 *   1. Editor picks WC products on a Treatment in the E1 field.
 *   2. acf/save_post below mirrors each product's linked treatment
 *      ID onto `_sp_linked_treatment_id` postmeta so product-side
 *      lookups are O(1).
 *   3. sp_product_is_pom() reads B4 off that treatment.
 *
 * If a product is linked to multiple treatments (E1 is multi-select,
 * rare but legal), any POM flag wins -- GSL-safe fallback products
 * can't "downgrade" a POM on a sibling treatment.
 * =============================================================== */

/**
 * Resolve which Treatment (if any) this product is linked to.
 *
 * Returns the treatment post ID stored in `_sp_linked_treatment_id`
 * postmeta (populated by the acf/save_post hook below). Products
 * created before a treatment was saved won't have this meta yet,
 * so sp_wc_backfill_product_treatment_links() can be run once from
 * an admin tool to populate historical data.
 *
 * @param int $product_id WC product ID.
 * @return int Treatment post ID, or 0 if unlinked.
 */
function sp_product_linked_treatment( $product_id ) {
	$treatment_id = (int) get_post_meta( (int) $product_id, '_sp_linked_treatment_id', true );
	if ( $treatment_id > 0 && 'treatment' === get_post_type( $treatment_id ) && 'publish' === get_post_status( $treatment_id ) ) {
		return $treatment_id;
	}
	return 0;
}

/**
 * Is this product a Prescription-Only Medicine?
 *
 * @param int $product_id WC product ID.
 * @return bool
 */
function sp_product_is_pom( $product_id ) {
	static $cache = array();
	$product_id = (int) $product_id;
	if ( isset( $cache[ $product_id ] ) ) {
		return $cache[ $product_id ];
	}

	$treatment_id = sp_product_linked_treatment( $product_id );
	if ( ! $treatment_id ) {
		return $cache[ $product_id ] = false;
	}

	if ( ! function_exists( 'get_field' ) ) {
		return $cache[ $product_id ] = false;
	}

	$legal   = get_field( 'tx_meta_legal_class', $treatment_id );
	$consult = get_field( 'tx_meta_requires_consultation', $treatment_id );

	$is_pom = ( 'POM' === $legal ) || ( true === (bool) $consult );
	return $cache[ $product_id ] = $is_pom;
}

/**
 * URL for the "Start Consultation" CTA on a POM product.
 *
 * Resolution order:
 *   1. The dedicated eligibility-checker page configured in the
 *      Smart Pharmacy Eligibility plugin (Eligibility -> Settings).
 *      Goes straight to the consultation form -- shortest funnel.
 *   2. The linked treatment landing page (legacy fallback for
 *      products linked to a treatment via the E1 relationship).
 *   3. The treatment archive so the CTA never dead-ends.
 *
 * @param int $product_id WC product ID.
 * @return string URL.
 */
function sp_product_consultation_url( $product_id ) {
	// Prefer the eligibility checker page when the plugin is active
	// and an admin has filled in the URL field. The method_exists
	// guard prevents fatals when the theme is ahead of the plugin
	// (e.g. theme deployed but plugin not yet reinstalled).
	if ( class_exists( 'SPE_Admin' ) && method_exists( 'SPE_Admin', 'get_checker_url' ) ) {
		$checker_url = SPE_Admin::get_checker_url();
		if ( $checker_url && home_url( '/' ) !== $checker_url ) {
			return $checker_url;
		}
	}

	$treatment_id = sp_product_linked_treatment( $product_id );
	if ( $treatment_id ) {
		return (string) get_permalink( $treatment_id );
	}
	return (string) get_post_type_archive_link( 'treatment' );
}

/**
 * Mirror E1 relationship field onto each linked product as postmeta.
 *
 * Runs on acf/save_post for the treatment CPT. Cleans up any
 * previously-linked products that were removed from the field, so
 * orphan mirrors don't linger.
 *
 * @param int $post_id Post ID being saved.
 */
function sp_wc_mirror_e1_to_products( $post_id ) {
	if ( 'treatment' !== get_post_type( $post_id ) ) {
		return;
	}
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$current = get_field( 'tx_related_products', $post_id );
	$current = is_array( $current ) ? array_map( 'intval', $current ) : array();

	// Find products that previously pointed at THIS treatment.
	$previous = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_sp_linked_treatment_id',
			'meta_value'     => $post_id,
			'no_found_rows'  => true,
		)
	);
	$previous = array_map( 'intval', (array) $previous );

	// Write new links.
	foreach ( $current as $product_id ) {
		update_post_meta( $product_id, '_sp_linked_treatment_id', (int) $post_id );
	}

	// Clear stale links (products that were removed from the E1 field).
	foreach ( array_diff( $previous, $current ) as $orphan_id ) {
		delete_post_meta( $orphan_id, '_sp_linked_treatment_id', (int) $post_id );
	}
}
add_action( 'acf/save_post', 'sp_wc_mirror_e1_to_products', 20 );

/**
 * Mark POM products as non-purchasable so direct URLs + AJAX get
 * rejected by WC's own validation rather than silently succeeding.
 *
 * If the Smart Pharmacy Eligibility plugin is active AND the visitor
 * has completed the assessment for this product, we yield and allow
 * the purchase -- otherwise the post-assessment add-to-cart would
 * paradoxically fail because of our own gating filter.
 *
 * @param bool       $purchasable Current WC state.
 * @param WC_Product $product     Product being tested.
 * @return bool
 */
function sp_wc_pom_not_purchasable( $purchasable, $product ) {
	if ( ! $purchasable || ! sp_product_is_pom( $product->get_id() ) ) {
		return $purchasable;
	}

	// Eligibility-completed customers can purchase POM products.
	if ( class_exists( 'SPE_WooCommerce_Integration' )
		&& SPE_WooCommerce_Integration::has_completed_eligibility( $product->get_id() ) ) {
		return true;
	}

	return false;
}
add_filter( 'woocommerce_is_purchasable', 'sp_wc_pom_not_purchasable', 10, 2 );

/**
 * Replace the archive-loop Add-to-Cart link with a consultation CTA.
 *
 * Filters `woocommerce_loop_add_to_cart_link`. content-product.php
 * emits its own CTA markup and therefore doesn't go through this
 * filter, but third-party templates / A8 Bestsellers / E1 Related
 * Products that call woocommerce_template_loop_add_to_cart() do,
 * so the consultation swap still happens for them.
 *
 * @param string     $html    Default WC button HTML.
 * @param WC_Product $product Product being rendered.
 * @return string
 */
function sp_wc_pom_loop_cta( $html, $product ) {
	if ( ! sp_product_is_pom( $product->get_id() ) ) {
		return $html;
	}

	return sprintf(
		'<a href="%s" class="sp-pom-consult-cta text-white text-sm font-semibold bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] px-6 py-3 rounded-full hover:shadow-lg transition-all inline-block">%s</a>',
		esc_url( sp_product_consultation_url( $product->get_id() ) ),
		esc_html__( 'Start Consultation', 'smart-pharmacy' )
	);
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'sp_wc_pom_loop_cta', 10, 2 );

/**
 * On the PDP: remove WC's add-to-cart markup for POM products and
 * emit a branded consultation block in its place.
 *
 * WC hooks woocommerce_template_single_add_to_cart at priority 30 on
 * woocommerce_single_product_summary. We bump past 30 after the
 * current post is resolved so we only strip on POM products.
 */
function sp_wc_pom_pdp_swap_add_to_cart() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	if ( ! sp_product_is_pom( $product->get_id() ) ) {
		return;
	}

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_single_product_summary', 'sp_wc_pom_pdp_consultation_block', 30 );
}
add_action( 'woocommerce_before_single_product', 'sp_wc_pom_pdp_swap_add_to_cart', 20 );

/**
 * Renders the POM consultation block on the PDP summary column.
 */
function sp_wc_pom_pdp_consultation_block() {
	global $product;
	$url = sp_product_consultation_url( $product->get_id() );
	?>
	<div class="sp-pom-pdp-block">
		<div class="sp-pom-pdp-block__badge">
			<?php echo sp_icon( 'shield', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'Prescription-only medicine', 'smart-pharmacy' ); ?></span>
		</div>
		<h3 class="sp-pom-pdp-block__title"><?php esc_html_e( 'Free consultation required', 'smart-pharmacy' ); ?></h3>
		<p class="sp-pom-pdp-block__body">
			<?php esc_html_e( 'This treatment is a UK-regulated prescription medicine. Complete a short, secure consultation with our GPhC-registered prescriber and we\'ll confirm suitability before dispensing. Usually under 5 minutes.', 'smart-pharmacy' ); ?>
		</p>
		<a href="<?php echo esc_url( $url ); ?>" class="sp-pom-pdp-block__cta">
			<?php esc_html_e( 'Start Consultation', 'smart-pharmacy' ); ?>
			<?php echo sp_icon( 'check', 'w-4 h-4 ml-2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<ul class="sp-pom-pdp-block__assurances">
			<li><?php echo sp_icon( 'lock', 'w-4 h-4 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Confidential — clinician-reviewed', 'smart-pharmacy' ); ?></span></li>
			<li><?php echo sp_icon( 'truck', 'w-4 h-4 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Discreet tracked dispatch', 'smart-pharmacy' ); ?></span></li>
			<li><?php echo sp_icon( 'shield', 'w-4 h-4 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'GPhC-registered UK pharmacy', 'smart-pharmacy' ); ?></span></li>
		</ul>
	</div>
	<?php
}

/* ===============================================================
 * 9. SHOP FILTER QUERY + PANEL (Stage 4b-revisit)
 *
 * Replaces the widget-driven sidebar filtering with a self-contained
 * filter panel whose state lives entirely in URL query params:
 *
 *   ?sp_cat[]=vitamins&sp_cat[]=pain-relief    product_cat terms
 *   ?sp_min_price=0&sp_max_price=50            price range
 *   ?sp_rating[]=4&sp_rating[]=5               minimum ratings
 *   ?sp_stock=instock|onbackorder              stock status
 *
 * Keeping state in GET params means:
 *   - filtered shop URLs are shareable / bookmarkable
 *   - pagination + sort preserve filters automatically (WC's
 *     add_query_arg-based links inherit the whole query string)
 *   - no JS / AJAX plumbing; submit is a plain <form method="get">
 *
 * Rating filter uses _wc_average_rating postmeta (written by WC
 * whenever a review is approved) so no extra seeding is needed;
 * it just takes a few reviews per product to show up.
 * =============================================================== */

/**
 * Apply the sp_* GET filters to the main shop query.
 *
 * Hooks woocommerce_product_query which fires on shop + category
 * + tag archives before the main loop runs. Safe to no-op when no
 * filters are present.
 *
 * @param WP_Query $q The WC main product query.
 */
function sp_wc_apply_shop_filters( $q ) {
	$tax_query  = (array) $q->get( 'tax_query', array() );
	$meta_query = (array) $q->get( 'meta_query', array() );

	// Category filter: multi-select checkboxes.
	$cats = isset( $_GET['sp_cat'] ) ? (array) wp_unslash( $_GET['sp_cat'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$cats = array_filter( array_map( 'sanitize_title', $cats ) );
	if ( ! empty( $cats ) ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $cats,
			'operator' => 'IN',
		);
	}

	// Price range.
	$min_price = isset( $_GET['sp_min_price'] ) ? (float) $_GET['sp_min_price'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$max_price = isset( $_GET['sp_max_price'] ) ? (float) $_GET['sp_max_price'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $min_price > 0 || $max_price > 0 ) {
		$range = array();
		if ( $min_price > 0 ) {
			$range[] = $min_price;
		}
		if ( $max_price > 0 ) {
			$range[] = $max_price;
		}
		if ( count( $range ) === 2 ) {
			$meta_query[] = array(
				'key'     => '_price',
				'value'   => array( $range[0], $range[1] ),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			);
		} else {
			$meta_query[] = array(
				'key'     => '_price',
				'value'   => $range[0],
				'compare' => $min_price > 0 ? '>=' : '<=',
				'type'    => 'NUMERIC',
			);
		}
	}

	// Rating: keep only products with _wc_average_rating >= min of the ticked values.
	$ratings = isset( $_GET['sp_rating'] ) ? (array) wp_unslash( $_GET['sp_rating'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$ratings = array_filter( array_map( 'intval', $ratings ) );
	if ( ! empty( $ratings ) ) {
		$meta_query[] = array(
			'key'     => '_wc_average_rating',
			'value'   => min( $ratings ),
			'compare' => '>=',
			'type'    => 'DECIMAL',
		);
	}

	// Stock status.
	$stock = isset( $_GET['sp_stock'] ) ? sanitize_key( wp_unslash( $_GET['sp_stock'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( in_array( $stock, array( 'instock', 'onbackorder' ), true ) ) {
		$meta_query[] = array(
			'key'     => '_stock_status',
			'value'   => $stock,
			'compare' => '=',
		);
	}

	$q->set( 'tax_query', $tax_query );
	$q->set( 'meta_query', $meta_query );
}
add_action( 'woocommerce_product_query', 'sp_wc_apply_shop_filters' );

/**
 * Highest product price in the shop (or in the currently-viewed
 * category) — used by the filter panel to configure the price-range
 * slider's max attribute without hardcoding a magic number.
 *
 * Cached per request; WC stores _price as sortable meta so the
 * ORDER BY query is cheap.
 *
 * @return int Ceil'd highest price, or 300 as a sensible default.
 */
function sp_wc_shop_max_price() {
	static $max = null;
	if ( null !== $max ) {
		return $max;
	}

	global $wpdb;
	$price = (float) $wpdb->get_var( "SELECT MAX(meta_value+0) FROM {$wpdb->postmeta} WHERE meta_key = '_price'" );
	$max   = $price > 0 ? (int) ceil( $price / 10 ) * 10 : 300;
	return $max;
}

/* ===============================================================
 * 10. CATEGORY COLOUR MAPPING (Stage 4d)
 *
 * Product cards, category tiles on the shop hero, and any future
 * category-aware UI share the same slug -> colour-key mapping so
 * a "Vitamins" tile, a "Vitamins" eyebrow on a product card, and
 * a "Vitamins" filter pill all wear the same brand accent.
 *
 * Tailwind JIT needs literal class strings, so the colour-key
 * lookup maps return pre-baked class names rather than composing
 * them dynamically (e.g. "text-${colour}-600" would not compile).
 * =============================================================== */

/**
 * Slug → colour-key lookup (extend as new categories are seeded).
 *
 * @return array<string,string>
 */
function sp_wc_category_colour_map() {
	return apply_filters(
		'sp_wc_category_colour_map',
		array(
			'pain-relief'       => 'red',
			'vitamins'          => 'orange',
			'first-aid'         => 'green',
			'weight-management' => 'teal',
			'mens-health'       => 'blue',
			'womens-health'     => 'purple',
			'hair-care'         => 'blue',
			'cold-flu'          => 'green',
			'sexual-wellness'   => 'purple',
			'skincare'          => 'pink',
		)
	);
}

/**
 * Resolve a colour bundle for a given product-category slug.
 *
 * Each bundle has the four class strings consumed by our UI: the
 * tinted gradient (bg), tile border, eyebrow text colour, and
 * "Shop Now" CTA text colour. Unknown slugs fall back to teal.
 *
 * @param string $slug product_cat term slug.
 * @return array{bg:string,border:string,text:string,cta:string}
 */
function sp_wc_category_colour_classes( $slug ) {
	$map    = sp_wc_category_colour_map();
	$colour = isset( $map[ $slug ] ) ? $map[ $slug ] : 'teal';

	$bundles = array(
		'teal'   => array( 'bg' => 'bg-gradient-to-br from-teal-50 to-cyan-50',   'border' => 'border-teal-100',   'text' => 'text-teal-600',   'cta' => 'text-teal-600' ),
		'purple' => array( 'bg' => 'bg-gradient-to-br from-purple-50 to-violet-50','border' => 'border-purple-100', 'text' => 'text-purple-600', 'cta' => 'text-teal-600' ),
		'green'  => array( 'bg' => 'bg-gradient-to-br from-green-50 to-emerald-50','border' => 'border-green-100',  'text' => 'text-green-600',  'cta' => 'text-teal-600' ),
		'orange' => array( 'bg' => 'bg-gradient-to-br from-orange-50 to-amber-50', 'border' => 'border-orange-100', 'text' => 'text-orange-600', 'cta' => 'text-teal-600' ),
		'red'    => array( 'bg' => 'bg-gradient-to-br from-red-50 to-rose-50',    'border' => 'border-red-100',    'text' => 'text-red-600',    'cta' => 'text-teal-600' ),
		'blue'   => array( 'bg' => 'bg-gradient-to-br from-blue-50 to-cyan-50',   'border' => 'border-blue-100',   'text' => 'text-blue-600',   'cta' => 'text-teal-600' ),
		'yellow' => array( 'bg' => 'bg-gradient-to-br from-yellow-50 to-amber-50','border' => 'border-yellow-100', 'text' => 'text-amber-600',  'cta' => 'text-teal-600' ),
		'pink'   => array( 'bg' => 'bg-gradient-to-br from-pink-50 to-rose-50',   'border' => 'border-pink-100',   'text' => 'text-pink-600',   'cta' => 'text-teal-600' ),
	);

	return isset( $bundles[ $colour ] ) ? $bundles[ $colour ] : $bundles['teal'];
}

/**
 * Resolve a thumbnail image URL for a product category tile.
 *
 * Resolution order:
 *   1. The category's own thumbnail (WC "Thumbnail" field on the
 *      term edit screen — `thumbnail_id` term meta).
 *   2. The featured image of the most recent published product in
 *      that category — zero admin work, sensible default.
 *
 * Returns empty string when nothing is available, in which case the
 * template renders a neutral placeholder.
 *
 * Cached per-term in a request-scoped static so the inner WP_Query
 * runs at most once per category per page load.
 *
 * @param int    $term_id product_cat term ID.
 * @param string $size    Image size (default 'medium').
 * @return string URL or empty string.
 */
function sp_wc_category_thumb_url( $term_id, $size = 'medium' ) {
	static $cache = array();
	$cache_key = $term_id . '|' . $size;
	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	$thumb_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
	if ( $thumb_id > 0 ) {
		$url = wp_get_attachment_image_url( $thumb_id, $size );
		if ( $url ) {
			return $cache[ $cache_key ] = $url;
		}
	}

	$products = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => (int) $term_id,
					'include_children' => true,
				),
			),
			'meta_query'     => array(
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
			'no_found_rows'  => true,
		)
	);
	if ( ! empty( $products ) ) {
		$url = get_the_post_thumbnail_url( (int) $products[0], $size );
		if ( $url ) {
			return $cache[ $cache_key ] = $url;
		}
	}

	return $cache[ $cache_key ] = '';
}

/* ===============================================================
 * 11. BRANDED EMPTY-CART STATE (Stage 4d follow-up)
 *
 * WC's block cart auto-renders a "Your basket is currently empty!"
 * heading + sad-face emoji + "New in store" cross-sell block when
 * the cart is empty. Functional, but looks generic and doesn't
 * route the visitor anywhere brand-appropriate.
 *
 * We inject a branded panel via the_content on the cart page when
 * cart is empty, then hide WC's default heading + emoji via CSS in
 * styles.css. Cross-sells stay -- they're useful for discovery.
 * =============================================================== */

/**
 * Prepend the branded empty-cart panel to the cart page content.
 *
 * @param string $content Existing page content (the cart block).
 * @return string
 */
function sp_wc_branded_empty_cart( $content ) {
	if ( ! is_cart() ) {
		return $content;
	}
	if ( ! function_exists( 'WC' ) || ! WC()->cart || ! WC()->cart->is_empty() ) {
		return $content;
	}

	ob_start();
	get_template_part( 'template-parts/shop/empty-cart' );
	$branded = ob_get_clean();

	return $branded . $content;
}
add_filter( 'the_content', 'sp_wc_branded_empty_cart', 5 );
