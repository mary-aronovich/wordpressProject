<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Image extends Pojo_Widget_Base {
	
	public function _valid_align( $option ) {
		if ( ! empty( $option ) && ! in_array( $option, array( 'left', 'right', 'center' ) ) )
			$option = '';

		return $option;
	}
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'image',
			'title' => __( 'Choose Image:', 'pojo' ),
			'type' => 'image',
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'alt_text',
			'title' => __( 'Alt Text:', 'pojo' ),
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'align',
			'title' => __( 'Align:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'None', 'pojo' ),
				'right' => __( 'Right', 'pojo' ),
				'left' => __( 'Left', 'pojo' ),
				'center' => __( 'Center', 'pojo' ),
			),
			'std' => '',
			'filter' => array( &$this, '_valid_align' ),
		);

		$this->_form_fields[] = array(
			'id' => 'link',
			'title' => __( 'Link:', 'pojo' ),
			'std' => '',
			'filter' => 'esc_url_raw',
		);

		$this->_form_fields[] = array(
			'id'      => 'target_link',
			'title'   => __( 'Open Link in', 'pojo' ),
			'type'    => 'select',
			'options' => array(
				'' => __( 'Same Window', 'pojo' ),
				'blank' => __( 'New Tab or Window', 'pojo' ),
			),
			'std'     => '',
		);

		$this->_form_fields[] = array(
			'id' => 'caption',
			'title' => __( 'Caption:', 'pojo' ),
			'std' => '',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'hover_animation',
			'title' => __( 'Hover Animation:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $this->_get_hover_animation_options(),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		parent::__construct(
			'pojo_image',
			__( 'Image', 'pojo' ),
			array( 'description' => __( 'Add an image to your site', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		if ( empty( $instance['image'] ) )
			return;

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		$image_class_html = ! empty( $instance['hover_animation'] ) ? ' class="hover-' . $instance['hover_animation'] . '"' : '';
		$image_html = sprintf( '<img src="%s" alt="%s"%s />', esc_attr( $instance['image'] ), esc_attr( $instance['alt_text'] ), $image_class_html );
		
		if ( ! empty( $instance['link'] ) ) {
			$target = '';
			if ( ! empty( $instance['target_link'] ) && 'blank' === $instance['target_link'] ) {
				$target = ' target="_blank"';
			}
			$image_html = sprintf( '<a href="%s"%s>%s</a>', $instance['link'], $target, $image_html );
		}
		
		$align_class = 'align-';
		if ( ! in_array( $instance['align'], array( 'left', 'right', 'center' ) ) )
			$instance['align'] = '';
		
		if ( empty( $instance['align'] ) )
			$instance['align'] = 'none';
		
		$align_class .= $instance['align'];

		if ( ! empty( $instance['caption'] ) )
			$image_html .= sprintf( '<p class="widget-image-text">%s</p>', $instance['caption'] );
		
		printf( '<div class="widget-image %s">%s</div>', $align_class, $image_html );
		
		echo $args['after_widget'];
	}


}