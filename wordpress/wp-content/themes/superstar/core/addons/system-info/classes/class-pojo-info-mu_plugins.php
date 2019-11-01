<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MU_Plugins_Reporter extends Pojo_Info_Base_Reporter {

	private $plugins;

	private function _get_must_use_plugins(  ) {
		if ( ! $this->plugins ) {
			$this->plugins = get_mu_plugins();
		}

		return $this->plugins;
	}

	public function is_enabled() {
		return !! $this->_get_must_use_plugins();
	}

	public function get_title() {
		return __( 'Must-Use Plugins', 'pojo' );
	}

	public function get_fields() {
		return array(
			'must_use_plugins' => __( 'Must Use Plugins', 'pojo' ),
		);
	}

	public function get_must_use_plugins() {
		return array(
			'value' => $this->_get_must_use_plugins(),
		);
	}
}