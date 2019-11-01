<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Typography extends Pojo_Customize_Control_Field_Base {

	protected $_default = array();

	public function __construct( WP_Customize_Manager $manager, $id, $args = array() ) {
		$args['type'] = 'typography';

		$defaults = array(
			'size'           => '13px',
			'family'         => 'Arial',
			'weight'         => 'normal',
			'color'          => '#ffffff',
			'transform'      => 'none',
			'style'          => 'normal',
			'line_height'    => '1.5em',
			'letter_spacing' => '0px',
		);
		if ( isset( $args['default']['line_height'] ) && false === $args['default']['line_height'] )
			unset( $defaults['line_height'] );

		$this->_default = wp_parse_args( $args['default'], $defaults );
		
		$args['settings'] = array();
		foreach ( $defaults as $key => $value ) {
			$child_id = $id . '[' . $key . ']';
			$manager->add_setting( $child_id, array(
				'default'   => $this->_default[ $key ],
				//'transport' => 'postMessage',
				'transport' => $args['transport'],
			) );

			$args['settings'][ $child_id ] = $child_id;
		}
		unset( $args['default'] );
		parent::__construct( $manager, $id, $args );
	}

	protected function render_content() {
		$this_default = $this->_default['color'];
		$default_attr = '';
		if ( $this_default ) {
			if ( false === strpos( $this_default, '#' ) )
				$this_default = '#' . $this_default;
			$default_attr = ' data-default-color="' . esc_attr( $this_default ) . '"';
		}
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

		<div class="font-option-wrap">

			<div class="customize-control-content">
				<input <?php $this->link( $this->id . '[color]' ); ?> class="typography-field-color" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'pojo' ); ?>"<?php echo $default_attr; ?> />

				<?php /*remove*/ ?>
				<a class="wp-color-result pojo-customize-font-and-size pojo-btn-control-toggle-wrap" tabindex="0" title="<?php _e( 'Font & Size', 'pojo' ); ?>" data-current="Change"></a>
				<?php /*END remove*/ ?>

				<div class="customize-control-toggle-wrap hidden">
					<div class="field-columns">
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Size', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[size]' ); ?> class="typography-field-size">
									<?php foreach ( array_merge( range( 8, 48 ), range( 60, 100, 10 ) ) as $pixel ) : ?>
										<option value="<?php echo $pixel; ?>px"<?php selected( $this->value( $this->id . '[size]' ), $pixel . 'px' ); ?>><?php echo $pixel; ?>px</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Family', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[family]' ); ?> class="typography-field-family">
									<?php foreach ( Pojo_Web_Fonts::get_web_fonts() as $k_family => $v_family ) : ?>
										<option data-font_type="<?php echo $v_family; ?>" value="<?php echo $k_family; ?>"<?php selected( $this->value( $this->id . '[family]' ), $k_family ); ?>><?php echo $k_family; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Weight', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[weight]' ); ?> class="typography-field-weight">
									<?php foreach ( array_merge( array( 'normal', 'bold' ), range( 100, 900, 100 ) ) as $weight ) : ?>
										<option value="<?php echo $weight; ?>"<?php selected( $this->value( $this->id . '[weight]' ), $weight ); ?>><?php echo ucfirst( $weight ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Text Transform', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[transform]' ); ?> class="typography-field-transform">
									<?php foreach ( array( 'none', 'uppercase', 'lowercase', 'capitalize' ) as $transform ) : ?>
										<option value="<?php echo $transform; ?>"<?php selected( $this->value( $this->id . '[transform]' ), $transform ); ?>><?php echo ucfirst( $transform ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Font Style', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<select <?php $this->link( $this->id . '[style]' ); ?> class="typography-field-font-style">
									<?php foreach ( array( 'normal', 'italic', 'oblique' ) as $font_style ) : ?>
										<option value="<?php echo $font_style; ?>"<?php selected( $this->value( $this->id . '[style]' ), $font_style ); ?>><?php echo ucfirst( $font_style ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<?php if ( ! empty( $this->_default['line_height'] ) ) : ?>
							<div class="pojo-row">
								<div class="pojo-column column-6">
									<label><?php _e( 'Line Height', 'pojo' ); ?></label>
								</div>
								<div class="pojo-column column-6">
									<input type="text" <?php $this->link( $this->id . '[line_height]' ); ?> value="<?php echo $this->value( $this->id . '[line_height]' ); ?>" class="typography-field-line-height" />
								</div>
							</div>
						<?php endif; ?>
						<div class="pojo-row">
							<div class="pojo-column column-6">
								<label><?php _e( 'Letter Spacing', 'pojo' ); ?></label>
							</div>
							<div class="pojo-column column-6">
								<input type="text" <?php $this->link( $this->id . '[letter_spacing]' ); ?> value="<?php echo $this->value( $this->id . '[letter_spacing]' ); ?>" class="typography-field-letter-spacing" />
							</div>
						</div>
					</div>

				</div>

			</div>

		</div>
	<?php
	}

}
