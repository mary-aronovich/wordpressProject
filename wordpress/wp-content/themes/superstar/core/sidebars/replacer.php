<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Theme_Sidebars_Replacer {

	protected $_original_wp_sidebars_widgets;

	public function store_wp_sidebars() {
		$this->_original_wp_sidebars_widgets = $GLOBALS['_wp_sidebars_widgets'];
	}

	public function customizer_sidebar() {
		$cpt = get_post_type();
		$override_sidebar = '';
		if ( is_singular() )
			$override_sidebar = get_theme_mod( "{$cpt}_main_sidebar" );
		elseif ( is_archive() )
			$override_sidebar = get_theme_mod( "{$cpt}_main_sidebar_archive" );
		
		if ( empty( $override_sidebar ) )
			$override_sidebar = get_theme_mod( 'pojo_general_main_sidebar' );
		
		if ( empty( $override_sidebar ) )
			return;

		if ( ! isset( $this->_original_wp_sidebars_widgets[ $override_sidebar ] ) )
			return;

		$GLOBALS['_wp_sidebars_widgets']['pojo-main-sidebar'] = $this->_original_wp_sidebars_widgets[ $override_sidebar ];
	}

	public function sidebar_replace() {
		global $_wp_sidebars_widgets;

		$core_sidebars = Pojo_Core::instance()->sidebars->get_core_sidebars();
		if ( empty( $core_sidebars ) )
			return;

		foreach ( $core_sidebars as $sidebar_id => $sidebar_args ) {
			$override_sidebar = atmb_get_field( 'pojo_override_sidebar_' . $sidebar_id );
			if ( empty( $override_sidebar ) )
				continue;
			
			if ( '_hide' === $override_sidebar ) {
				$_wp_sidebars_widgets[ $sidebar_id ] = array();
				continue;
			}
			
			if ( ! isset( $this->_original_wp_sidebars_widgets[ $override_sidebar ] ) )
				continue;

			$_wp_sidebars_widgets[ $sidebar_id ] = $this->_original_wp_sidebars_widgets[ $override_sidebar ];
		}
	}

	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'wp_head', array( &$this, 'store_wp_sidebars' ), 1 );
			add_action( 'wp_head', array( &$this, 'customizer_sidebar' ), 20 );
			add_action( 'wp_head', array( &$this, 'sidebar_replace' ), 30 );
		}
	}

}