<?php
/**
 * Checkout review order (Stage 4a-4 brand override).
 *
 * Renders the order line items + totals + payment methods inside the
 * #order_review container built by form-checkout.php.  Keeps the table
 * structure WC's JS expects (tr.cart_item, tr.cart-subtotal, tr.order-
 * total etc.) so the AJAX "update order review on field change"
 * handler keeps working, but dresses it in brand typography.
 *
 * WC hooks preserved:
 *   - woocommerce_review_order_before_cart_contents
 *   - woocommerce_review_order_after_cart_contents
 *   - woocommerce_review_order_before_shipping
 *   - woocommerce_review_order_after_shipping
 *   - woocommerce_review_order_before_order_total
 *   - woocommerce_review_order_after_order_total
 *   - woocommerce_review_order_before_payment
 *   - woocommerce_review_order_before_submit
 *   - woocommerce_review_order_after_submit
 *   - woocommerce_review_order_after_payment
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table sp-review-table w-full">
	<thead>
		<tr>
			<th class="product-name text-left text-neutral-900 text-sm font-bold uppercase tracking-wide pb-3 border-b border-neutral-200"><?php esc_html_e( 'Product', 'smart-pharmacy' ); ?></th>
			<th class="product-total text-right text-neutral-900 text-sm font-bold uppercase tracking-wide pb-3 border-b border-neutral-200"><?php esc_html_e( 'Total', 'smart-pharmacy' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<td class="product-name py-3 pr-3 border-b border-neutral-100 text-neutral-800 text-sm">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?>
						<strong class="product-quantity text-neutral-500 font-normal"><?php echo esc_html( apply_filters( 'woocommerce_checkout_cart_item_quantity', ' &times;&nbsp;' . $cart_item['quantity'], $cart_item, $cart_item_key ) ); ?></strong>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
					<td class="product-total py-3 text-right border-b border-neutral-100 text-neutral-900 font-semibold text-sm">
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</td>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal">
			<th class="py-3 pr-3 text-left text-neutral-600 font-normal text-sm"><?php esc_html_e( 'Subtotal', 'smart-pharmacy' ); ?></th>
			<td class="py-3 text-right text-neutral-900 font-semibold text-sm"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th class="py-3 pr-3 text-left text-neutral-600 font-normal text-sm"><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td class="py-3 text-right text-teal-600 font-semibold text-sm"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th class="py-3 pr-3 text-left text-neutral-600 font-normal text-sm"><?php echo esc_html( $fee->name ); ?></th>
				<td class="py-3 text-right text-neutral-900 font-semibold text-sm"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th class="py-3 pr-3 text-left text-neutral-600 font-normal text-sm"><?php echo esc_html( $tax->label ); ?></th>
						<td class="py-3 text-right text-neutral-900 font-semibold text-sm"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th class="py-3 pr-3 text-left text-neutral-600 font-normal text-sm"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td class="py-3 text-right text-neutral-900 font-semibold text-sm"><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th class="pt-4 pr-3 text-left text-neutral-900 font-bold text-base border-t-2 border-neutral-200"><?php esc_html_e( 'Total', 'smart-pharmacy' ); ?></th>
			<td class="pt-4 text-right text-neutral-900 font-black text-2xl border-t-2 border-neutral-200"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>

<?php do_action( 'woocommerce_review_order_before_payment' ); ?>

<div id="payment" class="woocommerce-checkout-payment mt-6">
	<?php if ( WC()->cart->needs_payment() ) : ?>
		<h4 class="text-neutral-900 text-base font-bold mb-3"><?php esc_html_e( 'Payment method', 'smart-pharmacy' ); ?></h4>
		<ul class="wc_payment_methods payment_methods methods list-none p-0 m-0 space-y-2">
			<?php
			$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info text-neutral-600 bg-neutral-50 border border-neutral-200 rounded-lg p-4 text-sm">';
				echo wp_kses_post( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) );
				echo '</li>';
			}
			?>
		</ul>
	<?php endif; ?>

	<div class="form-row place-order mt-6">
		<noscript>
			<?php
			/* translators: $1 and $2 opening and closing emphasis tags respectively. */
			printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
			?>
			<br/><button type="submit" class="button alt sp-btn-ghost" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt sp-place-order-btn text-white text-base font-bold items-center bg-[linear-gradient(to_right,rgb(59,155,159),rgb(44,122,126))] shadow-[rgba(0,0,0,0.1)_0px_10px_15px_-3px,rgba(0,0,0,0.1)_0px_4px_6px_-4px] gap-x-2 inline-flex justify-center leading-6 w-full px-8 py-4 rounded-full hover:shadow-[rgba(0,0,0,0.25)_0px_25px_50px_-12px] transition-all duration-300" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>
</div>

<?php do_action( 'woocommerce_review_order_after_payment' ); ?>
