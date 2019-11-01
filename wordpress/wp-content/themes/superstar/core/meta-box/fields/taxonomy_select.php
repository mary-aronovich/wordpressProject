<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Taxonomy_select extends Pojo_MetaBox_Field_Select {

	public function __construct( $field, $prefix = '' ) {
		$field = wp_parse_args( $field, array(
			'options'           => array(),
			'object_type'       => 'post',
			'hierarchical_only' => false,
			'exclude'           => array(),
			'show_option_all'   => '',
			'last_empty_option' => '',
		) );

		if ( ! empty( $field['show_option_all'] ) )
			$field['options'][''] = $field['show_option_all'];

		$taxonomies = get_object_taxonomies( $field['object_type'], 'objects' );

		foreach ( $taxonomies as $key => $taxonomy ) {
			if ( $field['hierarchical_only'] && ! $taxonomy->hierarchical )
				continue;
			
			if ( in_array( $key, $field['exclude'] ) )
				continue;
				
			$field['options'][ $key ] = $taxonomy->labels->name;
		}

		if ( ! empty( $field['last_empty_option'] ) && empty( $field['show_option_all'] ) )
			$field['options'][''] = $field['last_empty_option'];

		parent::__construct( $field, $prefix );
	}
}