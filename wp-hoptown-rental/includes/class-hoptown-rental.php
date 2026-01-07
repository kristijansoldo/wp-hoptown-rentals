<?php
/**
 * The core plugin class
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes
 */

class Hoptown_Rental {

	/**
	 * The loader that is responsible for maintaining and registering all hooks.
	 *
	 * @var      Hoptown_Rental_Loader    $loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var      string    $version
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 */
	public function __construct() {
		$this->version     = HOPTOWN_RENTAL_VERSION;
		$this->plugin_name = HOPTOWN_RENTAL_TEXTDOMAIN;

		add_action( "plugins_loaded", array( $this, "load_textdomain" ) );
		add_action( "phpmailer_init", array( $this, "configure_mailer" ) );
		add_filter( "wp_mail_from", array( $this, "filter_mail_from" ) );
		add_filter( "wp_mail_from_name", array( $this, "filter_mail_from_name" ) );

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_post_type_hooks();
		$this->define_api_hooks();
		$this->define_template_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/class-meta.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/class-booking.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/class-inflatable.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/value-objects/class-money.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/value-objects/class-date-value.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/value-objects/class-time-value.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/repositories/class-booking-repository.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/repositories/class-inflatable-repository.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/services/class-booking-service.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/services/class-inflatable-service.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/post-types/class-inflatable-post-type.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/post-types/class-booking-post-type.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/admin/class-admin.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/admin/class-inflatable-meta-boxes.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/admin/class-booking-meta-boxes.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/public/class-public.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/public/class-booking-handler.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/api/class-booking-api.php";
		require_once HOPTOWN_RENTAL_PLUGIN_DIR . "includes/class-template-loader.php";
	}

	/**
	 * Register all hooks related to custom post types.
	 */
	private function define_post_type_hooks() {
		$inflatable_cpt = new Hoptown_Rental_Inflatable_Post_Type();
		add_action( "init", array( $inflatable_cpt, "register_post_type" ) );

		$booking_cpt = new Hoptown_Rental_Booking_Post_Type();
		add_action( "init", array( $booking_cpt, "register_post_type" ) );
	}

	/**
	 * Register all hooks related to the admin area.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Hoptown_Rental_Admin( $this->get_plugin_name(), $this->get_version() );

		add_action( "admin_enqueue_scripts", array( $plugin_admin, "enqueue_styles" ) );
		add_action( "admin_enqueue_scripts", array( $plugin_admin, "enqueue_scripts" ) );
		add_action( "admin_menu", array( $plugin_admin, "register_settings_menu" ) );
		add_action( "admin_init", array( $plugin_admin, "register_settings" ) );

		$inflatable_meta = new Hoptown_Rental_Inflatable_Meta_Boxes();
		add_action( "add_meta_boxes", array( $inflatable_meta, "add_meta_boxes" ) );
		add_action( "save_post", array( $inflatable_meta, "save_meta_boxes" ), 10, 2 );

		$booking_meta = new Hoptown_Rental_Booking_Meta_Boxes();
		add_action( "add_meta_boxes", array( $booking_meta, "add_meta_boxes" ) );
		add_action( "save_post", array( $booking_meta, "save_meta_boxes" ), 10, 2 );
	}

	/**
	 * Register all hooks related to the public-facing functionality.
	 */
	private function define_public_hooks() {
		$plugin_public = new Hoptown_Rental_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( "wp_enqueue_scripts", array( $plugin_public, "enqueue_styles" ) );
		add_action( "wp_enqueue_scripts", array( $plugin_public, "enqueue_scripts" ) );

		$booking_handler = new Hoptown_Rental_Booking_Handler();
		add_action( "wp_ajax_hoptown_submit_booking", array( $booking_handler, "handle_booking_submission" ) );
		add_action( "wp_ajax_nopriv_hoptown_submit_booking", array( $booking_handler, "handle_booking_submission" ) );

		add_shortcode( "hoptown_booking_calendar", array( $plugin_public, "render_booking_calendar" ) );
		add_shortcode( "hoptown_booking_form", array( $plugin_public, "render_booking_form" ) );
	}

	/**
	 * Register all hooks related to the REST API.
	 */
	private function define_api_hooks() {
		$booking_api = new Hoptown_Rental_Booking_API();
		add_action( "rest_api_init", array( $booking_api, "register_routes" ) );
	}

	/**
	 * Register all hooks related to template loading.
	 */
	private function define_template_hooks() {
		$template_loader = new Hoptown_Rental_Template_Loader();
	}

	/**
	 * Run the plugin.
	 */
	public function run() {
	}

	/**
	 * Load plugin textdomain.
	 */
	public function load_textdomain() {
		$plugin_rel_path = dirname( plugin_basename( HOPTOWN_RENTAL_PLUGIN_DIR . 'wp-hoptown-rental.php' ) ) . '/languages/';
		load_plugin_textdomain(
			HOPTOWN_RENTAL_TEXTDOMAIN,
			false,
			$plugin_rel_path
		);
	}

	/**
	 * Configure SMTP when WP_MAIL_* constants are set.
	 *
	 * @param PHPMailer $phpmailer PHPMailer instance.
	 */
	public function configure_mailer( $phpmailer ) {
		if ( ! defined( 'WP_MAIL_SMTP_HOST' ) ) {
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->Host = WP_MAIL_SMTP_HOST;

		if ( defined( 'WP_MAIL_SMTP_PORT' ) ) {
			$phpmailer->Port = WP_MAIL_SMTP_PORT;
		}

		if ( defined( 'WP_MAIL_SMTP_AUTH' ) ) {
			$phpmailer->SMTPAuth = (bool) WP_MAIL_SMTP_AUTH;
		}

		if ( defined( 'WP_MAIL_SMTP_USERNAME' ) ) {
			$phpmailer->Username = WP_MAIL_SMTP_USERNAME;
		}

		if ( defined( 'WP_MAIL_SMTP_PASSWORD' ) ) {
			$phpmailer->Password = WP_MAIL_SMTP_PASSWORD;
		}

		if ( defined( 'WP_MAIL_SMTP_SECURE' ) ) {
			$phpmailer->SMTPSecure = WP_MAIL_SMTP_SECURE;
		}
	}

	/**
	 * Filter mail from address.
	 *
	 * @param string $from Email address.
	 * @return string
	 */
	public function filter_mail_from( $from ) {
		if ( defined( 'WP_MAIL_FROM' ) ) {
			return WP_MAIL_FROM;
		}

		return $from;
	}

	/**
	 * Filter mail from name.
	 *
	 * @param string $name From name.
	 * @return string
	 */
	public function filter_mail_from_name( $name ) {
		if ( defined( 'WP_MAIL_FROM_NAME' ) ) {
			return WP_MAIL_FROM_NAME;
		}

		return $name;
	}

	/**
	 * Get the plugin name.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Get the plugin version.
	 */
	public function get_version() {
		return $this->version;
	}
}
