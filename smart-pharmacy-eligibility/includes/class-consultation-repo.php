<?php
/**
 * Storage for submitted P-med consultations.
 *
 * One table, wp_spe_consultations, keyed by a UUID (consultation_id)
 * the same way assessments are — so the externally-visible id never
 * leaks an auto-increment, and the same UUID can bridge the cart and
 * the eventual order in the later payment / review cards.
 *
 * The full set of answers is stored as a JSON blob (answers). DOB and
 * who_for are promoted to their own columns because the clinician
 * review panel (separate card) will want to read/filter them without
 * decoding JSON.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Repo.
 */
class SPE_Consultation_Repo {

	/**
	 * Fully-qualified table name.
	 *
	 * @return string
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'spe_consultations';
	}

	/**
	 * Insert a consultation. Returns the UUID on success, '' on failure.
	 *
	 * @param array $data {
	 *     @type int    $product_id Product the consultation is for (0 if none).
	 *     @type string $dob        ISO date or ''.
	 *     @type string $who_for    Raw who_for answer.
	 *     @type array  $answers    question_key => value map.
	 * }
	 * @return string
	 */
	public static function create( array $data ) {
		global $wpdb;

		$uuid = spe_uuid_v4();
		$now  = current_time( 'mysql' );

		$answers = isset( $data['answers'] ) && is_array( $data['answers'] ) ? $data['answers'] : array();

		$ok = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			self::table(),
			array(
				'consultation_id' => $uuid,
				'status'          => 'submitted',
				'product_id'      => isset( $data['product_id'] ) ? (int) $data['product_id'] : 0,
				'dob'             => self::normalise_date( isset( $data['dob'] ) ? $data['dob'] : '' ),
				'who_for'         => isset( $data['who_for'] ) ? substr( sanitize_text_field( $data['who_for'] ), 0, 60 ) : '',
				'answers'         => wp_json_encode( $answers ),
				'order_id'        => null,
				'ip_address'      => self::client_ip(),
				'user_agent'      => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 255 ) : '',
				'created_at'      => $now,
				'updated_at'      => $now,
			),
			array( '%s', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
		);

		return $ok ? $uuid : '';
	}

	/**
	 * Fetch one consultation by its UUID.
	 *
	 * @param string $consultation_id UUID.
	 * @return object|null Row object or null.
	 */
	public static function find( $consultation_id ) {
		global $wpdb;
		$table = self::table();
		return $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->prepare( "SELECT * FROM {$table} WHERE consultation_id = %s", $consultation_id )
		);
	}

	/**
	 * Link a consultation to the order that completed its checkout.
	 * (Used by the later payment / order-status card.)
	 *
	 * @param string $consultation_id UUID.
	 * @param int    $order_id        WC order ID.
	 * @return void
	 */
	public static function set_order_id( $consultation_id, $order_id ) {
		global $wpdb;
		$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			self::table(),
			array(
				'order_id'   => (int) $order_id,
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'consultation_id' => $consultation_id ),
			array( '%d', '%s' ),
			array( '%s' )
		);
	}

	/**
	 * Decode the stored answers JSON for a row.
	 *
	 * @param object $row Row from find().
	 * @return array question_key => value.
	 */
	public static function answers( $row ) {
		if ( ! $row || empty( $row->answers ) ) {
			return array();
		}
		$decoded = json_decode( $row->answers, true );
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Coerce a date string to Y-m-d, or null if unparseable.
	 *
	 * @param string $value
	 * @return string|null
	 */
	protected static function normalise_date( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return null;
		}
		$ts = strtotime( $value );
		return $ts ? gmdate( 'Y-m-d', $ts ) : null;
	}

	/**
	 * Best-effort client IP for the audit trail (single value, validated).
	 *
	 * @return string
	 */
	protected static function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
		$ip = filter_var( $ip, FILTER_VALIDATE_IP );
		return $ip ? $ip : '';
	}
}
