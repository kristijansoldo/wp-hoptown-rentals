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
			'name'                  => _x( 'Bookings', 'Post Type General Name', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'singular_name'         => _x( 'Booking', 'Post Type Singular Name', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'menu_name'             => __( 'Bookings', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'name_admin_bar'        => __( 'Booking', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'archives'              => __( 'Booking Archives', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'attributes'            => __( 'Booking Attributes', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'parent_item_colon'     => __( 'Parent Booking:', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'all_items'             => __( 'All Bookings', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'add_new_item'          => __( 'Add New Booking', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'add_new'               => __( 'Add New', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'new_item'              => __( 'New Booking', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'edit_item'             => __( 'Edit Booking', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'update_item'           => __( 'Update Booking', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'view_item'             => __( 'View Booking', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'view_items'            => __( 'View Bookings', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'search_items'          => __( 'Search Booking', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'not_found'             => __( 'Not found', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'not_found_in_trash'    => __( 'Not found in Trash', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'items_list'            => __( 'Bookings list', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'items_list_navigation' => __( 'Bookings list navigation', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'filter_items_list'     => __( 'Filter bookings list', HOPTOWN_RENTAL_TEXTDOMAIN ),
		);

		$args = array(
			'label'               => __( 'Booking', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'description'         => __( 'Inflatable rental bookings', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'custom-fields' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=' . Hoptown_Rental_Inflatable_Post_Type::POST_TYPE,
			'menu_position'       => null,
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
		return Hoptown_Rental_Booking_Service::create_booking( $data );
	}

	/**
	 * Get booked dates for an inflatable.
	 *
	 * @param int $inflatable_id Inflatable post ID.
	 * @return array Array of booked dates in Y-m-d format.
	 */
	public static function get_booked_dates( $inflatable_id ) {
		return Hoptown_Rental_Booking_Repository::get_booked_dates( $inflatable_id );
	}
}
