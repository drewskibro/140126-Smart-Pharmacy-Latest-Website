<?php
/**
 * Shop archive (Stage 4a-2 brand override).
 *
 * Drives /shop/ and /product-category/{slug}/.  Replaces WC's default
 * archive-product.php with a brand layout: gradient page header on
 * top, 3-col card grid in the middle, pagination at the bottom.
 *
 * Hooks preserved (intentional):
 *   - woocommerce_before_main_content  → sp_wc_wrapper_open()  - 10
 *                                      → sp_wc_page_header()   - 15
 *   - woocommerce_archive_description  → category descriptions, rendered
 *                                        inside sp_wc_page_header()
 *   - woocommerce_before_shop_loop     → notices (priority 10)
 *   - woocommerce_after_shop_loop      → pagination (priority 10)
 *   - woocommerce_no_products_found    → empty-state template
 *   - woocommerce_after_main_content   → sp_wc_wrapper_close()
 *
 * Hooks intentionally NOT re-added here:
 *   - woocommerce_result_count   (Stage 4a-1 stripped it; Stage 4b
 *                                  will re-add a brand-styled version)
 *   - woocommerce_catalog_ordering (same — sort dropdown lands in 4b)
 *
 * The ul.products / li.product wrapper that WC normally emits via
 * woocommerce_product_loop_start/_end is replaced with an explicit
 * Tailwind <div> grid here; that lets us match the rest of the site
 * (3-col responsive grid identical to A8/E1) instead of fighting WC's
 * .products flex CSS.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked sp_wc_wrapper_open - 10  (inc/woocommerce.php)
 * @hooked sp_wc_page_header  - 15  (inc/woocommerce.php, shared across
 *                                    shop / cart / checkout / account)
 */
do_action( 'woocommerce_before_main_content' );

/**
 * Hook: woocommerce_before_shop_loop.
 *
 * @hooked woocommerce_output_all_notices - 10  (kept)
 * @hooked woocommerce_result_count       - 20  (removed in Stage 4a-1)
 * @hooked woocommerce_catalog_ordering   - 30  (removed in Stage 4a-1)
 */
do_action( 'woocommerce_before_shop_loop' );

if ( woocommerce_product_loop() ) :
	?>
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
		<?php
		while ( have_posts() ) :
			the_post();
			/**
			 * Each iteration loads woocommerce/content-product.php
			 * (our brand override at the same path).
			 */
			wc_get_template_part( 'content', 'product' );
		endwhile;
		?>
	</div>
	<?php
	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10  (kept; default WC pagination
	 *                                       markup, restyled in 4b)
	 */
	do_action( 'woocommerce_after_shop_loop' );
else :
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10  (renders woocommerce/loop/
	 *                                     no-products-found.php — our
	 *                                     branded empty state)
	 */
	do_action( 'woocommerce_no_products_found' );
endif;

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked sp_wc_wrapper_close - 10  (inc/woocommerce.php)
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
