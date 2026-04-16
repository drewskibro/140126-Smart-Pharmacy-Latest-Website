<?php
/**
 * One-shot seeder for WooCommerce demo content.
 *
 * Creates the 4 product categories + 10 test products described in
 * docs/woocommerce-seeding.md.  Idempotent -- safe to run multiple
 * times: products are matched by SKU and skipped if they already
 * exist, categories matched by slug.
 *
 * Two ways to run:
 *
 *   1. WP-CLI (recommended; available on Kinsta via SSH):
 *
 *      cd /www/<your-site>/public
 *      wp eval-file wp-content/themes/smart-pharmacy-theme/../../../docs/seed-wc-products.php
 *
 *      ...or copy the file into the site root first:
 *
 *      wp eval-file seed-wc-products.php
 *
 *   2. "Code Snippets" plugin (no SSH access required):
 *
 *      - Install the free Code Snippets plugin
 *      - Snippets -> Add New -> paste the BODY of this file (skip the
 *        opening <?php tag)
 *      - Set "Run snippet only once"
 *      - Click "Save Changes and Activate"
 *      - Snippet runs once on activation, then auto-deactivates
 *
 * Output goes to STDOUT in WP-CLI; in Code Snippets, look at the
 * "Output" panel that appears after activation.
 *
 * NOTE on images: this script sideloads featured images from Unsplash
 * URLs.  Each sideload is an HTTP fetch + WP media-library import,
 * which takes 2-5 seconds per product.  The whole script runs in
 * ~30-60 seconds.  If your hosting has a short PHP timeout, prefer
 * WP-CLI which doesn't suffer from web-request timeouts.
 *
 * @package SmartPharmacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "This file must run inside WordPress (via WP-CLI or Code Snippets).\n" );
}

if ( ! class_exists( 'WC_Product_Simple' ) ) {
	echo "WooCommerce is not active. Activate WC and re-run.\n";
	return;
}

/* ---------------------------------------------------------------
 * 1. CATEGORIES
 * ------------------------------------------------------------- */
$sp_seed_categories = array(
	'pain-relief'       => array(
		'name'        => 'Pain Relief',
		'description' => 'Fast-acting OTC pain and fever relief, GPhC-approved.',
	),
	'vitamins'          => array(
		'name'        => 'Vitamins & Supplements',
		'description' => 'Daily wellness essentials and targeted supplements.',
	),
	'first-aid'         => array(
		'name'        => 'First Aid',
		'description' => 'Plasters, bandages, and home first-aid kits.',
	),
	'weight-management' => array(
		'name'        => 'Weight Management',
		'description' => 'GLP-1 treatments and weight-loss programmes (consultation required).',
	),
);

echo "\n--- Seeding categories ---\n";
foreach ( $sp_seed_categories as $sp_slug => $sp_cat ) {
	$sp_existing = get_term_by( 'slug', $sp_slug, 'product_cat' );
	if ( $sp_existing ) {
		printf( "skip   %-22s (already exists)\n", $sp_slug );
		continue;
	}
	$sp_result = wp_insert_term(
		$sp_cat['name'],
		'product_cat',
		array(
			'slug'        => $sp_slug,
			'description' => $sp_cat['description'],
		)
	);
	if ( is_wp_error( $sp_result ) ) {
		printf( "ERROR  %-22s : %s\n", $sp_slug, $sp_result->get_error_message() );
	} else {
		printf( "create %-22s (term_id %d)\n", $sp_slug, $sp_result['term_id'] );
	}
}

/* ---------------------------------------------------------------
 * 2. PRODUCTS
 *
 * Image URLs are Unsplash search picks chosen for demo neutrality.
 * Replace with licensed product photography post-meeting.
 * ------------------------------------------------------------- */
$sp_seed_products = array(

	/* GSL / OTC ----------------------------------------------------- */

	array(
		'sku'         => 'SP-PAIN-PARA-500-16',
		'name'        => 'Paracetamol 500mg (16 tablets)',
		'price'       => 2.99,
		'category'    => 'pain-relief',
		'short'       => 'Fast-acting pain and fever relief. UK-licensed generic.',
		'description' => 'Effective relief from headaches, toothache, period pain, cold and flu symptoms, and feverish conditions. Each tablet contains 500mg paracetamol. Adults and children 12+: 1-2 tablets up to 4 times in 24 hours.',
		'image'       => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=600&h=600&fit=crop',
	),
	array(
		'sku'         => 'SP-PAIN-IBU-200-16',
		'name'        => 'Ibuprofen 200mg (16 tablets)',
		'price'       => 3.49,
		'category'    => 'pain-relief',
		'short'       => 'Anti-inflammatory pain relief for headaches, muscular aches, and period pain.',
		'description' => 'Non-steroidal anti-inflammatory (NSAID) tablets containing 200mg ibuprofen. Effective for muscular aches, period pain, headache, dental pain, and reducing fever. Take with or after food. Adults and children 12+: 1-2 tablets up to 3 times daily.',
		'image'       => 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?w=600&h=600&fit=crop',
	),
	array(
		'sku'         => 'SP-VIT-D3-1000-60',
		'name'        => 'Vitamin D3 1000IU (60 capsules)',
		'price'       => 8.99,
		'category'    => 'vitamins',
		'short'       => 'Essential daily supplement. Supports bone, muscle, and immune health.',
		'description' => 'High-strength vitamin D3 (cholecalciferol) at 1000IU per capsule. NHS recommends UK adults consider a daily 10mcg (400IU) supplement during autumn and winter; this product provides 25mcg per capsule for those needing higher intake. 60-day supply. Suitable for vegetarians.',
		'image'       => 'https://images.unsplash.com/photo-1628771065518-0d82f1938462?w=600&h=600&fit=crop',
	),
	array(
		'sku'         => 'SP-VIT-C-1000-30',
		'name'        => 'Vitamin C 1000mg (30 effervescent tablets)',
		'price'       => 5.99,
		'category'    => 'vitamins',
		'short'       => 'High-strength immune support. Orange-flavoured fizzy tablets.',
		'description' => 'Effervescent tablets dissolving in water for fast absorption. Each tablet provides 1000mg vitamin C, supporting normal immune function and reducing tiredness. Orange flavour. 30-day supply when taken once daily.',
		'image'       => 'https://images.unsplash.com/photo-1610632380989-680fe40816c6?w=600&h=600&fit=crop',
	),
	array(
		'sku'         => 'SP-FA-KIT-STD',
		'name'        => 'Standard First Aid Kit',
		'price'       => 14.99,
		'category'    => 'first-aid',
		'short'       => 'Complete home first-aid kit: 87 pieces, GPhC-recommended contents.',
		'description' => 'Comprehensive first-aid kit suitable for home, car, or workplace. Contents include assorted plasters, sterile dressings, gauze swabs, conforming bandages, triangular bandages, safety pins, scissors, tweezers, antiseptic wipes, disposable gloves, and a first-aid guidance leaflet. BS 8599-1:2019 compliant.',
		'image'       => 'https://images.unsplash.com/photo-1603398938378-e54eab446dde?w=600&h=600&fit=crop',
	),
	array(
		'sku'         => 'SP-FA-PLAST-30',
		'name'        => 'Assorted Plasters (30-pack)',
		'price'       => 3.49,
		'category'    => 'first-aid',
		'short'       => 'Hypoallergenic mixed-size plasters for everyday cuts and grazes.',
		'description' => 'Latex-free hypoallergenic plasters in three useful sizes (small strip, large strip, knuckle). Breathable fabric, water-resistant adhesive. Suitable for sensitive skin. 30 plasters per pack.',
		'image'       => 'https://images.unsplash.com/photo-1603398938378-e54eab446dde?w=600&h=600&fit=crop',
	),

	/* POM placeholders (Stage 4c will gate these with consultation) -- */

	array(
		'sku'         => 'SP-WL-MOUN-2.5-4W',
		'name'        => 'Mounjaro 2.5mg (4-week supply)',
		'price'       => 149.00,
		'category'    => 'weight-management',
		'short'       => 'Starter dose of tirzepatide. Prescription-only — consultation required.',
		'description' => 'Mounjaro (tirzepatide) is a weekly injection used alongside diet and exercise to support weight management in adults with a BMI of 30+, or 27+ with weight-related health conditions. The 2.5mg starter dose is the standard initial titration step. Available after a free online consultation with our GPhC-registered prescriber.',
		'image'       => 'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=600&h=600&fit=crop',
	),
	array(
		'sku'         => 'SP-WL-MOUN-5-4W',
		'name'        => 'Mounjaro 5mg (4-week supply)',
		'price'       => 179.00,
		'category'    => 'weight-management',
		'short'       => 'Intermediate dose of tirzepatide. Prescription-only — consultation required.',
		'description' => 'The 5mg intermediate dose is the second titration step for patients tolerating the 2.5mg starter dose well. Continued under prescriber review. Available after a free online consultation with our GPhC-registered prescriber.',
		'image'       => 'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=600&h=600&fit=crop',
	),
	array(
		'sku'         => 'SP-WL-MOUN-7.5-4W',
		'name'        => 'Mounjaro 7.5mg (4-week supply)',
		'price'       => 209.00,
		'category'    => 'weight-management',
		'short'       => 'Higher dose of tirzepatide. Prescription-only — consultation required.',
		'description' => 'The 7.5mg dose is for patients who have tolerated lower doses and require additional weight management support. Continued under prescriber review with regular check-ins. Available after a free online consultation with our GPhC-registered prescriber.',
		'image'       => 'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=600&h=600&fit=crop',
	),
	array(
		'sku'         => 'SP-WL-MOUN-10-4W',
		'name'        => 'Mounjaro 10mg (4-week supply)',
		'price'       => 229.00,
		'category'    => 'weight-management',
		'short'       => 'Maximum standard dose of tirzepatide. Prescription-only — consultation required.',
		'description' => 'The 10mg dose is the maximum standard maintenance dose for tirzepatide weight management. Reserved for patients who have titrated through lower doses and require maximum standard dosing. Continued under prescriber review. Available after a free online consultation with our GPhC-registered prescriber.',
		'image'       => 'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=600&h=600&fit=crop',
	),
);

echo "\n--- Seeding products ---\n";

foreach ( $sp_seed_products as $sp_data ) {

	if ( wc_get_product_id_by_sku( $sp_data['sku'] ) ) {
		printf( "skip   %-22s (SKU already exists)\n", $sp_data['sku'] );
		continue;
	}

	$sp_product = new WC_Product_Simple();
	$sp_product->set_name( $sp_data['name'] );
	$sp_product->set_sku( $sp_data['sku'] );
	$sp_product->set_regular_price( (string) $sp_data['price'] );
	$sp_product->set_short_description( $sp_data['short'] );
	$sp_product->set_description( $sp_data['description'] );
	$sp_product->set_status( 'publish' );
	$sp_product->set_catalog_visibility( 'visible' );
	$sp_product->set_manage_stock( true );
	$sp_product->set_stock_quantity( 100 );
	$sp_product->set_stock_status( 'instock' );
	$sp_product->set_tax_status( 'none' );

	$sp_term = get_term_by( 'slug', $sp_data['category'], 'product_cat' );
	if ( $sp_term ) {
		$sp_product->set_category_ids( array( (int) $sp_term->term_id ) );
	}

	$sp_id = $sp_product->save();

	if ( ! $sp_id ) {
		printf( "ERROR  %-22s : product save failed\n", $sp_data['sku'] );
		continue;
	}

	// Sideload featured image. Requires the WP media-handling
	// includes which aren't loaded in non-admin contexts.
	if ( ! empty( $sp_data['image'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$sp_attach_id = media_sideload_image( $sp_data['image'], $sp_id, $sp_data['name'], 'id' );
		if ( ! is_wp_error( $sp_attach_id ) ) {
			set_post_thumbnail( $sp_id, $sp_attach_id );
			printf( "create %-22s (ID %d, image %d)\n", $sp_data['sku'], $sp_id, $sp_attach_id );
		} else {
			printf( "create %-22s (ID %d, IMAGE FAILED: %s)\n", $sp_data['sku'], $sp_id, $sp_attach_id->get_error_message() );
		}
	} else {
		printf( "create %-22s (ID %d, no image)\n", $sp_data['sku'], $sp_id );
	}
}

echo "\nDone. Visit /shop/ to see the seeded catalogue.\n";
echo "If something looks wrong, you can re-run this script -- it skips existing products by SKU.\n\n";
