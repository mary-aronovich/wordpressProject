<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Select_languages extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		$languages = get_available_languages();
		if ( is_multisite() && ! empty( $languages ) ) : ?>
			<select id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>">
				<?php mu_dropdown_languages( $languages, get_option( $field['id'] ) ); ?>
			</select>
			<?php if ( ! empty( $field['sub_desc'] ) ) echo $field['sub_desc']; ?>
			<?php if ( ! empty( $field['desc'] ) ) : ?>
				<p class="description"><?php echo $field['desc']; ?></p>
			<?php endif; ?>
		<?php endif;
	}

	public function __construct() {

	}

}
