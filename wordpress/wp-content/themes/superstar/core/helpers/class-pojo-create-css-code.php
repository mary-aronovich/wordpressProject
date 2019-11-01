<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Create_CSS_Code {
	
	protected $_elements = array();
	protected $_raw_data = '';
	
	public function __construct() {
		// empty for now.
	}
	
	public function add_selector( $selector, $content ) {
		if ( ! isset( $this->_elements[ $selector ] ) )
			$this->_elements[ $selector ] = '';

		$this->_elements[ $selector ] .= $content;
	}
	
	public function remove_selector( $selector ) {
		unset( $this->_elements[ $selector ] );
	}
	
	public function add_value( $selector, $key, $value ) {
		if ( empty( $key ) || empty( $value ) )
			return;
		
		$this->add_selector( $selector, sprintf( '%s: %s;', $key, $value ) );
	}

	public function add_data( $string ) {
		$this->_raw_data .= $string;
	}
	
	public function get_css_code() {
		$output = '';
		if ( ! empty( $this->_elements ) ) {
			foreach ( $this->_elements as $key => $value ) {
				$output .= sprintf( '%s{%s}', $key, $value );
			}
		}
		
		if ( ! empty( $this->_raw_data ) ) {
			$output .= $this->_raw_data;
		}
		
		return $output;
	}

}