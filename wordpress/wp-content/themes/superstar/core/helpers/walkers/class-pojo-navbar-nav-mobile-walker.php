<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Pojo_Navbar_Nav_Mobile_Walker' ) ) {
	
	class Pojo_Navbar_Nav_Mobile_Walker extends Walker_Nav_Menu {

		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "\n$indent<ul class=\"dropdown-menu\">\n";
		}
		
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$slug = sanitize_title( $item->title );
			$id   = apply_filters( 'nav_menu_item_id', 'menu-' . $slug, $item, $args );
			$id   = strlen( $id ) ? '' . esc_attr( $id ) . '' : '';

			$class_names = $value = $li_attributes = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;

			if ( in_array( 'current_page_parent', $classes ) ) {
				$classes[] = 'active';
			}
			
			if ( $this->has_children )
				$classes[] = 'dropdown';
			
			$classes[] = ( $item->current ) ? 'active' : '';

			$classes     = array_unique( $classes );
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );

			if ( ! empty( $args->li_class ) ) {
				$class_names .= ' ' . $args->li_class;
			}

			if ( 1 === $item->menu_order ) {
				$class_names .= ' first-item';
			}
			
			$class_names .= ' menu-item-' . $item->ID;

			$class_names = $class_names ? ' class="' . $id . ' ' . esc_attr( $class_names ) . '"' : ' class="' . $id . '"';

			$output .= $indent . '<li' . $class_names . $li_attributes . '>';

			$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
			$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
			$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
			$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
			if ( $this->has_children )
				$attributes .= ' class="dropdown-toggle" data-toggle="dropdown"';
			
			$item_output = $args->before;
			$item_output .= '<a' . $attributes . '>';
			$item_output .= '<span>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= '</span>';
			if ( $this->has_children )
				$item_output .= ' <span class="caret"></span>';
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
		
	}
	
}
