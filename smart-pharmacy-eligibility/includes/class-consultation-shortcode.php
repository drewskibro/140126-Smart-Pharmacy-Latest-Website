<?php
/**
 * [smart_pharmacy_consultation] shortcode.
 *
 * Renders the single-page P-med consultation form built from the
 * editable base question set. Lighter sibling of the GLP-1 checker:
 * same plumbing (assets enqueued only when present, AJAX + nonce via
 * wp_localize_script), simpler UI.
 *
 * Product context: the "Start Consultation" CTA on a P-med product can
 * pass the product id either as a shortcode attribute
 * ([smart_pharmacy_consultation product="123"]) or as a `product`
 * query arg on the consultation page URL. It's captured with the
 * submission so later cards can add the right SKU to the basket.
 *
 * @package SmartPharmacyEligibility
 */

defined( 'ABSPATH' ) || exit;

/**
 * SPE_Consultation_Shortcode.
 */
class SPE_Consultation_Shortcode {

	const TAG = 'smart_pharmacy_consultation';

	/**
	 * Register the shortcode.
	 */
	public static function register() {
		add_shortcode( self::TAG, array( __CLASS__, 'render' ) );
	}

	/**
	 * Render the form.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function render( $atts = array() ) {
		$atts = shortcode_atts( array( 'product' => 0 ), $atts, self::TAG );

		$product_id = (int) $atts['product'];
		// Read the product id from the query string. Use `spe_product`, NOT
		// `product` -- `product` is a reserved WooCommerce query var and
		// hijacks the page ("Nothing found"). `product` kept only as a
		// legacy fallback for any old links.
		if ( ! $product_id && isset( $_GET['spe_product'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product_id = absint( wp_unslash( $_GET['spe_product'] ) );
		}
		if ( ! $product_id && isset( $_GET['product'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product_id = absint( wp_unslash( $_GET['product'] ) );
		}

		self::enqueue_assets();

		$questions = SPE_Consultation_Questions::get_questions( array( 'product_id' => $product_id ) );
		$intro     = SPE_Consultation_Questions::get_copy( 'intro' );
		$disclaimer = SPE_Consultation_Questions::get_copy( 'disclaimer' );

		ob_start();
		include SPE_PLUGIN_DIR . 'templates/consultation-form.php';
		return ob_get_clean();
	}

	/**
	 * Enqueue CSS + JS and hand the AJAX endpoint + nonce to the script.
	 */
	protected static function enqueue_assets() {
		wp_enqueue_style(
			'spe-fonts',
			'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700&display=swap',
			array(),
			null
		);

		wp_enqueue_style(
			'spe-consultation',
			SPE_PLUGIN_URL . 'assets/css/consultation.css',
			array(),
			SPE_VERSION
		);

		wp_enqueue_script(
			'spe-consultation',
			SPE_PLUGIN_URL . 'assets/js/consultation.js',
			array(),
			SPE_VERSION,
			true
		);

		wp_localize_script(
			'spe-consultation',
			'SPE_CONSULT_CONFIG',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => SPE_Consultation_Ajax::ACTION,
				'nonce'   => wp_create_nonce( SPE_Consultation_Ajax::NONCE_ACTION ),
			)
		);
	}
}
