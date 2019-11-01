<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function pojo_main_widgets_init() {
	register_sidebar(
		array(
			'id'            => 'pojo-' . sanitize_title( 'Main Sidebar' ),
			'name'          => __( 'Main Sidebar', 'pojo' ),
			'description'   => __( 'These are widgets for the Main Sidebar', 'pojo' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></section>',
			'before_title'  => '<h5 class="widget-title"><span>',
			'after_title'   => '</span></h5>',
		)
	);

	register_sidebar(
		array(
			'id'            => 'pojo-' . sanitize_title( 'Sticky Header' ),
			'name'          => __( 'Sticky Header', 'pojo' ),
			'description'   => __( 'These are widgets for the Sticky Header', 'pojo' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s ' . FOOTER_WIDGET_CLASSES . '"><div class="widget-inner">',
			'after_widget'  => '</div></section>',
			'before_title'  => '<h5 class="widget-title"><span>',
			'after_title'   => '</span></h5>',
		)
	);
}
add_action( 'widgets_init', 'pojo_main_widgets_init' );

function pojo_theme_setup() {
	add_theme_support( 'pojo-infinite-scroll' );
	add_theme_support( 'pojo-background-options' );
	add_theme_support( 'pojo-blank-page' );
	add_theme_support( 'pojo-wc-menu-cart' );
	add_theme_support( 'pojo-page-header' );
	add_theme_support( 'pojo-recent-post-metadata' );
}
add_action( 'after_setup_theme', 'pojo_theme_setup', 20 );

function superstar_set_breadcrumbs_default_delimiter( $delimiter ) {
	return '&#8226;';
}
add_filter( 'pojo_breadcrumbs_default_delimiter', 'superstar_set_breadcrumbs_default_delimiter' );

function superstar_set_localize_scripts_array( $array ) {
	$array['superfish_args']['speed'] = 'slow';
	$array['superfish_args']['animationOut'] = array( 'opacity' => 'hide', 'height' => 'hide' );
	
	return $array;
}
add_filter( 'pojo_localize_scripts_array', 'superstar_set_localize_scripts_array', 300 );