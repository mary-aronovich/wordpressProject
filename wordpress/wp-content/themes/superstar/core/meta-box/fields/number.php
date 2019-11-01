<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Number extends Pojo_MetaBox_Field_Text {
	
	public $type = 'number';

	public function __construct( $field, $prefix = '' ) {
		$default = array(
			'min' => 0,
			'max' => 99999,
		);

		$field = wp_parse_args( $field, $default );

		parent::__construct( $field, $prefix );
		
		$this->_field['field_attributes']['min'] = $field['min'];
		$this->_field['field_attributes']['max'] = $field['max'];
	}

}