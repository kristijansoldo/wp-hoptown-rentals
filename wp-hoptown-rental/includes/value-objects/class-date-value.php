<?php
/**
 * Date value object.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/value-objects
 */

class Hoptown_Rental_Date_Value {
	private $date_string;

	private function __construct( $date_string ) {
		$this->date_string = $date_string;
	}

	public static function from_ymd( $date_string ) {
		return new self( $date_string );
	}

	public function to_timestamp() {
		return strtotime( $this->date_string );
	}

	public function format( $format ) {
		return wp_date( $format, $this->to_timestamp() );
	}

	public function is_weekend() {
		$day_of_week = (int) date( 'N', $this->to_timestamp() );
		return $day_of_week >= 6;
	}

	public function raw() {
		return $this->date_string;
	}
}
