<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Base extends WP_Customize_Control {
	
	protected $selector = '';
	protected $change_type = '';
	
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}
	
	public function to_json() {
		$this->json['selector'] = $this->selector;
		$this->json['change_type'] = $this->change_type;
		parent::to_json();
	}
}
