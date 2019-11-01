<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Builder_Template_Type_Local extends Pojo_Builder_Template_Type_Base {

	const CPT = '_pb_templates';

	const BASE_TEMP_ROW_ID    = 'ROW_ID-';
	const BASE_TEMP_COLUMN_ID = 'COLUMN_ID-';
	const BASE_TEMP_WIDGET_ID = 'WIDGET_ID-';

	public function register_data() {
		// CPT: _pb_templates.
		$labels = array(
			'name'               => __( 'Builder Templates', 'pojo' ),
			'singular_name'      => __( 'Template', 'pojo' ),
			'add_new'            => __( 'Add New', 'pojo' ),
			'add_new_item'       => __( 'Add New Template', 'pojo' ),
			'edit_item'          => __( 'Edit Template', 'pojo' ),
			'new_item'           => __( 'New Template', 'pojo' ),
			'all_items'          => __( 'All Templates', 'pojo' ),
			'view_item'          => __( 'View Template', 'pojo' ),
			'search_items'       => __( 'Search Template', 'pojo' ),
			'not_found'          => __( 'No Templates found', 'pojo' ),
			'not_found_in_trash' => __( 'No Templates found in Trash', 'pojo' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Builder Templates', 'pojo' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'rewrite'         => false,
			'show_ui'         => true,
			'show_in_menu'    => false,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'supports'        => array( 'title', 'thumbnail', 'author' ),
		);

		register_post_type(
			self::CPT,
			apply_filters( 'pojo_register_post_type_builder_templates', $args )
		);
	}

	public function post_updated_messages( $messages ) {
		global $post;

		$messages[ self::CPT ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Template updated.', 'pojo' ),
			2  => __( 'Custom field updated.', 'pojo' ),
			3  => __( 'Custom field deleted.', 'pojo' ),
			4  => __( 'Template updated.', 'pojo' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Template restored to revision from %s', 'pojo' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Template published.', 'pojo' ),
			7  => __( 'Template saved.', 'pojo' ),
			8  => __( 'Template submitted.', 'pojo' ),
			9  => __( 'Template scheduled for: <strong>%1$s</strong>.', 'pojo' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'pojo' ), strtotime( $post->post_date ) ),
			10 => __( 'Template draft updated.', 'pojo' ),
		);

		return $messages;
	}

	public function register_metabox( $meta_boxes = array() ) {
		$fields = array();

		$fields[] = array(
			'id' => 'description',
			'title' => __( 'Description', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_TEXT,
		);

		$fields[] = array(
			'id' => 'template_actions',
			'title' => __( 'Export', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_RAW_HTML,
		);

		$meta_boxes[] = array(
			'id' => 'pojo-templates',
			'title' => __( 'Advanced Options', 'pojo' ),
			'post_types' => array( self::CPT ),
			'context' => 'normal',
			'priority' => 'core',
			'prefix' => 'bt_',
			'fields' => $fields,
		);
		return $meta_boxes;
	}

	/**
	 * @param string                      $raw
	 * @param Pojo_MetaBox_Field_Raw_html $object
	 *
	 * @return string
	 */
	public function parse_export_button( $raw, $object ) {
		if ( 'bt_template_actions' !== $object->_field['id'] )
			return $raw;
		
		return sprintf(
			'<a href="%s" class="button">%s</a><p class="description">%s</p>',
			$this->_get_export_link( get_the_ID() ),
			__( 'Export Template', 'pojo' ),
			__( 'When you click the button below WordPress will create a JSON file for you to save to your computer. Once you have saved the downloaded file, you can use the Template Import area to import the exported template.', 'pojo' )
		);
	}

	protected function _get_export_link( $post_id ) {
		return add_query_arg(
			array(
				'pb-action' => 'export',
				'type' => $this->get_type(),
				'post_id' => $post_id,
				'_nonce' => wp_create_nonce( 'pb-export-' . $this->get_type() . '-' . $post_id ),
			),
			admin_url()
		);
	}

	/**
	 * @param array  $builder_rows
	 * @param string $template_name
	 * @param string $template_desc
	 *
	 * @return int|WP_Error
	 */
	protected function _new_template( $builder_rows, $template_name = '', $template_desc = '' ) {
		$post_id = wp_insert_post(
			array(
				'post_title' => ! empty( $template_name ) ? $template_name : __( '(no title)', 'pojo' ),
				'post_status' => 'publish',
				'post_type' => self::CPT,
			)
		);
		
		//update_post_meta( $post_id, 'pb_builder_rows', $builder_rows );
		Pojo_Core::instance()->builder->save_builder_rows( $post_id, $builder_rows );
		update_post_meta( $post_id, 'bt_description', $template_desc );
		
		return $post_id;
	}

	public function manager_actions() {
		if ( empty( $_REQUEST['pb-action'] ) || empty( $_REQUEST['type'] ) || $this->get_type() !== $_REQUEST['type'] )
			return;
		
		switch ( $_REQUEST['pb-action'] ) {
			case 'export':
				if ( empty( $_GET['post_id'] ) || ! check_ajax_referer( 'pb-export-' . $this->get_type() . '-' . $_GET['post_id'], '_nonce', false ) ) {
					wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo' ) );
				}
				
				$post = get_post( $_GET['post_id'] );
				if ( empty( $post ) )
					wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo' ) );
				
				$filename = 'template-' . $post->ID . '-' . date( 'Y-m-d' ) . '.json';
				$content = array(
					'title' => $post->post_title,
					'description' => get_post_meta( $post->ID, 'bt_description', true ),
					'builder_rows' => Pojo_Core::instance()->builder->get_builder_rows( $post->ID ),
				);

				$file_contents = json_encode( $content );
				$filesize = strlen( $file_contents );

				// Headers to prompt "Save As"
				header( 'Content-Type: application/octet-stream' );
				header( 'Content-Disposition: attachment; filename=' . $filename );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate' );
				header( 'Pragma: public' );
				header( 'Content-Length: ' . $filesize );

				// Clear buffering just in case
				@ob_end_clean();
				flush();

				// Output file contents
				echo $file_contents;
				
				exit;
				
			case 'import' :
				check_admin_referer( 'pb-import-' . $this->get_type() );

				$import_file = $_FILES['pb_import_file']['tmp_name'];

				if ( empty( $import_file ) )
					wp_die( __( 'Please upload a file to import', 'pojo' ) );

				$content = json_decode( file_get_contents( $import_file ), true );
				if ( empty( $content ) || empty( $content['builder_rows'] ) || ! is_array( $content['builder_rows'] ) ) {
					wp_die( __( 'Invalid file', 'pojo' ) );
				}
				$template_name = isset( $content['title'] ) ? $content['title'] : '';
				$template_desc = isset( $content['description'] ) ? $content['description'] : '';

				$template_id = $this->_new_template( $content['builder_rows'], $template_name, $template_desc );
				
				wp_redirect( get_edit_post_link( $template_id, 'raw' ) );
				exit;
		}
	}

	public function admin_menu() {
		add_submenu_page(
			'pojo-home',
			__( 'Templates', 'pojo' ),
			__( 'Templates', 'pojo' ),
			'edit_theme_options',
			'edit.php?post_type=_pb_templates'
		);
	}

	public function menu_highlight() {
		global $parent_file, $submenu_file;
		
		if ( 'edit.php?post_type=_pb_templates' === $submenu_file ) {
			$parent_file = 'pojo-home';
		}
	}

	/**
	 * @param $actions
	 * @param $post WP_Post
	 *
	 * @return mixed
	 */
	public function post_row_actions( $actions, $post ) {
		if ( self::CPT === $post->post_type ) {
			$actions['pb-export'] = '<a href="' . $this->_get_export_link( $post->ID ) . '">' . __( 'Export Template', 'pojo' ) . '</a>';
		}
		return $actions;
	}

	public function print_import_form( $views ) {
		?>
		<div class="import-new-template postbox">
			<h3><?php _e( 'Import Template', 'pojo' ); ?></h3>
			<div class="inside">
				<form method="post" enctype="multipart/form-data">
					<?php wp_nonce_field( 'pb-import-' . $this->get_type() ); ?>
					<input type="hidden" name="pb-action" value="import" />
					<input type="hidden" name="type" value="<?php echo $this->get_type(); ?>" />
					<p><?php _e( 'Here you can choose a JSON file from an exported template on your computer, then click Upload File and Import. Once the template is imported it will be listed with the rest of the site\'s templates.', 'pojo' ); ?></p>
					<p>
						<label>
							<?php _e( 'Choose a file:', 'pojo' ); ?>
							<input type="file" name="pb_import_file" accept="application/json" />
						</label>
					</p>
					<p class="submit">
						<input type="submit" name="submit" class="button button-primary" value="<?php _e( 'Upload', 'pojo' ); ?>" />
					</p>
				</form>
			</div>
		</div>
		<?php
		
		return $views;
	}
	
	protected function _get_item( $post_id ) {
		$post = get_post( $post_id );
		
		$user = get_user_by( 'id', $post->post_author );
		return array(
			'id' => $post_id,
			'type' => $this->get_type(),
			'label' => $post->post_title,
			'thumbnail' => Pojo_Thumbnails::get_post_thumbnail_url( array( 'width' => '270', 'height' => '270', 'placeholder' => true ), $post_id ),
			'description' => get_post_meta( $post_id, 'bt_description', true ),
			'date' => mysql2date( get_option( 'date_format' ), $post->post_date ),
			'author' => $user->display_name,
			'edit_link' => get_edit_post_link( $post_id, 'raw' ),
			'export_link' => $this->_get_export_link( $post_id ),
		);
	}
	
	public function get_items() {
		$templates_query = new WP_Query(
			array(
				'post_type' => self::CPT,
				'post_status' => 'publish',
				'orderby' => 'title',
				'order' => 'ASC',
				'posts_per_page' => -1,
			)
		);
		
		$templates = array();
		
		if ( $templates_query->have_posts() ) {
			foreach ( $templates_query->get_posts() as $post ) {
				$templates[] = $this->_get_item( $post->ID );
			}
		}
		
		return $templates;
	}

	public function get_admin_templates() {
		return array(
			'item' => '
				<div class="attachment-preview js--select-attachment">
					<div class="thumbnail">
						<# if ( data.uploading ) { #>
							<div class="media-progress-bar"><div style="width: {{ data.percent }}%"></div></div>
						<# } else { #>
						<div class="centered">
							<img src="{{ data.thumbnail }}" />
						</div>
						<div class="filename"><div>{{ data.label }}</div></div>
						<# } #>
					</div>
				</div>
				<a class="check" href="#" title="' . esc_attr__( 'Deselect', 'pojo' ) . '"><div class="media-modal-icon"></div></a>
			',
			'sidebar' => sprintf(
				'<div class="attachment-media-view">
					<h3>%s</h3>
					<div class="thumbnail">
						<# if ( data.uploading ) { #>
							<div class="media-progress-bar"><div></div></div>
						<# } else { #>
							<img src="{{ data.thumbnail }}" />
						<# } #>
					</div>
					<div class="details">
						<div class="bt-name"><strong>%s:</strong> {{ data.label }}</div>
						<# if ( data.description ) { #>
							<div class="bt-description"><strong>%s:</strong> {{ data.description }}</div>
						<# } #>
						<div class="bt-date"><strong>%s:</strong> {{ data.date }}</div>
						<div class="bt-author"><strong>%s:</strong> {{ data.author }}</div>
					</div>
					<div class="actions">
						<a href="{{ data.edit_link }}" class="bt-edit-link" target="_blank">%s</a> | <a href="{{ data.export_link }}" class="bt-export-link" target="_blank">%s</a>
					</div>
				</div>',
				__( 'Template Details', 'pojo' ),
				__( 'Name', 'pojo' ),
				__( 'Description', 'pojo' ),
				__( 'Date', 'pojo' ),
				__( 'Author', 'pojo' ),
				__( 'Edit Template', 'pojo' ),
				__( 'Export Template', 'pojo' )
			),
			'add' => sprintf(
				'<div class="save-template">
					<div class="settings">
						<label class="setting name-template" >
							<span>%s</span>
							<input type="text" value="{{ data.label }}" data-setting="label" />
						</label>
					</div>
					<div class="settings">
						<label class="setting description-template" >
							<span>%s</span>
							<input type="text" value="{{ data.description }}" data-setting="description" />
						</label>
					</div>
				</div>',
				__( 'Title', 'pojo' ),
				__( 'Description', 'pojo' )
			),
		);
	}

	public function print_ajax_template( $template_id ) {
		$widget_rows = Pojo_Core::instance()->builder->get_builder_rows( $template_id );
		if ( ! empty( $widget_rows ) ) {
			$new_base_id = current_time( 'timestamp' );
			$index = 1;
			foreach ( $widget_rows as &$widget_row ) {
				$widget_row['id'] = str_replace( self::BASE_TEMP_ROW_ID, $new_base_id, $widget_row['id'] ) . $index++;

				foreach ( $widget_row['columns'] as &$column ) {
					$column['id'] = str_replace( self::BASE_TEMP_COLUMN_ID, $new_base_id, $column['id'] ) . $index++;

					foreach ( $column['widgets'] as &$widget ) {
						$widget['id']     = str_replace( self::BASE_TEMP_WIDGET_ID, $new_base_id, $widget['id'] ) . $index++;
						$widget['parent'] = $column['id'];
					}
				}
			}
			
			foreach ( $widget_rows as $row ) {
				Pojo_Core::instance()->builder->_print_row( $row );
			}
		}
	}

	public function ajax_bt_save_template() {
		if ( empty( $_POST['builder'] ) ) {
			wp_send_json_error();
		}

		$widget_rows = Pojo_Core::instance()->builder->get_saved_widgets( wp_parse_args( $_POST['builder'] ) );
		
		if ( empty( $widget_rows ) ) {
			wp_send_json_error();
		}
		
		$current_rows_id = $current_column_id = $current_widget_id = 1;
		
		foreach ( $widget_rows as &$widget_row ) {
			$widget_row['id'] = self::BASE_TEMP_ROW_ID . $current_rows_id++;
			
			foreach ( $widget_row['columns'] as &$column ) {
				$column['id'] = self::BASE_TEMP_COLUMN_ID . $current_column_id++;
				
				foreach ( $column['widgets'] as &$widget ) {
					$widget['id']     = self::BASE_TEMP_WIDGET_ID . $current_widget_id++;
					$widget['parent'] = $column['id'];
				}
			}
		}
		
		$template_name = isset( $_POST['template_name'] ) ? $_POST['template_name'] : '';
		$template_desc = isset( $_POST['template_desc'] ) ? $_POST['template_desc'] : '';
		
		$template_id = $this->_new_template( $widget_rows, $template_name, $template_desc );
		
		wp_send_json_success( $this->_get_item( $template_id ) );
	}

	public function __construct() {
		$this->type = 'local';
		$this->label = __( 'My Templates', 'pojo' );
		
		add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_metabox' ) );
		add_filter( 'atmb_field_raw_html', array( &$this, 'parse_export_button' ), 10, 2 );
		
		add_action( 'admin_init', array( &$this, 'manager_actions' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 100 );
		add_action( 'admin_head', array( &$this, 'menu_highlight' ) );

		add_filter( 'post_row_actions', array( &$this, 'post_row_actions' ), 20, 2 );
		add_action( 'views_edit-_pb_templates', array( &$this, 'print_import_form' ) );

		add_action( 'wp_ajax_bt_save_template', array( &$this, 'ajax_bt_save_template' ) );

		$this->register_data();
		
		parent::__construct();
	}
	
}