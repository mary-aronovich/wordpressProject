<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Slideshow_Ajax {

	public function preview_shortcode() {
		global $pojo_slideshow;

		if ( empty( $_POST['id'] ) ) {
			echo 'No found.';
			die();
		}

		$embed = new Pojo_Embed_Template();
		echo $embed->get_header();
		echo do_shortcode( $pojo_slideshow->helpers->get_shortcode_text( $_POST['id'] ) );
		echo $embed->get_footer();

		die();
	}
	
	public function __construct() {
		add_action( 'wp_ajax_slideshow_preview_shortcode', array( &$this, 'preview_shortcode' ) );
	}
		
}