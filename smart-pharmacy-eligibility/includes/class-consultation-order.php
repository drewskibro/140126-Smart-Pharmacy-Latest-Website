<?php
/**
 * Order side of the consultation flow.
 *
 * Three things:
 *   1. Registers the custom WooCommerce order status
 *      "Awaiting Clinical Review" (wc-awaiting-review) + its admin
 *      colour and dropdown entry.
 *   2. The clinician review meta box on the order edit screen — a
 *      readable, print-friendly view of every consultation answer.
 *   3. attach() — the single seam the (deferred, Stripe-dependent)
 *      payment / checkout card calls to link a consultation to the
 *      order it created and drop it into Awaiting Clinical Review.
 *
 * HPOS-aware throughout: statuses go through wc_order_statuses, the
 * meta box registers on both the legacy `shop_order` screen and the
 * HPOS orders screen, and order data is read via the CRUD API.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Order.
 */
class SPE_Consultation_Order {

	/** Full status key (post status + wc_get_order_statuses key). */
	const STATUS = 'wc-awaiting-review';

	/** Slug without wc- (CSS class + the status-transition action). */
	const STATUS_SLUG = 'awaiting-review';

	/** Order meta holding the linked consultation UUID. */
	const ORDER_META = '_spe_consultation_id';

	/**
	 * Wire hooks.
	 */
	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_status' ) );
		add_filter( 'wc_order_statuses', array( __CLASS__, 'add_to_dropdown' ) );
		add_action( 'admin_head', array( __CLASS__, 'status_colour_css' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ), 30, 2 );
	}

	/**
	 * Register the holding status. Not "paid"/"processing" — payment is
	 * only authorised at this point; the approve step captures it.
	 */
	public static function register_status() {
		register_post_status(
			self::STATUS,
			array(
				'label'                     => _x( 'Awaiting Clinical Review', 'Order status', 'smart-pharmacy-eligibility' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: order count. */
				'label_count'               => _n_noop( 'Awaiting Clinical Review <span class="count">(%s)</span>', 'Awaiting Clinical Review <span class="count">(%s)</span>', 'smart-pharmacy-eligibility' ),
			)
		);
	}

	/**
	 * Slot the status into the order-status dropdown, right after
	 * Pending payment.
	 *
	 * @param array $statuses
	 * @return array
	 */
	public static function add_to_dropdown( $statuses ) {
		$out = array();
		foreach ( $statuses as $key => $label ) {
			$out[ $key ] = $label;
			if ( 'wc-pending' === $key ) {
				$out[ self::STATUS ] = _x( 'Awaiting Clinical Review', 'Order status', 'smart-pharmacy-eligibility' );
			}
		}
		// If wc-pending wasn't present for some reason, ensure it's added.
		if ( ! isset( $out[ self::STATUS ] ) ) {
			$out[ self::STATUS ] = _x( 'Awaiting Clinical Review', 'Order status', 'smart-pharmacy-eligibility' );
		}
		return $out;
	}

	/**
	 * Amber pill for the status in the admin order list.
	 */
	public static function status_colour_css() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! self::is_order_screen( $screen->id ) ) {
			return;
		}
		echo '<style>
			.order-status.status-' . esc_attr( self::STATUS_SLUG ) . ' { background:#fef3c7; color:#92400e; }
			.order-status.status-' . esc_attr( self::STATUS_SLUG ) . '::before { background:#f59e0b; }
		</style>';
	}

	/* ----------------------------------------------------------------
	 * Integration seam (called by the payment / checkout card)
	 * ---------------------------------------------------------------- */

	/**
	 * Link a consultation to an order and move it into review.
	 *
	 * The Stripe authorise/capture flow is a separate card; this is the
	 * non-payment half it will call once an order exists.
	 *
	 * @param WC_Order|int $order
	 * @param string       $consultation_id UUID.
	 * @return void
	 */
	public static function attach( $order, $consultation_id ) {
		$order = is_a( $order, 'WC_Order' ) ? $order : ( function_exists( 'wc_get_order' ) ? wc_get_order( $order ) : null );
		if ( ! $order ) {
			return;
		}
		$consultation_id = sanitize_text_field( $consultation_id );
		$order->update_meta_data( self::ORDER_META, $consultation_id );
		$order->set_status( self::STATUS_SLUG, __( 'Consultation submitted — awaiting pharmacist review.', 'smart-pharmacy-eligibility' ) );
		$order->save();

		if ( class_exists( 'SPE_Consultation_Repo' ) && method_exists( 'SPE_Consultation_Repo', 'set_order_id' ) ) {
			SPE_Consultation_Repo::set_order_id( $consultation_id, $order->get_id() );
		}
	}

	/* ----------------------------------------------------------------
	 * Clinician review meta box
	 * ---------------------------------------------------------------- */

	/**
	 * Register the meta box — but only on an order that actually has a
	 * linked consultation, so normal (GSL) orders aren't cluttered with
	 * an empty panel. Works for the legacy `shop_order` post screen and
	 * the HPOS orders screen (where the second arg is the order object
	 * and the first is the screen id).
	 *
	 * @param string                 $post_type_or_screen
	 * @param WP_Post|WC_Order|null  $post_or_order
	 */
	public static function add_meta_box( $post_type_or_screen = '', $post_or_order = null ) {
		if ( ! function_exists( 'wc_get_order' ) ) {
			return;
		}

		$order = null;
		if ( is_a( $post_or_order, 'WC_Order' ) ) {
			$order = $post_or_order;
		} elseif ( is_object( $post_or_order ) && isset( $post_or_order->ID ) ) {
			$order = wc_get_order( $post_or_order->ID );
		}

		if ( ! $order || ! $order->get_meta( self::ORDER_META ) ) {
			return;
		}

		// Classic passes the post type ('shop_order'); HPOS passes the
		// screen id ('woocommerce_page_wc-orders'). Either is a valid
		// add_meta_box screen target.
		$screen = $post_type_or_screen ? $post_type_or_screen : 'shop_order';

		add_meta_box(
			'spe-consultation-review',
			__( 'Clinical Review — Consultation', 'smart-pharmacy-eligibility' ),
			array( __CLASS__, 'render_meta_box' ),
			$screen,
			'normal',
			'high'
		);
	}

	/**
	 * Render the consultation answers for this order.
	 *
	 * @param WP_Post|WC_Order $post_or_order Screen subject.
	 */
	public static function render_meta_box( $post_or_order ) {
		if ( ! function_exists( 'wc_get_order' ) ) {
			return;
		}
		$order = is_a( $post_or_order, 'WC_Order' ) ? $post_or_order : wc_get_order( is_object( $post_or_order ) ? $post_or_order->ID : $post_or_order );
		if ( ! $order ) {
			return;
		}

		$consultation_id = $order->get_meta( self::ORDER_META );
		if ( ! $consultation_id || ! class_exists( 'SPE_Consultation_Repo' ) ) {
			echo '<p>' . esc_html__( 'No consultation is linked to this order yet.', 'smart-pharmacy-eligibility' ) . '</p>';
			return;
		}

		$row = SPE_Consultation_Repo::find( $consultation_id );
		if ( ! $row ) {
			echo '<p>' . esc_html__( 'The linked consultation record could not be found.', 'smart-pharmacy-eligibility' ) . '</p>';
			return;
		}

		$answers   = SPE_Consultation_Repo::answers( $row );
		$label_map = self::label_map( (int) $row->product_id );

		?>
		<div class="spe-review" id="spe-review">
			<table class="widefat striped" style="margin-bottom:12px;">
				<tbody>
					<tr><th style="width:200px;"><?php esc_html_e( 'Submitted', 'smart-pharmacy-eligibility' ); ?></th><td><?php echo esc_html( mysql2date( 'Y-m-d H:i', $row->created_at ) ); ?></td></tr>
					<?php if ( ! empty( $row->who_for ) ) : ?>
						<tr><th><?php esc_html_e( 'Consultation for', 'smart-pharmacy-eligibility' ); ?></th><td><?php echo esc_html( $row->who_for ); ?></td></tr>
					<?php endif; ?>
					<?php if ( ! empty( $row->dob ) ) : ?>
						<tr><th><?php esc_html_e( 'Date of birth', 'smart-pharmacy-eligibility' ); ?></th><td><?php echo esc_html( $row->dob ); ?> <?php echo esc_html( self::age_hint( $row->dob ) ); ?></td></tr>
					<?php endif; ?>
					<?php if ( ! empty( $row->product_id ) ) : ?>
						<tr><th><?php esc_html_e( 'Product', 'smart-pharmacy-eligibility' ); ?></th><td>
							<?php
							$pname = get_the_title( (int) $row->product_id );
							$plink = get_edit_post_link( (int) $row->product_id );
							if ( $plink ) {
								echo '<a href="' . esc_url( $plink ) . '">' . esc_html( $pname ) . '</a>';
							} else {
								echo esc_html( $pname );
							}
							?>
						</td></tr>
					<?php endif; ?>
				</tbody>
			</table>

			<?php if ( empty( $answers ) ) : ?>
				<p><?php esc_html_e( 'The consultation has no recorded answers.', 'smart-pharmacy-eligibility' ); ?></p>
			<?php else : ?>
				<table class="widefat striped spe-review__answers">
					<thead><tr>
						<th style="width:45%;"><?php esc_html_e( 'Question', 'smart-pharmacy-eligibility' ); ?></th>
						<th><?php esc_html_e( 'Answer', 'smart-pharmacy-eligibility' ); ?></th>
					</tr></thead>
					<tbody>
						<?php foreach ( $answers as $key => $value ) : ?>
							<tr>
								<th style="text-align:left;font-weight:600;"><?php echo esc_html( isset( $label_map[ $key ] ) ? $label_map[ $key ] : $key ); ?></th>
								<td><?php echo esc_html( is_array( $value ) ? implode( ', ', $value ) : (string) $value ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<p style="margin-top:12px;">
				<button type="button" class="button" onclick="(function(){var w=window.open('','_blank');w.document.write('<title><?php echo esc_js( __( 'Consultation', 'smart-pharmacy-eligibility' ) ); ?></title>'+document.getElementById('spe-review').innerHTML);w.document.close();w.focus();w.print();})();">
					<?php esc_html_e( 'Print', 'smart-pharmacy-eligibility' ); ?>
				</button>
			</p>
		</div>
		<?php
		/**
		 * After the review panel — the pharmacist Approve/Reject actions
		 * (capture/void) render here. Outside #spe-review so they're not
		 * included when the panel is printed.
		 *
		 * @param WC_Order $order
		 * @param object   $row   Consultation row.
		 */
		do_action( 'spe_after_consultation_review', $order, $row );
	}

	/* ----------------------------------------------------------------
	 * Helpers
	 * ---------------------------------------------------------------- */

	/**
	 * Build a question-key => label map for an order's product, so the
	 * panel shows wording rather than raw keys.
	 *
	 * @param int $product_id
	 * @return array<string,string>
	 */
	protected static function label_map( $product_id ) {
		$map = array();
		if ( ! class_exists( 'SPE_Consultation_Questions' ) ) {
			return $map;
		}
		$questions = SPE_Consultation_Questions::get_questions(
			array(
				'include_disabled' => true,
				'product_id'       => (int) $product_id,
			)
		);
		foreach ( $questions as $q ) {
			$map[ $q['key'] ] = $q['label'];
		}
		return $map;
	}

	/**
	 * "(aged 43)" hint from a DOB, for the clinician.
	 *
	 * @param string $dob Y-m-d.
	 * @return string
	 */
	protected static function age_hint( $dob ) {
		$ts = strtotime( $dob );
		if ( ! $ts ) {
			return '';
		}
		$age = (int) floor( ( time() - $ts ) / YEAR_IN_SECONDS );
		if ( $age < 0 || $age > 120 ) {
			return '';
		}
		/* translators: %d: age in years. */
		return sprintf( __( '(aged %d)', 'smart-pharmacy-eligibility' ), $age );
	}

	/**
	 * Is this admin screen id an order screen (legacy or HPOS)?
	 *
	 * @param string $screen_id
	 * @return bool
	 */
	protected static function is_order_screen( $screen_id ) {
		if ( in_array( $screen_id, array( 'shop_order', 'edit-shop_order', 'woocommerce_page_wc-orders' ), true ) ) {
			return true;
		}
		return false !== strpos( (string) $screen_id, 'wc-orders' );
	}
}
