<?php
/**
 * Admin-specific functionality
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes/admin
 */

class Hoptown_Rental_Admin {

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
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		// Only load on our post types
		if ( in_array( $screen->post_type, array( Hoptown_Rental_Inflatable_Post_Type::POST_TYPE, Hoptown_Rental_Booking_Post_Type::POST_TYPE ), true ) ) {
			wp_enqueue_style(
				$this->plugin_name,
				HOPTOWN_RENTAL_PLUGIN_URL . 'assets/css/admin.css',
				array(),
				$this->version,
				'all'
			);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		// Only load on our post types
		if ( in_array( $screen->post_type, array( Hoptown_Rental_Inflatable_Post_Type::POST_TYPE, Hoptown_Rental_Booking_Post_Type::POST_TYPE ), true ) ) {
			// Enqueue WordPress media library
			wp_enqueue_media();

			wp_enqueue_script(
				$this->plugin_name,
				HOPTOWN_RENTAL_PLUGIN_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				$this->version,
				true
			);

			wp_localize_script(
				$this->plugin_name,
				'hoptownAdmin',
				array(
					'galleryTitle'  => __( 'Select Gallery Images', HOPTOWN_RENTAL_TEXTDOMAIN ),
					'galleryButton' => __( 'Add to Gallery', HOPTOWN_RENTAL_TEXTDOMAIN ),
					'galleryMetaKey' => Hoptown_Rental_Meta::INFLATABLE_GALLERY,
				)
			);
		}
	}

	/**
	 * Enqueue assets in block editor context.
	 */
	public function enqueue_block_editor_assets() {
		$this->enqueue_styles();
		$this->enqueue_scripts();
	}

	/**
	 * Register settings menu under Inflatables.
	 */
	public function register_settings_menu() {
		add_submenu_page(
			'edit.php?post_type=' . Hoptown_Rental_Inflatable_Post_Type::POST_TYPE,
			__( 'Hoptown Settings', HOPTOWN_RENTAL_TEXTDOMAIN ),
			__( 'Settings', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'manage_options',
			'hoptown-rental-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			'hoptown_rental_settings_group',
			'hoptown_rental_settings',
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'hoptown_rental_notifications',
			__( 'Notifications', HOPTOWN_RENTAL_TEXTDOMAIN ),
			'__return_false',
			'hoptown-rental-settings'
		);

		add_settings_field(
			'hoptown_rental_notification_email',
			__( 'Notification Email', HOPTOWN_RENTAL_TEXTDOMAIN ),
			array( $this, 'render_notification_email_field' ),
			'hoptown-rental-settings',
			'hoptown_rental_notifications'
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $input Raw input.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$output = array();

		if ( isset( $input['notification_email'] ) && is_email( $input['notification_email'] ) ) {
			$output['notification_email'] = sanitize_email( $input['notification_email'] );
		}

		return $output;
	}

	/**
	 * Render notification email field.
	 */
	public function render_notification_email_field() {
		$options = get_option( 'hoptown_rental_settings', array() );
		$value   = isset( $options['notification_email'] ) ? $options['notification_email'] : get_option( 'admin_email' );
		?>
		<input type="email" name="hoptown_rental_settings[notification_email]" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
		<p class="description"><?php esc_html_e( 'Address that receives booking notifications.', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></p>
		<?php
	}

	/**
	 * Render settings page.
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Hoptown Settings', HOPTOWN_RENTAL_TEXTDOMAIN ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'hoptown_rental_settings_group' ); ?>
				<?php do_settings_sections( 'hoptown-rental-settings' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
