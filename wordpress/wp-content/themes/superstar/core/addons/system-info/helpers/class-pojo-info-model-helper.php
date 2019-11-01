<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Model_Helper {

	private function __construct( ) {}

	public static function filter_possible_properties( $possibleProperties, $properties ) {
		$propertiesKeys = array_flip( $possibleProperties );

		return array_intersect_key( $properties, $propertiesKeys );
	}

	public static function prepare_properties( $possibleProperties, $userProperties ) {
		$properties = array_fill_keys($possibleProperties, null);

		$properties = array_merge($properties, $userProperties);

		return self::filter_possible_properties($possibleProperties, $properties);
	}
}