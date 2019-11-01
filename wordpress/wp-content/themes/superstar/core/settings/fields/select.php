<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Select extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		$options = array();
		foreach ( $field['options'] as $option_key => $option_value ) {
			$options[] = sprintf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $option_key ),
				selected( get_option( $field['id'], $field['std'] ), $option_key, false ),
				$option_value
			);
		}
		?>
		<select id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>">
			<?php echo implode( '', $options ); ?>
		</select>
		<?php if ( ! empty( $field['sub_desc'] ) ) echo $field['sub_desc']; ?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif; ?>
	<?php
	}

	public function __construct() {
		
	}

}
