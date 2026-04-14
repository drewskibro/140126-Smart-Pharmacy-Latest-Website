<?php
/**
 * Front page template.
 *
 * Assembles the homepage from section-specific template parts. Each part is
 * self-contained (pulls its own ACF data, has its own enabled toggle) so
 * stages can add / reorder sections without touching unrelated files.
 *
 * Sections ported so far:
 *   - Hero (Stage 2b)
 *   - Popular Treatments carousel (Stage 2b)
 *
 * @package SmartPharmacy
 */

get_header();

get_template_part( 'template-parts/front-page/hero' );
get_template_part( 'template-parts/front-page/popular-treatments' );

get_footer();
