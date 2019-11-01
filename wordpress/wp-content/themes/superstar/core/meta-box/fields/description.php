<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Description extends Pojo_MetaBox_Field {

	public function render() {
		return sprintf(
			'<p class="atmb-description">%s</p>',
			$this->_field['desc']
		);
	}
}