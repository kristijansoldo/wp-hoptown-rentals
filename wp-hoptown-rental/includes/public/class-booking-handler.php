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
			wp_send_json_error( array( 'message' => __( 'Security check failed', 'hoptown-rental' ) ) );
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

		// Validate required fields
		if ( ! $inflatable_id || ! $booking_date || ! $customer_name || ! $customer_email || ! $customer_phone || ! $delivery_method ) {
			wp_send_json_error( array( 'message' => __( 'Please fill all required fields', 'hoptown-rental' ) ) );
		}

		// Validate email
		if ( ! is_email( $customer_email ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid email address', 'hoptown-rental' ) ) );
		}

		// Validate delivery method specific fields
		if ( 'delivery' === $delivery_method && empty( $delivery_address ) ) {
			wp_send_json_error( array( 'message' => __( 'Please provide delivery address', 'hoptown-rental' ) ) );
		}

		if ( 'pickup' === $delivery_method && empty( $pickup_time ) ) {
			wp_send_json_error( array( 'message' => __( 'Please select pickup time', 'hoptown-rental' ) ) );
		}

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

		// Create booking
		$booking_id = Hoptown_Rental_Booking_Post_Type::create_booking( $booking_data );

		if ( is_wp_error( $booking_id ) ) {
			wp_send_json_error(
				array(
					'message' => $booking_id->get_error_message(),
				)
			);
		}

		// Send success response
		wp_send_json_success(
			array(
				'message'    => __( 'Booking submitted successfully!', 'hoptown-rental' ),
				'booking_id' => $booking_id,
			)
		);
	}
}
