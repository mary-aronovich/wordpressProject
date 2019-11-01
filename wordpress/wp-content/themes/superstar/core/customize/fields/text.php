<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Text extends Pojo_Customize_Control_Field_Base {

	public function __construct( $manager, $id, $args = array() ) {
		$args['type'] = 'text';
		parent::__construct( $manager, $id, $args );
	}

}
