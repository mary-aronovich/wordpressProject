<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Page_Builder {
	
	const DB_VER = '1.1';
	
	const MAX_WIDGET_COLUMNS = 12;

	/**
	 * @var WP_Widget[]
	 */
	public $_widgets = array();
	
	public $_row_fields = array();
	
	protected $_widget_categories = array();

	/**
	 * @var Pojo_Page_Builder_Updates
	 */
	public $updates;

	/**
	 * @var Pojo_Builder_Templates
	 */
	public $templates;

	/**
	 * @param $widget
	 * 
	 * @return WP_Widget
	 */
	protected function _get_widget_object( $widget ) {
		global $wp_widget_factory;
		
		if ( isset( $wp_widget_factory->widgets[ $widget ] ) ) {
			$widget_obj = $wp_widget_factory->widgets[ $widget ];
			if ( is_a( $widget_obj, 'WP_Widget' ) ) {
				$widget_obj->_set( 'REPLACE_TO_ID' );
				return $widget_obj;
			}
		}

		// Maybe private widget?
		if ( class_exists( $widget ) ) {
			$widget_obj = new $widget;
			if ( is_a( $widget_obj, 'WP_Widget' ) ) {
				$widget_obj->_set( 'REPLACE_TO_ID' );
				return $widget_obj;
			}
		}
		
		return false;
	}

	protected function _get_row_default_values() {
		$default = array();
		if ( ! empty( $this->_row_fields ) ) {
			foreach ( $this->_row_fields as $field ) {
				if ( isset( $field['std'] ) )
					$default[ $field['id'] ] = $field['std'];
			}
		}
		return $default;
	}

	protected function _get_row_field_by_id( $id ) {
		if ( ! empty( $this->_row_fields ) ) {
			foreach ( $this->_row_fields as $field ) {
				if ( $id === $field['id'] )
					return $field;
			}
		}
		return false;
	}
	
	public function is_builder_active() {
		return 'disable' !== get_option( 'pojo_builder_enable' );
	}
	
	public function _sanitize_by_options( $text, $field_id = '' ) {
		$field = $this->_get_row_field_by_id( $field_id );
		if ( ! $field )
			return '';

		if ( ! isset( $field['options'][ $text ] ) )
			$text = $field['std'];

		return $text;
	}
	
	public function _sanitize_floatval( $text, $field_id = '' ) {
		return (float) $text;
	}
	
	public function init() {
		$builder_widgets = apply_filters(
			'pojo_builder_widgets',
			array(
				'Pojo_Widget_Title',
				'Pojo_Widget_Wysiwyg',
				'Pojo_Widget_Image',
				'Pojo_Widget_Image_Text',
				'Pojo_Widget_Divider',
				'Pojo_Widget_Button',
				'Pojo_Widget_Recent_Posts',
				'Pojo_Widget_Embed_Video',
				'Pojo_Widget_Google_Maps',
				'Pojo_Widget_Like_Box',
				'Pojo_Widget_Tabs',
				'Pojo_Widget_Catalog',
				'Pojo_Widget_Testimonials',
				'Pojo_Widget_Social_Links',
				'Pojo_Widget_Opening_Hours',
				'Pojo_Widget_Animated_Numbers',
				'Pojo_Widget_Menu_Anchor',
				'Pojo_Widget_Sidebar',

				// RevSlider
				'Pojo_Widget_Rev_Slider',
				
				// Posts Group
				'Pojo_Widget_Posts_Group',
			)
		);

		$wp_widgets = apply_filters(
			'pojo_builder_wp_widgets',
			array(
				'WP_Widget_Text',
				'WP_Nav_Menu_Widget',
				'WP_Widget_Search',
				'WP_Widget_Calendar',
				'WP_Widget_Categories',
				'WP_Widget_Recent_Posts',
				'WP_Widget_Recent_Comments',
				'WP_Widget_Tag_Cloud',
				'WP_Widget_Pages',
				'WP_Widget_Archives',
				'WP_Widget_Meta',
				'WP_Widget_RSS',
			)
		);

		// Since WP 4.8 Widget Text use some custom JS
		// so we just restore to old version of this widget
		if ( version_compare( $GLOBALS['wp_version'], '4.8', '>=' ) ) {
			require( 'widgets/class-pojo-wp-widget-text.php' );

			unregister_widget( 'WP_Widget_Text' );
			register_widget( 'Pojo_WP_Widget_Text' );

			$wp_widgets[] = 'Pojo_WP_Widget_Text';
			$wp_widgets = array_diff( $wp_widgets, array( 'WP_Widget_Text' ) );
		}
		
		$this->_widget_categories = array(
			'builder' => array(
				'title' => __( 'Builder Widgets', 'pojo' ),
				'widgets' => array(),
			),
			'wp_widgets' => array(
				'title' => __( 'WordPress Widgets', 'pojo' ),
				'widgets' => array(),
			),
		);
		
		foreach ( $builder_widgets as $widget_id ) {
			$block_widget = $this->_get_widget_object( $widget_id );
			if  ( false === $block_widget )
				continue;
			
			$this->_widgets[ $block_widget->id_base ] = $block_widget;
			$this->_widget_categories['builder']['widgets'][] = $block_widget->id_base;
		}
		
		foreach ( $wp_widgets as $widget_id ) {
			$block_widget = $this->_get_widget_object( $widget_id );
			if  ( false === $block_widget )
				continue;

			$this->_widgets[ $block_widget->id_base ] = $block_widget;
			$this->_widget_categories['wp_widgets']['widgets'][] = $block_widget->id_base;
		}

		// Row Fields
		$this->_row_fields = array();
		
		$this->_row_fields[] = array(
			'id' => 'width_content',
			'title' => __( 'Width Content', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Boxed', 'pojo' ),
				'100_width' => __( '100% Width', 'pojo' )
			),
			'std' => '',
		);

		$this->_row_fields[] = array(
			'id' => 'text_color',
			'title' => __( 'Text Color', 'pojo' ),
			'type' => 'text',
			'std' => '',
			'placeholder' => '#000000',
			'filter' => 'pojo_sanitize_hex_color',
		);

		$this->_row_fields[] = array(
			'id' => 'background_color',
			'title' => __( 'Background Color', 'pojo' ),
			'type' => 'text',
			'std' => '',
			'placeholder' => '#FFFFFF',
			'filter' => 'pojo_sanitize_hex_color',
		);

		$this->_row_fields[] = array(
			'id' => 'background_image',
			'title' => __( 'Background Image', 'pojo' ),
			'type' => 'image',
			'std' => '',
			'filter' => 'esc_url_raw',
		);

		$this->_row_fields[] = array(
			'id' => 'background_repeat',
			'title' => __( 'Background Repeat', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'no-repeat' => __( 'No repeat', 'pojo' ),
				'repeat' => __( 'repeat', 'pojo' ),
				'repeat-x' => __( 'repeat-x', 'pojo' ),
				'repeat-y' => __( 'repeat-y', 'pojo' ),
			),
			'std' => '',
		);

		$this->_row_fields[] = array(
			'id' => 'background_size',
			'title' => __( 'Background Size', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'auto' => __( 'Auto', 'pojo' ),
				'cover' => __( 'Cover', 'pojo' ),
			),
			'std' => '',
		);

		$this->_row_fields[] = array(
			'id' => 'background_position',
			'title' => __( 'Background Position', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'top left' => __( 'Top Left', 'pojo' ),
				'top center' => __( 'Top Center', 'pojo' ),
				'top right' => __( 'Top Right', 'pojo' ),
				'center left' => __( 'Center Left', 'pojo' ),
				'center center' => __( 'Center Center', 'pojo' ),
				'center right' => __( 'Center Right', 'pojo' ),
				'bottom left' => __( 'Bottom Left', 'pojo' ),
				'bottom center' => __( 'Bottom Center', 'pojo' ),
				'bottom right' => __( 'Bottom Right', 'pojo' ),
			),
			'std' => '',
		);

		$this->_row_fields[] = array(
			'id' => 'background_attachment',
			'title' => __( 'Background Attachment', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'scroll' => __( 'Scroll', 'pojo' ),
				'fixed' => __( 'Fixed', 'pojo' ),
			),
			'std' => '',
		);
		
		$this->_row_fields[] = array(
			'id' => 'section_parallax',
			'title' => __( 'Parallax Effect', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'None', 'pojo' ),
				'scroll_up' => __( 'Scroll Up', 'pojo' ),
				'scroll_down' => __( 'Scroll Down', 'pojo' ),
			),
			'std' => '',
			'desc' => __( 'For best results we recommend using an image with a ratio of 1:1 (e.g. 1000x1000 px).', 'pojo' ),
		);
		
		$this->_row_fields[] = array(
			'id' => 'section_parallax_speed',
			'title' => __( 'Parallax Speed', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
			),
			'std' => '5',
		);

		$this->_row_fields[] = array(
			'id' => 'background_video',
			'title' => __( 'Background Video', 'pojo' ),
			'type' => 'text',
			'placeholder' => 'https://www.youtube.com/watch?v=gyBfsmzbG1E',
			'std' => '',
			'desc' => __( 'Enter the URL of your YouTube video', 'pojo' ),
		);

		$this->_row_fields[] = array(
			'id' => 'background_video_mobile',
			'title' => __( 'Hide video on Mobile?', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Hide', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
			),
			'std' => '',
			'desc' => __( 'You can choose to hide the video entirely on Mobile devices and instead display the Section Background image', 'pojo' ),
		);

		$this->_row_fields[] = array(
			'id' => 'padding_top',
			'title' => __( 'Padding Top', 'pojo' ),
			'type' => 'text',
			'placeholder' => '5px',
			'std' => '',
		);

		$this->_row_fields[] = array(
			'id' => 'padding_bottom',
			'title' => __( 'Padding Bottom', 'pojo' ),
			'type' => 'text',
			'placeholder' => '5px',
			'std' => '',
		);

		$this->_row_fields[] = array(
			'id' => 'visible',
			'title' => __( 'Section Visibility', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Show on all devices', 'pojo' ),
				'visible-desktop' => __( 'Visible Desktop', 'pojo' ),
				'visible-tablet' => __( 'Visible Tablet', 'pojo' ),
				'visible-phone' => __( 'Visible Phone', 'pojo' ),
				'hidden-desktop' => __( 'Hidden Desktop', 'pojo' ),
				'hidden-tablet' => __( 'Hidden Tablet', 'pojo' ),
				'hidden-phone' => __( 'Hidden Phone', 'pojo' ),
				'user-logged' => __( 'Logged-in users', 'pojo' ),
				'disable-row' => __( 'Disable Row', 'pojo' ),
			),
			'std' => '',
		);

		$this->_row_fields[] = array(
			'id' => 'css_classes',
			'title' => __( 'CSS Classes', 'pojo' ),
			'type' => 'text',
			'placeholder' => __( '(Optional)', 'pojo' ),
			'std' => '',
		);

		$this->_row_fields[] = array(
			'id' => 'css_id',
			'title' => __( 'CSS ID', 'pojo' ),
			'type' => 'text',
			'placeholder' => __( '(Optional)', 'pojo' ),
			'std' => '',
		);

		$this->_row_fields = apply_filters( 'pojo_builder_register_row_fields', $this->_row_fields );
	}
	
	public function save_builder_rows( $post_id, $rows = array() ) {
		if ( empty( $rows ) ) {
			delete_post_meta( $post_id, 'pb_builder' );
			return;
		}
		
		update_post_meta( $post_id, 'pb_builder', $rows );
	}

	public function get_builder_rows( $post_id ) {
		return get_post_meta( $post_id, 'pb_builder', true );
	}
	
	public function hook_save_post( $post_id ) {
		if ( ! isset( $_POST['_pojo_builder_nonce'] ) || ! wp_verify_nonce( $_POST['_pojo_builder_nonce'], basename( __FILE__ ) ) )
			return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Exit when you don't have $_POST array.
		if ( empty( $_POST ) )
			return;
		
		// Make sure JS is turn on
		if ( ! isset( $_POST['_pojo_builder_ready'] ) || 'true' !== $_POST['_pojo_builder_ready'] )
			return;
		
		$widget_rows = $this->get_saved_widgets( $_POST );
		$this->save_builder_rows( $post_id, $widget_rows );

		if ( 'page-builder' !== atmb_get_field( 'pf_id', $post_id ) )
			return;
		
		remove_action( 'save_post', array( &$this, 'hook_save_post' ), 55 );
		wp_update_post(
			array(
				'ID' => $post_id,
				'post_content' => $this->get_render_plain_text( $post_id ),
			)
		);
		add_action( 'save_post', array( &$this, 'hook_save_post' ), 55 );
	}
	
	public function get_render_plain_text( $post_id ) {
		$plain_text = '';
		
		$widget_rows = $this->get_builder_rows( $post_id );
		if ( empty( $widget_rows ) )
			return $plain_text;

		$empty_widget_args = apply_filters(
			'pb_empty_plain_text_widget_args',
			array(
				'before_widget' => '',
				'after_widget' => '',
				'before_title' => '<h5>',
				'after_title' => '</h5>',
			)
		);
		
		$display_widgets = apply_filters(
			'pb_display_widget_in_plain_text_mode',
			array(
				'pojo_title',
				'pojo_wysiwyg',
				'pojo_divider',
				'pojo_menu_anchor',
				'pojo_rev_slider',
				'pojo_button',
				'pojo_catalog',
				'pojo_embed_video',
				'pojo_google_maps',
				'pojo_image',
				'pojo_image_text',
				'pojo_tabs',
				'pojo_testimonials',
				'text',
			)
		);
		
		ob_start();
		foreach ( $widget_rows as $widget_row_key => $widget_row ) {
			foreach ( $widget_row['columns'] as $column ) {
				foreach ( $column['widgets'] as $widget ) {
					if ( empty( $this->_widgets[ $widget['id_base'] ] ) )
						continue;
					
					if ( ! in_array( $widget['id_base'], $display_widgets ) )
						continue;

					$widget_obj  = $this->_widgets[ $widget['id_base'] ];
					if ( method_exists( $widget_obj, 'widget_plain_text' ) )
						$widget_obj->widget_plain_text( $empty_widget_args, $widget['widget_args'] );
					else
						$widget_obj->widget( $empty_widget_args, $widget['widget_args'] );
				}
			}
		}
		$plain_text = ob_get_clean();

		// Remove unnecessary tags.
		$plain_text = preg_replace( '/<\/?div[^>]*\>/i', '', $plain_text );
		$plain_text = preg_replace( '/<\/?span[^>]*\>/i', '', $plain_text );
		$plain_text = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $plain_text );
		$plain_text = preg_replace( '/<i [^>]*><\\/i[^>]*>/', '', $plain_text );
		$plain_text = preg_replace( '/ class=".*?"/', '', $plain_text );

		// Remove empty lines.
		$plain_text = preg_replace( '/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/', "\n", $plain_text );
		
		return $plain_text;
	}

	public function get_saved_widgets( $posted ) {
		$widget_rows = $columns = $_preview_widgets = array();
		
		if ( ! empty( $posted['pb_input_widget_content'] ) ) {
			foreach ( $posted['pb_input_widget_content'] as $widget_content ) {
				if ( empty( $widget_content ) )
					continue;

				wp_parse_str( $widget_content, $widget_array );
				
				if ( ! isset( $widget_array['pb_widget_id'][0] ) )
					continue;

				$widget_id = $widget_array['pb_widget_id'][0];
				$widget_options = $widget_array['pb_widget_options'][ $widget_id ];
				if ( empty( $widget_options ) )
					continue;
				
				list( $size, $parent, $id_base ) = explode( ';', $widget_options );

				if ( ! isset( $this->_widgets[ $id_base ] ) )
					continue;

				$widget_obj = $this->_widgets[ $id_base ];

				// New row.
				if ( empty( $_preview_widgets[ $parent ] ) )
					$_preview_widgets[ $parent ] = array();

				$new_widget_args = $widget_array[ 'widget-' . $id_base ][ $widget_id ];
				$widget_args = array(
					'id' => $widget_id,
					'id_base' => $id_base,
					'size' => $size,
					'parent' => $parent,
					'widget_args' => apply_filters( 'pb_widget_update_callback', $widget_obj->update( $new_widget_args, array() ), $new_widget_args ),
				);
				$_preview_widgets[ $parent ][] = $widget_args;
			}
		}
		
		if ( ! empty( $posted['pb_row_id'] ) ) {
			foreach ( $posted['pb_row_id'] as $row_id ) {
				if ( 'ROW_REPLACE_TO_ID' === $row_id )
					continue;
				
				if ( ! isset( $posted['pb_row_column'][ $row_id ] ) )
					continue;
				
				$columns = array();
				foreach ( $posted['pb_row_column'][ $row_id ] as $column_id ) {
					if ( 'COLUMN_REPLACE_TO_ID' === $column_id )
						continue;
					
					$widgets = array();
					if ( isset( $_preview_widgets[ $column_id ] ) )
						$widgets = $_preview_widgets[ $column_id ];
					
					$column_size = isset( $posted['pb_row_column_field'][ $column_id ]['size'] ) ? $posted['pb_row_column_field'][ $column_id ]['size'] : 12;

					$columns[] = array(
						'id' => $column_id,
						'size' => $column_size,
						'widgets' => $widgets,
					);
				}

				// Row fields.
				$row_fields = array();
				foreach ( $this->_row_fields as $field ) {
					$row_fields[ $field['id'] ] = '';
					if ( isset( $posted['pb_row_field'][ $row_id ][ $field['id'] ] ) ) {
						$row_fields[ $field['id'] ] = $posted['pb_row_field'][ $row_id ][ $field['id'] ];
						if ( empty( $field['filter'] ) && 'select' === $field['type'] )
							$field['filter'] = array( &$this, '_sanitize_by_options' );

						if ( ! empty( $field['filter'] ) ) {
							// Params: $string, $field_id
							$row_fields[ $field['id'] ] = call_user_func( $field['filter'], $row_fields[ $field['id'] ], $field['id'] );
						}
					}
				}

				// New row.
				$widget_rows[] = array(
					'id' => $row_id,
					'row_fields' => $row_fields,
					'columns' => $columns,
				);
			}
		}
		
		return $widget_rows;
	}
	
	public function _print_row( $widget_row = false ) {
		$widget_row_id = empty( $widget_row ) ? 'ROW_REPLACE_TO_ID' : $widget_row['id'];
		?>
		<div class="pb-row clearfix <?php echo empty( $widget_row ) ? 'pb-row-clone' : 'pb-active-row'; ?>">
			<input type="hidden" name="pb_row_id[]" value="<?php echo $widget_row_id; ?>" class="pd-row-id" />
			<div class="pb-row-tools pb-row-sortable-handle">
				
			</div>

			<div class="pb-advanced-columns clearfix">
				<?php
				$this->_print_column( $widget_row_id );
				
				if ( ! empty( $widget_row['columns'] ) ) :
					foreach ( $widget_row['columns'] as $column ) :
						$this->_print_column( $widget_row_id, $column['id'], $column['size'], $column['widgets'] );
					endforeach;
				endif;
				?>
			</div>
			
			<div class="pb-row-setting-toggle">
				<div class="pb-row-setting-left">
					<a href="#" class="pb-add-column"><?php _e( '+ Add Column', 'pojo' ); ?></a>
					<span class="pb-separator">|</span>
					<a href="javascript:void(0);" class="pb-btn-row-setting-toggle"><?php _e( 'Section Settings', 'pojo' ); ?></a>
				</div>
				<div class="pb-row-setting-right">
					<a href="javascript:void(0);" class="pb-btn-row-duplicate"><?php _e( 'Duplicate', 'pojo' ); ?></a>
					<span class="pb-separator">|</span>
					<a href="javascript:void(0);" class="pb-btn-delete-row"><?php _e( 'Remove', 'pojo' ); ?></a>
				</div>
			</div>
			
			<div class="pb-row-setting-content">
				<?php foreach( $this->_row_fields as $row_field ) :
					$field_value = isset( $widget_row['row_fields'][ $row_field['id'] ] ) ? $widget_row['row_fields'][ $row_field['id'] ] : $row_field['std'];
					$field_name  = 'pb_row_field[' . $widget_row_id . '][' . $row_field['id'] . ']';
					?>
					<div class="pb-row-setting-line">
						<?php if ( in_array( $row_field['type'], array( 'text', 'url', 'number' ) ) ) : ?>
							<div class="label">
								<label>
									<?php echo $row_field['title']; ?>:
								</label>
								<?php if ( ! empty( $row_field['desc'] ) ) : ?>
									<p class="description"><?php echo $row_field['desc']; ?></p>
								<?php endif; ?>
							</div>
							<div class="input builder-field-<?php echo esc_attr( $row_field['type'] ); ?>">
								<input type="<?php echo $row_field['type']; ?>" name="<?php echo $field_name; ?>" value="<?php echo esc_attr( $field_value ); ?>"<?php if ( ! empty( $row_field['placeholder'] ) ) echo ' placeholder="' . esc_attr( $row_field['placeholder'] ) . '"'; ?><?php if ( isset( $row_field['min'] ) ) echo ' min="' . $row_field['min'] . '"'; ?><?php if ( isset( $row_field['max'] ) ) echo ' max="' . $row_field['max'] . '"'; ?> />
							</div>
						<?php elseif ( 'select' === $row_field['type'] ) : ?>
							<div class="label">
								<label>
									<?php echo $row_field['title']; ?>:
								</label>
								<?php if ( ! empty( $row_field['desc'] ) ) : ?>
									<p class="description"><?php echo $row_field['desc']; ?></p>
								<?php endif; ?>
							</div>
							<div class="input">
								<select name="<?php echo $field_name; ?>">
									<?php foreach ( $row_field['options'] as $option_key => $option_title ) : ?>
										<option value="<?php echo esc_attr( $option_key ); ?>"<?php selected( $option_key, $field_value ); ?>><?php echo $option_title; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php elseif ( 'image' === $row_field['type'] ) :
							$old_value = $field_value;
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
							<label class="label">
								<?php echo $row_field['title']; ?>:
							</label>
							<div class="input">
								<?php printf(
									'<p>
									<div class="pojo-setting-upload-image-wrap">
										<div class="atmb-input">
											<ul class="at-image-ul-wrap">%5$s</ul>
											<input type="hidden" name="%2$s" value="%3$s" class="at-image-upload-field" data-multiple="%4$s" />
											<div class="single-image%6$s">
												<a href="javascript:void(0);" class="at-image-upload button button-primary">%1$s</a>
											</div>
										</div>
									</div>
									</p>',
									__( 'Choose Image', 'pojo' ),
									$field_name,
									esc_attr( $field_value ),
									'false',
									implode( '', $images ),
									empty( $images ) ? '' : ' hidden'
								); ?>
							</div>
						<?php elseif ( 'heading' === $row_field['type'] ) : ?>
							<h3><?php echo $row_field['title']; ?></h3>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
	
	protected function _print_column( $row_parent, $column_id = '', $size = '12', $widgets = array() ) {
		$is_clone_column = false;
		if ( empty( $column_id ) ) {
			$column_id = 'COLUMN_REPLACE_TO_ID';
			$is_clone_column = true;
		}
		?>
		<div class="pb-advanced-column pb-column-<?php echo $size; ?> <?php echo $is_clone_column ? 'pb-advanced-column-clone' : 'pb-active-column'; ?>" data-current_size="<?php echo $size; ?>">
			<input type="hidden" name="pb_row_column[<?php echo $row_parent; ?>][]" value="<?php echo esc_attr( $column_id ); ?>" class="pb-advanced-column-id" />
			<input type="hidden" name="pb_row_column_field[<?php echo esc_attr( $column_id ); ?>][size]" value="<?php echo $size; ?>" class="pb-advanced-column-size" />
			
			<div class="pb-widget-columns">
				<?php
				if ( ! empty( $widgets ) ) :
					foreach ( $widgets as $widget ) :
						$this->_print_field( $widget['id_base'], $widget['id'], $widget['parent'], $widget['size'], $widget['widget_args'] );
					endforeach;
				endif;
				?>
			</div>

			<div class="pb-remove-column-action">
				<a href="#" class="pb-remove-column"><?php _e( 'Remove Column', 'pojo' ); ?></a>
			</div>

			<div class="pb-column-display-width">
				<span><?php echo absint( $size * 100 / self::MAX_WIDGET_COLUMNS ); ?>%</span>
			</div>

			<div class="pb-column-tools">
				<div class="pb-resize">
					<a href="javascript:void(0);" class="pb-column-resize-btn pb-action-more" data-resize_action="add"><div class="dashicons dashicons-plus"></div></a>
					<a href="javascript:void(0);" class="pb-column-resize-btn pb-action-less" data-resize_action="less"><div class="dashicons dashicons-minus"></div></a>
				</div>
			</div>
		</div>
		<?php
	}
	
	protected function _print_field( $id_base, $widget_id = '', $parent = '0', $size = self::MAX_WIDGET_COLUMNS, $widget_args = array() ) {
		if ( empty( $this->_widgets[ $id_base ] ) )
			return;
		
		$is_clone_widget = false;
		if ( empty( $widget_id ) ) {
			$widget_id = 'REPLACE_TO_ID';
			$is_clone_widget = true;
		}

		$widget_obj = $this->_widgets[ $id_base ];
		$widget_obj->_set( $widget_id );
		
		$size = absint( $size );
		if ( 1 > $size )
			$size = self::MAX_WIDGET_COLUMNS;
		
		$panel_classes = array( 'pb-widget', 'pb-column-' . $size );
		if ( $is_clone_widget )
			$panel_classes[] = 'pb-clone-widget';
		
		if ( in_array( $id_base, $this->_widget_categories['builder']['widgets'] ) )
			$panel_classes[] = $widget_obj->id_base;
		else
			$panel_classes[] = 'wp_widget_' . $widget_obj->id_base;
		
		?>
		<div class="<?php echo esc_attr( implode( ' ', $panel_classes ) ); ?>" data-current_size="<?php echo $size; ?>">
			<div class="pb-widget-inside">
				<div class="pb-widget-top">
					<div class="pb-resize">
						<a href="javascript:void(0);" class="pb-resize-btn pb-action-more" data-resize_action="add"><div class="dashicons dashicons-plus"></div></a>
						<a href="javascript:void(0);" class="pb-resize-btn pb-action-less" data-resize_action="less"><div class="dashicons dashicons-minus"></div></a>
					</div>
					<div class="pb-toggle">
						<div class="pb-current-size"><?php echo absint( $size * 100 / self::MAX_WIDGET_COLUMNS ); ?>%</div>
						<a href="javascript:void(0);" class="pb-action-toggle"><div class="dashicons dashicons-admin-generic"></div></a>
					</div>
					<div class="pb-widget-title">
						<h4 class="pb-title-name">
							<?php echo $widget_obj->name; ?>
							<span class="in-widget-title"></span>
						</h4>
						<h4 class="pb-title-tools">
						<a class="pb-widget-control-duplicate" href="javascript:void(0);"><?php _e( 'Duplicate', 'pojo' ); ?></a>
						<span class="pb-separator">|</span>
						<a class="pb-widget-control-remove" href="javascript:void(0);"><?php _e( 'Delete', 'pojo' ); ?></a>
						</h4>
					</div>
					<span class="spinner"></span>
				</div>
				<div class="pb-widget-content">
					<?php echo $widget_obj->form( $widget_args ); ?>
					<input type="hidden" name="pb_widget_options[<?php echo esc_attr( $widget_id ); ?>]" class="pb-widget-options" value="<?php echo esc_attr( $size . ';' . $parent . ';' . $widget_obj->id_base ); ?>" data-size="<?php echo esc_attr( $size ); ?>" data-parent="<?php echo esc_attr( $parent ); ?>" data-id_base="<?php echo esc_attr( $id_base ); ?>" />
					<input type="hidden" name="pb_input_widget_content[]" class="pb-input-widget-content" value="" />
					
					<input type="hidden" name="pb_widget_id[]" value="<?php echo esc_attr( $widget_id ); ?>" class="pb-widget-id" />

					<div class="pb-widget-control-actions">
						<div class="alignright">
							<a class="pb-widget-control-duplicate" href="javascript:void(0);"><?php _e( 'Duplicate', 'pojo' ); ?></a>
							<span class="pb-separator">|</span>
							<a class="pb-widget-control-remove" href="javascript:void(0);"><?php _e( 'Delete', 'pojo' ); ?></a>
							<span class="pb-separator">|</span>
							<a class="pb-widget-control-close" href="javascript:void(0);"><?php _e( 'Close', 'pojo' ); ?></a>
						</div>
						<div class="alignleft">
							<a href="javascript:void(0);" class="widget-button-collapse" data-toggle_class="collapse-<?php echo $widget_obj->get_field_id( 'pb_extra_fields' ); ?>"><?php _e( 'Settings', 'pojo' ); ?></a>
						</div>
						<div style="clear: both;"></div>
						<div class="widget-button-collapse hidden" id="collapse-<?php echo $widget_obj->get_field_id( 'pb_extra_fields' ); ?>">
							<?php do_action( 'pb_after_widget_form', $widget_obj, $widget_args ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	public function print_admin_page_builder_wrap( $format, $post ) {
		$widget_rows = $this->get_builder_rows( $post->ID );

		wp_nonce_field( basename( __FILE__ ), '_pojo_builder_nonce' );
		?>
		<div id="page-builder">
			<div class="postbox">
				<div class="inside">
					<div id="pb-widgets-list" class="clearfix">
						<div id="pb-widgets" class="pojo-widgets-sortables ui-sortable">
							<ul class="pojo-admin-tabs builder-tabs nav-tab-wrapper">
								<?php foreach ( $this->_widget_categories as $category_id => $category ) : ?>
									<li><a class="pojo-tab-link nav-tab" href="#tab-<?php echo esc_attr( $category_id ); ?>"><?php echo $category['title']; ?></a></li>
								<?php endforeach; ?>
							</ul>

							<div class="pojo-admin-tabs-content">
								<?php foreach ( $this->_widget_categories as $category_id => $category ) : ?>
									<div id="tab-<?php echo esc_attr( $category_id ); ?>" class="pojo-admin-tab-panel clearfix">
										<?php foreach ( $category['widgets'] as $widget_id ) : ?>
											<?php $this->_print_field( $widget_id ); ?>
										<?php endforeach; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<div id="pb-rows" class="clearfix">
						<?php $this->_print_row(); ?>
						<?php if ( ! empty( $widget_rows ) ) foreach ( $widget_rows as $widget_row ) : ?>
							<?php $this->_print_row( $widget_row ); ?>
						<?php endforeach; ?>
					</div>
					<div class="pb-rows-actions">
						<a href="javascript:void(0);" class="pb-open-add-row-toggle button button-primary"><?php _e( '+ Add Section', 'pojo' ); ?></a>
						
						<?php $this->templates->print_template_button(); ?>
					</div>

					<div class="pb-add-row-options">
						<div class="add-row-title">
							<span><?php _e( 'Pick a column structure:', 'pojo' ); ?></span>
						</div>
						<a href="#" class="pb-add-row-btn c-100" data-sizes="12"></a>
						<a href="#" class="pb-add-row-btn c-50-50" data-sizes="6,6"></a>
						<a href="#" class="pb-add-row-btn c-33-33-33" data-sizes="4,4,4"></a>
						<a href="#" class="pb-add-row-btn c-25-25-25-25" data-sizes="3,3,3,3"></a>
						<a href="#" class="pb-add-row-btn c-75-25" data-sizes="8,4"></a>
						<a href="#" class="pb-add-row-btn c-25-75" data-sizes="4,8"></a>
						<a href="#" class="pb-add-row-btn c-33-66" data-sizes="3,9"></a>
						<a href="#" class="pb-add-row-btn c-66-33" data-sizes="9,3"></a>
						<a href="#" class="pb-add-row-btn c-50-25-25" data-sizes="6,3,3"></a>
						<a href="#" class="pb-add-row-btn c-25-25-50" data-sizes="3,3,6"></a>
						<a href="#" class="pb-add-row-btn c-25-50-25" data-sizes="3,6,3"></a>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	
	public function pd_print_page_builder_front( $post_id ) {
		if ( post_password_required( $post_id ) ) {
			echo get_the_password_form( $post_id );
			return;
		}
		
		static $_sections_printed_count = 1;
		
		$widget_rows = $this->get_builder_rows( $post_id );
		if ( empty( $widget_rows ) )
			return;
		
		$empty_widget_args = apply_filters(
			'pb_empty_widget_args',
			array(
				'before_widget' => '',
				'after_widget' => '',
				'before_title' => '<h5 class="pb-widget-title"><span>',
				'after_title' => '</span></h5>',
			)
		);
		
		foreach ( $widget_rows as $widget_row_key => $widget_row ) :
			if ( ! isset( $widget_row['row_fields'] ) )
				$widget_row['row_fields'] = array();
			
			$row_fields = wp_parse_args( $widget_row['row_fields'], $this->_get_row_default_values() );
			
			if ( empty( $row_fields['css_id'] ) )
				$row_fields['css_id'] = 'builder-section-' . $_sections_printed_count;
			
			$row_style_inline = $row_css_classes = $row_html_attributes = $bg_video_overlay_style_inline = array();

			$row_css_classes[] = 'section';

			if ( ! empty( $row_fields['css_classes'] ) )
				$row_css_classes[] = $row_fields['css_classes'];

			if ( ! empty( $row_fields['visible'] ) ) {
				if ( 'disable-row' === $row_fields['visible'] )
					continue;
				
				if ( 'user-logged' === $row_fields['visible'] && ! is_user_logged_in() )
					continue;
				
				$row_css_classes[] = 'pojo-' . $row_fields['visible'];
			}
			
			$has_video_background = $is_bg_video_hide_in_mobile = false;
			$youtube_id = '';
			if ( ! empty( $row_fields['background_video'] ) ) {
				$youtube_id = pojo_get_youtube_id_from_url( $row_fields['background_video'] );
				if ( ! empty( $youtube_id ) ) {
					$has_video_background = true;
					$is_bg_video_hide_in_mobile = empty( $row_fields['background_video_mobile'] ) || 'show' !== $row_fields['background_video_mobile'];
				}
			}

			if ( $has_video_background ) {
				$row_css_classes[] = 'has-video-background';
			}
			
			$row_css_classes = array_unique( $row_css_classes );
			
			if ( '' !== pojo_sanitize_hex_color( $row_fields['text_color'] ) )
				$row_style_inline[] = 'color:' . $row_fields['text_color'];
			
			if ( '' !== pojo_sanitize_hex_color( $row_fields['background_color'] ) )
				$row_style_inline[] = 'background-color:' . $row_fields['background_color'];
			
			if ( ! empty( $row_fields['background_image'] ) )
				$row_style_inline[] = 'background-image:url("' . $row_fields['background_image'] . '")';
			
			if ( empty( $row_fields['section_parallax'] ) ) {
				if ( ! empty( $row_fields['background_repeat'] ) )
					$row_style_inline[] = 'background-repeat:' . $row_fields['background_repeat'];

				if ( ! empty( $row_fields['background_size'] ) )
					$row_style_inline[] = 'background-size:' . $row_fields['background_size'];

				if ( ! empty( $row_fields['background_position'] ) )
					$row_style_inline[] = 'background-position:' . $row_fields['background_position'];

				if ( ! empty( $row_fields['background_attachment'] ) )
					$row_style_inline[] = 'background-attachment:' . $row_fields['background_attachment'];
			} elseif ( in_array( $row_fields['section_parallax'], array( 'scroll_up', 'scroll_down' ) ) ) {
				$row_style_inline[] = 'background-repeat: no-repeat; background-size: cover; background-position: top center; background-attachment: fixed';
				
				if ( empty( $row_fields['section_parallax_speed'] ) )
					$row_fields['section_parallax_speed'] = 5;
				
				$parallax_speed = absint( $row_fields['section_parallax_speed'] ) * 10;
				
				if ( 'scroll_up' === $row_fields['section_parallax'] ) {
					$row_html_attributes[] = 'data-top-bottom="background-position: 50% ' . $parallax_speed . '%;"';
					$row_html_attributes[] = 'data-bottom-top="background-position: 50% 0%;"';
				} elseif ( 'scroll_down' === $row_fields['section_parallax'] ) {
					$row_html_attributes[] = 'data-top-bottom="background-position: 50% 0%;"';
					$row_html_attributes[] = 'data-bottom-top="background-position: 50% ' . $parallax_speed . '%;"';
				}
			}
			
			foreach ( array( 'top', 'bottom' ) as $dir ) {
				if ( ! empty( $row_fields[ 'padding_' . $dir ] ) ) {
					$style_inline = 'padding-' . $dir . ':' . $row_fields[ 'padding_' . $dir ];
					if ( ! $has_video_background )
						$row_style_inline[] = $style_inline;
					else
						$bg_video_overlay_style_inline[] = $style_inline;
				}
			}
			?>
			<section id="<?php echo esc_attr( $row_fields['css_id'] ); ?>" class="<?php if ( ! empty( $row_css_classes ) ) echo esc_attr( implode( ' ', $row_css_classes ) ); ?>"<?php if ( ! empty( $row_style_inline ) ) echo ' style="' . esc_attr( implode( ';', $row_style_inline ) ) . '"'; ?><?php if ( ! empty( $row_html_attributes ) ) echo ' ' . implode( ' ', $row_html_attributes ); ?> data-anchor-target="#<?php echo esc_attr( $row_fields['css_id'] ); ?>">

				<?php if ( $has_video_background ) : ?>
				<div class="custom-embed custom-video-background">
					<div height="1080" width="1920" class="pb-youtube-frame" id="youtube-player-<?php echo ( 1 + $widget_row_key ); ?>" data-autoplay="0" data-videoid="<?php echo $youtube_id; ?>" data-hd="1" data-vq="hd1080" data-rel="0" data-wmode="opaque" data-loop="0" data-version="3" data-autohide="1" data-color="white" data-controls="0" data-showinfo="0" data-iv_load_policy="3" data-mobile_mode="<?php echo $is_bg_video_hide_in_mobile ? 'hide' : 'show'; ?>"></div>
				</div>
				<div class="overlay-video-background"<?php if ( ! empty( $bg_video_overlay_style_inline ) ) echo ' style="' . esc_attr( implode( ';', $bg_video_overlay_style_inline ) ) . '"'; ?>>
				<?php endif; ?>
					
					<div class="<?php echo ! empty( $row_fields['width_content'] ) && '100_width' === $row_fields['width_content'] ? 'container-section' : 'container'; ?>">
						<div class="columns advanced-columns">
							<?php foreach ( $widget_row['columns'] as $column ) :
								$css_classes = array( 'column-' . absint( $column['size'] ), 'advanced-column' );
								?>
								<div class="<?php echo implode( ' ', $css_classes ); ?>">
									<div class="columns widget-columns">
										<?php foreach ( $column['widgets'] as $widget ) :
											if ( empty( $this->_widgets[ $widget['id_base'] ] ) )
												continue;

											$widget_obj  = $this->_widgets[ $widget['id_base'] ];
											$css_classes = array( 'column-' . absint( $widget['size'] ), 'widget-column' );
											if ( ! empty( $widget['widget_args']['pb_css_classes'] ) )
												$css_classes[] = esc_attr( $widget['widget_args']['pb_css_classes'] );

											$css_classes = apply_filters( 'pb_widget_css_classes', $css_classes, $widget );
											$widget_attributes = apply_filters(
												'pb_widget_attributes',
												array(
													'class' => implode( ' ', $css_classes ),
												),
												$widget
											);

											$widget_attributes_string = '';
											foreach ( $widget_attributes as $attr => $value ) {
												$widget_attributes_string .= ' ' . $attr . '="' . $value . '"';
											}

											$empty_widget_args['widget_id'] = $widget['id_base'] . '-' . $widget['id'];
											?>
											<div<?php echo $widget_attributes_string; ?>>
												<div class="pb-widget-inner">
													<?php $widget_obj->widget( $empty_widget_args, $widget['widget_args'] ); ?>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php if ( $has_video_background ) : ?>
				</div><!-- /.overlay-video-background -->
				<?php endif; ?>
			</section>
		<?php
			$_sections_printed_count++;
		endforeach;
	}

	public function admin_enqueue_scripts() {
		global $pagenow;

		if ( ! in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) )
			return;

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-resizable' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );

		if ( POJO_DEVELOPER_MODE ) {
			wp_enqueue_script( 'pojo-page-builder', get_template_directory_uri() . '/core/page-builder/admin-ui/page-builder.js', array( 'jquery' ) );
		}

		wp_enqueue_script( 'pojo-builder-templates', get_template_directory_uri() . '/core/page-builder/admin-ui/templates-app.min.js', array( 'jquery', 'backbone' ), false, true );
	}
	
	public function ajax_pb_open_visual_editor() {
		if ( empty( $_POST['value'] ) )
			$_POST['value'] = '';

		wp_editor( '', $_POST['id'], array( 'editor_class' => 'pb-text-editor' ) );
		
		die();
	}
	
	public function ajax_pb_get_duplicate_widget() {
		if ( ! empty( $_POST['pb_input_widget_content'][0] ) ) {
			$widget_content = $_POST['pb_input_widget_content'][0];
			if ( empty( $widget_content ) )
				die;
			
			$widget_content = stripslashes_deep( $widget_content );
			wp_parse_str( $widget_content, $widget_array );

			if ( ! isset( $widget_array['pb_widget_id'][0] ) )
				die;

			$widget_id = $widget_array['pb_widget_id'][0];
			$widget_options = $widget_array['pb_widget_options'][ $widget_id ];

			if ( empty( $widget_options ) )
				die;

			list( $widget_size, $widget_parent, $id_base ) = explode( ';', $widget_options );

			$widget_args = $widget_array[ 'widget-' . $id_base ][ $widget_id ];

			if ( isset( $this->_widgets[ $id_base ] ) ) {
				$widget_obj  = $this->_widgets[ $id_base ];
				$widget_args = apply_filters( 'pb_widget_update_callback', $widget_obj->update( $widget_args, array() ), $widget_args );
				
				//$widget_args = array_map( 'stripslashes_deep', $widget_args );
				
				$this->_print_field( $id_base, $widget_id, $widget_parent, $widget_size, $widget_args );
			}
		}
		die;
	}

	public function ajax_pb_get_duplicate_row() {
		$widget_row = $this->get_saved_widgets( $_POST );
		$this->_print_row( $widget_row[0] );
		die;
	}

	public function pb_after_widget_form( WP_Widget $widget, $instance ) {
		$id    = 'pb_admin_label';
		$value = isset( $instance[ $id ] ) ? $instance[ $id ] : '';
		?>
		<p>
			<label for="<?php echo $widget->get_field_id( $id ); ?>"><?php _e( 'Admin Label', 'pojo' ); ?></label>
			<input class="widefat pb-widget-<?php echo esc_attr( $id ); ?>" id="<?php echo $widget->get_field_id( $id ); ?>" name="<?php echo $widget->get_field_name( $id ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
		</p>
		<?php
		
		$id    = 'pb_css_classes';
		$value = isset( $instance[ $id ] ) ? $instance[ $id ] : '';
		?>
		<p>
			<label for="<?php echo $widget->get_field_id( $id ); ?>"><?php _e( 'CSS Classes', 'pojo' ); ?></label>
			<input class="widefat pb-widget-<?php echo esc_attr( $id ); ?>" id="<?php echo $widget->get_field_id( $id ); ?>" name="<?php echo $widget->get_field_name( $id ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
		</p>
		<?php
	}

	public function pb_widget_update_callback( $instance, $new_instance ) {
		$instance['pb_admin_label'] = isset( $new_instance['pb_admin_label'] ) ? $new_instance['pb_admin_label'] : '';
		$instance['pb_css_classes'] = isset( $new_instance['pb_css_classes'] ) ? $new_instance['pb_css_classes'] : '';
		return $instance;
	}

	public function display_builder( $post_id = null ) {
		if ( is_null( $post_id ) )
			$post_id = get_the_ID();
		
		$format = atmb_get_field( 'pf_id', $post_id );
		if ( empty( $format ) )
			$format = Pojo_Core::instance()->page_format->_default_id;
		
		if ( 'page-builder' === $format ) {
			do_action( 'pb_before_display_builder_front', $post_id );
			$this->pd_print_page_builder_front( $post_id );
			do_action( 'pb_after_display_builder_front', $post_id );
			return true;
		}
		return false;
	}

	public function after_display_builder_front( $post_id ) {
		echo apply_filters( 'the_content', '' );
	}

	public function __construct() {
		if ( ! $this->is_builder_active() ) {
			return;
		}

		include( 'templates/templates.php' );
		$this->templates = new Pojo_Builder_Templates();
		
		include( 'page-builder-updates.php' );
		$this->updates = new Pojo_Page_Builder_Updates();

		include( 'page-builder-embed-shortcode.php' );
		$this->embed_shortcode = new Pojo_Builder_Embed_Shortcode();
		
		if ( class_exists( 'Pojo_Widget_Base' ) ) {
			include( 'widgets/class-pojo-widget-title.php' );
			include( 'widgets/class-pojo-widget-menu-anchor.php' );
			include( 'widgets/class-pojo-widget-divider.php' );
			include( 'widgets/class-pojo-widget-wysiwyg.php' );
			include( 'widgets/class-pojo-widget-sidebar.php' );
			
			if ( Pojo_Compatibility::is_revslider_installer() )
				include( 'widgets/class-pojo-widget-rev-slider.php' );
		}

		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'save_post', array( &$this, 'hook_save_post' ), 55 );
		
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ), 150 );
		add_action( 'pojo_print_admin_page_builder_wrap', array( &$this, 'print_admin_page_builder_wrap' ), 10, 2 );

		add_action( 'pd_print_page_builder_front', array( &$this, 'pd_print_page_builder_front' ) );
		add_action( 'pb_after_display_builder_front', array( &$this, 'after_display_builder_front' ), 50 );
		
		add_action( 'wp_ajax_pb_open_visual_editor', array( &$this, 'ajax_pb_open_visual_editor' ) );
		add_action( 'wp_ajax_pb_get_duplicate_widget', array( &$this, 'ajax_pb_get_duplicate_widget' ) );
		add_action( 'wp_ajax_pb_get_duplicate_row', array( &$this, 'ajax_pb_get_duplicate_row' ) );

		add_action( 'pb_after_widget_form', array( &$this, 'pb_after_widget_form' ), 10, 2 );
		add_filter( 'pb_widget_update_callback', array( &$this, 'pb_widget_update_callback' ), 10, 2 );
	}
}
