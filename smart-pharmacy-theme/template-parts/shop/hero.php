<?php
/**
 * Shop hero (Stage 4d).
 *
 * Wide gradient banner with a trust-badge eyebrow, display heading,
 * supporting copy, a prominent search input, "Popular:" search pills,
 * and an inline trust-badges row.  Replaces the branded page header
 * that sp_wc_page_header() normally emits on /shop/ (it skips when
 * this hero renders).
 *
 * Shown on is_shop() only -- category / tag archives keep the
 * simpler sp_wc_page_header() treatment so the hero doesn't bury
 * the category context above the fold.
 *
 * Strings are wrapped in apply_filters() so client copy tweaks can
 * land in a child theme / mu-plugin without editing this template.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

$sp_hero_eyebrow = (string) apply_filters( 'sp_shop_hero_eyebrow', __( 'Trusted by 50,000+ customers', 'smart-pharmacy' ) );
$sp_hero_title   = (string) apply_filters( 'sp_shop_hero_title', __( 'Your Health, Delivered', 'smart-pharmacy' ) );
$sp_hero_body    = (string) apply_filters( 'sp_shop_hero_body', __( 'Browse our range of prescription treatments, wellness products, and healthcare essentials. Expert care, fast delivery, and unbeatable prices.', 'smart-pharmacy' ) );

// Popular search pills: label => query term. Editor-friendly via
// the filter; sensible defaults drawn from the catalogue.
$sp_hero_pills = (array) apply_filters(
	'sp_shop_hero_popular_pills',
	array(
		__( 'Weight Loss', 'smart-pharmacy' ) => 'weight',
		__( 'Vitamins', 'smart-pharmacy' )    => 'vitamin',
		__( 'Pain Relief', 'smart-pharmacy' ) => 'pain relief',
		__( 'Hair Loss', 'smart-pharmacy' )   => 'hair',
		__( 'Cold & Flu', 'smart-pharmacy' )  => 'cold flu',
		__( 'First Aid', 'smart-pharmacy' )   => 'first aid',
	)
);

// Trust badges: icon key (from sp_icon) => label.
$sp_hero_badges = (array) apply_filters(
	'sp_shop_hero_trust_badges',
	array(
		array( 'icon' => 'truck',        'label' => __( 'Free Delivery', 'smart-pharmacy' ) ),
		array( 'icon' => 'check_circle', 'label' => __( 'Same-Day Dispatch', 'smart-pharmacy' ) ),
		array( 'icon' => 'shield',       'label' => __( 'GPhC Registered', 'smart-pharmacy' ) ),
		array( 'icon' => 'sparkle',      'label' => __( '50,000+ Customers', 'smart-pharmacy' ) ),
	)
);

// Search action: WC's product search sends ?s= + ?post_type=product.
$sp_hero_search_action = home_url( '/' );
$sp_hero_current_s     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<section class="sp-shop-hero">
	<div class="sp-shop-hero__inner">

		<div class="sp-shop-hero__eyebrow">
			<?php echo sp_icon( 'check_circle', 'w-4 h-4 text-teal-500' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php echo esc_html( $sp_hero_eyebrow ); ?></span>
		</div>

		<h1 class="sp-shop-hero__title"><?php echo esc_html( $sp_hero_title ); ?></h1>
		<p class="sp-shop-hero__body"><?php echo esc_html( $sp_hero_body ); ?></p>

		<form role="search" method="get" action="<?php echo esc_url( $sp_hero_search_action ); ?>" class="sp-shop-hero__search">
			<label for="sp-shop-hero-search" class="sr-only"><?php esc_html_e( 'Search products', 'smart-pharmacy' ); ?></label>
			<input
				id="sp-shop-hero-search"
				type="search"
				name="s"
				value="<?php echo esc_attr( $sp_hero_current_s ); ?>"
				placeholder="<?php esc_attr_e( 'Search for products, treatments, conditions...', 'smart-pharmacy' ); ?>"
				autocomplete="off"
			/>
			<input type="hidden" name="post_type" value="product" />
			<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'smart-pharmacy' ); ?>">
				<svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
		</form>

		<?php if ( ! empty( $sp_hero_pills ) ) : ?>
			<div class="sp-shop-hero__pills">
				<span class="sp-shop-hero__pills-label"><?php esc_html_e( 'Popular:', 'smart-pharmacy' ); ?></span>
				<?php foreach ( $sp_hero_pills as $sp_pill_label => $sp_pill_term ) : ?>
					<a class="sp-shop-hero__pill" href="<?php echo esc_url( add_query_arg( array( 's' => rawurlencode( $sp_pill_term ), 'post_type' => 'product' ), home_url( '/' ) ) ); ?>">
						<?php echo esc_html( $sp_pill_label ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $sp_hero_badges ) ) : ?>
			<ul class="sp-shop-hero__badges">
				<?php foreach ( $sp_hero_badges as $sp_badge ) : ?>
					<li>
						<?php echo sp_icon( (string) $sp_badge['icon'], 'w-5 h-5 text-teal-600' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( $sp_badge['label'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
