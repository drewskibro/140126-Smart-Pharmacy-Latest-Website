<?php
/**
 * ACF Field Group registrations for Smart Pharmacy.
 *
 * Field groups are letter-coded (F1, G1, H1, ...) following the PharmoDigital
 * convention. Each group is registered via acf_add_local_field_group() so it
 * lives in code, not the database.
 *
 * Registered so far:
 *   A1  Page (front page) — Hero section
 *   A2  Page (front page) — Popular Treatments carousel
 *   A3  Page (front page) — Featured Treatment showcase
 *   A4  Page (front page) — How It Works
 *   A5  Page (front page) — NHS Prescription
 *   A6  Page (front page) — Safety / GPhC
 *   A7  Page (front page) — Most Trusted Treatments
 *   A8  Page (front page) — Shop Bestsellers (static; real WC loop in Stage 4)
 *   A9  Page (front page) — Testimonials
 *   A10 Page (front page) — FAQ
 *   B1  Post type (treatment) — Treatment Hero
 *   B2  Post type (treatment) — Treatment How It Works
 *   B3  Post type (treatment) — Treatment Options / Pricing (static; WC in Stage 4)
 *   B4  Post type (treatment) — Treatment Meta (identity, card data, UK compliance, category taxonomy)
 *   C1  Post type (treatment) — What's Included / Benefits
 *   C2  Post type (treatment) — Results / Testimonials
 *   C3  Post type (treatment) — Eligibility (Stage 3b stub UI; Stage 5 wires real calculator)
 *   D1  Post type (treatment) — FAQ
 *   D2  Post type (treatment) — Final CTA
 *   E1  Post type (treatment) — Related WooCommerce products (drives POM gating)
 *   F1  Options — Branding (logo, footer tagline, payment methods)
 *   G1  Options — Navigation (primary menu, footer link columns, search, NHS button)
 *   H1  Options — Contact (trading address, registered address)
 *   I1  Options — Compliance (GPhC number)
 *   J1  Options — Social (platform URLs)
 *
 * @package SmartPharmacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register all Smart Pharmacy ACF field groups.
 * Guarded because ACF may not be active.
 */
function sp_register_acf_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$sp_icon_choices = function_exists( 'sp_icon_choices' ) ? sp_icon_choices() : array();

	/* ---------------------------------------------------------------
	 * A1 — Homepage Hero (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a1_hero',
			'title'    => 'A1 — Homepage Hero',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'fields'   => array(

				array(
					'key'     => 'field_sp_hero_tab_rating',
					'label'   => 'Rating badge',
					'type'    => 'tab',
				),
				array(
					'key'           => 'field_sp_hero_rating_enabled',
					'label'         => 'Show rating badge',
					'name'          => 'hero_rating_enabled',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
				),
				array(
					'key'           => 'field_sp_hero_rating_stars',
					'label'         => 'Filled stars (1–5)',
					'name'          => 'hero_rating_stars',
					'type'          => 'number',
					'min'           => 1,
					'max'           => 5,
					'default_value' => 5,
				),
				array(
					'key'           => 'field_sp_hero_rating_score',
					'label'         => 'Score text',
					'name'          => 'hero_rating_score',
					'type'          => 'text',
					'default_value' => '4.8/5',
				),
				array(
					'key'           => 'field_sp_hero_rating_count',
					'label'         => 'Review count (animated)',
					'name'          => 'hero_rating_count',
					'type'          => 'number',
					'default_value' => 10392,
				),
				array(
					'key'           => 'field_sp_hero_rating_label',
					'label'         => 'Label after count',
					'name'          => 'hero_rating_label',
					'type'          => 'text',
					'default_value' => 'reviews',
				),

				array(
					'key'   => 'field_sp_hero_tab_headline',
					'label' => 'Headline & search',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_sp_hero_heading_1',
					'label'         => 'Heading line 1 (black)',
					'name'          => 'hero_heading_1',
					'type'          => 'text',
					'default_value' => 'Prescriptions sorted.',
				),
				array(
					'key'           => 'field_sp_hero_heading_2',
					'label'         => 'Heading line 2 (teal)',
					'name'          => 'hero_heading_2',
					'type'          => 'text',
					'default_value' => 'Privacy protected. Delivered.',
				),
				array(
					'key'           => 'field_sp_hero_subheading',
					'label'         => 'Subheading',
					'name'          => 'hero_subheading',
					'type'          => 'textarea',
					'rows'          => 3,
					'default_value' => 'Expert healthcare on your terms. NHS-approved, prescription-perfect, delivered tomorrow.',
				),
				array(
					'key'           => 'field_sp_hero_search_placeholder',
					'label'         => 'Hero search default placeholder',
					'name'          => 'hero_search_placeholder',
					'type'          => 'text',
					'instructions'  => 'The hero search also cycles through rotating placeholders from search-animation.js.',
					'default_value' => 'Search for trending treatments, medications or health concerns...',
				),

				array(
					'key'   => 'field_sp_hero_tab_trust',
					'label' => 'Trust badges',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_sp_hero_trust_badges',
					'label'        => 'Trust badges',
					'name'         => 'hero_trust_badges',
					'type'         => 'repeater',
					'button_label' => 'Add badge',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 6,
					'sub_fields'   => array(
						array(
							'key'           => 'field_sp_hero_trust_icon',
							'label'         => 'Icon',
							'name'          => 'icon',
							'type'          => 'select',
							'choices'       => $sp_icon_choices,
							'default_value' => 'check',
							'ui'            => 1,
						),
						array(
							'key'   => 'field_sp_hero_trust_label',
							'label' => 'Label',
							'name'  => 'label',
							'type'  => 'text',
						),
					),
				),

				array(
					'key'   => 'field_sp_hero_tab_pills',
					'label' => 'Treatment pills',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_sp_hero_pills',
					'label'        => 'Treatment pills',
					'name'         => 'hero_pills',
					'type'         => 'repeater',
					'button_label' => 'Add pill',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 8,
					'sub_fields'   => array(
						array(
							'key'     => 'field_sp_hero_pill_icon',
							'label'   => 'Icon',
							'name'    => 'icon',
							'type'    => 'select',
							'choices' => $sp_icon_choices,
							'ui'      => 1,
						),
						array(
							'key'   => 'field_sp_hero_pill_label',
							'label' => 'Label',
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_sp_hero_pill_url',
							'label' => 'URL',
							'name'  => 'url',
							'type'  => 'url',
						),
					),
				),

				array(
					'key'   => 'field_sp_hero_tab_info',
					'label' => 'Right-column info cards',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_sp_hero_info_cards',
					'label'        => 'Info cards (right column)',
					'name'         => 'hero_info_cards',
					'type'         => 'repeater',
					'button_label' => 'Add card',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 5,
					'sub_fields'   => array(
						array(
							'key'           => 'field_sp_hero_info_image',
							'label'         => 'Icon image (optional)',
							'name'          => 'image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'thumbnail',
							'instructions'  => 'If empty, the icon-key below is used.',
						),
						array(
							'key'     => 'field_sp_hero_info_icon',
							'label'   => 'Icon (fallback)',
							'name'    => 'icon',
							'type'    => 'select',
							'choices' => $sp_icon_choices,
							'ui'      => 1,
						),
						array(
							'key'   => 'field_sp_hero_info_title',
							'label' => 'Title',
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_sp_hero_info_body',
							'label' => 'Body',
							'name'  => 'body',
							'type'  => 'text',
						),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A2 — Popular Treatments Carousel (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a2_popular_treatments',
			'title'    => 'A2 — Popular Treatments',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'fields'   => array(
				array(
					'key'           => 'field_sp_pt_enabled',
					'label'         => 'Show section',
					'name'          => 'pt_enabled',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
				),
				array(
					'key'           => 'field_sp_pt_eyebrow_prefix',
					'label'         => 'Eyebrow — prefix',
					'name'          => 'pt_eyebrow_prefix',
					'type'          => 'text',
					'default_value' => 'Trusted by',
				),
				array(
					'key'           => 'field_sp_pt_eyebrow_count',
					'label'         => 'Eyebrow — patient count (animated)',
					'name'          => 'pt_eyebrow_count',
					'type'          => 'number',
					'default_value' => 50000,
				),
				array(
					'key'           => 'field_sp_pt_eyebrow_suffix',
					'label'         => 'Eyebrow — suffix',
					'name'          => 'pt_eyebrow_suffix',
					'type'          => 'text',
					'default_value' => '+ patients',
				),
				array(
					'key'           => 'field_sp_pt_heading',
					'label'         => 'Heading',
					'name'          => 'pt_heading',
					'type'          => 'text',
					'default_value' => 'Our most popular treatments',
				),
				array(
					'key'          => 'field_sp_pt_cards',
					'label'        => 'Treatment cards',
					'name'         => 'pt_cards',
					'type'         => 'repeater',
					'button_label' => 'Add card',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 10,
					'instructions' => 'Cards render in a 5-column grid on desktop and 2-column on mobile. 5 cards is the design default.',
					'sub_fields'   => array(
						array(
							'key'           => 'field_sp_pt_card_image',
							'label'         => 'Image',
							'name'          => 'image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
						),
						array(
							'key'   => 'field_sp_pt_card_title',
							'label' => 'Title',
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_sp_pt_card_url',
							'label' => 'Link URL',
							'name'  => 'url',
							'type'  => 'url',
						),
						array(
							'key'           => 'field_sp_pt_card_cta',
							'label'         => 'Hover CTA label',
							'name'          => 'cta',
							'type'          => 'text',
							'default_value' => 'View Treatment',
						),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A3 — Featured Treatment Showcase (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a3_featured',
			'title'    => 'A3 — Featured Treatment',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_ft_enabled', 'label' => 'Show section', 'name' => 'ft_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_ft_badge_label', 'label' => 'Badge label', 'name' => 'ft_badge_label', 'type' => 'text', 'default_value' => 'Medical Weight Loss' ),
				array( 'key' => 'field_sp_ft_badge_icon', 'label' => 'Badge icon', 'name' => 'ft_badge_icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'default_value' => 'person', 'ui' => 1 ),
				array( 'key' => 'field_sp_ft_heading_pre', 'label' => 'Heading — start', 'name' => 'ft_heading_pre', 'type' => 'text', 'default_value' => 'Transform your life with' ),
				array( 'key' => 'field_sp_ft_heading_highlight', 'label' => 'Heading — highlighted (teal)', 'name' => 'ft_heading_highlight', 'type' => 'text', 'default_value' => 'prescription weight loss' ),
				array( 'key' => 'field_sp_ft_subheading', 'label' => 'Subheading', 'name' => 'ft_subheading', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Clinically-proven treatments delivered discreetly to your door. Expert medical support every step of the way.' ),
				array(
					'key'          => 'field_sp_ft_stats',
					'label'        => 'Stats (max 3)',
					'name'         => 'ft_stats',
					'type'         => 'repeater',
					'button_label' => 'Add stat',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 3,
					'sub_fields'   => array(
						array( 'key' => 'field_sp_ft_stat_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text', 'instructions' => 'Use numeric-only values for animated counters, or any text (e.g. "4.9★") for static display.' ),
						array( 'key' => 'field_sp_ft_stat_animated', 'label' => 'Animate as counter?', 'name' => 'animated', 'type' => 'true_false', 'ui' => 1 ),
						array( 'key' => 'field_sp_ft_stat_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
					),
				),
				array( 'key' => 'field_sp_ft_cta_label', 'label' => 'CTA label', 'name' => 'ft_cta_label', 'type' => 'text', 'default_value' => 'Start Your Journey' ),
				array( 'key' => 'field_sp_ft_cta_url', 'label' => 'CTA URL', 'name' => 'ft_cta_url', 'type' => 'url', 'default_value' => '/treatments/weight-loss/' ),
				array( 'key' => 'field_sp_ft_testimonial_quote', 'label' => 'Testimonial quote', 'name' => 'ft_testimonial_quote', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'The online consultation was seamless, medication arrived next day, and I\'ve lost 32 pounds in 4 months. This service changed my life.' ),
				array( 'key' => 'field_sp_ft_testimonial_author', 'label' => 'Testimonial author', 'name' => 'ft_testimonial_author', 'type' => 'text', 'default_value' => 'Sarah M.' ),
				array( 'key' => 'field_sp_ft_testimonial_meta', 'label' => 'Testimonial author meta (e.g. "Lost 32 lbs")', 'name' => 'ft_testimonial_meta', 'type' => 'text', 'default_value' => 'Lost 32 lbs' ),
				array( 'key' => 'field_sp_ft_testimonial_age', 'label' => 'Testimonial age (e.g. "4 months ago")', 'name' => 'ft_testimonial_age', 'type' => 'text', 'default_value' => '4 months ago' ),
				array( 'key' => 'field_sp_ft_image_main', 'label' => 'Main large image', 'name' => 'ft_image_main', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
				array( 'key' => 'field_sp_ft_image_tr', 'label' => 'Top-right image', 'name' => 'ft_image_tr', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ),
				array( 'key' => 'field_sp_ft_image_bl', 'label' => 'Bottom-left image', 'name' => 'ft_image_bl', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ),
				array( 'key' => 'field_sp_ft_image_br', 'label' => 'Bottom-right accent image', 'name' => 'ft_image_br', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ),
				array( 'key' => 'field_sp_ft_main_badge_value', 'label' => 'Main image badge — value', 'name' => 'ft_main_badge_value', 'type' => 'text', 'default_value' => '32' ),
				array( 'key' => 'field_sp_ft_main_badge_label', 'label' => 'Main image badge — label', 'name' => 'ft_main_badge_label', 'type' => 'text', 'default_value' => 'lbs Lost' ),
				array( 'key' => 'field_sp_ft_corner_badge_value', 'label' => 'Bottom-right image badge — value', 'name' => 'ft_corner_badge_value', 'type' => 'text', 'default_value' => '-45 lbs' ),
				array( 'key' => 'field_sp_ft_floating_value', 'label' => 'Floating badge — value', 'name' => 'ft_floating_value', 'type' => 'text', 'default_value' => '84' ),
				array( 'key' => 'field_sp_ft_floating_label', 'label' => 'Floating badge — label', 'name' => 'ft_floating_label', 'type' => 'text', 'default_value' => '% Success Rate' ),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A4 — How It Works (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a4_how_it_works',
			'title'    => 'A4 — How It Works',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_hw_enabled', 'label' => 'Show section', 'name' => 'hw_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_hw_badge_label', 'label' => 'Badge label', 'name' => 'hw_badge_label', 'type' => 'text', 'default_value' => 'Fast & Simple Process' ),
				array( 'key' => 'field_sp_hw_heading_pre', 'label' => 'Heading — start', 'name' => 'hw_heading_pre', 'type' => 'text', 'default_value' => 'How it' ),
				array( 'key' => 'field_sp_hw_heading_highlight', 'label' => 'Heading — highlighted', 'name' => 'hw_heading_highlight', 'type' => 'text', 'default_value' => 'Works' ),
				array( 'key' => 'field_sp_hw_subheading', 'label' => 'Subheading', 'name' => 'hw_subheading', 'type' => 'text', 'default_value' => 'Three simple ways to access expert healthcare on your terms' ),
				array(
					'key'          => 'field_sp_hw_cards',
					'label'        => 'Service cards (3 ideal)',
					'name'         => 'hw_cards',
					'type'         => 'repeater',
					'button_label' => 'Add card',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 4,
					'sub_fields'   => array(
						array( 'key' => 'field_sp_hw_card_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'ui' => 1 ),
						array( 'key' => 'field_sp_hw_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_hw_card_subtitle', 'label' => 'Subtitle', 'name' => 'subtitle', 'type' => 'text' ),
						array( 'key' => 'field_sp_hw_card_tagline', 'label' => 'Tagline (teal)', 'name' => 'tagline', 'type' => 'text' ),
						array( 'key' => 'field_sp_hw_card_cta', 'label' => 'Link label', 'name' => 'cta', 'type' => 'text', 'default_value' => 'Get Started' ),
						array( 'key' => 'field_sp_hw_card_url', 'label' => 'Link URL', 'name' => 'url', 'type' => 'url' ),
						array( 'key' => 'field_sp_hw_card_featured', 'label' => 'Featured (Most Popular border)', 'name' => 'featured', 'type' => 'true_false', 'ui' => 1 ),
						array( 'key' => 'field_sp_hw_card_featured_label', 'label' => 'Featured badge text', 'name' => 'featured_label', 'type' => 'text', 'default_value' => 'Most Popular' ),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A5 — NHS Prescription (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a5_nhs',
			'title'    => 'A5 — NHS Prescription',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_nhs_enabled', 'label' => 'Show section', 'name' => 'nhs_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_nhs_logo', 'label' => 'NHS logo image', 'name' => 'nhs_logo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
				array( 'key' => 'field_sp_nhs_heading_pre', 'label' => 'Heading — start', 'name' => 'nhs_heading_pre', 'type' => 'text', 'default_value' => 'Order your' ),
				array( 'key' => 'field_sp_nhs_heading_highlight', 'label' => 'Heading — highlighted', 'name' => 'nhs_heading_highlight', 'type' => 'text', 'default_value' => 'NHS prescription' ),
				array( 'key' => 'field_sp_nhs_subheading', 'label' => 'Subheading', 'name' => 'nhs_subheading', 'type' => 'text', 'default_value' => 'Fast, free NHS prescriptions delivered to your door' ),
				array(
					'key'          => 'field_sp_nhs_features',
					'label'        => 'Feature bullets',
					'name'         => 'nhs_features',
					'type'         => 'repeater',
					'button_label' => 'Add feature',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 6,
					'sub_fields'   => array(
						array( 'key' => 'field_sp_nhs_feat_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_nhs_feat_body', 'label' => 'Body', 'name' => 'body', 'type' => 'text' ),
					),
				),
				array( 'key' => 'field_sp_nhs_card_heading', 'label' => 'Sidebar card heading', 'name' => 'nhs_card_heading', 'type' => 'text', 'default_value' => 'Get Started' ),
				array( 'key' => 'field_sp_nhs_card_body', 'label' => 'Sidebar card body', 'name' => 'nhs_card_body', 'type' => 'text', 'default_value' => 'Order your NHS prescription or login to your account' ),
				array( 'key' => 'field_sp_nhs_primary_label', 'label' => 'Primary button label', 'name' => 'nhs_primary_label', 'type' => 'text', 'default_value' => 'Order Prescription' ),
				array( 'key' => 'field_sp_nhs_primary_url', 'label' => 'Primary button URL', 'name' => 'nhs_primary_url', 'type' => 'url', 'default_value' => '/nhs-prescriptions/' ),
				array( 'key' => 'field_sp_nhs_secondary_label', 'label' => 'Secondary button label', 'name' => 'nhs_secondary_label', 'type' => 'text', 'default_value' => 'Login to Account' ),
				array( 'key' => 'field_sp_nhs_secondary_url', 'label' => 'Secondary button URL', 'name' => 'nhs_secondary_url', 'type' => 'url', 'default_value' => '/my-account/' ),
				array( 'key' => 'field_sp_nhs_help_heading', 'label' => 'Help card heading', 'name' => 'nhs_help_heading', 'type' => 'text', 'default_value' => 'Need Help?' ),
				array( 'key' => 'field_sp_nhs_help_body', 'label' => 'Help card body', 'name' => 'nhs_help_body', 'type' => 'text', 'default_value' => 'Contact our pharmacy team' ),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A6 — Safety / GPhC (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a6_safety',
			'title'    => 'A6 — Safety / GPhC',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_sf_enabled', 'label' => 'Show section', 'name' => 'sf_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_sf_badge_label', 'label' => 'Badge label', 'name' => 'sf_badge_label', 'type' => 'text', 'default_value' => 'GPhC Registered Pharmacy' ),
				array( 'key' => 'field_sp_sf_heading_pre', 'label' => 'Heading — start', 'name' => 'sf_heading_pre', 'type' => 'text', 'default_value' => 'Safe and' ),
				array( 'key' => 'field_sp_sf_heading_highlight', 'label' => 'Heading — highlighted', 'name' => 'sf_heading_highlight', 'type' => 'text', 'default_value' => 'Secure' ),
				array( 'key' => 'field_sp_sf_subheading', 'label' => 'Subheading', 'name' => 'sf_subheading', 'type' => 'textarea', 'rows' => 3, 'default_value' => "Your safety is our top priority. We're fully regulated and inspected to ensure the highest standards of care." ),
				array(
					'key'          => 'field_sp_sf_trust_cards',
					'label'        => 'Trust cards',
					'name'         => 'sf_trust_cards',
					'type'         => 'repeater',
					'button_label' => 'Add card',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 6,
					'sub_fields'   => array(
						array( 'key' => 'field_sp_sf_card_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'ui' => 1 ),
						array( 'key' => 'field_sp_sf_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_sf_card_body', 'label' => 'Body', 'name' => 'body', 'type' => 'text' ),
					),
				),
				array( 'key' => 'field_sp_sf_gphc_body', 'label' => 'GPhC description', 'name' => 'sf_gphc_body', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'The GPhC is the official body that regulates and inspects all pharmacies in the UK. They ensure we prioritise your safety and meet the highest standards.' ),
				array( 'key' => 'field_sp_sf_verify_label', 'label' => 'Verify button label', 'name' => 'sf_verify_label', 'type' => 'text', 'default_value' => 'Verify our registration' ),
				array( 'key' => 'field_sp_sf_verify_url', 'label' => 'Verify button URL', 'name' => 'sf_verify_url', 'type' => 'url', 'default_value' => 'https://www.pharmacyregulation.org/registers/pharmacy/registrationnumber/9012842' ),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A7 — Most Trusted Treatments (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a7_most_trusted',
			'title'    => 'A7 — Most Trusted Treatments',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_mt_enabled', 'label' => 'Show section', 'name' => 'mt_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_mt_rating_score', 'label' => 'Rating — score', 'name' => 'mt_rating_score', 'type' => 'text', 'default_value' => '4.8/5' ),
				array( 'key' => 'field_sp_mt_rating_count', 'label' => 'Rating — count (animated)', 'name' => 'mt_rating_count', 'type' => 'number', 'default_value' => 6093 ),
				array( 'key' => 'field_sp_mt_heading_pre', 'label' => 'Heading — start', 'name' => 'mt_heading_pre', 'type' => 'text', 'default_value' => 'Our most' ),
				array( 'key' => 'field_sp_mt_heading_highlight', 'label' => 'Heading — highlighted', 'name' => 'mt_heading_highlight', 'type' => 'text', 'default_value' => 'trusted' ),
				array( 'key' => 'field_sp_mt_heading_post', 'label' => 'Heading — end', 'name' => 'mt_heading_post', 'type' => 'text', 'default_value' => 'treatments' ),
				array( 'key' => 'field_sp_mt_subheading', 'label' => 'Subheading', 'name' => 'mt_subheading', 'type' => 'text', 'default_value' => 'Dispensed by UK-based Pharmacists, delivered with care' ),
				array(
					'key'          => 'field_sp_mt_cards',
					'label'        => 'Cards (1 big + 2 small)',
					'name'         => 'mt_cards',
					'type'         => 'repeater',
					'button_label' => 'Add card',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 3,
					'instructions' => 'First card renders large (left, ~60% width); next two render stacked on the right. Design works best with exactly 3.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_mt_card_image', 'label' => 'Background image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
						array( 'key' => 'field_sp_mt_card_badge', 'label' => 'Top badge (e.g. Most Popular)', 'name' => 'badge', 'type' => 'text' ),
						array( 'key' => 'field_sp_mt_card_category', 'label' => 'Category eyebrow', 'name' => 'category', 'type' => 'text' ),
						array( 'key' => 'field_sp_mt_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_mt_card_quote', 'label' => 'Testimonial quote', 'name' => 'quote', 'type' => 'textarea', 'rows' => 2 ),
						array( 'key' => 'field_sp_mt_card_quote_author', 'label' => 'Testimonial author', 'name' => 'quote_author', 'type' => 'text' ),
						array( 'key' => 'field_sp_mt_card_price', 'label' => 'Price (e.g. £199)', 'name' => 'price', 'type' => 'text' ),
						array( 'key' => 'field_sp_mt_card_price_unit', 'label' => 'Price unit (e.g. /month)', 'name' => 'price_unit', 'type' => 'text', 'default_value' => '/month' ),
						array( 'key' => 'field_sp_mt_card_cta_label', 'label' => 'CTA label', 'name' => 'cta_label', 'type' => 'text', 'default_value' => 'View →' ),
						array( 'key' => 'field_sp_mt_card_cta_url', 'label' => 'CTA URL', 'name' => 'cta_url', 'type' => 'url' ),
					),
				),
				array( 'key' => 'field_sp_mt_bottom_cta_label', 'label' => 'Bottom CTA label', 'name' => 'mt_bottom_cta_label', 'type' => 'text', 'default_value' => 'Browse All Treatments' ),
				array( 'key' => 'field_sp_mt_bottom_cta_url', 'label' => 'Bottom CTA URL', 'name' => 'mt_bottom_cta_url', 'type' => 'url', 'default_value' => '/shop/' ),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A8 — Shop Bestsellers (options)  — static, replaced with real WC query in Stage 4
	 * ------------------------------------------------------------- */
	$sp_colour_choices = array(
		'teal'   => 'Teal',
		'purple' => 'Purple',
		'green'  => 'Green',
		'orange' => 'Orange',
		'red'    => 'Red',
		'blue'   => 'Blue',
	);
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a8_bestsellers',
			'title'    => 'A8 — Shop Bestsellers',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_bs_enabled', 'label' => 'Show section', 'name' => 'bs_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_bs_badge_label', 'label' => 'Badge label', 'name' => 'bs_badge_label', 'type' => 'text', 'default_value' => 'Shop Bestsellers' ),
				array( 'key' => 'field_sp_bs_heading_pre', 'label' => 'Heading — start', 'name' => 'bs_heading_pre', 'type' => 'text', 'default_value' => 'Top Pharmacy' ),
				array( 'key' => 'field_sp_bs_heading_highlight', 'label' => 'Heading — highlighted', 'name' => 'bs_heading_highlight', 'type' => 'text', 'default_value' => 'Essentials' ),
				array( 'key' => 'field_sp_bs_subheading', 'label' => 'Subheading', 'name' => 'bs_subheading', 'type' => 'text', 'default_value' => 'Fast delivery on everyday health essentials. Trusted brands, unbeatable prices.' ),
				array(
					'key'          => 'field_sp_bs_products',
					'label'        => 'Products (static)',
					'name'         => 'bs_products',
					'type'         => 'repeater',
					'button_label' => 'Add product',
					'layout'       => 'block',
					'min'          => 0,
					'instructions' => 'Static product cards. Replaced with a real WooCommerce query in Stage 4.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_bs_p_image', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ),
						array( 'key' => 'field_sp_bs_p_bg', 'label' => 'Image bg tint', 'name' => 'bg', 'type' => 'select', 'choices' => $sp_colour_choices, 'default_value' => 'teal', 'ui' => 1 ),
						array( 'key' => 'field_sp_bs_p_category', 'label' => 'Category label', 'name' => 'category', 'type' => 'text' ),
						array( 'key' => 'field_sp_bs_p_category_colour', 'label' => 'Category text colour', 'name' => 'category_colour', 'type' => 'select', 'choices' => $sp_colour_choices, 'default_value' => 'teal', 'ui' => 1 ),
						array( 'key' => 'field_sp_bs_p_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
						array( 'key' => 'field_sp_bs_p_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'text' ),
						array( 'key' => 'field_sp_bs_p_rating', 'label' => 'Rating (1-5)', 'name' => 'rating', 'type' => 'number', 'min' => 1, 'max' => 5, 'default_value' => 5 ),
						array( 'key' => 'field_sp_bs_p_reviews', 'label' => 'Reviews count', 'name' => 'reviews', 'type' => 'number', 'default_value' => 2847 ),
						array( 'key' => 'field_sp_bs_p_price', 'label' => 'Price', 'name' => 'price', 'type' => 'text' ),
						array( 'key' => 'field_sp_bs_p_badge_text', 'label' => 'Top badge text (optional)', 'name' => 'badge_text', 'type' => 'text' ),
						array( 'key' => 'field_sp_bs_p_badge_colour', 'label' => 'Top badge colour', 'name' => 'badge_colour', 'type' => 'select', 'choices' => $sp_colour_choices, 'default_value' => 'red', 'ui' => 1 ),
						array( 'key' => 'field_sp_bs_p_cta_label', 'label' => 'CTA label', 'name' => 'cta_label', 'type' => 'text', 'default_value' => 'Add to Cart' ),
						array( 'key' => 'field_sp_bs_p_cta_url', 'label' => 'CTA URL', 'name' => 'cta_url', 'type' => 'url' ),
					),
				),
				array( 'key' => 'field_sp_bs_bottom_cta_label', 'label' => 'Bottom CTA label', 'name' => 'bs_bottom_cta_label', 'type' => 'text', 'default_value' => 'View All Products' ),
				array( 'key' => 'field_sp_bs_bottom_cta_url', 'label' => 'Bottom CTA URL', 'name' => 'bs_bottom_cta_url', 'type' => 'url', 'default_value' => '/shop/' ),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A9 — Testimonials (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a9_testimonials',
			'title'    => 'A9 — Testimonials',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_ts_enabled', 'label' => 'Show section', 'name' => 'ts_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_ts_patient_count', 'label' => 'Eyebrow patient count (animated)', 'name' => 'ts_patient_count', 'type' => 'number', 'default_value' => 50000 ),
				array( 'key' => 'field_sp_ts_heading_pre', 'label' => 'Heading — start', 'name' => 'ts_heading_pre', 'type' => 'text', 'default_value' => 'Experience the' ),
				array( 'key' => 'field_sp_ts_heading_highlight', 'label' => 'Heading — highlighted', 'name' => 'ts_heading_highlight', 'type' => 'text', 'default_value' => 'difference' ),
				array( 'key' => 'field_sp_ts_subheading', 'label' => 'Subheading', 'name' => 'ts_subheading', 'type' => 'text', 'default_value' => 'Real stories from real patients who chose Smart Pharmacy for their healthcare journey' ),
				array(
					'key'          => 'field_sp_ts_cards',
					'label'        => 'Testimonial cards (1 big + 2 small)',
					'name'         => 'ts_cards',
					'type'         => 'repeater',
					'button_label' => 'Add card',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 3,
					'instructions' => 'First card = big (left, 7 cols, large stats). Next two stack on the right (5 cols, small stats). Design works best with exactly 3.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_ts_card_image', 'label' => 'Background image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
						array( 'key' => 'field_sp_ts_card_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_ts_card_quote', 'label' => 'Quote', 'name' => 'quote', 'type' => 'textarea', 'rows' => 3 ),
						array( 'key' => 'field_sp_ts_card_author', 'label' => 'Author', 'name' => 'author', 'type' => 'text' ),
						array(
							'key'          => 'field_sp_ts_card_stats',
							'label'        => 'Stats (max 2)',
							'name'         => 'stats',
							'type'         => 'repeater',
							'button_label' => 'Add stat',
							'layout'       => 'table',
							'min'          => 0,
							'max'          => 2,
							'sub_fields'   => array(
								array( 'key' => 'field_sp_ts_stat_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text' ),
								array( 'key' => 'field_sp_ts_stat_animate', 'label' => 'Animate?', 'name' => 'animate', 'type' => 'true_false', 'ui' => 1 ),
								array( 'key' => 'field_sp_ts_stat_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
							),
						),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * A10 — FAQ (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_a10_faq',
			'title'    => 'A10 — FAQ',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_faq_enabled', 'label' => 'Show section', 'name' => 'faq_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_faq_badge_label', 'label' => 'Badge label', 'name' => 'faq_badge_label', 'type' => 'text', 'default_value' => '24/7 Support Available' ),
				array( 'key' => 'field_sp_faq_heading_pre', 'label' => 'Heading — start', 'name' => 'faq_heading_pre', 'type' => 'text', 'default_value' => 'Ask us' ),
				array( 'key' => 'field_sp_faq_heading_highlight', 'label' => 'Heading — highlighted', 'name' => 'faq_heading_highlight', 'type' => 'text', 'default_value' => 'anything' ),
				array( 'key' => 'field_sp_faq_subheading', 'label' => 'Subheading', 'name' => 'faq_subheading', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Get instant answers to common questions, or speak with our pharmacy team for personalized support.' ),
				array(
					'key'          => 'field_sp_faq_items',
					'label'        => 'Questions & answers',
					'name'         => 'faq_items',
					'type'         => 'repeater',
					'button_label' => 'Add Q&A',
					'layout'       => 'block',
					'min'          => 0,
					'sub_fields'   => array(
						array( 'key' => 'field_sp_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text' ),
						array( 'key' => 'field_sp_faq_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'textarea', 'rows' => 4 ),
					),
				),
				array( 'key' => 'field_sp_faq_view_all_label', 'label' => 'View all label', 'name' => 'faq_view_all_label', 'type' => 'text', 'default_value' => 'View All Questions' ),
				array( 'key' => 'field_sp_faq_view_all_url', 'label' => 'View all URL', 'name' => 'faq_view_all_url', 'type' => 'url', 'default_value' => '/faqs/' ),
				array( 'key' => 'field_sp_faq_sidebar_heading', 'label' => 'Sidebar heading', 'name' => 'faq_sidebar_heading', 'type' => 'text', 'default_value' => 'Need more help?' ),
				array( 'key' => 'field_sp_faq_sidebar_body', 'label' => 'Sidebar body', 'name' => 'faq_sidebar_body', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Our pharmacy team is here to answer your questions and provide expert guidance on your healthcare journey.' ),
				array(
					'key'          => 'field_sp_faq_contacts',
					'label'        => 'Sidebar contact links',
					'name'         => 'faq_contacts',
					'type'         => 'repeater',
					'button_label' => 'Add link',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 4,
					'sub_fields'   => array(
						array( 'key' => 'field_sp_faq_c_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'ui' => 1 ),
						array( 'key' => 'field_sp_faq_c_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_faq_c_body', 'label' => 'Body', 'name' => 'body', 'type' => 'text' ),
						array( 'key' => 'field_sp_faq_c_url', 'label' => 'URL', 'name' => 'url', 'type' => 'text' ),
					),
				),
			),
		)
	);

	/* ===============================================================
	 * TREATMENT CPT FIELD GROUPS (B1–E1)
	 *
	 * All scoped to the `treatment` post type. All use
	 * `position => 'acf_after_title'` so the panel sits above the
	 * (disabled) Gutenberg editor on the edit screen.
	 * =============================================================== */

	/* ---------------------------------------------------------------
	 * B1 — Treatment Hero
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_b1_treatment_hero',
			'title'    => 'B1 — Treatment Hero',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_tx_hero_category', 'label' => 'Category eyebrow', 'name' => 'tx_hero_category', 'type' => 'text', 'instructions' => 'Small label above the heading (e.g. WEIGHT MANAGEMENT).' ),
				array( 'key' => 'field_sp_tx_hero_h_pre', 'label' => 'Heading — start', 'name' => 'tx_hero_heading_pre', 'type' => 'text' ),
				array( 'key' => 'field_sp_tx_hero_h_hi', 'label' => 'Heading — highlighted (teal gradient)', 'name' => 'tx_hero_heading_highlight', 'type' => 'text' ),
				array( 'key' => 'field_sp_tx_hero_h_post', 'label' => 'Heading — end', 'name' => 'tx_hero_heading_post', 'type' => 'text' ),
				array( 'key' => 'field_sp_tx_hero_sub', 'label' => 'Subheading', 'name' => 'tx_hero_subheading', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_sp_tx_hero_cta1_l', 'label' => 'Primary CTA label', 'name' => 'tx_hero_primary_cta_label', 'type' => 'text', 'default_value' => 'Start Consultation' ),
				array( 'key' => 'field_sp_tx_hero_cta1_u', 'label' => 'Primary CTA URL', 'name' => 'tx_hero_primary_cta_url', 'type' => 'url', 'default_value' => '#consultation' ),
				array( 'key' => 'field_sp_tx_hero_cta2_l', 'label' => 'Secondary CTA label', 'name' => 'tx_hero_secondary_cta_label', 'type' => 'text', 'default_value' => 'Learn More' ),
				array( 'key' => 'field_sp_tx_hero_cta2_u', 'label' => 'Secondary CTA URL', 'name' => 'tx_hero_secondary_cta_url', 'type' => 'url', 'default_value' => '#how-it-works' ),
				array( 'key' => 'field_sp_tx_hero_image', 'label' => 'Hero image', 'name' => 'tx_hero_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ),
				array(
					'key'          => 'field_sp_tx_hero_trust',
					'label'        => 'Trust items (row below CTAs)',
					'name'         => 'tx_hero_trust_items',
					'type'         => 'repeater',
					'button_label' => 'Add trust item',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 6,
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_hero_trust_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'default_value' => 'check', 'ui' => 1 ),
						array( 'key' => 'field_sp_tx_hero_trust_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
					),
				),
				array(
					'key'          => 'field_sp_tx_hero_stats',
					'label'        => 'Overlay stat cards (max 2)',
					'name'         => 'tx_hero_stats',
					'type'         => 'repeater',
					'button_label' => 'Add stat',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 2,
					'instructions' => 'Two stat tiles that overlay the bottom of the hero image.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_hero_stat_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text' ),
						array( 'key' => 'field_sp_tx_hero_stat_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * B2 — Treatment How It Works
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_b2_treatment_how_it_works',
			'title'    => 'B2 — Treatment How It Works',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_tx_how_enabled', 'label' => 'Show section', 'name' => 'tx_how_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_tx_how_badge_pre', 'label' => 'Badge — start', 'name' => 'tx_how_badge_pre', 'type' => 'text', 'default_value' => 'Simple' ),
				array( 'key' => 'field_sp_tx_how_badge_hi', 'label' => 'Badge — highlighted', 'name' => 'tx_how_badge_highlight', 'type' => 'text', 'default_value' => '3-Step' ),
				array( 'key' => 'field_sp_tx_how_badge_suf', 'label' => 'Badge — suffix', 'name' => 'tx_how_badge_suffix', 'type' => 'text', 'default_value' => 'Process' ),
				array( 'key' => 'field_sp_tx_how_h_pre', 'label' => 'Heading — start', 'name' => 'tx_how_heading_pre', 'type' => 'text', 'default_value' => 'How It' ),
				array( 'key' => 'field_sp_tx_how_h_hi', 'label' => 'Heading — highlighted', 'name' => 'tx_how_heading_highlight', 'type' => 'text', 'default_value' => 'Works' ),
				array( 'key' => 'field_sp_tx_how_sub', 'label' => 'Subheading', 'name' => 'tx_how_subheading', 'type' => 'text' ),
				array(
					'key'          => 'field_sp_tx_how_steps',
					'label'        => 'Steps',
					'name'         => 'tx_how_steps',
					'type'         => 'repeater',
					'button_label' => 'Add step',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 4,
					'instructions' => 'Each step renders as a numbered card. Design works best with 3.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_how_step_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_tx_how_step_meta', 'label' => 'Meta pill label', 'name' => 'meta_label', 'type' => 'text', 'instructions' => 'Small pill inside the card (e.g. "2-5 Minutes", "UK Doctors", "Next Day").' ),
						array( 'key' => 'field_sp_tx_how_step_icon', 'label' => 'Meta pill icon', 'name' => 'meta_icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'default_value' => 'check', 'ui' => 1 ),
						array( 'key' => 'field_sp_tx_how_step_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'rows' => 3 ),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * B3 — Treatment Options / Pricing
	 * (Static repeater for Stage 3a; Stage 4 replaces the data source
	 *  with a real WC_Query or ACF Relationship to WC product objects.)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_b3_treatment_options',
			'title'    => 'B3 — Treatment Options / Pricing',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_tx_opts_enabled', 'label' => 'Show section', 'name' => 'tx_opts_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_tx_opts_badge_pre', 'label' => 'Badge — start', 'name' => 'tx_opts_badge_pre', 'type' => 'text', 'default_value' => 'Prescription' ),
				array( 'key' => 'field_sp_tx_opts_badge_hi', 'label' => 'Badge — highlighted', 'name' => 'tx_opts_badge_highlight', 'type' => 'text', 'default_value' => 'Treatments' ),
				array( 'key' => 'field_sp_tx_opts_h_pre', 'label' => 'Heading — start', 'name' => 'tx_opts_heading_pre', 'type' => 'text' ),
				array( 'key' => 'field_sp_tx_opts_h_hi', 'label' => 'Heading — highlighted', 'name' => 'tx_opts_heading_highlight', 'type' => 'text' ),
				array( 'key' => 'field_sp_tx_opts_sub', 'label' => 'Subheading', 'name' => 'tx_opts_subheading', 'type' => 'text' ),
				array(
					'key'          => 'field_sp_tx_opts_items',
					'label'        => 'Options (max 4)',
					'name'         => 'tx_opts_items',
					'type'         => 'repeater',
					'button_label' => 'Add option',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 4,
					'instructions' => 'Design works best with 1–2 options. Each renders as a pricing card.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_opt_image', 'label' => 'Product image', 'name' => 'image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'instructions' => 'Displays full-width at the top of the card (banner ~288px tall). Upload at least 1000×1000px, transparent PNG preferred. Any aspect ratio works — the product is centered without cropping.' ),
						array( 'key' => 'field_sp_tx_opt_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text' ),
						array( 'key' => 'field_sp_tx_opt_badge', 'label' => 'Top-right badge (optional)', 'name' => 'badge', 'type' => 'text', 'instructions' => 'E.g. "Most Popular", "New", "Best Value".' ),
						array(
							'key'          => 'field_sp_tx_opt_benefits',
							'label'        => 'Bullet benefits',
							'name'         => 'benefits',
							'type'         => 'repeater',
							'button_label' => 'Add benefit',
							'layout'       => 'table',
							'min'          => 0,
							'sub_fields'   => array(
								array( 'key' => 'field_sp_tx_opt_benefit_text', 'label' => 'Text', 'name' => 'text', 'type' => 'text' ),
							),
						),
						array( 'key' => 'field_sp_tx_opt_price', 'label' => 'Price (e.g. £199)', 'name' => 'price', 'type' => 'text' ),
						array( 'key' => 'field_sp_tx_opt_price_unit', 'label' => 'Price unit', 'name' => 'price_unit', 'type' => 'text', 'default_value' => '/month' ),
						array( 'key' => 'field_sp_tx_opt_cta_l', 'label' => 'CTA label', 'name' => 'cta_label', 'type' => 'text', 'default_value' => 'Learn More' ),
						array( 'key' => 'field_sp_tx_opt_cta_u', 'label' => 'CTA URL', 'name' => 'cta_url', 'type' => 'url', 'default_value' => '#consultation' ),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * B4 — Treatment Meta
	 *
	 * Structured data about the treatment itself. Most of this drives
	 * OTHER contexts -- homepage carousel cards, archive listings,
	 * schema.org markup, consultation gating logic. The single-treatment
	 * template surfaces just a couple of these (legal classification pill
	 * in the hero, active ingredient near the headline).
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_b4_treatment_meta',
			'title'    => 'B4 — Treatment Meta',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(

				/* --- Identity --- */
				array(
					'key'          => 'field_sp_tx_meta_active',
					'label'        => 'Active ingredient',
					'name'         => 'tx_meta_active_ingredient',
					'type'         => 'text',
					'instructions' => 'Generic name of the main active ingredient (e.g. "Semaglutide", "Finasteride"). Used in hero meta, schema.org markup, and search.',
				),

				/* --- Listing / card data --- */
				array(
					'key'          => 'field_sp_tx_meta_short',
					'label'        => 'Short description',
					'name'         => 'tx_meta_short_description',
					'type'         => 'textarea',
					'rows'         => 2,
					'instructions' => '1-2 sentences used on homepage Popular Treatments cards, archive listings, and search results. NOT shown on the treatment page itself.',
				),
				array(
					'key'           => 'field_sp_tx_meta_card_image',
					'label'         => 'Card image',
					'name'          => 'tx_meta_card_image',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'instructions'  => 'Portrait 4:5 ratio works best. Used on homepage carousels and archive cards. Falls back to the hero image if left blank.',
				),
				array(
					'key'           => 'field_sp_tx_meta_price_from',
					'label'         => 'Price from',
					'name'          => 'tx_meta_price_from',
					'type'          => 'text',
					'instructions'  => 'Short price summary for cards and listings (e.g. "£149", "From £12.99"). Full pricing lives in B3.',
				),

				/* --- Taxonomy (UK-native hierarchical) --- */
				array(
					'key'           => 'field_sp_tx_meta_category',
					'label'         => 'Category',
					'name'          => 'tx_meta_category',
					'type'          => 'taxonomy',
					'taxonomy'      => 'treatment_category',
					'field_type'    => 'multi_select',
					'add_term'      => 1,
					'save_terms'    => 1,
					'load_terms'    => 1,
					'return_format' => 'id',
					'instructions'  => 'Assign one or more categories (e.g. Weight Management > GLP-1). Can be added inline.',
				),

				/* --- UK pharmacy compliance --- */
				array(
					'key'          => 'field_sp_tx_meta_legal',
					'label'        => 'Legal classification',
					'name'         => 'tx_meta_legal_class',
					'type'         => 'select',
					'choices'      => array(
						''    => '— Not set —',
						'POM' => 'POM — Prescription-Only Medicine',
						'P'   => 'P — Pharmacy Medicine',
						'GSL' => 'GSL — General Sales List',
					),
					'ui'           => 1,
					'instructions' => 'Drives whether consultation is required before purchase.',
				),
				array(
					'key'           => 'field_sp_tx_meta_requires_consult',
					'label'         => 'Requires consultation?',
					'name'          => 'tx_meta_requires_consultation',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
					'instructions'  => 'If yes, CTAs route to the consultation flow. If no, CTAs go straight to cart/basket. Stage 5 eligibility checker reads this.',
				),
				array(
					'key'          => 'field_sp_tx_meta_prescriber',
					'label'        => 'Prescriber specialist required?',
					'name'         => 'tx_meta_prescriber_required',
					'type'         => 'true_false',
					'ui'           => 1,
					'instructions' => 'Tick for treatments that need a specialist prescriber (e.g. HRT).',
				),
				array(
					'key'          => 'field_sp_tx_meta_mhra',
					'label'        => 'MHRA license number',
					'name'         => 'tx_meta_mhra_number',
					'type'         => 'text',
					'instructions' => 'Optional. Required for marketing-compliance display on POM products.',
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * C1 — Treatment What's Included
	 *
	 * Three feature cards highlighting what the customer receives
	 * (medication, clinical support, lifestyle guidance). Content-only
	 * -- no ties to WooCommerce or taxonomies.
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_c1_treatment_whats_included',
			'title'    => "C1 — Treatment What's Included",
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_tx_incl_enabled', 'label' => 'Show section', 'name' => 'tx_incl_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_tx_incl_badge_pre', 'label' => 'Badge — start', 'name' => 'tx_incl_badge_pre', 'type' => 'text', 'default_value' => 'Complete' ),
				array( 'key' => 'field_sp_tx_incl_badge_hi', 'label' => 'Badge — highlighted', 'name' => 'tx_incl_badge_highlight', 'type' => 'text', 'default_value' => 'Package' ),
				array( 'key' => 'field_sp_tx_incl_h_pre', 'label' => 'Heading — start', 'name' => 'tx_incl_heading_pre', 'type' => 'text', 'default_value' => "What's" ),
				array( 'key' => 'field_sp_tx_incl_h_hi', 'label' => 'Heading — highlighted', 'name' => 'tx_incl_heading_highlight', 'type' => 'text', 'default_value' => 'Included' ),
				array( 'key' => 'field_sp_tx_incl_sub', 'label' => 'Subheading', 'name' => 'tx_incl_subheading', 'type' => 'text', 'default_value' => 'Everything you need for successful weight loss' ),
				array(
					'key'          => 'field_sp_tx_incl_items',
					'label'        => 'Items',
					'name'         => 'tx_incl_items',
					'type'         => 'repeater',
					'button_label' => 'Add item',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 6,
					'instructions' => 'Each item renders as a feature card with icon, title, bullet list, and highlighted tagline. Design works best with 3.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_incl_item_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'default_value' => 'check_circle', 'ui' => 1 ),
						array( 'key' => 'field_sp_tx_incl_item_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array(
							'key'          => 'field_sp_tx_incl_item_bullets',
							'label'        => 'Bullet points',
							'name'         => 'bullets',
							'type'         => 'repeater',
							'button_label' => 'Add bullet',
							'layout'       => 'table',
							'min'          => 0,
							'max'          => 6,
							'sub_fields'   => array(
								array( 'key' => 'field_sp_tx_incl_item_bullet_text', 'label' => 'Text', 'name' => 'text', 'type' => 'text' ),
							),
						),
						array( 'key' => 'field_sp_tx_incl_item_tagline', 'label' => 'Tagline', 'name' => 'tagline', 'type' => 'text', 'instructions' => 'Short highlighted line shown in the teal callout at the bottom of the card (e.g. "Available treatments: Wegovy, Mounjaro").' ),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * C2 — Treatment Results / Testimonials
	 *
	 * Social-proof grid: rating summary + 3 testimonial cards per
	 * treatment. Each card has a patient photo, star rating, editorial
	 * italic quote, name, and outcome meta line. Content-only; no ties
	 * to WooCommerce reviews (Stage 4 could swap in WC review data).
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_c2_treatment_testimonials',
			'title'    => 'C2 — Treatment Results / Testimonials',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_tx_tst_enabled', 'label' => 'Show section', 'name' => 'tx_tst_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_tx_tst_rating_score', 'label' => 'Rating score', 'name' => 'tx_tst_rating_score', 'type' => 'text', 'default_value' => '4.9/5', 'instructions' => 'Summary score shown in the eyebrow pill (e.g. "4.9/5"). Leave blank to hide the pill.' ),
				array( 'key' => 'field_sp_tx_tst_rating_count', 'label' => 'Rating count label', 'name' => 'tx_tst_rating_count', 'type' => 'text', 'default_value' => 'from 3,847 reviews', 'instructions' => 'Trailing text after the score (e.g. "from 3,847 reviews").' ),
				array( 'key' => 'field_sp_tx_tst_h_pre', 'label' => 'Heading — start', 'name' => 'tx_tst_heading_pre', 'type' => 'text', 'default_value' => 'Real results.' ),
				array( 'key' => 'field_sp_tx_tst_h_hi', 'label' => 'Heading — highlighted', 'name' => 'tx_tst_heading_highlight', 'type' => 'text', 'default_value' => 'Real people.' ),
				array( 'key' => 'field_sp_tx_tst_sub', 'label' => 'Subheading', 'name' => 'tx_tst_subheading', 'type' => 'text', 'default_value' => 'See what our patients have achieved with Smart Pharmacy' ),
				array(
					'key'          => 'field_sp_tx_tst_items',
					'label'        => 'Testimonials',
					'name'         => 'tx_tst_items',
					'type'         => 'repeater',
					'button_label' => 'Add testimonial',
					'layout'       => 'block',
					'min'          => 0,
					'max'          => 6,
					'instructions' => 'Each testimonial renders as a card. Design works best with 3. Patient photo is optional — cards without an image render as text-only.',
					'sub_fields'   => array(
						array(
							'key'           => 'field_sp_tx_tst_item_image',
							'label'         => 'Patient photo',
							'name'          => 'image',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'instructions'  => 'Portrait photo; displays as a 256px-tall banner at the top of the card. Obtain signed consent before uploading patient imagery.',
						),
						array(
							'key'           => 'field_sp_tx_tst_item_rating',
							'label'         => 'Star rating (0-5)',
							'name'          => 'rating',
							'type'          => 'number',
							'min'           => 0,
							'max'           => 5,
							'default_value' => 5,
							'instructions'  => 'Number of filled stars shown above the quote. Use 0 to hide the rating row.',
						),
						array( 'key' => 'field_sp_tx_tst_item_quote', 'label' => 'Quote', 'name' => 'quote', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Patient testimonial. Rendered in italic serif with curly quotes auto-added.' ),
						array( 'key' => 'field_sp_tx_tst_item_name', 'label' => 'Name', 'name' => 'name', 'type' => 'text', 'instructions' => 'Patient name or initials (e.g. "Sarah M.").' ),
						array( 'key' => 'field_sp_tx_tst_item_meta', 'label' => 'Outcome meta', 'name' => 'meta', 'type' => 'text', 'instructions' => 'Short outcome line under the name (e.g. "Lost 32 lbs • 4 months").' ),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * C3 — Treatment Eligibility (Stage 3b stub)
	 *
	 * Teal-gradient "Check Your Eligibility" CTA block with 3 criteria
	 * tiles and a primary button. Anchor target for B3's `#consultation`
	 * pricing-card CTAs. Stage 5 swaps the static tiles for a live BMI
	 * calculator form + eligibility gating logic.
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_c3_treatment_eligibility',
			'title'    => 'C3 — Treatment Eligibility',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_tx_elig_enabled', 'label' => 'Show section', 'name' => 'tx_elig_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_tx_elig_badge_text', 'label' => 'Eyebrow badge text', 'name' => 'tx_elig_badge_text', 'type' => 'text', 'default_value' => 'Check Your Eligibility' ),
				array( 'key' => 'field_sp_tx_elig_badge_icon', 'label' => 'Eyebrow badge icon', 'name' => 'tx_elig_badge_icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'default_value' => 'check_circle', 'ui' => 1 ),
				array( 'key' => 'field_sp_tx_elig_heading', 'label' => 'Heading', 'name' => 'tx_elig_heading', 'type' => 'text', 'default_value' => 'Calculate Your Weight Loss Journey' ),
				array( 'key' => 'field_sp_tx_elig_sub', 'label' => 'Subheading', 'name' => 'tx_elig_subheading', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Find out if weight loss medication is right for you with our quick eligibility checker' ),
				array(
					'key'          => 'field_sp_tx_elig_criteria',
					'label'        => 'Eligibility criteria',
					'name'         => 'tx_elig_criteria',
					'type'         => 'repeater',
					'button_label' => 'Add criterion',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 4,
					'instructions' => 'Each criterion renders as a tile with icon, title, and short description. Design works best with 3.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_elig_c_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'default_value' => 'check_circle', 'ui' => 1 ),
						array( 'key' => 'field_sp_tx_elig_c_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_tx_elig_c_desc', 'label' => 'Description', 'name' => 'description', 'type' => 'text' ),
					),
				),
				array( 'key' => 'field_sp_tx_elig_cta_label', 'label' => 'CTA button label', 'name' => 'tx_elig_cta_label', 'type' => 'text', 'default_value' => 'Start Free Assessment' ),
				array( 'key' => 'field_sp_tx_elig_cta_url', 'label' => 'CTA button URL', 'name' => 'tx_elig_cta_url', 'type' => 'url', 'default_value' => '#consultation', 'instructions' => 'Stage 5 will replace this with the real consultation / eligibility-checker flow. Leave as "#consultation" for now.' ),
				array( 'key' => 'field_sp_tx_elig_footer', 'label' => 'Footer microcopy', 'name' => 'tx_elig_footer_note', 'type' => 'text', 'default_value' => 'Takes less than 5 minutes • No payment required' ),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * D1 — Treatment FAQ
	 *
	 * Accordion of treatment-specific Q&As + sticky sidebar with a
	 * "Need more help?" contact card and GPhC trust badge. Mirrors the
	 * field shape of A10 (homepage FAQ) so editors learn one pattern,
	 * but scoped per-treatment with tx_faq_* field names. GPhC number
	 * is pulled from I1 Compliance (options) via the three-tier resolver.
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_d1_treatment_faq',
			'title'    => 'D1 — Treatment FAQ',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_tx_faq_enabled', 'label' => 'Show section', 'name' => 'tx_faq_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_tx_faq_badge_pre', 'label' => 'Badge — start', 'name' => 'tx_faq_badge_pre', 'type' => 'text', 'default_value' => 'Common' ),
				array( 'key' => 'field_sp_tx_faq_badge_hi', 'label' => 'Badge — highlighted', 'name' => 'tx_faq_badge_highlight', 'type' => 'text', 'default_value' => 'Questions' ),
				array( 'key' => 'field_sp_tx_faq_h_pre', 'label' => 'Heading — start', 'name' => 'tx_faq_heading_pre', 'type' => 'text', 'default_value' => 'Frequently Asked' ),
				array( 'key' => 'field_sp_tx_faq_h_hi', 'label' => 'Heading — highlighted', 'name' => 'tx_faq_heading_highlight', 'type' => 'text', 'default_value' => 'Questions' ),
				array( 'key' => 'field_sp_tx_faq_sub', 'label' => 'Subheading', 'name' => 'tx_faq_subheading', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Everything you need to know about our weight loss service' ),
				array(
					'key'          => 'field_sp_tx_faq_items',
					'label'        => 'Questions & answers',
					'name'         => 'tx_faq_items',
					'type'         => 'repeater',
					'button_label' => 'Add Q&A',
					'layout'       => 'block',
					'min'          => 0,
					'instructions' => 'Each entry renders as a <details> accordion item. No hard cap — but 5–8 reads best.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text' ),
						array( 'key' => 'field_sp_tx_faq_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'textarea', 'rows' => 4 ),
					),
				),
				array( 'key' => 'field_sp_tx_faq_view_all_label', 'label' => 'View-all CTA label', 'name' => 'tx_faq_view_all_label', 'type' => 'text', 'default_value' => 'View All Questions' ),
				array( 'key' => 'field_sp_tx_faq_view_all_url', 'label' => 'View-all CTA URL', 'name' => 'tx_faq_view_all_url', 'type' => 'url', 'default_value' => '/faqs/', 'instructions' => 'Leave blank to hide the button.' ),
				array( 'key' => 'field_sp_tx_faq_side_h', 'label' => 'Sidebar heading', 'name' => 'tx_faq_sidebar_heading', 'type' => 'text', 'default_value' => 'Need more help?' ),
				array( 'key' => 'field_sp_tx_faq_side_b', 'label' => 'Sidebar body', 'name' => 'tx_faq_sidebar_body', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Our pharmacy team is here to answer your questions and provide expert guidance on your weight loss journey.' ),
				array(
					'key'          => 'field_sp_tx_faq_contacts',
					'label'        => 'Sidebar contact links',
					'name'         => 'tx_faq_contacts',
					'type'         => 'repeater',
					'button_label' => 'Add link',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 4,
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_faq_c_icon', 'label' => 'Icon', 'name' => 'icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'ui' => 1 ),
						array( 'key' => 'field_sp_tx_faq_c_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_sp_tx_faq_c_body', 'label' => 'Body', 'name' => 'body', 'type' => 'text' ),
						array( 'key' => 'field_sp_tx_faq_c_url', 'label' => 'URL', 'name' => 'url', 'type' => 'text', 'instructions' => 'Accepts any URL scheme (https://, mailto:, tel:). The esc_url() filter allows tel:/mailto: links.' ),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * D2 — Treatment Final CTA
	 *
	 * Teal-gradient closing CTA band with eyebrow pill, h2, paired
	 * primary (solid white) + secondary (glass) buttons, and a trust-
	 * signal row. Primary CTA defaults to #consultation (anchors to
	 * the C3 Eligibility section); secondary defaults to /contact/.
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_d2_treatment_final_cta',
			'title'    => 'D2 — Treatment Final CTA',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array( 'key' => 'field_sp_tx_cta_enabled', 'label' => 'Show section', 'name' => 'tx_cta_enabled', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_sp_tx_cta_badge_text', 'label' => 'Eyebrow badge text', 'name' => 'tx_cta_badge_text', 'type' => 'text', 'default_value' => 'Start Your Journey Today' ),
				array( 'key' => 'field_sp_tx_cta_badge_icon', 'label' => 'Eyebrow badge icon', 'name' => 'tx_cta_badge_icon', 'type' => 'select', 'choices' => $sp_icon_choices, 'default_value' => 'sparkle', 'ui' => 1 ),
				array( 'key' => 'field_sp_tx_cta_heading', 'label' => 'Heading', 'name' => 'tx_cta_heading', 'type' => 'text', 'default_value' => 'Start Your Weight Loss Journey Online' ),
				array( 'key' => 'field_sp_tx_cta_sub', 'label' => 'Subheading', 'name' => 'tx_cta_subheading', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'Get started in under 5 minutes with our simple online assessment' ),
				array( 'key' => 'field_sp_tx_cta_primary_label', 'label' => 'Primary button label', 'name' => 'tx_cta_primary_label', 'type' => 'text', 'default_value' => 'Start Consultation', 'instructions' => 'Leave blank to hide the primary button.' ),
				array( 'key' => 'field_sp_tx_cta_primary_url', 'label' => 'Primary button URL', 'name' => 'tx_cta_primary_url', 'type' => 'url', 'default_value' => '#consultation', 'instructions' => 'Default `#consultation` anchors to the C3 Eligibility section above. Stage 5 may replace this with the real consultation flow URL.' ),
				array( 'key' => 'field_sp_tx_cta_secondary_label', 'label' => 'Secondary button label', 'name' => 'tx_cta_secondary_label', 'type' => 'text', 'default_value' => 'Speak to a Pharmacist', 'instructions' => 'Leave blank to hide the secondary button.' ),
				array( 'key' => 'field_sp_tx_cta_secondary_url', 'label' => 'Secondary button URL', 'name' => 'tx_cta_secondary_url', 'type' => 'url', 'default_value' => '/contact/' ),
				array(
					'key'          => 'field_sp_tx_cta_trust',
					'label'        => 'Trust signals',
					'name'         => 'tx_cta_trust',
					'type'         => 'repeater',
					'button_label' => 'Add trust item',
					'layout'       => 'table',
					'min'          => 0,
					'max'          => 5,
					'instructions' => 'Inline-separated items shown under the CTAs. Each renders with a white checkmark.',
					'sub_fields'   => array(
						array( 'key' => 'field_sp_tx_cta_trust_text', 'label' => 'Text', 'name' => 'text', 'type' => 'text' ),
					),
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * E1 — Treatment → Related WooCommerce Products
	 *
	 * Links a treatment landing page to the WooCommerce SKUs it sells.
	 * The Treatment is the source of truth for medical classification
	 * (B4: POM / P / GSL + requires-consultation flag); the E1
	 * relationship makes that classification reachable from the product
	 * side so the Stage 4c POM gating can swap Add-to-Cart for a
	 * consultation CTA on prescription products.
	 *
	 * The product <-> treatment link is stored once here (treatment side)
	 * and cached onto each linked product as _sp_linked_treatment_id
	 * postmeta via the acf/save_post hook in inc/woocommerce.php, so
	 * runtime product lookups are O(1) rather than scanning every
	 * treatment's meta on each request.
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_e1_treatment_related_products',
			'title'    => 'E1 — Related Products',
			'position' => 'acf_after_title',
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'treatment' ),
				),
			),
			'fields'   => array(
				array(
					'key'           => 'field_sp_tx_related_products',
					'label'         => 'Related WooCommerce products',
					'name'          => 'tx_related_products',
					'type'          => 'relationship',
					'post_type'     => array( 'product' ),
					'filters'       => array( 'search', 'post_type', 'taxonomy' ),
					'return_format' => 'id',
					'min'           => 0,
					'max'           => 0,
					'instructions'  => 'Search and pick the WooCommerce products this treatment sells. Drives the related-products block on the treatment page AND the POM gating on the shop -- if the treatment is flagged POM in B4, every linked product routes Add-to-Cart to this treatment\'s consultation flow instead of the basket.',
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * F1 — Branding (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_f1_branding',
			'title'    => 'F1 — Branding',
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'sp-branding',
					),
				),
			),
			'fields'   => array(
				array(
					'key'           => 'field_sp_brand_logo',
					'label'         => 'Logo',
					'name'          => 'brand_logo',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'instructions'  => 'Main site logo (appears in header and footer).',
				),
				array(
					'key'          => 'field_sp_brand_logo_alt',
					'label'        => 'Logo alt text',
					'name'         => 'brand_logo_alt',
					'type'         => 'text',
					'default_value' => 'Smart Pharmacy',
				),
				array(
					'key'          => 'field_sp_brand_footer_tagline',
					'label'        => 'Footer tagline',
					'name'         => 'brand_footer_tagline',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value' => 'Your trusted online pharmacy providing expert healthcare on your terms. NHS-approved, prescription-perfect, delivered tomorrow.',
				),
				array(
					'key'           => 'field_sp_brand_payment_methods',
					'label'         => 'Payment methods image',
					'name'          => 'brand_payment_methods',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'thumbnail',
					'instructions'  => 'Row of accepted payment logos (Visa, Mastercard, etc.).',
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * G1 — Navigation (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_g1_navigation',
			'title'    => 'G1 — Navigation',
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'sp-navigation',
					),
				),
			),
			'fields'   => array(
				array(
					'key'          => 'field_sp_nav_primary',
					'label'        => 'Primary menu items',
					'name'         => 'nav_primary',
					'type'         => 'repeater',
					'button_label' => 'Add menu item',
					'layout'       => 'table',
					'sub_fields'   => array(
						array(
							'key'   => 'field_sp_nav_primary_label',
							'label' => 'Label',
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_sp_nav_primary_url',
							'label' => 'URL',
							'name'  => 'url',
							'type'  => 'url',
						),
						array(
							'key'          => 'field_sp_nav_primary_has_caret',
							'label'        => 'Show dropdown caret',
							'name'         => 'has_caret',
							'type'         => 'true_false',
							'ui'           => 1,
							'instructions' => 'Adds a chevron icon. Dropdown panel comes in a later stage.',
						),
					),
				),
				array(
					'key'          => 'field_sp_nav_search_placeholder',
					'label'        => 'Header search placeholder',
					'name'         => 'nav_search_placeholder',
					'type'         => 'text',
					'default_value' => 'Search medicines...',
				),
				array(
					'key'          => 'field_sp_nav_nhs_enabled',
					'label'        => 'Show NHS Prescriptions button',
					'name'         => 'nav_nhs_enabled',
					'type'         => 'true_false',
					'ui'           => 1,
					'default_value' => 1,
				),
				array(
					'key'          => 'field_sp_nav_nhs_label',
					'label'        => 'NHS button label',
					'name'         => 'nav_nhs_label',
					'type'         => 'text',
					'default_value' => 'NHS Prescriptions',
				),
				array(
					'key'          => 'field_sp_nav_nhs_url',
					'label'        => 'NHS button URL',
					'name'         => 'nav_nhs_url',
					'type'         => 'url',
					'default_value' => '/nhs-prescriptions/',
				),
				array(
					'key'          => 'field_sp_nav_footer_quick',
					'label'        => 'Footer — Quick Links',
					'name'         => 'nav_footer_quick',
					'type'         => 'repeater',
					'button_label' => 'Add link',
					'layout'       => 'table',
					'sub_fields'   => array(
						array(
							'key'   => 'field_sp_nav_fq_label',
							'label' => 'Label',
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_sp_nav_fq_url',
							'label' => 'URL',
							'name'  => 'url',
							'type'  => 'url',
						),
					),
				),
				array(
					'key'          => 'field_sp_nav_footer_legal',
					'label'        => 'Footer — Legal',
					'name'         => 'nav_footer_legal',
					'type'         => 'repeater',
					'button_label' => 'Add link',
					'layout'       => 'table',
					'sub_fields'   => array(
						array(
							'key'   => 'field_sp_nav_fl_label',
							'label' => 'Label',
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_sp_nav_fl_url',
							'label' => 'URL',
							'name'  => 'url',
							'type'  => 'url',
						),
					),
				),
				array(
					'key'          => 'field_sp_nav_copyright',
					'label'        => 'Footer copyright line',
					'name'         => 'nav_copyright',
					'type'         => 'text',
					'default_value' => '© ' . gmdate( 'Y' ) . ' Smart Pharmacy. All rights reserved.',
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * H1 — Contact (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_h1_contact',
			'title'    => 'H1 — Contact',
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'sp-contact',
					),
				),
			),
			'fields'   => array(
				array(
					'key'          => 'field_sp_contact_trading_address',
					'label'        => 'Trading address',
					'name'         => 'contact_trading_address',
					'type'         => 'textarea',
					'rows'         => 4,
					'new_lines'    => 'br',
					'default_value' => "Smart Pharmacy\nUnit A2 Ivinghoe Business Centre\nLU55BQ",
				),
				array(
					'key'          => 'field_sp_contact_registered',
					'label'        => 'Company registration',
					'name'         => 'contact_registered',
					'type'         => 'textarea',
					'rows'         => 3,
					'default_value' => 'Emwhy Pharma, 51 Arnald Way, Houghton Regis, LU55UN',
					'instructions' => 'Displayed in the footer registration badge. No line breaks required.',
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * I1 — Compliance (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_i1_compliance',
			'title'    => 'I1 — Compliance',
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'sp-compliance',
					),
				),
			),
			'fields'   => array(
				array(
					'key'          => 'field_sp_comp_gphc',
					'label'        => 'GPhC number',
					'name'         => 'comp_gphc_number',
					'type'         => 'text',
					'default_value' => '9012842',
				),
				array(
					'key'           => 'field_sp_comp_mhra_logo',
					'label'         => 'MHRA registered pharmacy logo',
					'name'          => 'comp_mhra_logo',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'instructions'  => 'The official MHRA "registered pharmacy" distance-selling logo (obtained from the MHRA once registered). Shown in the footer.',
				),
				array(
					'key'          => 'field_sp_comp_mhra_url',
					'label'        => 'MHRA register link',
					'name'         => 'comp_mhra_register_url',
					'type'         => 'url',
					'instructions' => 'Link to this pharmacy\'s entry on the MHRA register (the logo must link here).',
				),
			),
		)
	);

	/* ---------------------------------------------------------------
	 * J1 — Social (options)
	 * ------------------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_sp_j1_social',
			'title'    => 'J1 — Social',
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'sp-social',
					),
				),
			),
			'fields'   => array(
				array(
					'key'   => 'field_sp_social_facebook',
					'label' => 'Facebook URL',
					'name'  => 'social_facebook',
					'type'  => 'url',
				),
				array(
					'key'   => 'field_sp_social_instagram',
					'label' => 'Instagram URL',
					'name'  => 'social_instagram',
					'type'  => 'url',
				),
				array(
					'key'   => 'field_sp_social_tiktok',
					'label' => 'TikTok URL',
					'name'  => 'social_tiktok',
					'type'  => 'url',
				),
				array(
					'key'          => 'field_sp_social_twitter',
					'label'        => 'X / Twitter URL (optional)',
					'name'         => 'social_twitter',
					'type'         => 'url',
				),
				array(
					'key'          => 'field_sp_social_linkedin',
					'label'        => 'LinkedIn URL (optional)',
					'name'         => 'social_linkedin',
					'type'         => 'url',
				),
			),
		)
	);
}
add_action( 'acf/init', 'sp_register_acf_field_groups' );
