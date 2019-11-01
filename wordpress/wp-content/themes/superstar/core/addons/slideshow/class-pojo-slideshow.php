<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include( 'classes/class-pojo-slideshow-helpers.php' );
include( 'classes/class-pojo-slideshow-cpt.php' );
include( 'classes/class-pojo-slideshow-shortcode.php' );
include( 'classes/class-pojo-slideshow-ajax.php' );

class Pojo_Slideshow {

	/**
	 * @var Pojo_Slideshow_CPT
	 */
	public $cpt;

	/**
	 * @var Pojo_Slideshow_Shortcode
	 */
	public $shortcode;

	/**
	 * @var Pojo_Slideshow_Helpers
	 */
	public $helpers;

	/**
	 * @var Pojo_Slideshow_Ajax
	 */
	public $ajax;
	
	public function is_activated() {
		return ! apply_filters( 'pojo_force_disable_addon_slideshow', false );
	}

	public function widgets_init() {
		if ( ! class_exists( 'Pojo_Widget_Base' ) )
			return;

		include_once( 'classes/class-pojo-slideshow-widget.php' );
		register_widget( 'Pojo_Slideshow_Widget' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'masterslider' );
	}

	public function bootstrap() {
		if ( ! $this->is_activated() )
			return;
		
		$this->cpt       = new Pojo_Slideshow_CPT();
		$this->shortcode = new Pojo_Slideshow_Shortcode();
		$this->helpers   = new Pojo_Slideshow_Helpers();
		$this->ajax      = new Pojo_Slideshow_Ajax();
		
		add_action( 'widgets_init', array( &$this, 'widgets_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 150 );
	}

	public function __construct() {
		add_action( 'after_setup_theme', array( &$this, 'bootstrap' ), 30 );
	}
	
}
global $pojo_slideshow;
$pojo_slideshow = new Pojo_Slideshow();

// EOF
