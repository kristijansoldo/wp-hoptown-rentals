<?php
/**
 * Booking Custom Post Type
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/post-types
 */

class Hoptown_Rental_Booking_Post_Type {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const POST_TYPE = 'hoptown_booking';

	/**
	 * Register the custom post type.
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Bookings', 'Post Type General Name', 'hoptown-rental' ),
			'singular_name'         => _x( 'Booking', 'Post Type Singular Name', 'hoptown-rental' ),
			'menu_name'             => __( 'Bookings', 'hoptown-rental' ),
			'name_admin_bar'        => __( 'Booking', 'hoptown-rental' ),
			'archives'              => __( 'Booking Archives', 'hoptown-rental' ),
			'attributes'            => __( 'Booking Attributes', 'hoptown-rental' ),
			'parent_item_colon'     => __( 'Parent Booking:', 'hoptown-rental' ),
			'all_items'             => __( 'All Bookings', 'hoptown-rental' ),
			'add_new_item'          => __( 'Add New Booking', 'hoptown-rental' ),
			'add_new'               => __( 'Add New', 'hoptown-rental' ),
			'new_item'              => __( 'New Booking', 'hoptown-rental' ),
			'edit_item'             => __( 'Edit Booking', 'hoptown-rental' ),
			'update_item'           => __( 'Update Booking', 'hoptown-rental' ),
			'view_item'             => __( 'View Booking', 'hoptown-rental' ),
			'view_items'            => __( 'View Bookings', 'hoptown-rental' ),
			'search_items'          => __( 'Search Booking', 'hoptown-rental' ),
			'not_found'             => __( 'Not found', 'hoptown-rental' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'hoptown-rental' ),
			'items_list'            => __( 'Bookings list', 'hoptown-rental' ),
			'items_list_navigation' => __( 'Bookings list navigation', 'hoptown-rental' ),
			'filter_items_list'     => __( 'Filter bookings list', 'hoptown-rental' ),
		);

		$args = array(
			'label'               => __( 'Booking', 'hoptown-rental' ),
			'description'         => __( 'Inflatable rental bookings', 'hoptown-rental' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 6,
			'menu_icon'           => 'dashicons-calendar-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'show_in_rest'        => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Create a new booking.
	 *
	 * @param array $data Booking data.
	 * @return int|WP_Error Post ID on success, WP_Error on failure.
	 */
	public static function create_booking( $data ) {
		// Validate required fields
		$required_fields = array( 'inflatable_id', 'booking_date', 'customer_name', 'customer_email', 'customer_phone' );
		foreach ( $required_fields as $field ) {
			if ( empty( $data[ $field ] ) ) {
				return new WP_Error( 'missing_field', sprintf( 'Missing required field: %s', $field ) );
			}
		}

		// Check if inflatable is available
		if ( ! Hoptown_Rental_Inflatable_Post_Type::is_available( $data['inflatable_id'], $data['booking_date'] ) ) {
			return new WP_Error( 'not_available', 'Inflatable is not available on this date' );
		}

		// Get inflatable title
		$inflatable_title = get_the_title( $data['inflatable_id'] );

		// Create booking post
		$booking_title = sprintf(
			'%s - %s - %s',
			$inflatable_title,
			$data['customer_name'],
			$data['booking_date']
		);

		$post_data = array(
			'post_title'  => $booking_title,
			'post_type'   => self::POST_TYPE,
			'post_status' => 'publish',
		);

		$booking_id = wp_insert_post( $post_data );

		if ( is_wp_error( $booking_id ) ) {
			return $booking_id;
		}

		// Save meta data
		update_post_meta( $booking_id, '_hoptown_inflatable_id', sanitize_text_field( $data['inflatable_id'] ) );
		update_post_meta( $booking_id, '_hoptown_booking_date', sanitize_text_field( $data['booking_date'] ) );
		update_post_meta( $booking_id, '_hoptown_customer_name', sanitize_text_field( $data['customer_name'] ) );
		update_post_meta( $booking_id, '_hoptown_customer_email', sanitize_email( $data['customer_email'] ) );
		update_post_meta( $booking_id, '_hoptown_customer_phone', sanitize_text_field( $data['customer_phone'] ) );
		if ( ! empty( $data['customer_note'] ) ) {
			update_post_meta( $booking_id, '_hoptown_customer_note', sanitize_textarea_field( $data['customer_note'] ) );
		}

		// Optional fields
		if ( ! empty( $data['delivery_method'] ) ) {
			update_post_meta( $booking_id, '_hoptown_delivery_method', sanitize_text_field( $data['delivery_method'] ) );
		}

		if ( ! empty( $data['delivery_address'] ) ) {
			update_post_meta( $booking_id, '_hoptown_delivery_address', sanitize_text_field( $data['delivery_address'] ) );
		}

		if ( ! empty( $data['pickup_time'] ) ) {
			update_post_meta( $booking_id, '_hoptown_pickup_time', sanitize_text_field( $data['pickup_time'] ) );
		}

		// Calculate and save total price
		$price          = Hoptown_Rental_Inflatable_Post_Type::get_price_for_date( $data['inflatable_id'], $data['booking_date'] );
		$delivery_price = 0;

		if ( ! empty( $data['delivery_method'] ) && 'delivery' === $data['delivery_method'] ) {
			$delivery_price = get_post_meta( $data['inflatable_id'], '_hoptown_delivery_price', true );
		}

		$total_price = floatval( $price ) + floatval( $delivery_price );
		update_post_meta( $booking_id, '_hoptown_total_price', $total_price );
		update_post_meta( $booking_id, '_hoptown_rental_price', $price );
		update_post_meta( $booking_id, '_hoptown_delivery_price', $delivery_price );

		return $booking_id;
	}

	/**
	 * Get booked dates for an inflatable.
	 *
	 * @param int $inflatable_id Inflatable post ID.
	 * @return array Array of booked dates in Y-m-d format.
	 */
	public static function get_booked_dates( $inflatable_id ) {
		$bookings = new WP_Query(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => array( 'publish', 'pending' ),
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_hoptown_inflatable_id',
						'value'   => $inflatable_id,
						'compare' => '=',
					),
				),
			)
		);

		$booked_dates = array();

		if ( $bookings->have_posts() ) {
			while ( $bookings->have_posts() ) {
				$bookings->the_post();
				$booking_date = get_post_meta( get_the_ID(), '_hoptown_booking_date', true );
				if ( $booking_date ) {
					$booked_dates[] = $booking_date;
				}
			}
		}

		wp_reset_postdata();

		return $booked_dates;
	}
}
