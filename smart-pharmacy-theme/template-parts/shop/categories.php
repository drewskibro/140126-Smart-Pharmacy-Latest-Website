<?php
/**
 * Shop by Category tiles (Stage 4d).
 *
 * Four- or eight-up responsive grid of colour-tinted category
 * tiles, each a link to /product-category/{slug}/. Terms come
 * from product_cat (top-level only, non-empty). Colour classes
 * come from sp_wc_category_colour_classes() so each tile matches
 * the eyebrow colour used on product cards + filter chips.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

$sp_cat_terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'number'     => 12,
	)
);

if ( is_wp_error( $sp_cat_terms ) || empty( $sp_cat_terms ) ) {
	return;
}
?>
<section class="sp-shop-cats">
	<header class="sp-shop-cats__header">
		<h2 class="sp-shop-cats__title"><?php esc_html_e( 'Shop by Category', 'smart-pharmacy' ); ?></h2>
		<p class="sp-shop-cats__body"><?php esc_html_e( 'Find exactly what you need', 'smart-pharmacy' ); ?></p>
	</header>

	<div class="sp-shop-cats__grid">
		<?php foreach ( $sp_cat_terms as $sp_cat_term ) : ?>
			<?php
			$sp_cat_colour = sp_wc_category_colour_classes( $sp_cat_term->slug );
			$sp_cat_desc   = ! empty( $sp_cat_term->description )
				? wp_trim_words( wp_strip_all_tags( $sp_cat_term->description ), 10, '…' )
				: '';

			// Thumbnail with auto-fallback: explicit category image, then
			// the most-recent product's featured image, then placeholder.
			$sp_cat_thumb = sp_wc_category_thumb_url( $sp_cat_term->term_id, 'medium' );
			?>
			<a class="sp-shop-cats__tile <?php echo esc_attr( $sp_cat_colour['bg'] . ' ' . $sp_cat_colour['border'] ); ?>" href="<?php echo esc_url( get_term_link( $sp_cat_term ) ); ?>">
				<div class="sp-shop-cats__thumb">
					<?php if ( $sp_cat_thumb ) : ?>
						<img src="<?php echo esc_url( $sp_cat_thumb ); ?>" alt="" loading="lazy" />
					<?php else : ?>
						<span class="sp-shop-cats__thumb-placeholder" aria-hidden="true"></span>
					<?php endif; ?>
				</div>
				<h3 class="sp-shop-cats__name"><?php echo esc_html( $sp_cat_term->name ); ?></h3>
				<?php if ( $sp_cat_desc ) : ?>
					<p class="sp-shop-cats__desc"><?php echo esc_html( $sp_cat_desc ); ?></p>
				<?php endif; ?>
				<span class="sp-shop-cats__cta <?php echo esc_attr( $sp_cat_colour['cta'] ); ?>">
					<?php esc_html_e( 'Shop Now', 'smart-pharmacy' ); ?>
					<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
