<?php
/**
 * Order details - customer info (Stage 4a-5 brand override).
 *
 * Shown beneath the order-details table on the thank-you page and in
 * My Account → View order.  Renders billing + shipping addresses side-
 * by-side on desktop, stacked on mobile.
 *
 * WC hooks preserved:
 *   - woocommerce_order_details_after_customer_details
 *   - woocommerce_order_details_after_customer_address
 *
 * @package SmartPharmacy
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>

<section class="woocommerce-customer-details sp-customer-details">

	<h2 class="woocommerce-column__title text-neutral-900 text-xl font-bold mb-5"><?php esc_html_e( 'Your details', 'smart-pharmacy' ); ?></h2>

	<?php if ( $show_shipping ) : ?>

		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

			<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1 bg-white border border-neutral-200 rounded-2xl p-6">
				<h3 class="woocommerce-column__title text-neutral-900 text-base font-bold mb-3 flex items-center gap-2">
					<svg class="w-4 h-4 text-teal-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
						<path d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<?php esc_html_e( 'Billing address', 'smart-pharmacy' ); ?>
				</h3>
				<address class="text-neutral-700 text-sm leading-relaxed not-italic">
					<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'smart-pharmacy' ) ) ); ?>
					<?php if ( $order->get_billing_phone() ) : ?>
						<p class="woocommerce-customer-details--phone text-neutral-600 text-sm mt-2 flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php echo esc_html( $order->get_billing_phone() ); ?>
						</p>
					<?php endif; ?>
					<?php if ( $order->get_billing_email() ) : ?>
						<p class="woocommerce-customer-details--email text-neutral-600 text-sm mt-1 flex items-center gap-1.5 break-all">
							<svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php echo esc_html( $order->get_billing_email() ); ?>
						</p>
					<?php endif; ?>
				</address>
			</div>

			<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2 bg-white border border-neutral-200 rounded-2xl p-6">
				<h3 class="woocommerce-column__title text-neutral-900 text-base font-bold mb-3 flex items-center gap-2">
					<svg class="w-4 h-4 text-teal-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
						<path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					<?php esc_html_e( 'Shipping address', 'smart-pharmacy' ); ?>
				</h3>
				<address class="text-neutral-700 text-sm leading-relaxed not-italic">
					<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'smart-pharmacy' ) ) ); ?>
					<?php if ( $order->get_shipping_phone() ) : ?>
						<p class="woocommerce-customer-details--phone text-neutral-600 text-sm mt-2">
							<?php echo esc_html( $order->get_shipping_phone() ); ?>
						</p>
					<?php endif; ?>
				</address>
			</div>

			<?php do_action( 'woocommerce_order_details_after_customer_address', $order ); ?>

		</div>

	<?php else : ?>

		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1 bg-white border border-neutral-200 rounded-2xl p-6 max-w-lg">
			<h3 class="woocommerce-column__title text-neutral-900 text-base font-bold mb-3 flex items-center gap-2">
				<svg class="w-4 h-4 text-teal-600" viewBox="0 0 24 24" fill="none" aria-hidden="true">
					<path d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<?php esc_html_e( 'Billing address', 'smart-pharmacy' ); ?>
			</h3>
			<address class="text-neutral-700 text-sm leading-relaxed not-italic">
				<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'smart-pharmacy' ) ) ); ?>
				<?php if ( $order->get_billing_phone() ) : ?>
					<p class="woocommerce-customer-details--phone text-neutral-600 text-sm mt-2"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
				<?php endif; ?>
				<?php if ( $order->get_billing_email() ) : ?>
					<p class="woocommerce-customer-details--email text-neutral-600 text-sm mt-1 break-all"><?php echo esc_html( $order->get_billing_email() ); ?></p>
				<?php endif; ?>
			</address>
			<?php do_action( 'woocommerce_order_details_after_customer_address', $order ); ?>
		</div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
