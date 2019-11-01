<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Gallery extends Pojo_Widget_Base {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
			'filter' => 'sanitize_text_field',
		);

		$galleries = new WP_Query( array(
			'post_type' => 'pojo_gallery',
			'posts_per_page' => -1,
		) );

		$options = array();
		if ( $galleries->have_posts() ) {
			$galleries = $galleries->get_posts();

			foreach ( $galleries as $gallery ) {
				$options[ $gallery->ID ] = $gallery->post_title;
			}
		}

		if ( ! empty( $options ) ) {
			$std = array_keys( $options );
			$std = $std[0];
			$this->_form_fields[] = array(
				'id' => 'gallery',
				'title' => __( 'Choose Gallery:', 'pojo' ),
				'type' => 'select',
				'std' => $std,
				'options' => $options,
				'filter' => array( &$this, '_valid_by_options' ),
			);
		} else {
			$this->_form_fields[] = array(
				'id' => 'lbl_no_found',
				'title' => sprintf( '<a href="%s">%s</a>', admin_url( 'post-new.php?post_type=pojo_gallery' ), __( 'Create a Gallery', 'pojo' ) ),
				'type' => 'label',
			);
		}

		$this->_form_fields[] = array(
			'id' => 'lbl_all_galleries',
			'title' => sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=pojo_gallery' ), __( 'All Galleries', 'pojo' ) ),
			'type' => 'label',
		);
		
		parent::__construct(
			'pojo_gallery',
			__( 'Gallery', 'pojo' ),
			array( 'description' => __( 'Display a gallery', 'pojo' ), )
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
		
		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );

		if ( empty( $instance['gallery'] ) )
			return;

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		do_action( 'pojo_gallery_print_front', $instance['gallery'] );

		echo $args['after_widget'];
	}

}