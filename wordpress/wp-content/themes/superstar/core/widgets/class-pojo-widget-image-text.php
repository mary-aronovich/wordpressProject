<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Image_Text extends Pojo_Widget_Base {
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
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
			'id' => 'text_align',
			'title' => __( 'Align:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'none' => __( 'None', 'pojo' ),
				'right' => __( 'Right', 'pojo' ),
				'center' => __( 'Center', 'pojo' ),
				'left' => __( 'Left', 'pojo' ),
			),
			'std' => 'none',
			'filter' => array( &$this, '_valid_align' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'description',
			'title' => __( 'Description:', 'pojo' ),
			'type' => 'textarea',
			'std' => '',
			'filter' => 'normalize_whitespace',
		);

		$this->_form_fields[] = array(
			'id' => 'image',
			'title' => __( 'Choose Image:', 'pojo' ),
			'type' => 'image',
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'alt_text',
			'title' => __( 'Alt Text:', 'pojo' ),
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'image_position',
			'title' => __( 'Image Position:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'center' => __( 'Top', 'pojo' ),
				'right' => __( 'Right', 'pojo' ),
				'left' => __( 'Left', 'pojo' ),
			),
			'std' => 'top',
			'filter' => array( &$this, '_valid_align' ),
		);

		$this->_form_fields[] = array(
			'id' => 'link_to',
			'title' => __( 'Link to:', 'pojo' ),
			'std' => '',
			'filter' => 'esc_url_raw',
		);

		$this->_form_fields[] = array(
			'id'      => 'target_link',
			'title'   => __( 'Open Link in', 'pojo' ),
			'type'    => 'select',
			'options' => array(
				'' => __( 'Same Window', 'pojo' ),
				'blank' => __( 'New Tab or Window', 'pojo' ),
			),
			'std'     => '',
		);

		$this->_form_fields[] = array(
			'id' => 'hover_animation',
			'title' => __( 'Image Hover Animation:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $this->_get_hover_animation_options(),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		// Styles
		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Style', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'start',
		);

		$this->_form_fields[] = array(
			'id' => 'heading_title',
			'type' => 'heading',
			'title' => __( 'Title Style', 'pojo' ),
		);

		$this->_form_fields[] = array(
			'id' => 'title_color',
			'title' => __( 'Color:', 'pojo' ),
			'type' => 'color',
			'std' => '',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'title_font_size',
			'title' => __( 'Font Size:', 'pojo' ),
			'placeholder' => '20px',
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'title_font_weight',
			'title' => __( 'Font Weight:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $this->_get_font_weights(),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[ ] = array(
			'id' => 'title_font_transform',
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
			'id' => 'title_font_style',
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
			'id' => 'title_line_height',
			'title' => __( 'Line Height:', 'pojo' ),
			'placeholder' => '30px',
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'title_letter_spacing',
			'title' => __( 'Letter Spacing:', 'pojo' ),
			'placeholder' => '',
			'std' => '',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'heading_description',
			'type' => 'heading',
			'title' => __( 'Style Description', 'pojo' ),
		);

		$this->_form_fields[] = array(
			'id' => 'desc_color',
			'title' => __( 'Color:', 'pojo' ),
			'type' => 'color',
			'std' => '',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'desc_font_size',
			'title' => __( 'Font Size:', 'pojo' ),
			'placeholder' => '20px',
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'desc_font_weight',
			'title' => __( 'Font Weight:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $this->_get_font_weights(),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[ ] = array(
			'id' => 'desc_font_transform',
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
			'id' => 'desc_font_style',
			'title' => __( 'Font Style:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'normal' => __( 'Normal', 'pojo' ),
				'italic' => __( 'Italic', 'pojo' ),
				'oblique' => __( 'Oblique', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'desc_line_height',
			'title' => __( 'Line Height:', 'pojo' ),
			'placeholder' => '30px',
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'desc_letter_spacing',
			'title' => __( 'Letter Spacing:', 'pojo' ),
			'placeholder' => '',
			'std' => '',
			'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Custom', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'end',
		);
		// End Styles

		parent::__construct(
			'pojo_image_text',
			__( 'Image & Text', 'pojo' ),
			array( 'description' => __( 'Display a combination of image and text', 'pojo' ) )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		// Maybe we do not have anything to show?
		if ( empty( $instance['title'] ) && empty( $instance['description'] ) && empty( $instance['image'] ) )
			return;

		echo $args['before_widget'];
		
		echo '<div class="pojo-image-text">';
		
		if ( ! empty( $instance['image'] ) ) {
			$image_html = sprintf(
				'<img src="%s" alt="%s" class="image-text-thumbnail%s%s" />',
				esc_attr( $instance['image'] ),
				esc_attr( $instance['alt_text'] ),
				( ! empty( $instance['image_position'] ) && in_array( $instance['image_position'], array( 'right', 'left', 'center' ) ) ) ? ' align' . $instance['image_position'] : '',
				( ! empty( $instance['hover_animation'] ) ) ? ' hover-' . $instance['hover_animation'] : ''
			);
			
			echo $this->_get_link_wrapper( $image_html, $instance );
		}
		
		if ( ! empty( $instance['title'] ) || ! empty( $instance['description'] ) ) {
			printf(
				'<div class="image-text-body%s">',
				( ! empty( $instance['text_align'] ) && in_array( $instance['text_align'], array( 'right', 'left', 'center' ) ) ) ? ' text-align-' . $instance['text_align'] : ''
			);
			
			if ( ! empty( $instance['title'] ) ) {
				if ( empty( $instance['size'] ) )
					$instance['size'] = 'h2';

				$title_inline = $this->_get_inline_styles( 'title', $instance );
				if ( ! empty( $title_inline ) )
					$title_inline = ' style="' . $title_inline . '"';

				printf( '<%1$s class="image-text-title"%3$s>%2$s</%1$s>', $instance['size'], $this->_get_link_wrapper( $instance['title'], $instance ), $title_inline );
			}

			if ( ! empty( $instance['description'] ) ) {
				$desc_inline = $this->_get_inline_styles( 'desc', $instance );
				if ( ! empty( $desc_inline ) )
					$desc_inline = ' style="' . $desc_inline . '"';
				
				printf( '<p class="image-text-description"%s>%s</p>', $desc_inline, nl2br( $instance['description'] ) );
			}
			
			echo '</div>';
		}
		
		echo '</div>';
		
		echo $args['after_widget'];
	}

	protected function _get_inline_styles( $prefix, $instance ) {
		$properties = array(
			// Option => CSS Property
			'color' => 'color',
			'font_size' => 'font-size',
			'font_weight' => 'font-weight',
			'line_height' => 'line-height',
			'font_style' => 'font-style',
			'font_transform' => 'text-transform',
			'letter_spacing' => 'letter-spacing',
		);

		$inline_style = array();
		foreach ( $properties as $property => $css_property ) {
			if ( ! empty( $instance[ $prefix . '_' . $property ] ) ) {
				$inline_style[] = $css_property . ': ' . $instance[ $prefix . '_' . $property ];
			}
		}

		return implode( '; ', $inline_style );
	}

	protected function _get_link_wrapper( $content, $instance ) {
		if ( ! empty( $instance['link_to'] ) ) {
			$target_html = '';
			if ( ! empty( $instance['target_link'] ) && 'blank' === $instance['target_link'] ) {
				$target_html = ' target="_blank"';
			}
			$content = sprintf( '<a href="%s"%s>%s</a>', $instance['link_to'], $target_html, $content );
		}
		return $content;
	}

}