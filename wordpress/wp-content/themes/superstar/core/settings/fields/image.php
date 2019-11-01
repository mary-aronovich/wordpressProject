<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Field_Image extends Pojo_Settings_Field_Base {

	public function render( $field ) {
		$old_value = get_option( $field['id'], $field['std'] );
		$images    = array();
		$field['img_multiple'] = ! empty( $field['img_multiple'] ) ? $field['img_multiple'] : false;

		if ( ! empty( $old_value ) ) {
			$old_images_ids = explode( ',', $old_value );
			foreach ( $old_images_ids as $old_image_id ) {
				$images[] = sprintf(
					'<li class="image" data-attachment_url="%s"><img src="%s" alt="img-preview" /><a href="javascript:void(0);" class="image-delete button">%s</a></li>',
					$old_image_id,
					$old_image_id,
					__( 'Remove', 'pojo' )
				);
			}
		}

		printf(
			'
			<div class="pojo-setting-upload-image-wrap">
				<div class="atmb-input">
					<ul class="at-image-ul-wrap">%5$s</ul>
					<input type="hidden" name="%2$s" value="%3$s" class="at-image-upload-field" data-multiple="%4$s" />
					<div class="single-image%6$s">
						<a href="javascript:void(0);" class="at-image-upload button button-primary">%1$s</a>
					</div>
				</div>
			</div>',
			__( 'Choose Image', 'pojo' ),
			$field['id'],
			esc_attr( $old_value ),
			$field['img_multiple'] ? 'true' : 'false',
			implode( '', $images ),
			$field['img_multiple'] || empty( $images ) ? '' : ' hidden'
		);

		if ( ! empty( $field['sub_desc'] ) ) echo $field['sub_desc']; ?>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
			<p class="description"><?php echo $field['desc']; ?></p>
		<?php endif;
	}

	public function __construct() {
		
	}

}
