<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Background extends Pojo_Customize_Control_Field_Base {
	
	protected $_default = array();
	
	public $type = 'pojo_background';

	public function __construct( WP_Customize_Manager $manager, $id, $args = array() ) {
		$defaults = array(
			'color' => '#ffffff',
			'image'  => '',
			'position'  => 'top center',
			'repeat' => 'repeat',
			'size' => 'auto',
			'attachment' => 'scroll',
			'opacity' => '100',
		);
		
		$this->_default = wp_parse_args( $args['default'], $defaults );
		
		$args['settings'] = array();
		foreach ( $defaults as $key => $value ) {
			$child_id = $id . '[' . $key . ']';
			$manager->add_setting( $child_id, array(
				'default'   => $this->_default[ $key ],
				'transport' => $args['transport'],
			) );

			$args['settings'][ $child_id ] = $child_id;
		}
		unset( $args['default'] );
		parent::__construct( $manager, $id, $args );
	}

	public function enqueue() {
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	protected function render_content() {
		$this_default = $this->_default['color'];
		$default_attr = '';
		if ( $this_default ) {
			if ( false === strpos( $this_default, '#' ) )
				$this_default = '#' . $this_default;
			$default_attr = ' data-default-color="' . esc_attr( $this_default ) . '"';
		}

		$image_default = $this->_default['image'];
		$images_old_value = $this->value( $this->id . '[image]' );
		$images    = array();
		if ( ! empty( $images_old_value ) ) {
			$old_images_ids = explode( ',', $images_old_value );
			foreach ( $old_images_ids as $old_image_id ) {
				$images[] = sprintf(
					'<li class="image" data-attachment_url="%s"><img src="%s" alt="img-preview" /><a href="javascript:void(0);" class="image-delete button">%s</a></li>',
					$old_image_id,
					$old_image_id,
					__( 'Remove', 'pojo' )
				);
			}
		}
		
		$bg_positions = array(
			'top left'      => 'Top Left',
			'top center'    => 'Top Center',
			'top right'     => 'Top Right',
			'center left'   => 'Center Left',
			'center center' => 'Center Center',
			'center right'  => 'Center Right',
			'bottom left'   => 'Bottom Left',
			'bottom center' => 'Bottom Center',
			'bottom right'  => 'Bottom Right',
		);
		
		$bg_repeats = array(
			'no-repeat' => 'No repeat',
			'repeat'    => 'repeat',
			'repeat-x'  => 'repeat-x',
			'repeat-y'  => 'repeat-y',
		);
		
		$bg_sizes = array(
			'auto'  => 'Auto',
			'cover' => 'Cover',
		);

		$bg_attachments = array(
			'scroll' => 'Scroll',
			'fixed' => 'Fixed',
		);
		
		$default_image = $default_image_original = get_template_directory_uri() . '/core/assets/admin-ui/images/empty-image.png';
		$default_color = '#ffffff';
		if ( ! empty( $images_old_value ) ) {
			$default_image = $images_old_value;
		}
		
		if ( ! empty( $this_default ) ) {
			$default_color = $this_default;
		}
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

		<div class="background-option-wrap">

			<div class="customize-control-content">
				<input <?php $this->link( $this->id . '[color]' ); ?> class="typography-field-color" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'pojo' ); ?>"<?php echo $default_attr; ?> />
				
				<a data-default_image="<?php echo $default_image_original; ?>" class="wp-color-result pojo-customize-bg-image pojo-btn-control-toggle-wrap" tabindex="0" title="<?php _e( 'Select Image', 'pojo' ); ?>" data-current="Change"></a>
				
				<div class="customize-control-toggle-wrap hidden">
					<div class="pojo-customizer-upload-image-wrap" data-field_type="background">
						<span class="customize-control-title"><?php _e( 'Choose Image', 'pojo' ); ?></span>
						<div class="choose-background-image">
							<ul class="at-image-ul-wrap attachments"><?php echo implode( '', $images ); ?></ul>
							<input type="hidden" <?php $this->link( $this->id . '[image]' ); ?> value="<?php echo esc_attr( $this->value( $this->id . '[image]' ) ); ?>" class="at-image-upload-field" data-multiple="false" />
							<div class="single-image<?php if ( ! empty( $images ) ) echo ' hidden'; ?>">
								<a href="javascript:void(0);" class="at-image-upload button button-primary"><?php _e( 'Choose Image', 'pojo' ); ?></a>
							</div>
						</div>
					</div>
					<div class="field-columns">
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Background Position', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[position]' ); ?> class="background-field-position">
									<?php foreach ( $bg_positions as $key => $value ) : ?>
										<option value="<?php echo $key; ?>"<?php selected( $this->value( $this->id . '[position]' ), $key ); ?>><?php echo $value; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Background Repeat', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[repeat]' ); ?> class="background-field-repeat">
									<?php foreach ( $bg_repeats as $key => $value ) : ?>
										<option value="<?php echo $key; ?>"<?php selected( $this->value( $this->id . '[repeat]' ), $key ); ?>><?php echo $value; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Background Size', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[size]' ); ?> class="background-field-size">
									<?php foreach ( $bg_sizes as $key => $value ) : ?>
										<option value="<?php echo $key; ?>"<?php selected( $this->value( $this->id . '[size]' ), $key ); ?>><?php echo $value; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Background Attachment', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[attachment]' ); ?> class="background-field-attachment">
									<?php foreach ( $bg_attachments as $key => $value ) : ?>
										<option value="<?php echo $key; ?>"<?php selected( $this->value( $this->id . '[attachment]' ), $key ); ?>><?php echo $value; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
				</div>
				
				<label class="background-color-opacity">
					<span class="customize-control-title"><?php _e( 'Opacity', 'pojo' ); ?></span>
					<input type="number" min="0" max="100" value="<?php echo esc_attr( $this->value( $this->id . '[opacity]' ) ); ?>" <?php $this->link( $this->id . '[opacity]' ); ?> />%
				</label>
			</div>

		</div>
	<?php
	}
	
}
