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
			'name'                  => _x( 'Inflatables', 'Post Type General Name', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'singular_name'         => _x( 'Inflatable', 'Post Type Singular Name', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'menu_name'             => __( 'Inflatables', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'name_admin_bar'        => __( 'Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'archives'              => __( 'Inflatable Archives', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'attributes'            => __( 'Inflatable Attributes', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'parent_item_colon'     => __( 'Parent Inflatable:', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'all_items'             => __( 'All Inflatables', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'add_new_item'          => __( 'Add New Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'add_new'               => __( 'Add New', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'new_item'              => __( 'New Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'edit_item'             => __( 'Edit Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'update_item'           => __( 'Update Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'view_item'             => __( 'View Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'view_items'            => __( 'View Inflatables', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'search_items'          => __( 'Search Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'not_found'             => __( 'Not found', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'not_found_in_trash'    => __( 'Not found in Trash', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'featured_image'        => __( 'Featured Image', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'set_featured_image'    => __( 'Set featured image', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'remove_featured_image' => __( 'Remove featured image', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'use_featured_image'    => __( 'Use as featured image', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'insert_into_item'      => __( 'Insert into inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'uploaded_to_this_item' => __( 'Uploaded to this inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'items_list'            => __( 'Inflatables list', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'items_list_navigation' => __( 'Inflatables list navigation', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'filter_items_list'     => __( 'Filter inflatables list', HOPTOWN_RENTAL_TEXTDOMAIN ),
		);

		$args = array(
			'label'               => __( 'Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'description'         => __( 'Inflatable rental items', HOPTOWN_RENTAL_TEXTDOMAIN ),
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

		add_action( 'rest_after_insert_' . self::POST_TYPE, array( $this, 'sync_rest_meta' ), 10, 3 );

		register_post_meta(
			self::POST_TYPE,
			Hoptown_Rental_Meta::INFLATABLE_GALLERY,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => array(
					'schema' => array(
						'type'    => 'string',
						'context' => array( 'view', 'edit' ),
					),
				),
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}

	/**
	 * Sync gallery meta from REST requests (Gutenberg saves).
	 *
	 * @param WP_Post         $post     Inserted or updated post.
	 * @param WP_REST_Request $request  Request object.
	 * @param bool            $creating True if creating a new post.
	 */
	public function sync_rest_meta( $post, $request, $creating ) {
		if ( ! $post || ! $request instanceof WP_REST_Request ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		$meta = $request->get_param( 'meta' );
		if ( ! is_array( $meta ) ) {
			return;
		}

		if ( isset( $meta[ Hoptown_Rental_Meta::INFLATABLE_GALLERY ] ) ) {
			Hoptown_Rental_Meta::update( $post->ID, Hoptown_Rental_Meta::INFLATABLE_GALLERY, $meta[ Hoptown_Rental_Meta::INFLATABLE_GALLERY ], 'sanitize_text_field' );
			return;
		}

		if ( isset( $meta['hoptown_gallery'] ) ) {
			Hoptown_Rental_Meta::update( $post->ID, Hoptown_Rental_Meta::INFLATABLE_GALLERY, $meta['hoptown_gallery'], 'sanitize_text_field' );
		}
	}

	/**
	 * Get inflatable price for a specific date.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $date    Date in Y-m-d format.
	 * @return float Price for the date.
	 */
	public static function get_price_for_date( $post_id, $date ) {
		$date_value = Hoptown_Rental_Date_Value::from_ymd( $date );
		return Hoptown_Rental_Inflatable_Repository::get_price_for_date( $post_id, $date_value );
	}

	/**
	 * Check if inflatable is available on a specific date.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $date    Date in Y-m-d format.
	 * @return bool True if available, false otherwise.
	 */
	public static function is_available( $post_id, $date ) {
		return ! Hoptown_Rental_Booking_Repository::is_booked_on_date( $post_id, $date );
	}
}
