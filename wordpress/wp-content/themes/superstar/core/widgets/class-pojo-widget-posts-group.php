<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Posts_Group extends Pojo_Widget_Base {
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);
		
		$styles = apply_filters( 'pojo_posts_group_layouts', array() );
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
		
		// Featured Post Options
		$this->_form_fields[] = array(
			'id' => 'heading_main_post_options',
			'type' => 'heading',
			'title' => __( 'Featured Post', 'pojo' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'featured_post_options',
			'title' => __( 'Featured Post Options', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'start',
		);

		$this->_form_fields[] = array(
			'id' => 'featured_show_title',
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
			'id' => 'featured_thumbnail',
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
			'id' => 'featured_except',
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
			'id' => 'featured_except_length_words',
			'title' => __( 'Number Words:', 'pojo' ),
			'std' => get_option( 'archive_excerpt_number_words' ),
			'filter' => array( &$this, '_valid_number' ),
		);

		$this->_form_fields = apply_filters( 'pojo_posts_group_widget_fields_before_featured_metadata', $this->_form_fields, $this );

		$this->_form_fields[] = array(
			'id' => 'featured_metadata_readmore',
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
			'id' => 'featured_text_readmore_mode',
			'title' => __( 'Custom Read More:', 'pojo' ),
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'featured_metadata_date',
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
			'id' => 'featured_metadata_time',
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
			'id' => 'featured_metadata_comments',
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
			'id' => 'featured_metadata_author',
			'title' => __( 'Author:', 'pojo' ),
			'type' => 'select',
			'std' => 'hide',
			'options' => array(
				'hide' => __( 'Hide', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields = apply_filters( 'pojo_posts_group_widget_fields_after_featured_metadata', $this->_form_fields, $this );

		$this->_form_fields[] = array(
			'id' => 'main_post_options',
			'title' => __( 'Custom', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'end',
		);
		// End Featured Post Options
		
		// Post Options
		$this->_form_fields[] = array(
			'id' => 'heading_post_options',
			'type' => 'heading',
			'title' => __( 'Post Options', 'pojo' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'post_options',
			'title' => __( 'Post Options', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'start',
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

		$this->_form_fields = apply_filters( 'pojo_posts_group_widget_fields_before_metadata', $this->_form_fields, $this );

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

		$this->_form_fields = apply_filters( 'pojo_posts_group_widget_fields_after_metadata', $this->_form_fields, $this );
		
		$this->_form_fields[] = array(
			'id' => 'post_options',
			'title' => __( 'Custom', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'end',
		);
		// End Post Options
		
		// Advanced Options
		$this->_form_fields[] = array(
			'id' => 'heading_advanced_options',
			'type' => 'heading',
			'title' => __( 'Advanced Options', 'pojo' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'advanced_options',
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
			'id' => 'advanced_options',
			'title' => __( 'Custom', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'end',
		);
		// End Advanced Options
		
		parent::__construct(
			'pojo_posts_group',
			__( 'Posts Group', 'pojo' ),
			array( 'description' => __( 'Display posts group by category', 'pojo' ), )
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
		$magazine_posts = new WP_Query( $query_args );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		if ( $magazine_posts->have_posts() ) :
			do_action( 'pojo_posts_group_before_content_loop', $instance['style'] );
			$_current_widget_instance['is_first_item'] = true;
			while ( $magazine_posts->have_posts() ) : $magazine_posts->the_post();
				pojo_get_content_template_part( 'posts_group', $instance['style'] );
				$_current_widget_instance['is_first_item'] = false;
			endwhile;
			do_action( 'pojo_posts_group_after_content_loop', $instance['style'] );
			wp_reset_postdata();
		else :
			printf( '<p>%s</p>', __( 'No posts found.', 'pojo' ) );
		endif;
		
		echo $args['after_widget'];
	}

}