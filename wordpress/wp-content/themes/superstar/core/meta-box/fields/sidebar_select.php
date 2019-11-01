<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Sidebar_select extends Pojo_MetaBox_Field_Select {

	public function __construct( $field, $prefix = '' ) {
		$field = wp_parse_args( $field, array(
			'options' => array(),
		) );

		if ( ! empty( $GLOBALS['wp_registered_sidebars'] ) ) {	
			foreach ( $GLOBALS['wp_registered_sidebars'] as $key => $sidebar ) {
				$field['options'][ $key ] = $sidebar['name'];
			}
		}
		parent::__construct( $field, $prefix );
	}
}