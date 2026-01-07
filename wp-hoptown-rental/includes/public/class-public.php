<?php
/**
 * Public-facing functionality
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/public
 */

class Hoptown_Rental_Public {

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
	 * Register the stylesheets for the public-facing side.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			HOPTOWN_RENTAL_PLUGIN_URL . 'assets/css/public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			HOPTOWN_RENTAL_PLUGIN_URL . 'assets/js/booking.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		$month_names = array();
		for ( $month = 1; $month <= 12; $month++ ) {
			$month_names[] = wp_date( 'F', mktime( 0, 0, 0, $month, 1, 2020 ) );
		}

		$weekday_names = array();
		for ( $weekday = 0; $weekday < 7; $weekday++ ) {
			$weekday_names[] = wp_date( 'D', strtotime( "monday +{$weekday} days" ) );
		}

		// Localize script with API endpoint and nonce
		wp_localize_script(
			$this->plugin_name,
			'hoptownRental',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'restUrl'   => rest_url( 'hoptown-rental/v1' ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'ajaxNonce' => wp_create_nonce( 'hoptown_booking' ),
				'i18n'      => array(
					'monthNames'       => $month_names,
					'weekdayNames'     => $weekday_names,
					'dateNotAvailable' => __( 'This date is not available.', 'hoptown-rental' ),
					'pricingError'     => __( 'Error fetching pricing information.', 'hoptown-rental' ),
					'submitError'      => __( 'An error occurred. Please try again.', 'hoptown-rental' ),
					'submitting'       => __( 'Submitting...', 'hoptown-rental' ),
					'reserve'          => __( 'Reserve', 'hoptown-rental' ),
					'selectDate'       => __( 'Please select a date from the calendar', 'hoptown-rental' ),
				),
			)
		);
	}

	/**
	 * Render booking calendar shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_booking_calendar( $atts ) {
		$atts = shortcode_atts(
			array(
				'inflatable_id' => 0,
			),
			$atts,
			'hoptown_booking_calendar'
		);

		$inflatable_id = intval( $atts['inflatable_id'] );

		if ( ! $inflatable_id ) {
			return '<p>' . esc_html__( 'Please provide an inflatable ID.', 'hoptown-rental' ) . '</p>';
		}

		// Get booked dates
		$booked_dates = Hoptown_Rental_Booking_Post_Type::get_booked_dates( $inflatable_id );

		ob_start();
		include HOPTOWN_RENTAL_PLUGIN_DIR . 'templates/booking-calendar.php';
		return ob_get_clean();
	}

	/**
	 * Render booking form shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_booking_form( $atts ) {
		$atts = shortcode_atts(
			array(
				'inflatable_id' => 0,
			),
			$atts,
			'hoptown_booking_form'
		);

		$inflatable_id = intval( $atts['inflatable_id'] );

		if ( ! $inflatable_id ) {
			return '<p>' . esc_html__( 'Please provide an inflatable ID.', 'hoptown-rental' ) . '</p>';
		}

		// Get inflatable details
		$inflatable     = get_post( $inflatable_id );
		$base_price     = get_post_meta( $inflatable_id, '_hoptown_base_price', true );
		$delivery_price = get_post_meta( $inflatable_id, '_hoptown_delivery_price', true );

		ob_start();
		include HOPTOWN_RENTAL_PLUGIN_DIR . 'templates/booking-form.php';
		return ob_get_clean();
	}
}
