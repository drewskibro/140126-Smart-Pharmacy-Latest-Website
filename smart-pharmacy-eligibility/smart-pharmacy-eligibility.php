<?php
/**
 * Plugin Name:       Smart Pharmacy Eligibility Checker
 * Plugin URI:        https://github.com/drewskibro/140126-Smart-Pharmacy-Latest-Website
 * Description:       Multi-step medical eligibility checker for GLP-1 weight loss treatments (Wegovy / Mounjaro). Captures patient assessment, applies UK clinical eligibility rules, and hands the chosen treatment off to WooCommerce checkout.
 * Version:           0.3.2
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

define( 'SPE_VERSION', '0.3.2' );
define( 'SPE_PLUGIN_FILE', __FILE__ );
define( 'SPE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SPE_DB_VERSION', '1' );

require_once SPE_PLUGIN_DIR . 'includes/helpers.php';
require_once SPE_PLUGIN_DIR . 'includes/class-activator.php';
require_once SPE_PLUGIN_DIR . 'includes/class-assessment-repo.php';
require_once SPE_PLUGIN_DIR . 'includes/class-eligibility-rules.php';
require_once SPE_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once SPE_PLUGIN_DIR . 'includes/class-ajax.php';
require_once SPE_PLUGIN_DIR . 'includes/class-woocommerce-integration.php';
require_once SPE_PLUGIN_DIR . 'includes/class-treatment-cards.php';
require_once SPE_PLUGIN_DIR . 'includes/class-admin.php';

// Activation / deactivation.
register_activation_hook( __FILE__, array( 'SPE_Activator', 'activate' ) );

/**
 * Boot the plugin.
 */
function spe_bootstrap() {
	SPE_Shortcode::register();
	SPE_Ajax::register();
	SPE_WooCommerce_Integration::register();

	if ( is_admin() ) {
		SPE_Admin::register();
	}
}
add_action( 'plugins_loaded', 'spe_bootstrap' );
