<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Radio_image extends Pojo_MetaBox_Field {

	public function get_value( $post_id = null ) {
		$current_value = parent::get_value( $post_id );
		if ( empty( $current_value ) && ! empty( $this->_field['options'] ) ) {
			$current_value = $this->_field['options'][0]['id'];
		}
		return $current_value;
	}

	public function render() {
		if ( empty( $this->_field['options'] ) )
			return __( 'No found any options', 'pojo' );

		$html = '';
		if ( ! empty( $this->_field['title'] ) )
			$html .= sprintf( '<div class="atmb-label"><label>%s:</label></div>', $this->_field['title'] );
		
		$html .= '<div class="atmb-input radio-image">';
		
		foreach ( $this->_field['options'] as $option ) {
			$html .= sprintf(
				'<div class="radio-image-item">
					<input id="atmb-id-%5$d" type="radio" class="atmb-field-radio-image" value="%2$s" name="%3$s" data-image="%6$s"%4$s />
					<label for="atmb-id-%5$d">%1$s</label>
				</div>',
				$option['title'],
				$option['id'],
				$this->_field['id'],
				checked( $option['id'], $this->get_value(), false ),
				self::$_index_id++,
				$option['image']
			);
		}

		$html .= '</div>';

		return $html;
	}
}