<?php
/**
 * Fired during plugin activation
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes
 */

class Hoptown_Rental_Activator {

	/**
	 * Activate the plugin.
	 *
	 * Register post types and flush rewrite rules.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Register custom post types
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . 'includes/post-types/class-inflatable-post-type.php';
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . 'includes/post-types/class-booking-post-type.php';

		$inflatable_cpt = new Hoptown_Rental_Inflatable_Post_Type();
		$inflatable_cpt->register_post_type();

		$booking_cpt = new Hoptown_Rental_Booking_Post_Type();
		$booking_cpt->register_post_type();

		// Flush rewrite rules
		flush_rewrite_rules();
	}
}
