<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Radio extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		if ( empty( $field['options'] ) ) {
			return;
		}
		$options = array();
		foreach ( $field['options'] as $option_key => $option_value ) {
			printf(
				'<label><input type="radio" name="%1$s" id="%1$s" value="%2$s" /> %3$s</label>',
				esc_attr( $option_key ),
				selected( get_option( $field['id'], $field['std'] ), $option_key, false ),
				$option_value
			);
		}
		?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif; ?>
	<?php
	}

	public function __construct() {
		
	}

}
