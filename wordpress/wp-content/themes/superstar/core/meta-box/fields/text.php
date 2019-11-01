<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Text extends Pojo_MetaBox_Field {
	
	public $type = 'text';

	public function render() {
		$classes = array( 'atmb-field-text' );
		if ( ! empty( $this->_field['classes_field'] ) )
			$classes = array_merge( $classes, $this->_field['classes_field'] );
		
		$field_id = 'atmb-id-' . self::$_index_id++;
		
		$input_attributes = array(
			'id' => $field_id,
			'name' => $this->_field['id'],
			'type' => $this->type,
			'class' => implode( ' ', $classes ),
			'value' => esc_attr( $this->get_value() ),
		);
		
		if ( ! empty( $this->_field['field_attributes'] ) ) {
			foreach ( $this->_field['field_attributes'] as $attr_key => $attr_value ) {
				$input_attributes[ $attr_key ] = $attr_value;
			}
		}
		
		if ( ! empty( $this->_field['placeholder'] ) )
			$input_attributes['placeholder'] = esc_attr( $this->_field['placeholder'] );
		
		if ( ! empty( $this->_field['readonly'] ) )
			$input_attributes['readonly'] = 'readonly';
		
		return sprintf(
			'<div class="atmb-label"><label for="%2$s">%1$s:</label></div><div class="atmb-input"><input %3$s />%4$s</div>',
			$this->_field['title'],
			$field_id,
			pojo_array_to_attributes( $input_attributes ),
			$this->get_desc_field()
		);
	}

}