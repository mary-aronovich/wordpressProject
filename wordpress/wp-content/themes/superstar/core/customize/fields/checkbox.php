<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Checkbox extends Pojo_Customize_Control_Field_Base {

	public function __construct( $manager, $id, $args = array() ) {
		$args['type'] = 'checkbox';
		parent::__construct( $manager, $id, $args );
	}

}
