<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Taxonomy_term_checkbox extends Pojo_MetaBox_Field_Checkbox_list {

	public function __construct( $field, $prefix = '' ) {
		$field = wp_parse_args(
			$field,
			array(
				'taxonomy' => '',
				'args'     => array( 'hide_empty' => false ),
				'options'  => array(),
			)
		);

		if ( ! empty( $field['taxonomy'] ) ) {
			$terms = get_terms( $field['taxonomy'], $field['args'] );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$field['options'][ $term->term_id ] = $term->name;
				}
			}
		}

		parent::__construct( $field, $prefix );
	}
}