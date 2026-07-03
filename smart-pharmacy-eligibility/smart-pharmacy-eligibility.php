<?php
/**
 * Plugin Name:       Smart Pharmacy Eligibility Checker
 * Plugin URI:        https://github.com/drewskibro/140126-Smart-Pharmacy-Latest-Website
 * Description:       Multi-step medical eligibility checker for GLP-1 weight loss treatments (Wegovy / Mounjaro). Captures patient assessment, applies UK clinical eligibility rules, and hands the chosen treatment off to WooCommerce checkout.
 * Version:           0.6.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Smart Pharmacy
 * Author URI:        https://smartpharmacy.co.uk
 * Text Domain:       smart-pharmacy-eligibility
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package SmartPharmacyEligibility
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SPE_VERSION', '0.6.0' );
define( 'SPE_PLUGIN_FILE', __FILE__ );
define( 'SPE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SPE_DB_VERSION', '4' );

require_once SPE_PLUGIN_DIR . 'includes/helpers.php';
require_once SPE_PLUGIN_DIR . 'includes/class-activator.php';
require_once SPE_PLUGIN_DIR . 'includes/class-assessment-repo.php';
require_once SPE_PLUGIN_DIR . 'includes/class-eligibility-rules.php';
require_once SPE_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once SPE_PLUGIN_DIR . 'includes/class-ajax.php';
require_once SPE_PLUGIN_DIR . 'includes/class-woocommerce-integration.php';
require_once SPE_PLUGIN_DIR . 'includes/class-treatment-cards.php';
require_once SPE_PLUGIN_DIR . 'includes/class-admin.php';

// P-med consultation form (editable base questions) — lighter sibling of
// the GLP-1 checker, reusing the same plumbing.
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-questions.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-product-questions.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-repo.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-shortcode.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-ajax.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-admin.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-order.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-checkout.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-review-actions.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-email.php';
require_once SPE_PLUGIN_DIR . 'includes/class-consultation-my-account.php';
require_once SPE_PLUGIN_DIR . 'includes/class-id-upload.php';

// Activation / deactivation.
register_activation_hook( __FILE__, array( 'SPE_Activator', 'activate' ) );

/**
 * Boot the plugin.
 */
function spe_bootstrap() {
	// Create the consultations table on deploys that SCP new files
	// without re-activating the plugin. Cheap no-op once up to date.
	SPE_Activator::maybe_upgrade();

	SPE_Shortcode::register();
	SPE_Ajax::register();
	SPE_WooCommerce_Integration::register();

	SPE_Consultation_Shortcode::register();
	SPE_Consultation_Ajax::register();
	SPE_Consultation_Product_Questions::register();
	SPE_Consultation_Order::register();
	SPE_Consultation_Checkout::register();
	SPE_Consultation_Review_Actions::register();
	SPE_Consultation_Email::register();
	SPE_Consultation_My_Account::register();
	SPE_ID_Upload::register();

	if ( is_admin() ) {
		SPE_Admin::register();
		SPE_Consultation_Admin::register();
	}
}
add_action( 'plugins_loaded', 'spe_bootstrap' );
