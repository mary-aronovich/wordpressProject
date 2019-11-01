<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Menu_Anchor extends Pojo_Widget_Base {

	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'anchor',
			'title' => __( 'Name of Menu Anchor:', 'pojo' ),
			'desc' => __( 'This name will be the ID you will have to use in your one page menu.', 'pojo' ),
			'placeholder' => __( 'For Example: About', 'pojo' ),
			'std' => '',
		);
		
		parent::__construct(
			'pojo_menu_anchor',
			__( 'Menu Anchor', 'pojo' ),
			array( 'description' => __( 'Menu Anchor', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		
		if ( empty( $instance['anchor'] ) )
			return;
		
		printf( '<div id="%s" class="pojo-menu-anchor"></div>', $instance['anchor'] );
	}


}