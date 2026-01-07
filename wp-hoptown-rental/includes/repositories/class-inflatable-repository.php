<?php
/**
 * Inflatable repository.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/repositories
 */

class Hoptown_Rental_Inflatable_Repository {
	public static function get_meta( $inflatable_id ) {
		return array(
			'base_price'      => Hoptown_Rental_Meta::get( $inflatable_id, Hoptown_Rental_Meta::INFLATABLE_BASE_PRICE ),
			'use_day_pricing' => Hoptown_Rental_Meta::get( $inflatable_id, Hoptown_Rental_Meta::INFLATABLE_USE_DAY_PRICING ),
			'weekday_price'   => Hoptown_Rental_Meta::get( $inflatable_id, Hoptown_Rental_Meta::INFLATABLE_WEEKDAY_PRICE ),
			'weekend_price'   => Hoptown_Rental_Meta::get( $inflatable_id, Hoptown_Rental_Meta::INFLATABLE_WEEKEND_PRICE ),
			'delivery_price'  => Hoptown_Rental_Meta::get( $inflatable_id, Hoptown_Rental_Meta::INFLATABLE_DELIVERY_PRICE ),
			'gallery_ids'     => Hoptown_Rental_Meta::get( $inflatable_id, Hoptown_Rental_Meta::INFLATABLE_GALLERY ),
		);
	}

	public static function get_price_for_date( $inflatable_id, Hoptown_Rental_Date_Value $date ) {
		$meta = self::get_meta( $inflatable_id );
		$base_price = $meta['base_price'];

		if ( empty( $meta['use_day_pricing'] ) || 'yes' !== $meta['use_day_pricing'] ) {
			return (float) $base_price;
		}

		if ( $date->is_weekend() ) {
			return (float) ( $meta['weekend_price'] ? $meta['weekend_price'] : $base_price );
		}

		return (float) ( $meta['weekday_price'] ? $meta['weekday_price'] : $base_price );
	}
}
