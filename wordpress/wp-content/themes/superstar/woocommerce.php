<?php
/**
 * The main WC template file.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$view = 'archive';
if ( is_singular() )
	$view = 'single';
elseif ( is_search() )
	$view = 'search';

do_action( 'pojo_setup_body_classes', $view, get_post_type(), '' );

get_header();

do_action( 'pojo_get_start_layout', $view, get_post_type(), '' );

if ( po_breadcrumbs_need_to_show() ) {
	pojo_breadcrumbs();
}

woocommerce_content();

do_action( 'pojo_get_end_layout', $view, get_post_type(), '' );

get_footer();