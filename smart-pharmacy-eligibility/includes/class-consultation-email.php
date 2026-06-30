<?php
/**
 * Wiring for the "Consultation received" customer email.
 *
 * Registers the custom WC_Email and tells WooCommerce's email system to
 * treat the Awaiting-Clinical-Review status transition as a trigger.
 * The email class itself lives in
 * class-email-consultation-received.php and is loaded lazily inside the
 * woocommerce_email_classes filter, when WC_Email is guaranteed loaded.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Email.
 */
class SPE_Consultation_Email {

	/**
	 * Wire hooks. No-op unless WooCommerce's email system is present.
	 */
	public static function register() {
		add_filter( 'woocommerce_email_classes', array( __CLASS__, 'register_class' ) );
		add_filter( 'woocommerce_email_actions', array( __CLASS__, 'register_action' ) );
	}

	/**
	 * Make the status transition a transactional-email trigger so
	 * WooCommerce fires the matching `..._notification` action.
	 *
	 * @param array $actions
	 * @return array
	 */
	public static function register_action( $actions ) {
		$actions[] = 'woocommerce_order_status_' . SPE_Consultation_Order::STATUS_SLUG;
		return $actions;
	}

	/**
	 * Add our email to WooCommerce's email manager.
	 *
	 * @param array $emails
	 * @return array
	 */
	public static function register_class( $emails ) {
		if ( class_exists( 'WC_Email' ) && ! class_exists( 'SPE_Email_Consultation_Received' ) ) {
			require_once SPE_PLUGIN_DIR . 'includes/class-email-consultation-received.php';
		}
		if ( class_exists( 'SPE_Email_Consultation_Received' ) ) {
			$emails['SPE_Email_Consultation_Received'] = new SPE_Email_Consultation_Received();
		}
		return $emails;
	}
}
