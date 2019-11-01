<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Builder_Template_Type_Pojo extends Pojo_Builder_Template_Type_Local {

	public function register_data() {
		
	}

	public function __construct() {
		$this->type = 'pojo';
		$this->label = __( 'Pojo Templates', 'pojo' );

		Pojo_Builder_Template_Type_Base::__construct();
	}
	
}