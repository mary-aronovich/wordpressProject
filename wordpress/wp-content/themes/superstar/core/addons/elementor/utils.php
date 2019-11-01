<?php

function pojo_elementor_theme_do_location( $location ) {
	if ( ! function_exists( 'elementor_theme_do_location' ) ) {
		return false;
	}

	return elementor_theme_do_location( $location );
}

function pojo_elementor_location_exits( $location, $check_match = false ) {
	if ( ! function_exists( 'elementor_location_exits' ) ) {
		return false;
	}

	return elementor_location_exits( $location, $check_match );
}
