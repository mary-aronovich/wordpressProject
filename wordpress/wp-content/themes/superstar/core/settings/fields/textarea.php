<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Textarea extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		?>
		<textarea id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" rows="10" cols="50" class="large-text code"><?php echo get_option( $field['id'], $field['std'] ); ?></textarea>
		<?php if ( ! empty( $field['sub_desc'] ) ) echo $field['sub_desc']; ?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
			<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif; ?>
	<?php
	}

	public function __construct() {
		
	}

}