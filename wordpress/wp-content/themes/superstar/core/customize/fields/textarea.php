<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Textarea extends Pojo_Customize_Control_Field_Base {

	public function __construct( WP_Customize_Manager $manager, $id, $args = array() ) {
		$args['type'] = 'textarea';
		parent::__construct( $manager, $id, $args );
	}

	protected function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<textarea class="large-text" cols="20" rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
		</label>
	<?php
	}

}
