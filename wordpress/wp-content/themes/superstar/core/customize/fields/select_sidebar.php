<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Select_sidebar extends Pojo_Customize_Control_Field_Select {

	public function __construct( $manager, $id, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'choices' => array(),
		) );

		if ( ! empty( $GLOBALS['wp_registered_sidebars'] ) ) {
			foreach ( $GLOBALS['wp_registered_sidebars'] as $key => $sidebar ) {
				$args['choices'][ $key ] = $sidebar['name'];
			}
		}
		
		parent::__construct( $manager, $id, $args );
	}

}
