<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Advanced_Widget_Title {

	public function init() {
		if ( ! current_theme_supports( 'pojo-advanced-widget-title' ) )
			return;

		add_filter( 'pojo_parse_widget_args', array( &$this, 'parse_widget_args' ), 50, 3 );
		add_filter( 'pojo_init_widget_fields', array( &$this, 'pojo_init_widget_fields' ), 50, 3 );
	}

	public function parse_widget_args( $args, $instance, $id_base ) {
		// Link in Title
		if ( ! empty( $instance['title_link'] ) ) {
			$args['before_title'] .= '<a href="' . esc_attr( $instance['title_link'] ) . '">';
			$args['after_title'] = '</a>' . $args['after_title'];
		}
		
		// Has sub title
		if ( ! empty( $instance['sub_title'] ) ) {
			$args['after_title'] = '<small class="widget-sub-title">' . $instance['sub_title'] . '</small>' . $args['after_title'];
		}
		
		return $args;
	}

	public function pojo_init_widget_fields( $fields, $id_base, $widget_obj ) {
		$exclude_widgets = array(
			'pojo_menu_anchor',
		);

		$exclude_widgets = apply_filters( 'pojo_init_widget_fields_exclude_widgets', $exclude_widgets );
		
		if ( ! in_array( $id_base, $exclude_widgets ) ) {
			$fields[] = array(
				'id' => 'title_link',
				'title' => __( 'Title Link:', 'pojo' ),
				'type' => 'text',
				'std' => '',
				'placeholder' => 'http://',
				'filter' => 'esc_url_raw',
			);

			$fields[] = array(
				'id' => 'sub_title',
				'title' => __( 'Sub Title:', 'pojo' ),
				'type' => 'text',
				'std' => '',
			);
		}
		
		return $fields;
	}

	public function __construct() {
		add_action( 'after_setup_theme', array( &$this, 'init' ), 30 );
	}
	
}
new Pojo_Advanced_Widget_Title();