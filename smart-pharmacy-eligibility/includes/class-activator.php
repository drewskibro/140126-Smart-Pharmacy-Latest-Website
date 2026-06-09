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
	 * Create / upgrade the assessments table.
	 *
	 * Uses dbDelta so subsequent schema changes are migrations rather
	 * than drop-and-recreates. SPE_DB_VERSION is bumped whenever the
	 * schema below changes.
	 */
	public static function activate() {
		global $wpdb;

		$table   = $wpdb->prefix . 'spe_assessments';
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
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

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'spe_db_version', SPE_DB_VERSION );

		// Seed an empty product map so the admin settings screen
		// has the expected option key on first load.
		if ( false === get_option( 'spe_product_map' ) ) {
			add_option(
				'spe_product_map',
				array(
					// Filled in by admin: e.g. 'wegovy-0.25mg' => 123 (WC product ID).
				)
			);
		}
	}
}
