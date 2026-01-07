<?php
/**
 * Admin-specific functionality
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/admin
 */

class Hoptown_Rental_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version
	 */
	private $version;

	/**
	 * Initialize the class.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		// Only load on our post types
		if ( in_array( $screen->post_type, array( Hoptown_Rental_Inflatable_Post_Type::POST_TYPE, Hoptown_Rental_Booking_Post_Type::POST_TYPE ), true ) ) {
			wp_enqueue_style(
				$this->plugin_name,
				HOPTOWN_RENTAL_PLUGIN_URL . 'assets/css/admin.css',
				array(),
				$this->version,
				'all'
			);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		// Only load on our post types
		if ( in_array( $screen->post_type, array( Hoptown_Rental_Inflatable_Post_Type::POST_TYPE, Hoptown_Rental_Booking_Post_Type::POST_TYPE ), true ) ) {
			// Enqueue WordPress media library
			wp_enqueue_media();

			wp_enqueue_script(
				$this->plugin_name,
				HOPTOWN_RENTAL_PLUGIN_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				$this->version,
				true
			);
		}
	}
}
