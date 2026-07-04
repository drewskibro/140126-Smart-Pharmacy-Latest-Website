<?php
/**
 * One-shot Erectile Dysfunction treatment landing page seeder.
 *
 * Same pattern as the Men's / Women's Health seeders: creates a
 * Treatment CPT post with slug `erectile-dysfunction` on the next admin
 * page load after deploy, populated with B1 (Hero) + B4 (Treatment Meta)
 * ACF defaults. Remaining field groups fall back to their acf-fields.php
 * defaults; the client swaps the section copy in admin.
 *
 * Copy is deliberately condition-level (no specific POM names in the
 * hero) — final wording is a pharmacist / MHRA-CAP review point.
 *
 * Idempotency: guarded by `_sp_ed_seeded_v1`; respects deletion.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

/**
 * Create the Erectile Dysfunction treatment post if missing.
 */
function sp_seed_ed_treatment() {
	if ( '1' === (string) get_option( '_sp_ed_seeded_v1', '' ) ) {
		return;
	}

	$existing = get_page_by_path( 'erectile-dysfunction', OBJECT, 'treatment' );
	if ( $existing ) {
		update_option( '_sp_ed_seeded_v1', '1' );
		return;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => 'Erectile Dysfunction',
			'post_name'    => 'erectile-dysfunction',
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
		'tx_hero_category'              => 'ERECTILE DYSFUNCTION',
		'tx_hero_heading_pre'          => 'Discreet',
		'tx_hero_heading_highlight'    => 'ED treatment',
		'tx_hero_heading_post'         => 'delivered to your door',
		'tx_hero_subheading'           => 'Prescription treatment for erectile dysfunction, reviewed by UK-licensed clinicians and dispatched in plain packaging within 24 hours.',
		'tx_hero_primary_cta_label'    => 'Start Consultation',
		'tx_hero_primary_cta_url'      => '/start-consultation/',
		'tx_hero_secondary_cta_label'  => 'Learn more',
		'tx_hero_secondary_cta_url'    => '#how-it-works',

		// B4 Treatment Meta
		'tx_meta_short_description'    => 'Prescription ED treatment, reviewed by a UK pharmacist before dispatch.',
		'tx_meta_legal_class'          => 'POM',
		'tx_meta_requires_consultation' => 1,
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
		array( 'icon' => 'lock',   'label' => 'Discreet packaging' ),
		array( 'icon' => 'truck',  'label' => 'Free next-day delivery' ),
	);
	if ( function_exists( 'update_field' ) ) {
		update_field( 'tx_hero_trust_items', $trust, $post_id );
	}

	$term = term_exists( 'erectile-dysfunction', 'treatment_category' );
	if ( ! $term ) {
		$term = wp_insert_term( 'Erectile Dysfunction', 'treatment_category', array( 'slug' => 'erectile-dysfunction' ) );
	}
	if ( ! is_wp_error( $term ) && ! empty( $term['term_id'] ) ) {
		wp_set_object_terms( $post_id, (int) $term['term_id'], 'treatment_category' );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'tx_meta_category', array( (int) $term['term_id'] ), $post_id );
		}
	}

	update_option( '_sp_ed_seeded_v1', '1' );
}
add_action( 'init', 'sp_seed_ed_treatment', 11 );
