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
			__( 'Booking Details', HOPTOWN_RENTAL_TEXTDOMAIN ),
			array( $this, 'render_booking_details_meta_box' ),
			Hoptown_Rental_Booking_Post_Type::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'hoptown_customer_details',
			__( 'Customer Details', HOPTOWN_RENTAL_TEXTDOMAIN ),
			array( $this, 'render_customer_details_meta_box' ),
			Hoptown_Rental_Booking_Post_Type::POST_TYPE,
			'normal',
			'high'
		);

		add_meta_box(
			'hoptown_pricing_details',
			__( 'Pricing Details', HOPTOWN_RENTAL_TEXTDOMAIN ),
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
		wp_nonce_field( 'hoptown_booking_meta', 'hoptown_booking_meta_nonce' );

		$booking          = Hoptown_Rental_Booking::from_id( $post->ID );
		$inflatable_id    = $booking->inflatable_id;
		$booking_date     = $booking->booking_date;
		$delivery_method  = $booking->delivery_method;
		$pickup_time      = $booking->pickup_time;
		$delivery_address = $booking->delivery_address;

		$inflatable_title = $inflatable_id ? get_the_title( $inflatable_id ) : __( 'N/A', HOPTOWN_RENTAL_TEXTDOMAIN );
		$inflatables      = get_posts(
			array(
				'post_type'      => Hoptown_Rental_Inflatable_Post_Type::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 200,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td>
					<select name="hoptown_inflatable_id" class="regular-text">
						<option value=""><?php esc_html_e( 'Select Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></option>
						<?php foreach ( $inflatables as $inflatable ) : ?>
							<option value="<?php echo esc_attr( $inflatable->ID ); ?>" <?php selected( (int) $inflatable_id, (int) $inflatable->ID ); ?>>
								<?php echo esc_html( $inflatable->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<?php if ( $inflatable_id ) : ?>
						<p><a href="<?php echo esc_url( get_edit_post_link( $inflatable_id ) ); ?>" target="_blank"><?php esc_html_e( 'Edit Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></a></p>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Booking Date', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td>
					<input type="date" name="hoptown_booking_date" value="<?php echo esc_attr( $booking_date ); ?>" />
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Delivery Method', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td>
					<label>
						<input type="radio" name="hoptown_delivery_method" value="delivery" <?php checked( $delivery_method, 'delivery' ); ?> />
						<?php esc_html_e( 'Delivery', HOPTOWN_RENTAL_TEXTDOMAIN ); ?>
					</label>
					<label style="margin-left: 12px;">
						<input type="radio" name="hoptown_delivery_method" value="pickup" <?php checked( $delivery_method, 'pickup' ); ?> />
						<?php esc_html_e( 'Pickup', HOPTOWN_RENTAL_TEXTDOMAIN ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Delivery Address', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td><input type="text" name="hoptown_delivery_address" class="regular-text" value="<?php echo esc_attr( $delivery_address ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Pickup Time (24h)', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td><input type="text" name="hoptown_pickup_time" value="<?php echo esc_attr( $pickup_time ); ?>" placeholder="HH:MM" /></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render customer details meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_customer_details_meta_box( $post ) {
		$booking        = Hoptown_Rental_Booking::from_id( $post->ID );
		$customer_name  = $booking->customer_name;
		$customer_email = $booking->customer_email;
		$customer_phone = $booking->customer_phone;
		$customer_note  = $booking->customer_note;
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Name', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td><input type="text" name="hoptown_customer_name" class="regular-text" value="<?php echo esc_attr( $customer_name ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Email', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td><input type="email" name="hoptown_customer_email" class="regular-text" value="<?php echo esc_attr( $customer_email ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Phone', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td><input type="text" name="hoptown_customer_phone" class="regular-text" value="<?php echo esc_attr( $customer_phone ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Note', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td><textarea name="hoptown_customer_note" rows="3" class="regular-text"><?php echo esc_textarea( $customer_note ); ?></textarea></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render pricing details meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_pricing_details_meta_box( $post ) {
		$booking        = Hoptown_Rental_Booking::from_id( $post->ID );
		$rental_price   = $booking->rental_price;
		$delivery_price = $booking->delivery_price;
		$total_price    = $booking->total_price;
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Rental Price', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td><?php echo esc_html( ( new Hoptown_Rental_Money( $rental_price ) )->format() ); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Delivery Price', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></th>
				<td><?php echo esc_html( ( new Hoptown_Rental_Money( $delivery_price ) )->format() ); ?></td>
			</tr>
			<tr>
				<th scope="row"><strong><?php esc_html_e( 'Total', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></strong></th>
				<td><strong><?php echo esc_html( ( new Hoptown_Rental_Money( $total_price ) )->format() ); ?></strong></td>
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
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['hoptown_booking_meta_nonce'] ) ) {
			return;
		}

		if ( ! isset( $_POST['hoptown_booking_meta_nonce'] ) || ! wp_verify_nonce( $_POST['hoptown_booking_meta_nonce'], 'hoptown_booking_meta' ) ) {
			return;
		}

		if ( Hoptown_Rental_Booking_Post_Type::POST_TYPE !== $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		Hoptown_Rental_Booking_Service::save_from_admin( $post_id, $_POST );
	}
}
