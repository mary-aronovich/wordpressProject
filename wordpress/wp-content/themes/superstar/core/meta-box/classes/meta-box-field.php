<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Abstract class for MetaBox fields.
 */
abstract class Pojo_MetaBox_Field {
	/**
	 * Unique index for ID element.
	 * @var int
	 */
	protected static $_index_id = 1;

	/**
	 * field options.
	 * @var array
	 */
	public $_field;

	/**
	 * Method check if has any saved fields.
	 * 
	 * @param int|null $post_id Optional
	 *
	 * @return bool
	 */
	public function has_been_saved( $post_id = null ) {
		// Use post_id from the loop if not setup.
		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}
		// Get all post_meta by post id for checking..
		$value = get_post_meta( $post_id );
		// Hotfix for Yoast Wordpress SEO
		if ( Pojo_Compatibility::is_wordpress_seo_installer() ) {
			unset( $value['_yoast_wpseo_linkdex'] );
		}
		
		// Hotfix for Easy Digital Downloads
		if ( Pojo_Compatibility::is_easy_digital_downloads_installed() ) {
			unset( $value['_edd_download_earnings'] );
			unset( $value['_edd_download_sales'] );
		}
		
		// Hotfix for pf_id value
		if ( isset( $value['pf_id'] ) )
			unset( $value['pf_id'] );
		
		return ! empty( $value );
	}

	/**
	 * Method for get value from post_meta with field options.
	 * 
	 * @param int|null $post_id
	 *
	 * @return mixed
	 */
	public function get_value( $post_id = null ) {
		// Use post_id from the loop if not setup.
		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}
		
		// If not has been saved yet and setup std value, return it.
		if ( ! $this->has_been_saved( $post_id ) && ! empty( $this->_field['std'] ) ) {
			return $this->_field['std'];
		}
		// Return post meta by key.
		return get_post_meta( $post_id, $this->_field['id'], ! $this->_field['multiple'] );
	}

	/**
	 * Method running when save post.
	 * 
	 * @param int $post_id
	 */
	public function save( $post_id ) {
		// Get old value.
		//$old_value = $this->get_value( $post_id );
		// Get new value (if exits).
		$new_value = isset( $_POST[ $this->_field['id'] ] ) ? $_POST[ $this->_field['id'] ] : null;
		
		if ( empty( $new_value ) && '0' !== $new_value ) {
			delete_post_meta( $post_id, $this->_field['id'] );
		}
		elseif ( $this->_field['multiple'] ) {
			delete_post_meta( $post_id, $this->_field['id'] );

			foreach ( $new_value as $tmp_value ) {
				add_post_meta( $post_id, $this->_field['id'], $tmp_value );
			}
		}
		else {
			update_post_meta( $post_id, $this->_field['id'], $new_value );
		}
	}

	/**
	 * Print field html.
	 * 
	 * @return string
	 */
	public function get_field_html() {
		return $this->before_wrap_html() . $this->render() . $this->after_wrap_html();
	}

	/**
	 * Print Before Wrap with classes.
	 * 
	 * @return string
	 */
	public function before_wrap_html() {
		$attributes = '';
		if ( ! empty( $this->_field['show_on'] ) ) {
			foreach ( $this->_field['show_on'] as $attr_key => $attr_val ) {
				if ( is_array( $attr_val ) ) {	
					foreach ( $attr_val as $attr_val_item ) {
						$attributes .= sprintf( ' data-show_on_%s="%s"', $attr_key, $attr_val_item );
					}
				} else {
					$attributes .= sprintf( ' data-show_on_%s="%s"', $attr_key, $attr_val );
				}
			}
		}

		return sprintf( '<div class="%s"%s>', implode( ' ', $this->_field['classes'] ), $attributes );
	}

	/**
	 * Print field html.
	 * 
	 * @return string
	 */
	public function render() {
		return __( 'No setup render field', 'pojo' );
	}

	/**
	 * Print close field.
	 * 
	 * @return string
	 */
	public function after_wrap_html() {
		return '</div>';
	}

	/**
	 * Get Desc Field (If available)
	 * 
	 * @return string
	 */
	public function get_desc_field() {
		if ( ! empty( $this->_field['desc'] ) ) {
			return sprintf( '<span class="atmb-note">%s</span>', $this->_field['desc'] );
		}
		
		return '';
	}

	/**
	 * Construct Class.
	 * 
	 * @param        $field
	 * @param string $prefix
	 */
	public function __construct( $field, $prefix = '' ) {
		// Default field options.
		$default = array(
			'title' => '',
			'desc' => '',
			'std' => '',
			'placeholder' => '',
			'multiple' => false,
			'saved' => true,
			'options' => array(),
			'classes' => array(),
			'show_on' => array(),
			'field_attributes' => array(),
		);

		// Parse args with default options.
		$this->_field = wp_parse_args( $field, $default );

		// Added default class.
		$this->_field['classes'][] = 'atmb-field-row';
		$this->_field['classes'][] = 'atmb-' . $this->_field['id'];

		// Remove duplicate classes.
		$this->_field['classes'] = array_unique( $this->_field['classes'] );

		// Setup field prefix (Optional).
		$this->_field['id'] = $prefix . $this->_field['id'];
	}
}