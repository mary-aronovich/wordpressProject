<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Page_General extends Pojo_Settings_Page_Base {

	public function section_integrations( $sections = array() ) {
		// This option are deprecated.
		$ga_id = get_option( 'txt_google_analytics_id' );
		if ( empty( $ga_id ) ) {
			return $sections;
		}

		$fields = array();

		$fields[] = array(
			'id'                => 'txt_google_analytics_id',
			'title'             => __( 'Google Analytics ID', 'pojo' ),
			'desc'              => __( 'Log into your google analytics account to find your ID. e.g. UA-XXXXX-X', 'pojo' ),
			'classes'           => array( 'medium-text' ),
			'sanitize_callback' => array( 'Pojo_Settings_Validations', 'field_analytics' ),
		);

		$sections[] = array(
			'id'     => 'section-integrations',
			'page'   => $this->_page_id,
			'title'  => __( 'Integrations', 'pojo' ),
			'intro'  => __( 'Google Analytics is a free service offered by Google that generates detailed statistics about the visitors to a website.', 'pojo' ),
			'fields' => $fields,
		);

		return $sections;
	}

	public function section_favicons( $sections = array() ) {
		// No needed from WordPress 4.3, so we are hide this if no used.
		$favicon_icon = get_option( 'favicon_icon' );
		if ( empty( $favicon_icon ) ) {
			return $sections;
		}

		$fields = array();

		$fields[] = array(
			'id'    => 'favicon_icon',
			'type'  => Pojo_Settings::FIELD_IMAGE,
			'title' => __( 'Favicon Upload', 'pojo' ),
			'desc'  => __( 'Upload Icon for any Browser (16x16px ico/png)', 'pojo' ),
		);

		$fields[] = array(
			'id'    => 'apple_touch_icon_iphone',
			'type'  => Pojo_Settings::FIELD_IMAGE,
			'title' => __( 'Apple Touch Icon iPhone', 'pojo' ),
			'desc'  => __( 'Upload Icon for Apple iPhone (57x57 png)', 'pojo' ),
		);

		$fields[] = array(
			'id'    => 'apple_touch_icon_iphone_retina',
			'type'  => Pojo_Settings::FIELD_IMAGE,
			'title' => __( 'Apple Touch Icon iPhone retina', 'pojo' ),
			'desc'  => __( 'Upload Icon for Apple iPhone Retina Version (114x114 png)', 'pojo' ),
		);

		$fields[] = array(
			'id'    => 'apple_touch_icon_ipad',
			'type'  => Pojo_Settings::FIELD_IMAGE,
			'title' => __( 'Apple Touch Icon iPad', 'pojo' ),
			'desc'  => __( 'Upload Icon for Apple iPad (72x72 png)', 'pojo' ),
		);

		$fields[] = array(
			'id'    => 'apple_touch_icon_ipad_retina',
			'type'  => Pojo_Settings::FIELD_IMAGE,
			'title' => __( 'Apple Touch Icon iPad retina', 'pojo' ),
			'desc'  => __( 'Upload Icon for Apple iPad Retina Version (144x144 png)', 'pojo' ),
		);

		$sections[] = array(
			'id'     => 'section-favicons',
			'page'   => $this->_page_id,
			'title'  => __( 'Custom Favicons', 'pojo' ),
			'intro'  => '<span style="color: #ff0000; font-weight: bold;">' . sprintf( __( 'Please note: As of version 4.3 this feature will be added to one of the basic features of WordPress, we recommend you use it in <a href="%s">Customize > Site Identity</a>.', 'pojo' ), admin_url( 'customize.php?autofocus[control]=site_icon' ) ) . '</span>',
			'fields' => $fields,
		);

		return $sections;
	}

	public function section_copyright_text( $sections = array() ) {
		$fields = array();

		$fields[] = array(
			'id'      => 'txt_copyright_right',
			'title'   => __( 'Text Field 1', 'pojo' ),
			'classes' => array( 'large-text' ),
			'std'     => sprintf( __( 'Design by <a href="%s" rel="nofollow">Elementor</a>', 'pojo' ), 'https://elementor.com/' ),
		);

		$fields[] = array(
			'id'      => 'txt_copyright_left',
			'title'   => __( 'Text Field 2', 'pojo' ),
			'classes' => array( 'large-text' ),
			'std'     => sprintf( __( 'Theme by <a href="%s" rel="nofollow">Pojo.me</a> - WordPress Themes', 'pojo' ), 'http://pojo.me/' ),
		);

		$sections[] = array(
			'id'     => 'section-copyright-text',
			'page'   => $this->_page_id,
			'title'  => __( 'Copyright Text', 'pojo' ),
			'fields' => $fields,
		);

		return $sections;
	}

	public function __construct( $priority = 10 ) {
		$this->_page_id         = 'pojo-general';
		$this->_page_title      = __( 'General Settings', 'pojo' );
		$this->_page_menu_title = __( 'General', 'pojo' );
		//$this->_page_type       = 'top';
		$this->_page_type   = 'submenu';
		$this->_page_parent = 'pojo-home';
		//$this->_page_position   = '62';
		
		add_filter( 'pojo_register_settings_sections', array( &$this, 'section_integrations' ), 100 );
		add_filter( 'pojo_register_settings_sections', array( &$this, 'section_favicons' ), 110 );
		add_filter( 'pojo_register_settings_sections', array( &$this, 'section_copyright_text' ), 120 );

		parent::__construct( $priority );
	}
}
