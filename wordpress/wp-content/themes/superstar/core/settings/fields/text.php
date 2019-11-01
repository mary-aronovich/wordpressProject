<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Text extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		if ( empty( $field['classes'] ) )
			$field['classes'] = array( 'regular-text' );
		?>
		<input type="text" class="<?php echo implode( ' ', $field['classes'] ); ?>" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" value="<?php echo esc_attr( get_option( $field['id'], $field['std'] ) ); ?>"<?php echo ! empty( $field['placeholder'] ) ? ' placeholder="' . $field['placeholder'] . '"' : ''; ?> />
		<?php if ( ! empty( $field['sub_desc'] ) ) echo $field['sub_desc']; ?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif; ?>
	<?php
	}

	public function __construct() {}

}
