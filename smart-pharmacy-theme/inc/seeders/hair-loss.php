<?php
/**
 * One-shot Hair Loss treatment landing page seeder.
 *
 * Same pattern as the Men's / Women's Health seeders: creates a
 * Treatment CPT post with slug `hair-loss` on the next admin page load
 * after deploy, populated with brand-appropriate B1 (Hero) + B4
 * (Treatment Meta) ACF defaults so the page is presentable end-to-end.
 * Remaining field groups fall back to their acf-fields.php defaults; the
 * client swaps the section copy in admin.
 *
 * Copy is deliberately condition-level (no specific POM names in the
 * hero) — final wording is a pharmacist / MHRA-CAP review point.
 *
 * Idempotency: guarded by `_sp_hair_loss_seeded_v1`; respects deletion.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

/**
 * Create the Hair Loss treatment post if missing.
 */
function sp_seed_hair_loss_treatment() {
	if ( '1' === (string) get_option( '_sp_hair_loss_seeded_v1', '' ) ) {
		return;
	}

	$existing = get_page_by_path( 'hair-loss', OBJECT, 'treatment' );
	if ( $existing ) {
		update_option( '_sp_hair_loss_seeded_v1', '1' );
		return;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => 'Hair Loss',
			'post_name'    => 'hair-loss',
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
		'tx_hero_category'              => 'HAIR LOSS',
		'tx_hero_heading_pre'          => 'Proven',
		'tx_hero_heading_highlight'    => 'hair loss',
		'tx_hero_heading_post'         => 'treatment, delivered discreetly',
		'tx_hero_subheading'           => 'Prescription treatment for male and female hair loss, reviewed by UK-licensed clinicians and dispatched discreetly within 24 hours.',
		'tx_hero_primary_cta_label'    => 'Start Consultation',
		'tx_hero_primary_cta_url'      => '/start-consultation/',
		'tx_hero_secondary_cta_label'  => 'Learn more',
		'tx_hero_secondary_cta_url'    => '#how-it-works',

		// B4 Treatment Meta
		'tx_meta_short_description'    => 'Prescription hair loss treatment, reviewed by a UK pharmacist before dispatch.',
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

	$term = term_exists( 'hair-loss', 'treatment_category' );
	if ( ! $term ) {
		$term = wp_insert_term( 'Hair Loss', 'treatment_category', array( 'slug' => 'hair-loss' ) );
	}
	if ( ! is_wp_error( $term ) && ! empty( $term['term_id'] ) ) {
		wp_set_object_terms( $post_id, (int) $term['term_id'], 'treatment_category' );
		if ( function_exists( 'update_field' ) ) {
			update_field( 'tx_meta_category', array( (int) $term['term_id'] ), $post_id );
		}
	}

	update_option( '_sp_hair_loss_seeded_v1', '1' );
}
add_action( 'init', 'sp_seed_hair_loss_treatment', 11 );
