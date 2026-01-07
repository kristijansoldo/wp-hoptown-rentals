<?php
/**
 * Booking submission handler
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/public
 */

class Hoptown_Rental_Booking_Handler {

	/**
	 * Handle booking submission via AJAX.
	 */
	public function handle_booking_submission() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'hoptown_booking' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed', HOPTOWN_RENTAL_TEXTDOMAIN ) ) );
		}

		// Get form data
		$inflatable_id    = isset( $_POST['inflatable_id'] ) ? intval( $_POST['inflatable_id'] ) : 0;
		$booking_date     = isset( $_POST['booking_date'] ) ? sanitize_text_field( $_POST['booking_date'] ) : '';
		$customer_name    = isset( $_POST['customer_name'] ) ? sanitize_text_field( $_POST['customer_name'] ) : '';
		$customer_email   = isset( $_POST['customer_email'] ) ? sanitize_email( $_POST['customer_email'] ) : '';
		$customer_phone   = isset( $_POST['customer_phone'] ) ? sanitize_text_field( $_POST['customer_phone'] ) : '';
		$customer_note    = isset( $_POST['customer_note'] ) ? sanitize_textarea_field( $_POST['customer_note'] ) : '';
		$delivery_method  = isset( $_POST['delivery_method'] ) ? sanitize_text_field( $_POST['delivery_method'] ) : '';
		$delivery_address = isset( $_POST['delivery_address'] ) ? sanitize_text_field( $_POST['delivery_address'] ) : '';
		$pickup_time      = isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';

		// Prepare booking data
		$booking_data = array(
			'inflatable_id'    => $inflatable_id,
			'booking_date'     => $booking_date,
			'customer_name'    => $customer_name,
			'customer_email'   => $customer_email,
			'customer_phone'   => $customer_phone,
			'customer_note'    => $customer_note,
			'delivery_method'  => $delivery_method,
			'delivery_address' => $delivery_address,
			'pickup_time'      => $pickup_time,
		);

		$validation = Hoptown_Rental_Booking_Service::validate_data( $booking_data );
		if ( is_wp_error( $validation ) ) {
			wp_send_json_error( array( 'message' => $validation->get_error_message() ) );
		}

		// Create booking
		$booking_id = Hoptown_Rental_Booking_Post_Type::create_booking( $booking_data );

		if ( is_wp_error( $booking_id ) ) {
			wp_send_json_error(
				array(
					'message' => $booking_id->get_error_message(),
				)
			);
		}

		$this->send_booking_notification( $booking_id );

		// Send success response
		wp_send_json_success(
			array(
				'message'    => __( 'Booking submitted successfully!', HOPTOWN_RENTAL_TEXTDOMAIN ),
				'booking_id' => $booking_id,
			)
		);
	}

	/**
	 * Send booking notification email to configured address.
	 *
	 * @param int $booking_id Booking post ID.
	 */
	private function send_booking_notification( $booking_id ) {
		$options            = get_option( 'hoptown_rental_settings', array() );
		$notification_email = isset( $options['notification_email'] ) ? $options['notification_email'] : get_option( 'admin_email' );

		if ( ! $notification_email || ! is_email( $notification_email ) ) {
			return;
		}

		$booking = Hoptown_Rental_Booking::from_id( $booking_id );

		$inflatable_title = $booking->inflatable_id ? get_the_title( $booking->inflatable_id ) : __( 'N/A', HOPTOWN_RENTAL_TEXTDOMAIN );
		$date_display     = $booking->booking_date ? Hoptown_Rental_Date_Value::from_ymd( $booking->booking_date )->format( get_option( 'date_format' ) ) : '';

		$subject = sprintf(
			__( 'New booking: %1$s (%2$s)', HOPTOWN_RENTAL_TEXTDOMAIN ),
			$inflatable_title,
			$date_display
		);

		$delivery_label = $booking->delivery_method;
		if ( 'delivery' === $booking->delivery_method ) {
			$delivery_label = __( 'Delivery', HOPTOWN_RENTAL_TEXTDOMAIN );
		} elseif ( 'pickup' === $booking->delivery_method ) {
			$delivery_label = __( 'Pickup', HOPTOWN_RENTAL_TEXTDOMAIN );
		}

		$lines = array(
			sprintf( __( 'Inflatable: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $inflatable_title ),
			sprintf( __( 'Date: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $date_display ),
			sprintf( __( 'Name: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $booking->customer_name ),
			sprintf( __( 'Email: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $booking->customer_email ),
			sprintf( __( 'Phone: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $booking->customer_phone ),
			sprintf( __( 'Delivery Method: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $delivery_label ),
		);

		if ( 'delivery' === $booking->delivery_method && $booking->delivery_address ) {
			$lines[] = sprintf( __( 'Delivery Address: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $booking->delivery_address );
		}

		if ( 'pickup' === $booking->delivery_method && $booking->pickup_time ) {
			$lines[] = sprintf( __( 'Pickup Time: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $booking->pickup_time );
		}

		if ( $booking->customer_note ) {
			$lines[] = sprintf( __( 'Note: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), $booking->customer_note );
		}

		$lines[] = sprintf( __( 'Rental Price: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), ( new Hoptown_Rental_Money( $booking->rental_price ) )->format() );
		$lines[] = sprintf( __( 'Delivery Price: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), ( new Hoptown_Rental_Money( $booking->delivery_price ) )->format() );
		$lines[] = sprintf( __( 'Total: %s', HOPTOWN_RENTAL_TEXTDOMAIN ), ( new Hoptown_Rental_Money( $booking->total_price ) )->format() );

		wp_mail( $notification_email, $subject, implode( "\n", $lines ) );
	}
}
