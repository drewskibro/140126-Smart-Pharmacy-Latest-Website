<?php
/**
 * Smart Pharmacy template helpers.
 *
 * Centralises the three-tier ACF fallback pattern used across all PharmoDigital
 * pharmacy themes:
 *
 *   Tier 1 — current post (page field)
 *   Tier 2 — theme options (options field)
 *   Tier 3 — hardcoded default passed by caller
 *
 * Uses strict null comparison. ACF's `true_false` fields return integer `0`
 * for "No" — loose comparisons (empty, !) would incorrectly fall through
 * and clobber an intentional "No" with the options/default value.
 *
 * @package SmartPharmacy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Three-tier field fetch.
 *
 * @param string     $name    ACF field name (or key).
 * @param mixed      $default Hardcoded default if neither tier has a value. Default null.
 * @param int|string $post_id Optional post ID for tier 1. Defaults to the current post.
 * @return mixed The resolved value, or $default.
 */
function sp_field( $name, $default = null, $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	// Tier 1: page-level (current post or explicit post ID).
	$value = get_field( $name, $post_id );
	if ( $value !== null ) {
		return $value;
	}

	// Tier 2: global options.
	$value = get_field( $name, 'option' );
	if ( $value !== null ) {
		return $value;
	}

	// Tier 3: hardcoded default.
	return $default;
}

/**
 * Echo a three-tier field, escaped for HTML.
 *
 * @param string $name    ACF field name.
 * @param string $default Hardcoded default.
 */
function sp_field_e( $name, $default = '' ) {
	echo esc_html( (string) sp_field( $name, $default ) );
}

/**
 * Echo a three-tier field, escaped as a URL attribute.
 *
 * @param string $name    ACF field name.
 * @param string $default Hardcoded default URL.
 */
function sp_field_url( $name, $default = '#' ) {
	echo esc_url( (string) sp_field( $name, $default ) );
}

/**
 * Echo a three-tier field, escaped for HTML attributes.
 *
 * @param string $name    ACF field name.
 * @param string $default Hardcoded default.
 */
function sp_field_attr( $name, $default = '' ) {
	echo esc_attr( (string) sp_field( $name, $default ) );
}

/**
 * Resolve an ACF image field to its URL, with a hardcoded fallback.
 *
 * Handles ACF image fields regardless of return_format (array | url | id).
 *
 * @param string     $name    ACF field name.
 * @param string     $default Fallback URL if the field is empty.
 * @param int|string $post_id Post ID or 'option'. Defaults to the current post.
 * @return string
 */
function sp_image_url( $name, $default = '', $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$img = get_field( $name, $post_id );
	if ( is_array( $img ) && ! empty( $img['url'] ) ) {
		return $img['url'];
	}
	if ( is_string( $img ) && $img !== '' ) {
		return $img;
	}
	if ( is_int( $img ) && $img > 0 ) {
		$src = wp_get_attachment_image_url( $img, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	return $default;
}

/**
 * Resolve the alt text for an ACF image field, with a fallback.
 *
 * @param string     $name    ACF field name.
 * @param string     $default Fallback alt string.
 * @param int|string $post_id Post ID or 'option'. Defaults to the current post.
 * @return string
 */
function sp_image_alt( $name, $default = '', $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$img = get_field( $name, $post_id );
	if ( is_array( $img ) ) {
		if ( ! empty( $img['alt'] ) ) {
			return $img['alt'];
		}
		if ( ! empty( $img['title'] ) ) {
			return $img['title'];
		}
	}
	return $default;
}

/**
 * Render an inline SVG icon from a predefined set.
 *
 * Keeps the bundle small (no icon font, no sprite fetch) and lets ACF
 * select fields pick icons by key. Unknown keys render nothing.
 *
 * @param string $key         Icon key (see list below).
 * @param string $class_attr  Classes applied to the <svg> element.
 * @param string $title       Optional <title> for accessibility. Empty = aria-hidden.
 * @return string SVG markup (trusted, static, safe to echo unescaped).
 */
function sp_icon( $key, $class_attr = 'w-5 h-5', $title = '' ) {
	$icons = array(
		// Star (solid) — rating badge.
		'star'        => '<path d="M11.48 3.5l2.36 4.78 5.27.77-3.81 3.71.9 5.25L11.48 15.6 6.77 18l.9-5.25L3.86 9.05l5.27-.77z" fill="currentColor"/>',
		// UK regulated shield / checkmark shield.
		'shield'      => '<path d="M12 3l7 3v6c0 4.97-3.4 8.5-7 9-3.6-.5-7-4.03-7-9V6l7-3z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		// 100% secure / padlock.
		'lock'        => '<rect x="5" y="11" width="14" height="10" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M8 11V8a4 4 0 018 0v3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		// Checkmark.
		'check'       => '<path d="M5 13l4 4L19 7" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
		// Delivery truck.
		'truck'       => '<path d="M3 7h11v10H3zM14 10h4l3 3v4h-7z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="7" cy="18" r="2" fill="currentColor"/><circle cx="17" cy="18" r="2" fill="currentColor"/>',
		// Weight management (person silhouette).
		'person'      => '<circle cx="12" cy="5" r="3" fill="currentColor"/><path d="M6 22v-6a6 6 0 0112 0v6z" fill="currentColor"/>',
		// HRT / clipboard.
		'clipboard'   => '<rect x="5" y="3" width="14" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M9 8h6M9 12h6M9 16h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		// Hair loss / arch.
		'hair'        => '<path d="M12 2C12 2 8 6 8 10a4 4 0 008 0c0-4-4-8-4-8z" fill="currentColor"/><path d="M8 20v-4a4 4 0 018 0v4z" fill="currentColor"/>',
		// Women's health / medical cross in square.
		'women'       => '<rect x="3" y="3" width="18" height="18" rx="2" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 8v8M8 12h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
		// Men's health / shield + plus.
		'men'         => '<circle cx="12" cy="7" r="4" fill="currentColor"/><path d="M4 22v-2a6 6 0 0116 0v2z" fill="currentColor"/>',
		// Sparkle / star accent.
		'sparkle'     => '<path d="M12 3l2 6 6 2-6 2-2 6-2-6-6-2 6-2z" fill="currentColor"/>',
		// UK flag simplified.
		'uk'          => '<rect x="3" y="5" width="18" height="14" rx="2" fill="#012169"/><path d="M3 5l18 14M21 5L3 19" stroke="#fff" stroke-width="2"/><path d="M12 5v14M3 12h18" stroke="#C8102E" stroke-width="2"/>',
		// Circle check (success).
		'check_circle' => '<circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2"/><path d="M8 12l3 3 5-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
		// Document / prescription.
		'document'    => '<path d="M7 3h8l4 4v14H7z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 3v4h4" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
		// Eye (discreet).
		'eye'         => '<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" fill="currentColor"/>',
	);

	if ( ! isset( $icons[ $key ] ) ) {
		return '';
	}

	$aria = $title ? '' : 'aria-hidden="true"';
	$a11y = $title ? '<title>' . esc_html( $title ) . '</title>' : '';

	return sprintf(
		'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="%s" %s>%s%s</svg>',
		esc_attr( $class_attr ),
		$aria,
		$a11y,
		$icons[ $key ]
	);
}

/**
 * Icon choices for ACF select fields.
 *
 * @return array<string,string>
 */
function sp_icon_choices() {
	return array(
		''             => '— None —',
		'star'         => 'Star',
		'shield'       => 'Shield (UK regulated)',
		'lock'         => 'Lock (secure)',
		'check'        => 'Checkmark',
		'check_circle' => 'Check in circle',
		'truck'        => 'Delivery truck',
		'person'       => 'Person (weight / general)',
		'clipboard'    => 'Clipboard (HRT / form)',
		'hair'         => 'Hair',
		'women'        => "Women's health",
		'men'          => "Men's health",
		'sparkle'      => 'Sparkle',
		'uk'           => 'UK flag',
		'document'     => 'Document / prescription',
		'eye'          => 'Eye (discreet)',
	);
}
