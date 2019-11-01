<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function pojo_gallery_get_shortcode_text( $id ) {
	return '[pojo-gallery id="' . $id . '"]';
}

function pojo_gallery_get_single_layout( $post_id = null ) {
	if ( is_null( $post_id ) )
		$post_id = get_the_ID();

	$option = atmb_get_field( 'po_gallery_single_layout', $post_id );
	if ( empty( $option ) ) {
		$option = pojo_get_option( 'gallery_single_layout' );
	}
	
	return $option;
}

function pojo_gallery_get_single_layout_class( $type ) {
	$layout = pojo_gallery_get_single_layout();
	
	if ( 'wide' === $layout ) {
		return SINGLE_GALLERY_WIDE_CLASSES;
	} else {
		if ( 'content' === $type )
			return SINGLE_GALLERY_HALF_CONTENT_CLASSES;
		else
			return SINGLE_GALLERY_HALF_THUMBNAIL_CLASSES;
	}
}