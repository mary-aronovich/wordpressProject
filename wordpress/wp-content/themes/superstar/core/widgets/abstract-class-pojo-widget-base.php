<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Pojo_Widget_Base extends WP_Widget {
	
	protected $_form_fields = array();
	protected $_default_field_options = array();
	
	protected function _get_hover_animation_options() {
		return array(
			'' => __( 'None', 'pojo' ),
			'grow' => __( 'Grow', 'pojo' ),
			'shrink' => __( 'Shrink', 'pojo' ),
			'pulse-grow' => __( 'Pulse Grow', 'pojo' ),
			'pulse-shrink' => __( 'Pulse Shrink', 'pojo' ),
			'push' => __( 'Push', 'pojo' ),
			'pop' => __( 'Pop', 'pojo' ),
			'rotate' => __( 'Rotate', 'pojo' ),
			'grow-rotate' => __( 'Grow Rotate', 'pojo' ),
			'float' => __( 'Float', 'pojo' ),
			'sink' => __( 'Sink', 'pojo' ),
			'hover' => __( 'Hover', 'pojo' ),
			'wobble-vertical' => __( 'Wobble Vertical', 'pojo' ),
			'wobble-horizontal' => __( 'Wobble Horizontal', 'pojo' ),
			'buzz' => __( 'Buzz', 'pojo' ),
		);
	}

	protected function _get_font_weights() {
		$font_weights = array( '' => __( 'Default', 'pojo' ) );
		foreach ( array_merge( array( 'normal', 'bold' ), range( 100, 900, 100 ) ) as $weight ) {
			$font_weights[ $weight ] = ucfirst( $weight );
		}
		return $font_weights;
	}

	protected function _parse_widget_args( $args, $instance ) {
		return apply_filters( 'pojo_parse_widget_args', $args, $instance, $this->id_base );
	}

	/**
	 * Helper method to get std value on all items.
	 *
	 * @param bool|array $fields
	 *
	 * @return array default std's
	 */
	protected function _get_default_values( $fields = false ) {
		if ( ! $fields )
			$fields = $this->_form_fields;
		
		$default = array();
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				$default[ $field['id'] ] = $field['std'];
			}
		}
		return $default;
	}
	
	protected function _get_field_by_id( $id ) {
		if ( ! empty( $this->_form_fields ) ) {
			foreach ( $this->_form_fields as $field ) {
				if ( $id === $field['id'] )
					return $field;
			}
		}
		return false;
	}
	
	protected function _setup_repeater_field( $field, $repeater_field, $index = 'REPEATER_ID' ) {
		$field_id = $repeater_field['id'];
		
		if ( version_compare( get_bloginfo( 'version' ), '4.4-RC1', '>=' ) ) {
			$repeater_field['id'] = $field['id'] . '[' . $index . '][' . $field_id . ']';
		} else {
			$repeater_field['id'] = $field['id'] . '][' . $index . '][' . $field_id;
		}
		
		return $repeater_field;
	}
	
	protected function _create_field( $field, $value ) {
		if ( in_array( $field['type'], array( 'text', 'number' ) ) ) : ?>
			<p class="field-text">
				<label for="<?php echo $this->get_field_id( $field['id'] ); ?>"><?php echo $field['title']; ?></label>
				<input class="widefat pb-widget-field-<?php echo esc_attr( $field['id'] ); ?>" id="<?php echo $this->get_field_id( $field['id'] ); ?>" name="<?php echo $this->get_field_name( $field['id'] ); ?>" type="<?php echo esc_attr( $field['type'] ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php if ( ! empty( $field['placeholder'] ) ) echo ' placeholder="' . esc_attr( $field['placeholder'] ) . '"'; ?><?php if ( isset( $field['min'] ) ) echo ' min="' . absint( $field['min'] ) . '"'; ?><?php if ( isset( $field['max'] ) ) echo ' max="' . absint( $field['max'] ) . '"'; ?> />
				<?php if ( ! empty( $field['desc'] ) ) : ?>
				<br /><small><?php echo $field['desc']; ?></small>
				<?php endif; ?>
			</p>
		<?php elseif ( 'textarea' === $field['type'] ) : ?>
			<p class="field-textarea">
				<label for="<?php echo $this->get_field_id( $field['id'] ); ?>"><?php echo $field['title']; ?></label>
				<textarea class="widefat pb-widget-field-<?php echo esc_attr( $field['id'] ); ?>" rows="4" cols="20" id="<?php echo $this->get_field_id( $field['id'] ); ?>" name="<?php echo $this->get_field_name( $field['id'] ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
				<?php if ( ! empty( $field['desc'] ) ) : ?>
					<br /><small><?php echo $field['desc']; ?></small>
				<?php endif; ?>
			</p>
		<?php elseif ( 'checkbox' === $field['type'] ) : ?>
			<p class="field-checkbox">
				<input id="<?php echo $this->get_field_id( $field['id'] ); ?>" class="checkbox" type="checkbox" value="1" name="<?php echo $this->get_field_name( $field['id'] ); ?>"<?php checked( $value, true ); ?> /> <label for="<?php echo $this->get_field_id( $field['id'] ); ?>"><?php echo $field['title']; ?></label>
				<?php if ( ! empty( $field['desc'] ) ) : ?>
					<br /><small><?php echo $field['desc']; ?></small>
				<?php endif; ?>
			</p>
		<?php elseif ( 'wysiwyg' === $field['type'] ) : ?>
			<p class="field-wysiwyg">
				<?php /*<label for="<?php echo $this->get_field_id( $field['id'] ); ?>"><?php echo $field['title']; ?></label>*/ ?>
				<a href="javascript:void(0);" class="btn-pb-open-visual-editor button" data-action="pb_open_visual_editor"><?php _e( 'Open Visual Editor', 'pojo' ); ?></a>
				<textarea class="ppp-test" style="display: none;" rows="16" cols="20" id="<?php echo $this->get_field_id( $field['id'] ); ?>" name="<?php echo $this->get_field_name( $field['id'] ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
			</p>
		<?php elseif ( 'select' === $field['type'] ) : ?>
			<p class="field-select">
				<label for="<?php echo $this->get_field_id( $field['id'] ); ?>"><?php echo $field['title']; ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( $field['id'] ); ?>" name="<?php echo $this->get_field_name( $field['id'] ); ?>">
					<?php foreach ( $field['options'] as $option_key => $option_value ) : ?>
						<option value="<?php echo esc_attr( $option_key ); ?>"<?php selected( $value, $option_key ); ?>><?php echo $option_value; ?></option>
					<?php endforeach; ?>
				</select>
				<?php if ( ! empty( $field['desc'] ) ) : ?>
					<br /><small><?php echo $field['desc']; ?></small>
				<?php endif; ?>
			</p>
		<?php elseif ( 'radio_image' === $field['type'] ) : ?>
			<p class="field-radio-image">
				<label><?php echo $field['title']; ?></label>
				<br />
				<?php foreach ( (array) $field['options'] as $option ) : ?>
				<?php printf(
						'<div class="radio-image-item">
							<input id="%3$s-%2$s" type="radio" class="pb-field-radio-image" value="%2$s" name="%4$s" data-image="%6$s"%5$s />
							<label for="%3$s-%2$s">%1$s</label>
						</div>',
						$option['title'],
						$option['id'],
						$this->get_field_id( $field['id'] ),
						$this->get_field_name( $field['id'] ),
						checked( $option['id'], $value, false ),
						$option['image']
					); ?>
				<?php endforeach; ?>
			</p>
		<?php elseif ( 'image' === $field['type'] ) :
			$old_value = $value;
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
			printf(
				'<p class="field-image">
				<div class="pojo-setting-upload-image-wrap">
					<label>%7$s</label>
					<div class="atmb-input">
						<ul class="at-image-ul-wrap">%5$s</ul>
						<input type="hidden" name="%2$s" value="%3$s" class="at-image-upload-field" data-multiple="%4$s" />
						<div class="single-image%6$s">
							<a href="javascript:void(0);" class="at-image-upload button button-primary">%1$s</a>
						</div>
					</div>
					%8$s
				</div>
				</p>',
				__( 'Choose Image', 'pojo' ),
				$this->get_field_name( $field['id'] ),
				esc_attr( $old_value ),
				'false',
				implode( '', $images ),
				empty( $images ) ? '' : ' hidden',
				$field['title'],
				( ! empty( $field['desc'] ) ) ? '<small>' . $field['desc'] . '</small>' : ''
			); ?>
		<?php elseif ( 'color' === $field['type'] ) :
			$this_default = $value;
			$default_attr = '';
			if ( $this_default ) {
				if ( false === strpos( $this_default, '#' ) )
					$this_default = '#' . $this_default;
				$default_attr = ' data-default-color="' . esc_attr( $this_default ) . '"';
			}
			?>
			<p class="field-color-picker">
				<label for="<?php echo $this->get_field_id( $field['id'] ); ?>"><?php echo $field['title']; ?></label>
					<span>
						<input id="<?php echo $this->get_field_id( $field['id'] ); ?>" name="<?php echo $this->get_field_name( $field['id'] ); ?>" class="pojo-color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'pojo' ); ?>" value="<?php echo $this_default; ?>"<?php echo $default_attr; ?> />
					</span>
			</p>
		<?php elseif ( 'button_collapse' === $field['type'] ) : ?>
			<?php if ( isset( $field['mode'] ) && 'start' === $field['mode'] ) : ?>
				<a href="javascript:void(0);" class="pojo-widget-button-collapse widget-button-collapse button" data-toggle_class="collapse-<?php echo $this->get_field_id( $field['id'] ); ?>"><?php echo $field['title']; ?></a>
				<div class="widget-button-collapse hidden" id="collapse-<?php echo $this->get_field_id( $field['id'] ); ?>">
			<?php else : ?>
				</div>
			<?php endif; ?>
		<?php elseif ( 'multi_checkbox' === $field['type'] ) :
			if ( empty( $value ) || ! is_array( $value ) )
				$value = array();
			?>
			<p class="field-multi-checkbox">
				<label><?php echo $field['title']; ?></label>
				<br />
				<?php if ( $field['options'] ) : ?>
					<?php foreach ( $field['options'] as $option_id => $option_title ) : ?>
						<label><input class="checkbox" type="checkbox" value="<?php echo $option_id; ?>" name="<?php echo $this->get_field_name( $field['id'] ); ?>[]"<?php checked( in_array( $option_id, $value ), true ); ?> /> <?php echo $option_title; ?></label><br />
					<?php endforeach; ?>
				<?php else : ?>
					<?php _e( 'No have any options.', 'pojo' ); ?>
				<?php endif; ?>
			</p>
		<?php elseif ( 'multi_taxonomy' === $field['type'] ) :
			if ( empty( $value ) || ! is_array( $value ) )
				$value = array();
			
			$terms = get_terms( $field['taxonomy'] );
			?>
			<div>
				<label><?php echo $field['title']; ?></label>
				<br />
				<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
				<div class="field-multi-checkbox">
					<?php foreach ( $terms as $term ) : ?>
						<label><input class="checkbox" type="checkbox" value="<?php echo $term->term_id; ?>" name="<?php echo $this->get_field_name( $field['id'] ); ?>[]"<?php checked( in_array( $term->term_id, $value ), true ); ?> /> <?php echo $term->name; ?></label><br />
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php _e( 'No Taxonomies Found', 'pojo' ); ?>
			<?php endif; ?>
			</div>
		<?php elseif ( 'label' === $field['type'] ) : ?>
			<p><?php echo $field['title']; ?></p>
		<?php elseif ( 'heading' === $field['type'] ) : ?>
			<h4><?php echo $field['title']; ?></h4>
		<?php elseif ( 'repeater' === $field['type'] ) : ?>
			<div class="field-repeater">
				<div class="field-repeater-clone hidden">
					<div class="field-row-actions field-handle">
						<div class="number-row">
							<?php _e( 'Item', 'pojo' ); ?> <span class="number-row-span">#</span>
						</div>
						<div class="item-actions">
							<a href="javascript:void(0);" class="btn-edit-current-row"><?php _e( 'Edit', 'pojo' ); ?></a>
							<span>|</span>
							<a href="javascript:void(0);" class="btn-remove-current-row"><?php _e( 'Delete', 'pojo' ); ?></a>
						</div>
					</div>
					<div class="field-row-content">
					<?php foreach ( $field['fields'] as $repeater_field ) :
						$repeater_field = wp_parse_args( $repeater_field, $this->_default_field_options );
						$repeater_field = $this->_setup_repeater_field( $field, $repeater_field );
						?>
						<?php $this->_create_field( $repeater_field, '' ); ?>
					<?php endforeach; ?>
					</div>
				</div>
				<div class="field-repeater-list">
				<?php if ( ! empty( $value ) ) foreach ( $value as $v_key => $v_value ) :
					$v_value = wp_parse_args( $v_value, $this->_get_default_values( $field['fields'] ) );
					?>
					<div class="field-repeater-row">
						<div class="field-row-actions field-handle">
							<div class="number-row">
								<?php _e( 'Item', 'pojo' ); ?> <span class="number-row-span">#</span>
							</div>
							<div class="item-actions">
								<a href="javascript:void(0);" class="btn-edit-current-row"><?php _e( 'Edit', 'pojo' ); ?></a>
								<span>|</span>
								<a href="javascript:void(0);" class="btn-remove-current-row"><?php _e( 'Delete', 'pojo' ); ?></a>
							</div>
						</div>
						<div class="field-row-content">
							<?php foreach ( $field['fields'] as $repeater_field ) :
								$repeater_field = wp_parse_args( $repeater_field, $this->_default_field_options );
								$field_id = $repeater_field['id'];
								$repeater_field = $this->_setup_repeater_field( $field, $repeater_field, $v_key );
								$this->_create_field( $repeater_field, $v_value[ $field_id ] );
							endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
				</div>
				<a href="javascript:void(0);" class="button btn-add-row"><?php _e( '+ Add Item', 'pojo' ); ?></a>
			</div>
		<?php endif;
	}

	public function _valid_align( $option ) {
		if ( empty( $option ) )
			$option = 'none';

		if ( ! in_array( $option, array( 'none', 'left', 'right', 'center' ) ) )
			$option = 'none';

		return $option;
	}
	
	public function _valid_number( $option ) {
		$value = (int) $option;
		if ( empty( $value ) )
			$value = 1;
		if ( $value < -1 )
			$value = abs( $value );

		return $value;
	}
	
	public function _valid_checkbox( $option, $field_id = '' ) {
		return ! empty( $option ) && '1' === $option;
	}
	
	public function _valid_bg_opacity( $option, $field_id = '' ) {
		$field = $this->_get_field_by_id( $field_id );
		if ( ! $field )
			return $option;
		
		
		if ( empty( $option ) && '0' !== $option )
			$option = 100;
		
		return absint( $option );
	}
	
	public function _valid_by_options( $text, $field_id = '' ) {
		$field = $this->_get_field_by_id( $field_id );
		if ( ! $field )
			return '';
		
		if ( ! isset( $field['options'][ $text ] ) )
			$text = $field['std'];
		
		return $text;
	}
	
	public function _valid_by_radio_image( $text, $field_id = '' ) {
		$field = $this->_get_field_by_id( $field_id );
		if ( ! $field )
			return '';
		
		foreach ( $field['options'] as $option ) {
			if ( $text === (string) $option['id'] )
				return $text;
		}
		$text = $field['std'];
		
		return $text;
	}

	public function _filter_wysiwyg( $text ) {
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$text = wp_filter_post_kses( $text );
		}
		$text = normalize_whitespace( $text );
		return $text;
	}

	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		$this->_default_field_options = array(
			'id' => '',
			'title' => '',
			'type' => 'text',
			'std' => '',
			'filter' => '',
			'options' => array(),
		);

		$this->_form_fields = apply_filters( 'pojo_init_widget_fields', $this->_form_fields, $id_base, $this );
		$this->_form_fields = apply_filters( 'pojo_init_widget_fields-' . $id_base, $this->_form_fields, $this );
		
		if ( ! empty( $this->_form_fields ) ) {
			foreach ( $this->_form_fields as &$field ) {
				$field = wp_parse_args( $field, $this->_default_field_options );
				if ( 'repeater' === $field['type'] ) {
					foreach ( $field['fields'] as &$r_field ) {
						$r_field = wp_parse_args( $r_field, $this->_default_field_options );
					}
				}
			}
		}
		
		parent::__construct( $id_base, $name, $widget_options, $control_options );
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		return $this->_update_field( $this->_form_fields, $new_instance, $old_instance ); 
	}
	
	protected function _update_field( $fields, $new_instance, $old_instance ) {
		$instance = $old_instance;
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				if ( 'repeater' === $field['type'] ) {
					$instance[ $field['id'] ] = array();
					if ( ! empty( $new_instance[ $field['id'] ] ) ) {
						foreach ( $new_instance[ $field['id'] ] as $uniqid => $temp_instance ) {
							if ( 'REPEATER_ID' === $uniqid )
								continue;
							$instance[ $field['id'] ][] = $this->_update_field( $field['fields'], $temp_instance, array() );
						}
					}
				} else {
					if ( ! isset( $new_instance[ $field['id'] ] ) )
						$new_instance[ $field['id'] ] = '';

					$instance[ $field['id'] ] = $new_instance[ $field['id'] ];
					if ( ! empty( $field['filter'] ) ) {
						// Params: $string, $field_id
						$instance[ $field['id'] ] = call_user_func( $field['filter'], $instance[ $field['id'] ], $field['id'] );
					}
				}
			}
		}
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		if ( empty( $this->_form_fields ) )
			return;
		
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		foreach ( $this->_form_fields as $field ) {
			$this->_create_field( $field, $instance[ $field['id'] ] );
		}
	}

}