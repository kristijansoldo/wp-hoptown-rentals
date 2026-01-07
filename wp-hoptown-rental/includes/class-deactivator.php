<?php
/**
 * Fired during plugin deactivation
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes
 */

class Hoptown_Rental_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Flush rewrite rules.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
