<?php
/**
 * Booking Form Template
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="hoptown-booking-form-wrapper">
	<form id="hoptown-booking-form" class="hoptown-booking-form" data-inflatable-id="<?php echo esc_attr( $inflatable_id ); ?>">
		<input type="hidden" name="inflatable_id" value="<?php echo esc_attr( $inflatable_id ); ?>" />
		<input type="hidden" name="booking_date" id="hoptown-booking-date" value="" />

		<div class="hoptown-form-section">
			<h3><?php esc_html_e( 'Selected Date', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></h3>
			<p class="hoptown-selected-date"><?php esc_html_e( 'Please select a date from the calendar', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></p>
		</div>

		<div class="hoptown-form-section hoptown-customer-info" style="display: none;">
			<h3><?php esc_html_e( 'Your Information', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></h3>

			<div class="hoptown-form-group">
				<label for="hoptown-customer-name"><?php esc_html_e( 'Name and Surname', HOPTOWN_RENTAL_TEXTDOMAIN ); ?> *</label>
				<input type="text" id="hoptown-customer-name" name="customer_name" required />
			</div>

			<div class="hoptown-form-group">
				<label for="hoptown-customer-email"><?php esc_html_e( 'Email', HOPTOWN_RENTAL_TEXTDOMAIN ); ?> *</label>
				<input type="email" id="hoptown-customer-email" name="customer_email" required />
			</div>

			<div class="hoptown-form-group">
				<label for="hoptown-customer-phone"><?php esc_html_e( 'Phone', HOPTOWN_RENTAL_TEXTDOMAIN ); ?> *</label>
				<input type="tel" id="hoptown-customer-phone" name="customer_phone" required />
			</div>

			<div class="hoptown-form-group">
				<label for="hoptown-customer-note"><?php esc_html_e( 'Note', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></label>
				<textarea id="hoptown-customer-note" name="customer_note" rows="3"></textarea>
			</div>
		</div>

		<div class="hoptown-form-section hoptown-delivery-section" style="display: none;">
			<h3><?php esc_html_e( 'Delivery Method', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></h3>

			<div class="hoptown-form-group">
				<label>
					<input type="radio" name="delivery_method" value="pickup" required />
					<?php esc_html_e( 'Pickup', HOPTOWN_RENTAL_TEXTDOMAIN ); ?>
				</label>
			</div>

			<div class="hoptown-pickup-fields" style="display: none;">
				<div class="hoptown-form-group">
					<label for="hoptown-pickup-time"><?php esc_html_e( 'Pickup Time (24h)', HOPTOWN_RENTAL_TEXTDOMAIN ); ?> *</label>
					<input type="text" id="hoptown-pickup-time" name="pickup_time" placeholder="HH:MM" inputmode="numeric" pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]$" />
				</div>
			</div>

			<div class="hoptown-form-group">
				<label>
					<input type="radio" name="delivery_method" value="delivery" />
					<?php esc_html_e( 'Delivery', HOPTOWN_RENTAL_TEXTDOMAIN ); ?>
					<?php if ( $delivery_price ) : ?>
						(+<?php echo esc_html( ( new Hoptown_Rental_Money( $delivery_price ) )->format() ); ?>)
					<?php endif; ?>
				</label>
			</div>

			<div class="hoptown-delivery-fields" style="display: none;">
				<div class="hoptown-form-group">
					<label for="hoptown-delivery-address"><?php esc_html_e( 'Delivery Address', HOPTOWN_RENTAL_TEXTDOMAIN ); ?> *</label>
					<input type="text" id="hoptown-delivery-address" name="delivery_address" />
				</div>
			</div>
		</div>

		<div class="hoptown-form-section hoptown-pricing-info" style="display: none;">
			<h3><?php esc_html_e( 'Pricing', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></h3>
			<table class="hoptown-pricing-table">
				<tr>
					<td><?php esc_html_e( 'Rental Price:', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></td>
					<td class="hoptown-rental-price">-</td>
				</tr>
				<tr class="hoptown-delivery-price-row" style="display: none;">
					<td><?php esc_html_e( 'Delivery Price:', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></td>
					<td class="hoptown-delivery-price">-</td>
				</tr>
				<tr class="hoptown-total-price-row">
					<td><strong><?php esc_html_e( 'Total:', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></strong></td>
					<td><strong class="hoptown-total-price">-</strong></td>
				</tr>
			</table>
		</div>

		<div class="hoptown-form-section hoptown-submit-section" style="display: none;">
			<button type="submit" class="hoptown-submit-booking"><?php esc_html_e( 'Reserve', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></button>
		</div>

		<div class="hoptown-form-messages"></div>
	</form>
</div>
