<?php
/**
 * Site header.
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

<body <?php body_class( 'relative antialiased text-neutral-900' ); ?>>
<?php wp_body_open(); ?>

<a class="skip-link sr-only focus:not-sr-only" href="#content"><?php esc_html_e( 'Skip to content', 'smart-pharmacy' ); ?></a>

<div id="scroll-progress"></div>

<header id="masthead" class="site-header">
	<?php // Stage 2 will port the full header markup from index.html here. ?>
</header>

<main id="content" class="site-content">
