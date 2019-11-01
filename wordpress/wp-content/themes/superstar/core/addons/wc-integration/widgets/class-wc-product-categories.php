<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_WC_Product_Categories extends Pojo_Widget_Base {
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'number',
			'title' => __( 'Number:', 'pojo' ),
			'std' => '',
			'desc' => __( 'The `number` field is used to display the number of products.', 'pojo' ),
		);

		$this->_form_fields[] = array(
			'id' => 'ids',
			'title' => __( 'IDs:', 'pojo' ),
			'std' => '',
			'desc' => __( 'The `ids` field is to tell the widget which categories to display.', 'pojo' ),
		);

		$this->_form_fields[] = array(
			'id' => 'orderby',
			'title' => __( 'Order by:', 'pojo' ),
			'type' => 'select',
			'std' => 'date',
			'options' => array(
				'name' => __( 'Name', 'pojo' ),
				'slug' => __( 'Slug', 'pojo' ),
				'count' => _x( 'Count', 'count of WC products', 'pojo' ),
				'none' => __( 'None', 'pojo' ),
				'id' => __( 'ID', 'pojo' ),
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
			'id' => 'hide_empty',
			'title' => __( 'Hide Empty:', 'pojo' ),
			'type' => 'select',
			'std' => 'date',
			'options' => array(
				'1' => __( 'Hide', 'pojo' ),
				'0' => __( 'Show', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'columns',
			'title' => __( 'Columns:', 'pojo' ),
			'std' => 4,
			'filter' => array( &$this, '_valid_number' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'child_of',
			'title' => __( 'Child of:', 'pojo' ),
			'std' => '',
			'desc' => __( 'Set the parent paramater to 0 to only display top level categories. Set ids to a comma separated list of category ids to only show those.', 'pojo' ),
		);
		
		parent::__construct(
			'pojo_wc_product_categories',
			__( 'Product Categories', 'pojo' ),
			array( 'description' => __( 'Product Categories', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		$attributes = array();
		foreach ( array( 'number', 'ids', 'orderby', 'order', 'hide_empty', 'columns', 'child_of' ) as $instance_attr_key ) {
			$attr_key = $instance_attr_key;
			if ( 'child_of' === $instance_attr_key )
				$attr_key = 'parent';

			if ( isset( $instance[ $instance_attr_key ] ) )
				$attributes[] = "{$attr_key}=\"{$instance[ $instance_attr_key ]}\"";
		}
		$shortcode = 'product_categories';
		echo do_shortcode( sprintf( '[%s %s]', $shortcode, implode( ' ', $attributes ) ) );
		
		echo $args['after_widget'];
	}


}