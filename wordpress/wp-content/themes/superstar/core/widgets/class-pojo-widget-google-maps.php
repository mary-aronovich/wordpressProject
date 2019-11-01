<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Google_Maps extends Pojo_Widget_Base {
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'address',
			'title' => __( 'Address Text Field:', 'pojo' ),
			'std' => '',
			'placeholder' => __( '5th Ave, New York, United States', 'pojo' ),
			'filter' => 'sanitize_text_field',
		);
		
		$zoom_options = array();
		foreach ( range( 3, 19 ) as $val ) {
			$zoom_options[ $val ] = $val;
		}
		$this->_form_fields[] = array(
			'id' => 'zoom',
			'title' => __( 'Zoom:', 'pojo' ),
			'type' => 'select',
			'std' => '10',
			'options' => $zoom_options,
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'height',
			'title' => __( 'Height', 'pojo' ),
			'std' => '200px',
			'placeholder' => __( '200px', 'pojo' ),
			'filter' => 'sanitize_text_field',
		);
		
		parent::__construct(
			'pojo_google_maps',
			__( 'Maps', 'pojo' ),
			array( 'description' => __( 'Add a google map with your location', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		if ( empty( $instance['address'] ) )
			return;
		
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		if ( 0 === absint( $instance['zoom'] ) )
			$instance['zoom'] = 10;
		
		printf(
			'<div class="pojo-google-map-wrap custom-embed"><iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=%s&amp;t=m&amp;z=%d&amp;output=embed&amp;iwloc=near"%s></iframe></div>',
			urlencode( $instance['address'] ),
			absint( $instance['zoom'] ),
			! empty( $instance['height'] ) ? ' style="height:' . $instance['height'] . ';"' : ''
		);
		
		echo $args['after_widget'];
	}


}