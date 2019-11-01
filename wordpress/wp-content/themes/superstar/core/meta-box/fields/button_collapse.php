<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Button_collapse extends Pojo_MetaBox_Field {

	public function get_field_id() {
		$id = str_replace( array( '[', ']' ), array( '-', '' ), $this->_field['id'] );
		return $id;
	}

	public function render() {
		return sprintf(
			'<a href="javascript:void(0);" class="atmb-button-collapse button close" data-toggle_class="atmb-collapse-%2$s">%1$s</a>',
			$this->_field['title'],
			$this->get_field_id()
		);
	}

	public function get_field_html() {
		$parent_return = parent::get_field_html();
		
		if ( 'start' === $this->_field['mode'] ) {
			$parent_return .= sprintf( '<div class="atmb-button-collapse hidden" id="atmb-collapse-%s">', $this->get_field_id() );
		} else {
			$parent_return = '</div>';
		}
		
		return $parent_return;
	}

	public function __construct( $field, $prefix = '' ) {
		$field = wp_parse_args( $field, array(
			'mode' => 'start',
		) );
		parent::__construct( $field, $prefix );
	}

}