<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Checkbox_list extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		$old_value = get_option( $field['id'], $field['std'] );
		if ( ! is_array( $old_value ) )
			$old_value = array();
		
		foreach ( $field['options'] as $option_key => $option_value ) : ?>
		<label>
			<input type="checkbox" name="<?php echo $field['id']; ?>[]" value="<?php echo $option_key; ?>"<?php checked( in_array( $option_key, $old_value ), true ); ?> />
			<?php echo $option_value; ?>
		</label><br />
		<?php endforeach; ?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif; ?>
	<?php
	}

	public function __construct() {
		
	}

}
