<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MetaBox_Field_Gallery extends Pojo_MetaBox_Field {

	public function render() {
		$old_value = $this->get_value();
		$images    = array();

		if ( ! empty( $old_value ) ) {
			$old_images_ids = explode( ',', $old_value );
			foreach ( $old_images_ids as $old_image_id ) {
				$images[] = sprintf(
					'<li>%s</li>',
					wp_get_attachment_image( $old_image_id, 'thumbnail' )
				);
			}
		}

		return sprintf(
			'
			<div class="pojo-media-manager">
				<div class="pojo-media-actions">
					<a href="javascript:void(0);" class="pojo-insert-media button button-primary" data-label_add_to_post="%6$s" data-state="gallery-library" data-frame="post" data-class_name="pojo-media-gallery-insert">%2$s</a>
					<a href="javascript:void(0);" class="pojo-empty-media button%8$s">%7$s</a>
				</div>
				<ul class="pojo-media-preview-html">%5$s</ul>
				<input type="hidden" name="%3$s" value="%4$s" class="pojo-media-field" />
			</div>',
			$this->_field['title'],
			__( 'Add/Edit Gallery', 'pojo' ),
			$this->_field['id'],
			esc_attr( $old_value ),
			implode( '', $images ),
			$this->_field['label_add_to_post'],
			__( 'Reset Gallery', 'pojo' ),
			empty( $images ) ? ' hidden' : ''
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
