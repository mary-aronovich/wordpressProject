<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @deprecated
 * 
 * @param $key
 * @return string
 */
function atto_get_option( $key ) {
	_deprecated_function( __FUNCTION__, '3.8', 'pojo_get_option()' );
	
	return get_option( $key );
}

/**
 * @deprecated
 */
function at_print_post_share() {
	_deprecated_function( __FUNCTION__, '3.8' );
}

/**
 * @deprecated
 * @param $option_key
 */
function at_single_display_meta( $option_key ) {
	_deprecated_function( __FUNCTION__, '3.8', 'atpo_single_metadata_show()' );
}

/**
 * @deprecated
 * @param $option_key
 */
function at_archive_display_meta( $option_key ) {
	_deprecated_function( __FUNCTION__, '3.8', 'po_archive_metadata_show()' );
}

/**
 * @deprecated
 */
function at_get_limit_excerpt() {
	_deprecated_function( __FUNCTION__, '3.8', 'atpo_get_limit_excerpt()' );
}

/**
 * @deprecated
 */
function at_get_read_more() {
	_deprecated_function( __FUNCTION__, '3.8', 'atpo_print_archive_readmore()' );
}

/**
 * @deprecated
 */
function at_print_readmore_and_entry_meta() {
	_deprecated_function( __FUNCTION__, '3.8', 'atpo_print_archive_excerpt()' );
}

/**
 * @deprecated
 */
function atpo_print_layout_content() {
	_deprecated_function( __FUNCTION__, '3.8', 'new Smart Page api' );
}

/**
 * @deprecated
 * @param $parent_id
 */
function atpo_entry_meta( $parent_id ) {
	_deprecated_function( __FUNCTION__, '3.8', 'po_archive_metadata_show()' );
}

/**
 * @deprecated
 * @param $parent_id
 */
function atpo_print_readmore_and_entry_meta( $parent_id ) {
	_deprecated_function( __FUNCTION__, '3.8', 'po_print_archive_excerpt() or po_print_archive_readmore()' );
}

/**
 * @deprecated
 * @param $parent_id
 *
 * @return string
 */
function po_get_limit_excerpt( $parent_id ) {
	_deprecated_function( __FUNCTION__, '3.8', 'po_print_archive_excerpt()' );
	return '';
}

/**
 * @deprecated
 *
 * @param $parent_id
 *
 * @return string
 */
function po_get_post_classes( $parent_id ) {
	_deprecated_function( __FUNCTION__, '3.8' );
	return '';
}

/**
 * @deprecated
 */
function at_entry_meta() {
	_deprecated_function( __FUNCTION__, '3.8' );
}

/**
 * @deprecated
 */
function at_archive_entry_meta() {
	_deprecated_function( __FUNCTION__, '3.8' );
}

/**
 * @deprecated
 * 
 * @return bool
 */
function po_is_section_page() {
	_deprecated_function( __FUNCTION__, '3.9' );
	return false;
}

/**
 * @deprecated
 * 
 * @return bool
 */
function pojo_is_child_theme() {
	_deprecated_function( __FUNCTION__, '4.1', 'is_child_theme()' );
	return is_child_theme();
}