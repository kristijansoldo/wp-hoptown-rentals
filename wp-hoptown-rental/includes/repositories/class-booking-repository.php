<?php
/**
 * Booking repository.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/repositories
 */

class Hoptown_Rental_Booking_Repository {
	public static function get_meta( $booking_id ) {
		return array(
			'inflatable_id'    => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_INFLATABLE_ID ),
			'booking_date'     => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_DATE ),
			'customer_name'    => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_NAME ),
			'customer_email'   => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_EMAIL ),
			'customer_phone'   => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_PHONE ),
			'customer_note'    => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_NOTE ),
			'delivery_method'  => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_DELIVERY_METHOD ),
			'delivery_address' => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_DELIVERY_ADDRESS ),
			'pickup_time'      => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_PICKUP_TIME ),
			'rental_price'     => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_RENTAL_PRICE ),
			'delivery_price'   => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_DELIVERY_PRICE ),
			'total_price'      => Hoptown_Rental_Meta::get( $booking_id, Hoptown_Rental_Meta::BOOKING_TOTAL_PRICE ),
		);
	}

	public static function create_post( $title ) {
		$post_data = array(
			'post_title'  => $title,
			'post_type'   => Hoptown_Rental_Booking_Post_Type::POST_TYPE,
			'post_status' => 'publish',
		);

		return wp_insert_post( $post_data );
	}

	public static function update_title( $booking_id, $title ) {
		return wp_update_post(
			array(
				'ID'         => $booking_id,
				'post_title' => $title,
			)
		);
	}

	public static function update_meta( $booking_id, $data ) {
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_INFLATABLE_ID, $data['inflatable_id'], 'sanitize_text_field' );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_DATE, $data['booking_date'], 'sanitize_text_field' );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_NAME, $data['customer_name'], 'sanitize_text_field' );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_EMAIL, $data['customer_email'], 'sanitize_email' );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_PHONE, $data['customer_phone'], 'sanitize_text_field' );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_DELIVERY_METHOD, $data['delivery_method'], 'sanitize_text_field' );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_DELIVERY_ADDRESS, $data['delivery_address'], 'sanitize_text_field' );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_PICKUP_TIME, $data['pickup_time'], 'sanitize_text_field' );

		if ( ! empty( $data['customer_note'] ) ) {
			Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_NOTE, $data['customer_note'], 'sanitize_textarea_field' );
		} else {
			Hoptown_Rental_Meta::delete( $booking_id, Hoptown_Rental_Meta::BOOKING_CUSTOMER_NOTE );
		}

		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_RENTAL_PRICE, $data['rental_price'] );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_DELIVERY_PRICE, $data['delivery_price'] );
		Hoptown_Rental_Meta::update( $booking_id, Hoptown_Rental_Meta::BOOKING_TOTAL_PRICE, $data['total_price'] );
	}

	/**
	 * Get booked dates for an inflatable.
	 *
	 * @param int $inflatable_id Inflatable post ID.
	 * @return array
	 */
	public static function get_booked_dates( $inflatable_id ) {
		$bookings = new WP_Query(
			array(
				'post_type'      => Hoptown_Rental_Booking_Post_Type::POST_TYPE,
				'post_status'    => array( 'publish', 'pending' ),
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => Hoptown_Rental_Meta::BOOKING_INFLATABLE_ID,
						'value'   => $inflatable_id,
						'compare' => '=',
					),
				),
			)
		);

		$booked_dates = array();

		if ( $bookings->have_posts() ) {
			while ( $bookings->have_posts() ) {
				$bookings->the_post();
				$booking_date = Hoptown_Rental_Meta::get( get_the_ID(), Hoptown_Rental_Meta::BOOKING_DATE );
				if ( $booking_date ) {
					$booked_dates[] = $booking_date;
				}
			}
		}

		wp_reset_postdata();

		return $booked_dates;
	}

	/**
	 * Check if an inflatable is booked on a date.
	 *
	 * @param int    $inflatable_id Inflatable post ID.
	 * @param string $date          Date in Y-m-d format.
	 * @return bool
	 */
	public static function is_booked_on_date( $inflatable_id, $date ) {
		$bookings = new WP_Query(
			array(
				'post_type'      => Hoptown_Rental_Booking_Post_Type::POST_TYPE,
				'post_status'    => array( 'publish', 'pending' ),
				'posts_per_page' => 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => Hoptown_Rental_Meta::BOOKING_INFLATABLE_ID,
						'value'   => $inflatable_id,
						'compare' => '=',
					),
					array(
						'key'     => Hoptown_Rental_Meta::BOOKING_DATE,
						'value'   => $date,
						'compare' => '=',
					),
				),
			)
		);

		$is_booked = $bookings->have_posts();
		wp_reset_postdata();

		return $is_booked;
	}
}
