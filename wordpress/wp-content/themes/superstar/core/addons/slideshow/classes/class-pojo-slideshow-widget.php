<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Slideshow_Widget extends Pojo_Widget_Base {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		global $pojo_slideshow;
		
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
			'filter' => 'sanitize_text_field',
		);
		
		$options = $pojo_slideshow->helpers->get_all_sliders();
		if ( ! empty( $options ) ) {
			$std = array_keys( $options );
			$std = $std[0];
			$this->_form_fields[] = array(
				'id' => 'slide',
				'title' => __( 'Choose Slider:', 'pojo' ),
				'type' => 'select',
				'std' => $std,
				'options' => $options,
				'filter' => array( &$this, '_valid_by_options' ),
			);
		} else {
			$this->_form_fields[] = array(
				'id' => 'lbl_no_found',
				'title' => sprintf( '<a href="%s">%s</a>', admin_url( 'post-new.php?post_type=pojo_slideshow' ), __( 'Create a Slider', 'pojo' ) ),
				'type' => 'label',
			);
		}

		$this->_form_fields[] = array(
			'id' => 'lbl_all_slideshows',
			'title' => sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=pojo_slideshow' ), __( 'All Slideshows', 'pojo' ) ),
			'type' => 'label',
		);
		
		parent::__construct(
			'pojo_slideshow_widget',
			__( 'Slideshows', 'pojo' ),
			array( 'description' => __( 'Display your slideshow', 'pojo' ), )
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

		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );
		$args = $this->_parse_widget_args( $args, $instance );
		
		if ( empty( $instance['slide'] ) )
			return;
		
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		echo do_shortcode( sprintf( '[pojo-slideshow id="%d"]', $instance['slide'] ) );

		echo $args['after_widget'];
	}

}

// Register this widget in Page Builder
function pojo_slideshow_page_builder_register_widget( $widgets ) {
	$widgets[] = 'Pojo_Slideshow_Widget';
	return $widgets;
}
add_action( 'pojo_builder_widgets', 'pojo_slideshow_page_builder_register_widget' );