<?php
/**
 * Single product — Related Products section (Stage 4a-3 brand override).
 *
 * Replaces WC's default related.php so the "Related Products" block at
 * the bottom of a single-product page gets a brand header (gradient
 * heading) and renders inside our standard 3-col Tailwind grid instead
 * of the default ul.products flex grid.
 *
 * Each related product card is rendered via wc_get_template_part(
 * 'content', 'product' ), which loads our brand override at
 * woocommerce/content-product.php -- so cards already match the shop
 * archive visually, no extra styling needed here.
 *
 * @package SmartPharmacy
 *
 * @var WC_Product[] $related_products  Pulled by woocommerce_output_related_products().
 * @var int          $columns           Default columns (we hardcode 3 for grid consistency).
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $related_products ) ) {
	return;
}
?>

<section class="sp-related-products mt-16 md:mt-24">
	<header class="box-border break-words text-center mb-12">
		<h2 class="text-neutral-900 text-3xl font-black box-border leading-[1.2] break-words mb-3 md:text-4xl">
			<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php esc_html_e( 'You may also like', 'smart-pharmacy' ); ?></span>
		</h2>
		<p class="text-neutral-600 text-base box-border leading-[1.6] break-words md:text-lg">
			<?php esc_html_e( 'Other pharmacy products our customers picked up alongside this one.', 'smart-pharmacy' ); ?>
		</p>
	</header>

	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
		<?php foreach ( $related_products as $related_product ) : ?>
			<?php
			$post_object       = get_post( $related_product->get_id() );
			$GLOBALS['post']   = $post_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			setup_postdata( $GLOBALS['post'] );

			// Loads woocommerce/content-product.php — our shop card.
			wc_get_template_part( 'content', 'product' );
			?>
		<?php endforeach; ?>
	</div>
</section>

<?php wp_reset_postdata(); ?>
