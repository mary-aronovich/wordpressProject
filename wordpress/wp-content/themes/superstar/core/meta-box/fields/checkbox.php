<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Checkbox extends Pojo_MetaBox_Field {
	
	public function get_value( $post_id = null ) {
		$value = parent::get_value( $post_id );
		return ! empty( $value );
	}

	public function render() {
		return sprintf(
			'<div class="atmb-label"><label for="atmb-id-%4$d">%1$s</label></div><div class="atmb-input"><input id="atmb-id-%4$d" type="checkbox" class="atmb-field-checkbox" value="1" name="%2$s"%3$s />%5$s</div>',
			$this->_field['title'],
			$this->_field['id'],
			checked( true, $this->get_value(), false ),
			self::$_index_id++,
			$this->get_desc_field()
		);
	}
}