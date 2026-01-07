<?php
/**
 * Meta boxes for Booking post type
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/admin
 */

class Hoptown_Rental_Booking_Meta_Boxes {

	/**
	 * Add meta boxes.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'hoptown_booking_details',
			__( 'Booking Details', 'hoptown-rental' ),
			array( $this, 'render_booking_details_meta_box' ),
			Hoptown_Rental_Booking_Post_Type::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'hoptown_customer_details',
			__( 'Customer Details', 'hoptown-rental' ),
			array( $this, 'render_customer_details_meta_box' ),
			Hoptown_Rental_Booking_Post_Type::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'hoptown_pricing_details',
			__( 'Pricing Details', 'hoptown-rental' ),
			array( $this, 'render_pricing_details_meta_box' ),
			Hoptown_Rental_Booking_Post_Type::POST_TYPE,
			'side',
			'default'
		);
	}

	/**
	 * Render booking details meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_booking_details_meta_box( $post ) {
		$inflatable_id   = get_post_meta( $post->ID, '_hoptown_inflatable_id', true );
		$booking_date    = get_post_meta( $post->ID, '_hoptown_booking_date', true );
		$delivery_method = get_post_meta( $post->ID, '_hoptown_delivery_method', true );
		$pickup_time     = get_post_meta( $post->ID, '_hoptown_pickup_time', true );
		$delivery_address = get_post_meta( $post->ID, '_hoptown_delivery_address', true );

		$inflatable_title = $inflatable_id ? get_the_title( $inflatable_id ) : __( 'N/A', 'hoptown-rental' );
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Inflatable', 'hoptown-rental' ); ?></th>
				<td>
					<strong><?php echo esc_html( $inflatable_title ); ?></strong>
					<?php if ( $inflatable_id ) : ?>
						<br><a href="<?php echo esc_url( get_edit_post_link( $inflatable_id ) ); ?>" target="_blank"><?php esc_html_e( 'Edit Inflatable', 'hoptown-rental' ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Booking Date', 'hoptown-rental' ); ?></th>
				<td><strong><?php echo esc_html( date( 'd.m.Y', strtotime( $booking_date ) ) ); ?></strong></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Delivery Method', 'hoptown-rental' ); ?></th>
				<td>
					<?php
					if ( 'delivery' === $delivery_method ) {
						esc_html_e( 'Delivery', 'hoptown-rental' );
					} else {
						esc_html_e( 'Pickup', 'hoptown-rental' );
					}
					?>
				</td>
			</tr>
			<?php if ( 'delivery' === $delivery_method && $delivery_address ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Delivery Address', 'hoptown-rental' ); ?></th>
				<td><?php echo nl2br( esc_html( $delivery_address ) ); ?></td>
			</tr>
			<?php endif; ?>
			<?php if ( 'pickup' === $delivery_method && $pickup_time ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Pickup Time', 'hoptown-rental' ); ?></th>
				<td><?php echo esc_html( $pickup_time ); ?></td>
			</tr>
			<?php endif; ?>
		</table>
		<?php
	}

	/**
	 * Render customer details meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_customer_details_meta_box( $post ) {
		$customer_name  = get_post_meta( $post->ID, '_hoptown_customer_name', true );
		$customer_email = get_post_meta( $post->ID, '_hoptown_customer_email', true );
		$customer_phone = get_post_meta( $post->ID, '_hoptown_customer_phone', true );
		$customer_note  = get_post_meta( $post->ID, '_hoptown_customer_note', true );
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Name', 'hoptown-rental' ); ?></th>
				<td><?php echo esc_html( $customer_name ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Email', 'hoptown-rental' ); ?></th>
				<td><a href="mailto:<?php echo esc_attr( $customer_email ); ?>"><?php echo esc_html( $customer_email ); ?></a></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Phone', 'hoptown-rental' ); ?></th>
				<td><a href="tel:<?php echo esc_attr( $customer_phone ); ?>"><?php echo esc_html( $customer_phone ); ?></a></td>
			</tr>
			<?php if ( $customer_note ) : ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Note', 'hoptown-rental' ); ?></th>
				<td><?php echo nl2br( esc_html( $customer_note ) ); ?></td>
			</tr>
			<?php endif; ?>
		</table>
		<?php
	}

	/**
	 * Render pricing details meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_pricing_details_meta_box( $post ) {
		$rental_price   = get_post_meta( $post->ID, '_hoptown_rental_price', true );
		$delivery_price = get_post_meta( $post->ID, '_hoptown_delivery_price', true );
		$total_price    = get_post_meta( $post->ID, '_hoptown_total_price', true );
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Rental Price', 'hoptown-rental' ); ?></th>
				<td><?php echo esc_html( number_format( floatval( $rental_price ), 2 ) ); ?> €</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Delivery Price', 'hoptown-rental' ); ?></th>
				<td><?php echo esc_html( number_format( floatval( $delivery_price ), 2 ) ); ?> €</td>
			</tr>
			<tr>
				<th scope="row"><strong><?php esc_html_e( 'Total', 'hoptown-rental' ); ?></strong></th>
				<td><strong><?php echo esc_html( number_format( floatval( $total_price ), 2 ) ); ?> €</strong></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save meta boxes.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// Bookings are typically created programmatically
		// This method is here for completeness but may not be used
	}
}
