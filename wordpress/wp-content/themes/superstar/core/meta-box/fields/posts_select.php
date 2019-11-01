<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Posts_select extends Pojo_MetaBox_Field_Select {
	
	public function __construct( $field, $prefix = '' ) {
		/**
		 * @var $posts WP_Post[]
		 */
		$field = wp_parse_args( $field, array(
			'options'   => array(),
			'exclude'   => array(),
			'args'      => array( 'posts_per_page ' => -1 ),
		) );

		$posts_query = new WP_Query( $field['args'] );
		$posts = $posts_query->get_posts();
		
		if ( $posts ) {
			foreach ( $posts as $post ) {
				if ( in_array( $post->ID, $field['exclude'] ) )
					continue;
	
				$field['options'][ $post->ID ] = $post->post_title;
			}
		}

		parent::__construct( $field, $prefix );
	}
}