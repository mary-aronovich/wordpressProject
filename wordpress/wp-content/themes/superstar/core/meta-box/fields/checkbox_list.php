<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Checkbox_list extends Pojo_MetaBox_Field {

	public function render() {
		if ( empty( $this->_field['options'] ) )
			return __( 'No found any results', 'pojo' );

		$html = '';
		if ( ! empty( $this->_field['title'] ) )
			$html .= sprintf( '<div class="atmb-label"><label>%s:</label></div>', $this->_field['title'] );

		$html .= '<div class="atmb-input atmb-multi-checkboxes">';
		
		foreach ( $this->_field['options'] as $option_key => $option_value ) {
			$html .= sprintf(
				'<div class="atmb-checkbox-list-item"><input id="atmb-id-%5$d" type="checkbox" class="atmb-field-checkbox" value="%2$s" name="%3$s[]"%4$s /><label for="atmb-id-%5$d"> %1$s</label></div>',
				$option_value,
				$option_key,
				$this->_field['id'],
				checked( in_array( $option_key, $this->get_value() ), true, false ),
				self::$_index_id++
			);
		}

		$html .= '</div>';

		return $html;
	}

	public function __construct( $field, $prefix = '' ) {
		$field['multiple'] = true;
		
		parent::__construct( $field, $prefix );
	}
}
