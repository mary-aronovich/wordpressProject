<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Recent_Galleries extends Pojo_Widget_Base {

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
		
		$styles = apply_filters( 'pojo_recent_galleries_layouts', array() );
		$std = array_keys( $styles );
		$std = array_shift( $std );
		$this->_form_fields[] = array(
			'id' => 'style',
			'title' => __( 'Style:', 'pojo' ),
			'type' => 'select',
			'std' => $std,
			'options' => $styles,
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'category',
			'title' => __( 'Category:', 'pojo' ),
			'type' => 'multi_taxonomy',
			'taxonomy' => 'pojo_gallery_cat',
			'std' => array(),
		);

		$this->_form_fields[] = array(
			'id' => 'posts_per_page',
			'title' => __( 'Number Galleries:', 'pojo' ),
			'std' => get_option( 'posts_per_page' ),
			'filter' => array( &$this, '_valid_number' ),
		);

		$this->_form_fields[] = array(
			'id' => 'show_title',
			'title' => __( 'Title:', 'pojo' ),
			'type' => 'select',
			'std' => 'show',
			'options' => array(
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'show_category',
			'title' => __( 'Categories:', 'pojo' ),
			'type' => 'select',
			'std' => 'show',
			'options' => array(
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Advanced Options', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'start',
		);

		$this->_form_fields[] = array(
			'id' => 'orderby',
			'title' => __( 'Order By:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => array(
				'' => __( 'Date', 'pojo' ),
				'menu_order' => __( 'Menu Order', 'pojo' ),
				'title' => __( 'Title', 'pojo' ),
				'author' => __( 'Author', 'pojo' ),
				'name' => __( 'Post Slug', 'pojo' ),
				'modified' => __( 'Modified', 'pojo' ),
				'comment_count' => __( 'Comment Count', 'pojo' ),
				'ID' => __( 'Post ID', 'pojo' ),
				'rand' => __( 'Random', 'pojo' ),
				'none' => __( 'None', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'order',
			'title' => __( 'Order:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => array(
				'' => __( 'Descending', 'pojo' ),
				'ASC' => __( 'Ascending', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'offset',
			'title' => __( 'Offset:', 'pojo' ),
			'std' => 0,
			'filter' => 'absint',
			'desc' => __( 'Number of post to displace or pass over', 'pojo' ),
		);

		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Custom', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'end',
		);

		parent::__construct(
			'pojo_recent_galleries',
			__( 'Recent Galleries', 'pojo' ),
			array( 'description' => __( 'Display latest galleries by category', 'pojo' ), )
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
		global $_current_widget_instance;

		$_current_widget_instance = $instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );

		$query_args = array(
			'post_type' => 'pojo_gallery',
			'posts_per_page' => $instance['posts_per_page'],
			'order' => $instance['order'],
			'orderby' => $instance['orderby'],
			'offset' => absint( $instance['offset'] ),
		);

		if ( ! empty( $instance['category'] ) && is_array( $instance['category'] ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'pojo_gallery_cat',
					'field' => 'id',
					'terms' => $instance['category'],
					'include_children' => false,
				),
			);
		}
		$recent_posts = new WP_Query( $query_args );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		if ( $recent_posts->have_posts() ) :
			do_action( 'pojo_recent_gallery_before_content_loop', $instance['style'] );
			while ( $recent_posts->have_posts() ) : $recent_posts->the_post();
				pojo_get_content_template_part( 'recent_gallery', $instance['style'] );
			endwhile;
			do_action( 'pojo_recent_gallery_after_content_loop', $instance['style'] );
			wp_reset_postdata();
		else :
			printf( '<p>%s</p>', __( 'No found posts.', 'pojo' ) );
		endif;

		echo $args['after_widget'];
	}

}