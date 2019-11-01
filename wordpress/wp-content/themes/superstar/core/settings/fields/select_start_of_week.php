<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Select_start_of_week extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		/** @var $wp_locale WP_Locale */
		global $wp_locale;
		
		$options = array();
		for ( $day_index = 0; $day_index <= 6; $day_index++ ) {
			$options[] = sprintf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $day_index ),
				selected( get_option( $field['id'], $field['std'] ), $day_index, false ),
				$wp_locale->get_weekday( $day_index )
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
