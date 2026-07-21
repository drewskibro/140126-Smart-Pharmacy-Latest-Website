<?php
/**
 * "Consultation received" customer email.
 *
 * Sent when an order enters Awaiting Clinical Review, reassuring the
 * customer that a pharmacist will review their answers (and may be in
 * touch) before anything is dispatched or charged. Integrates with
 * WooCommerce → Settings → Emails so staff can toggle/edit it, and uses
 * the WC email wrapper so the "Branded transactional emails" card can
 * style it centrally later.
 *
 * Loaded only from SPE_Consultation_Email::register_class(), i.e. when
 * WC_Email already exists.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Email' ) ) {
	return;
}

/**
 * SPE_Email_Consultation_Received.
 */
class SPE_Email_Consultation_Received extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id             = 'spe_consultation_received';
		$this->title          = __( 'Consultation received', 'smart-pharmacy-eligibility' );
		$this->description    = __( 'Sent to the customer when their order enters Awaiting Clinical Review, before a pharmacist has approved it.', 'smart-pharmacy-eligibility' );
		$this->customer_email = true;
		$this->placeholders   = array(
			'{site_title}'   => $this->get_blogname(),
			'{order_number}' => '',
		);

		// Trigger on the custom status transition (registered as an email
		// action by SPE_Consultation_Email::register_action()).
		add_action(
			'woocommerce_order_status_' . SPE_Consultation_Order::STATUS_SLUG . '_notification',
			array( $this, 'trigger' ),
			10,
			2
		);

		parent::__construct();
	}

	/**
	 * Default subject (WC merges {placeholders}).
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return class_exists( 'SPE_Consultation_Emails_Admin' )
			? SPE_Consultation_Emails_Admin::get_subject( 'received' )
			: __( '[{site_title}] We have your consultation', 'smart-pharmacy-eligibility' );
	}

	/**
	 * Default heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return class_exists( 'SPE_Consultation_Emails_Admin' )
			? SPE_Consultation_Emails_Admin::get_heading( 'received' )
			: __( "Thanks — we've received your consultation", 'smart-pharmacy-eligibility' );
	}

	/**
	 * Send the email for an order.
	 *
	 * @param int           $order_id
	 * @param WC_Order|bool $order
	 */
	public function trigger( $order_id, $order = false ) {
		$this->setup_locale();

		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( is_a( $order, 'WC_Order' ) ) {
			$this->object                         = $order;
			$this->recipient                      = $order->get_billing_email();
			$this->placeholders['{order_number}'] = $order->get_order_number();
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	/**
	 * Body copy (shared by HTML + plain).
	 *
	 * @return string
	 */
	protected function body_text() {
		return class_exists( 'SPE_Consultation_Emails_Admin' )
			? SPE_Consultation_Emails_Admin::get_body( 'received' )
			: __( 'Thanks for completing your consultation. One of our pharmacists will review your answers and may contact you with a few further questions before anything is dispatched. You have not been charged yet — payment is only taken once a pharmacist approves your order.', 'smart-pharmacy-eligibility' );
	}

	/**
	 * HTML content via the WC email wrapper.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template(
			'emails/email-header.php',
			array(
				'email_heading' => $this->get_heading(),
				'email'         => $this,
			)
		);
		echo wpautop( wptexturize( $this->body_text() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static translatable copy.
		if ( is_a( $this->object, 'WC_Order' ) ) {
			do_action( 'woocommerce_email_order_details', $this->object, false, false, $this );
		}
		wc_get_template( 'emails/email-footer.php', array( 'email' => $this ) );
		return ob_get_clean();
	}

	/**
	 * Plain-text content.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		$out  = $this->get_heading() . "\n\n";
		$out .= $this->body_text() . "\n";
		if ( is_a( $this->object, 'WC_Order' ) ) {
			$out .= "\n" . sprintf(
				/* translators: %s: order number. */
				__( 'Order: %s', 'smart-pharmacy-eligibility' ),
				$this->object->get_order_number()
			) . "\n";
		}
		return $out;
	}

	/**
	 * Settings fields shown under WooCommerce → Settings → Emails.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'smart-pharmacy-eligibility' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'smart-pharmacy-eligibility' ),
				'default' => 'yes',
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'smart-pharmacy-eligibility' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Available placeholders: {site_title}, {order_number}', 'smart-pharmacy-eligibility' ),
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email heading', 'smart-pharmacy-eligibility' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'Available placeholders: {site_title}, {order_number}', 'smart-pharmacy-eligibility' ),
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'smart-pharmacy-eligibility' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'smart-pharmacy-eligibility' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}
}
