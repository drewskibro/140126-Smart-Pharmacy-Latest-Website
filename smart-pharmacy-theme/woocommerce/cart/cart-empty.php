<?php
/**
 * Empty cart page (Stage 4a-4 brand override).
 *
 * Shown when a user visits /cart/ with nothing in their basket.  Mirrors
 * the no-products-found.php empty state from the shop archive -- same
 * gradient icon tile, same "Browse all products" CTA -- so the empty
 * experience is visually consistent whether you're on /shop/ or /cart/.
 *
 * WC hooks preserved:
 *   - woocommerce_cart_is_empty
 *   - woocommerce_cart_has_errors   (already output by cart.php wrapper)
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="sp-cart-empty bg-white border border-neutral-200 rounded-2xl p-10 md:p-16 text-center max-w-2xl mx-auto">

	<div class="w-20 h-20 bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-[rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px]">
		<svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
	</div>

	<h1 class="text-neutral-900 text-3xl font-black leading-[1.2] mb-3 md:text-4xl">
		<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php esc_html_e( 'Your basket is empty', 'smart-pharmacy' ); ?></span>
	</h1>

	<?php
	/**
	 * Shows any cart-is-empty notices registered by plugins.
	 * Preserve the hook for compatibility.
	 */
	do_action( 'woocommerce_cart_is_empty' );
	?>

	<p class="text-neutral-600 text-base leading-[1.6] max-w-md mx-auto mb-8 md:text-lg">
		<?php esc_html_e( 'Looks like you haven\'t added anything yet. Browse our range of pharmacy essentials, vitamins, and treatments.', 'smart-pharmacy' ); ?>
	</p>

	<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="wc-backward text-white text-base font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] gap-x-2 inline-flex justify-center leading-6 px-8 py-4 rounded-full hover:shadow-[rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300">
			<?php esc_html_e( 'Browse the shop', 'smart-pharmacy' ); ?>
			<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
				<path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</a>
	<?php endif; ?>

</div>
