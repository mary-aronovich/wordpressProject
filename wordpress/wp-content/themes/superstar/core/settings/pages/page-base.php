<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Pojo_Settings_Page_Base {

	protected $_page_id;
	protected $_page_title;
	protected $_page_menu_title;
	protected $_page_parent;
	protected $_page_position;
	
	protected $_page_type = 'top';
	protected $_page_capability = 'manage_options';

	public function pojo_register_settings_pages( $pages = array() ) {
		$pages[ $this->_page_id ] = array(
			'type' => $this->_page_type,
			'title' => $this->_page_title,
			'menu_title' => $this->_page_menu_title,
			'parent' => $this->_page_parent,
			'capability' => $this->_page_capability,
			'position' => $this->_page_position,
		);

		return $pages;
	}

	public function __construct( $priority = 10 ) {
		add_filter( 'pojo_register_settings_pages', array( &$this, 'pojo_register_settings_pages' ), $priority );
	}
	
}