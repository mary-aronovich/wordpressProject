<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Raw_html extends Pojo_MetaBox_Field {

	public function render() {
		return sprintf(
			'<div class="atmb-label"><label>%1$s:</label></div><div class="atmb-input">%2$s</div>',
			$this->_field['title'],
			apply_filters( 'atmb_field_raw_html', $this->_field['raw'], $this )
		);
	}

	public function __construct( $field, $prefix = '' ) {
		parent::__construct( wp_parse_args( $field, array( 'raw' => '' ) ), $prefix );
	}
}
