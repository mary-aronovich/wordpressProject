<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Validations {
	
	protected static function _get_field_id() {
		$field_id = str_replace( 'sanitize_option_', '', current_filter() );
		if ( empty( $field_id ) )
			return false;
		
		return $field_id;
	}
	
	public static function field_email( $input ) {
		$field_id = str_replace( 'sanitize_option_', '', current_filter() );
		if ( empty( $field_id ) )
			return $input;

		if ( ! is_email( $input ) ) {
			$input = get_option( $field_id );
			add_settings_error( $field_id, 'invalid-email', __( 'You have entered an invalid e-mail address.', 'pojo' ) );
		}
		return $input;
	}
	
	public static function field_languages( $input ) {
		$field_id = self::_get_field_id();
		if ( ! $field_id )
			return $input;

		$allowed = get_available_languages();
		//if ( ! in_array( $input, $allowed ) && ! empty( $input ) )
		//	$input = get_option( $field_id );
		
		return $input;
	}
	
	public static function field_number( $input ) {
		$value = (int) $input;
		if ( empty( $value ) )
			$value = 1;
		if ( $value < -1 )
			$value = abs( $value );
		
		return $value;
	}
	
	public static function field_html( $input ) {
		return stripslashes( wp_filter_post_kses( addslashes( $input ) ) );
	}
	
	public static function field_checkbox_list( $input ) {
		if ( empty( $input ) )
			$input = array();
		
		return $input;
	}
	
	public static function field_analytics( $input ) {
		if ( preg_match( '/^ua-\d{4,9}-\d{1,4}$/i', strval( $input ) ) )
			return $input;
		return '';
	}
	
}
