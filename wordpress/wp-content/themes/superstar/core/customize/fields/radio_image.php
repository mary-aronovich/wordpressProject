<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Radio_image extends Pojo_Customize_Control_Field_Base {

	public function __construct( $manager, $id, $args = array() ) {
		$args['type'] = 'radio_image';
		parent::__construct( $manager, $id, $args );
	}

	protected function render_content() {
		if ( empty( $this->choices ) )
			return;

		printf( '<span class="customize-control-title">%s</span>', esc_html( $this->label ) );
		
		foreach ( $this->choices as $option ) {
			printf(
				'<div class="radio-image-item">
					<input id="atmb-id-%3$s-%2$s" type="radio" class="atmb-field-radio-image" value="%2$s" name="%3$s" %6$s data-image="%5$s"%4$s />
					<label for="atmb-id-%3$s-%2$s">%1$s</label>
				</div>',
				$option['title'],
				$option['id'],
				'_customize-radio-' . $this->id,
				checked( $option['id'], $this->value(), false ),
				$option['image'],
				$this->get_link()
			);
		}
	}


}
