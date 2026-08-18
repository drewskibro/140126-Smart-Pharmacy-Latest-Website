<?php
/**
 * Shop filter panel (Stage 4b-revisit).
 *
 * Hardcoded branded filter sidebar rendered on every /shop/ and
 * /product-category/{slug}/ page via archive-product.php. Replaces
 * the widget-driven sidebar from Stage 4b so filtering is a
 * zero-setup experience for the client.
 *
 * Form submits GET to the current shop URL. The query handler in
 * inc/woocommerce.php (sp_wc_apply_shop_filters) reads the sp_*
 * params and adjusts the main query. Sort + pagination links
 * inherit these params because they're on the query string.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

// Current state from GET — preserves selected filters across re-renders.
$sp_f_cats    = isset( $_GET['sp_cat'] ) ? array_map( 'sanitize_title', (array) wp_unslash( $_GET['sp_cat'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$sp_f_min     = isset( $_GET['sp_min_price'] ) ? max( 0, (int) $_GET['sp_min_price'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$sp_f_max     = isset( $_GET['sp_max_price'] ) ? max( 0, (int) $_GET['sp_max_price'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$sp_f_ratings = isset( $_GET['sp_rating'] ) ? array_map( 'intval', (array) wp_unslash( $_GET['sp_rating'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$sp_f_stock   = isset( $_GET['sp_stock'] ) ? sanitize_key( wp_unslash( $_GET['sp_stock'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$sp_f_max_shop = sp_wc_shop_max_price();
$sp_f_has_any  = ! empty( $sp_f_cats ) || $sp_f_min > 0 || $sp_f_max > 0 || ! empty( $sp_f_ratings ) || $sp_f_stock;

// Context-aware category filter:
//   - On a category archive, show THAT category's subsections (children)
//     and submit back to the category, so filtering refines within it
//     instead of widening to the whole shop.
//   - On the shop root, show top-level categories, ordered by size and
//     capped so the panel never becomes a 24-row wall.
$sp_f_queried = ( function_exists( 'is_product_category' ) && is_product_category() ) ? get_queried_object() : null;
$sp_f_in_cat  = ( $sp_f_queried && ! is_wp_error( $sp_f_queried ) && isset( $sp_f_queried->term_id ) );

if ( $sp_f_in_cat ) {
	$sp_f_cat_legend = sprintf( /* translators: %s: category name. */ __( 'Refine in %s', 'smart-pharmacy' ), $sp_f_queried->name );
	$sp_f_cat_terms  = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => (int) $sp_f_queried->term_id,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	$sp_f_action = get_term_link( $sp_f_queried );
} else {
	$sp_f_cat_legend = __( 'Category', 'smart-pharmacy' );
	$sp_f_cat_terms  = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => 0,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 15, // keep the panel short; the big categories surface first.
		)
	);
	$sp_f_action = get_permalink( wc_get_page_id( 'shop' ) );
}

if ( is_wp_error( $sp_f_cat_terms ) ) {
	$sp_f_cat_terms = array();
}
if ( ! $sp_f_action || is_wp_error( $sp_f_action ) ) {
	$sp_f_action = home_url( '/shop/' );
}
?>
<form method="get" action="<?php echo esc_url( $sp_f_action ); ?>" class="sp-filters" role="search" aria-label="<?php esc_attr_e( 'Filter products', 'smart-pharmacy' ); ?>">

	<header class="sp-filters__header">
		<h2 class="sp-filters__title"><?php esc_html_e( 'Filters', 'smart-pharmacy' ); ?></h2>
		<?php if ( $sp_f_has_any ) : ?>
			<a class="sp-filters__clear" href="<?php echo esc_url( $sp_f_action ); ?>">
				<?php esc_html_e( 'Clear All', 'smart-pharmacy' ); ?>
			</a>
		<?php endif; ?>
	</header>

	<?php if ( ! empty( $sp_f_cat_terms ) ) : ?>
		<fieldset class="sp-filters__block">
			<legend class="sp-filters__legend"><?php echo esc_html( $sp_f_cat_legend ); ?></legend>
			<ul class="sp-filters__list">
				<?php foreach ( $sp_f_cat_terms as $sp_f_term ) : ?>
					<li>
						<label class="sp-filters__option">
							<input type="checkbox" name="sp_cat[]" value="<?php echo esc_attr( $sp_f_term->slug ); ?>" <?php checked( in_array( $sp_f_term->slug, $sp_f_cats, true ) ); ?> />
							<span class="sp-filters__option-label"><?php echo esc_html( $sp_f_term->name ); ?></span>
							<span class="sp-filters__option-count"><?php echo esc_html( number_format_i18n( $sp_f_term->count ) ); ?></span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</fieldset>
	<?php endif; ?>

	<fieldset class="sp-filters__block">
		<legend class="sp-filters__legend"><?php esc_html_e( 'Price Range', 'smart-pharmacy' ); ?></legend>
		<div class="sp-filters__price">
			<label class="sp-filters__price-field">
				<span class="sp-filters__price-label"><?php esc_html_e( 'Min', 'smart-pharmacy' ); ?></span>
				<span class="sp-filters__price-prefix">£</span>
				<input type="number" min="0" max="<?php echo esc_attr( $sp_f_max_shop ); ?>" step="1" name="sp_min_price" inputmode="numeric" value="<?php echo $sp_f_min > 0 ? esc_attr( $sp_f_min ) : ''; ?>" placeholder="0" />
			</label>
			<span class="sp-filters__price-dash" aria-hidden="true">–</span>
			<label class="sp-filters__price-field">
				<span class="sp-filters__price-label"><?php esc_html_e( 'Max', 'smart-pharmacy' ); ?></span>
				<span class="sp-filters__price-prefix">£</span>
				<input type="number" min="0" max="<?php echo esc_attr( $sp_f_max_shop ); ?>" step="1" name="sp_max_price" inputmode="numeric" value="<?php echo $sp_f_max > 0 ? esc_attr( $sp_f_max ) : ''; ?>" placeholder="<?php echo esc_attr( $sp_f_max_shop ); ?>" />
			</label>
		</div>
	</fieldset>

	<?php if ( function_exists( 'sp_wc_shop_has_reviews' ) && sp_wc_shop_has_reviews() ) : ?>
	<fieldset class="sp-filters__block">
		<legend class="sp-filters__legend"><?php esc_html_e( 'Rating', 'smart-pharmacy' ); ?></legend>
		<ul class="sp-filters__list">
			<?php foreach ( array( 5, 4, 3 ) as $sp_f_rating ) : ?>
				<li>
					<label class="sp-filters__option">
						<input type="checkbox" name="sp_rating[]" value="<?php echo esc_attr( $sp_f_rating ); ?>" <?php checked( in_array( $sp_f_rating, $sp_f_ratings, true ) ); ?> />
						<span class="sp-filters__stars" aria-label="<?php printf( /* translators: %d is a number of stars. */ esc_attr( _n( '%d star and up', '%d stars and up', $sp_f_rating, 'smart-pharmacy' ) ), (int) $sp_f_rating ); ?>">
							<?php for ( $sp_f_i = 1; $sp_f_i <= 5; $sp_f_i++ ) : ?>
								<?php echo sp_icon( 'star', 'w-4 h-4 ' . ( $sp_f_i <= $sp_f_rating ? 'text-yellow-400' : 'text-gray-200' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endfor; ?>
						</span>
						<span class="sp-filters__option-label"><?php echo 5 === $sp_f_rating ? esc_html__( '5 stars', 'smart-pharmacy' ) : esc_html( sprintf( /* translators: %d is a number of stars. */ __( '%d+ stars', 'smart-pharmacy' ), $sp_f_rating ) ); ?></span>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	<?php endif; // sp_wc_shop_has_reviews ?>


	<fieldset class="sp-filters__block sp-filters__block--last">
		<legend class="sp-filters__legend"><?php esc_html_e( 'Availability', 'smart-pharmacy' ); ?></legend>
		<ul class="sp-filters__list">
			<li>
				<label class="sp-filters__option">
					<input type="radio" name="sp_stock" value="" <?php checked( '' === $sp_f_stock ); ?> />
					<span class="sp-filters__option-label"><?php esc_html_e( 'Any', 'smart-pharmacy' ); ?></span>
				</label>
			</li>
			<li>
				<label class="sp-filters__option">
					<input type="radio" name="sp_stock" value="instock" <?php checked( 'instock' === $sp_f_stock ); ?> />
					<span class="sp-filters__option-label"><?php esc_html_e( 'In stock', 'smart-pharmacy' ); ?></span>
				</label>
			</li>
			<li>
				<label class="sp-filters__option">
					<input type="radio" name="sp_stock" value="onbackorder" <?php checked( 'onbackorder' === $sp_f_stock ); ?> />
					<span class="sp-filters__option-label"><?php esc_html_e( 'On back-order', 'smart-pharmacy' ); ?></span>
				</label>
			</li>
		</ul>
	</fieldset>

	<?php
	// Preserve non-filter query args (orderby, search term) on submit
	// so applying a filter doesn't wipe the user's sort choice.
	foreach ( array( 'orderby', 's' ) as $sp_f_keep ) {
		if ( isset( $_GET[ $sp_f_keep ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			printf(
				'<input type="hidden" name="%s" value="%s" />',
				esc_attr( $sp_f_keep ),
				esc_attr( sanitize_text_field( wp_unslash( $_GET[ $sp_f_keep ] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			);
		}
	}
	?>

	<button type="submit" class="sp-filters__submit"><?php esc_html_e( 'Apply Filters', 'smart-pharmacy' ); ?></button>

</form>

<?php
// Optional extras from the Shop Sidebar widget area below the
// hardcoded filters -- lets editors drop promo banners etc.
if ( is_active_sidebar( 'shop-sidebar' ) ) :
	?>
	<div class="sp-shop-sidebar-extras">
		<?php dynamic_sidebar( 'shop-sidebar' ); ?>
	</div>
	<?php
endif;
