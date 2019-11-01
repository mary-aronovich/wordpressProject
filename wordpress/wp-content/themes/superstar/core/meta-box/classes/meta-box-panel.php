<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @property Pojo_MetaBox_Field[] $fields
 */
class Pojo_MetaBox_Panel {
	
	/**
	 * @var array
	 */
	protected $_meta_box = array();

	/**
	 * @var Pojo_MetaBox_Field[]
	 */
	protected $_fields = array();

	public function __get( $name ) {
		if ( 'fields' === $name ) {
			$this->_build_fields();
			return $this->_fields;
		}

		return null;
	}

	public function __isset( $name ) {
		if ( 'fields' === $name ) {
			$this->_build_fields();
			return ! empty( $this->_fields );
		}

		return false;
	}

	protected function _build_fields() {
		if ( ! empty( $this->_fields ) )
			return;

		if ( ! empty( $this->_meta_box['fields'] ) ) {
			foreach ( $this->_meta_box['fields'] as $field ) {
				if ( empty( $field['id'] ) )
					continue;

				if ( empty( $field['type'] ) )
					$field['type'] = Pojo_MetaBox::FIELD_TEXT;

				if ( ! $classField = Pojo_MetaBoxHelpers::get_field_class( $field['type'] ) )
					continue;

				$this->_fields[] = new $classField( $field, $this->_meta_box['prefix'] );
			}
		}
	}

	public function admin_menu() {
		foreach ( $this->_meta_box['post_types'] as $post_type ) {
			add_meta_box(
				$this->_meta_box['id'],
				$this->_meta_box['title'],
				array( &$this, 'render_panel' ),
				$post_type,
				$this->_meta_box['context'],
				$this->_meta_box['priority']
			);
		}
	}

	public function render_panel( $post ) {
		if ( empty( $this->_meta_box['fields'] ) )
			return;

		wp_nonce_field( basename( __FILE__ ), '_pojo_meta_box_nonce' );

		echo '<div class="atmb-wrap-fields">';
		echo sprintf( '<input type="hidden" name="pojo_meta_box_id" value="%s" />', $this->_meta_box['id'] );

		foreach ( $this->fields as $field ) {
			echo $field->get_field_html();
		}
		echo '</div>';
	}

	public function save_post( $post_id ) {
		if ( ! isset( $_POST['_pojo_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['_pojo_meta_box_nonce'], basename( __FILE__ ) ) )
			return;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( ! isset( $this->fields ) )
			return;

		foreach ( $this->fields as $field ) {
			$field->save( $post_id );
		}
	}

	public function atmb_ajax_update_wrap() {
		
	}

	public function __construct( $meta_box ) {
		if ( empty( $meta_box['id'] ) ) {
			$meta_box['id'] = uniqid();
		}

		$default = array(
			'title'      => __( 'Pojo Meta Box', 'pojo' ),
			'post_types' => array( 'post' ),
			'priority'   => 'default',
			'context'    => 'advanced',
			'prefix'     => '',
			'locations'  => array(),
			'fields'     => array(),
		);

		$this->_meta_box = wp_parse_args( $meta_box, $default );

		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'save_post', array( &$this, 'save_post' ) );

		//add_action( 'atmb_ajax_update_wrap_' . $this->_meta_box['id'], array( &$this, 'atmb_ajax_update_wrap' ) );
	}
}
