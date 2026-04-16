<?php
/**
 * Order received / thank-you page (Stage 4a-5 brand override).
 *
 * Shown immediately after a customer completes checkout (and also via
 * the "View order" link from My Account). Replaces WC's default with
 * a celebratory branded layout:
 *
 *   1. Success hero  : circular check icon, "Thank you" heading,
 *                      order-number sub-line.
 *   2. Order overview: 5-stat row -- number, date, email, total,
 *                      payment method.
 *   3. Order details : line items + totals (delegated to overridden
 *                      order/order-details.php).
 *   4. Customer info : billing + shipping addresses (delegated to
 *                      overridden order/order-details-customer.php).
 *
 * WC hooks preserved:
 *   - woocommerce_thankyou
 *   - woocommerce_thankyou_{$payment_method}
 *   - woocommerce_order_details_after_order_table
 *   - woocommerce_order_details_after_customer_details
 *
 * Edge cases handled:
 *   - $order is false (URL hit with no valid order) -> short message.
 *   - User no longer logged in / order belongs to someone else ->
 *     WC's permission check above this template handles it; we just
 *     render whatever is passed in.
 *
 * @package SmartPharmacy
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order sp-thankyou">

	<?php
	if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<div class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed bg-red-50 border border-red-200 rounded-2xl p-6 mb-6">
				<h2 class="text-red-900 text-xl font-bold mb-2"><?php esc_html_e( 'Payment failed', 'smart-pharmacy' ); ?></h2>
				<p class="text-red-800 text-sm leading-relaxed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'smart-pharmacy' ); ?></p>

				<p class="woocommerce-thankyou-order-failed-actions mt-4 flex gap-3">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button sp-btn-secondary"><?php esc_html_e( 'Pay', 'smart-pharmacy' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button sp-btn-ghost"><?php esc_html_e( 'My account', 'smart-pharmacy' ); ?></a>
					<?php endif; ?>
				</p>
			</div>

		<?php else : ?>

			<?php /* ---------- 1. Success hero ---------- */ ?>
			<header class="sp-thankyou__hero text-center mb-10 md:mb-14">
				<div class="w-20 h-20 bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] rounded-full mx-auto mb-6 flex items-center justify-center shadow-[rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px]">
					<svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
						<path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>

				<h1 class="text-neutral-900 text-3xl font-black leading-[1.2] mb-3 md:text-5xl">
					<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php echo esc_html( apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'smart-pharmacy' ), $order ) ); ?></span>
				</h1>

				<p class="text-neutral-600 text-base leading-[1.6] max-w-2xl mx-auto md:text-lg">
					<?php esc_html_e( 'A confirmation email is on its way. Our pharmacy team will dispatch your order from our GPhC-registered facility.', 'smart-pharmacy' ); ?>
				</p>
			</header>

			<?php /* ---------- 2. Order overview (5-stat strip) ---------- */ ?>
			<section class="sp-thankyou__overview bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 shadow-sm mb-10">
				<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details grid grid-cols-2 md:grid-cols-5 gap-6 list-none p-0 m-0">

					<li class="woocommerce-order-overview__order order">
						<span class="block text-neutral-500 text-xs font-semibold uppercase tracking-wide mb-1"><?php esc_html_e( 'Order number', 'smart-pharmacy' ); ?></span>
						<strong class="block text-neutral-900 text-base font-bold break-all"><?php echo wp_kses_post( $order->get_order_number() ); ?></strong>
					</li>

					<li class="woocommerce-order-overview__date date">
						<span class="block text-neutral-500 text-xs font-semibold uppercase tracking-wide mb-1"><?php esc_html_e( 'Date', 'smart-pharmacy' ); ?></span>
						<strong class="block text-neutral-900 text-base font-bold"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
					</li>

					<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
						<li class="woocommerce-order-overview__email email">
							<span class="block text-neutral-500 text-xs font-semibold uppercase tracking-wide mb-1"><?php esc_html_e( 'Email', 'smart-pharmacy' ); ?></span>
							<strong class="block text-neutral-900 text-base font-bold break-all"><?php echo esc_html( $order->get_billing_email() ); ?></strong>
						</li>
					<?php endif; ?>

					<li class="woocommerce-order-overview__total total">
						<span class="block text-neutral-500 text-xs font-semibold uppercase tracking-wide mb-1"><?php esc_html_e( 'Total', 'smart-pharmacy' ); ?></span>
						<strong class="block text-teal-600 text-base font-black"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
					</li>

					<?php if ( $order->get_payment_method_title() ) : ?>
						<li class="woocommerce-order-overview__payment-method method">
							<span class="block text-neutral-500 text-xs font-semibold uppercase tracking-wide mb-1"><?php esc_html_e( 'Payment', 'smart-pharmacy' ); ?></span>
							<strong class="block text-neutral-900 text-base font-bold"><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
						</li>
					<?php endif; ?>

				</ul>
			</section>

		<?php endif; ?>

		<?php /* Gateway-specific extra HTML (e.g. BACS bank details). */ ?>
		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<header class="sp-thankyou__hero text-center mb-10">
			<h1 class="text-neutral-900 text-3xl font-black leading-[1.2] mb-3 md:text-4xl">
				<span class="text-transparent bg-clip-text bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))]"><?php esc_html_e( 'Order received', 'smart-pharmacy' ); ?></span>
			</h1>
			<p class="text-neutral-600 text-base leading-[1.6] max-w-2xl mx-auto">
				<?php echo esc_html( apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'smart-pharmacy' ), null ) ); ?>
			</p>
		</header>

	<?php endif; ?>

</div>
