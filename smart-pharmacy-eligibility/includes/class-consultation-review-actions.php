<?php
/**
 * Pharmacist Approve / Reject actions on a consultation order.
 *
 * Renders the decision buttons inside the clinical review panel and
 * handles the POST:
 *
 *   Approve -> order to "processing"  -> the configured Stripe gateway
 *              captures the held (authorised) payment, order proceeds to
 *              dispatch, customer gets WooCommerce's Processing email.
 *   Reject  -> order to "cancelled"   -> the gateway voids the
 *              authorisation (no charge), customer is emailed.
 *
 * We deliberately drive capture/void through standard WooCommerce status
 * transitions rather than calling Stripe directly, so it's gateway-
 * agnostic and there are no API keys in this plugin (keys live in the
 * WooCommerce Stripe settings). MUST be verified end-to-end on staging
 * once Stripe manual-capture is configured.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Review_Actions.
 */
class SPE_Consultation_Review_Actions {

	const ACTION     = 'spe_consultation_decision';
	const CAPABILITY = 'manage_woocommerce';

	/** Order statuses where a pharmacist decision is still pending. */
	const PENDING_STATUSES = array( 'awaiting-review', 'on-hold', 'pending' );

	/**
	 * Wire hooks.
	 */
	public static function register() {
		add_action( 'spe_after_consultation_review', array( __CLASS__, 'render_actions' ), 10, 2 );
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'handle_decision' ) );
	}

	/**
	 * Render the Approve / Reject form inside the review panel.
	 *
	 * @param WC_Order $order
	 * @param object   $row   Consultation row (unused but passed by the hook).
	 */
	public static function render_actions( $order, $row ) {
		if ( ! is_a( $order, 'WC_Order' ) || ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		$status = $order->get_status();
		if ( ! in_array( $status, self::PENDING_STATUSES, true ) ) {
			// Already decided — show the outcome instead of the buttons.
			$decision = $order->get_meta( '_spe_review_decision' );
			if ( $decision ) {
				$by   = (int) $order->get_meta( '_spe_review_by' );
				$user = $by ? get_userdata( $by ) : null;
				echo '<p class="spe-decision-made"><strong>' . esc_html(
					'approve' === $decision
						? __( 'Approved', 'smart-pharmacy-eligibility' )
						: __( 'Rejected', 'smart-pharmacy-eligibility' )
				) . '</strong>';
				if ( $user ) {
					echo ' · ' . esc_html( $user->display_name );
				}
				echo '</p>';
			}
			return;
		}
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="spe-decision" style="margin-top:12px;border-top:1px solid #dcdcde;padding-top:12px;">
			<input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>" />
			<input type="hidden" name="order_id" value="<?php echo (int) $order->get_id(); ?>" />
			<?php wp_nonce_field( self::ACTION . '_' . $order->get_id() ); ?>

			<p style="margin:0 0 6px;font-weight:600;"><?php esc_html_e( 'Pharmacist decision', 'smart-pharmacy-eligibility' ); ?></p>
			<p>
				<label for="spe-decision-reason"><?php esc_html_e( 'Notes (included in the message to the customer if you reject):', 'smart-pharmacy-eligibility' ); ?></label><br />
				<textarea id="spe-decision-reason" name="reason" rows="2" style="width:100%;"></textarea>
			</p>
			<p>
				<button type="submit" name="decision" value="approve" class="button button-primary">
					<?php esc_html_e( 'Approve & capture payment', 'smart-pharmacy-eligibility' ); ?>
				</button>
				<button type="submit" name="decision" value="reject" class="button"
					onclick="return confirm('<?php echo esc_js( __( 'Reject this consultation and void the held payment? The customer will not be charged.', 'smart-pharmacy-eligibility' ) ); ?>');">
					<?php esc_html_e( 'Reject & void payment', 'smart-pharmacy-eligibility' ); ?>
				</button>
			</p>
			<p class="description"><?php esc_html_e( 'Approving captures the held payment via the configured gateway; rejecting voids it.', 'smart-pharmacy-eligibility' ); ?></p>
		</form>
		<?php
	}

	/**
	 * Handle the decision POST.
	 */
	public static function handle_decision() {
		$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		check_admin_referer( self::ACTION . '_' . $order_id );

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'smart-pharmacy-eligibility' ) );
		}
		if ( ! function_exists( 'wc_get_order' ) ) {
			wp_die( esc_html__( 'WooCommerce is not active.', 'smart-pharmacy-eligibility' ) );
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_die( esc_html__( 'Order not found.', 'smart-pharmacy-eligibility' ) );
		}

		$decision = isset( $_POST['decision'] ) ? sanitize_text_field( wp_unslash( $_POST['decision'] ) ) : '';
		$reason   = isset( $_POST['reason'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reason'] ) ) : '';

		// Only act on orders still awaiting a decision (idempotent / no
		// double-capture if the button is re-submitted).
		if ( in_array( $order->get_status(), self::PENDING_STATUSES, true ) && in_array( $decision, array( 'approve', 'reject' ), true ) ) {
			$order->update_meta_data( '_spe_review_decision', $decision );
			$order->update_meta_data( '_spe_review_by', get_current_user_id() );
			$order->update_meta_data( '_spe_review_at', current_time( 'mysql' ) );
			if ( $reason ) {
				$order->update_meta_data( '_spe_review_reason', $reason );
			}

			if ( 'approve' === $decision ) {
				$note = __( 'Consultation approved by pharmacist — capturing payment.', 'smart-pharmacy-eligibility' );
				if ( $reason ) {
					$note .= ' ' . $reason;
				}
				// Transition to processing -> the gateway captures the
				// authorised payment.
				$order->update_status( 'processing', $note );
			} else {
				$note = __( 'Consultation rejected by pharmacist — voiding payment.', 'smart-pharmacy-eligibility' );
				if ( $reason ) {
					$note .= ' ' . $reason;
				}
				// Transition to cancelled -> the gateway voids the
				// authorisation (no charge).
				$order->update_status( 'cancelled', $note );
				self::notify_rejected( $order, $reason );
			}
		}

		$redirect = wp_get_referer();
		if ( ! $redirect ) {
			$redirect = admin_url( 'admin.php?page=wc-orders' );
		}
		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Email the customer that their consultation was not approved.
	 *
	 * Lightweight, sent through the WooCommerce mailer wrapper so it's
	 * branded by the same templates the "Branded transactional emails"
	 * card will refine.
	 *
	 * @param WC_Order $order
	 * @param string   $reason
	 */
	protected static function notify_rejected( $order, $reason ) {
		$to = $order->get_billing_email();
		if ( ! $to || ! function_exists( 'WC' ) ) {
			return;
		}

		$site = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		// Editable copy (Eligibility → Consultation Emails), with the
		// hardcoded strings as the fallback.
		if ( class_exists( 'SPE_Consultation_Emails_Admin' ) ) {
			$heading = str_replace( '{site_title}', $site, SPE_Consultation_Emails_Admin::get_heading( 'rejected' ) );
			$message = str_replace( '{site_title}', $site, SPE_Consultation_Emails_Admin::get_body( 'rejected' ) );
			$subject = str_replace( '{site_title}', $site, SPE_Consultation_Emails_Admin::get_subject( 'rejected' ) );
		} else {
			$heading = __( 'About your recent consultation', 'smart-pharmacy-eligibility' );
			$message = __( 'Thank you for completing your consultation. After review, our pharmacist was unable to approve this treatment for you on this occasion, so your order has been cancelled and you have not been charged. If you have any questions, please reply to this email or contact us.', 'smart-pharmacy-eligibility' );
			$subject = sprintf( /* translators: %s: site title. */ __( '[%s] An update on your consultation', 'smart-pharmacy-eligibility' ), $site );
		}

		if ( $reason ) {
			$message .= "\n\n" . __( 'Note from the pharmacist:', 'smart-pharmacy-eligibility' ) . ' ' . $reason;
		}

		$mailer  = WC()->mailer();
		$wrapped = $mailer->wrap_message( $heading, wpautop( wptexturize( $message ) ) );

		$mailer->send( $to, $subject, $wrapped, "Content-Type: text/html\r\n" );
	}
}
