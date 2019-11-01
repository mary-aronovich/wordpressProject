<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Simple function for get field.
 *
 * @param string   $key
 * @param bool|int $post_id
 * @param string   $type
 *
 * @return bool|mixed
 */
function atmb_get_field( $key, $post_id = false, $type = Pojo_MetaBox::FIELD_TEXT ) {
	/** @var $field Pojo_MetaBox_Field */
	global $post;
	
	if ( ! $post_id ) {
		if ( is_null( $post ) )
			return false;
		
		$post_id = $post->ID;
	}

	$cache_key   = md5( $key . $post_id . $type );
	$cache_value = wp_cache_get( $cache_key );

	if ( $cache_value )
		return $cache_value;

	if ( ! $class_field = Pojo_MetaBoxHelpers::get_field_class( $type ) )
		return false;

	$field       = new $class_field( array( 'id' => $key ) );
	$field_value = $field->get_value( $post_id );
	
	if ( $field_value || '0' === $field_value ) {
		wp_cache_set( $cache_key, $field_value );
		
		return $field_value;
	}

	return false;
}

function atmb_get_field_without_type( $key, $prefix = '', $post_id = false ) {
	/** @var $field Pojo_MetaBox_Field */
	global $post;

	if ( ! $post_id ) {
		if ( is_null( $post ) )
			return false;

		$post_id = $post->ID;
	}

	$cache_key = md5( $key . $prefix . $post_id );
	if ( $cache_value = wp_cache_get( $cache_key ) )
		return $cache_value;

	$meta_boxes = apply_filters( 'pojo_meta_boxes', array() );
	foreach ( $meta_boxes as $meta_box ) {
		if ( ! empty( $meta_box['fields'] ) && $prefix === $meta_box['prefix'] ) {
			foreach ( $meta_box['fields'] as $field ) {
				if ( $field['id'] === $key ) {
					$type = ! empty( $field['type'] ) ? $field['type'] : Pojo_MetaBox::FIELD_TEXT;

					if ( ! $class_field = Pojo_MetaBoxHelpers::get_field_class( $type ) )
						return false;

					$field = new $class_field( $field, $prefix );
					$field_value = $field->get_value( $post_id );
					wp_cache_set( $cache_key, $field_value );
					return $field_value;
				}
			}
		}
	}
	return false;
}

function atmb_get_field_with_default( $key, $post_id = false, $default = false ) {
	if ( ! $value = atmb_get_field( $key, $post_id, Pojo_MetaBox::FIELD_TEXT ) )
		$value = $default;
	
	return $value;
}