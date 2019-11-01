<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Radio_image extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		if ( empty( $field['options'] ) ) {
			return;
		}
		
		foreach ( $field['options'] as $option ) {
			printf(
				'<div class="radio-image-item">
					<input id="atmb-id-%3$s-%2$s" type="radio" class="atmb-field-radio-image" value="%2$s" name="%3$s" data-image="%5$s"%4$s />
					<label for="atmb-id-%3$s-%2$s">%1$s</label>
				</div>',
				$option['title'],
				$option['id'],
				$field['id'],
				checked( $option['id'], get_option( $field['id'], $field['std'] ), false ),
				$option['image']
			);
		}
		?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif; ?>
	<?php
	}

}
