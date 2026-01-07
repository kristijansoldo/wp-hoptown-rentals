<?php
/**
 * Inflatable Custom Post Type
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/post-types
 */

class Hoptown_Rental_Inflatable_Post_Type {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const POST_TYPE = 'hoptown_inflatable';

	/**
	 * Register the custom post type.
	 */
	public function register_post_type() {
		$locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
		$is_hr  = ( 0 === strpos( $locale, 'hr' ) );
		$default_slug = $is_hr ? 'napuhanci' : 'inflatables';
		$slug = apply_filters( 'hoptown_rental_inflatable_slug', $default_slug, $locale );

		$labels = array(
			'name'                  => _x( 'Inflatables', 'Post Type General Name', 'hoptown-rental' ),
			'singular_name'         => _x( 'Inflatable', 'Post Type Singular Name', 'hoptown-rental' ),
			'menu_name'             => __( 'Inflatables', 'hoptown-rental' ),
			'name_admin_bar'        => __( 'Inflatable', 'hoptown-rental' ),
			'archives'              => __( 'Inflatable Archives', 'hoptown-rental' ),
			'attributes'            => __( 'Inflatable Attributes', 'hoptown-rental' ),
			'parent_item_colon'     => __( 'Parent Inflatable:', 'hoptown-rental' ),
			'all_items'             => __( 'All Inflatables', 'hoptown-rental' ),
			'add_new_item'          => __( 'Add New Inflatable', 'hoptown-rental' ),
			'add_new'               => __( 'Add New', 'hoptown-rental' ),
			'new_item'              => __( 'New Inflatable', 'hoptown-rental' ),
			'edit_item'             => __( 'Edit Inflatable', 'hoptown-rental' ),
			'update_item'           => __( 'Update Inflatable', 'hoptown-rental' ),
			'view_item'             => __( 'View Inflatable', 'hoptown-rental' ),
			'view_items'            => __( 'View Inflatables', 'hoptown-rental' ),
			'search_items'          => __( 'Search Inflatable', 'hoptown-rental' ),
			'not_found'             => __( 'Not found', 'hoptown-rental' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'hoptown-rental' ),
			'featured_image'        => __( 'Featured Image', 'hoptown-rental' ),
			'set_featured_image'    => __( 'Set featured image', 'hoptown-rental' ),
			'remove_featured_image' => __( 'Remove featured image', 'hoptown-rental' ),
			'use_featured_image'    => __( 'Use as featured image', 'hoptown-rental' ),
			'insert_into_item'      => __( 'Insert into inflatable', 'hoptown-rental' ),
			'uploaded_to_this_item' => __( 'Uploaded to this inflatable', 'hoptown-rental' ),
			'items_list'            => __( 'Inflatables list', 'hoptown-rental' ),
			'items_list_navigation' => __( 'Inflatables list navigation', 'hoptown-rental' ),
			'filter_items_list'     => __( 'Filter inflatables list', 'hoptown-rental' ),
		);

		$args = array(
			'label'               => __( 'Inflatable', 'hoptown-rental' ),
			'description'         => __( 'Inflatable rental items', 'hoptown-rental' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-admin-home',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest'        => true,
			'rewrite'             => array( 'slug' => $slug ),
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Get inflatable price for a specific date.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $date    Date in Y-m-d format.
	 * @return float Price for the date.
	 */
	public static function get_price_for_date( $post_id, $date ) {
		$base_price      = get_post_meta( $post_id, '_hoptown_base_price', true );
		$weekend_price   = get_post_meta( $post_id, '_hoptown_weekend_price', true );
		$weekday_price   = get_post_meta( $post_id, '_hoptown_weekday_price', true );
		$use_day_pricing = get_post_meta( $post_id, '_hoptown_use_day_pricing', true );

		// If day-specific pricing is not enabled, return base price
		if ( ! $use_day_pricing || 'yes' !== $use_day_pricing ) {
			return floatval( $base_price );
		}

		// Determine if the date is a weekend
		$timestamp  = strtotime( $date );
		$day_of_week = date( 'N', $timestamp ); // 1 (Monday) through 7 (Sunday)

		// Saturday (6) or Sunday (7)
		if ( $day_of_week >= 6 ) {
			return floatval( $weekend_price ? $weekend_price : $base_price );
		}

		// Weekday
		return floatval( $weekday_price ? $weekday_price : $base_price );
	}

	/**
	 * Check if inflatable is available on a specific date.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $date    Date in Y-m-d format.
	 * @return bool True if available, false otherwise.
	 */
	public static function is_available( $post_id, $date ) {
		// Query bookings for this inflatable on this date
		$bookings = new WP_Query(
			array(
				'post_type'      => Hoptown_Rental_Booking_Post_Type::POST_TYPE,
				'post_status'    => array( 'publish', 'pending' ),
				'posts_per_page' => 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => '_hoptown_inflatable_id',
						'value'   => $post_id,
						'compare' => '=',
					),
					array(
						'key'     => '_hoptown_booking_date',
						'value'   => $date,
						'compare' => '=',
					),
				),
			)
		);

		$is_booked = $bookings->have_posts();
		wp_reset_postdata();

		return ! $is_booked;
	}
}
