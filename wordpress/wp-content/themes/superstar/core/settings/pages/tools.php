<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Page_Tools {
	
	protected $_capability = 'manage_options';
	
	protected $_message = '';
	
	protected $_print_footer_scripts = false;
	
	protected function _redirect_back( $message_id = '' ) {
		wp_redirect(
			add_query_arg(
				array(
					'page' => 'pojo-tools',
					'message' => $message_id,
				),
				admin_url( 'admin.php' )
			)
		);
	}

	public function manager_actions() {
		if ( empty( $_POST['pojo-tools-action'] ) )
			return;
		
		switch ( $_POST['pojo-tools-action'] ) {
			case 'export' :
				check_admin_referer( 'pojo-customizer-export' );
				
				$filename = get_stylesheet() . '-customizer-' . date( 'Y-m-d' ) . '.json';

				$theme_mods = get_theme_mods();

				// We are no need menu location to our export file..
				unset( $theme_mods['nav_menu_locations'] );

				$export_options = array();
				foreach ( $theme_mods as $key => $value ) {
					$export_options[ $key ] = maybe_unserialize( $value );
				}
				
				header( 'Content-Type: text/json; charset=' . get_option( 'blog_charset' ) );
				header( 'Content-Disposition: attachment; filename=' . $filename );

				echo json_encode( $export_options );
				die();
				
			case 'import' :
				check_admin_referer( 'pojo-customizer-import' );

				$import_file = $_FILES['import_file']['tmp_name'];

				if ( empty( $import_file ) )
					wp_die( __( 'Please upload a file to import', 'pojo' ) );

				$options = json_decode( file_get_contents( $import_file ), true );
				if ( empty( $options ) )
					wp_die( __( 'Invalid file', 'pojo' ) );
				
				foreach ( $options as $key => $value ) {
					set_theme_mod( $key, $value );
				}

				$this->_redirect_back( 'pojo_customizer_export' );
				die();
				
			
			
			case 'reset' :
				
				break;
		}
	}

	public function register_menu() {
		add_submenu_page(
			'pojo-home',
			__( 'Tools', 'pojo' ),
			__( 'Tools', 'pojo' ),
			$this->_capability,
			'pojo-tools',
			array( &$this, 'display_page' )
		);
	}

	public function display_page() {
		$this->_print_footer_scripts = true;
		$reset_customizer_link = sprintf(
			'<a href="%s" id="%s" class="button">%s</a>',
			add_query_arg(
				array(
					'action' => 'pojo_reset_customizer',
					'_nonce' => wp_create_nonce( 'pojo-reset-customizer' ),
				),
				admin_url( 'admin-ajax.php' )
			),
			'pojo-button-reset-customizer',
			__( 'Reset Customizer', 'pojo' )
		);
		
		$reset_thumb_link = sprintf(
			'<a href="%s" id="%s" class="button">%s</a>',
			add_query_arg(
				array(
					'action' => 'pojo_reset_thumbs',
					'_nonce' => wp_create_nonce( 'pojo-reset-thumbs' ),
				),
				admin_url( 'admin-ajax.php' )
			),
			'pojo-button-reset-thumbs',
			__( 'Purge Cache Thumbnails', 'pojo' )
		);
		
		$reset_page_templates_link = sprintf(
			'<a href="%s" id="%s" class="button">%s</a>',
			add_query_arg(
				array(
					'action' => 'pojo_reset_page_templates',
					'_nonce' => wp_create_nonce( 'pojo-reset-page-templates' ),
				),
				admin_url( 'admin-ajax.php' )
			),
			'pojo-button-reset-page-templates',
			__( 'Reset Page Templates', 'pojo' )
		);

		?>
		<div class="wrap">

			<div id="icon-themes" class="icon32"></div>
			<h2><?php _e( 'Tools', 'pojo' ); ?></h2>
			
			<?php if ( ! empty( $this->_message ) ) : ?>
			<div class="updated"><p><?php echo $this->_message; ?></p></div>
			<?php endif; ?>

			<h3><?php _e( 'Customizer Export', 'pojo' ); ?></h3>
			<form method="post">
				<?php wp_nonce_field( 'pojo-customizer-export' ); ?>
				<input type="hidden" name="pojo-tools-action" value="export" />
				
				<p><?php _e( 'When you click the button below, WordPress will create a JSON file for you to save on your computer.', 'pojo' ); ?></p>
				<p><?php _e( 'This format, will contain your Customizer settings for your theme.', 'pojo' ); ?></p>
				<p><?php _e( 'Once you\'ve saved the download file, you can use the Import function to import the previously exported settings.', 'pojo' ); ?></p>
				<p class="submit">
					<input type="submit" name="export" class="button button-primary" value="<?php _e( 'Download Export File', 'pojo' ); ?>" />
				</p>
			</form>
			
			<hr />

			<h3><?php _e( 'Customizer Import', 'pojo' ); ?></h3>
			<form method="post" enctype="multipart/form-data" id="pojo-customizer-import">
				<?php wp_nonce_field( 'pojo-customizer-import' ); ?>
				<input type="hidden" name="pojo-tools-action" value="import" />
				
				<p><?php _e( 'Howdy! Upload your Customizer Settings file and we\'ll import the options into this site.', 'pojo' ); ?></p>
				<p><?php _e( 'Choose a filename.json file to upload, then click Upload File and Import.', 'pojo' ); ?></p>
				<p>
					<label>
						<?php _e( 'Choose a file from your computer:', 'pojo' ); ?>
						<input type="file" class="pojo-import-file" name="import_file" />
					</label>
				</p>
				<p class="submit">
					<input type="submit" name="submit" class="button pojo-import-submit" value="<?php _e( 'Upload Import File', 'pojo' ); ?>" disabled />
				</p>
			</form>

			<hr />

			<h3><?php _e( 'Reset Customizer', 'pojo' ); ?></h3>
		
			<div>
				<p style="color: #ff0000;"><?php _e( 'Revert your theme back to a brand new installation. This process cannot be undone!', 'pojo' ); ?></p>
				<p><?php _e( 'Do not forget to backup all your Customizer Settings with the Customizer Export before the reset.', 'pojo' ); ?></p>
				<p><?php echo $reset_customizer_link; ?></p>
			</div>

			<hr />
			
			<h3><?php _e( 'Purge all Cache Thumbnails', 'pojo' ); ?></h3>
			<div>
				<p><?php _e( 'Clicking on this button will delete all the thumbnails that are stored in the wp-content/uploads/thumbs folder.', 'pojo' ); ?></p>
				<p><?php _e( 'This should not affect the images display on the site, but just refresh the thumbnails stored on the server. This is useful in cases where you want to delete unused images.', 'pojo' ); ?></p>
				<p><?php echo $reset_thumb_link; ?></p>
			</div>

			<hr />
			
			<h3><?php _e( 'Page Template Reset', 'pojo' ); ?></h3>
			<div>
				<p><?php _e( 'This tool allows you to reset all the site\'s page templates to default mode. Please note that this cannot be undone, and is designed for users that are moving their site to Pojo Framework from a different theme which uses page templates.', 'pojo' ); ?></p>
				<p><?php echo $reset_page_templates_link; ?></p>
			</div>
			
		</div>
	<?php
	}

	public function admin_footer() {
		if ( ! $this->_print_footer_scripts )
			return;
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				$( '#pojo-button-reset-customizer, #pojo-button-reset-thumbs, #pojo-button-reset-page-templates' ).on( 'click', function( e ) {
					if ( ! confirm( '<?php _e( 'Are you sure you want to do this action?', 'pojo' ); ?>' ) ) {
						e.preventDefault();
					}
				} );
			} );
		</script>
	<?php
	}

	public function admin_notices() {
		switch ( filter_input( INPUT_GET, 'message' ) ) {
			case 'pojo_customizer_reset' :
				printf( '<div class="updated"><p>%s</p></div>', __( 'All customizer settings have been successfully deleted.', 'pojo' ) );
				break;
			
			case 'pojo_customizer_export' :
				printf( '<div class="updated"><p>%s</p></div>', __( 'All options were restored successfully!', 'pojo' ) );
				break;
			
			case 'pojo_thumbs_reset' :
				printf( '<div class="updated"><p>%s</p></div>', __( 'All thumbs cache have been successfully deleted.', 'pojo' ) );
				break;
			
			case 'pojo_templates_reset' :
				printf( '<div class="updated"><p>%s</p></div>', __( 'All page templates have been successfully reset.', 'pojo' ) );
				break;
		}
	}

	public function ajax_pojo_reset_customizer() {
		if ( ! check_ajax_referer( 'pojo-reset-customizer', '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo' ) );
		}
		
		// Remove all.
		remove_theme_mods();
		
		$this->_redirect_back( 'pojo_customizer_reset' );
		die();
	}

	public function ajax_pojo_reset_thumbs() {
		if ( ! check_ajax_referer( 'pojo-reset-thumbs', '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo' ) );
		}

		// Empty cache.
		$upload_info = wp_upload_dir();
		$upload_dir = $upload_info['basedir'];
		$upload_dir .= '/' . BFITHUMB_UPLOAD_DIR;
		
		foreach ( glob( $upload_dir . '/*' ) as $file ) {
			unlink( $file );
		}

		$this->_redirect_back( 'pojo_thumbs_reset' );
		die();
	}

	public function ajax_pojo_reset_page_templates() {
		if ( ! check_ajax_referer( 'pojo-reset-page-templates', '_nonce', false ) || ! current_user_can( $this->_capability ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'pojo' ) );
		}
		
		global $wpdb;
		$wpdb->delete(
			$wpdb->postmeta,
			array(
				'meta_key' => '_wp_page_template',
			)
		);

		$this->_redirect_back( 'pojo_templates_reset' );
		die();
	}

	public function __construct() {
		if ( ! current_user_can( $this->_capability ) )
			return;
		
		add_action( 'admin_init', array( &$this, 'manager_actions' ), 500 );
		add_action( 'admin_menu', array( &$this, 'register_menu' ), 500 );
		add_action( 'admin_footer', array( &$this, 'admin_footer' ) );

		add_action( 'admin_notices', array( &$this, 'admin_notices' ) );

		add_action( 'wp_ajax_pojo_reset_customizer', array( &$this, 'ajax_pojo_reset_customizer' ) );
		add_action( 'wp_ajax_pojo_reset_thumbs', array( &$this, 'ajax_pojo_reset_thumbs' ) );
		add_action( 'wp_ajax_pojo_reset_page_templates', array( &$this, 'ajax_pojo_reset_page_templates' ) );
	}
	
}