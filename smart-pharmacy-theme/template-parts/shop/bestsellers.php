<?php
/**
 * Best Sellers carousel (Stage 4d).
 *
 * Horizontal-scrolling row of the 8 top-selling products on the
 * shop landing page.  Uses the existing content-product.php card
 * so styling, AJAX add-to-cart, POM gating, and sale badges all
 * match the main grid below.
 *
 * Ordering: total_sales DESC, falling back to ID DESC for
 * fresh-install demos where nothing has sold yet (otherwise the
 * section would empty on day one).
 *
 * Navigation arrows are hidden on touch devices; the container
 * scrolls with native inertia (scroll-snap + snap-mandatory so
 * cards land squarely at the start/end of the viewport).
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

// Query the 8 top-selling published products.
$sp_bs_args = array(
	'status'   => 'publish',
	'limit'    => 8,
	'orderby'  => array(
		'meta_value_num' => 'DESC',
		'date'           => 'DESC',
	),
	'meta_key' => 'total_sales', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	'return'   => 'ids',
);

$sp_bs_ids = wc_get_products( $sp_bs_args );

if ( empty( $sp_bs_ids ) ) {
	return;
}
?>
<section class="sp-shop-bestsellers" id="best-sellers">
	<header class="sp-shop-bestsellers__header">
		<h2 class="sp-shop-bestsellers__title"><?php esc_html_e( 'Best Sellers', 'smart-pharmacy' ); ?></h2>
		<p class="sp-shop-bestsellers__body"><?php esc_html_e( 'Our most popular products, trusted by thousands', 'smart-pharmacy' ); ?></p>
	</header>

	<div class="sp-shop-bestsellers__carousel" data-sp-carousel>
		<button type="button" class="sp-shop-bestsellers__nav sp-shop-bestsellers__nav--prev" aria-label="<?php esc_attr_e( 'Scroll best sellers backwards', 'smart-pharmacy' ); ?>" data-sp-carousel-prev>
			<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>

		<div class="sp-shop-bestsellers__track" data-sp-carousel-track>
			<?php foreach ( $sp_bs_ids as $sp_bs_id ) : ?>
				<?php
				// wc_get_template_part expects the globals $product + $post to
				// match the current iteration so content-product.php works
				// unmodified.
				$sp_bs_post    = get_post( $sp_bs_id );
				$sp_bs_product = wc_get_product( $sp_bs_id );
				if ( ! $sp_bs_post || ! $sp_bs_product ) {
					continue;
				}
				$GLOBALS['post']    = $sp_bs_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['product'] = $sp_bs_product;
				setup_postdata( $sp_bs_post );
				?>
				<div class="sp-shop-bestsellers__slide">
					<?php wc_get_template_part( 'content', 'product' ); ?>
				</div>
			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		</div>

		<button type="button" class="sp-shop-bestsellers__nav sp-shop-bestsellers__nav--next" aria-label="<?php esc_attr_e( 'Scroll best sellers forwards', 'smart-pharmacy' ); ?>" data-sp-carousel-next>
			<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</button>
	</div>
</section>
