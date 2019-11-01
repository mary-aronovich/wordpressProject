<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Heading extends Pojo_MetaBox_Field {

	public function render() {
		return sprintf(
			'<h4 class="atmb-heading">%s</h4>',
			$this->_field['title']
		);
	}
}