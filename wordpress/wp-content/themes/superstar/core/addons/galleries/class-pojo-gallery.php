<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include( 'classes/class-pojo-gallery-helpers.php' );
include( 'classes/class-pojo-gallery-shortcode.php' );
include( 'classes/class-pojo-gallery-cpt.php' );
include( 'classes/class-pojo-gallery-front.php' );

class Pojo_Gallery {

	/**
	 * @var Pojo_Gallery_CPT
	 */
	public $cpt;

	/**
	 * @var Pojo_Gallery_Front
	 */
	public $front;
	

	/**
	 * @var Pojo_Gallery_Shortcode
	 */
	public $shortcode;

	public function widgets_init() {
		if ( ! class_exists( 'Pojo_Widget_Base' ) )
			return;

		include_once( 'classes/class-pojo-widget-recent-galleries.php' );
		include_once( 'classes/class-pojo-widget-gallery.php' );
		
		register_widget( 'Pojo_Widget_Recent_Galleries' );
		register_widget( 'Pojo_Widget_Gallery' );
	}

	public function pojo_gallery_page_builder_register_widget( $widgets ) {
		$widgets[] = 'Pojo_Widget_Recent_Galleries';
		$widgets[] = 'Pojo_Widget_Gallery';
		return $widgets;
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'masterslider' );
	}

	public function bootstrap() {
		if ( apply_filters( 'pojo_force_disable_addon_gallery', false ) )
			return;
		
		$this->cpt       = new Pojo_Gallery_CPT();
		$this->front     = new Pojo_Gallery_Front();
		$this->shortcode = new Pojo_Gallery_Shortcode();

		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'pojo_builder_widgets', array( $this, 'pojo_gallery_page_builder_register_widget' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 150 );
	}

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'bootstrap' ), 20 );
	}

}
global $pojo_gallery;
$pojo_gallery = new Pojo_Gallery();

// EOF
