<?php
/**
 * AJAX endpoints for the eligibility checker.
 *
 * Four actions, all gated by the tc_eligibility_nonce nonce:
 *
 *   tc_eligibility_save_partial  -- early capture, returns assessment_id
 *   tc_eligibility_save          -- final submission, returns checkout URL
 *   tc_eligibility_add_to_cart   -- explicit add-to-cart (used if the
 *                                   final submission already happened
 *                                   and the user picks a different dose)
 *   tc_eligibility_ineligible    -- audit log when client-side rules fail
 *
 * Both nopriv_ and authed variants are registered because patients
 * complete the form without logging in.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Ajax.
 */
class SPE_Ajax {

	const NONCE_ACTION = 'tc_eligibility_nonce';

	/**
	 * Wire the handlers.
	 */
	public static function register() {
		$actions = array(
			'tc_eligibility_save_partial' => 'handle_save_partial',
			'tc_eligibility_save'         => 'handle_save_final',
			'tc_eligibility_add_to_cart'  => 'handle_add_to_cart',
			'tc_eligibility_ineligible'   => 'handle_ineligible',
		);
		foreach ( $actions as $action => $callback ) {
			add_action( 'wp_ajax_' . $action, array( __CLASS__, $callback ) );
			add_action( 'wp_ajax_nopriv_' . $action, array( __CLASS__, $callback ) );
		}
	}

	/**
	 * Early capture: name / email / phone after the agreement step.
	 *
	 * Creates a partial row and returns its UUID for the JS to stash
	 * in the tc_eligibility_data cookie.
	 */
	public static function handle_save_partial() {
		self::verify_nonce();

		$payload = self::read_payload();
		$row_id  = SPE_Assessment_Repo::create_partial( $payload );

		wp_send_json_success(
			array(
				'assessment_id' => $row_id,
				'nonce'         => wp_create_nonce( self::NONCE_ACTION ), // rotate
			)
		);
	}

	/**
	 * Final submission: rerun rules, update the row, hand off to WC.
	 */
	public static function handle_save_final() {
		self::verify_nonce();

		$payload       = self::read_payload();
		$assessment_id = isset( $payload['assessmentId'] ) ? sanitize_text_field( $payload['assessmentId'] ) : '';

		if ( ! $assessment_id ) {
			$assessment_id = SPE_Assessment_Repo::create_partial( $payload );
		}

		// Server-side rules are the source of truth.
		$ruling = SPE_Eligibility_Rules::evaluate( $payload );

		if ( ! $ruling['eligible'] ) {
			SPE_Assessment_Repo::update( $assessment_id, $payload, 'partial' );
			SPE_Assessment_Repo::mark_ineligible( $assessment_id, $ruling['reason'] );
			wp_send_json_success(
				array(
					'status' => 'ineligible',
					'reason' => $ruling['reason'],
				)
			);
		}

		SPE_Assessment_Repo::update( $assessment_id, $payload, 'complete' );

		// Add the selected treatment to the WC cart (if WC is active).
		$checkout_url = '';
		if ( function_exists( 'WC' ) ) {
			$product_id = SPE_WooCommerce_Integration::resolve_product_id(
				isset( $payload['selectedTreatment'] ) ? $payload['selectedTreatment'] : '',
				isset( $payload['selectedDose'] ) ? $payload['selectedDose'] : ''
			);
			if ( $product_id ) {
				SPE_WooCommerce_Integration::add_to_cart_with_assessment( $product_id, $assessment_id );
				$checkout_url = wc_get_checkout_url();
			}
		}

		wp_send_json_success(
			array(
				'status'       => 'eligible',
				'checkoutUrl'  => $checkout_url ?: home_url( '/checkout/' ),
				'nonce'        => wp_create_nonce( self::NONCE_ACTION ),
				'assessmentId' => $assessment_id,
			)
		);
	}

	/**
	 * Add-to-cart on its own (e.g. user changes dose post-confirmation).
	 */
	public static function handle_add_to_cart() {
		self::verify_nonce();

		$assessment_id = isset( $_POST['assessmentId'] ) ? sanitize_text_field( wp_unslash( $_POST['assessmentId'] ) ) : '';
		$treatment     = isset( $_POST['treatment'] ) ? sanitize_text_field( wp_unslash( $_POST['treatment'] ) ) : '';
		$dose          = isset( $_POST['dose'] ) ? sanitize_text_field( wp_unslash( $_POST['dose'] ) ) : '';

		if ( ! function_exists( 'WC' ) ) {
			wp_send_json_error( array( 'message' => 'WooCommerce not active' ), 500 );
		}

		$product_id = SPE_WooCommerce_Integration::resolve_product_id( $treatment, $dose );
		if ( ! $product_id ) {
			wp_send_json_error( array( 'message' => 'No matching product configured for that treatment / dose.' ), 422 );
		}

		SPE_WooCommerce_Integration::add_to_cart_with_assessment( $product_id, $assessment_id );

		wp_send_json_success(
			array(
				'checkoutUrl' => wc_get_checkout_url(),
			)
		);
	}

	/**
	 * Log an ineligible outcome (called by JS when a client-side rule
	 * shows the ineligible screen so we still capture the funnel
	 * drop-off and the reason for the audit log).
	 */
	public static function handle_ineligible() {
		self::verify_nonce();

		$assessment_id = isset( $_POST['assessmentId'] ) ? sanitize_text_field( wp_unslash( $_POST['assessmentId'] ) ) : '';
		$reason        = isset( $_POST['reason'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reason'] ) ) : '';

		if ( $assessment_id ) {
			SPE_Assessment_Repo::mark_ineligible( $assessment_id, $reason );
		}

		wp_send_json_success();
	}

	/* ----------------------------------------------------------------
	 * Helpers
	 * ---------------------------------------------------------------- */

	/**
	 * Reject the request if the nonce is missing or wrong.
	 */
	protected static function verify_nonce() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );
	}

	/**
	 * Decode the JSON payload sent by the JS into an associative array.
	 *
	 * The JS sends `payload` as a JSON-encoded string under that key
	 * so nested arrays (conditions[], prevMeds[], prevWeights{})
	 * survive the round-trip cleanly. Falls back to raw POST for
	 * simpler endpoints.
	 *
	 * @return array
	 */
	protected static function read_payload() {
		if ( isset( $_POST['payload'] ) ) {
			$raw     = wp_unslash( $_POST['payload'] );
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				return $decoded;
			}
		}
		return array_map(
			function ( $v ) {
				if ( is_array( $v ) ) {
					return $v;
				}
				return wp_unslash( $v );
			},
			(array) $_POST
		);
	}
}
