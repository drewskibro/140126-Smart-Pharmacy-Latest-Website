<?php
/**
 * Single Treatment template.
 *
 * Renders `treatment` CPT posts at /treatments/{slug}/. Assembles the page
 * from section-specific template parts, each of which pulls its own ACF
 * data and has its own enabled toggle where appropriate.
 *
 * Sections ported so far:
 *   - Hero (Stage 3a)
 *   - How It Works (Stage 3a)
 *   - Treatment Options / Pricing (Stage 3a — static; real WC data in Stage 4)
 *   - What's Included (Stage 3b)
 *   - Results / Testimonials (Stage 3b)
 *   - Eligibility (Stage 3b — static UI; Stage 5 wires the real calculator)
 *   - FAQ (Stage 3c)
 *   - Final CTA (Stage 3c)
 *
 * Planned:
 *   - Related products (Stage 3c stub; Stage 4 wires real relationships)
 *
 * @package SmartPharmacy
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/single-treatment/hero' );
		get_template_part( 'template-parts/single-treatment/how-it-works' );
		get_template_part( 'template-parts/single-treatment/treatment-options' );
		get_template_part( 'template-parts/single-treatment/whats-included' );
		get_template_part( 'template-parts/single-treatment/testimonials' );
		get_template_part( 'template-parts/single-treatment/eligibility' );
		get_template_part( 'template-parts/single-treatment/faq' );
		get_template_part( 'template-parts/single-treatment/final-cta' );

	endwhile;
endif;

get_footer();
