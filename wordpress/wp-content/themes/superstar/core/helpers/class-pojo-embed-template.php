<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Embed_Template {
	
	public function get_header() {
		ob_start(); ?>
		<!DOCTYPE html>
		<!--[if lt IE 7]>
		<html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
		<!--[if IE 7]>
		<html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
		<!--[if IE 8]>
		<html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
		<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
		<head>
			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0" />
			<title><?php wp_title( '|', true, 'right' ); ?></title>
			<?php

			if ( is_singular() && get_option( 'thread_comments' ) )
				wp_enqueue_script( 'comment-reply' );

			wp_head();
			?>
		</head>
		<body <?php body_class( 'admin-preview-iframe' ); ?>>
		<?php
		return ob_get_clean();
	}
	
	public function get_footer() {
		ob_start(); ?>
		<?php wp_footer(); ?>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}
	
	public function __construct() {}
	
}