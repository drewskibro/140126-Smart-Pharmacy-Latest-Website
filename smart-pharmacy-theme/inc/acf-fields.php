<?php
/**
 * ACF Field Group registrations for Smart Pharmacy.
 *
 * Field groups are letter-coded (A1, B1, C1, ...) following the convention
 * from Denton and Easy Pharmacy themes. Each group is registered via
 * acf_add_local_field_group() so it lives in code, not the database.
 *
 * Planned groups (populated in later stages):
 *   A1  Front page — hero section
 *   A2  Front page — treatment grid
 *   A3  Front page — testimonials
 *   A4  Front page — trust badges / stats
 *   B1  Treatment (CPT) — meta (price range, category, MHRA flags)
 *   C1  Treatment (CPT) — benefits repeater
 *   D1  Treatment (CPT) — FAQ repeater
 *   E1  Treatment (CPT) — related WooCommerce products (relationship)
 *   F1  Options — Branding (logo, primary/secondary colours)
 *   G1  Options — Contact (phone, email, pharmacy address, opening hours)
 *   H1  Options — Compliance (Superintendent Pharmacist, GPhC #, MHRA links)
 *   I1  Options — Social (platform URLs)
 *   J1  Options — Navigation (primary/footer menus)
 *
 * @package SmartPharmacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all Smart Pharmacy ACF field groups.
 * Guarded because ACF may not be active (theme must not fatal-error without it).
 */
function sp_register_acf_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Field groups will be registered here in Stage 2+.
	// Keeping this file wired now so the contract is in place.
}
add_action( 'acf/init', 'sp_register_acf_field_groups' );
