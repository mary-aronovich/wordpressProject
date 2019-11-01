<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Page_Home {
	
	protected $_capability = 'manage_options';
	protected $_page_id    = 'pojo-home';

	public function register_menu() {
		add_menu_page(
			__( 'Home', 'pojo' ),
			__( 'Theme Options', 'pojo' ),
			$this->_capability,
			$this->_page_id,
			array( &$this, 'display_page' ),
			'',
			62
		);
	}

	public function display_page() {
		$theme_name = Pojo_Core::instance()->licenses->updater->theme_name;
		$theme_version = Pojo_Core::instance()->licenses->updater->theme_version;

		$license_key = Pojo_Core::instance()->licenses->get_license_key();
		
		$install_plugins_link = add_query_arg( 'page', TGM_Plugin_Activation::get_instance()->menu, admin_url( 'admin.php' ) );
		
		$installed_plugins = get_plugins();
		$pojo_importer_plugin_path = 'pojo-importer/pojo-importer.php';
		?>
		<div class="wrap about-wrap pojo-home-setting">

			<header>
				<h1><?php _e( 'Welcome to Pojo', 'pojo' ); ?></h1>

				<p class="about-text"><?php
					echo strtr(
						__( '<strong>Congratulations!</strong> Thank you for installing the {THEME_NAME} {VERSION}. We hope you will enjoy it, as the world of easy and fast web designing is now open to you!', 'pojo' ),
						array(
							'{THEME_NAME}' => $theme_name,
							'{VERSION}' => $theme_version,
						)
					);
					?></p>

				<div class="wp-badge pojo-badge">
					<div class="pojo-theme"><?php echo $theme_name; ?></div>
					<div class="pojo-version"><?php echo $theme_version; ?></div>
				</div>
			</header>

			<div class="content">

				<div class="install-section three-col">

					<p class="install-description"><?php _e( 'If this is your first time using our theme, we are happy to show you a few quick links that will help you learn more about using it.', 'pojo' ); ?></p>

					<div class="col">
						<h4><?php _e( '1. Activating Your License Key', 'pojo' ); ?></h4>
						<p><?php _e( 'To get started, enter the License Key you\'ve received during the purchase. The License Key also appears in your account\'s purchase area on our website. Once it\'s entered activate it, and then you\'ll receive all our automatic updates.', 'pojo' ); ?></p>
						
						<?php if ( empty( $license_key ) ) : ?>
						<a class="button button-primary" href="<?php echo Pojo_Core::instance()->licenses->settings->get_setting_page_link(); ?>"><?php _e( 'Activate the License Key', 'pojo' ); ?></a>
						<?php else : ?>
						<div class="success-massage"><span class="dashicons dashicons-yes"></span> <?php _e( 'The License Key has been Successfully Activated', 'pojo' ); ?></div>
						<?php endif; ?>
					</div>
					
					<div class="col">
						<h4><?php _e( '2. Installing the Extensions', 'pojo' ); ?></h4>
						<p><?php _e( 'Before moving on to customizing the theme to your website, activate our extensions to receive more features that will make your website even better.', 'pojo' ); ?></p>
						<?php if ( TGM_Plugin_Activation::get_instance()->has_none_activate_plugins ) : ?>
						<a class="button button-primary" href="<?php echo $install_plugins_link; ?>"><?php _e( 'Install the Extensions', 'pojo' ); ?></a>
						<?php else : ?>
						<div class="success-massage"><span class="dashicons dashicons-yes"></span> <?php _e( 'All the Extensions have been Successfully Installed', 'pojo' ); ?></div>
						<?php endif; ?>
					</div>
					
					<div class="col">
						<h4><?php _e( '3. Importing the Demo Content', 'pojo' ); ?></h4>
						<p><?php _e( 'You can create a website that will look like our demo, in a just a few clicks. In our Demo Import page you can import all the demo\'s content, widgets, menus, customizer and front page settings.', 'pojo' ); ?></p>
						<?php if ( ! isset( $installed_plugins[ $pojo_importer_plugin_path ] ) ) : ?>
						<a class="button button-primary" href="<?php echo $install_plugins_link; ?>"><?php _e( 'Install Pojo Importer', 'pojo' ); ?></a>
						<?php elseif ( is_plugin_inactive( $pojo_importer_plugin_path ) ) : ?>
						<a class="button button-primary" href="<?php echo $install_plugins_link; ?>"><?php _e( 'Activate Pojo Importer', 'pojo' ); ?></a>
						<?php elseif ( 'true' !== get_option( 'pojo_has_import_content_data_' . strtolower( $theme_name ) ) ) : ?>
						<a class="button button-primary" href="<?php echo add_query_arg( 'page', 'pojo-importer', admin_url( 'admin.php' ) ); ?>"><?php _e( 'Go to our Demo Import Page', 'pojo' ); ?></a>
						<?php else : ?>
						<div class="success-massage"><span class="dashicons dashicons-yes"></span> <?php _e( 'The Demo Content has been Successfully Imported', 'pojo' ); ?></div>
						<?php endif; ?>

					</div>
				</div>

				<div class="support-section three-col">
					<div class="col">
						<h4><span class="dashicons dashicons-category"></span> <?php _e( 'Child Theme', 'pojo' ); ?></h4>
						<p><?php _e( 'If you\'re planning on making changes in our theme that are beyond the existing Customizer settings, the Child Theme brings you the perfect solution.', 'pojo' ); ?></p>
						<p><?php _e( 'We provide in all our themes an adjusted Child Theme which allows you to make any changes in the code files and add countless modifications to your website.', 'pojo' ); ?></p>
						<a class="go-to-page" href="<?php esc_attr_e( 'http://support.pojo.me/docs/child-theme/?utm_source=dashboard&utm_medium=link&utm_campaign=home', 'pojo' ); ?>"><?php _e( 'For More Info', 'pojo' ); ?> »</a>
					</div>
					<div class="col">
						<h4><span class="dashicons dashicons-megaphone"></span> <?php _e( 'Support Forum', 'pojo' ); ?></h4>
						<p><?php _e( 'Have a question or problem? We are here to help! Our technical support team will help you with any question via the Support Forum.', 'pojo' ); ?></p>
						<p><?php _e( 'To do so, just login to the account in which you made your purchase. Once you\'re in your account you can open a new topic or add a comment.', 'pojo' ); ?></p>
						<a class="go-to-page" href="<?php esc_attr_e( 'http://support.pojo.me/forum/support/?utm_source=dashboard&utm_medium=link&utm_campaign=home', 'pojo' ); ?>"><?php _e( 'To the Support Forum', 'pojo' ); ?> »</a>
					</div>
					<div class="col">
						<h4><span class="dashicons dashicons-welcome-learn-more"></span> <?php _e( 'Documentation', 'pojo' ); ?></h4>
						<p><?php _e( 'In the Documentation area you can find helping guides, tips, troubleshooting and more information regarding the features that are included in our Framework.', 'pojo' ); ?></p>
						<p><?php _e( 'If you can\'t find the information you\'re looking for, you\'re invited to move on to our Support Forum for more information.', 'pojo' ); ?></p>
						<a class="go-to-page" href="<?php esc_attr_e( 'http://support.pojo.me/documentation/?utm_source=dashboard&utm_medium=link&utm_campaign=home', 'pojo' ); ?>"><?php _e( 'To our Documentation', 'pojo' ); ?> »</a>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	public function redirect_to_home_when_activated() {
		if ( isset( $_GET['activated'] ) ) {
			wp_redirect(
				add_query_arg(
					array(
						'page' => $this->_page_id,
					),
					admin_url( 'admin.php' )
				)
			);
			die;
		}
	}

	public function __construct() {
		if ( ! current_user_can( $this->_capability ) )
			return;
		
		add_action( 'admin_menu', array( &$this, 'register_menu' ), 19 );
		add_action( 'admin_init', array( &$this, 'redirect_to_home_when_activated' ) );
	}
	
}