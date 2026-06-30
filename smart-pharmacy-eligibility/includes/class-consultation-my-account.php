<?php
/**
 * Customer-facing consultation status, under WooCommerce → My Account.
 *
 * Adds a "Consultations" tab that lists the customer's consultations and
 * where each one is in clinical review — Pending review / Approved / Not
 * approved — with timestamps. Cuts support load ("has a pharmacist
 * looked at mine yet?").
 *
 * The customer-facing status is derived from the linked order's status,
 * so it tracks the pharmacist's approve/reject (the Stripe card) for
 * free once that lands. Until then orders sit in Awaiting Clinical
 * Review = "Pending review".
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_My_Account.
 */
class SPE_Consultation_My_Account {

	const ENDPOINT = 'consultations';

	/**
	 * Wire hooks.
	 */
	public static function register() {
		add_action( 'init', array( __CLASS__, 'add_endpoint' ) );
		add_action( 'init', array( __CLASS__, 'maybe_flush' ), 11 );
		add_filter( 'woocommerce_account_menu_items', array( __CLASS__, 'menu_item' ) );
		add_filter( 'woocommerce_endpoint_' . self::ENDPOINT . '_title', array( __CLASS__, 'title' ) );
		add_action( 'woocommerce_account_' . self::ENDPOINT . '_endpoint', array( __CLASS__, 'render' ) );
	}

	/**
	 * Register the My Account rewrite endpoint.
	 */
	public static function add_endpoint() {
		add_rewrite_endpoint( self::ENDPOINT, EP_ROOT | EP_PAGES );
	}

	/**
	 * Flush rewrite rules exactly once after the endpoint is added
	 * (covers SCP deploys that don't re-activate the plugin).
	 */
	public static function maybe_flush() {
		if ( '1' !== get_option( 'spe_account_endpoint' ) ) {
			flush_rewrite_rules( false );
			update_option( 'spe_account_endpoint', '1' );
		}
	}

	/**
	 * Insert the menu item before "Log out".
	 *
	 * @param array $items
	 * @return array
	 */
	public static function menu_item( $items ) {
		$out = array();
		foreach ( $items as $key => $label ) {
			if ( 'customer-logout' === $key ) {
				$out[ self::ENDPOINT ] = __( 'Consultations', 'smart-pharmacy-eligibility' );
			}
			$out[ $key ] = $label;
		}
		if ( ! isset( $out[ self::ENDPOINT ] ) ) {
			$out[ self::ENDPOINT ] = __( 'Consultations', 'smart-pharmacy-eligibility' );
		}
		return $out;
	}

	/**
	 * Endpoint page title.
	 *
	 * @param string $title
	 * @return string
	 */
	public static function title( $title ) {
		return __( 'Consultations', 'smart-pharmacy-eligibility' );
	}

	/**
	 * Render the customer's consultation list.
	 */
	public static function render() {
		$user_id = get_current_user_id();
		if ( ! $user_id || ! function_exists( 'wc_get_orders' ) ) {
			echo '<p>' . esc_html__( 'Please sign in to view your consultations.', 'smart-pharmacy-eligibility' ) . '</p>';
			return;
		}

		// Pull recent orders for this customer and keep the ones that
		// carry a consultation. A customer's order count is small, so a
		// PHP-side filter avoids HPOS/posts meta-query portability issues.
		$orders = wc_get_orders(
			array(
				'customer_id' => $user_id,
				'limit'       => 100,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);

		$rows = array();
		foreach ( (array) $orders as $order ) {
			$consultation_id = $order->get_meta( SPE_Consultation_Order::ORDER_META );
			if ( $consultation_id ) {
				$rows[] = array( $order, $consultation_id );
			}
		}

		if ( empty( $rows ) ) {
			echo '<p>' . esc_html__( 'You don\'t have any consultations yet.', 'smart-pharmacy-eligibility' ) . '</p>';
			return;
		}
		?>
		<table class="woocommerce-orders-table shop_table shop_table_responsive spe-consultations-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Submitted', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'For', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'Status', 'smart-pharmacy-eligibility' ); ?></th>
					<th><?php esc_html_e( 'Updated', 'smart-pharmacy-eligibility' ); ?></th>
					<th class="spe-col-actions">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $rows as $pair ) :
					list( $order, $consultation_id ) = $pair;
					$status   = self::status_label( $order->get_status() );
					$submitted = self::submitted_date( $consultation_id, $order );
					$updated   = $order->get_date_modified();
					?>
					<tr>
						<td data-title="<?php esc_attr_e( 'Submitted', 'smart-pharmacy-eligibility' ); ?>">
							<?php echo esc_html( $submitted ? $submitted : '—' ); ?>
						</td>
						<td data-title="<?php esc_attr_e( 'For', 'smart-pharmacy-eligibility' ); ?>">
							<?php echo esc_html( self::order_product_summary( $order ) ); ?>
						</td>
						<td data-title="<?php esc_attr_e( 'Status', 'smart-pharmacy-eligibility' ); ?>">
							<span class="spe-cstatus spe-cstatus--<?php echo esc_attr( $status['key'] ); ?>"><?php echo esc_html( $status['label'] ); ?></span>
						</td>
						<td data-title="<?php esc_attr_e( 'Updated', 'smart-pharmacy-eligibility' ); ?>">
							<?php echo esc_html( $updated ? wc_format_datetime( $updated ) : '—' ); ?>
						</td>
						<td class="spe-col-actions">
							<a class="woocommerce-button button view" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
								<?php esc_html_e( 'View', 'smart-pharmacy-eligibility' ); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<style>
			.spe-cstatus { display:inline-block; padding:3px 10px; border-radius:999px; font-size:12px; font-weight:600; }
			.spe-cstatus--pending  { background:#fef3c7; color:#92400e; }
			.spe-cstatus--approved { background:#dcfce7; color:#166534; }
			.spe-cstatus--declined { background:#fee2e2; color:#991b1b; }
			.spe-cstatus--other    { background:#e5e7eb; color:#374151; }
		</style>
		<?php
	}

	/* ---------------------------------------------------------------- */

	/**
	 * Map a WC order status to a customer-facing review status.
	 *
	 * @param string $order_status Status slug (no wc- prefix).
	 * @return array{key:string,label:string}
	 */
	protected static function status_label( $order_status ) {
		switch ( $order_status ) {
			case SPE_Consultation_Order::STATUS_SLUG:
			case 'pending':
			case 'on-hold':
				return array( 'key' => 'pending', 'label' => __( 'Pending review', 'smart-pharmacy-eligibility' ) );
			case 'processing':
			case 'completed':
				return array( 'key' => 'approved', 'label' => __( 'Approved', 'smart-pharmacy-eligibility' ) );
			case 'cancelled':
			case 'failed':
			case 'refunded':
				return array( 'key' => 'declined', 'label' => __( 'Not approved', 'smart-pharmacy-eligibility' ) );
			default:
				return array( 'key' => 'other', 'label' => ucfirst( str_replace( '-', ' ', $order_status ) ) );
		}
	}

	/**
	 * Consultation submission date, preferring the stored record and
	 * falling back to the order date.
	 *
	 * @param string   $consultation_id
	 * @param WC_Order $order
	 * @return string
	 */
	protected static function submitted_date( $consultation_id, $order ) {
		if ( class_exists( 'SPE_Consultation_Repo' ) && method_exists( 'SPE_Consultation_Repo', 'find' ) ) {
			$row = SPE_Consultation_Repo::find( $consultation_id );
			if ( $row && ! empty( $row->created_at ) ) {
				return mysql2date( get_option( 'date_format' ), $row->created_at );
			}
		}
		$created = $order->get_date_created();
		return $created ? wc_format_datetime( $created ) : '';
	}

	/**
	 * Short "what it's for" summary from the order's line items.
	 *
	 * @param WC_Order $order
	 * @return string
	 */
	protected static function order_product_summary( $order ) {
		$names = array();
		foreach ( $order->get_items() as $item ) {
			$names[] = $item->get_name();
		}
		$names = array_filter( $names );
		if ( empty( $names ) ) {
			return '—';
		}
		return implode( ', ', $names );
	}
}
