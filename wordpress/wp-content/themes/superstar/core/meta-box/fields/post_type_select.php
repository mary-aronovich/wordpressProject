<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Post_type_select extends Pojo_MetaBox_Field_Select {
	
	public function __construct( $field, $prefix = '' ) {
		$field = wp_parse_args( $field, array(
			'options'   => array(),
			'exclude'   => array(),
			'args'      => array( 'public' => true ),
		) );

		$post_types = get_post_types( $field['args'], 'objects' );
		foreach ( $post_types as $key => $post_type ) {
			if ( in_array( $key, $field['exclude'] ) )
				continue;

			$field['options'][ $key ] = $post_type->labels->name;
		}

		parent::__construct( $field, $prefix );
	}
}