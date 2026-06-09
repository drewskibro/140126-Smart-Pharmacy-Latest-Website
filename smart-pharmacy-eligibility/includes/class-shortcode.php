<?php
/**
 * [smart_pharmacy_eligibility] shortcode.
 *
 * Renders the multi-screen checker on any WP page. Loads the matching
 * CSS + JS only when the shortcode is present so we don't bloat every
 * page on the site.
 *
 * Usage in admin: just drop [smart_pharmacy_eligibility] into a page.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Shortcode.
 */
class SPE_Shortcode {

	const TAG = 'smart_pharmacy_eligibility';

	/**
	 * Register the shortcode.
	 */
	public static function register() {
		add_shortcode( self::TAG, array( __CLASS__, 'render' ) );
	}

	/**
	 * Render the shortcode output.
	 *
	 * @param array $atts Shortcode attributes (unused for now).
	 * @return string Markup.
	 */
	public static function render( $atts = array() ) {
		self::enqueue_assets();

		ob_start();
		include SPE_PLUGIN_DIR . 'templates/checker.php';
		return ob_get_clean();
	}

	/**
	 * Enqueue CSS + JS and pass the AJAX endpoint + nonce to the JS.
	 *
	 * wp_localize_script is the safe way to inject ajaxurl / nonces
	 * into a vanilla JS file -- avoids string interpolation in PHP
	 * and lets the script work whether it's loaded inline or async.
	 */
	protected static function enqueue_assets() {
		wp_enqueue_style(
			'spe-fonts',
			'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700&display=swap',
			array(),
			null
		);

		wp_enqueue_style(
			'spe-checker',
			SPE_PLUGIN_URL . 'assets/css/eligibility.css',
			array(),
			SPE_VERSION
		);

		wp_enqueue_script(
			'spe-checker',
			SPE_PLUGIN_URL . 'assets/js/eligibility.js',
			array(),
			SPE_VERSION,
			true
		);

		wp_localize_script(
			'spe-checker',
			'SPE_CONFIG',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'tc_eligibility_nonce' ),
				'cookie'   => spe_cookie_name(),
				'shopBase' => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ),
			)
		);
	}
}
