<?php
/**
 * One-shot seeder for the homepage A2 — Popular Treatments cards.
 *
 * The card grid ([popular-treatments.php]) renders the `pt_cards` ACF
 * repeater (Theme Settings → Homepage → A2). While that repeater is
 * empty the template shows 5 HARDCODED placeholder cards with a teal
 * gradient and no image — and because they're hardcoded, there's no row
 * in admin to attach an image to.
 *
 * This seeder writes those same 5 cards into the repeater ONCE (title +
 * URL + CTA filled, image left blank), so the client only has to open
 * each row and upload an image. URLs match the seeded Treatment pages.
 *
 * Idempotency: guarded by `_sp_pt_cards_seeded_v1`. Respects existing
 * content — if the client has already added any cards, it never
 * overwrites them, it just marks itself done.
 *
 * @package SmartPharmacy
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pre-fill the Popular Treatments repeater if it's still empty.
 */
function sp_seed_popular_treatments_cards() {
	if ( '1' === (string) get_option( '_sp_pt_cards_seeded_v1', '' ) ) {
		return;
	}

	// ACF must be loaded (options page + field group registered on acf/init).
	if ( ! function_exists( 'get_field' ) || ! function_exists( 'update_field' ) ) {
		return;
	}

	// Respect anything the client has already entered — never clobber it.
	$existing = get_field( 'pt_cards', 'option' );
	if ( ! empty( $existing ) && is_array( $existing ) ) {
		update_option( '_sp_pt_cards_seeded_v1', '1' );
		return;
	}

	// URLs mirror the seeded Treatment CPT slugs. Image left blank so the
	// client just uploads one per row (empty = teal gradient placeholder).
	$cards = array(
		array( 'image' => '', 'title' => 'Weight loss',           'url' => '/treatments/weight-loss/',           'cta' => 'View Treatment' ),
		array( 'image' => '', 'title' => 'Hair loss',             'url' => '/treatments/hair-loss/',             'cta' => 'View Treatment' ),
		array( 'image' => '', 'title' => 'ED',                    'url' => '/treatments/erectile-dysfunction/',  'cta' => 'View Treatment' ),
		array( 'image' => '', 'title' => 'Acne',                  'url' => '/treatments/acne/',                  'cta' => 'View Treatment' ),
		array( 'image' => '', 'title' => 'Premature Ejaculation', 'url' => '/treatments/premature-ejaculation/', 'cta' => 'View Treatment' ),
	);

	update_field( 'field_sp_pt_cards', $cards, 'option' );
	update_option( '_sp_pt_cards_seeded_v1', '1' );
}
// After acf/init (fields register at init priority 5); matches the
// Treatment seeders which hook init:11.
add_action( 'init', 'sp_seed_popular_treatments_cards', 12 );
