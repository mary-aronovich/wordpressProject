<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBoxHelpers {

	protected static $_mb_locations = array();

	public static function get_field_class( $type ) {
		$classField = 'Pojo_MetaBox_Field_' . ucwords( $type );
		if ( ! class_exists( $classField ) )
			return false;

		return $classField;
	}

	public static function add_new_location( $mb_id, $location ) {
		if ( empty( $location ) )
			return;

		self::$_mb_locations[] = array( $mb_id => $location );
	}

	public static function get_json_all_location() {
		return json_encode( self::$_mb_locations );
	}
}