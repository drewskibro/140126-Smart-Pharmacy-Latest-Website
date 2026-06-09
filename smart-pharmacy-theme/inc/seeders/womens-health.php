<?php
/**
 * One-shot Women's Health treatment landing page seeder.
 *
 * Same pattern as mens-health.php -- runs once on the next admin
 * page load after deploy, creates a Treatment CPT post at slug
 * `womens-health` with B1 Hero + B4 Treatment Meta ACF fields
 * pre-populated. B2-D2 sections fall back to defaults until an
 * editor customises them in admin.
 *
 * Idempotency: `_sp_womens_health_seeded_v1` option guard. Won't
 * recreate the post if it's later deleted.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

/**
 * Create the Women's Health treatment post if missing.
 */
function sp_seed_womens_health_treatment() {
	if ( '1' === (string) get_option( '_sp_womens_health_seeded_v1', '' ) ) {
		return;
	}

	$existing = get_page_by_path( 'womens-health', OBJECT, 'treatment' );
	if ( $existing ) {
		update_option( '_sp_womens_health_seeded_v1', '1' );
		return;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => "Women's Health",
			'post_name'    => 'womens-health',
			'post_type'    => 'treatment',
			'post_status'  => 'publish',
			'post_content' => '',
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	$fields = array(
		// B1 Hero
		'tx_hero_category'             => "WOMEN'S HEALTH",
		'tx_hero_heading_pre'          => 'Expert',
		'tx_hero_heading_highlight'    => "women's health",
		'tx_hero_heading_post'         => 'care made simple',
		'tx_hero_subheading'           => 'HRT, contraception, menopause support, and UTI treatment from UK-licensed female prescribers. Confidential, fast, and delivered to your door.',
		'tx_hero_primary_cta_label'    => 'Start Consultation',
		'tx_hero_primary_cta_url'      => '/start-consultation/',
		'tx_hero_secondary_cta_label'  => 'Learn more',
		'tx_hero_secondary_cta_url'    => '#how-it-works',

		// B4 Treatment Meta
		'tx_meta_short_description'    => 'Confidential UK pharmacy care for HRT, contraception, menopause and UTI.',
		'tx_meta_legal_class'          => 'POM',
		'tx_meta_requires_consultation'=> 1,
	);

	foreach ( $fields as $key => $value ) {
		if ( function_exists( 'update_field' ) ) {
			update_field( $key, $value, $post_id );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}

	$trust = array(
		array( 'icon' => 'shield', 'label' => 'GPhC-registered UK pharmacy' ),
		array( 'icon' => 'lock',   'label' => 'Confidential and discreet' ),
		array( 'icon' => 'truck',  'label' => 'Free next-day delivery' ),
	);
	if ( function_exists( 'update_field' ) ) {
		update_field( 'tx_hero_trust_items', $trust, $post_id );
	}

	$term = term_exists( 'womens-health', 'treatment_category' );
	if ( ! $term ) {
		$term = wp_insert_term( "Women's Health", 'treatment_category', array( 'slug' => 'womens-health' ) );
	}
	if ( ! is_wp_error( $term ) && ! empty( $term['term_id'] ) ) {
		wp_set_object_terms( $post_id, (int) $term['term_id'], 'treatment_category' );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'tx_meta_category', array( (int) $term['term_id'] ), $post_id );
		}
	}

	update_option( '_sp_womens_health_seeded_v1', '1' );
}
add_action( 'init', 'sp_seed_womens_health_treatment', 11 );
