<?php
/**
 * ACF Field Group registrations for Smart Pharmacy.
 *
 * Field groups are letter-coded (F1, G1, H1, ...) following the PharmoDigital
 * convention. Each group is registered via acf_add_local_field_group() so it
 * lives in code, not the database.
 *
 * Registered so far:
 *   A1  Options (Homepage) — Hero section (rating badge, heading, search, trust badges, pills, info cards)
 *   A2  Options (Homepage) — Popular Treatments carousel
 *   F1  Options — Branding (logo, footer tagline, payment methods)
 *   G1  Options — Navigation (primary menu, footer link columns, search, NHS button)
 *   H1  Options — Contact (trading address, registered address)
 *   I1  Options — Compliance (GPhC number)
 *   J1  Options — Social (platform URLs)
 *
 * Planned:
 *   A3–A8  Remaining front page sections (how it works, testimonials, FAQ, etc.)
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
