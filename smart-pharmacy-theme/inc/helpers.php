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
