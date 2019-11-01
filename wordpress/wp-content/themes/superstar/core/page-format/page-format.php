<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Page_Format {
	
	public $_formats    = array();
	public $_default_id = '';

	/**
	 * @var Pojo_MetaBox_Panel
	 */
	public $panel;
	
	/**
	 * @var Pojo_MetaBox_Panel[]
	 */
	public $panels = array();
	
	public function init() {
		add_post_type_support( 'page', array( 'pojo-page-format' ) );
	}

	public function get_formats() {
		$cpt = get_post_type();
		if ( empty( $this->_formats ) && $cpt ) {			
			if ( post_type_supports( $cpt, 'editor' ) ) {
				$this->_formats['text'] = array(
					'title' => __( 'Editor', 'pojo' ),
					'type' => 'editor',
					'actions' => array(
						array(
							'selector' => '#postdivrich, #elementor-switch-mode',
							'type' => 'show',
						),
						array(
							'selector' => 'div.pf-list-content-wrap, #page-builder',
							'type' => 'hide',
						),
					),
				);
			}

			if ( 'page' === $cpt ) {
				foreach ( apply_filters( 'pf_format_content_list', array( 'post' ) ) as $cpt ) {
					$post_type_object = get_post_type_object( $cpt );
					$this->_formats[ 'cpt-' . $cpt ] = array(
						'title' => $post_type_object->labels->name,
						'type' => 'content',
						'actions' => array(
							array(
								'selector' => '#postdivrich, #page-builder, #elementor-switch-mode',
								'type' => 'hide',
							),
							array(
								'selector' => 'div.pf-list-content-wrap',
								'type' => 'hide',
							),
							array(
								'selector' => 'div.pf-list-content-wrap-cpt-' . $cpt,
								'type' => 'show',
							),
						),
					);
				}
			}
			
			if ( Pojo_Core::instance()->builder->is_builder_active() ) {
				$this->_formats['page-builder'] = array(
					'title' => __( 'Builder', 'pojo' ),
					'type' => 'builder',
					'actions' => array(
						array(
							'selector' => '#postdivrich, div.pf-list-content-wrap, #elementor-switch-mode',
							'type' => 'hide',
						),
						array(
							'selector' => '#page-builder',
							'type' => 'show',
						),
					),
				);
			}

			foreach ( $this->_formats as $key => $format ) {
				if ( 'editor' === $format['type'] )
					continue;

				if ( 'content' === $format['type'] ) {
					$fields = apply_filters( 'pf_list_posts_' . $key, array() );
					if ( empty( $fields ) )
						continue;

					$meta_box = array(
						'id' => '',
						'title' => __( 'Page Format', 'pojo' ),
						'post_types' => array(),
						'prefix' => 'po_',
						'fields' => $fields,
					);
					$this->panels[ $key ] = new Pojo_MetaBox_Panel( $meta_box );
				}
			}

			// Default format is first item in formats array.
			$default_format    = array_keys( $this->_formats );
			$default_format    = array_shift( $default_format );
			$this->_default_id = $default_format;
		}
		return $this->_formats;
	}
	
	public function admin_enqueue_scripts() {
		global $pagenow;
		
		$formats = $this->get_formats();
		if ( empty( $formats ) || ! in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) )
			return;
		
		if ( POJO_DEVELOPER_MODE ) {
			wp_enqueue_script( 'pojo-page-format', get_template_directory_uri() . '/core/page-format/admin-ui/page-format.js', array( 'jquery' ) );
		}
	}
	
	public function pojo_admin_localize_scripts_array( $localize_array ) {
		$pf = array();
		foreach ( $this->get_formats() as $key => $format ) {
			$pf[ $key ] = array(
				'id' => $key,
				'actions' => $format['actions'],
			);
		}
		$localize_array['pf_formats'] = $pf;
		
		return $localize_array;
	}
	
	public function edit_form_after_title( $post ) {
		$formats = $this->get_formats();
		if ( empty( $formats ) )
			return;

		$current_cpt = get_post_type( $post->ID );
		if ( ! post_type_supports( $current_cpt, 'pojo-page-format' ) )
			return;

		wp_nonce_field( basename( __FILE__ ), '_pojo_page_format_nonce' );

		$current_pf_id = atmb_get_field( 'pf_id', $post->ID );
		if ( ! $current_pf_id )
			$current_pf_id = $this->_default_id;
		?>
		<div id="pojo-page-format">
			<h2 class="nav-tab-wrapper ppf-radio-select-wrapper">
			<?php foreach ( $formats as $key => $format ) : ?>
				<span id="ppf-id-<?php echo esc_attr( $key ); ?>" class="nav-tab">
					<label>
						<input style="display: none;" type="radio" class="ppf-format-id" name="pf_id" value="<?php echo esc_attr( $key ); ?>"<?php checked( $current_pf_id, $key ); ?> />
						<?php echo $format['title']; ?>
					</label>
				</span>
			<?php endforeach; ?>
			</h2>

			<?php foreach ( $formats as $key => $format ) :
				if ( 'editor' === $format['type'] ) continue;
				if ( 'content' === $format['type'] && ! empty( $this->panels[ $key ] ) ) : ?>
				<div id="ppf-post-<?php echo esc_attr( $key ); ?>" class="pf-list-content-wrap pf-list-content-wrap-<?php echo esc_attr( $key ); ?>">
					<div class="postbox">
						<div class="inside"><?php $this->panels[ $key ]->render_panel( $post ); ?></div>
					</div>
				</div>
				<?php elseif ( 'builder' === $format['type'] ) : ?>
					<?php do_action( 'pojo_print_admin_page_builder_wrap', $format, $post ); ?>
				<?php endif; ?>
			<?php endforeach; ?>
			
		</div>
		<?php
	}
	
	public function save_post( $post_id ) {
		if ( ! isset( $_POST['_pojo_page_format_nonce'] ) || ! wp_verify_nonce( $_POST['_pojo_page_format_nonce'], basename( __FILE__ ) ) )
			return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// Exit when you don't have $_POST array.
		if ( empty( $_POST ) )
			return;
		
		if ( ! empty( $_POST['pf_id'] ) )
			update_post_meta( $post_id, 'pf_id', $_POST['pf_id'] );
		else
			delete_post_meta( $post_id, 'pf_id' );
	}

	public function remove_format_in_elementor_mode( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
		if ( '_elementor_edit_mode' === $meta_key && 'builder' === $meta_value ) {
			delete_post_meta( $object_id, 'pf_id' );
			delete_post_meta( $object_id, 'po_content' );
		}

		return $check;
	}
	
	public function admin_head() {
		global $post;

		if ( ! post_type_supports( get_post_type( $post->ID ), 'pojo-page-format' ) )
			return;

		$pf_id   = atmb_get_field( 'pf_id', $post->ID );
		$formats = $this->get_formats();
		if ( empty( $pf_id ) || ! isset( $formats[ $pf_id ] ) )
			$pf_id = $this->_default_id;
		
		?><style><?php
		foreach ( $formats[ $pf_id ]['actions'] as $action ) :
			printf( '%s{display:%s}', $action['selector'], 'hide' === $action['type'] ? 'none' : 'block' );
		endforeach; ?></style><?php
	}
	
	public function display_format_in_actions( $actions, $post ) {
		if ( post_type_supports( get_post_type( $post->ID ), 'pojo-page-format' ) ) {
			$formats = $this->get_formats();
			$display = array();
			$format  = atmb_get_field( 'pf_id', $post->ID );
			if ( empty( $format ) )
				$format = $this->_default_id;
			
			if ( isset( $formats[ $format ] ) ) {
				$format_title = $formats[ $format ]['title'];
				
				if ( 'text' === $format && Pojo_Compatibility::is_elementor_installed() ) {
					$edit_mode = get_post_meta( $post->ID, '_elementor_edit_mode', true );
					
					if ( 'builder' === $edit_mode ) {
						$format_title = __( 'Elementor', 'pojo' );
					}
				}
				$display[] = sprintf( '<b>%s:</b> %s', __( 'Format', 'pojo' ), $format_title );
			}
			
			$page_layout = atmb_get_field( 'po_layout', $post->ID );
			if ( empty( $page_layout ) )
				$page_layout = '';
			
			foreach ( Pojo_Layouts::instance()->get_available_layouts() as $layout ) {
				if ( $page_layout === $layout['id'] ) {
					$display[] = sprintf( '<b>%s:</b> %s', __( 'Layout', 'pojo' ), $layout['title'] );
					break;
				}
			}
			
			if ( ! empty( $display ) ) {
				echo implode( ', ', $display );
			}
		}
		return $actions;
	}

	public function body_class( $classes, $class ) {
		if ( is_page() ) {
			$format = atmb_get_field( 'pf_id' );
			if ( empty( $format ) )
				$format = $this->_default_id;
			
			$classes[] = 'format-' . $format;
		}
		return $classes;
	}
	
	public function pf_list_posts_cpt_post( $fields ) {
		$cpt = 'post';
		$fields[] = array(
			'id'   => 'content',
			'type' => Pojo_MetaBox::FIELD_HIDDEN,
			'std'  => $cpt,
		);
		$taxonomies = get_object_taxonomies( 'post' );

		if ( $taxonomies ) {
			$fields[] = array(
				'id' => 'taxonomy',
				'title' => __( 'Select Taxonomy', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_TAXONOMY_SELECT,
				'object_type' => 'post',
				'classes' => array( 'select-show-or-hide-fields' ),
			);

			foreach ( $taxonomies as $taxonomy ) {
				$fields[] = array(
					'id'       => 'taxonomy_terms',
					'type'     => Pojo_MetaBox::FIELD_TAXONOMY_TERM_CHECKBOX,
					'show_on'  => array( 'po_taxonomy' => $taxonomy ),
					'taxonomy' => $taxonomy,
					'classes'  => array( 'atmb-field-border-none' ),
				);
			}
		}

		$display_types = array();
		$display_types['default'] = __( 'Default', 'pojo' );
		
		$display_types = apply_filters( 'po_display_types', $display_types, $cpt );

		$base_radio_image_url = get_template_directory_uri() . '/core/assets/admin-ui/images/display_type';
		
		$display_types_radios = array();
		foreach ( $display_types as $d_key => $display_type ) {
			$display_types_radios[] = array(
				'id' => $d_key,
				'title' => $display_type,
				'image' => sprintf( '%s/%s.png', $base_radio_image_url, $d_key ),
			);
		}
		
		$fields[] = array(
			'id'      => 'display_type',
			'title'   => __( 'Select Content Layout', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_RADIO_IMAGE,
			'desc'    => __( 'Blog with thumbnail (List) or Gallery with columns (Grid)', 'pojo' ),
			'classes' => array( 'select-show-or-hide-fields' ),
			'options' => $display_types_radios,
			'std'     => 'default',
		);

		$fields = apply_filters( 'po_init_fields_after_display_type', $fields, $cpt );

		$fields[] = array(
			'id'      => 'advanced_settings',
			'title'   => __( 'Advanced Settings', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_BUTTON_COLLAPSE,
		);
		
		$fields[] = array(
			'id'      => 'posts_per_page_mode',
			'title'   => __( 'Posts Per Page', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'classes' => array( 'select-show-or-hide-fields' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'custom' => __( 'Custom', 'pojo' ),
			),
			'std'     => '',
		);

		$fields[] = array(
			'id'      => 'posts_per_page',
			'title'   => __( 'Number Posts', 'pojo' ),
			'std'     => get_option( 'posts_per_page' ),
			'show_on' => array( 'po_posts_per_page_mode' => 'custom' ),
		);

		$fields[] = array(
			'id' => 'order_by',
			'title' => __( 'Order By', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Date', 'pojo' ),
				'menu_order' => __( 'Menu Order', 'pojo' ),
				'title' => __( 'Title', 'pojo' ),
				'rand' => __( 'Random', 'pojo' ),
			),
			'std' => '',
		);
		
		$fields[] = array(
			'id' => 'order',
			'title' => __( 'Order', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'DESC', 'pojo' ),
				'ASC' => __( 'ASC', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'offset',
			'title'   => __( 'Offset', 'pojo' ),
			'std'     => 0,
			'desc'    => __( 'Number of post to displace or pass over', 'pojo' ),
		);
		
		$fields[] = array(
			'id'      => 'pagination',
			'title'   => __( 'Pagination', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => po_get_theme_pagination_support(),
			'std'     => '',
		);

		$fields[] = array(
			'id'    => 'heading_meta_data',
			'title' => __( 'Meta Data', 'pojo' ),
			'type'  => Pojo_MetaBox::FIELD_HEADING,
		);
		
		$fields = apply_filters( 'po_register_list_post_fields_before_metadata', $fields, $cpt );

		$fields[] = array(
			'id'      => 'metadata_date',
			'title'   => __( 'Date', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'metadata_time',
			'title'   => __( 'Time', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'metadata_comments',
			'title'   => __( 'Comments', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'metadata_author',
			'title'   => __( 'Author', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'    => 'heading_excerpt_post',
			'title' => __( 'Content Post', 'pojo' ),
			'type'  => Pojo_MetaBox::FIELD_HEADING,
		);

		$fields[] = array(
			'id'      => 'metadata_excerpt',
			'title'   => __( 'Content', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'show' => __( 'Excerpt', 'pojo' ),
				'full' => __( 'Full Content', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'excerpt_words_mode',
			'title'   => __( 'Excerpt Length (Words)', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'classes' => array( 'select-show-or-hide-fields' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'custom' => __( 'Custom', 'pojo' ),
			),
			'std'     => '',
		);

		$fields[] = array(
			'id'      => 'excerpt_words',
			'title'   => __( 'Number Words', 'pojo' ),
			'std'     => '',
			'show_on' => array( 'po_excerpt_words_mode' => 'custom' ),
		);

		$fields[] = array(
			'id'      => 'metadata_readmore',
			'title'   => __( 'Read More', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'text_readmore_mode',
			'title'   => __( 'Text Read More', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'classes' => array( 'select-show-or-hide-fields' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'custom' => __( 'Custom', 'pojo' ),
			),
			'std'     => '',
		);

		$fields[] = array(
			'id'      => 'text_readmore',
			'title'   => __( 'Text Read More', 'pojo' ),
			'show_on' => array( 'po_text_readmore_mode' => 'custom' ),
			'std'     => '',
		);

		$fields = apply_filters( 'po_register_list_post_fields_after_metadata', $fields, $cpt );

		$fields[] = array(
			'id'    => 'no_apply_child_posts',
			'title' => __( 'Don\'t apply the page settings to the child posts', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_CHECKBOX,
			'std' => false,
		);

		$fields[] = array(
			'id'      => 'advanced_settings',
			'title'   => __( 'Advanced Settings', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_BUTTON_COLLAPSE,
			'mode'    => 'end',
		);

		return $fields;
	}
	
	public function __construct() {
		$this->_default_id = 'text';

		add_action( 'init', array( &$this, 'init' ), 50 );
		
		// Make sure all metaboxes are loaded.
		add_action( 'save_post', array( &$this, 'get_formats' ), 1 );

		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ), 101 );
		add_action( 'edit_form_after_title', array( &$this, 'edit_form_after_title' ), 5 );
		add_action( 'save_post', array( &$this, 'save_post' ), 50 );
		add_action( 'update_post_metadata', array( &$this, 'remove_format_in_elementor_mode' ), 50, 5 );

		add_action( 'admin_head-post.php', array( &$this, 'admin_head' ) );
		add_action( 'admin_head-post-new.php', array( &$this, 'admin_head' ) );
		add_filter( 'page_row_actions', array( &$this, 'display_format_in_actions' ), 10, 2 );
		add_filter( 'body_class', array( &$this, 'body_class' ), 20, 2 );
		
		add_filter( 'pf_list_posts_cpt-post', array( &$this, 'pf_list_posts_cpt_post' ) );
		add_filter( 'pojo_admin_localize_scripts_array', array( &$this, 'pojo_admin_localize_scripts_array' ) );
	}
	
}