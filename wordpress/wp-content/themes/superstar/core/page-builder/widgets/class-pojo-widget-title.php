<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Title extends Pojo_Widget_Base {
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$font_sizes = $font_weights = array( '' => __( 'Default', 'pojo' ) );
		foreach ( array_merge( range( 8, 48 ), range( 60, 100, 10 ) ) as $pixel ) {
			$font_sizes[ $pixel . 'px' ] = $pixel . 'px';
		}
		
		foreach ( array_merge( array( 'normal', 'bold' ), range( 100, 900, 100 ) ) as $weight ) {
			$font_weights[ $weight ] = ucfirst( $weight );
		}
		
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'link',
			'title' => __( 'Link:', 'pojo' ),
			'placeholder' => __( 'http://pojo.me/', 'pojo' ),
			'std' => '',
			'filter' => 'esc_url_raw',
		);
		
		$this->_form_fields[] = array(
			'id' => 'size',
			'title' => __( 'Heading Size:', 'pojo' ),
			'type' => 'select',
			'std' => 'h2',
			'options' => array(
				'h1' => __( 'H1', 'pojo' ),
				'h2' => __( 'H2', 'pojo' ),
				'h3' => __( 'H3', 'pojo' ),
				'h4' => __( 'H4', 'pojo' ),
				'h5' => __( 'H5', 'pojo' ),
				'h6' => __( 'H6', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'align',
			'title' => __( 'Align:', 'pojo' ),
			'type' => 'select',
			'std' => 'none',
			'options' => array(
				'none' => __( 'None', 'pojo' ),
				'right' => __( 'Right', 'pojo' ),
				'left' => __( 'Left', 'pojo' ),
				'center' => __( 'Center', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Style', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'start',
		);

		$this->_form_fields[] = array(
			'id' => 'color',
			'title' => __( 'Color:', 'pojo' ),
			'type' => 'color',
			'std' => '',
			'filter' => 'sanitize_text_field',
		);
		
		$this->_form_fields[] = array(
			'id' => 'font_size',
			'title' => __( 'Font Size:', 'pojo' ),
			'placeholder' => '20px',
			'std' => '',
		);
		
		$this->_form_fields[ ] = array(
			'id' => 'font_weight',
			'title' => __( 'Font Weight:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $font_weights,
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[ ] = array(
			'id' => 'font_transform',
			'title' => __( 'Text Transform:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'none' => __( 'None', 'pojo' ),
				'uppercase' => __( 'Uppercase', 'pojo' ),
				'lowercase' => __( 'Lowercase', 'pojo' ),
				'capitalize' => __( 'Capitalize', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[ ] = array(
			'id' => 'font_style',
			'title' => __( 'Font Style:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'normal' => __( 'normal', 'pojo' ),
				'italic' => __( 'Italic', 'pojo' ),
				'oblique' => __( 'Oblique', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'line_height',
			'title' => __( 'Line Height:', 'pojo' ),
			'placeholder' => '30px',
			'std' => '',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'letter_spacing',
			'title' => __( 'Letter Spacing:', 'pojo' ),
			'placeholder' => '0px',
			'std' => '',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Custom', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'end',
		);
		
		parent::__construct(
			'pojo_title',
			__( 'Title', 'pojo' ),
			array( 'description' => __( 'Title Widget', 'pojo' ), )
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
		$args = $this->_parse_widget_args( $args, $instance );
		
		if ( empty( $instance['title'] ) )
			return;

		if ( empty( $instance['size'] ) )
			$instance['size'] = 'h2';
		
		$style_inline = array();
		
		if ( ! empty( $instance['align'] ) && 'none' !== $instance['align'] )
			$style_inline[] = 'text-align:' . $instance['align'];
		
		if ( ! empty( $instance['color'] ) )
			$style_inline[] = 'color:' . $instance['color'];
		
		if ( ! empty( $instance['font_size'] ) )
			$style_inline[] = 'font-size:' . $instance['font_size'];
		
		if ( ! empty( $instance['font_weight'] ) )
			$style_inline[] = 'font-weight:' . $instance['font_weight'];
		
		if ( ! empty( $instance['line_height'] ) )
			$style_inline[] = 'line-height:' . $instance['line_height'];
		
		if ( ! empty( $instance['font_transform'] ) )
			$style_inline[] = 'text-transform:' . $instance['font_transform'];
		
		if ( ! empty( $instance['letter_spacing'] ) )
			$style_inline[] = 'letter-spacing:' . $instance['letter_spacing'];
		
		if ( ! empty( $instance['font_style'] ) )
			$style_inline[] = 'font-style:' . $instance['font_style'];

		echo $args['before_widget'];
		
		$title_html = sprintf( '<%1$s%3$s>%2$s</%1$s>', $instance['size'], $instance['title'], ! empty( $style_inline ) ? ' style="' . implode( ';', $style_inline ) . '"' : '' );
		
		if ( ! empty( $instance['link'] ) )
			$title_html = sprintf( '<a href="%s">%s</a>', $instance['link'], $title_html );
		
		echo $title_html;
		
		echo $args['after_widget'];
	}
	
}