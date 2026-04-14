<?php
/**
 * ACF Field Group registrations for Smart Pharmacy.
 *
 * Field groups are letter-coded (F1, G1, H1, ...) following the PharmoDigital
 * convention. Each group is registered via acf_add_local_field_group() so it
 * lives in code, not the database.
 *
 * Registered so far:
 *   A1  Options (Homepage) — Hero section
 *   A2  Options (Homepage) — Popular Treatments carousel
 *   A3  Options (Homepage) — Featured Treatment showcase (Weight Loss block)
 *   A4  Options (Homepage) — How It Works
 *   A5  Options (Homepage) — NHS Prescription
 *   A6  Options (Homepage) — Safety / GPhC
 *   A7  Options (Homepage) — Most Trusted Treatments
 *   F1  Options — Branding (logo, footer tagline, payment methods)
 *   G1  Options — Navigation (primary menu, footer link columns, search, NHS button)
 *   H1  Options — Contact (trading address, registered address)
 *   I1  Options — Compliance (GPhC number)
 *   J1  Options — Social (platform URLs)
 *
 * Planned:
 *   A8     Remaining front page sections (testimonials, FAQ, bestsellers)
 *   B1–E1  Treatment CPT (meta, benefits, FAQ, related products)
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
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'sp-homepage',
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
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'sp-homepage',
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
			'location' => array(
				array(
					array( 'param' => 'options_page', 'operator' => '==', 'value' => 'sp-homepage' ),
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
			'location' => array(
				array(
					array( 'param' => 'options_page', 'operator' => '==', 'value' => 'sp-homepage' ),
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
			'location' => array(
				array(
					array( 'param' => 'options_page', 'operator' => '==', 'value' => 'sp-homepage' ),
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
			'location' => array(
				array(
					array( 'param' => 'options_page', 'operator' => '==', 'value' => 'sp-homepage' ),
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
			'location' => array(
				array(
					array( 'param' => 'options_page', 'operator' => '==', 'value' => 'sp-homepage' ),
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
