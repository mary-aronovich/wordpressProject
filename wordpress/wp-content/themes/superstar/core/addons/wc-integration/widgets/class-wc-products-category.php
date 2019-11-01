<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_WC_Products_Category extends Pojo_Widget_Base {

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
			'id' => 'category',
			'title' => __( 'Category:', 'pojo' ),
			'type' => 'multi_taxonomy',
			'taxonomy' => 'product_cat',
			'std' => array(),
		);

		$this->_form_fields[] = array(
			'id' => 'per_page',
			'title' => __( 'Number Posts:', 'pojo' ),
			'std' => 5,
			'filter' => array( &$this, '_valid_number' ),
		);

		$this->_form_fields[] = array(
			'id' => 'orderby',
			'title' => __( 'Order by:', 'pojo' ),
			'type' => 'select',
			'std' => 'date',
			'options' => array(
				'date'   => __( 'Date', 'pojo' ),
				'rand'   => __( 'Random', 'pojo' ),
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
			'pojo_wc_products_category',
			__( 'Products by Category', 'pojo' ),
			array( 'description' => __( 'Products by Category', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		global $woocommerce_loop;
		
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );

		// Default ordering args
		$ordering_args = WC()->query->get_catalog_ordering_args( $instance['orderby'], $instance['order'] );

		$query_args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $ordering_args['orderby'],
			'order' 				=> $ordering_args['order'],
			'posts_per_page' 		=> $instance['per_page'],
			'meta_query' 			=> array(),
			'tax_query'             => array(),
		);
		
		if ( ! empty( $instance['category'] ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'terms' => $instance['category'],
				'field' => 'id',
				'operator' => 'IN',
			);
		}

		if ( isset( $ordering_args['meta_key'] ) ) {
			$query_args['meta_key'] = $ordering_args['meta_key'];
		}

		if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
			$query_args = $this->get_wc_legacy_visibility_parse_query( $query_args );
		}

		$products = new WP_Query( $query_args );
		
		if ( ! $products->have_posts() )
			return;
		
		add_filter( 'post_class', array( $this, 'add_product_post_class' ) );
		
		$woocommerce_loop['columns'] = $instance['columns'];
		
		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		echo '<div class="woocommerce columns-' . $instance['columns'] . '">';
		
		woocommerce_product_loop_start();
		while ( $products->have_posts() ) : $products->the_post();
			wc_get_template_part( 'content', 'product' );
		endwhile;
		woocommerce_product_loop_end();
		
		woocommerce_reset_loop();
		wp_reset_postdata();
		
		echo '</div>';
		
		echo $args['after_widget'];

		remove_filter( 'post_class', array( $this, 'add_product_post_class' ) );
	}

	private function get_wc_legacy_visibility_parse_query( $query_args ) {
		$query_args['meta_query'][] = array(
			'key' => '_visibility',
			'value' => array( 'catalog', 'visible' ),
			'compare' => 'IN',
		);

		return $query_args;
	}
}
