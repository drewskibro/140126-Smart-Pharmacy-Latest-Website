<?php
/**
 * AJAX endpoint for the P-med consultation form.
 *
 *   spe_consultation_submit -- validate (server-side), store, hand off.
 *
 * Registered for both nopriv_ and authed users because patients submit
 * without logging in. Client-side validation in consultation.js is for
 * UX only; the rules enforced here are the source of truth — the same
 * defence-in-depth stance the eligibility checker takes.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Ajax.
 */
class SPE_Consultation_Ajax {

	const ACTION       = 'spe_consultation_submit';
	const NONCE_ACTION = 'spe_consultation_nonce';

	/**
	 * Wire the handler.
	 */
	public static function register() {
		add_action( 'wp_ajax_' . self::ACTION, array( __CLASS__, 'handle_submit' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( __CLASS__, 'handle_submit' ) );
	}

	/**
	 * Validate + store a consultation submission.
	 */
	public static function handle_submit() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$payload    = self::read_payload();
		$raw        = isset( $payload['answers'] ) && is_array( $payload['answers'] ) ? $payload['answers'] : array();
		$product_id = isset( $payload['product_id'] ) ? absint( $payload['product_id'] ) : 0;

		$questions = SPE_Consultation_Questions::get_questions( array( 'product_id' => $product_id ) );

		$clean  = array();
		$errors = array();

		foreach ( $questions as $q ) {
			$key   = $q['key'];
			$value = isset( $raw[ $key ] ) ? $raw[ $key ] : '';
			$value = self::sanitise_answer( $q, $value );

			if ( ! empty( $q['required'] ) && self::is_empty( $value ) ) {
				$errors[ $key ] = __( 'This question is required.', 'smart-pharmacy-eligibility' );
				continue;
			}

			// Choice answers must be one of the configured options.
			if ( in_array( $q['type'], array( 'radio', 'select' ), true ) && ! self::is_empty( $value ) ) {
				if ( ! in_array( $value, (array) $q['options'], true ) ) {
					$errors[ $key ] = __( 'Please choose one of the available options.', 'smart-pharmacy-eligibility' );
					continue;
				}
			}

			// DOB sanity: a real, past date.
			if ( 'date' === $q['type'] && ! self::is_empty( $value ) ) {
				$ts = strtotime( $value );
				if ( ! $ts || $ts > time() ) {
					$errors[ $key ] = __( 'Please enter a valid date in the past.', 'smart-pharmacy-eligibility' );
					continue;
				}
			}

			$clean[ $key ] = $value;
		}

		if ( $errors ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please check the highlighted answers and try again.', 'smart-pharmacy-eligibility' ),
					'fields'  => $errors,
				),
				422
			);
		}

		$consultation_id = SPE_Consultation_Repo::create(
			array(
				'product_id' => $product_id,
				'dob'        => isset( $clean['dob'] ) ? $clean['dob'] : '',
				'who_for'    => isset( $clean['who_for'] ) ? $clean['who_for'] : '',
				'answers'    => $clean,
			)
		);

		if ( ! $consultation_id ) {
			wp_send_json_error(
				array( 'message' => __( 'Sorry, we could not save your consultation. Please try again.', 'smart-pharmacy-eligibility' ) ),
				500
			);
		}

		/**
		 * Fires after a consultation is stored and validated.
		 *
		 * The payment / order-creation card hooks here to authorise
		 * payment and create the "Awaiting Clinical Review" order. It is
		 * deliberately a no-op until that card lands.
		 *
		 * @param string $consultation_id UUID.
		 * @param array  $clean           Validated answers, keyed by question key.
		 * @param int    $product_id      Product the consultation is for (0 if none).
		 */
		do_action( 'spe_consultation_submitted', $consultation_id, $clean, $product_id );

		/**
		 * Where to send the customer next. Empty string = show the
		 * on-page confirmation. The payment card sets this to the
		 * checkout URL once a payment-hold flow exists.
		 *
		 * @param string $redirect
		 * @param string $consultation_id
		 * @param int    $product_id
		 */
		$redirect = (string) apply_filters( 'spe_consultation_redirect', '', $consultation_id, $product_id );

		wp_send_json_success(
			array(
				'consultationId' => $consultation_id,
				'redirect'       => $redirect,
				'message'        => __( 'Thank you. Your consultation has been submitted and will be reviewed by a pharmacist.', 'smart-pharmacy-eligibility' ),
			)
		);
	}

	/* ---------------------------------------------------------------- */

	/**
	 * Sanitise a single answer according to its question type.
	 *
	 * @param array $q     Question record.
	 * @param mixed $value Raw value.
	 * @return mixed
	 */
	protected static function sanitise_answer( $q, $value ) {
		switch ( $q['type'] ) {
			case 'textarea':
				return sanitize_textarea_field( (string) $value );
			case 'checkbox':
				$value = is_array( $value ) ? $value : array( $value );
				return array_values( array_filter( array_map( 'sanitize_text_field', $value ), 'strlen' ) );
			case 'date':
				$value = sanitize_text_field( (string) $value );
				$ts    = strtotime( $value );
				return $ts ? gmdate( 'Y-m-d', $ts ) : '';
			default:
				return sanitize_text_field( (string) $value );
		}
	}

	/**
	 * Empty test that treats array() and '' alike.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	protected static function is_empty( $value ) {
		if ( is_array( $value ) ) {
			return 0 === count( $value );
		}
		return '' === trim( (string) $value );
	}

	/**
	 * Decode the JSON `payload` field, matching SPE_Ajax::read_payload().
	 *
	 * @return array
	 */
	protected static function read_payload() {
		if ( isset( $_POST['payload'] ) ) {
			$raw     = wp_unslash( $_POST['payload'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON decoded then each field sanitised per question type.
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				return $decoded;
			}
		}
		return array();
	}
}
