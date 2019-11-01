<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Slideshow_Helpers {
	
	public function get_all_sliders() {
		$sliders = new WP_Query( array(
			'post_type' => 'pojo_slideshow',
			'posts_per_page' => -1,
		) );
		
		$return = array();
		if ( $sliders->have_posts() ) {
			$sliders = $sliders->get_posts();
			
			foreach ( $sliders as $slide ) {
				$return[ $slide->ID ] = $slide->post_title;
			}
		}
		
		return $return;
	}
	
	public function get_shortcode_text( $id ) {
		return '[pojo-slideshow id="' . $id . '"]';
	}
	
}