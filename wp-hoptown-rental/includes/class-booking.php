<?php
/**
 * Booking data object.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes
 */

class Hoptown_Rental_Booking {
	public $id = 0;
	public $inflatable_id;
	public $booking_date;
	public $customer_name;
	public $customer_email;
	public $customer_phone;
	public $customer_note;
	public $delivery_method;
	public $delivery_address;
	public $pickup_time;
	public $rental_price;
	public $delivery_price;
	public $total_price;

	/**
	 * Load booking by ID.
	 *
	 * @param int $booking_id Booking post ID.
	 * @return self
	 */
	public static function from_id( $booking_id ) {
		$booking            = new self();
		$booking->id        = (int) $booking_id;
		$meta               = Hoptown_Rental_Booking_Repository::get_meta( $booking_id );
		$booking->inflatable_id    = $meta['inflatable_id'];
		$booking->booking_date     = $meta['booking_date'];
		$booking->customer_name    = $meta['customer_name'];
		$booking->customer_email   = $meta['customer_email'];
		$booking->customer_phone   = $meta['customer_phone'];
		$booking->customer_note    = $meta['customer_note'];
		$booking->delivery_method  = $meta['delivery_method'];
		$booking->delivery_address = $meta['delivery_address'];
		$booking->pickup_time      = $meta['pickup_time'];
		$booking->rental_price     = $meta['rental_price'];
		$booking->delivery_price   = $meta['delivery_price'];
		$booking->total_price      = $meta['total_price'];

		return $booking;
	}
}
