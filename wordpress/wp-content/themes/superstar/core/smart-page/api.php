<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function po_get_archive_query( $post_id = 0 ) {
	if ( 0 === $post_id )
		$post_id = get_the_ID();
	
	$post_type           = atmb_get_field( 'po_content', $post_id );
	$taxonomy            = atmb_get_field( 'po_taxonomy', $post_id );
	$posts_per_page_mode = atmb_get_field( 'po_posts_per_page_mode', $post_id );
	$order               = atmb_get_field( 'po_order', $post_id );
	$order_by            = atmb_get_field( 'po_order_by', $post_id );
	$posts_per_page      = absint( atmb_get_field( 'po_posts_per_page', $post_id ) );
	$offset              = absint( atmb_get_field( 'po_offset', $post_id ) );

	if ( ! $post_type ) {
		$post_type = 'post';
	}
	
	if ( empty( $order_by ) || ! in_array( $order_by, array( 'date', 'menu_order', 'title', 'rand' ) ) )
		$order_by = 'date';
	
	if ( empty( $order ) || ! in_array( $order, array( 'DESC', 'ASC' ) ) )
		$order = 'DESC';
	
	if ( 'custom' !== $posts_per_page_mode || empty( $posts_per_page ) ) {
		$posts_per_page = absint( pojo_get_option( 'posts_per_page' ) );
	}
	
	$current_paged = get_query_var( 'paged' );
	if ( is_front_page() && empty( $current_paged ) )
		$current_paged = get_query_var( 'page' );

	if ( 0 === $offset )
		$offset = '';
	elseif ( 1 < $current_paged )
		$offset = $offset + ( ( $current_paged - 1 ) * $posts_per_page );
	
	$query = array(
		'post_type'      => $post_type,
		'posts_per_page' => $posts_per_page,
		'paged'          => $current_paged,
		'order'          => $order,
		'orderby'        => $order_by,
		'offset'         => $offset,
	);

	if ( $taxonomy ) {
		$taxonomy_terms = atmb_get_field( 'po_taxonomy_terms', $post_id, Pojo_MetaBox::FIELD_CHECKBOX_LIST );
		if ( $taxonomy_terms ) {
			$query['tax_query'] = array(
				array(
					'taxonomy'         => $taxonomy,
					'field'            => 'id',
					'terms'            => $taxonomy_terms,
					'include_children' => false,
				),
			);
		}
	}

	return apply_filters( 'po_get_archive_query', $query, $post_id );
}

/**
 * @deprecated
 * 
 * @param     $option_key
 * @param int $post_id
 *
 * @return bool
 */
function po_is_need_to_show( $option_key, $post_id = 0 ) {
	$displays = atmb_get_field( 'po_display', $post_id, Pojo_MetaBox::FIELD_CHECKBOX_LIST );

	if ( ! $displays )
		return false;

	return ( in_array( $option_key, $displays ) );
}

function po_get_current_page_parent( $post_id = 0 ) {
	global $wpdb, $post;

	if ( is_null( $post ) )
		return 0;

	if ( 0 === $post_id )
		$post_id = $post->ID;

	if ( 'page' === get_post_type( $post_id ) || is_search() )
		return 0;

	if ( ! $parent_id = wp_cache_get( 'po_cache_parent_id_' . $post_id ) ) {
		$parent_id = 0;

		$parent_ids = $wpdb->get_col( $wpdb->prepare(
			'SELECT `post_id` FROM `' . $wpdb->postmeta . '`
				WHERE `meta_key` = \'po_content\'
					AND `meta_value` = %s;',
			get_post_type( $post_id )
		) );

		if ( $parent_ids ) {
			foreach ( $parent_ids as $_parent_id ) {
				if ( 'publish' !== get_post_status( $_parent_id ) )
					continue;
				
				if ( atmb_get_field( 'po_no_apply_child_posts', $_parent_id, Pojo_MetaBox::FIELD_CHECKBOX ) )
					continue;
				
				if ( $taxonomy = atmb_get_field( 'po_taxonomy', $_parent_id ) ) {

					if ( $terms = atmb_get_field( 'po_taxonomy_terms', $_parent_id, Pojo_MetaBox::FIELD_CHECKBOX_LIST ) ) {
						$post_terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );

						foreach ( $terms as $term ) {
							if ( in_array( $term, $post_terms ) ) {
								$parent_id = $_parent_id;
								break;
							}
						}
					}
				} else {
					$parent_id = $_parent_id;
				}
			}
		}
		wp_cache_set( 'po_cache_parent_id_' . $post_id, $parent_id );
	}

	return $parent_id;
}

function po_change_loop_to_parent( $action = 'restore' ) {
	static $old_val = null;
	global $wp_query, $post;

	switch ( $action ) {
		case 'change' :
			if ( is_page() || ! is_single() || is_404() || is_search() )
				return;

			$old_val = array(
				'wp_query'  => $wp_query,
				'post'      => $post,
			);

			$parent_id = po_get_current_page_parent( $post->ID );
			if ( 0 === $parent_id )
				return;

			$parent_id = absint( $parent_id );

			$wp_query = new WP_Query( array( 'page_id' => $parent_id ) );
			$post = WP_Post::get_instance( $parent_id );
			
			break;
		case 'restore' :

			if ( is_null( $old_val ) )
				return;

			$wp_query = $old_val['wp_query'];
			$post     = $old_val['post'];

			break;
	}
}

function pojo_fixes_split_shared_term( $term_id, $new_term_id, $term_taxonomy_id, $taxonomy ) {
	$query_args = array(
		'post_type' => 'page',
		'meta_query' => array(
			array(
				'key' => 'po_taxonomy',
				'value' => $taxonomy,
			),
			array(
				'key' => 'po_taxonomy_terms',
				'value' => $term_id,
			),
		),
	);
	$query = new WP_Query( $query_args );
	if ( ! $query->have_posts() )
		return;

	$posts = $query->get_posts();
	foreach ( $posts as $post ) {
		delete_post_meta( $post->ID, 'po_taxonomy_terms', $term_id );
		add_post_meta( $post->ID, 'po_taxonomy_terms', $new_term_id );
	}
}
add_action( 'split_shared_term', 'pojo_fixes_split_shared_term', 10, 4 );

function po_get_read_more( $parent_id ) {
	$add_read_more  = atmb_get_field( 'po_add_read_more', $parent_id, Pojo_MetaBox::FIELD_CHECKBOX );
	$text_read_more = atmb_get_field( 'po_text_read_more', $parent_id );
	$return         = '';

	if ( empty( $text_read_more ) )
		$text_read_more = __( 'Read More', 'pojo' );
	
	if ( $add_read_more )
		$return = sprintf( '<a href="%s" class="read-more">%s</a>', get_permalink(), $text_read_more );
	
	return $return;
}

function po_sub_header_style( $post_id = 0 ) {
	if ( 0 === $post_id )
		$post_id = get_the_ID();
	return atmb_get_field( 'po_sub_header_style', $post_id );
}

function po_breadcrumbs_need_to_show( $view = 'content' ) {
	global $post;
	
	if ( apply_filters( 'po_force_hide_breadcrumbs', false, $view ) )
		return false;

	$site_breadcrumbs = pojo_get_option( 'site_breadcrumbs' );
	if ( is_archive() ) {
		return 'hide' !== $site_breadcrumbs && 'content' === $view;
	}
	
	if ( ! $post )
		return false;
	
	$parent_id = get_the_ID();
	$page_breadcrumbs = atmb_get_field( 'po_show_page_breadcrumbs', $parent_id );
	if ( 'hide' === $site_breadcrumbs && ! is_singular() && ! is_page() )
		return false;
	
	if ( 'hide' === $page_breadcrumbs || ( empty( $page_breadcrumbs ) && 'hide' === $site_breadcrumbs ) )
		return false;
	
	$sub_header_style = po_sub_header_style( $parent_id );
	$have_sub_header  = true;
	if ( ( empty( $sub_header_style ) && ! po_is_page_header_support( get_post_type( $parent_id ) ) ) || in_array( $sub_header_style, array( 'none', 'slideshow', 'rev_slider', 'widgets_area' ) ) || ( ! is_singular() && ! is_page() ) ) {
		$have_sub_header = false;
	}
	
	if ( 'content' === $view )
		$return = ! $have_sub_header;
	else
		$return = $have_sub_header;
	
	return apply_filters( 'po_breadcrumbs_need_to_show', $return, $have_sub_header, $view );
}

function po_dynamic_sidebar( $index ) {
	po_change_loop_to_parent( 'change' );
	$return = dynamic_sidebar( $index );
	po_change_loop_to_parent();
	
	return $return;
}

/**
 * @param string $type
 * @param int    $parent_id
 *
 * @return bool
 */
function po_archive_metadata_show( $type, $parent_id = 0 ) {
	$site_metadata = pojo_get_option( 'archive_metadata_' . $type );
	
	if ( ! empty( $parent_id ) && 0 !== $parent_id ) {
		// Smart Page
		$metadata = atmb_get_field( 'po_metadata_' . $type, $parent_id );
		if ( ! empty( $metadata ) )
			$site_metadata = $metadata;
	}
	
	return 'hide' !== $site_metadata;
}

/**
 * @param string $type
 *
 * @return bool
 */
function po_single_metadata_show( $type ) {
	if ( 'post' !== get_post_type() ) {
		$bool = false;
	} else {
		$site_metadata = pojo_get_option( 'single_metadata_' . $type );
		$single_metadata = atmb_get_field( 'po_single_metadata_' . $type );
		if ( ! empty( $single_metadata ) ) {
			$site_metadata = $single_metadata;
		}

		$bool = ( 'hide' !== $site_metadata );
	}
	return apply_filters( 'po_single_metadata_show', $bool, $type );
}

/**
 * @param int $parent_id
 * 
 * @return void
 */
function po_print_archive_excerpt( $parent_id = 0 ) {
	$site_metadata = pojo_get_option( 'archive_metadata_excerpt' );

	if ( ! empty( $parent_id ) && 0 !== $parent_id ) {
		// Smart Page
		$metadata = atmb_get_field( 'po_metadata_excerpt', $parent_id );
		if ( ! empty( $metadata ) )
			$site_metadata = $metadata;
	}
	
	if ( 'hide' === $site_metadata )
		return;

	if ( 'full' === $site_metadata ) {
		if ( is_page() ) {
			// Fixes for WordPress read more in our Smart page.
			global $more;
			$more = 0;
		}
		echo '<div class="entry-content">';
		$readmore_text = po_get_archive_readmore_text( $parent_id, true );
		if ( ! empty( $readmore_text ) )
			$readmore_text = '<span>' . $readmore_text . '</span>';

		remove_filter( 'the_content', 'sharing_display', 19 );
		the_content( $readmore_text );

		// Hotfix for Pojo Sharing
		if ( function_exists( 'sharing_display' ) )
			add_filter( 'the_content', 'sharing_display', 19 );

		echo '</div>';
		return;
	}
	
	$excerpt = get_the_excerpt();
	if ( empty( $excerpt ) )
		return;
	
	$excerpt_words = absint( pojo_get_option( 'archive_excerpt_number_words' ) );
	if ( ! empty( $parent_id ) && 0 !== $parent_id ) {
		// Smart Page
		$metadata = atmb_get_field( 'po_excerpt_words', $parent_id );
		if ( ! empty( $metadata ) && $metadata == absint( $metadata ) && 'custom' === atmb_get_field( 'po_excerpt_words_mode', $parent_id ) )
			$excerpt_words = absint( $metadata );
	}
	
	if ( 0 !== $excerpt_words )
		$excerpt = pojo_get_words_limit( $excerpt, $excerpt_words );

	printf( '<div class="entry-excerpt"><p>%s</p></div>', $excerpt );
}

function po_get_archive_readmore_text( $parent_id = 0, $force_get_value = false ) {
	if ( ! po_archive_metadata_show( 'readmore', $parent_id ) && ! $force_get_value )
		return '';

	$readmore_text = pojo_get_option( 'archive_text_readmore' );
	if ( empty( $readmore_text ) )
		$readmore_text = __( 'Read More &raquo;', 'pojo' );

	if ( ! empty( $parent_id ) && 0 !== $parent_id ) {
		// Smart Page
		$metadata = atmb_get_field( 'po_text_readmore', $parent_id );
		if ( ! empty( $metadata ) && 'custom' === atmb_get_field( 'po_text_readmore_mode', $parent_id ) )
			$readmore_text = $metadata;
	}
	
	return $readmore_text;
}

function po_print_archive_readmore( $parent_id = 0, $readmore_link = false ) {
	if ( ! po_archive_metadata_show( 'readmore', $parent_id ) )
		return;
	
	if ( ! $readmore_link )
		$readmore_link = get_permalink();
	
	$readmore_text = po_get_archive_readmore_text( $parent_id );
	printf( '<a href="%s" class="read-more">%s</a>', $readmore_link, $readmore_text );
}

function po_get_display_type( $parent_id = 0 ) {
	global $post;
	
	if ( 0 === $parent_id ) {
		if ( $post )
			$parent_id = get_the_ID();
		else
			$parent_id = false;
	}
	
	$post_type    = atmb_get_field( 'po_content', $parent_id );
	$display_type = atmb_get_field( 'po_display_type' );
	if ( empty( $display_type ) || 'default' === $display_type || is_archive() ) {
		$display_type = pojo_get_option( 'posts_display_type' );
		if ( empty( $display_type ) )
			$display_type = 'default';
		
		$display_type = apply_filters( 'po_get_default_display_type', $display_type, $post_type );
	}
	
	return apply_filters( 'po_get_display_type', $display_type, $parent_id, $post_type );
}

function po_is_page_header_support( $post_type ) {
	if ( ! current_theme_supports( 'pojo-page-header' ) )
		return false;
	
	if ( ! is_single() && ! is_page() )
		return false;
	
	$post_type_supports = pojo_get_option( 'page_header_support' );
	if ( ! is_array( $post_type_supports ) )
		return false;
	
	return in_array( $post_type, $post_type_supports );
}

function po_get_theme_pagination_support() {
	$options = array(
		// Default is Numbers.
		'' => __( 'Numbers', 'pojo' ),
	);
	
	if ( current_theme_supports( 'pojo-infinite-scroll' ) ) {
		$options['infinite-scroll'] = __( 'Infinite Scroll', 'pojo' );
	}
	
	$options['hide'] = __( 'Hide', 'pojo' );
	
	return apply_filters( 'po_get_theme_pagination_support', $options );
}

function po_is_current_loop_smart_page() {
	global $content_query;

	if ( isset( $content_query ) && $content_query instanceof WP_Query && isset( $content_query->is_smart_page ) && $content_query->is_smart_page )
		return true;
	
	return false;
}

function smart_page_add_post_classes( $classes, $cpt ) {
	if ( po_is_current_loop_smart_page() ) {
		$classes[] = 'smart-page-item';
	}
	$classes[] = 'pojo-class-item';
	
	return $classes;
}
add_filter( 'pojo_post_classes', 'smart_page_add_post_classes', 10, 2 );
