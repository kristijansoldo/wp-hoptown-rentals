<?php
/**
 * Inflatable service.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/services
 */

class Hoptown_Rental_Inflatable_Service {
	/**
	 * Save inflatable meta from admin form.
	 *
	 * @param int   $post_id Inflatable post ID.
	 * @param array $data    Raw data.
	 */
	public static function save_from_admin( $post_id, $data ) {
		if ( isset( $data['hoptown_base_price'] ) ) {
			Hoptown_Rental_Meta::update( $post_id, Hoptown_Rental_Meta::INFLATABLE_BASE_PRICE, $data['hoptown_base_price'], 'sanitize_text_field' );
		}

		if ( isset( $data['hoptown_use_day_pricing'] ) ) {
			Hoptown_Rental_Meta::update( $post_id, Hoptown_Rental_Meta::INFLATABLE_USE_DAY_PRICING, 'yes' );
		} else {
			Hoptown_Rental_Meta::update( $post_id, Hoptown_Rental_Meta::INFLATABLE_USE_DAY_PRICING, 'no' );
		}

		if ( isset( $data['hoptown_weekday_price'] ) ) {
			Hoptown_Rental_Meta::update( $post_id, Hoptown_Rental_Meta::INFLATABLE_WEEKDAY_PRICE, $data['hoptown_weekday_price'], 'sanitize_text_field' );
		}

		if ( isset( $data['hoptown_weekend_price'] ) ) {
			Hoptown_Rental_Meta::update( $post_id, Hoptown_Rental_Meta::INFLATABLE_WEEKEND_PRICE, $data['hoptown_weekend_price'], 'sanitize_text_field' );
		}

		if ( isset( $data['hoptown_delivery_price'] ) ) {
			Hoptown_Rental_Meta::update( $post_id, Hoptown_Rental_Meta::INFLATABLE_DELIVERY_PRICE, $data['hoptown_delivery_price'], 'sanitize_text_field' );
		}

		if ( isset( $data['hoptown_gallery'] ) ) {
			Hoptown_Rental_Meta::update( $post_id, Hoptown_Rental_Meta::INFLATABLE_GALLERY, $data['hoptown_gallery'], 'sanitize_text_field' );
		}
	}
}
