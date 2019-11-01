<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Pojo_MetaBox_Field_Repeater
 * 
 * @property Pojo_MetaBox_Field[] repeater_fields
 */
class Pojo_MetaBox_Field_Repeater extends Pojo_MetaBox_Field {
	
	protected $_repeater_fields = array();

	public function __get( $name ) {
		if ( 'repeater_fields' === $name ) {
			$this->_build_fields();
			return $this->_repeater_fields;
		}

		return null;
	}

	public function __isset( $name ) {
		if ( 'repeater_fields' === $name ) {
			$this->_build_fields();
			return ! empty( $this->_repeater_fields );
		}

		return false;
	}

	protected function _build_fields() {
		if ( ! empty( $this->_repeater_fields ) )
			return;

		if ( ! empty( $this->_field['fields'] ) ) {
			foreach ( $this->_field['fields'] as $field ) {
				if ( empty( $field['id'] ) )
					continue;

				if ( empty( $field['type'] ) )
					$field['type'] = Pojo_MetaBox::FIELD_TEXT;

				if ( ! $class_field = Pojo_MetaBoxHelpers::get_field_class( $field['type'] ) )
					continue;

				$this->_field['prefix'] = '';
				$this->_repeater_fields[] = new $class_field( $field, $this->_field['prefix'] );
			}
		}
	}

	public function get_value( $post_id = null ) {
		$return = array();
		$value = $this->get_real_value( $post_id );
		if ( ! empty( $value ) && 1 <= $value ) {
			for ( $i = 0; $i < $value; $i++ ) {
				foreach ( $this->repeater_fields as $field ) {
					$tmp_field = clone $field;
					$tmp_field->_field['id'] = $this->_field['id'] . '[' . $i . '][' . $field->_field['id'] . ']';
					$return[ $i ][ $field->_field['id'] ] = $tmp_field->get_value( $post_id );
				}
			}
		}

		return $return;
	}

	public function get_real_value( $post_id = null ) {
		if ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}
		return absint( get_post_meta( $post_id, $this->_field['id'], true ) );
	}

	public function save( $post_id ) {
		// Get old value.
		$old_value = $this->get_real_value();
		// Get new value (if exits).
		$new_values = isset( $_POST[ $this->_field['id'] ] ) ? $_POST[ $this->_field['id'] ] : array();

		if ( ! empty( $old_value ) && 1 <= $old_value ) {
			delete_post_meta( $post_id, $this->_field['id'] );
			for ( $i = 0; $i <= $old_value; $i++ ) {
				foreach ( $this->repeater_fields as $field ) {
					delete_post_meta( $post_id, $this->_field['id'] . '[' . $i . '][' . $field->_field['id'] . ']' );
				}
			}
		}

		$index_count = sizeof( $new_values );
		if ( 1 <= $index_count ) {
			update_post_meta( $post_id, $this->_field['id'], $index_count );
			$i = 0;
			foreach( $new_values as $new_value ) {
				foreach ( $this->repeater_fields as $field ) {
					$field_key = $this->_field['id'] . '[' . $i . '][' . $field->_field['id'] . ']';
					if ( ! empty( $new_value[ $field->_field['id'] ] ) ) {
						update_post_meta( $post_id, $field_key, $new_value[ $field->_field['id'] ] );
					} else {
						delete_post_meta( $post_id, $field_key );
					}
				}
				$i++;
			}
		}
	}

	public function render() {
		$return = '<div class="atmb-repeater-wrap"><ol class="atmb-repeater-ol"><li class="atmb-repeater-clone hidden">';
		foreach ( $this->repeater_fields as $field ) {
			$tmp_field = clone $field;
			$tmp_field->_field['id'] = $this->_field['id'] . '[SKIP_FIELD][' . $field->_field['id'] . ']';
			$return .= $tmp_field->get_field_html();
		}
		$return .= '<div class="row-sortable-handle"><a href="javascript:void(0);" class="atmb-btn-repeater-remove-row"></a></div>';
		$return .= '</li>';

		$field_value = $this->get_real_value();
		if ( ! empty( $field_value ) ) {
			if ( ! empty( $field_value ) && 1 <= $field_value ) {
				for ( $i = 0; $i < $field_value; $i++ ) {
					$return .= '<li class="atmb-repeater-row">';
					foreach ( $this->repeater_fields as $field ) {
						$tmp_field = clone $field;
						$tmp_field->_field['id'] = $this->_field['id'] . '[' . $i . '][' . $field->_field['id'] . ']';
						$return .= $tmp_field->get_field_html();
					}
					$return .= '<div class="row-sortable-handle"><a href="javascript:void(0);" class="atmb-btn-repeater-remove-row"></a></div>';
					$return .= '</li>';
				}
			}
		}
		$return .= sprintf( '</ol><a href="javascript:void(0);" class="atmb-btn-add-repeater-clone button-primary">%s</a></div>', $this->_field['add_row_text'] );
		return $return;
	}

	public function __construct( $field, $prefix = '' ) {
		$field = wp_parse_args( $field, array(
			'fields' => array(),
			'add_row_text' => __( '+ Add Row', 'pojo' ),
		) );

		parent::__construct( $field, $prefix );
	}
}