<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Network_Plugins_Reporter extends Pojo_Info_Base_Reporter {

	private $plugins;

	public function get_title() {
		return __( 'Network Plugins', 'pojo' );
	}

	private function _get_network_plugins() {
		if ( ! $this->plugins ) {
			$active_plugins = get_site_option( 'active_sitewide_plugins' );
			$this->plugins  = array_intersect_key( get_plugins(), array_flip( $active_plugins ) );
		}

		return $this->plugins;
	}

	public function is_enabled() {
		if ( ! is_multisite() ) {
			return false;
		};
		
		return ! ! $this->_get_network_plugins();
	}

	public function get_fields() {
		return array(
			'network_active_plugins' => __( 'Network Plugins', 'pojo' ),
		);
	}

	public function get_network_active_plugins() {
		return array(
			'value' => $this->_get_network_plugins(),
		);
	}
}