<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'pojo_button_post_edit' ) ) {
	/**
	 * Print button post edit with Bootstrap classes.
	 * 
	 * @param int    $id
	 * @param string $context
	 * 
	 * @return void
	 */
	function pojo_button_post_edit( $id = 0, $context = 'display' ) {
		$edit_post_link = get_edit_post_link( $id, $context );
		if ( ! $edit_post_link ) {
			return;
		}
		echo apply_filters( 'pojo_button_post_edit', '<a href="' . $edit_post_link . '" class="button size-small edit-link"><i class="fa fa-pencil"></i> ' . __( 'Edit', 'pojo' ) . '</a>', $edit_post_link, $id, $context );
	}
}

if ( ! function_exists( 'pojo_alert' ) ) {
	/**
	 * Our Alert block.
	 * 
	 * @param $msg
	 * @param bool $header
	 * @param bool $close_button
	 * @param string $type
	 * 
	 * @return void
	 */
	function pojo_alert( $msg, $header = false, $close_button = true, $type = 'success' ) {
		?>
	<div class="alert alert-<?php echo $type; ?> fade in">
		<?php if ( $close_button ) : ?><a class="close" data-dismiss="alert">&times;</a><?php endif; ?>
		<?php if ( $header ) : ?>
		<h4 class="alert-heading"><?php echo $header; ?></h4>
		<?php endif; ?>
		<p><?php echo $msg; ?></p>
	</div>
	<?php
	}
}
if ( ! function_exists( 'pojo_paginate' ) ) {
	/**
	 * Our custom Paginate with Bootstrap classes.
	 * 
	 * @param WP_Query $custom_query Optional. You can put it with your a custom query.
	 * 
	 * @return void
	 */
	function pojo_paginate( $custom_query = null ) {
		global $wp_query;
	
		$custom_query   = is_null( $custom_query ) ? $wp_query : $custom_query;
		$posts_per_page = intval( $custom_query->get( 'posts_per_page' ) );
		$paged          = intval( $custom_query->get( 'paged' ) );
		$numposts       = $custom_query->found_posts;
		$max_page       = $custom_query->max_num_pages;
		
		if ( $numposts <= $posts_per_page || -1 === $posts_per_page )
			return;
	
		if ( empty( $paged ) || 0 === $paged )
			$paged = 1;
	
		$pages_to_show         = 7;
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start       = floor( $pages_to_show_minus_1 / 2 );
		$half_page_end         = ceil( $pages_to_show_minus_1 / 2 );
		$start_page            = $paged - $half_page_start;
	
		if ( $start_page <= 0 )
			$start_page = 1;
	
		$end_page = $paged + $half_page_end;
	
		if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 )
			$end_page = $start_page + $pages_to_show_minus_1;
	
		if ( $end_page > $max_page ) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page   = $max_page;
		}
	
		if ( $start_page <= 0 )
			$start_page = 1;
	
		echo '<div class="align-pagination clearfix"><ul class="pagination">';
		
		for ( $i = $start_page; $i <= $end_page; $i++ ) {
			if ( $i == $paged )
				echo '<li data-page="' . $i . '" class="active"><a href="' . get_pagenum_link( $i ) . '">' . $i . '</a></li>';
			else
				echo '<li data-page="' . $i . '"><a href="' . get_pagenum_link( $i ) . '">' . $i . '</a></li>';
		}
	
		echo '</ul></div>';
		
		do_action( 'pojo_after_print_paginate', $posts_per_page, $paged, $numposts, $max_page );
	}
}

if ( ! function_exists( 'pojo_comment_reply_link' ) ) {
	/**
	 * I do not remember why I write this function.
	 *
	 * @param array $args
	 * @param int   $comment
	 * @param int   $post
	 * 
	 * @deprecated
	 * 
	 * @return void
	 */
	function pojo_comment_reply_link( $args = array(), $comment = null, $post = null ) {
		echo get_comment_reply_link( $args, $comment, $post );
	}
}

if ( ! function_exists( 'pojo_the_tags' ) ) {
	/**
	 * Print post tags with Bootstrap classes.
	 * 
	 * @param int    $id
	 * @param string $taxonomy
	 * 
	 * @return void
	 */
	function pojo_the_tags( $id = 0, $taxonomy = 'post_tag' ) {
		$terms = get_the_terms( $id, $taxonomy );
		
		if ( empty( $terms ) )
			return;
	
		$tag_formats = array();
	
		foreach ( $terms as $term ) {
			$term_link = get_term_link( $term->slug, $taxonomy );
			if ( ! is_wp_error( $term_link ) )
				$tag_formats[] = '<a rel="tag" class="label label-info" href="' . $term_link . '">' . $term->name . '</a>';
		}
	
	
		echo apply_filters( 'pojo_the_tags', implode( ' ', $tag_formats ), $tag_formats, $id, $taxonomy );
	}
}

if ( ! function_exists( 'pojo_get_taxonomies_without_links' ) ) {
	/**
	 * @param int $id
	 * @param string $taxonomy
	 *
	 * @return string
	 */
	function pojo_get_taxonomies_without_links( $id = null, $taxonomy ) {
		$return = '';
		$terms  = get_the_terms( $id, $taxonomy );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) )
			$return = implode( ', ', wp_list_pluck( $terms, 'name' ) );
		
		return $return;
	}
}

if ( ! function_exists( 'pojo_get_text_limit' ) ) {
	/**
	 * Limit string by chars, and no break the word.
	 *
	 * @param string    $string
	 * @param int       $limit
	 *
	 * @return string
	 */
	function pojo_get_text_limit( $string, $limit = 200 ) {
		if ( strlen( $string ) > $limit ) {
			$string = substr( $string, 0, $limit );
			$string = substr( $string, 0, - ( strlen( strrchr( $string, ' ' ) ) ) );
		}
		return $string;
	}
}

if ( ! function_exists( 'pojo_get_words_limit' ) ) {
	/**
	 * Limit string by words.
	 * 
	 * @param string    $string
	 * @param int       $word_limit
	 *
	 * @return string
	 */
	function pojo_get_words_limit( $string, $word_limit = 10 ) {
		$words = explode( ' ', $string );
		return implode( ' ', array_splice( $words, 0, $word_limit ) );
	}
}

if ( ! function_exists( 'pojo_get_list_pluck_with_prefix' ) ) {
	/**
	 * @param array  $list
	 * @param string $field
	 * @param string $prefix
	 *
	 * @return array
	 */
	function pojo_get_list_pluck_with_prefix( $list, $field, $prefix = '' ) {
		if ( ! $list )
			return array();
		
		$list = wp_list_pluck( $list, $field );
		if ( ! empty( $prefix ) ) {
			foreach ( $list as &$l )
				$l = $prefix . $l;
		}
		
		return $list;
	}
}

if ( ! function_exists( 'pojo_placeholder_img_src' ) ) {
	/**
	 * Get the placeholder image URL
	 * 
	 * @return string
	 */
	function pojo_placeholder_img_src() {
		return apply_filters( 'pojo_placeholder_img_src', get_template_directory_uri() . '/core/assets/images/placeholder.png' );
	}
}

if ( ! function_exists( 'pojo_hex2rgb' ) ) {
	/**
	 * @param $hex
	 *
	 * @return array
	 */
	function pojo_hex2rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );

		if ( 3 === strlen( $hex ) ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );

		return $rgb;
	}
}

if ( ! function_exists( 'pojo_link_pages' ) ) {
	/**
	 * @return void
	 */
	function pojo_link_pages() {
		wp_link_pages(
			array(
				'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'pojo' ) . '</span>',
				'after' => '</div>',
				'link_before' => '<span>',
				'link_after' => '</span>',
			)
		);
	}
}

if ( ! function_exists( 'pojo_is_enable_post_navigation' ) ) {
	function pojo_is_enable_post_navigation( $post_id = null ) {
		if ( is_null( $post_id ) )
			$post_id = get_the_ID();
		
		$post_option = atmb_get_field( 'po_show_post_nav', $post_id );
		if ( ! empty( $post_option ) ) {
			return 'show' === $post_option;
		}
		
		// Default
		$post_type = get_post_type( $post_id );
		$post_type_supports = pojo_get_option( 'pojo_enable_post_nav' );
		if ( ! is_array( $post_type_supports ) )
			return false;

		return in_array( $post_type, $post_type_supports );
	}
}

if ( ! function_exists( 'pojo_get_post_navigation' ) ) {
	function pojo_get_post_navigation( $args = array() ) {
		if ( ! pojo_is_enable_post_navigation() )
			return '';
		
		$post_type = get_post_type();
		$nav_by_taxonomy = pojo_get_option( 'pojo_post_nav_by_taxonomy_' . $post_type );
		$taxonomy = ! empty( $nav_by_taxonomy ) ? $nav_by_taxonomy : 'category';

		$args = wp_parse_args(
			$args,
			array(
				'prev_text' => '%title',
				'next_text' => '%title',
				'in_same_cat' => ( ! empty( $nav_by_taxonomy ) ),
				'excluded_terms' => '',
				'taxonomy' => $taxonomy,
			)
		);

		$navigation = '';
		$previous   = get_previous_post_link( '<div class="nav-prev">%link</div>', $args['prev_text'], $args['in_same_cat'], $args['excluded_terms'], $args['taxonomy'] );
		$next       = get_next_post_link( '<div class="nav-next">%link</div>', $args['next_text'], $args['in_same_cat'], $args['excluded_terms'], $args['taxonomy'] );

		if ( $previous || $next ) {
			$navigation = sprintf(
				'<nav class="post-navigation" rel="navigation">%s</nav>',
				$previous . $next
			);
		}
		
		return apply_filters( 'pojo_get_post_navigation', $navigation, $args );
	}
}

function pojo_get_layout_template_part( $slug, $name = null ) {
	$slug = 'layout/' . $slug;
	get_template_part( $slug, $name );
}

function pojo_get_loop_template_part( $slug, $name = null ) {
	$slug = 'loop/' . $slug;
	get_template_part( $slug, $name );
}

function pojo_get_content_template_part( $slug, $name = null ) {
	$slug = 'content/' . $slug;
	get_template_part( $slug, $name );
}

function pojo_get_template( $template_name, $args = array() ) {
	$located = locate_template( $template_name );
	if ( empty( $located ) )
		return;

	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}
	
	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'pojo_get_template', $located, $template_name, $args );

	//do_action( 'pojo_before_template_part', $template_name, $located, $args );
	include( $located );
	//do_action( 'pojo_after_template_part', $template_name, $located, $args );
}

function pojo_is_show_page_title( $parent_id = false ) {
	$option = atmb_get_field( 'po_hide_page_title', $parent_id );
	if ( empty( $option ) ) {
		$option = pojo_get_option( 'hide_page_title' );
	}
	
	return ( 'hide' !== $option );
}

function pojo_is_show_about_author( $post_id = false ) {
	$option = atmb_get_field( 'po_about_author', $post_id );
	if ( empty( $option ) ) {
		$option = pojo_get_option( 'single_about_author' );
	}
	
	return ( 'hide' !== $option );
}

function pojo_get_demo_theme_id( $theme, $lang = '' ) {
	if ( empty( $lang ) )
		$lang = 'en';
	
	return get_blog_option( 1, 'pojo_demo_theme_id_' . $theme . '_' . $lang );
}

function pojo_array_to_attributes( $array = array(), $delimiter = ' ' ) {
	$return = array();
	if ( is_array( $array ) && ! empty( $array ) ) {
		foreach ( $array as $key => $value ) {
			$return[] = sprintf( '%s="%s"', $key, $value );
		}
	}
	return implode( $delimiter, $return );
}

function pojo_get_sidebar_columns_class( $choices, $default, $theme_mod_name ) {
	$choices = apply_filters( 'pojo_get_sidebar_columns_choices_classes', $choices, $theme_mod_name );
	
	$current_column = get_theme_mod( $theme_mod_name );
	if ( ! isset( $choices[ $current_column ]  ) )
		$current_column = $default;
	
	return apply_filters( 'pojo_get_sidebar_columns_class', $choices[ $current_column ], $theme_mod_name );
}

/**
 * Is page no have Header and Footer?
 *
 * @param $post_id
 *
 * @return bool
 */
function pojo_is_blank_page( $post_id = false ) {
	if ( ! current_theme_supports( 'pojo-blank-page' ) || ! is_singular() ) {
		return false;
	}
	
	$is_blank_page = atmb_get_field( 'po_blank_page', $post_id, Pojo_MetaBox::FIELD_CHECKBOX );
	return (bool) apply_filters( 'pojo_is_blank_page', $is_blank_page );
}

/**
 * Validates a hex color.
 *
 * Returns either '', a 3 or 6 digit hex color (with #), or null.
 * For validating values without a #, see sanitize_hex_color_no_hash().
 *
 *
 * @param string $color
 * @return string
 */
function pojo_sanitize_hex_color( $color ) {
	if ( '' === $color )
		return '';

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
		return $color;
	return '';
}

/**
 * @private
 */
function _pojo_parse_titlebar_html() {
	if ( ! have_posts() || ( ! is_single() && ! is_page() ) )
		return;

	$page_header_inline_styles = array();
	$current_page_parent = get_the_ID();
	$sub_header_style = po_sub_header_style( $current_page_parent );

	if ( empty( $sub_header_style ) || 'show' === $sub_header_style ) {
		if ( 'show' !== $sub_header_style && ! po_is_page_header_support( get_post_type( $current_page_parent ) ) )
			return;

		$sub_header_style = get_theme_mod( 'ph_style' );
		if ( ! in_array( $sub_header_style, array( 'custom_bg', 'transparent' ) ) )
			$sub_header_style = 'custom_bg';
	}

	if ( 'none' === $sub_header_style )
		return;

	if ( 'rev_slider' === $sub_header_style ) :
		$rev_slider = atmb_get_field( 'po_sub_header_rev_slider', $current_page_parent );
		if ( $rev_slider ) :
			echo '<div id="page-header" class="rev-slider-wrap">' . do_shortcode( sprintf( '[rev_slider %s]', $rev_slider ) ) . '</div>';
		endif;
	elseif ( 'widgets_area' === $sub_header_style ) :
		$sidebar_index = atmb_get_field( 'po_sub_header_widgets_area', $current_page_parent );
		if ( is_active_sidebar( $sidebar_index ) ) :
			$container_class = 'container';
			if ( '100_width' === atmb_get_field( 'po_sub_header_width_content', $current_page_parent ) )
				$container_class = 'container-section';

			echo '<div id="title-bar" class="widgets-area"><div class="' . $container_class . '">';
			dynamic_sidebar( $sidebar_index );
			echo '</div></div>';
		endif;
	elseif ( 'slideshow' === $sub_header_style ) :
		$slideshow_id = absint( atmb_get_field( 'po_sub_header_slideshow', $current_page_parent ) );
		if ( 0 !== $slideshow_id ) :
			echo '<div id="page-header" class="pojo-slideshow-wrap">' . do_shortcode( sprintf( '[pojo-slideshow id="%d"]', $slideshow_id ) ) . '</div>';
		endif;
	else :
		$sub_header_image = false;
		$height_header = atmb_get_field( 'po_height_sub_header', $current_page_parent );

		$print_breadcrumbs = po_breadcrumbs_need_to_show( 'subheader' );

		if ( 'custom_bg' === $sub_header_style ) {
			$sub_header_attachment_id = atmb_get_field( 'po_sub_header_image', $current_page_parent, Pojo_MetaBox::FIELD_IMAGE );
			$sub_header_color = atmb_get_field( 'po_sub_header_color', $current_page_parent );
			$sub_header_position = atmb_get_field( 'po_sub_header_position', $current_page_parent );
			$sub_header_repeat = atmb_get_field( 'po_sub_header_repeat', $current_page_parent );
			$sub_header_attachment = atmb_get_field( 'po_sub_header_attachment', $current_page_parent );
			$sub_header_size = atmb_get_field( 'po_sub_header_size', $current_page_parent );

			if ( ! empty( $sub_header_attachment_id ) ) {
				$sub_header_image = wp_get_attachment_image_src( $sub_header_attachment_id, 'full' );
				$page_header_inline_styles[] = 'background-image: url("' . esc_attr( $sub_header_image[0] ) . '")';
			}

			if ( ! empty( $sub_header_color ) ) {
				$sub_header_opacity = atmb_get_field( 'po_sub_header_opacity', $current_page_parent );
				if ( $sub_header_opacity ) {
					$rgb_color = pojo_hex2rgb( $sub_header_color );
					$sub_header_color = sprintf( 'rgba(%d,%d,%d,%s)', $rgb_color[0], $rgb_color[1], $rgb_color[2], ( $sub_header_opacity / 100 ) );
				}
				$page_header_inline_styles[] = 'background-color:' . $sub_header_color;
			}

			if ( ! empty( $sub_header_position ) ) {
				$page_header_inline_styles[] = 'background-position:' . $sub_header_position;
			}

			if ( ! empty( $sub_header_repeat ) ) {
				$page_header_inline_styles[] = 'background-repeat:' . $sub_header_repeat;
			}

			if ( ! empty( $sub_header_attachment ) ) {
				$page_header_inline_styles[] = 'background-attachment:' . $sub_header_attachment;
			}

			if ( ! empty( $sub_header_size ) ) {
				$page_header_inline_styles[] = 'background-size:' . $sub_header_size;
			}
		}

		$title = atmb_get_field( 'po_title', $current_page_parent );

		if ( empty( $title ) && empty( $sub_title ) )
			$title = get_the_title();

		pojo_get_template(
			'titlebar.php',
			array(
				'page_header_inline_styles' => $page_header_inline_styles,
				'height_header' => $height_header,
				'sub_header_style' => $sub_header_style,
				'title' => $title,
				'print_breadcrumbs' => $print_breadcrumbs,
			)
		);
	endif;
}

function pojo_get_titlebar() {
	static $_titlebar_html = null;
	
	if ( is_null( $_titlebar_html ) ) {
		ob_start();
		_pojo_parse_titlebar_html();
		$_titlebar_html = ob_get_clean();
	}
	return $_titlebar_html;
}

function pojo_has_titlebar() {
	$titlebar_html = pojo_get_titlebar();
	return ( ! empty( $titlebar_html ) );
}

function pojo_print_titlebar() {
	echo pojo_get_titlebar();
}

function pojo_get_youtube_id_from_url( $link ) {
	preg_match( "#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $link, $matches );
	
	if ( empty( $matches ) )
		return '';
	
	if ( isset( $matches[0] ) )
		return $matches[0];
	
	return '';
}
