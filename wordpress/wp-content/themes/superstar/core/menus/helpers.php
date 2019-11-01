<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function pojo_has_nav_menu( $menu_location, $post_id = false ) {
	$override_nav_menu = atmb_get_field( 'po_override_nav_menu_location_' . $menu_location, $post_id );

	if ( 'hide' === $override_nav_menu )
		return false;
	return true;
}

function pojo_get_nav_menu_location( $original_location, $post_id = false ) {
	$override_nav_menu = atmb_get_field( 'po_override_nav_menu_location_' . $original_location, $post_id );
	
	if ( ! empty( $original_location ) && 'hide' !== $override_nav_menu ) {
		return $override_nav_menu;
	}
	
	return $original_location;
}
