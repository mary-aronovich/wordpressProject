<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Wrapper extends Pojo_MetaBox_Field {

	public function get_field_html() {
		if ( 'start' === $this->_field['mode'] ) {
			$parent_return = sprintf( '<div class="%s">', $this->_field['wrap_class'] );
		} else {
			$parent_return = '</div>';
		}
		
		return $parent_return;
	}

	public function __construct( $field, $prefix = '' ) {
		$field = wp_parse_args( $field, array(
			'mode' => 'start',
			'wrap_class' => 'metabox-wrap',
		) );
		parent::__construct( $field, $prefix );
	}

}