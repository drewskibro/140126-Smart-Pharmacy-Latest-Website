<?php
/**
 * Helpers shared across the plugin.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generate a v4 UUID (RFC 4122) for assessment_id.
 *
 * Used as the externally-visible primary key for assessments so URLs
 * and audit logs don't leak the internal auto-increment ID.
 *
 * @return string UUID v4.
 */
function spe_uuid_v4() {
	$data = random_bytes( 16 );
	$data[6] = chr( ( ord( $data[6] ) & 0x0f ) | 0x40 );
	$data[8] = chr( ( ord( $data[8] ) & 0x3f ) | 0x80 );
	return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );
}

/**
 * Wrapper around get_option with a default — keeps option keys consistent.
 *
 * @param string $key     Option key (without spe_ prefix).
 * @param mixed  $default Default if not set.
 * @return mixed
 */
function spe_option( $key, $default = null ) {
	return get_option( 'spe_' . $key, $default );
}

/**
 * Cookie name used to bridge a partial assessment across the form.
 */
function spe_cookie_name() {
	return 'tc_eligibility_data';
}
