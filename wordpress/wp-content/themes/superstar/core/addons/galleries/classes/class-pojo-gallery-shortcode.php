<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Gallery_Shortcode {
	
	public function render( $atts = array(), $content = null ) {
		if ( empty( $atts['id'] ) )
			return '';
		
		ob_start();
		do_action( 'pojo_gallery_print_front', $atts['id'] );
		return ob_get_clean();
	}

	public function __construct() {
		add_shortcode( 'pojo-gallery', array( &$this, 'render' ) );
	}

}