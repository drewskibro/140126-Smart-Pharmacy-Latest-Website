<?php
/**
 * Repository for assessment rows.
 *
 * All DB reads/writes for the spe_assessments table go through here so
 * sanitisation, status transitions, and field whitelisting live in one
 * place rather than being scattered through the AJAX handlers.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Assessment_Repo.
 */
class SPE_Assessment_Repo {

	/**
	 * Get the table name (handles WP multisite prefix).
	 *
	 * @return string
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'spe_assessments';
	}

	/**
	 * Map of public payload keys -> DB column names + sanitiser callbacks.
	 *
	 * Centralised so add_partial(), update(), and the admin export all
	 * agree on what fields exist and how to clean them.
	 *
	 * @return array<string, array{column:string, sanitise:callable}>
	 */
	protected static function field_map() {
		return array(
			'firstName'           => array( 'column' => 'first_name',            'sanitise' => 'sanitize_text_field' ),
			'lastName'            => array( 'column' => 'last_name',             'sanitise' => 'sanitize_text_field' ),
			'email'               => array( 'column' => 'email',                 'sanitise' => 'sanitize_email' ),
			'phone'               => array( 'column' => 'phone',                 'sanitise' => 'sanitize_text_field' ),
			'dob'                 => array( 'column' => 'dob',                   'sanitise' => array( __CLASS__, 'sanitise_date' ) ),
			'sex'                 => array( 'column' => 'sex',                   'sanitise' => 'sanitize_text_field' ),
			'ethnicity'           => array( 'column' => 'ethnicity',             'sanitise' => 'sanitize_text_field' ),
			'heightCm'            => array( 'column' => 'height_cm',             'sanitise' => array( __CLASS__, 'sanitise_decimal' ) ),
			'weightKg'            => array( 'column' => 'weight_kg',             'sanitise' => array( __CLASS__, 'sanitise_decimal' ) ),
			'bmi'                 => array( 'column' => 'bmi',                   'sanitise' => array( __CLASS__, 'sanitise_decimal' ) ),
			'userType'            => array( 'column' => 'user_type',             'sanitise' => 'sanitize_text_field' ),
			'provider'            => array( 'column' => 'provider',              'sanitise' => 'sanitize_text_field' ),
			'currentMedication'   => array( 'column' => 'current_medication',    'sanitise' => 'sanitize_text_field' ),
			'currentDose'         => array( 'column' => 'current_dose',          'sanitise' => 'sanitize_text_field' ),
			'diabetes'            => array( 'column' => 'diabetes',              'sanitise' => 'sanitize_text_field' ),
			'conditions'          => array( 'column' => 'conditions',            'sanitise' => array( __CLASS__, 'sanitise_json_array' ) ),
			'weightConditions'    => array( 'column' => 'weight_conditions',     'sanitise' => array( __CLASS__, 'sanitise_json_array' ) ),
			'bariatricDetails'    => array( 'column' => 'bariatric_details',     'sanitise' => 'sanitize_textarea_field' ),
			'mentalHealthDetails' => array( 'column' => 'mental_health_details', 'sanitise' => 'sanitize_textarea_field' ),
			'otherConditions'     => array( 'column' => 'other_conditions',      'sanitise' => 'sanitize_textarea_field' ),
			'prevMeds'            => array( 'column' => 'prev_meds',             'sanitise' => array( __CLASS__, 'sanitise_json_array' ) ),
			'prevWeights'         => array( 'column' => 'prev_weights',          'sanitise' => array( __CLASS__, 'sanitise_json_object' ) ),
			'currentMeds'         => array( 'column' => 'current_meds',          'sanitise' => 'sanitize_textarea_field' ),
			'allergies'           => array( 'column' => 'allergies',             'sanitise' => 'sanitize_textarea_field' ),
			'goalWeight'          => array( 'column' => 'goal_weight',           'sanitise' => 'sanitize_text_field' ),
			'addressLine1'        => array( 'column' => 'address_line1',         'sanitise' => 'sanitize_text_field' ),
			'addressLine2'        => array( 'column' => 'address_line2',         'sanitise' => 'sanitize_text_field' ),
			'city'                => array( 'column' => 'city',                  'sanitise' => 'sanitize_text_field' ),
			'postcode'            => array( 'column' => 'postcode',              'sanitise' => array( __CLASS__, 'sanitise_postcode' ) ),
			'country'             => array( 'column' => 'country',               'sanitise' => 'sanitize_text_field' ),
			'gpName'              => array( 'column' => 'gp_name',               'sanitise' => 'sanitize_text_field' ),
			'gpPostcode'          => array( 'column' => 'gp_postcode',           'sanitise' => array( __CLASS__, 'sanitise_postcode' ) ),
			'gpConsentShare'      => array( 'column' => 'gp_consent_share',      'sanitise' => array( __CLASS__, 'sanitise_bool' ) ),
			'gpConsentScr'        => array( 'column' => 'gp_consent_scr',        'sanitise' => array( __CLASS__, 'sanitise_bool' ) ),
			'selectedTreatment'   => array( 'column' => 'selected_treatment',    'sanitise' => 'sanitize_text_field' ),
			'selectedDose'        => array( 'column' => 'selected_dose',         'sanitise' => 'sanitize_text_field' ),
		);
	}

	/**
	 * Create a new partial row. Returns the assessment_id.
	 *
	 * @param array $payload Raw payload from the early-capture step.
	 * @return string assessment_id (UUID v4)
	 */
	public static function create_partial( array $payload ) {
		global $wpdb;

		$now           = current_time( 'mysql' );
		$assessment_id = spe_uuid_v4();

		$row = array(
			'assessment_id' => $assessment_id,
			'status'        => 'partial',
			'ip_address'    => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			'user_agent'    => isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 500 ) : '',
			'created_at'    => $now,
			'updated_at'    => $now,
		);

		$row = array_merge( $row, self::map_payload( $payload ) );
		$row['raw_payload'] = wp_json_encode( $payload );

		$wpdb->insert( self::table(), $row ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return $assessment_id;
	}

	/**
	 * Update an existing assessment row by assessment_id.
	 *
	 * @param string $assessment_id UUID.
	 * @param array  $payload       Fresh payload.
	 * @param string $status        New status: partial|complete|ineligible.
	 * @return bool true on success.
	 */
	public static function update( $assessment_id, array $payload, $status = 'complete' ) {
		global $wpdb;

		$row              = self::map_payload( $payload );
		$row['raw_payload'] = wp_json_encode( $payload );
		$row['status']    = in_array( $status, array( 'partial', 'complete', 'ineligible' ), true ) ? $status : 'partial';
		$row['updated_at'] = current_time( 'mysql' );

		$existing = self::find_by_assessment_id( $assessment_id );
		if ( ! $existing ) {
			return false;
		}

		$wpdb->update( self::table(), $row, array( 'assessment_id' => $assessment_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return true;
	}

	/**
	 * Flag a row as ineligible with the supplied reason text.
	 *
	 * @param string $assessment_id UUID.
	 * @param string $reason        Human-readable ineligibility reason.
	 * @return bool
	 */
	public static function mark_ineligible( $assessment_id, $reason ) {
		global $wpdb;
		$existing = self::find_by_assessment_id( $assessment_id );
		if ( ! $existing ) {
			return false;
		}
		$wpdb->update(
			self::table(),
			array(
				'status'            => 'ineligible',
				'ineligible_reason' => sanitize_textarea_field( $reason ),
				'updated_at'        => current_time( 'mysql' ),
			),
			array( 'assessment_id' => $assessment_id )
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return true;
	}

	/**
	 * Link a completed assessment to a WC order.
	 *
	 * @param string $assessment_id UUID.
	 * @param int    $order_id      WC order ID.
	 * @return void
	 */
	public static function set_order_id( $assessment_id, $order_id ) {
		global $wpdb;
		$wpdb->update(
			self::table(),
			array(
				'order_id'   => (int) $order_id,
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'assessment_id' => $assessment_id )
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Find a row by its public UUID.
	 *
	 * @param string $assessment_id UUID.
	 * @return object|null DB row.
	 */
	public static function find_by_assessment_id( $assessment_id ) {
		global $wpdb;
		$table = self::table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE assessment_id = %s LIMIT 1", $assessment_id ) ); // phpcs:ignore WordPress.DB
	}

	/**
	 * List assessments (admin grid).
	 *
	 * @param array $args { status, search, limit, offset }
	 * @return array
	 */
	public static function list_recent( array $args = array() ) {
		global $wpdb;
		$table   = self::table();
		$status  = isset( $args['status'] ) ? sanitize_text_field( $args['status'] ) : '';
		$search  = isset( $args['search'] ) ? sanitize_text_field( $args['search'] ) : '';
		$limit   = isset( $args['limit'] ) ? max( 1, (int) $args['limit'] ) : 50;
		$offset  = isset( $args['offset'] ) ? max( 0, (int) $args['offset'] ) : 0;

		$where  = 'WHERE 1=1';
		$params = array();
		if ( $status ) {
			$where    .= ' AND status = %s';
			$params[] = $status;
		}
		if ( $search ) {
			$where    .= ' AND ( email LIKE %s OR first_name LIKE %s OR last_name LIKE %s OR assessment_id = %s )';
			$like      = '%' . $wpdb->esc_like( $search ) . '%';
			$params[]  = $like;
			$params[]  = $like;
			$params[]  = $like;
			$params[]  = $search;
		}

		$sql      = "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d";
		$params[] = $limit;
		$params[] = $offset;

		return $wpdb->get_results( $wpdb->prepare( $sql, $params ) ); // phpcs:ignore WordPress.DB
	}

	/* ----------------------------------------------------------------
	 * Sanitisers
	 * ---------------------------------------------------------------- */

	/**
	 * Reduce the public payload to (column => sanitised value) pairs.
	 *
	 * Unknown keys are dropped.
	 *
	 * @param array $payload Public payload.
	 * @return array<string,mixed>
	 */
	protected static function map_payload( array $payload ) {
		$map = self::field_map();
		$out = array();
		foreach ( $map as $public_key => $spec ) {
			if ( ! array_key_exists( $public_key, $payload ) ) {
				continue;
			}
			$value          = $payload[ $public_key ];
			$out[ $spec['column'] ] = call_user_func( $spec['sanitise'], $value );
		}
		return $out;
	}

	/**
	 * @param mixed $value
	 * @return string Y-m-d or empty string.
	 */
	public static function sanitise_date( $value ) {
		$value = sanitize_text_field( $value );
		if ( '' === $value ) {
			return '';
		}
		$ts = strtotime( $value );
		return $ts ? gmdate( 'Y-m-d', $ts ) : '';
	}

	/**
	 * @param mixed $value
	 * @return float|null
	 */
	public static function sanitise_decimal( $value ) {
		if ( '' === $value || null === $value ) {
			return null;
		}
		$float = (float) $value;
		return $float > 0 ? $float : null;
	}

	/**
	 * @param mixed $value
	 * @return string JSON-encoded array.
	 */
	public static function sanitise_json_array( $value ) {
		if ( ! is_array( $value ) ) {
			return wp_json_encode( array() );
		}
		$clean = array_map( 'sanitize_text_field', $value );
		return wp_json_encode( array_values( $clean ) );
	}

	/**
	 * @param mixed $value
	 * @return string JSON-encoded object (assoc array).
	 */
	public static function sanitise_json_object( $value ) {
		if ( ! is_array( $value ) ) {
			return wp_json_encode( new stdClass() );
		}
		$clean = array();
		foreach ( $value as $k => $v ) {
			$clean[ sanitize_text_field( $k ) ] = sanitize_text_field( $v );
		}
		return wp_json_encode( $clean );
	}

	/**
	 * @param mixed $value
	 * @return string Uppercase + collapsed-space postcode.
	 */
	public static function sanitise_postcode( $value ) {
		$value = strtoupper( sanitize_text_field( $value ) );
		return preg_replace( '/\s+/', ' ', trim( $value ) );
	}

	/**
	 * @param mixed $value
	 * @return int 0|1
	 */
	public static function sanitise_bool( $value ) {
		return $value ? 1 : 0;
	}
}
