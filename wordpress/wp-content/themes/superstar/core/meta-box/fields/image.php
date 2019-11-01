<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Image extends Pojo_MetaBox_Field {

	public function render() {
		$old_value = $this->get_value();
		$images    = array();

		if ( ! empty( $old_value ) ) {
			$old_images_ids = explode( ',', $old_value );
			foreach ( $old_images_ids as $old_image_id ) {
				$attachment_image = wp_get_attachment_image( $old_image_id, 'thumbnail' );
				if ( ! empty( $attachment_image ) ) {
					$images[] = sprintf(
						'<li class="image" data-attachment_id="%d">%s<a href="javascript:void(0);" class="image-delete button">%s</a></li>',
						$old_image_id,
						wp_get_attachment_image( $old_image_id, 'thumbnail' ),
						__( 'Remove', 'pojo' )
					);
				}
			}
		}

		return sprintf(
			'
			<div class="at-upload-image-wrap">
				<div class="atmb-label">
					<label>%6$s</label>
				</div>
				<div class="atmb-input">
					<ul class="at-image-ul-wrap">%5$s</ul>
					<input type="hidden" name="%2$s" value="%3$s" class="at-image-upload-field" data-multiple="%4$s" />
					<div class="single-image%7$s">
						<a href="javascript:void(0);" class="at-image-upload button button-primary" data-label_add_to_post="%8$s">%1$s</a>
					</div>
				</div>
			</div>',
			__( 'Add image', 'pojo' ),
			$this->_field['id'],
			esc_attr( $old_value ),
			$this->_field['img_multiple'] ? 'true' : 'false',
			implode( '', $images ),
			$this->_field['title'],
			$this->_field['img_multiple'] || empty( $images ) ? '' : ' hidden',
			$this->_field['label_add_to_post']
		);
	}

	public function __construct( $field, $prefix = '' ) {
		$default = array(
			'img_multiple' => false,
			'label_add_to_post' => __( 'Add to Post', 'pojo' ),
		);

		$field = wp_parse_args( $field, $default );

		parent::__construct( $field, $prefix );
	}
}
