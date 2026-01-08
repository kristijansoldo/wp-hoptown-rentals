<?php
/**
 * Public-facing functionality
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/public
 */

class Hoptown_Rental_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version
	 */
	private $version;

	/**
	 * Initialize the class.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			HOPTOWN_RENTAL_PLUGIN_URL . 'assets/css/public.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			HOPTOWN_RENTAL_PLUGIN_URL . 'assets/js/booking.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		$month_names = array();
		for ( $month = 1; $month <= 12; $month++ ) {
			$month_names[] = wp_date( 'F', mktime( 0, 0, 0, $month, 1, 2020 ) );
		}

		$weekday_names = array();
		for ( $weekday = 0; $weekday < 7; $weekday++ ) {
			$weekday_names[] = wp_date( 'D', strtotime( "monday +{$weekday} days" ) );
		}

		// Localize script with API endpoint and nonce
		wp_localize_script(
			$this->plugin_name,
			'hoptownRental',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'restUrl'   => rest_url( 'hoptown-rental/v1' ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'ajaxNonce' => wp_create_nonce( 'hoptown_booking' ),
				'i18n'      => array(
					'monthNames'       => $month_names,
					'weekdayNames'     => $weekday_names,
					'dateNotAvailable' => __( 'This date is not available.', HOPTOWN_RENTAL_TEXTDOMAIN ),
					'pricingError'     => __( 'Error fetching pricing information.', HOPTOWN_RENTAL_TEXTDOMAIN ),
					'submitError'      => __( 'An error occurred. Please try again.', HOPTOWN_RENTAL_TEXTDOMAIN ),
					'submitting'       => __( 'Submitting...', HOPTOWN_RENTAL_TEXTDOMAIN ),
					'reserve'          => __( 'Reserve', HOPTOWN_RENTAL_TEXTDOMAIN ),
					'selectDate'       => __( 'Please select a date from the calendar', HOPTOWN_RENTAL_TEXTDOMAIN ),
					'priceFormat'      => array(
						'decimal'   => get_option( 'decimal_separator', '.' ),
						'thousands' => get_option( 'thousands_separator', ',' ),
						'currency'  => '€',
						'position'  => 'after',
						'space'     => true,
					),
				),
			)
		);
	}

	/**
	 * Render booking calendar shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_booking_calendar( $atts ) {
		$atts = shortcode_atts(
			array(
				'inflatable_id' => 0,
			),
			$atts,
			'hoptown_booking_calendar'
		);

		$inflatable_id = intval( $atts['inflatable_id'] );

		if ( ! $inflatable_id ) {
			return '<p>' . esc_html__( 'Please provide an inflatable ID.', HOPTOWN_RENTAL_TEXTDOMAIN ) . '</p>';
		}

		// Get booked dates
		$booked_dates = Hoptown_Rental_Booking_Post_Type::get_booked_dates( $inflatable_id );
		$inflatable   = Hoptown_Rental_Inflatable::from_id( $inflatable_id );

		ob_start();
		include HOPTOWN_RENTAL_PLUGIN_DIR . 'templates/booking-calendar.php';
		return ob_get_clean();
	}

	/**
	 * Render booking form shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function render_booking_form( $atts ) {
		$atts = shortcode_atts(
			array(
				'inflatable_id' => 0,
			),
			$atts,
			'hoptown_booking_form'
		);

		$inflatable_id = intval( $atts['inflatable_id'] );

		if ( ! $inflatable_id ) {
			return '<p>' . esc_html__( 'Please provide an inflatable ID.', HOPTOWN_RENTAL_TEXTDOMAIN ) . '</p>';
		}

		// Get inflatable details
		$inflatable     = get_post( $inflatable_id );
		$inflatable_obj = Hoptown_Rental_Inflatable::from_id( $inflatable_id );
		$base_price     = $inflatable_obj->base_price;
		$delivery_price = $inflatable_obj->delivery_price;

		ob_start();
		include HOPTOWN_RENTAL_PLUGIN_DIR . 'templates/booking-form.php';
		return ob_get_clean();
	}

	/**
	 * Append booking shortcodes to inflatable content if missing.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public function append_booking_shortcodes( $content ) {
		if ( is_admin() || ! is_singular( Hoptown_Rental_Inflatable_Post_Type::POST_TYPE ) ) {
			return $content;
		}

		if ( ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$post_id    = get_the_ID();
		$inflatable = Hoptown_Rental_Inflatable::from_id( $post_id );

		$images = array();
		if ( has_post_thumbnail( $post_id ) ) {
			$images[] = get_post_thumbnail_id( $post_id );
		}
		if ( ! empty( $inflatable->gallery_ids ) ) {
			$images = array_merge( $images, $inflatable->gallery_ids );
		}
		$images = array_values( array_unique( array_filter( $images ) ) );

		$calendar = do_shortcode( '[hoptown_booking_calendar inflatable_id="' . $post_id . '"]' );
		$form     = do_shortcode( '[hoptown_booking_form inflatable_id="' . $post_id . '"]' );
		$clean_content = preg_replace( '/\[hoptown_booking_(calendar|form)[^\]]*\]/', '', $content );

		ob_start();
		?>
		<section class="hoptown-product">
			<div class="hoptown-product-media">
				<?php if ( ! empty( $images ) ) : ?>
					<div class="hoptown-product-gallery">
						<div class="hoptown-product-main">
							<?php if ( $images ) : ?>
								<a href="<?php echo esc_url( wp_get_attachment_image_url( $images[0], 'full' ) ); ?>" class="hoptown-gallery-link">
									<?php echo wp_get_attachment_image( $images[0], 'large' ); ?>
								</a>
							<?php endif; ?>
						</div>
						<?php if ( count( $images ) > 1 ) : ?>
							<div class="hoptown-product-thumbs">
								<?php foreach ( array_slice( $images, 1 ) as $image_id ) : ?>
									<a href="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ); ?>" class="hoptown-gallery-link">
										<?php echo wp_get_attachment_image( $image_id, 'thumbnail' ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="hoptown-product-details">
					<?php echo $clean_content; ?>
				</div>
			</div>
			<div class="hoptown-product-summary">
				<div class="hoptown-booking-card">
					<h2 class="hoptown-booking-title"><?php esc_html_e( 'Book This Inflatable', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></h2>
					<?php echo $calendar; ?>
					<?php echo $form; ?>
				</div>
			</div>
		</section>
		<?php

		return ob_get_clean();
	}
}
