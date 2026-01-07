<?php
/**
 * Money value object.
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/value-objects
 */

class Hoptown_Rental_Money {
	private $amount;
	private $currency;

	public function __construct( $amount, $currency = 'EUR' ) {
		$this->amount   = (float) $amount;
		$this->currency = $currency;
	}

	public function add( Hoptown_Rental_Money $other ) {
		return new self( $this->amount + $other->amount, $this->currency );
	}

	public function amount() {
		return $this->amount;
	}

	public function currency() {
		return $this->currency;
	}

	public function format() {
		return number_format( $this->amount, 2 ) . ' €';
	}
}
