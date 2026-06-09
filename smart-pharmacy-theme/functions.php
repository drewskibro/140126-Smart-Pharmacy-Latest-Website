<?php
/**
 * Smart Pharmacy theme functions.
 *
 * Architecture mirrors Denton / Easy Pharmacy (PharmoDigital pattern):
 *   - ACF-powered content with a three-tier field fallback (see inc/helpers.php)
 *   - Theme Settings options pages for global content (see inc/acf-options.php)
 *   - Field groups declared in code (see inc/acf-fields.php)
 *
 * Divergences from other themes:
 *   - Tailwind CSS is compiled in CI (see .github/workflows/deploy-smart-pharmacy-to-kinsta.yml)
 *   - WooCommerce is supported (first theme in the suite with WC)
 *
 * @package SmartPharmacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SMART_PHARMACY_VERSION', '0.1.0' );
define( 'SMART_PHARMACY_DIR', get_template_directory() );
define( 'SMART_PHARMACY_URI', get_template_directory_uri() );

require_once SMART_PHARMACY_DIR . '/inc/helpers.php';
require_once SMART_PHARMACY_DIR . '/inc/acf-options.php';
require_once SMART_PHARMACY_DIR . '/inc/acf-fields.php';
require_once SMART_PHARMACY_DIR . '/inc/woocommerce.php';
require_once SMART_PHARMACY_DIR . '/inc/seeders/mens-health.php';
require_once SMART_PHARMACY_DIR . '/inc/seeders/womens-health.php';

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
 * Cache-bust a theme asset using its modification time.
 *
 * Per PharmoDigital convention: version strings are derived from the CSS/JS
 * file's mtime, NOT functions.php. This guarantees a cache bust on every real
 * asset change without manually bumping SMART_PHARMACY_VERSION.
 *
 * @param string $relative_path Path from the theme root, e.g. 'assets/css/tailwind.css'.
 * @return string Version string.
 */
function sp_asset_version( $relative_path ) {
	$full = SMART_PHARMACY_DIR . '/' . ltrim( $relative_path, '/' );
	if ( file_exists( $full ) ) {
		return (string) filemtime( $full );
	}
	return SMART_PHARMACY_VERSION;
}

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

	// Compiled Tailwind output (built in CI by GitHub Actions).
	wp_enqueue_style(
		'smart-pharmacy-tailwind',
		SMART_PHARMACY_URI . '/assets/css/tailwind.css',
		array(),
		sp_asset_version( 'assets/css/tailwind.css' )
	);

	// Custom CSS: animations, overrides, scroll progress.
	wp_enqueue_style(
		'smart-pharmacy-styles',
		SMART_PHARMACY_URI . '/assets/css/styles.css',
		array( 'smart-pharmacy-tailwind' ),
		sp_asset_version( 'assets/css/styles.css' )
	);

	// Shared JS: scroll progress bar, mobile menu toggle.
	wp_enqueue_script(
		'smart-pharmacy-main',
		SMART_PHARMACY_URI . '/assets/js/main.js',
		array(),
		sp_asset_version( 'assets/js/main.js' ),
		true
	);

	// Search dropdown and rotating placeholder (scoped to the hero search input).
	wp_enqueue_script(
		'smart-pharmacy-search',
		SMART_PHARMACY_URI . '/assets/js/search-animation.js',
		array(),
		sp_asset_version( 'assets/js/search-animation.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'smart_pharmacy_enqueue_assets' );

/**
 * Register the Treatment custom post type.
 *
 * Treatments are SEO/educational landing pages (e.g. /treatments/weight-loss/)
 * that link out to one or more WooCommerce products (the actual SKUs) via a
 * related-products ACF relationship field added in Stage 2+.
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

/**
 * Register the `treatment_category` hierarchical taxonomy.
 *
 * Category-like taxonomy attached to Treatment posts. Lets editors group
 * treatments (e.g. Weight Management > GLP-1, Men's Health > ED) and
 * drives archive URLs at /treatment-category/{slug}/ for SEO and
 * related-treatment widgets.
 *
 * Hierarchical so parent/child terms work (e.g. Men's Health > ED).
 * Exposed to REST API so the block editor / WP admin list view / any
 * future headless consumers can read it.
 */
function smart_pharmacy_register_treatment_taxonomy() {
	$labels = array(
		'name'              => _x( 'Treatment Categories', 'taxonomy general name', 'smart-pharmacy' ),
		'singular_name'     => _x( 'Treatment Category', 'taxonomy singular name', 'smart-pharmacy' ),
		'search_items'      => __( 'Search Categories', 'smart-pharmacy' ),
		'all_items'         => __( 'All Categories', 'smart-pharmacy' ),
		'parent_item'       => __( 'Parent Category', 'smart-pharmacy' ),
		'parent_item_colon' => __( 'Parent Category:', 'smart-pharmacy' ),
		'edit_item'         => __( 'Edit Category', 'smart-pharmacy' ),
		'update_item'       => __( 'Update Category', 'smart-pharmacy' ),
		'add_new_item'      => __( 'Add New Category', 'smart-pharmacy' ),
		'new_item_name'     => __( 'New Category Name', 'smart-pharmacy' ),
		'menu_name'         => __( 'Categories', 'smart-pharmacy' ),
	);

	register_taxonomy(
		'treatment_category',
		array( 'treatment' ),
		array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'       => 'treatment-category',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'smart_pharmacy_register_treatment_taxonomy' );

/**
 * Disable the Gutenberg block editor for `page` and `treatment` post types.
 *
 * These post types are driven entirely by ACF field groups (positioned
 * `acf_after_title` so editors see them immediately). Gutenberg would just
 * add visual clutter and isn't used for content. The classic editor also
 * makes the `acf_after_title` ACF position work properly -- with Gutenberg
 * it silently falls back to `normal` (below the editor).
 *
 * Matches the editing pattern used in Denton / Easy Pharmacy.
 *
 * @param bool   $use_block_editor Whether the post type can use the block editor.
 * @param string $post_type        The post type being checked.
 * @return bool
 */
function smart_pharmacy_disable_gutenberg( $use_block_editor, $post_type ) {
	if ( in_array( $post_type, array( 'page', 'treatment' ), true ) ) {
		return false;
	}
	return $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'smart_pharmacy_disable_gutenberg', 10, 2 );

/**
 * Admin notice if ACF Pro is not active.
 *
 * The theme degrades gracefully (sp_field() returns the hardcoded default),
 * but most of the site will be blank without ACF content, so we surface it.
 */
function smart_pharmacy_admin_notice_acf() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>Smart Pharmacy:</strong> ';
	esc_html_e( 'ACF Pro is required for theme content to render. Please install and activate Advanced Custom Fields Pro.', 'smart-pharmacy' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'smart_pharmacy_admin_notice_acf' );
