<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Animated_Numbers extends Pojo_Widget_Base {
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'number',
			'title' => __( 'Number:', 'pojo' ),
			'desc' => __( 'Add a number here and it will be animated. Examples: 100%, 24/7, 99.9$ etc', 'pojo' ),
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'duration',
			'title' => __( 'Duration (in ms):', 'pojo' ),
			'desc' => __( 'Default: 2000, 1000 ms = 1 second', 'pojo' ),
			'std' => '2000',
			'filter' => 'absint',
		);

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'description',
			'title' => __( 'Description:', 'pojo' ),
			'type' => 'textarea',
			'desc' => __( 'Add some content to be displayed below the title', 'pojo' ),
			'std' => '',
			'filter' => 'normalize_whitespace',
		);

		$this->_form_fields[] = array(
			'id' => 'link_to',
			'title' => __( 'Link to:', 'pojo' ),
			'placeholder' => 'http://',
			'std' => '',
			'filter' => 'esc_url_raw',
		);

		$this->_form_fields[] = array(
			'id' => 'target_link',
			'title' => __( 'Open Link in', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Same Window', 'pojo' ),
				'blank' => __( 'New Tab or Window', 'pojo' ),
			),
			'std' => '',
		);

		// Styles
		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Style', 'pojo' ),
			'type' => 'button_collapse',
			'mode' => 'start',
		);

		$this->_form_fields[] = array(
			'id' => 'heading_number',
			'type' => 'heading',
			'title' => __( 'Number Style', 'pojo' ),
		);

		$this->_form_fields[] = array(
			'id' => 'number_color',
			'title' => __( 'Color:', 'pojo' ),
			'type' => 'color',
			'std' => '',
			'filter' => 'sanitize_text_field',
		);
		
		$this->_form_fields[] = array(
			'id' => 'number_font_size',
			'title' => __( 'Font Size:', 'pojo' ),
			'placeholder' => '20px',
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'number_font_weight',
			'title' => __( 'Font Weight:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => $this->_get_font_weights(),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[ ] = array(
			'id' => 'number_font_transform',
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
			'id' => 'number_font_style',
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
			'id' => 'number_line_height',
			'title' => __( 'Line Height:', 'pojo' ),
			'placeholder' => '30px',
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'number_letter_spacing',
			'title' => __( 'Letter Spacing:', 'pojo' ),
			'placeholder' => '0px',
			'std' => '',
			'filter' => 'sanitize_text_field',
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
			'placeholder' => '0px',
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
				'normal' => __( 'normal', 'pojo' ),
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
		// End Styles

		parent::__construct(
			'pojo_animated_nums',
			__( 'Animated Numbers', 'pojo' ),
			array( 'description' => __( 'Animated Numbers', 'pojo' ) )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );

		echo $args['before_widget'];
		
		ob_start();
		echo '<div class="pojo-numbers-number"';
		$number_inline = $this->_get_inline_styles( 'number', $instance );
		if ( ! empty( $number_inline ) )
			echo ' style="' . $number_inline . '"';
		echo '>';
		echo $this->_parse_numbers_from_string( $instance['number'], $instance );
		echo '</div>';
		
		if ( ! empty( $instance['title'] ) ) {
			echo '<div class="pojo-numbers-title"';
			$title_inline = $this->_get_inline_styles( 'title', $instance );
			if ( ! empty( $title_inline ) )
				echo ' style="' . $title_inline . '"';
			
			echo '>' . $instance['title'] . '</div>';
		}
		
		if ( ! empty( $instance['description'] ) ) {
			echo '<div class="pojo-numbers-desc"';
			$desc_inline = $this->_get_inline_styles( 'desc', $instance );
			if ( ! empty( $desc_inline ) )
				echo ' style="' . $desc_inline . '"';
			
			echo '>' . $instance['description'] . '</div>';
		}
		echo $this->_get_link_wrapper( ob_get_clean(), $instance );
		
		echo $args['after_widget'];
	}
	
	protected function _parse_numbers_from_string( $string, $instance ) {
		$return_html = preg_replace( '#(\d+)#', '<span class="pojo-animated-numbers" data-to_value="$1" data-duration="' . $instance['duration'] . '">0</span>', $string );

		return $return_html;
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
			$content = sprintf( '<a class="pojo-numbers pojo-numbers-link" href="%s"%s>%s</a>', $instance['link_to'], $target_html, $content );
		} else {
			$content = sprintf( '<div class="pojo-numbers">%s</div>', $content );
		}
		return $content;
	}

}