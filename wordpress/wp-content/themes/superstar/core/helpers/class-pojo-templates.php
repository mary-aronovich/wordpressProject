<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Pojo_Templates {

	/**
	 * @var string|null for body class
	 */
	protected $_last_layout_name = null;
	
	public function get_layout_name( $view, $cpt ) {
		$layout = 'default';
		if ( 'single' === $view || 'page' === $view ) {
			if ( post_type_supports( $cpt, 'pojo-layout' ) ) {
				$layout = atmb_get_field( 'po_layout' );
			}

			if ( empty( $layout ) || 'default' === $layout )
				$layout = get_theme_mod( "{$cpt}_layout" );
		}
		elseif ( 'archive' === $view ) {
			$layout = get_theme_mod( "{$cpt}_layout_archive" );
		}
		elseif ( '404' === $view ) {
			$layout = get_theme_mod( 'pojo_404_layouts' );
		}
		
		if ( empty( $layout ) || 'default' === $layout )
			$layout = get_theme_mod( 'pojo_general_layouts', Pojo_Layouts::LAYOUT_SIDEBAR_RIGHT );

		return $layout;
	}

	protected function _get_layout( $type, $name = 'default' ) {
		$default_name = 'default';
		if ( ! in_array( $type, array( 'start', 'end' ) ) )
			$type = 'start';
		
		$layout_method = sprintf( '_print_%s_layout_%s', $type, $name );
		if ( ! method_exists( $this, $layout_method ) )
			$layout_method = sprintf( '_print_%s_layout_%s', $type, $default_name );
		$this->$layout_method();
	}
	
	public function pojo_base_layouts( $layouts ) {
		return array(
			Pojo_Layouts::LAYOUT_FULL,
			Pojo_Layouts::LAYOUT_SIDEBAR_RIGHT,
			Pojo_Layouts::LAYOUT_SIDEBAR_LEFT,
			Pojo_Layouts::LAYOUT_SECTION,
		);
	}

	public function pojo_available_layout_page( $layouts ) {
		return array(
			Pojo_Layouts::LAYOUT_FULL,
			Pojo_Layouts::LAYOUT_SIDEBAR_RIGHT,
			Pojo_Layouts::LAYOUT_SIDEBAR_LEFT,
			Pojo_Layouts::LAYOUT_SECTION,
		);
	}
	
	protected function _print_start_layout_default() {}

	protected function _print_end_layout_default() {}

	public function pojo_before_content_loop( $display_type ) {}

	public function pojo_after_content_loop( $display_type ) {}

	public function pojo_get_the_content_layout( $view, $cpt, $addon = '' ) {
		if ( 'single' === $view ) {
			$format = atmb_get_field( 'pf_id' );
			if ( empty( $format ) )
				$format = 'text';
			
			if ( 'page' === $cpt ) {
				if ( in_array( $format, array( 'text', 'page-builder' ) ) ) {
					pojo_get_loop_template_part( 'page', $cpt );
				} else {
					pojo_get_loop_template_part( 'smart-page' );
				}
			} else {
				pojo_get_loop_template_part( $view, $cpt );
			}
		}
		elseif ( 'archive' === $view ) {
			pojo_get_loop_template_part( $view, $cpt );
		}
		else {
			pojo_get_loop_template_part( $view, $cpt );
		}
	}
	
	public function pojo_setup_body_classes( $view, $cpt, $addon = '' ) {
		$this->_last_layout_name = $this->get_layout_name( $view, $cpt );
	}

	public function body_class( $classes ) {
		if ( ! is_null( $this->_last_layout_name ) && ! empty( $this->_last_layout_name ) ) {
			if ( 'full' === $this->_last_layout_name )
				$this->_last_layout_name = 'full-width';
			$classes[] = 'layout-' . str_replace( '_', '-', $this->_last_layout_name );
		}
		
		if ( pojo_has_titlebar() ) {
			$classes[] = 'pojo-title-bar';
		}
		return $classes;
	}

	public function pojo_get_start_layout( $view, $cpt, $addon = '' ) {
		$this->_get_layout( 'start', $this->get_layout_name( $view, $cpt ) );
	}

	public function pojo_get_end_layout( $view, $cpt, $addon = '' ) {
		$this->_get_layout( 'end', $this->get_layout_name( $view, $cpt ) );
	}

	public function po_display_types( $display_types = array(), $cpt ) {
		return $display_types;
	}
	
	public function pojo_recent_galleries_layouts( $styles = array() ) {
		return $styles;
	}
	
	public function pojo_recent_posts_layouts( $styles = array() ) {
		return $styles;
	}
	
	public function pojo_posts_group_layouts( $styles = array() ) {
		return $styles;
	}
	
	public function pojo_recent_post_before_content_loop( $style ) {}
	
	public function pojo_recent_post_after_content_loop( $style ) {}
	
	public function pojo_recent_gallery_before_content_loop( $style ) {}
	
	public function pojo_recent_gallery_after_content_loop( $style ) {}
	
	public function pojo_posts_group_before_content_loop( $style ) {}
	
	public function pojo_posts_group_after_content_loop( $style ) {}

	public function pojo_after_print_paginate( $posts_per_page, $paged, $numposts, $max_page ) {
		if ( ! current_theme_supports( 'pojo-infinite-scroll' ) || 1 !== $paged )
			return;
		
		if ( is_page() )
			$paginate = atmb_get_field( 'po_pagination' );
		else
			$paginate = pojo_get_option( 'archive_pagination' );
		
		if ( 'infinite-scroll' !== $paginate )
			return;
		
		printf( '
		<div class="pojo-infscr-loader hidden">
			<div class="pojo-loadmore-wrap">
				<a href="javascript:void(0);" data-max_page="%d" data-url_structure="%s" class="pojo-load-more button">%s</a>
			</div>
			<div class="pojo-loading-wrap">
				<div class="pojo-loading button">%s</div>
			</div>
		</div>
	', $max_page, get_pagenum_link( 9999999 ), __( 'Load More', 'pojo' ), __( 'Loading', 'pojo' ) );
	}

	public function breadcrumbs_print_current_page( $bool ) {
		if ( ! is_singular() )
			return $bool;
		
		$post_type_supports = pojo_get_option( 'breadcrumbs_hide_current_page' );
		if ( ! is_array( $post_type_supports ) )
			return $bool;

		if ( in_array( get_post_type(), $post_type_supports ) )
			$bool = false;
		
		return $bool;
	}
	
	public function __construct() {
		global $_pojo_parent_id;
		$_pojo_parent_id = 0;
		
		add_action( 'pojo_setup_body_classes', array( &$this, 'pojo_setup_body_classes' ), 10, 3 );
		add_filter( 'body_class', array( &$this, 'body_class' ) );

		add_filter( 'pojo_base_layouts', array( &$this, 'pojo_base_layouts' ) );
		add_filter( 'pojo_available_layout_page', array( &$this, 'pojo_available_layout_page' ) );
		
		add_action( 'pojo_get_start_layout', array( &$this, 'pojo_get_start_layout' ), 20, 3 );
		add_action( 'pojo_get_end_layout', array( &$this, 'pojo_get_end_layout' ), 20, 3 );
		
		add_action( 'pojo_get_the_content_layout', array( &$this, 'pojo_get_the_content_layout' ), 20, 3 );

		add_action( 'pojo_before_content_loop', array( &$this, 'pojo_before_content_loop' ), 50 );
		add_action( 'pojo_after_content_loop', array( &$this, 'pojo_after_content_loop' ), 50 );

		add_filter( 'po_display_types', array( &$this, 'po_display_types' ), 10, 2 );
		
		add_filter( 'pojo_recent_galleries_layouts', array( &$this, 'pojo_recent_galleries_layouts' ) );
		add_filter( 'pojo_recent_posts_layouts', array( &$this, 'pojo_recent_posts_layouts' ) );
		
		add_filter( 'pojo_posts_group_layouts', array( &$this, 'pojo_posts_group_layouts' ) );

		add_action( 'pojo_recent_post_before_content_loop', array( &$this, 'pojo_recent_post_before_content_loop' ) );
		add_action( 'pojo_recent_post_after_content_loop', array( &$this, 'pojo_recent_post_after_content_loop' ) );

		add_action( 'pojo_recent_gallery_before_content_loop', array( &$this, 'pojo_recent_gallery_before_content_loop' ) );
		add_action( 'pojo_recent_gallery_after_content_loop', array( &$this, 'pojo_recent_gallery_after_content_loop' ) );

		add_action( 'pojo_posts_group_before_content_loop', array( &$this, 'pojo_posts_group_before_content_loop' ) );
		add_action( 'pojo_posts_group_after_content_loop', array( &$this, 'pojo_posts_group_after_content_loop' ) );
		
		add_action( 'pojo_after_print_paginate', array( &$this, 'pojo_after_print_paginate' ), 20, 4 );

		add_filter( 'pojo_breadcrumbs_print_current_page', array( &$this, 'breadcrumbs_print_current_page' ) );
	}
	
}
