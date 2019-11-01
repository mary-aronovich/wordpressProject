<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Recent_Posts extends Pojo_Widget_Base {
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);
		
		$styles = apply_filters( 'pojo_recent_posts_layouts', array() );
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
			'title' => __( 'Categories:', 'pojo' ),
			'type' => 'multi_taxonomy',
			'taxonomy' => 'category',
			'std' => array(),
		);
		
		$this->_form_fields[] = array(
			'id' => 'posts_per_page',
			'title' => __( 'Number Posts:', 'pojo' ),
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
			'id' => 'thumbnail',
			'title' => __( 'Thumbnail:', 'pojo' ),
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
			'id' => 'except',
			'title' => __( 'Excerpt:', 'pojo' ),
			'type' => 'select',
			'std' => 'show',
			'options' => array(
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'except_length_words',
			'title' => __( 'Number Words:', 'pojo' ),
			'std' => get_option( 'archive_excerpt_number_words' ),
			'filter' => array( &$this, '_valid_number' ),
		);
		
		if ( current_theme_supports( 'pojo-recent-post-metadata' ) ) {

			$this->_form_fields = apply_filters( 'pojo_recent_posts_widget_fields_before_metadata', $this->_form_fields, $this );

			$this->_form_fields[] = array(
				'id' => 'metadata_readmore',
				'title' => __( 'Read More:', 'pojo' ),
				'type' => 'select',
				'std' => 'hide',
				'options' => array(
					'hide' => __( 'Hide', 'pojo' ),
					'show' => __( 'Show', 'pojo' ),
				),
				'filter' => array( &$this, '_valid_by_options' ),
			);

			$this->_form_fields[] = array(
				'id' => 'text_readmore_mode',
				'title' => __( 'Custom Read More:', 'pojo' ),
				'std' => '',
			);

			$this->_form_fields[] = array(
				'id' => 'metadata_date',
				'title' => __( 'Date:', 'pojo' ),
				'type' => 'select',
				'std' => 'hide',
				'options' => array(
					'hide' => __( 'Hide', 'pojo' ),
					'show' => __( 'Show', 'pojo' ),
				),
				'filter' => array( &$this, '_valid_by_options' ),
			);

			$this->_form_fields[] = array(
				'id' => 'metadata_time',
				'title' => __( 'Time:', 'pojo' ),
				'type' => 'select',
				'std' => 'hide',
				'options' => array(
					'hide' => __( 'Hide', 'pojo' ),
					'show' => __( 'Show', 'pojo' ),
				),
				'filter' => array( &$this, '_valid_by_options' ),
			);

			$this->_form_fields[] = array(
				'id' => 'metadata_comments',
				'title' => __( 'Comments:', 'pojo' ),
				'type' => 'select',
				'std' => 'hide',
				'options' => array(
					'hide' => __( 'Hide', 'pojo' ),
					'show' => __( 'Show', 'pojo' ),
				),
				'filter' => array( &$this, '_valid_by_options' ),
			);

			$this->_form_fields[] = array(
				'id' => 'metadata_author',
				'title' => __( 'Author:', 'pojo' ),
				'type' => 'select',
				'std' => 'hide',
				'options' => array(
					'hide' => __( 'Hide', 'pojo' ),
					'show' => __( 'Show', 'pojo' ),
				),
				'filter' => array( &$this, '_valid_by_options' ),
			);
			
			$this->_form_fields = apply_filters( 'pojo_recent_posts_widget_fields_after_metadata', $this->_form_fields, $this );
			
		} // End theme support with metadata
		
		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Custom', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'end',
		);
		
		parent::__construct(
			'pojo_recent_posts',
			__( 'Posts', 'pojo' ),
			array( 'description' => __( 'Display recent posts by category', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		global $_current_widget_instance;

		$_current_widget_instance = $instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );

		$query_args = array(
			'post_type' => 'post',
			'posts_per_page' => $instance['posts_per_page'],
			'order' => $instance['order'],
			'orderby' => $instance['orderby'],
			'offset' => absint( $instance['offset'] ),
		);

		if ( ! empty( $instance['category'] ) && is_array( $instance['category'] ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field' => 'id',
					'terms' => $instance['category'],
					'include_children' => false,
				),
			);
		}

		$query_args = apply_filters( 'pojo_recent_posts_widget_query_args', $query_args, $instance );
		$recent_posts = new WP_Query( $query_args );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		if ( $recent_posts->have_posts() ) :
			do_action( 'pojo_recent_post_before_content_loop', $instance['style'] );
			while ( $recent_posts->have_posts() ) : $recent_posts->the_post();
				pojo_get_content_template_part( 'recent_post', $instance['style'] );
			endwhile;
			do_action( 'pojo_recent_post_after_content_loop', $instance['style'] );
			wp_reset_postdata();
		else :
			printf( '<p>%s</p>', __( 'No posts found.', 'pojo' ) );
		endif;
		
		echo $args['after_widget'];
	}

}
