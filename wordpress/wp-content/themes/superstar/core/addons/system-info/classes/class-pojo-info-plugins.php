<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Plugins_Reporter extends Pojo_Info_Base_Reporter {

	private $plugins;

	private function _get_plugins() {
		if ( ! $this->plugins ) {
			$active_plugins = get_option( 'active_plugins' );
			$this->plugins  = array_intersect_key( get_plugins(), array_flip( $active_plugins ) );
		}

		return $this->plugins;
	}

	public function get_title() {
		return __( 'Active Plugins', 'pojo' );
	}

	public function is_enabled() {
		return ! ! $this->_get_plugins();
	}

	public function get_fields() {
		return array(
			'active_plugins' => __( 'Active Plugins', 'pojo' ),
		);
	}

	public function get_active_plugins() {
		return array(
			'value' => $this->_get_plugins(),
		);
	}
}