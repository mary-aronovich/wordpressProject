<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Radio extends Pojo_MetaBox_Field {

	public function get_value( $post_id = null ) {
		$current_value = parent::get_value( $post_id );
		if ( empty( $current_value ) && ! empty( $this->_field['options'] ) ) {
			$current_value = array_keys( $this->_field['options'] );
			$current_value = $current_value[0];
		}
		return $current_value;
	}

	public function render() {
		if ( empty( $this->_field['options'] ) )
			return __( 'No found any options', 'pojo' );

		$html = '';
		if ( ! empty( $this->_field['title'] ) )
			$html .= sprintf( '<div class="atmb-label"><label>%s</label></div>', $this->_field['title'] );
		
		$html .= '<div class="atmb-input">';

		foreach ( $this->_field['options'] as $option_key => $option_value ) {
			$html .= sprintf(
				'<div class="atmb-radio-item"><input id="atmb-id-%5$d" type="radio" class="atmb-field-radio" value="%2$s" name="%3$s"%4$s /><label for="atmb-id-%5$d"> %1$s</label></div>',
				$option_value,
				$option_key,
				$this->_field['id'],
				checked( $option_key, $this->get_value(), false ),
				self::$_index_id++
			);
		}

		$html .= '</div>';

		return $html;
	}
}