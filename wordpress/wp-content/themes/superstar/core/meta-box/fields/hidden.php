<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Hidden extends Pojo_MetaBox_Field {

	public function render() {
		$classes = array( 'atmb-field-hidden' );
		$classes = array_merge( $classes, array_diff( $this->_field['classes'], array( 'atmb-field-row' ) ) );
		
		return sprintf(
			'<input id="atmb-id-%4$d" type="hidden" class="%3$s" name="%1$s" value="%2$s" />',
			$this->_field['id'],
			esc_attr( $this->_field['std'] ),
			implode( ' ', $classes ),
			self::$_index_id++
		);
	}

	public function get_field_html() {
		return $this->render();
	}

}
