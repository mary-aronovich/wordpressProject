<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Sidebar extends Pojo_Widget_Base {
	
	protected function _get_sidebars() {
		global $wp_registered_sidebars;

		$return_sidebars = array( '' => __( 'Default', 'pojo' ) );
		if ( ! empty( $wp_registered_sidebars ) ) {
			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar_args ) {
				$return_sidebars[ $sidebar_id ] = $sidebar_args['name'];
			}
		}
		
		return $return_sidebars;
	}

	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'sidebar',
			'title' => __( 'Sidebar:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $this->_get_sidebars(),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		parent::__construct(
			'pojo_sidebar',
			__( 'Sidebar', 'pojo' ),
			array( 'description' => __( 'Sidebar', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		if ( empty( $instance['sidebar'] ) )
			return;

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		dynamic_sidebar( $instance['sidebar'] );
		
		echo $args['after_widget'];
	}


}