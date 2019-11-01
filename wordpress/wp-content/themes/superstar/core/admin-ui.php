<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Admin_UI {

	/**
	 * @var Pojo_WP_Pointers
	 */
	public $pointers;

	/**
	 * @var Pojo_Feedback
	 */
	public $feedback;

	public function admin_menu() {
		global $submenu;
		if ( isset( $submenu['pojo-home'] ) )
			$submenu['pojo-home'][0][0] = __( 'Home', 'pojo' );
	}
	
	public function __construct() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 200 );
		
		if ( is_admin() ) {
			include( 'helpers/pointers/class-pojo-wp-pointers.php' );
			include( 'helpers/feedback/class-pojo-feedback.php' );
			
			$this->pointers = new Pojo_WP_Pointers();
			$this->feedback = new Pojo_Feedback();
		}
	}
	
}