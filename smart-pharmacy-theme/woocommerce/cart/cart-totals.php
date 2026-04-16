<?php
/**
 * Cart totals (Stage 4a-4 brand override).
 *
 * Right-hand summary card rendered by cart.php via the
 * woocommerce_cart_collaterals action.  Shows subtotal, shipping
 * options (if shipping calc is enabled), fees, tax, total, and the
 * "Proceed to checkout" CTA.
 *
 * Uses a <table> structure rather than <dl> because WC's default
 * cart-shipping.php (which we use unmodified) outputs <tr> rows --
 * putting those inside a <dl> would be invalid HTML.  The .sp-cart-
 * totals CSS styles the table to look like a stacked summary card
 * with flex-style rows; the table semantic is invisible to the eye.
 *
 * WC hooks preserved:
 *   - woocommerce_before_cart_totals
 *   - woocommerce_cart_totals_before_shipping
 *   - woocommerce_cart_totals_after_shipping
 *   - woocommerce_cart_totals_before_order_total
 *   - woocommerce_cart_totals_after_order_total
 *   - woocommerce_proceed_to_checkout
 *   - woocommerce_after_cart_totals
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="cart_totals sp-cart-totals bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 shadow-sm <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 class="text-neutral-900 text-xl font-bold mb-5"><?php esc_html_e( 'Order summary', 'smart-pharmacy' ); ?></h2>

	<table class="shop_table shop_table_responsive sp-cart-totals__table w-full text-sm">
		<tbody>

			<tr class="cart-subtotal">
				<th class="py-2 pr-3 text-left text-neutral-600 font-normal"><?php esc_html_e( 'Subtotal', 'smart-pharmacy' ); ?></th>
				<td class="py-2 text-right text-neutral-900 font-semibold"><?php wc_cart_totals_subtotal_html(); ?></td>
			</tr>

			<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
				<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<th class="py-2 pr-3 text-left text-neutral-600 font-normal"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
					<td class="py-2 text-right text-teal-600 font-semibold"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

				<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

				<?php wc_cart_totals_shipping_html(); ?>

				<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

			<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>

				<tr class="shipping">
					<th class="py-2 pr-3 text-left text-neutral-600 font-normal"><?php esc_html_e( 'Shipping', 'smart-pharmacy' ); ?></th>
					<td class="py-2 text-right text-neutral-500 text-xs"><?php woocommerce_shipping_calculator(); ?></td>
				</tr>

			<?php endif; ?>

			<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
				<tr class="fee">
					<th class="py-2 pr-3 text-left text-neutral-600 font-normal"><?php echo esc_html( $fee->name ); ?></th>
					<td class="py-2 text-right text-neutral-900 font-semibold"><?php wc_cart_totals_fee_html( $fee ); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php
			if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
				$taxable_address = WC()->customer->get_taxable_address();
				$estimated_text  = '';

				if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
					/* translators: %s location. */
					$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
				}

				if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
					foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore
						?>
						<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
							<th class="py-2 pr-3 text-left text-neutral-600 font-normal"><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
							<td class="py-2 text-right text-neutral-900 font-semibold"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
						</tr>
						<?php
					endforeach;
				} else {
					?>
					<tr class="tax-total">
						<th class="py-2 pr-3 text-left text-neutral-600 font-normal"><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
						<td class="py-2 text-right text-neutral-900 font-semibold"><?php wc_cart_totals_taxes_total_html(); ?></td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

			<tr class="order-total">
				<th class="pt-4 pr-3 text-left text-neutral-900 font-bold text-lg border-t-2 border-neutral-200"><?php esc_html_e( 'Total', 'smart-pharmacy' ); ?></th>
				<td class="pt-4 text-right text-neutral-900 font-black text-2xl border-t-2 border-neutral-200"><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>

			<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

		</tbody>
	</table>

	<div class="wc-proceed-to-checkout mt-6">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<p class="text-neutral-500 text-xs text-center mt-4 leading-relaxed">
		<svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<?php esc_html_e( 'Secure checkout. GPhC-registered pharmacy.', 'smart-pharmacy' ); ?>
	</p>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
