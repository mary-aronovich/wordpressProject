<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Server_Reporter extends Pojo_Info_Base_Reporter{

	public function get_title() {
		return __( 'Server Environment', 'pojo' );
	}

	public function get_fields() {
		return array(
			'os' => __( 'Operating System', 'pojo' ),
			'software' => __( 'Software', 'pojo' ),
			'mysql_version' => __( 'MySQL version', 'pojo' ),
			'php_version' => __( 'PHP Version', 'pojo' ),
			'php_max_input_vars' => __( 'PHP Max Input Vars', 'pojo' ),
			'php_max_post_size' => __( 'PHP Max Post Size', 'pojo' ),
			'gd_installed' => __( 'GD Installed', 'pojo' ),
		);
	}

	public function get_os() {
		return array(
			'value' => PHP_OS,
		);
	}

	public function get_software() {
		return array(
			'value' => $_SERVER['SERVER_SOFTWARE'],
		);
	}

	public function get_php_version() {
		$result = array(
			'value' => PHP_VERSION,
		);

		if ( version_compare( $result['value'], '5.4', '<' ) ) {
			$result['recommendation'] = __( 'We recommend to use php 5.4 or higher', 'pojo' );
		}

		return $result;
	}

	public function get_php_max_input_vars() {
		return array(
			'value' => ini_get( 'max_input_vars' ),
		);
	}

	public function get_php_max_post_size() {
		return array(
			'value' => ini_get( 'post_max_size' ),
		);
	}

	public function get_gd_installed() {
		return array(
			'value' => extension_loaded( 'gd' ) ? __( 'Yes', 'pojo' ) : __( 'No', 'pojo' ),
		);
	}

	public function get_mysql_version() {
		global $wpdb;

		return array(
			'value' => $wpdb->db_version(),
		);
	}
}