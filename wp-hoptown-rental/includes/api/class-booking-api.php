<?php
/**
 * REST API endpoints for booking
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/api
 */

class Hoptown_Rental_Booking_API {

	/**
	 * Register REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			'hoptown-rental/v1',
			'/availability/(?P<inflatable_id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_availability' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'inflatable_id' => array(
						'validate_callback' => function( $param ) {
							return is_numeric( $param );
						},
					),
				),
			)
		);

		register_rest_route(
			'hoptown-rental/v1',
			'/price/(?P<inflatable_id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_price' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'inflatable_id' => array(
						'validate_callback' => function( $param ) {
							return is_numeric( $param );
						},
					),
					'date'          => array(
						'required'          => true,
						'validate_callback' => function( $param ) {
							return (bool) strtotime( $param );
						},
					),
				),
			)
		);
	}

	/**
	 * Get availability for an inflatable.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function get_availability( $request ) {
		$inflatable_id = $request->get_param( 'inflatable_id' );

		// Check if inflatable exists
		if ( ! get_post( $inflatable_id ) || Hoptown_Rental_Inflatable_Post_Type::POST_TYPE !== get_post_type( $inflatable_id ) ) {
			return new WP_REST_Response(
				array(
					'error' => __( 'Inflatable not found', 'hoptown-rental' ),
				),
				404
			);
		}

		// Get booked dates
		$booked_dates = Hoptown_Rental_Booking_Post_Type::get_booked_dates( $inflatable_id );

		return new WP_REST_Response(
			array(
				'inflatable_id' => $inflatable_id,
				'booked_dates'  => $booked_dates,
			),
			200
		);
	}

	/**
	 * Get price for a specific date.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response Response object.
	 */
	public function get_price( $request ) {
		$inflatable_id = $request->get_param( 'inflatable_id' );
		$date          = $request->get_param( 'date' );

		// Check if inflatable exists
		if ( ! get_post( $inflatable_id ) || Hoptown_Rental_Inflatable_Post_Type::POST_TYPE !== get_post_type( $inflatable_id ) ) {
			return new WP_REST_Response(
				array(
					'error' => __( 'Inflatable not found', 'hoptown-rental' ),
				),
				404
			);
		}

		// Check if date is available
		$is_available = Hoptown_Rental_Inflatable_Post_Type::is_available( $inflatable_id, $date );

		if ( ! $is_available ) {
			return new WP_REST_Response(
				array(
					'error'     => __( 'Date not available', 'hoptown-rental' ),
					'available' => false,
				),
				200
			);
		}

		// Get pricing
		$rental_price   = Hoptown_Rental_Inflatable_Post_Type::get_price_for_date( $inflatable_id, $date );
		$delivery_price = get_post_meta( $inflatable_id, '_hoptown_delivery_price', true );

		return new WP_REST_Response(
			array(
				'inflatable_id'  => $inflatable_id,
				'date'           => $date,
				'available'      => true,
				'rental_price'   => floatval( $rental_price ),
				'delivery_price' => floatval( $delivery_price ),
			),
			200
		);
	}
}
