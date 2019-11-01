<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Select_pages extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		wp_dropdown_pages( array(
			'name'              => $field['id'],
			'show_option_none'  => __( '&mdash; Select &mdash;', 'pojo' ),
			'option_none_value' => '0',
			'selected'          => get_option( $field['id'], $field['std'] ),
		) );
		?>
		<?php if ( ! empty( $field['sub_desc'] ) ) echo $field['sub_desc']; ?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
			<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif; ?>
	<?php
	}

}
