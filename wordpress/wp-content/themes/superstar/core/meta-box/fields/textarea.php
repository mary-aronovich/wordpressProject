<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Textarea extends Pojo_MetaBox_Field {

	public function render() {
		$placeholder = ! empty( $this->_field['placeholder'] ) ? ' placeholder="' . esc_attr( $this->_field['placeholder'] ) . '"' : '';
		$classes = array( 'atmb-field-textarea' );
		if ( ! empty( $this->_field['classes_field'] ) )
			$classes = array_merge( $classes, $this->_field['classes_field'] );
		
		return sprintf(
			'<div class="atmb-label"><label for="atmb-id-%4$d">%1$s:</label></div><div class="atmb-input"><textarea id="atmb-id-%4$d" class="%6$s" name="%2$s"%7$s>%3$s</textarea>%5$s</div>',
			$this->_field['title'],
			$this->_field['id'],
			esc_attr( $this->get_value() ),
			self::$_index_id++,
			$this->get_desc_field(),
			implode( ' ', $classes ),
			$placeholder
		);
	}
}