<?php
/**
 * Smart Pharmacy theme functions.
 *
 * @package SmartPharmacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SMART_PHARMACY_VERSION', '0.1.0' );
define( 'SMART_PHARMACY_DIR', get_template_directory() );
define( 'SMART_PHARMACY_URI', get_template_directory_uri() );

/**
 * Theme setup: supports, menus, textdomain.
 */
function smart_pharmacy_setup() {
	load_theme_textdomain( 'smart-pharmacy', SMART_PHARMACY_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'smart-pharmacy' ),
			'footer'  => __( 'Footer Menu', 'smart-pharmacy' ),
		)
	);
}
add_action( 'after_setup_theme', 'smart_pharmacy_setup' );

/**
 * Declare WooCommerce High-Performance Order Storage (HPOS) compatibility.
 */
function smart_pharmacy_declare_wc_compatibility() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
	}
}
add_action( 'before_woocommerce_init', 'smart_pharmacy_declare_wc_compatibility' );

/**
 * Enqueue front-end styles and scripts.
 */
function smart_pharmacy_enqueue_assets() {
	// Google Fonts: Instrument Sans + Playfair Display italic.
	wp_enqueue_style(
		'smart-pharmacy-fonts',
		'https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;900&family=Playfair+Display:ital,wght@0,400;1,400&display=swap',
		array(),
		null
	);

	// Compiled Tailwind output.
	wp_enqueue_style(
		'smart-pharmacy-tailwind',
		SMART_PHARMACY_URI . '/assets/css/tailwind.css',
		array(),
		SMART_PHARMACY_VERSION
	);

	// Custom CSS: animations, overrides, scroll progress, etc.
	wp_enqueue_style(
		'smart-pharmacy-styles',
		SMART_PHARMACY_URI . '/assets/css/styles.css',
		array( 'smart-pharmacy-tailwind' ),
		SMART_PHARMACY_VERSION
	);

	// Search dropdown and rotating placeholder.
	wp_enqueue_script(
		'smart-pharmacy-search',
		SMART_PHARMACY_URI . '/assets/js/search-animation.js',
		array(),
		SMART_PHARMACY_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'smart_pharmacy_enqueue_assets' );

/**
 * Register the Treatment custom post type.
 *
 * Treatment posts are SEO/educational landing pages (e.g. /treatments/weight-loss/).
 * They link out to one or more WooCommerce products (the actual SKUs) via a
 * related-products meta field added in a later stage.
 */
function smart_pharmacy_register_treatment_cpt() {
	$labels = array(
		'name'               => _x( 'Treatments', 'post type general name', 'smart-pharmacy' ),
		'singular_name'      => _x( 'Treatment', 'post type singular name', 'smart-pharmacy' ),
		'menu_name'          => _x( 'Treatments', 'admin menu', 'smart-pharmacy' ),
		'name_admin_bar'     => _x( 'Treatment', 'add new on admin bar', 'smart-pharmacy' ),
		'add_new'            => _x( 'Add New', 'treatment', 'smart-pharmacy' ),
		'add_new_item'       => __( 'Add New Treatment', 'smart-pharmacy' ),
		'new_item'           => __( 'New Treatment', 'smart-pharmacy' ),
		'edit_item'          => __( 'Edit Treatment', 'smart-pharmacy' ),
		'view_item'          => __( 'View Treatment', 'smart-pharmacy' ),
		'all_items'          => __( 'All Treatments', 'smart-pharmacy' ),
		'search_items'       => __( 'Search Treatments', 'smart-pharmacy' ),
		'not_found'          => __( 'No treatments found.', 'smart-pharmacy' ),
		'not_found_in_trash' => __( 'No treatments found in Trash.', 'smart-pharmacy' ),
	);

	register_post_type(
		'treatment',
		array(
			'labels'        => $labels,
			'public'        => true,
			'show_in_rest'  => true,
			'has_archive'   => 'treatments',
			'menu_icon'     => 'dashicons-heart',
			'menu_position' => 20,
			'rewrite'       => array(
				'slug'       => 'treatments',
				'with_front' => false,
			),
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields' ),
		)
	);
}
add_action( 'init', 'smart_pharmacy_register_treatment_cpt' );
