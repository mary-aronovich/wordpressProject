<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Builder_Embed_Shortcode {

	public function render( $atts ) {
		if ( empty( $atts['id'] ) )
			return __( 'Setup template ID in the template', 'pojo' );
		
		ob_start();
		Pojo_Core::instance()->builder->pd_print_page_builder_front( $atts['id'] );
		return ob_get_clean();
	}
	
	public function __construct() {
		add_shortcode( 'pojo-builder-embed', array( &$this, 'render' ) );
	}
}