<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Checkbox extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		if ( empty( $field['value'] ) )
			$field['value'] = '0';
		?>
		<label>
			<input type="checkbox" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" value="<?php echo $field['value']; ?>"<?php checked( $field['value'], get_option( $field['id'], $field['std'] ) ); ?> />
			<?php if ( ! empty( $field['sub_desc'] ) ) echo $field['sub_desc']; ?>
		</label>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif; ?>
	<?php
	}

	public function __construct() {
		
	}

}
