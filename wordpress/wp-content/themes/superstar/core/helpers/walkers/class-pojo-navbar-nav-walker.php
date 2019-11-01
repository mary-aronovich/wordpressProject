<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Pojo_Navbar_Nav_Walker' ) ) {
	
	class Pojo_Navbar_Nav_Walker extends Walker_Nav_Menu {
		
		/**
		 * Start the element output.
		 *
		 * @see Walker::start_el()
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 * @param int    $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$args = (object) $args;
			
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
			
			$slug = sanitize_title( $item->title );
			$id   = apply_filters( 'nav_menu_item_id', 'menu-' . $slug, $item, $args );
			$id   = ! empty( $id ) ? esc_attr( $id ) : '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;

			if ( in_array( 'current_page_parent', $classes ) ) {
				$classes[] = 'active';
			}
			
			$classes[] = ( $item->current ) ? 'active' : '';
			
			if ( ! empty( $id ) )
				$classes[] = $id;

			$classes     = array_unique( $classes );
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );

			if ( ! empty( $args->li_class ) ) {
				$class_names .= ' ' . $args->li_class;
			}

			if ( 1 === $item->menu_order ) {
				$class_names .= ' first-item';
			}
			
			$class_names .= ' menu-item-' . $item->ID;

			$output .= $indent . '<li class="' . esc_attr( $class_names ) . '">';

			$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
			
			$item_output = $args->before;
			$item_output .= '<a' . $attributes . '>';
			$item_output .= '<span>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= '</span>';
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
		
	}
	
}
