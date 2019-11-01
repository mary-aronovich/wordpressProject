<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Button extends Pojo_Widget_Base {

	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'text',
			'title' => __( 'Button Text:', 'pojo' ),
			'std' => __( 'Click me', 'pojo' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'link',
			'title' => __( 'Link:', 'pojo' ),
			'placeholder' => __( 'http://pojo.me/', 'pojo' ),
			'std' => '',
			//'filter' => 'esc_url_raw',
		);

		$this->_form_fields[] = array(
			'id'      => 'target_link',
			'title'   => __( 'Open Link in', 'pojo' ),
			'type'    => 'select',
			'options' => array(
				'same' => __( 'Same Window', 'pojo' ),
				'blank' => __( 'New Tab or Window', 'pojo' ),
			),
			'std'     => 'same',
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'bg_color',
			'title' => __( 'Background Color:', 'pojo' ),
			'type' => 'color',
			'std' => '#ffffff',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'bg_opacity',
			'title' => __( 'Background Opacity:', 'pojo' ),
			'type' => 'number',
			'std' => '100',
			'filter' => array( &$this, '_valid_bg_opacity' ),
		);

		$this->_form_fields[] = array(
			'id' => 'border_color',
			'title' => __( 'Border Color:', 'pojo' ),
			'type' => 'color',
			'std' => '#cccccc',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'text_color',
			'title' => __( 'Text Color:', 'pojo' ),
			'type' => 'color',
			'std' => '#333333',
			'filter' => 'sanitize_text_field',
		);
		
		$this->_form_fields[] = array(
			'id' => 'size',
			'title' => __( 'Size:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'small' => __( 'Small', 'pojo' ),
				'medium' => __( 'Medium', 'pojo' ),
				'large' => __( 'Large', 'pojo' ),
				'xl' => __( 'XL', 'pojo' ),
				'xxl' => __( 'XXL', 'pojo' ),
			),
			'std' => 'medium',
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'icon',
			'title' => __( 'Icon:', 'pojo' ),
			'std' => '',
			'placeholder' => '<i class="fa fa-check-square"></i>',
			'desc' => sprintf( __( 'Check in <a href="%s" target="_blank">Font-Awesome</a>.', 'pojo' ), 'http://fontawesome.io/icons/' ),
		);

		$this->_form_fields[] = array(
			'id' => 'align',
			'title' => __( 'Align:', 'pojo' ),
			'type' => 'select',
			'std' => 'none',
			'options' => array(
				'none' => _x( 'None', 'button-align', 'pojo' ),
				'center' => _x( 'Center', 'button-align', 'pojo' ),
				'right' => _x( 'Right', 'button-align', 'pojo' ),
				'left' => _x( 'Left', 'button-align', 'pojo' ),
				'block' => _x( 'Block', 'button-align', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		parent::__construct(
			'pojo_button',
			__( 'Button', 'pojo' ),
			array( 'description' => __( 'Button', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		if ( empty( $instance['text'] ) )
			return;
		
		if ( empty( $instance['link'] ) )
			$instance['link'] = 'javascript:void(0);';
		
		$target = '';
		if ( ! empty( $instance['target_link'] ) && 'blank' === $instance['target_link'] )
			$target = ' target="_blank"';
		
		$link_inline_style = array();
		if ( ! empty( $instance['bg_color'] ) ) {
			$rgb_color = pojo_hex2rgb( $instance['bg_color'] );
			$color_value = sprintf( 'rgba(%d,%d,%d,%s)', $rgb_color[0], $rgb_color[1], $rgb_color[2], ( $instance['bg_opacity'] / 100 ) );
			
			$link_inline_style[] = 'background-color:' . $color_value;
		}

		if ( ! empty( $instance['border_color'] ) ) {
			$link_inline_style[] = 'border-color:' . $instance['border_color'];
		}

		if ( ! empty( $instance['text_color'] ) ) {
			$link_inline_style[] = 'color:' . $instance['text_color'];
		}
		
		echo $args['before_widget'];
		
		printf(
			'<div class="pojo-button-wrap pojo-button-%s">
				<a class="button size-%s"%s href="%s"%s>%s
					<span class="pojo-button-text">%s</span>
				</a>
			</div>',
			esc_attr( $instance['align'] ),
			esc_attr( $instance['size'] ),
			! empty( $link_inline_style ) ? ' style="' . esc_attr( implode( ';', $link_inline_style ) ) . '"' : '',
			$instance['link'],
			$target,
			! empty( $instance['icon'] ) ? '<span class="pojo-button-icon">' . $instance['icon'] . '</span> ' : '',
			$instance['text']
		);
		
		echo $args['after_widget'];
	}

}