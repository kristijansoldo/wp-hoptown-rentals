<?php
/**
 * Inflatable data object.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes
 */

class Hoptown_Rental_Inflatable {
	public $id = 0;
	public $base_price;
	public $use_day_pricing;
	public $weekday_price;
	public $weekend_price;
	public $delivery_price;
	public $gallery_ids = array();

	/**
	 * Load inflatable by ID.
	 *
	 * @param int $inflatable_id Inflatable post ID.
	 * @return self
	 */
	public static function from_id( $inflatable_id ) {
		$inflatable              = new self();
		$inflatable->id           = (int) $inflatable_id;
		$meta                    = Hoptown_Rental_Inflatable_Repository::get_meta( $inflatable_id );
		$inflatable->base_price  = $meta['base_price'];
		$inflatable->use_day_pricing = $meta['use_day_pricing'];
		$inflatable->weekday_price   = $meta['weekday_price'];
		$inflatable->weekend_price   = $meta['weekend_price'];
		$inflatable->delivery_price  = $meta['delivery_price'];

		$gallery_ids = $meta['gallery_ids'];
		if ( ! empty( $gallery_ids ) ) {
			$inflatable->gallery_ids = array_filter( array_map( 'trim', explode( ',', $gallery_ids ) ) );
		}

		return $inflatable;
	}
}
