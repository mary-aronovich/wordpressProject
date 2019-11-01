<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_WC_Products extends Pojo_Widget_Base {

	public function add_product_post_class( $classes ) {
		$classes[] = 'product';

		return $classes;
	}
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'per_page',
			'title' => __( 'Number Products:', 'pojo' ),
			'std' => 5,
			'filter' => array( &$this, '_valid_number' ),
		);

		$this->_form_fields[] = array(
			'id' => 'show',
			'title' => __( 'Show:', 'pojo' ),
			'type' => 'select',
			'std' => 'show',
			'options' => array(
				''         => __( 'All Products', 'pojo' ),
				'featured' => __( 'Featured Products', 'pojo' ),
				'onsale'   => __( 'On-sale Products', 'pojo' ),
				'toprated'   => __( 'Top Rated Products', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'orderby',
			'title' => __( 'Order by:', 'pojo' ),
			'type' => 'select',
			'std' => 'date',
			'options' => array(
				'date'   => __( 'Date', 'pojo' ),
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
			'title' => _x( 'Order', 'Sorting order', 'pojo' ),
			'type' => 'select',
			'std' => 'date',
			'options' => array(
				'asc'  => __( 'ASC', 'pojo' ),
				'desc' => __( 'DESC', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'columns',
			'title' => __( 'Columns:', 'pojo' ),
			'std' => 4,
			'filter' => array( &$this, '_valid_number' ),
		);
		
		parent::__construct(
			'pojo_wc_products',
			__( 'Recent Products', 'pojo' ),
			array( 'description' => __( 'Recent Products', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );

		add_filter( 'post_class', array( $this, 'add_product_post_class' ) );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		switch ( $instance['show'] ) {
			case 'featured' :
				$shortcode = 'featured_products';
				break;
			
			case 'onsale' :
				$shortcode = 'sale_products';
				break;
			
			case 'toprated' :
				$shortcode = 'top_rated_products';
				break;
			
			default :
				$shortcode = 'recent_products';
				break;
		}
		
		$attributes = array();
		foreach ( array( 'per_page', 'orderby', 'order', 'columns' ) as $attr_key ) {
			if ( ! empty( $instance[ $attr_key ] ) )
				$attributes[] = "{$attr_key}=\"{$instance[ $attr_key ]}\"";
		}
		
		echo do_shortcode( sprintf( '[%s %s]', $shortcode, implode( ' ', $attributes ) ) );
		
		echo $args['after_widget'];

		remove_filter( 'post_class', array( $this, 'add_product_post_class' ) );
	}
}
