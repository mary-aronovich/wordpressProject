<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox {

	const FIELD_TEXT                   = 'text';
	const FIELD_TEXTAREA               = 'textarea';
	const FIELD_SELECT                 = 'select';
	const FIELD_CHECKBOX               = 'checkbox';
	const FIELD_RADIO                  = 'radio';
	const FIELD_NUMBER                 = 'number';
	const FIELD_DATE                   = 'date';
	const FIELD_COLOR                  = 'color';
	const FIELD_IMAGE                  = 'image';
	const FIELD_GALLERY                = 'gallery';
	const FIELD_HIDDEN                 = 'hidden';
	const FIELD_CHECKBOX_LIST          = 'checkbox_list';
	const FIELD_RADIO_IMAGE            = 'radio_image';
	const FIELD_RAW_HTML               = 'raw_html';
	const FIELD_DESCRIPTION            = 'description';
	const FIELD_HEADING                = 'heading';
	const FIELD_POST_TYPE_SELECT       = 'post_type_select';
	const FIELD_POSTS_SELECT           = 'posts_select';
	const FIELD_TAXONOMY_SELECT        = 'taxonomy_select';
	const FIELD_TAXONOMY_TERM_CHECKBOX = 'taxonomy_term_checkbox';
	const FIELD_SIDEBAR_SELECT         = 'sidebar_select';
	const FIELD_REPEATER               = 'repeater';

	const FIELD_BUTTON_COLLAPSE = 'button_collapse';
	const FIELD_WRAPPER         = 'wrapper';

	protected $_meta_boxes = array();

	public function init() {
		$this->_meta_boxes = apply_filters( 'pojo_meta_boxes', array() );

		foreach ( $this->_meta_boxes as $meta_box ) {
			new Pojo_MetaBox_Panel( $meta_box );
		}
	}

	public function admin_enqueue_scripts() {
		global $pagenow;
		
		if ( ! in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) )
			return;
		
		wp_enqueue_script( 'jquery-ui-sortable' );
		//wp_enqueue_style( 'at-meta-box', get_template_directory_uri() . '/core/meta-box/admin-ui/meta-box.css' );
		if ( POJO_DEVELOPER_MODE ) {
			wp_enqueue_script( 'at-meta-box', get_template_directory_uri() . '/core/meta-box/admin-ui/meta-box.js', array( 'jquery', 'jquery-ui-sortable' ) );
		}
	}

	public function ajax_atmb_update_wrap() {
		if ( empty( $_POST['post_id'] ) || empty( $_POST['pojo_meta_box_id'] ) ) {
			die( 'No found any post/wrap target' );
		}

		do_action( 'atmb_ajax_update_wrap_' . $_POST['pojo_meta_box_id'] );

		die();
	}

	public function admin_footer() {
		//echo '<script type="text/javascript">var atmb_locations = ' . Pojo_MetaBoxHelpers::get_json_all_location() . ';</script>';
	}

	public function __construct() {
		include( 'classes/helpers.php' );
		include( 'classes/meta-box-panel.php' );
		include( 'classes/meta-box-field.php' );
		include( 'classes/api.php' );

		$fields = array(
			self::FIELD_TEXT,
			self::FIELD_TEXTAREA,
			self::FIELD_SELECT,
			self::FIELD_CHECKBOX,
			self::FIELD_RADIO,
			self::FIELD_HIDDEN,
			self::FIELD_COLOR,
			self::FIELD_NUMBER,
			self::FIELD_DATE,
			self::FIELD_IMAGE,
			self::FIELD_GALLERY,
			self::FIELD_CHECKBOX_LIST,
			self::FIELD_RADIO_IMAGE,
			self::FIELD_RAW_HTML,
			self::FIELD_DESCRIPTION,
			self::FIELD_HEADING,
			self::FIELD_POST_TYPE_SELECT,
			self::FIELD_POSTS_SELECT,
			self::FIELD_TAXONOMY_SELECT,
			self::FIELD_TAXONOMY_TERM_CHECKBOX,
			self::FIELD_SIDEBAR_SELECT,
			self::FIELD_REPEATER,
			
			self::FIELD_BUTTON_COLLAPSE,
			self::FIELD_WRAPPER,
		);

		foreach ( $fields as $file ) {
			include( sprintf( '%s/core/meta-box/fields/%s.php', get_template_directory(), $file ) );
		}
		
		add_action( 'init', array( &$this, 'init' ), 50 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );

		add_action( 'wp_ajax_atmb_update_wrap', array( &$this, 'ajax_atmb_update_wrap' ) );

		//add_action( 'admin_footer', array( &$this, 'admin_footer' ) );
	}
}
new Pojo_MetaBox();

