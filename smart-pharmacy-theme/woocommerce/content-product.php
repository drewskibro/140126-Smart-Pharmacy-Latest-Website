<?php
/**
 * Single product card in a shop loop (Stage 4a-2 brand override).
 *
 * Replaces WooCommerce's default content-product.php with a brand
 * card whose visual treatment matches A8 Bestsellers and E1 Related
 * Products: rounded white card, coloured-tint image area on top,
 * category eyebrow + name + short description + (optional) star
 * rating + price/CTA row in the body.
 *
 * Differences vs. A8/E1 (which use static ACF data):
 *   - $product is the live WC_Product, so name/price/image/rating
 *     all read from WC core (stock-aware, sale-aware, srcset-ready).
 *   - The Add-to-Cart button gets WC's data attributes + ajax_add_to_cart
 *     class so AJAX add-to-cart works for simple in-stock products
 *     out of the box. Variable products / out-of-stock fall through
 *     to the product page (WC handles add_to_cart_url() correctly).
 *   - Sale flash auto-derives a "X% Off" badge from regular vs. sale
 *     price; nothing for editors to configure per-product.
 *   - Category background tint maps from category slug to a colour
 *     key, with teal as the fallback. Add new categories to the map
 *     below as the catalogue grows.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

/* ---------- Resolve the card's primary category + colour classes */
// Colour bundle (bg tint, eyebrow text colour) comes from the
// shared sp_wc_category_colour_classes() helper so the same
// slug always matches the colour used on category tiles + filters.
$sp_terms       = get_the_terms( $product->get_id(), 'product_cat' );
$sp_primary_cat = ( is_array( $sp_terms ) && ! empty( $sp_terms ) ) ? $sp_terms[0] : null;
$sp_cat_name    = $sp_primary_cat ? $sp_primary_cat->name : '';
$sp_cat_slug    = $sp_primary_cat ? $sp_primary_cat->slug : '';
$sp_colour      = sp_wc_category_colour_classes( $sp_cat_slug );
$sp_bg          = $sp_colour['bg'];
$sp_cat_text    = $sp_colour['text'];

/* ---------- Sale-flash badge: "X% Off" -------------------------- */
$sp_sale_badge = '';
if ( $product->is_on_sale() && $product->get_regular_price() && $product->get_sale_price() ) {
	$sp_off = (int) round(
		( ( (float) $product->get_regular_price() - (float) $product->get_sale_price() ) / (float) $product->get_regular_price() ) * 100
	);
	if ( $sp_off > 0 ) {
		/* translators: %d is the percentage discount. */
		$sp_sale_badge = sprintf( __( '%d%% Off', 'smart-pharmacy' ), $sp_off );
	}
}

/* ---------- Star rating ----------------------------------------- */
$sp_rating  = (float) $product->get_average_rating();
$sp_reviews = (int) $product->get_review_count();
$sp_show_rating = ( $sp_rating > 0 || $sp_reviews > 0 );

/* ---------- POM gating ----------------------------------------- */
// Products linked (via ACF E1) to a treatment flagged POM / requires-
// consultation in B4 swap their CTA for a "Start Consultation" link
// that deep-links to the treatment landing page. Also drives the
// orange "Prescription only" eyebrow that overrides the category
// eyebrow when set.
$sp_is_pom           = sp_product_is_pom( $product->get_id() );
$sp_consultation_url = $sp_is_pom ? sp_product_consultation_url( $product->get_id() ) : '';

/* ---------- Add-to-Cart button data attrs ----------------------- */
// Match WC's own templates so the AJAX add-to-cart fragment system
// finds the button. Without these, clicks fall back to a full page
// reload with ?add-to-cart=ID -- still works, just less smooth.
// POM products never get AJAX add-to-cart because they aren't
// purchasable (woocommerce_is_purchasable filter returns false).
$sp_can_ajax = ! $sp_is_pom
	&& $product->supports( 'ajax_add_to_cart' )
	&& $product->is_purchasable()
	&& $product->is_in_stock()
	&& $product->is_type( 'simple' );

$sp_cta_classes = 'add_to_cart_button text-white text-sm font-semibold bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] px-6 py-3 rounded-full hover:shadow-lg transition-all inline-block';
if ( $sp_can_ajax ) {
	$sp_cta_classes .= ' ajax_add_to_cart';
}
?>
<div <?php wc_product_class( 'sp-product-card relative bg-white shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.05)_0px_1px_2px_0px] box-border flex flex-col break-words border border-gray-200 overflow-hidden rounded-2xl border-solid hover:shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_20px_25px_-5px,rgba(0,0,0,0.1)_0px_8px_10px_-6px] hover:border-teal-500/30 group transition-all duration-300', $product ); ?>>

	<?php if ( $sp_is_pom ) : ?>
		<div class="absolute box-border break-words z-10 left-4 top-4">
			<span class="sp-pom-flash items-center gap-1.5 text-white text-xs font-bold bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border inline-flex leading-4 break-words px-3 py-1.5 rounded-full uppercase tracking-wider">
				<?php echo sp_icon( 'shield', 'w-3.5 h-3.5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Prescription only', 'smart-pharmacy' ); ?>
			</span>
		</div>
	<?php elseif ( $sp_sale_badge ) : ?>
		<div class="absolute box-border break-words z-10 left-4 top-4">
			<span class="text-white text-xs font-bold bg-orange-500 shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] box-border inline-block leading-4 break-words px-3 py-1.5 rounded-full uppercase tracking-wider"><?php echo esc_html( $sp_sale_badge ); ?></span>
		</div>
	<?php endif; ?>

	<!-- Image: clickable, links to single-product page. -->
	<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="relative <?php echo esc_attr( $sp_bg ); ?> box-border flex items-center justify-center break-words h-64 p-8 overflow-hidden" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
		<?php
		// $product->get_image() emits an <img> with srcset, sizes, alt,
		// and lazy-loading already populated. The wrapper div constrains
		// it; group-hover:scale-110 on the inner img provides the lift.
		echo $product->get_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'woocommerce_thumbnail',
			array(
				'class' => 'w-full h-full object-cover transition-transform duration-500 group-hover:scale-110',
			)
		);
		?>
	</a>

	<div class="box-border break-words p-6">
		<?php if ( $sp_cat_name ) : ?>
			<p class="<?php echo esc_attr( $sp_cat_text ); ?> text-xs font-bold box-border tracking-[0.6px] leading-4 break-words uppercase mb-2"><?php echo esc_html( $sp_cat_name ); ?></p>
		<?php endif; ?>

		<h3 class="text-neutral-900 text-xl font-black box-border leading-[1.3] break-words mb-2">
			<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="hover:text-teal-600 transition-colors"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>

		<?php
		$sp_short = $product->get_short_description();
		if ( '' === $sp_short ) {
			$sp_short = wp_trim_words( wp_strip_all_tags( $product->get_description() ), 18, '…' );
		}
		?>
		<?php if ( $sp_short ) : ?>
			<p class="text-neutral-600 text-sm box-border leading-[1.5] break-words mb-4"><?php echo esc_html( wp_strip_all_tags( $sp_short ) ); ?></p>
		<?php endif; ?>

		<?php if ( $sp_show_rating ) : ?>
			<div class="flex items-center gap-1 mb-4" aria-label="<?php echo esc_attr( sprintf( /* translators: %s is the average rating. */ __( 'Rated %s out of 5', 'smart-pharmacy' ), number_format_i18n( $sp_rating, 1 ) ) ); ?>">
				<?php for ( $sp_i = 0; $sp_i < 5; $sp_i++ ) {
					$sp_star_class = $sp_i < (int) round( $sp_rating ) ? 'text-yellow-400' : 'text-gray-300';
					echo sp_icon( 'star', 'w-4 h-4 ' . $sp_star_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} ?>
				<?php if ( $sp_reviews > 0 ) : ?>
					<span class="text-neutral-600 text-sm ml-2">
						<?php
						/* translators: %s is the number of reviews. */
						printf( esc_html( _n( '(%s review)', '(%s reviews)', $sp_reviews, 'smart-pharmacy' ) ), esc_html( number_format_i18n( $sp_reviews ) ) );
						?>
					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="flex items-center justify-between gap-4">
			<div class="text-neutral-900 text-2xl font-black price">
				<?php
				// get_price_html() already returns formatted HTML
				// (handles sale prices with <del>/<ins>, ranges for
				// variable products, "Free!", etc.). Trusted output.
				echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
			<?php if ( $sp_is_pom ) : ?>
				<a href="<?php echo esc_url( $sp_consultation_url ); ?>"
					class="sp-pom-consult-cta text-white text-sm font-semibold bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0)_0px_0px_0px_0px,rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] px-6 py-3 rounded-full hover:shadow-lg transition-all inline-block"
					aria-label="<?php printf( /* translators: %s is the product name. */ esc_attr__( 'Start consultation for %s', 'smart-pharmacy' ), esc_attr( $product->get_name() ) ); ?>">
					<?php esc_html_e( 'Start Consultation', 'smart-pharmacy' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
					data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
					data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					data-quantity="1"
					data-product_type="<?php echo esc_attr( $product->get_type() ); ?>"
					rel="nofollow"
					class="<?php echo esc_attr( $sp_cta_classes ); ?>"
					aria-label="<?php echo esc_attr( wp_strip_all_tags( $product->add_to_cart_description() ) ); ?>">
					<?php echo esc_html( $product->add_to_cart_text() ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
