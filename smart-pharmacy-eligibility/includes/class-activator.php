<?php
/**
 * Plugin activation: create the assessments table.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * Runs once when the plugin is activated.
 */
class SPE_Activator {

	/**
	 * Activation entry point: build tables + seed default options.
	 *
	 * Uses dbDelta so subsequent schema changes are migrations rather
	 * than drop-and-recreates. SPE_DB_VERSION is bumped whenever a
	 * schema below changes.
	 */
	public static function activate() {
		self::create_tables();
		update_option( 'spe_db_version', SPE_DB_VERSION );

		// Seed an empty product map so the admin settings screen has the
		// expected option key on first load.
		if ( false === get_option( 'spe_product_map' ) ) {
			add_option( 'spe_product_map', array() );
		}
	}

	/**
	 * Run the schema migration on a normal request when the stored DB
	 * version is behind the code's. Deploys SCP the plugin files without
	 * re-activating, so without this the consultations table would never
	 * be created on staging / production. Cheap no-op once up to date.
	 */
	public static function maybe_upgrade() {
		if ( (string) get_option( 'spe_db_version' ) === (string) SPE_DB_VERSION ) {
			return;
		}
		self::create_tables();
		update_option( 'spe_db_version', SPE_DB_VERSION );
	}

	/**
	 * Create / upgrade all plugin tables via dbDelta.
	 */
	public static function create_tables() {
		global $wpdb;

		$charset = $wpdb->get_charset_collate();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$assessments = $wpdb->prefix . 'spe_assessments';
		$sql = "CREATE TABLE {$assessments} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			assessment_id CHAR(36) NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'partial',
			ineligible_reason TEXT NULL,
			first_name VARCHAR(100) NULL,
			last_name VARCHAR(100) NULL,
			email VARCHAR(190) NULL,
			phone VARCHAR(40) NULL,
			dob DATE NULL,
			sex VARCHAR(10) NULL,
			ethnicity VARCHAR(60) NULL,
			height_cm DECIMAL(6,2) NULL,
			weight_kg DECIMAL(6,2) NULL,
			bmi DECIMAL(5,2) NULL,
			user_type VARCHAR(20) NULL,
			provider VARCHAR(80) NULL,
			current_medication VARCHAR(40) NULL,
			current_dose VARCHAR(20) NULL,
			diabetes VARCHAR(40) NULL,
			conditions LONGTEXT NULL,
			weight_conditions LONGTEXT NULL,
			bariatric_details TEXT NULL,
			mental_health_details TEXT NULL,
			other_conditions TEXT NULL,
			prev_meds LONGTEXT NULL,
			prev_weights LONGTEXT NULL,
			current_meds TEXT NULL,
			allergies TEXT NULL,
			goal_weight VARCHAR(20) NULL,
			address_line1 VARCHAR(190) NULL,
			address_line2 VARCHAR(190) NULL,
			city VARCHAR(100) NULL,
			postcode VARCHAR(15) NULL,
			country VARCHAR(60) NULL,
			gp_name VARCHAR(190) NULL,
			gp_postcode VARCHAR(15) NULL,
			gp_consent_share TINYINT(1) NOT NULL DEFAULT 0,
			gp_consent_scr TINYINT(1) NOT NULL DEFAULT 0,
			selected_treatment VARCHAR(40) NULL,
			selected_dose VARCHAR(20) NULL,
			raw_payload LONGTEXT NULL,
			order_id BIGINT(20) UNSIGNED NULL,
			ip_address VARCHAR(45) NULL,
			user_agent TEXT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY assessment_id (assessment_id),
			KEY email (email),
			KEY status (status),
			KEY created_at (created_at),
			KEY order_id (order_id)
		) {$charset};";
		dbDelta( $sql );

		// P-med consultation submissions. UUID-keyed like assessments so
		// the same id can bridge the cart and the order in later cards.
		$consultations = $wpdb->prefix . 'spe_consultations';
		$sql = "CREATE TABLE {$consultations} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			consultation_id CHAR(36) NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'submitted',
			product_id BIGINT(20) UNSIGNED NULL,
			first_name VARCHAR(100) NULL,
			last_name VARCHAR(100) NULL,
			email VARCHAR(190) NULL,
			phone VARCHAR(40) NULL,
			dob DATE NULL,
			who_for VARCHAR(60) NULL,
			answers LONGTEXT NULL,
			notes LONGTEXT NULL,
			order_id BIGINT(20) UNSIGNED NULL,
			ip_address VARCHAR(45) NULL,
			user_agent TEXT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY consultation_id (consultation_id),
			KEY status (status),
			KEY product_id (product_id),
			KEY order_id (order_id),
			KEY email (email),
			KEY created_at (created_at)
		) {$charset};";
		dbDelta( $sql );
	}
}
