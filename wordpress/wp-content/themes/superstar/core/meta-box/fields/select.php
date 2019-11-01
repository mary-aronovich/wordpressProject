<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Select extends Pojo_MetaBox_Field {

	public function render() {
		if ( empty( $this->_field['options'] ) )
			return 'No build select option';

		$options = array();
		foreach ( $this->_field['options'] as $option_key => $option_value ) {
			$options[] = sprintf(
				'<option value="%1$s"%2$s>%3$s</option>',
				$option_key,
				selected( $this->get_value(), $option_key, false ),
				$option_value
			);
		}
		
		return sprintf(
			'<div class="atmb-label"><label for="atmb-id-%4$d">%1$s:</label></div><div class="atmb-input"><select id="atmb-id-%4$d" class="atmb-field-select" name="%2$s">%3$s</select>%5$s</div>',
			$this->_field['title'],
			$this->_field['id'],
			implode( '', $options ),
			self::$_index_id++,
			$this->get_desc_field()
		);
	}
}
