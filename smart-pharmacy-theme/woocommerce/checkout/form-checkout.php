<?php
/**
 * Checkout form (Stage 4a-4 brand override).
 *
 * Two-column brand layout:
 *
 *   - Left (lg:col-span-7)  : customer details -- billing form, optional
 *                             ship-to-different-address, order notes.
 *   - Right (lg:col-span-5) : sticky order review -- cart items, totals,
 *                             payment method, place-order button.
 *
 * On mobile everything stacks; order review sits underneath billing.
 *
 * All WC hooks preserved for gateway + plugin compatibility:
 *   - woocommerce_before_checkout_form
 *   - woocommerce_checkout_before_customer_details
 *   - woocommerce_checkout_billing
 *   - woocommerce_checkout_shipping
 *   - woocommerce_checkout_after_customer_details
 *   - woocommerce_checkout_before_order_review_heading
 *   - woocommerce_checkout_before_order_review
 *   - woocommerce_checkout_order_review   (payment methods + place order)
 *   - woocommerce_checkout_after_order_review
 *   - woocommerce_after_checkout_form
 *
 * @package SmartPharmacy
 *
 * @var WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_ajax() ) {
	do_action( 'woocommerce_before_checkout_form', $checkout );
}

// Guard: if the user isn't logged in and checkout requires login, WC
// prints its own login message; we leave that path alone.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout sp-checkout-page" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr( 'Checkout' ); ?>">

	<header class="mb-8">
		<h1 class="text-neutral-900 text-3xl font-black leading-[1.2] md:text-4xl">
			<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php esc_html_e( 'Checkout', 'smart-pharmacy' ); ?></span>
		</h1>
		<p class="text-neutral-600 text-base leading-[1.6] mt-2">
			<?php esc_html_e( 'Complete your order below. All orders are dispatched from our GPhC-registered pharmacy.', 'smart-pharmacy' ); ?>
		</p>
	</header>

	<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

		<?php /* ---------- Customer details (left, 7 of 12) ---------- */ ?>
		<div class="lg:col-span-7">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div id="customer_details" class="space-y-6">

					<div class="col-1 sp-checkout-card bg-white border border-neutral-200 rounded-2xl p-6 md:p-8">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>

					<div class="col-2 sp-checkout-card bg-white border border-neutral-200 rounded-2xl p-6 md:p-8">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>

				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

		</div>

		<?php /* ---------- Order review (right, 5 of 12) ---------- */ ?>
		<aside class="lg:col-span-5">
			<div class="sp-checkout-review lg:sticky lg:top-8">

				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

				<h3 id="order_review_heading" class="text-neutral-900 text-xl font-bold mb-4"><?php esc_html_e( 'Your order', 'smart-pharmacy' ); ?></h3>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 shadow-sm">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

				<p class="text-neutral-500 text-xs text-center mt-4 leading-relaxed">
					<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
						<path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<?php esc_html_e( 'Secure checkout. Data encrypted in transit.', 'smart-pharmacy' ); ?>
				</p>

			</div>
		</aside>

	</div>

</form>

<?php
if ( ! is_ajax() ) {
	do_action( 'woocommerce_after_checkout_form', $checkout );
}
?>
