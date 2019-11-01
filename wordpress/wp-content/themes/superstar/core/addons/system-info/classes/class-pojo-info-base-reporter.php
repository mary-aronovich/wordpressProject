<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Pojo_Info_Base_Reporter {

	private $_properties;

	public abstract function get_title();
	
	public abstract function get_fields();
	
	public function is_enabled() {
		return true;
	}

	public final function get_report() {
		$result = array();

		foreach ( $this->get_fields() as $field_name => $field_label ) {
			$method = 'get_' . $field_name;

			if ( ! method_exists( $this, $method ) ) {
				return new WP_error( "Getter method for the field '{$field_name}' wasn't found in " . get_called_class() );
			}

			$reporter_field = array(
				'name' => $field_name,
				'label' => $field_label,
			);

			$reporter_field = array_merge( $reporter_field, $this->$method() );
			$result[ $field_name ] = $reporter_field;
		}

		return $result;
	}

	public static function get_properties_keys() {
		return array(
			'name',
			'fields',
		);
	}

	public final static function filter_possible_properties( $properties ) {
		return Pojo_Model_Helper::filter_possible_properties( self::get_properties_keys(), $properties );
	}

	public final function set_properties( $key, $value = null ) {
		if ( is_array( $key ) ) {
			$key = self::filter_possible_properties( $key );

			foreach ( $key as $sub_key => $sub_value ) {
				$this->set_properties( $sub_key, $sub_value );
			}

			return;
		}

		if ( ! in_array( $key, self::get_properties_keys() ) ) {
			return;
		}

		$this->_properties[ $key ] = $value;
	}

	public function __construct( $properties = null ) {
		$this->_properties = array_fill_keys( self::get_properties_keys(), null );

		if ( $properties ) {
			$this->set_properties( $properties, null );
		}
	}
}
