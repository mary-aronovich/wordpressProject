<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Thumbnails {
	
	protected static function _init() {
		if ( ! function_exists( 'bfi_thumb' ) )
			include( 'bfi_thumb/BFI_Thumb.php' );
	}

	/** Uses WP's Image Editor Class to resize and filter images
	 *
	 * @param $url string the local image URL to manipulate
	 * @param $params array the options to perform on the image. Keys and values supported:
	 *          'width' int pixels
	 *          'height' int pixels
	 *          'opacity' int 0-100
	 *          'color' string hex-color #000000-#ffffff
	 *          'grayscale' bool
	 *          'negate' bool
	 *          'crop' bool
	 * @param $single boolean, if false then an array of data will be returned
	 * @return string|array containing the url of the resized modofied image
	 */
	public static function get_thumb( $url, $params = array(), $single = true ) {
		self::_init();
		return bfi_thumb( $url, $params, $single );
	}
	
	public static function get_post_thumbnail_url( $params = array(), $post_id = null ) {
		self::_init();

		$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		return self::get_attachment_image_src( $post_thumbnail_id, $params );
	}
	
	public static function get_attachment_image_src( $attachment_id, $params = array() ) {
		self::_init();
		
		$url_data = wp_get_attachment_image_src( $attachment_id, 'fullsize' );
		
		if ( empty( $url_data ) ) {
			if ( ! isset( $params['placeholder'] ) || ! $params['placeholder'] )
				return false;
			
			$url_data[0] = pojo_placeholder_img_src();
		}

		return self::get_thumb( $url_data[0], $params );
	}
	
}