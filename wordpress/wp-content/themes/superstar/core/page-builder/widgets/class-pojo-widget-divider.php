<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Divider extends Pojo_Widget_Base {

	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'style',
			'title' => __( 'Divider Style:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'space' => __( 'Space', 'pojo' ),
				'solid' => __( 'Solid', 'pojo' ),
				'double' => __( 'Double', 'pojo' ),
				'dotted' => __( 'Dotted', 'pojo' ),
				'dashed' => __( 'Dashed', 'pojo' ),
			),
			'std' => 'space',
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'weight',
			'title' => __( 'Divider Weight:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'1' => '1px',
				'2' => '2px',
				'3' => '3px',
				'4' => '4px',
				'5' => '5px',
				'6' => '6px',
				'7' => '7px',
				'8' => '8px',
				'9' => '9px',
				'10' => '10px',
			),
			'std' => '1',
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'color',
			'title' => __( 'Divider Color:', 'pojo' ),
			'type' => 'color',
			'std' => '#999999',
			//'filter' => 'sanitize_text_field',
		);
		
		$this->_form_fields[] = array(
			'id' => 'width',
			'title' => __( 'Width:', 'pojo' ),
			'std' => '100%',
			'filter' => 'sanitize_text_field',
		);
		
		$this->_form_fields[] = array(
			'id' => 'align',
			'title' => __( 'Align:', 'pojo' ),
			'type' => 'select',
			'std' => 'none',
			'options' => array(
				'none' => __( 'None', 'pojo' ),
				'right' => __( 'Right', 'pojo' ),
				'left' => __( 'Left', 'pojo' ),
				'center' => __( 'Center', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'margin_top',
			'title' => __( 'Margin Top:', 'pojo' ),
			'std' => '20px',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'margin_bottom',
			'title' => __( 'Margin Bottom:', 'pojo' ),
			'std' => '20px',
			'filter' => 'sanitize_text_field',
		);
		
		parent::__construct(
			'pojo_divider',
			__( 'Divider', 'pojo' ),
			array( 'description' => __( 'Divider', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		$wrap_style_array = $hr_style_array = array();
		if ( ! empty( $instance['margin_top'] ) )
			$wrap_style_array[] = 'margin-top:' . $instance['margin_top'];
		
		if ( ! empty( $instance['margin_bottom'] ) )
			$wrap_style_array[] = 'margin-bottom:' . $instance['margin_bottom'];
		
		if ( ! empty( $instance['width'] ) )
			$wrap_style_array[] = 'width:' . $instance['width'];
		
		if ( ! empty( $instance['color'] ) )
			$hr_style_array[] = 'border-color:' . $instance['color'];
		
		if ( ! empty( $instance['weight'] ) )
			$hr_style_array[] = 'border-width:' . $instance['weight'] . 'px';
		
		echo $args['before_widget'];
		
		printf(
			'<div class="pojo-divider align%s divider-style-%s"%s>
				<hr%s />
			</div>',
			esc_attr( $instance['align'] ),
			$instance['style'],
			! empty( $wrap_style_array ) ? ' style="' . esc_attr( implode( ';', $wrap_style_array ) ) . '"' : '',
			! empty( $hr_style_array ) ? ' style="' . esc_attr( implode( ';', $hr_style_array ) ) . '"' : ''
		);
		
		echo $args['after_widget'];
	}


}