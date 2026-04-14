<?php
/**
 * ACF Options pages for Smart Pharmacy.
 *
 * Registers the "Theme Settings" menu in WP admin with sub-pages for
 * Branding, Contact, Compliance, Social, and Navigation — matching the
 * pattern established in Denton and Easy Pharmacy themes.
 *
 * Requires ACF Pro (free ACF does not ship options pages).
 *
 * @package SmartPharmacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register options pages. Guarded because ACF Pro may not be active.
 */
function sp_register_acf_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Smart Pharmacy Settings', 'smart-pharmacy' ),
			'menu_title' => __( 'Theme Settings', 'smart-pharmacy' ),
			'menu_slug'  => 'sp-theme-settings',
			'capability' => 'edit_posts',
			'icon_url'   => 'dashicons-admin-settings',
			'position'   => 21,
			'redirect'   => true,
		)
	);

	$subpages = array(
		array(
			'title' => __( 'Homepage', 'smart-pharmacy' ),
			'slug'  => 'sp-homepage',
		),
		array(
			'title' => __( 'Branding', 'smart-pharmacy' ),
			'slug'  => 'sp-branding',
		),
		array(
			'title' => __( 'Contact', 'smart-pharmacy' ),
			'slug'  => 'sp-contact',
		),
		array(
			'title' => __( 'Compliance', 'smart-pharmacy' ),
			'slug'  => 'sp-compliance',
		),
		array(
			'title' => __( 'Social', 'smart-pharmacy' ),
			'slug'  => 'sp-social',
		),
		array(
			'title' => __( 'Navigation', 'smart-pharmacy' ),
			'slug'  => 'sp-navigation',
		),
	);

	foreach ( $subpages as $sub ) {
		acf_add_options_sub_page(
			array(
				'page_title'  => $sub['title'],
				'menu_title'  => $sub['title'],
				'menu_slug'   => $sub['slug'],
				'parent_slug' => 'sp-theme-settings',
				'capability'  => 'edit_posts',
			)
		);
	}
}
add_action( 'acf/init', 'sp_register_acf_options_pages' );
