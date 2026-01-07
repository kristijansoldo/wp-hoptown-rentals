<?php
/**
 * Booking service.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/services
 */

class Hoptown_Rental_Booking_Service {
	private static $is_updating_title = false;
	/**
	 * Create a booking with validation.
	 *
	 * @param array $data Booking data.
	 * @return int|WP_Error
	 */
	public static function create_booking( $data ) {
		$validation = self::validate_data( $data );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		if ( ! Hoptown_Rental_Inflatable_Post_Type::is_available( $data['inflatable_id'], $data['booking_date'] ) ) {
			return new WP_Error( 'not_available', __( 'Inflatable is not available on this date', HOPTOWN_RENTAL_TEXTDOMAIN ) );
		}

		$inflatable_title = get_the_title( $data['inflatable_id'] );
		$date_display     = Hoptown_Rental_Date_Value::from_ymd( $data['booking_date'] )->format( 'd.m.Y' );
		$booking_title    = sprintf(
			'%s - %s - %s',
			$inflatable_title,
			$data['customer_name'],
			$date_display
		);

		$booking_id = Hoptown_Rental_Booking_Repository::create_post( $booking_title );
		if ( is_wp_error( $booking_id ) ) {
			return $booking_id;
		}

		self::update_meta( $booking_id, $data );

		return $booking_id;
	}

	/**
	 * Save booking data from admin.
	 *
	 * @param int   $post_id Booking post ID.
	 * @param array $data    Raw data.
	 */
	public static function save_from_admin( $post_id, $data ) {
		$sanitized = self::sanitize_admin_data( $data );
		self::update_meta( $post_id, $sanitized );

		if ( $sanitized['inflatable_id'] && $sanitized['booking_date'] && $sanitized['customer_name'] ) {
			$inflatable_title = get_the_title( $sanitized['inflatable_id'] );
			$date_display     = Hoptown_Rental_Date_Value::from_ymd( $sanitized['booking_date'] )->format( 'd.m.Y' );
			$booking_title    = sprintf( '%s - %s - %s', $inflatable_title, $sanitized['customer_name'], $date_display );

			if ( ! self::$is_updating_title ) {
				self::$is_updating_title = true;
				Hoptown_Rental_Booking_Repository::update_title( $post_id, $booking_title );
				self::$is_updating_title = false;
			}
		}
	}

	/**
	 * Update booking meta and pricing.
	 *
	 * @param int   $booking_id Booking post ID.
	 * @param array $data       Booking data.
	 */
	private static function update_meta( $booking_id, $data ) {
		$rental_price   = 0;
		$delivery_price = 0;

		if ( $data['inflatable_id'] && $data['booking_date'] ) {
			$date_value  = Hoptown_Rental_Date_Value::from_ymd( $data['booking_date'] );
			$rental_price = Hoptown_Rental_Inflatable_Repository::get_price_for_date( $data['inflatable_id'], $date_value );
			if ( 'delivery' === $data['delivery_method'] ) {
				$delivery_price = Hoptown_Rental_Inflatable_Repository::get_meta( $data['inflatable_id'] )['delivery_price'];
			}
		}

		$total_money = ( new Hoptown_Rental_Money( $rental_price ) )->add( new Hoptown_Rental_Money( $delivery_price ) );

		Hoptown_Rental_Booking_Repository::update_meta(
			$booking_id,
			array(
				'inflatable_id'    => $data['inflatable_id'],
				'booking_date'     => $data['booking_date'],
				'customer_name'    => $data['customer_name'],
				'customer_email'   => $data['customer_email'],
				'customer_phone'   => $data['customer_phone'],
				'customer_note'    => $data['customer_note'],
				'delivery_method'  => $data['delivery_method'],
				'delivery_address' => $data['delivery_address'],
				'pickup_time'      => $data['pickup_time'],
				'rental_price'     => $rental_price,
				'delivery_price'   => $delivery_price,
				'total_price'      => $total_money->amount(),
			)
		);
	}

	/**
	 * Validate booking data for frontend create.
	 *
	 * @param array $data Booking data.
	 * @return true|WP_Error
	 */
	public static function validate_data( $data ) {
		$required = array( 'inflatable_id', 'booking_date', 'customer_name', 'customer_email', 'customer_phone', 'delivery_method' );
		foreach ( $required as $field ) {
			if ( empty( $data[ $field ] ) ) {
				return new WP_Error( 'missing_field', __( 'Please fill all required fields', HOPTOWN_RENTAL_TEXTDOMAIN ) );
			}
		}

		if ( ! is_email( $data['customer_email'] ) ) {
			return new WP_Error( 'invalid_email', __( 'Invalid email address', HOPTOWN_RENTAL_TEXTDOMAIN ) );
		}

		if ( 'delivery' === $data['delivery_method'] && empty( $data['delivery_address'] ) ) {
			return new WP_Error( 'missing_delivery_address', __( 'Please provide delivery address', HOPTOWN_RENTAL_TEXTDOMAIN ) );
		}

		if ( 'pickup' === $data['delivery_method'] && empty( $data['pickup_time'] ) ) {
			return new WP_Error( 'missing_pickup_time', __( 'Please select pickup time', HOPTOWN_RENTAL_TEXTDOMAIN ) );
		}

		if ( 'pickup' === $data['delivery_method'] && ! empty( $data['pickup_time'] ) && ! Hoptown_Rental_Time_Value::is_valid( $data['pickup_time'] ) ) {
			return new WP_Error( 'invalid_pickup_time', __( 'Pickup time must be in HH:MM format', HOPTOWN_RENTAL_TEXTDOMAIN ) );
		}

		return true;
	}

	/**
	 * Sanitize admin form data.
	 *
	 * @param array $data Raw data.
	 * @return array
	 */
	private static function sanitize_admin_data( $data ) {
		return array(
			'inflatable_id'    => isset( $data['hoptown_inflatable_id'] ) ? intval( $data['hoptown_inflatable_id'] ) : 0,
			'booking_date'     => isset( $data['hoptown_booking_date'] ) ? sanitize_text_field( $data['hoptown_booking_date'] ) : '',
			'customer_name'    => isset( $data['hoptown_customer_name'] ) ? sanitize_text_field( $data['hoptown_customer_name'] ) : '',
			'customer_email'   => isset( $data['hoptown_customer_email'] ) ? sanitize_email( $data['hoptown_customer_email'] ) : '',
			'customer_phone'   => isset( $data['hoptown_customer_phone'] ) ? sanitize_text_field( $data['hoptown_customer_phone'] ) : '',
			'customer_note'    => isset( $data['hoptown_customer_note'] ) ? sanitize_textarea_field( $data['hoptown_customer_note'] ) : '',
			'delivery_method'  => isset( $data['hoptown_delivery_method'] ) ? sanitize_text_field( $data['hoptown_delivery_method'] ) : '',
			'delivery_address' => isset( $data['hoptown_delivery_address'] ) ? sanitize_text_field( $data['hoptown_delivery_address'] ) : '',
			'pickup_time'      => isset( $data['hoptown_pickup_time'] ) ? sanitize_text_field( $data['hoptown_pickup_time'] ) : '',
		);
	}
}
