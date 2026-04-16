<?php
/**
 * Proceed to Checkout button (Stage 4a-4 brand override).
 *
 * Hooked onto woocommerce_proceed_to_checkout @10 by WC -- we can't
 * dequeue the hook without losing the default button, so overriding
 * the template is the clean path.  Replaces WC's default grey button
 * with the brand gradient teal CTA.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;
?>

<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward sp-proceed-btn text-white text-base font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] gap-x-2 inline-flex justify-center leading-6 w-full px-8 py-4 rounded-full hover:shadow-[rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300">
	<?php esc_html_e( 'Proceed to checkout', 'smart-pharmacy' ); ?>
	<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
		<path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
	</svg>
</a>
