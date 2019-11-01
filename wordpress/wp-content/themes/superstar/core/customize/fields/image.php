<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Image extends Pojo_Customize_Control_Field_Base {

	public $type = 'pojo_image';
	
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	protected function render_content() {
		$old_value = $this->value();
		$images    = array();

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
		
		?>
		<div class="pojo-customizer-upload-image-wrap">
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="at-input">
				<ul class="at-image-ul-wrap"><?php echo implode( '', $images ); ?></ul>
				<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" class="at-image-upload-field" data-multiple="false" />
				<div class="single-image<?php if ( ! empty( $images ) ) echo ' hidden'; ?>">
					<a href="javascript:void(0);" class="at-image-upload button button-primary"><?php _e( 'Choose Image', 'pojo' ); ?></a>
				</div>
			</div>
		</div>
	<?php
	}
}


/*
class Pojo_Customize_Control_Field_Image extends WP_Customize_Image_Control {
	
	protected $selector = '';
	protected $change_type = '';
	
	public function __construct( $manager, $id, $args = array() ) {
		$this->type = 'image';
		//$this->add_tab( 'media_library', __( 'Media Library', 'pojo' ), array( $this, 'media_library' ) );
		
		WP_Customize_Image_Control::__construct( $manager, $id, $args );
	}
	
	public function to_json() {
		$this->json['selector'] = $this->selector;
		$this->json['change_type'] = $this->change_type;
		parent::to_json();
	}

}
*/