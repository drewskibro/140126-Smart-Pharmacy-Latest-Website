<?php
/**
 * Single product layout (Stage 4a-3 brand override).
 *
 * Replaces WC's default content-single-product.php with a 2-col Tailwind
 * grid: image gallery on the left, product summary (title / price /
 * excerpt / add-to-cart / meta) on the right.  Tabs / upsells / related
 * products sit below as full-width sections.
 *
 * Every WC hook is preserved so plugins (e.g. wishlist, share buttons,
 * variation swatches) continue to fire in the right place. The only
 * intentional structural change vs. WC default is the two outer wrapper
 * divs that establish the lg:grid-cols-2 layout.
 *
 * Sub-templates overridden alongside this one:
 *   - single-product/add-to-cart/simple.php  (branded button + qty)
 *   - single-product/related.php             (branded "Related" header
 *                                              + Tailwind grid)
 *
 * Title / price / excerpt / meta / tabs / sale-flash positioning are
 * styled via raw CSS in assets/css/styles.css (loaded after Tailwind
 * for cascade priority).  Avoids the maintenance burden of overriding
 * every sub-template just to swap class names.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>

<?php
/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'sp-single-product', $product ); ?>>

	<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">

		<!-- LEFT column: gallery + sale flash -->
		<div class="sp-product-gallery relative">
			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary.
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images     - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>

		<!-- RIGHT column: title, rating, price, excerpt, add-to-cart, meta -->
		<div class="sp-product-summary summary entry-summary">
			<?php
			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked woocommerce_template_single_title       - 5
			 * @hooked woocommerce_template_single_rating      - 10
			 * @hooked woocommerce_template_single_price       - 10
			 * @hooked woocommerce_template_single_excerpt     - 20
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked woocommerce_template_single_meta        - 40
			 * @hooked woocommerce_template_single_sharing     - 50
			 */
			do_action( 'woocommerce_single_product_summary' );
			?>
		</div>

	</div>

	<!-- BELOW: product data tabs, upsells, related products -->
	<div class="sp-product-after">
		<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display           - 15
		 * @hooked woocommerce_output_related_products  - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
