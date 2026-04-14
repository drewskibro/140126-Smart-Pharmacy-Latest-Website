<?php
/**
 * Site header.
 *
 * The markup itself lives in template-parts/header/site-header.php so the
 * shell (doctype, <head>, body open, progress bar, skip link) stays clean.
 *
 * @package SmartPharmacy
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'relative text-neutral-700 text-[15px] bg-fixed bg-white bg-[linear-gradient(to_right_bottom,rgb(253,254,255),rgb(240,248,249),rgb(232,244,245))] box-border flex flex-col leading-[25.71px] min-h-screen break-words overflow-x-hidden overflow-y-auto font-sans' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-link sr-only focus:not-sr-only absolute top-2 left-2 z-[200] bg-white px-4 py-2 rounded-lg shadow-lg" href="#content"><?php esc_html_e( 'Skip to content', 'smart-pharmacy' ); ?></a>

<!-- Scroll progress bar (updated by assets/js/main.js) -->
<div id="scroll-progress" class="fixed top-0 left-0 w-0 h-[3px] bg-gradient-to-r from-[#10c0a9] to-[#0da592] z-[100] transition-all duration-100 shadow-[0_2px_10px_rgba(16,192,169,0.3)]"></div>

<?php get_template_part( 'template-parts/header/site-header' ); ?>
<?php get_template_part( 'template-parts/header/mobile-menu' ); ?>

<main id="content" role="main" class="box-border grow break-words">
