<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class compatibility by other plugins in our theme functions.
 *
 * Class Pojo_Compatibility
 */
class Pojo_Compatibility {

	public static function is_wordpress_seo_installer() {
		return function_exists( 'wpseo_init' );
	}

	public static function is_contact_form_7_installer() {
		return function_exists( 'wpcf7_init' );
	}

	public static function is_revslider_installer() {
		return class_exists( 'UniteBaseClassRev' );
	}

	public static function is_slideshow_installed() {
		global $pojo_slideshow;
		return isset( $pojo_slideshow ) && $pojo_slideshow instanceof Pojo_Slideshow && $pojo_slideshow->is_activated();
	}
	
	public static function is_pojo_sharing_installed() {
		return function_exists( 'sharing_load_textdomain' );
	}
	
	public static function is_woocommerce_installed() {
		return class_exists( 'WooCommerce' );
	}
	
	public static function is_easy_digital_downloads_installed() {
		return class_exists( 'Easy_Digital_Downloads' );
	}
	
	public static function is_bbpress_installed() {
		return class_exists( 'bbPress' );
	}

	public static function is_elementor_installed() {
		return class_exists( '\Elementor\Plugin' );
	}
}