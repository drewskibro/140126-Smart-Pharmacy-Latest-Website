<?php
/**
 * One-shot Men's Health treatment landing page seeder.
 *
 * Runs once on the next admin page load after deploy. Creates a
 * Treatment CPT post with slug `mens-health` and populates the B1
 * (Hero) + B4 (Treatment Meta) ACF fields with brand-appropriate
 * default copy so the page is presentable end-to-end without an
 * editor having to fill in 60+ fields first.
 *
 * The rest of the field groups (B2-D2) fall back to their hardcoded
 * defaults from acf-fields.php (most of which are weight-loss themed
 * for now) -- the client can swap the section-specific copy in admin.
 *
 * Idempotency: a one-shot `_sp_mens_health_seeded_v1` option guards
 * against re-running. If the post is later deleted the option stays
 * set, so deletion is respected (the page won't auto-resurrect).
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

/**
 * Create the Men's Health treatment post if missing.
 */
function sp_seed_mens_health_treatment() {
	// Bail early on every request after the first.
	if ( '1' === (string) get_option( '_sp_mens_health_seeded_v1', '' ) ) {
		return;
	}

	// Run from admin / WP-CLI only -- not on every front-end hit.
	if ( ! is_admin() && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	// Don't double-create if a post already exists at this slug.
	$existing = get_page_by_path( 'mens-health', OBJECT, 'treatment' );
	if ( $existing ) {
		update_option( '_sp_mens_health_seeded_v1', '1' );
		return;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => "Men's Health",
			'post_name'    => 'mens-health',
			'post_type'    => 'treatment',
			'post_status'  => 'publish',
			'post_content' => '', // ACF drives the rendered content.
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	// Populate B1 Hero + B4 Treatment Meta. update_field() is ACF;
	// fall back to update_post_meta() with the field-name key if
	// ACF isn't active (rare given the rest of the theme requires it).
	$fields = array(
		// B1 Hero
		'tx_hero_category'             => "MEN'S HEALTH",
		'tx_hero_heading_pre'          => 'Confidential',
		'tx_hero_heading_highlight'    => "men's health",
		'tx_hero_heading_post'         => 'delivered to your door',
		'tx_hero_subheading'           => 'Get treatment for ED, hair loss, and weight management from UK-licensed clinicians. Dispatched discreetly within 24 hours, in plain packaging.',
		'tx_hero_primary_cta_label'    => 'Start Consultation',
		'tx_hero_primary_cta_url'      => '/start-consultation/',
		'tx_hero_secondary_cta_label'  => 'Learn more',
		'tx_hero_secondary_cta_url'    => '#how-it-works',

		// B4 Treatment Meta
		'tx_meta_short_description'    => 'Discreet UK pharmacy treatment for ED, hair loss and weight management.',
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

	// Trust badges row: three pill items under the hero CTAs.
	$trust = array(
		array( 'icon' => 'shield', 'label' => 'GPhC-registered UK pharmacy' ),
		array( 'icon' => 'lock',   'label' => 'Discreet packaging' ),
		array( 'icon' => 'truck',  'label' => 'Free next-day delivery' ),
	);
	if ( function_exists( 'update_field' ) ) {
		update_field( 'tx_hero_trust_items', $trust, $post_id );
	}

	// Assign to the Men's Health treatment category, creating the
	// term if it doesn't yet exist.
	$term = term_exists( 'mens-health', 'treatment_category' );
	if ( ! $term ) {
		$term = wp_insert_term( "Men's Health", 'treatment_category', array( 'slug' => 'mens-health' ) );
	}
	if ( ! is_wp_error( $term ) && ! empty( $term['term_id'] ) ) {
		wp_set_object_terms( $post_id, (int) $term['term_id'], 'treatment_category' );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'tx_meta_category', array( (int) $term['term_id'] ), $post_id );
		}
	}

	update_option( '_sp_mens_health_seeded_v1', '1' );
}
add_action( 'admin_init', 'sp_seed_mens_health_treatment' );
