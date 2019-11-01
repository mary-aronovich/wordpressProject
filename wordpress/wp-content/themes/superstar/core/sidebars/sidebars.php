<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Theme_Sidebars {

	/**
	 * @var Pojo_Theme_Sidebars_Replacer
	 */
	public $replacer;

	/**
	 * @var Pojo_Theme_Sidebars_Admin_UI
	 */
	public $ui;

	public function get_core_sidebars() {
		global $wp_registered_sidebars;

		$return_sidebars = array();
		if ( ! empty( $wp_registered_sidebars ) ) {
			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar_args ) {
				$return_sidebars[ $sidebar_id ] = $sidebar_args['name'];
			}
		}

		return (array) apply_filters( 'pojo_get_core_sidebars', $return_sidebars );
	}

	public function __construct() {
		include( 'replacer.php' );
		include( 'admin-ui.php' );
		
		$this->replacer = new Pojo_Theme_Sidebars_Replacer();
		$this->ui       = new Pojo_Theme_Sidebars_Admin_UI();
	}
	
}