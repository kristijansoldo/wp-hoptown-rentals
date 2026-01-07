<?php
/**
 * Meta keys and helpers.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes
 */

class Hoptown_Rental_Meta {
	// Inflatable meta keys.
	const INFLATABLE_BASE_PRICE    = '_hoptown_base_price';
	const INFLATABLE_WEEKEND_PRICE = '_hoptown_weekend_price';
	const INFLATABLE_WEEKDAY_PRICE = '_hoptown_weekday_price';
	const INFLATABLE_USE_DAY_PRICING = '_hoptown_use_day_pricing';
	const INFLATABLE_DELIVERY_PRICE = '_hoptown_delivery_price';
	const INFLATABLE_GALLERY         = '_hoptown_gallery';

	// Booking meta keys.
	const BOOKING_INFLATABLE_ID   = '_hoptown_inflatable_id';
	const BOOKING_DATE            = '_hoptown_booking_date';
	const BOOKING_CUSTOMER_NAME   = '_hoptown_customer_name';
	const BOOKING_CUSTOMER_EMAIL  = '_hoptown_customer_email';
	const BOOKING_CUSTOMER_PHONE  = '_hoptown_customer_phone';
	const BOOKING_CUSTOMER_NOTE   = '_hoptown_customer_note';
	const BOOKING_DELIVERY_METHOD = '_hoptown_delivery_method';
	const BOOKING_DELIVERY_ADDRESS = '_hoptown_delivery_address';
	const BOOKING_PICKUP_TIME     = '_hoptown_pickup_time';
	const BOOKING_RENTAL_PRICE    = '_hoptown_rental_price';
	const BOOKING_DELIVERY_PRICE  = '_hoptown_delivery_price';
	const BOOKING_TOTAL_PRICE     = '_hoptown_total_price';

	/**
	 * Get a meta value.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key.
	 * @return mixed
	 */
	public static function get( $post_id, $key ) {
		return get_post_meta( $post_id, $key, true );
	}

	/**
	 * Update a meta value with optional sanitization.
	 *
	 * @param int      $post_id Post ID.
	 * @param string   $key     Meta key.
	 * @param mixed    $value   Meta value.
	 * @param callable $sanitize Optional sanitize callback.
	 */
	public static function update( $post_id, $key, $value, $sanitize = null ) {
		if ( $sanitize ) {
			$value = call_user_func( $sanitize, $value );
		}

		update_post_meta( $post_id, $key, $value );
	}

	/**
	 * Delete a meta value.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key.
	 */
	public static function delete( $post_id, $key ) {
		delete_post_meta( $post_id, $key );
	}
}
