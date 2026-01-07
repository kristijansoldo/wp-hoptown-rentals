<?php
/**
 * Template Loader
 *
 * Handles loading of plugin templates with theme override support
 *
 * @package    Hoptown_Rental
 * @subpackage Hoptown_Rental/includes
 */

class Hoptown_Rental_Template_Loader {

	/**
	 * Initialize the template loader.
	 */
	public function __construct() {
		add_filter( "template_include", array( $this, "template_loader" ), 99 );
	}

	/**
	 * Load templates.
	 *
	 * @param string $template Template path.
	 * @return string Template path.
	 */
	public function template_loader( $template ) {
		if ( is_singular( Hoptown_Rental_Inflatable_Post_Type::POST_TYPE ) ) {
			return $this->get_template_hierarchy( "single-hoptown_inflatable.php", $template );
		}

		if ( is_post_type_archive( Hoptown_Rental_Inflatable_Post_Type::POST_TYPE ) ) {
			return $this->get_template_hierarchy( "archive-hoptown_inflatable.php", $template );
		}

		return $template;
	}

	/**
	 * Retrieve a template file with theme override support.
	 *
	 * Search order:
	 * 1. theme/hoptown-rental/{template-name}
	 * 2. theme/{template-name}
	 * 3. plugin/templates/{template-name}
	 *
	 * @param string $template_name Template file name.
	 * @param string $default_template Default template path.
	 * @return string Template path.
	 */
	public function get_template_hierarchy( $template_name, $default_template = "" ) {
		$theme_template = locate_template(
			array(
				"hoptown-rental/" . $template_name,
				$template_name,
			)
		);

		if ( $theme_template ) {
			return $theme_template;
		}

		$plugin_template = HOPTOWN_RENTAL_PLUGIN_DIR . "templates/" . $template_name;

		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		return $default_template;
	}

	/**
	 * Get template part.
	 *
	 * @param string $slug Template slug.
	 * @param string $name Template name (optional).
	 */
	public static function get_template_part( $slug, $name = "" ) {
		$template = "";

		if ( $name ) {
			$template = locate_template( array( "hoptown-rental/{$slug}-{$name}.php", "{$slug}-{$name}.php" ) );
		}

		if ( ! $template ) {
			$template = locate_template( array( "hoptown-rental/{$slug}.php", "{$slug}.php" ) );
		}

		if ( ! $template ) {
			if ( $name && file_exists( HOPTOWN_RENTAL_PLUGIN_DIR . "templates/{$slug}-{$name}.php" ) ) {
				$template = HOPTOWN_RENTAL_PLUGIN_DIR . "templates/{$slug}-{$name}.php";
			} elseif ( file_exists( HOPTOWN_RENTAL_PLUGIN_DIR . "templates/{$slug}.php" ) ) {
				$template = HOPTOWN_RENTAL_PLUGIN_DIR . "templates/{$slug}.php";
			}
		}

		$template = apply_filters( "hoptown_rental_get_template_part", $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}
	}
}
