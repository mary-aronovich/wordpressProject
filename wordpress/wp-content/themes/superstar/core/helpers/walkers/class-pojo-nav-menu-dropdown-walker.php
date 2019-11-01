<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Nav_Menu_Dropdown_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$url = '#' !== $item->url ? $item->url : '';
		$output .= sprintf(
			'<option value="%s"%s>%s %s</option>',
			$url,
			selected( true, $item->current, false ),
			str_repeat( '-', $depth ),
			$item->title
		);
	}

}