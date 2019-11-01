<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Rev_Slider extends Pojo_Widget_Base {
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
			//'filter' => 'sanitize_text_field',
		);

		/** @var $arr_sliders RevSlider[] */
		$rev_slider_options = array();

		$rev_slider = new RevSlider();
		$arr_sliders = $rev_slider->getArrSliders();

		if ( empty( $arr_sliders ) )
			$rev_slider_options[] = __( 'No have any register Rev Slider in website', 'pojo' );
		else
			foreach ( $arr_sliders as $slider)
				$rev_slider_options[ $slider->getAlias() ] = $slider->getShowTitle();
		
		$this->_form_fields[] = array(
			'id' => 'slider',
			'title' => __( 'Rev Slider:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $rev_slider_options,
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		parent::__construct(
			'pojo_rev_slider',
			__( 'Rev Slider', 'pojo' ),
			array( 'description' => __( 'Rev Slider', 'pojo' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		if ( empty( $instance['slider'] ) )
			return;

		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		echo do_shortcode( sprintf( '[rev_slider %s]', $instance['slider'] ) );
		
		echo $args['after_widget'];
	}

	public function widget_plain_text( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		printf( '[rev_slider %s]', $instance['slider'] );
	}
	
}