<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_User_Reporter extends Pojo_Info_Base_Reporter {

	public function get_title() {
		return __( 'User', 'pojo' );
	}

	public function get_fields() {
		return array(
			'locale' => __( 'WP Profile lang', 'pojo' ),
			'agent' => __( 'User Agent', 'pojo' ),
		);
	}

	public function get_locale() {
		return array(
			'value' => get_locale(),
		);
	}

	public function get_agent() {
		return array(
			'value' => $_SERVER['HTTP_USER_AGENT'],
		);
	}
}