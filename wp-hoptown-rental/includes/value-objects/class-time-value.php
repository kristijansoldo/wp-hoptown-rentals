<?php
/**
 * Time value object (HH:MM).
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/value-objects
 */

class Hoptown_Rental_Time_Value {
	private $time_string;

	private function __construct( $time_string ) {
		$this->time_string = $time_string;
	}

	public static function from_hhmm( $time_string ) {
		return new self( $time_string );
	}

	public static function is_valid( $time_string ) {
		return (bool) preg_match( '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time_string );
	}

	public function raw() {
		return $this->time_string;
	}
}
