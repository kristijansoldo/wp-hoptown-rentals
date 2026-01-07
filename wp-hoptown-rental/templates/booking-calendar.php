<?php
/**
 * Booking Calendar Template
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php
$weekday_names = array();
for ( $weekday = 0; $weekday < 7; $weekday++ ) {
	$weekday_names[] = wp_date( 'D', strtotime( "monday +{$weekday} days" ) );
}
?>

<div class="hoptown-booking-calendar" data-inflatable-id="<?php echo esc_attr( $inflatable_id ); ?>" data-booked-dates="<?php echo esc_attr( wp_json_encode( $booked_dates ) ); ?>">
	<div class="hoptown-calendar-header">
		<button type="button" class="hoptown-calendar-prev">&laquo;</button>
		<h3 class="hoptown-calendar-month"></h3>
		<button type="button" class="hoptown-calendar-next">&raquo;</button>
	</div>
	<div class="hoptown-calendar-body">
		<div class="hoptown-calendar-weekdays">
			<?php foreach ( $weekday_names as $weekday_label ) : ?>
				<div class="hoptown-calendar-weekday"><?php echo esc_html( $weekday_label ); ?></div>
			<?php endforeach; ?>
		</div>
		<div class="hoptown-calendar-days"></div>
	</div>
	<div class="hoptown-calendar-legend">
		<div class="hoptown-legend-item">
			<span class="hoptown-legend-color hoptown-legend-available"></span>
			<span><?php esc_html_e( 'Available', 'hoptown-rental' ); ?></span>
		</div>
		<div class="hoptown-legend-item">
			<span class="hoptown-legend-color hoptown-legend-booked"></span>
			<span><?php esc_html_e( 'Booked', 'hoptown-rental' ); ?></span>
		</div>
		<div class="hoptown-legend-item">
			<span class="hoptown-legend-color hoptown-legend-selected"></span>
			<span><?php esc_html_e( 'Selected', 'hoptown-rental' ); ?></span>
		</div>
	</div>
</div>
