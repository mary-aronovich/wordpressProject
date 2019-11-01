<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Layouts {

	const LAYOUT_DEFAULT       = 'default';
	const LAYOUT_FULL          = 'full';
	const LAYOUT_SIDEBAR_RIGHT = 'sidebar_right';
	const LAYOUT_SIDEBAR_LEFT  = 'sidebar_left';
	const LAYOUT_TWO_SIDEBARS  = 'two_sidebars';
	const LAYOUT_SECTION       = 'section';
	
	private static $_instance = null;
	
	protected $_available_layouts = array();
	
	protected $_layouts = array();
	
	protected $_base_layouts = array();
	
	protected $_404_layouts = array();

	/**
	 * @return Pojo_Layouts
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new Pojo_Layouts();
		
		return self::$_instance;
	}

	public function get_available_layouts() {
		if ( empty( $this->_available_layouts ) ) {
			$base_radio_image_url = get_template_directory_uri() . '/core/assets/admin-ui/images/';

			$this->_available_layouts = array(
				self::LAYOUT_DEFAULT => array(
					'id' => '',
					'title' => __( 'Default', 'pojo' ),
					'image' => $base_radio_image_url . 'layout/default.png',
				),
				self::LAYOUT_FULL => array(
					'id' => self::LAYOUT_FULL,
					'title' => __( 'Full Width', 'pojo' ),
					'image' => $base_radio_image_url . 'layout/full.png',
				),
				self::LAYOUT_SIDEBAR_RIGHT => array(
					'id' => self::LAYOUT_SIDEBAR_RIGHT,
					'title' => ! is_rtl() ? __( 'Sidebar Right', 'pojo' ) : __( 'Sidebar Left', 'pojo' ),
					'image' => $base_radio_image_url . sprintf( 'layout/sidebar_%s.png', ! is_rtl() ? 'right' : 'left' ),
				),
				self::LAYOUT_SIDEBAR_LEFT => array(
					'id' => self::LAYOUT_SIDEBAR_LEFT,
					'title' => ! is_rtl() ? __( 'Sidebar Left', 'pojo' ) : __( 'Sidebar Right', 'pojo' ),
					'image' => $base_radio_image_url . sprintf( 'layout/sidebar_%s.png', ! is_rtl() ? 'left' : 'right' ),
				),
				self::LAYOUT_TWO_SIDEBARS => array(
					'id' => self::LAYOUT_TWO_SIDEBARS,
					'title' => __( 'Two Sidebars', 'pojo' ),
					'image' => $base_radio_image_url . 'layout/two_sidebars.png',
				),
				self::LAYOUT_SECTION => array(
					'id' => self::LAYOUT_SECTION,
					'title' => __( '100% Width', 'pojo' ),
					'image' => $base_radio_image_url . 'layout/section.png',
				),
			);
		}
		return $this->_available_layouts;
	}

	public function get_base_layouts() {
		if ( empty( $this->_base_layouts ) ) {
			$this->_base_layouts = apply_filters( 'pojo_base_layouts', array() );
		}
		return $this->_base_layouts;
	}

	public function get_cpt_layouts( $cpt = null ) {
		$layouts = $this->get_layouts();
		return ! empty( $cpt ) && isset( $layouts[ $cpt ] ) ? $layouts[ $cpt ] : $this->get_base_layouts();
	}

	public function get_cpt_layouts_with_options( $cpt = null, $include_default = false ) {
		$return = array();
		$available_layouts = $this->get_available_layouts();
		
		if ( $include_default ) {
			$return[] = $available_layouts[ self::LAYOUT_DEFAULT ];
		}
		
		foreach ( $this->get_cpt_layouts( $cpt ) as $layout ) {
			if ( isset( $available_layouts[ $layout ] ) )
				$return[] = $available_layouts[ $layout ];
		}
		return $return;
	}

	public function get_default_layout( $cpt = null ) {
		$default = apply_filters( 'pojo_default_layout', self::LAYOUT_SIDEBAR_RIGHT );
		if ( ! empty( $cpt ) )
			$default = apply_filters( "pojo_default_layout_{$cpt}", $default );
		
		return $default;
	}

	public function get_layouts() {
		if ( empty( $this->_layouts ) ) {
			$post_types = get_post_types( array( 'public' => true ) );
			
			foreach ( $post_types as $cpt ) {
				$this->_layouts[ $cpt ] = apply_filters( "pojo_available_layout_{$cpt}", $this->get_base_layouts() );
			}
		}
		
		return $this->_layouts;
	}
	
	// 404 Layouts
	public function get_404_layouts() {
		if ( empty( $this->_404_layouts ) ) {
			$this->_404_layouts = apply_filters( 'pojo_404_layouts', $this->get_base_layouts() );
		}
		return $this->_404_layouts;
	}

	public function get_404_layouts_with_options() {
		$return = array();
		$available_layouts = $this->get_available_layouts();

		foreach ( $this->get_404_layouts() as $layout ) {
			if ( isset( $available_layouts[ $layout ] ) )
				$return[] = $available_layouts[ $layout ];
		}
		return $return;
	}

	public function get_404_default_layout() {
		return apply_filters( 'pojo_404_default_layout', self::LAYOUT_FULL );
	}
	
	protected function __construct() {}
	
}