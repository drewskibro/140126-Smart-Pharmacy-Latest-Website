<?php
/**
 * Shop archive (Stage 4a-2 brand override).
 *
 * Drives /shop/ and /product-category/{slug}/.  Replaces WC's default
 * archive-product.php with a brand layout: gradient page header on
 * top, an optional filter sidebar on the left, a 3-col card grid on
 * the right, pagination underneath.
 *
 * Hooks preserved (intentional):
 *   - woocommerce_before_main_content  → sp_wc_wrapper_open()  - 10
 *                                      → sp_wc_page_header()   - 15
 *   - woocommerce_archive_description  → category descriptions, rendered
 *                                        inside sp_wc_page_header()
 *   - woocommerce_before_shop_loop     → notices                - 10
 *                                      → sp_wc_shop_loop_header_open  - 15
 *                                      → woocommerce_result_count     - 20
 *                                      → woocommerce_catalog_ordering - 30
 *                                      → sp_wc_shop_loop_header_close - 35
 *   - woocommerce_after_shop_loop      → pagination (priority 10)
 *   - woocommerce_no_products_found    → empty-state template
 *   - woocommerce_after_main_content   → sp_wc_wrapper_close()
 *
 * The filter sidebar renders only when the `shop-sidebar` widget
 * area has at least one widget -- otherwise the grid expands to fill
 * the container, preserving the pre-Stage-4b layout.
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

$sp_has_sidebar   = sp_wc_has_shop_sidebar();
$sp_layout_class  = $sp_has_sidebar
	? 'sp-shop-layout sp-shop-layout--with-sidebar grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-10'
	: 'sp-shop-layout';
?>
<div class="<?php echo esc_attr( $sp_layout_class ); ?>">

	<?php if ( $sp_has_sidebar ) : ?>
		<aside class="sp-shop-sidebar" aria-label="<?php esc_attr_e( 'Shop filters', 'smart-pharmacy' ); ?>">
			<?php dynamic_sidebar( 'shop-sidebar' ); ?>
		</aside>
	<?php endif; ?>

	<div class="sp-shop-main">
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop.
		 *
		 * @hooked woocommerce_output_all_notices    - 10
		 * @hooked sp_wc_shop_loop_header_open       - 15  (Stage 4b)
		 * @hooked woocommerce_result_count          - 20  (re-added 4b)
		 * @hooked woocommerce_catalog_ordering      - 30  (re-added 4b)
		 * @hooked sp_wc_shop_loop_header_close      - 35  (Stage 4b)
		 */
		do_action( 'woocommerce_before_shop_loop' );

		if ( woocommerce_product_loop() ) :
			?>
			<div class="grid grid-cols-1 md:grid-cols-2 <?php echo $sp_has_sidebar ? 'xl:grid-cols-3' : 'lg:grid-cols-3'; ?> gap-6 mb-12">
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
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		else :
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10  (renders woocommerce/loop/
			 *                                     no-products-found.php)
			 */
			do_action( 'woocommerce_no_products_found' );
		endif;
		?>
	</div>

</div>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked sp_wc_wrapper_close - 10  (inc/woocommerce.php)
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
