<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_WordPress_Reporter extends Pojo_Info_Base_Reporter {

	public function get_title() {
		return __( 'WordPress Environment', 'pojo' );
	}
	
	public function get_fields() {
		return array(
			'version' => __( 'Version', 'pojo' ),
			'site_url' => __( 'Site URL', 'pojo' ),
			'home_url' => __( 'Home URL', 'pojo' ),
			'is_multisite' => __( 'WP Multisite', 'pojo' ),
			'max_upload_size' => __( 'Max Upload Size', 'pojo' ),
			'memory_limit' => __( 'Memory limit', 'pojo' ),
			'permalink_structure' => __( 'Permalink Structure', 'pojo' ),
			'language' => __( 'Language', 'pojo' ),
			'timezone' => __( 'Timezone', 'pojo' ),
			'system_email' => __( 'System Email', 'pojo' ),
			'debug_mode' => __( 'Debug Mode', 'pojo' ),
			'pojo_server_status' => __( 'Pojo Server', 'pojo' ),
		);
	}

	public function get_memory_limit() {
		return array(
			'value' => WP_MEMORY_LIMIT,
		);
	}

	public function get_version() {
		return array(
			'value' => get_bloginfo( 'version' ),
		);
	}

	public function get_is_multisite() {
		return array(
			'value' => is_multisite()? __( 'Yes', 'pojo' ) : __( 'No', 'pojo' ),
		);
	}

	public function get_site_url() {
		return array(
			'value' => get_site_url(),
		);
	}

	public function get_home_url() {
		return array(
			'value' => get_home_url(),
		);
	}

	public function get_permalink_structure() {
		global $wp_rewrite;

		return array(
			'value' => $wp_rewrite->permalink_structure,
		);
	}

	public function get_language() {
		return array(
			'value' => get_bloginfo( 'language' ),
		);
	}

	public function get_max_upload_size() {
		return array(
			'value' => size_format( wp_max_upload_size() ),
		);
	}

	public function get_timezone() {
		$timezone = get_option( 'timezone_string' );
		if ( ! $timezone ) {
			$timezone = get_option( 'gmt_offset' );
		}
		
		return array(
			'value' => $timezone,
		);
	}

	public function get_system_email() {
		return array(
			'value' => get_option( 'admin_email' ),
		);
	}

	public function get_debug_mode() {
		return array(
			'value' => WP_DEBUG ? __( 'Active', 'pojo' ) : __( 'Inactive', 'pojo' ),
		);
	}

	public function get_pojo_server_status() {
		$license_key = Pojo_Core::instance()->licenses->get_license_key();
		$update_data = Pojo_Core::instance()->licenses->api->get_version( $license_key );

		$status = __( 'Connected', 'pojo' );
		if ( ! is_object( $update_data ) || empty( $update_data->new_version ) ) {
			$status = __( 'Not Connected', 'pojo' );
		}
		
		return array(
			'value' => $status,
		);
	}
}