<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Pojo_Class_BreadCrumbs
 */
class Pojo_Class_BreadCrumbs {

	protected $_breadcrumbs = array();
	protected $_delimiter;

	protected $_home;
	protected $_before;
	protected $_after;
	protected $_print_current_page;

	protected function _print_breadcrumbs() {
		// If our array empty, exit method. 
		if ( empty( $this->_breadcrumbs ) )
			return;

		$output = array();

		echo '<div id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">';

		foreach ( $this->_breadcrumbs as $link => $title ) {
			if ( 0 === $link ) {
				if ( $this->_print_current_page )
					$output[] = $this->_before . $title . $this->_after;
			} else
				$output[] = sprintf( '<span typeof="v:Breadcrumb"><a href="%s" rel="v:url" property="v:title">%s</a></span>', $link, $title );
		}
		
		echo implode( ' <span class="separator">' . $this->_delimiter . '</span> ', $output );

		echo '</div>';
	}

	protected function _get_category() {
		global $wp_query;

		$cat_obj    = $wp_query->get_queried_object();
		$this_cat   = $cat_obj->term_id;
		$this_cat   = get_category( $this_cat );
		$parent_cat = get_category( $this_cat->parent );

		if ( 0 !== (int) $this_cat->parent ) {
			$this->get_category_parents( $parent_cat );
		}

		$this->push( single_cat_title( '', false ) );
	}

	protected function _get_taxonomy() {
		global $wp_query;

		$cat_obj    = $wp_query->get_queried_object();
		$this_term   = $cat_obj->term_id;
		$this_term   = get_term( $this_term, $cat_obj->taxonomy );
		$parent_term = get_term( $this_term->parent, $cat_obj->taxonomy );

		if ( 0 !== (int) $this_term->parent ) {
			$this->get_taxonomy_parents( $parent_term, $cat_obj->taxonomy );
		}

		$this->push( single_term_title( '', false ) );
	}

	public function get_taxonomy_parents( $id, $taxonomy, $visited = array() ) {
		$parent = get_term( $id, $taxonomy );

		if ( is_wp_error( $parent ) )
			return;

		if ( $parent->parent && ( $parent->parent != $parent->term_id ) && ! in_array( $parent->parent, $visited ) ) {
			$visited[] = $parent->parent;
			$this->get_taxonomy_parents( $parent->parent, $taxonomy, $visited );
		}

		$this->push( $parent->name, get_term_link( $parent->term_id, $taxonomy ) );
	}

	protected function _get_day() {
		$this->push( get_the_time( 'Y' ), get_year_link( get_the_time( 'Y' ) ) );
		$this->push( get_the_time( 'F' ), get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) );
		$this->push( get_the_time( 'd' ) );
	}

	protected function _get_month() {
		$this->push( get_the_time( 'Y' ), get_year_link( get_the_time( 'Y' ) ) );
		$this->push( get_the_time( 'F' ) );
	}

	protected function _get_year() {
		$this->push( get_the_time( 'Y' ) );
	}

	protected function _get_single() {
		$current_page_parent = function_exists( 'po_get_current_page_parent' ) ? po_get_current_page_parent() : 0;
		if ( 0 !== $current_page_parent ) {
			$this->get_page_parents( $current_page_parent );
		} elseif ( 'post' === get_post_type() ) {
			$cat = get_the_category();
			$this->get_category_parents( $cat[0] );
		} else {
			$post_type = get_post_type_object( get_post_type() );
			$rewrite   = $post_type->rewrite;

			if ( $rewrite )
				$post_type_link = home_url( $rewrite['slug'] );
			else
				$post_type_link = add_query_arg( 'post_type', get_post_type(), home_url() );

			$this->push( $post_type->labels->singular_name, $post_type_link );
		}

		$this->push( get_the_title() );
	}

	protected function _get_archive_custom_post_type() {
		$post_type = get_post_type_object( get_post_type() );
		if ( ! empty( $post_type ) )
			$this->push( $post_type->labels->singular_name );
	}

	protected function _get_attachment() {
		global $post;

		$parent = get_post( $post->post_parent );
		$cat    = get_the_category( $parent->ID );

		if ( ! empty( $cat ) )
			$this->get_category_parents( $cat[0] );

		$this->push( $parent->post_title, get_permalink( $parent->ID ) );
		$this->push( get_the_title() );
	}

	protected function _get_page() {
		global $post;

		if ( $post->post_parent ) {
			$this->get_page_parents( $post->post_parent );
		}

		$this->push( get_the_title() );
	}

	protected function _get_author() {
		global $author;
		$userdata = get_userdata( $author );

		$this->push( sprintf( __( 'Article of: %s', 'pojo' ), $userdata->display_name ) );
	}

	public function get_category_parents( $id, $visited = array() ) {
		$parent = get_category( $id );
		if ( is_wp_error( $parent ) )
			return;

		if ( $parent->parent && ( $parent->parent != $parent->term_id ) && ! in_array( $parent->parent, $visited ) ) {
			$visited[] = $parent->parent;
			$this->get_category_parents( $parent->parent, $visited );
		}

		$this->push( $parent->name, get_category_link( $parent->term_id ) );
	}

	public function get_page_parents( $id ) {
		$post = get_post( $id );

		if ( 0 !== $post->post_parent )
			$this->get_page_parents( $post->post_parent );

		$this->push( $post->post_title, get_permalink( $post->ID ) );
	}
	
	protected function _get_woocommerce_pages() {
		$shop_page_id  = wc_get_page_id( 'shop' );
		$shop_on_front = get_option( 'page_on_front' ) === $shop_page_id;
		
		if ( ! $shop_on_front ) {
			$_name = $shop_page_id ? get_the_title( $shop_page_id ) : '';

			if ( ! $_name ) {
				$product_post_type = get_post_type_object( 'product' );
				$_name = $product_post_type->labels->singular_name;
			}

			if ( is_shop() && ! is_search() )
				$this->push( $_name );
			else
				$this->push( $_name, get_post_type_archive_link( 'product' ) );
		}
		
		if ( is_product_category() ) {
			$current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

			$ancestors = array_reverse( get_ancestors( $current_term->term_id, get_query_var( 'taxonomy' ) ) );

			foreach ( $ancestors as $ancestor ) {
				$ancestor = get_term( $ancestor, get_query_var( 'taxonomy' ) );
				
				$this->push( $ancestor->name, get_term_link( $ancestor->slug, get_query_var( 'taxonomy' ) ) );
			}

			$this->push( $current_term->name );
		} elseif ( is_product_tag() ) {
			$queried_object = get_queried_object();
			
			$this->push( $queried_object->name );
		} elseif ( is_search() ) {
			$this->push( get_search_query() );
		} elseif ( is_product() && ! is_attachment() ) {
			if ( $terms = wc_get_product_terms( get_the_ID(), 'product_cat', array( 'orderby' => 'parent', 'order' => 'DESC' ) ) ) {
				
				$main_term = $terms[0];

				$ancestors = get_ancestors( $main_term->term_id, 'product_cat' );

				$ancestors = array_reverse( $ancestors );

				foreach ( $ancestors as $ancestor ) {
					$ancestor = get_term( $ancestor, 'product_cat' );

					if ( ! is_wp_error( $ancestor ) && $ancestor )
						$this->push( $ancestor->name, get_term_link( $ancestor->slug, 'product_cat' ) );
				}
				
				$this->push( $main_term->name, get_term_link( $main_term->slug, 'product_cat' ) );
			}
			
			$this->push( get_the_title() );
		}
	}

	public function is_woocommerce_pages() {
		return Pojo_Compatibility::is_woocommerce_installed() && is_woocommerce();
	}
	
	protected function _get_archive_post_format() {
		//$this->push( get_post_format_string( get_post_format() ) );
		$format = get_post_format();
		if ( ! $format )
			return;
		
		$format_string = get_post_format_string( $format );
		if ( empty( $format_string ) )
			return;
		
		$this->push( $format_string );
	}

	public function push( $title, $link = null ) {
		if ( is_null( $link ) )
			$this->_breadcrumbs[] = $title;
		else
			$this->_breadcrumbs[ $link ] = $title;
	}

	public function breadcrumbs() {
		if ( is_home() || is_front_page() )
			return;

		// First all, home link.
		$this->push( $this->_home, home_url() );

		if ( is_category() )
			$this->_get_category();

		elseif ( is_day() )
			$this->_get_day();

		elseif ( is_month() )
			$this->_get_month();

		elseif ( is_year() )
			$this->_get_year();
		
		elseif ( $this->is_woocommerce_pages() )
			$this->_get_woocommerce_pages();

		elseif ( is_search() )
			$this->push( get_search_query() );

		elseif ( is_tax() )
			$this->_get_taxonomy();

		elseif ( is_single() && ! is_attachment() )
			$this->_get_single();

		elseif ( ! is_single() && ! is_page() && 'post' !== get_post_type() && ! is_404() )
			$this->_get_archive_custom_post_type();

		elseif ( is_attachment() )
			$this->_get_attachment();

		elseif ( is_page() )
			$this->_get_page();

		elseif ( is_author() )
			$this->_get_author();

		elseif ( is_tag() )
			$this->push( single_tag_title( '', false ) );
		
		elseif ( is_tax( 'post_format' ) )
			$this->_get_archive_post_format();
			
		elseif ( is_404() )
			$this->push( __( 'Error 404', 'pojo' ) );

		if ( get_query_var( 'paged' ) ) {
			$this->_breadcrumbs[0] .= sprintf( __( ' (Page %d)', 'pojo' ), get_query_var( 'paged' ) );
		}

		$this->_print_breadcrumbs();
	}

	/**
	 * @param string $delimiter
	 */
	public function __construct( $delimiter = '&raquo;' ) {
		if ( '&raquo;' === $delimiter ) {
			$delimiter = apply_filters( 'pojo_breadcrumbs_default_delimiter', $delimiter );
			
			$delimiter_option = pojo_get_option( 'breadcrumbs_delimiter' );
			if ( ! empty( $delimiter_option ) )
				$delimiter = '&' . $delimiter_option . ';';
		}
		$this->_delimiter = $delimiter;

		// text for the 'Home' link
		$this->_home = apply_filters( 'pojo_breadcrumbs_home_text', pojo_get_option( 'breadcrumbs_home_text' ) );
		if ( empty( $this->_home ) )
			$this->_home = __( 'Home', 'pojo' );
		
		$this->_before = '<span class="current">'; // tag before the current crumb
		$this->_after  = '</span>'; // tag after the current crumb
		$this->_print_current_page = apply_filters( 'pojo_breadcrumbs_print_current_page', true );
	}
}

/**
 * @param string $delimiter
 *
 * @return void
 */
if ( ! function_exists( 'pojo_breadcrumbs' ) ) {
	function pojo_breadcrumbs( $delimiter = '&raquo;' ) {
		$breadcrumbs = new Pojo_Class_BreadCrumbs( $delimiter );
		$breadcrumbs->breadcrumbs();
	} // end pojo_breadcrumbs()
}
