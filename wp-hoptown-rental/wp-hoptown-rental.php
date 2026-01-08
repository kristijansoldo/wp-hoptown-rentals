<?php
/**
 * Plugin Name: Hoptown Rental
 * Plugin URI: https://hoptown.com
 * Description: Complete inflatable rental management system with booking calendar, pricing rules, and delivery options
 * Version: 1.1.1
 * Author: Hoptown
 * Author URI: https://hoptown.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: hoptown-rental
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'HOPTOWN_RENTAL_VERSION', '1.1.1' );
define( 'HOPTOWN_RENTAL_TEXTDOMAIN', 'hoptown-rental' );
define( 'HOPTOWN_RENTAL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HOPTOWN_RENTAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_hoptown_rental() {
	require_once HOPTOWN_RENTAL_PLUGIN_DIR . 'includes/class-activator.php';
	Hoptown_Rental_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_hoptown_rental() {
	require_once HOPTOWN_RENTAL_PLUGIN_DIR . 'includes/class-deactivator.php';
	Hoptown_Rental_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hoptown_rental' );
register_deactivation_hook( __FILE__, 'deactivate_hoptown_rental' );

/**
 * The core plugin class.
 */
require HOPTOWN_RENTAL_PLUGIN_DIR . 'includes/class-hoptown-rental.php';

/**
 * Begins execution of the plugin.
 */
function run_hoptown_rental() {
	$plugin = new Hoptown_Rental();
	$plugin->run();
}
run_hoptown_rental();
