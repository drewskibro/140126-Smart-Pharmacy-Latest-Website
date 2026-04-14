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
 *   - Featured Treatment showcase (Stage 2c)
 *   - How It Works (Stage 2c)
 *   - NHS Prescription (Stage 2c)
 *   - Safety / GPhC (Stage 2c)
 *   - Most Trusted Treatments (Stage 2c)
 *
 * @package SmartPharmacy
 */

get_header();

get_template_part( 'template-parts/front-page/hero' );
get_template_part( 'template-parts/front-page/popular-treatments' );
get_template_part( 'template-parts/front-page/featured-treatment' );
get_template_part( 'template-parts/front-page/how-it-works' );
get_template_part( 'template-parts/front-page/nhs-prescription' );
get_template_part( 'template-parts/front-page/safety' );
get_template_part( 'template-parts/front-page/most-trusted' );

get_footer();
