<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'pojo_setup' ) ) {
	// Pluggable function.. for register all our theme support.
	function pojo_setup() {
		// Load theme text domain.
		load_theme_textdomain( 'pojo', get_template_directory() . '/languages' );
		
		// Load child theme text domain.
		if ( is_child_theme() )
			load_child_theme_textdomain( 'pojochild', get_stylesheet_directory() . '/languages' );

		// Add callback for custom TinyMCE editor stylesheets.
		add_editor_style();

		// Support Thumbnail.
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_theme_support( 'automatic-feed-links' );

		// Register navigation menus for a theme.
		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'pojo' ),
				'primary_mobile' => __( 'Primary Mobile Menu', 'pojo' ),
				'sticky_menu' => __( 'Sticky Menu', 'pojo' ),
			)
		);

		add_filter( 'widget_text', 'shortcode_unautop' );
		add_filter( 'widget_text', 'do_shortcode' );
		add_filter( 'widget_title', 'shortcode_unautop' );
		add_filter( 'widget_title', 'do_shortcode' );
		add_filter( 'term_description', 'shortcode_unautop' );
		add_filter( 'term_description', 'do_shortcode' );
		add_filter( 'the_excerpt', 'shortcode_unautop' );
		add_filter( 'the_excerpt', 'do_shortcode' );
	}
}
add_action( 'after_setup_theme', 'pojo_setup' );

// Flush your rewrite rules.
function pojo_flush_rewrite_rules() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'pojo_flush_rewrite_rules' );

if ( ! isset( $content_width ) )
	$content_width = POJO_GLOBAL_CONTENT_WIDTH;

function _pojo_wrap_embed_video( $match ) {
	return '<div class="embed">' . $match[0] . '</div>';
}

function pojo_embed_content_filter( $content ) {
	// Looks for an iFrame on the page
	$pattern = '/<iframe.*?src=".*?(vimeo|youtu\.?be).*?".*?<\/iframe>/';

	return preg_replace_callback( $pattern, '_pojo_wrap_embed_video', $content );
}

// Apply it to post or page content areas
add_filter( 'the_content', 'pojo_embed_content_filter' );

// Apply it to your sidebar widgets if you like
add_filter( 'widget_text', 'pojo_embed_content_filter' );


/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function pojo_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() ) {
		return $title;
	}

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title = "$title $sep $site_description";
	}

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title = "$title $sep " . sprintf( __( 'Page %s', 'pojo' ), max( $paged, $page ) );
	}

	return $title;
}
add_filter( 'wp_title', 'pojo_wp_title', 10, 2 );

function pojome_optin_monster_sas_id( $id ) {
	return '1408736';
}
add_filter( 'optinmonster_sas_id', 'pojome_optin_monster_sas_id', 99999 );

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Fire the wp_body_open action.
	 *
	 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
	 */
	function wp_body_open() {
		/**
		 * Triggered after the opening <body> tag.
		 */
		do_action( 'wp_body_open' );
	}
endif;
