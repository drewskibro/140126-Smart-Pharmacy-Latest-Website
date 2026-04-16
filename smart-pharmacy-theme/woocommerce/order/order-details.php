<?php
/**
 * Order details (Stage 4a-5 brand override).
 *
 * Shown on:
 *   - The thank-you page (immediately after checkout)
 *   - The "View order" page in My Account
 *   - The reorder/order-tracking pages
 *
 * Renders the line-items table + totals + (optionally) customer
 * details.  Wraps WC's hooks in the same brand card pattern used by
 * cart-totals.php and the checkout review.
 *
 * WC hooks preserved:
 *   - woocommerce_order_details_before_order_table
 *   - woocommerce_order_details_before_order_table_items
 *   - woocommerce_order_details_after_order_table_items
 *   - woocommerce_get_order_item_totals  (filter; we honour it)
 *   - woocommerce_order_details_after_order_table
 *
 * @package SmartPharmacy
 *
 * @var WC_Order $order
 * @var bool     $show_customer_details
 * @var bool     $show_downloads
 */

defined( 'ABSPATH' ) || exit;

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>

<section class="woocommerce-order-details sp-order-details bg-white border border-neutral-200 rounded-2xl p-6 md:p-8 shadow-sm mb-8">

	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title text-neutral-900 text-xl font-bold mb-5"><?php esc_html_e( 'Order details', 'smart-pharmacy' ); ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details sp-order-table w-full text-sm">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name text-left text-neutral-900 text-xs font-bold uppercase tracking-wide pb-3 border-b border-neutral-200"><?php esc_html_e( 'Product', 'smart-pharmacy' ); ?></th>
				<th class="woocommerce-table__product-table product-total text-right text-neutral-900 text-xs font-bold uppercase tracking-wide pb-3 border-b border-neutral-200"><?php esc_html_e( 'Total', 'smart-pharmacy' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				$is_total_row = ( 'order_total' === $key );
				?>
					<tr>
						<th scope="row" class="<?php echo $is_total_row ? 'pt-4 pr-3 text-left text-neutral-900 font-bold text-base border-t-2 border-neutral-200' : 'py-2 pr-3 text-left text-neutral-600 font-normal'; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
						<td class="<?php echo $is_total_row ? 'pt-4 text-right text-neutral-900 font-black text-2xl border-t-2 border-neutral-200' : 'py-2 text-right text-neutral-900 font-semibold'; ?>"><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th scope="row" class="py-2 pr-3 text-left text-neutral-600 font-normal align-top"><?php esc_html_e( 'Note:', 'smart-pharmacy' ); ?></th>
					<td class="py-2 text-right text-neutral-700 italic"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>

	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
