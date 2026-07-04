<?php
/**
 * Brand the WooCommerce transactional emails.
 *
 * Rather than override the email templates (fragile, version-specific),
 * we set WooCommerce's own email settings to the Smart Pharmacy brand.
 * That styles EVERY transactional email through WC's tested templates —
 * order confirmation, dispatch, and the plugin's own consultation
 * emails — consistently:
 *
 *   - teal header band + the site's logo
 *   - light body background, brand text colour
 *   - a branded footer with the GPhC number + pharmacy address
 *
 * The colours/footer are seeded ONCE (so staff can still tweak them in
 * WooCommerce → Settings → Emails afterwards). The header logo is
 * filtered live from the WP custom logo, so it appears as soon as a logo
 * is set in the Customizer.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Email_Branding.
 */
class SPE_Email_Branding {

	const BRAND       = '#10c0a9'; // Smart Pharmacy teal (theme brand.DEFAULT).
	const BRAND_DARK  = '#0da592';
	const SEED_OPTION = 'spe_email_brand_seeded';

	/**
	 * Wire hooks.
	 */
	public static function register() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_seed' ) );
		add_filter( 'woocommerce_email_header_image', array( __CLASS__, 'header_logo' ) );
	}

	/**
	 * Seed the brand email settings once.
	 */
	public static function maybe_seed() {
		if ( get_option( self::SEED_OPTION ) || ! function_exists( 'WC' ) ) {
			return;
		}

		update_option( 'woocommerce_email_base_color', self::BRAND );          // header band + links.
		update_option( 'woocommerce_email_background_color', '#f4f9fa' );       // outer background.
		update_option( 'woocommerce_email_body_background_color', '#ffffff' );  // content card.
		update_option( 'woocommerce_email_text_color', '#374151' );
		update_option( 'woocommerce_email_footer_text', self::footer_text() );

		$logo = self::logo_url();
		if ( $logo && ! get_option( 'woocommerce_email_header_image' ) ) {
			update_option( 'woocommerce_email_header_image', $logo );
		}

		update_option( self::SEED_OPTION, '1' );
	}

	/**
	 * Use the WP custom logo as the email header image when the admin
	 * hasn't set a specific one.
	 *
	 * @param string $image Configured header image URL.
	 * @return string
	 */
	public static function header_logo( $image ) {
		if ( $image ) {
			return $image;
		}
		$logo = self::logo_url();
		return $logo ? $logo : $image;
	}

	/* ---------------------------------------------------------------- */

	/**
	 * WP custom logo URL, or '' if none.
	 *
	 * @return string
	 */
	protected static function logo_url() {
		if ( ! function_exists( 'get_theme_mod' ) ) {
			return '';
		}
		$id = get_theme_mod( 'custom_logo' );
		return $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : '';
	}

	/**
	 * Branded footer text (WC replaces {site_title} / {site_url}).
	 * Pulls the GPhC number + address from the theme's fields when
	 * available, with the known values as a fallback.
	 *
	 * @return string
	 */
	protected static function footer_text() {
		$gphc = function_exists( 'sp_field' )
			? sp_field( 'comp_gphc_number', '9012842' )
			: '9012842';

		$address = function_exists( 'sp_field' )
			? sp_field( 'contact_trading_address', "Smart Pharmacy\nUnit A2 Ivinghoe Business Centre\nLU5 5BQ" )
			: "Unit A2 Ivinghoe Business Centre, LU5 5BQ";
		$address = trim( str_replace( array( "\r\n", "\n" ), ', ', (string) $address ) );

		return '{site_title}' . "\n"
			. sprintf( 'GPhC-registered UK pharmacy — GPhC number %s', $gphc ) . "\n"
			. $address;
	}
}
