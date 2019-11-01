<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'TGM_Plugin_Activation' ) )
	include( 'class-tgm-plugin-activation.php' );

/**
 * Class AT_Plugin_Activation
 */
class Pojo_Plugin_Activation {

	/**
	 * Plugin folder.
	 *
	 * @var string
	 */
	protected $_plugins_dir = '';

	/**
	 * Register theme plugins.
	 */
	public function register_plugins() {
		$plugins = array(

			// Elementor
			array(
				'name' => 'Elementor', // The plugin name
				'slug' => 'elementor',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),

			// Pojo Importer
			array(
				'name' => 'Pojo Importer', // The plugin name
				'slug' => 'pojo-importer',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),

			// Pojo Forms
			array(
				'name' => 'Pojo Forms', // The plugin name
				'slug' => 'pojo-forms',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),

			// Pojo Accessibility
			array(
				'name' => 'One Click Accessibility', // The plugin name
				'slug' => 'pojo-accessibility',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),
			
			// Pojo Lightbox
			array(
				'name' => 'Pojo Lightbox', // The plugin name
				'slug' => 'pojo-lightbox',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),
			
			// Pojo Sidebars
			array(
				'name' => 'Pojo Sidebars', // The plugin name
				'slug' => 'pojo-sidebars',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),
			
			// Pojo Custom Fonts
			array(
				'name' => 'Pojo Custom Fonts', // The plugin name
				'slug' => 'pojo-custom-fonts',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),
			
			// Pojo News Ticker
			array(
				'name' => 'Pojo News Ticker', // The plugin name
				'slug' => 'pojo-news-ticker',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),

			// Activity Log
			array(
				'name' => 'Activity Log', // The plugin name
				'slug' => 'aryo-activity-log',
				'required' => false, // If false, the plugin is only 'recommended' instead of required
				'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			),
		);

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'default_path' => '', // Default absolute path to pre-packaged plugins.
			'menu' => 'install-required-plugins', // Menu slug.
			'has_notices' => true, // Show admin notices or not.
			'dismissable' => true, // If false, a user cannot dismiss the nag message.
			'dismiss_msg' => '', // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false, // Automatically activate plugins after installation or not.
			'message' => '', // Message to output right before the plugins table.
			'strings' => array(
				'page_title' => __( 'Install Required Plugins', 'pojo' ),
				'menu_title' => __( 'Install Plugins', 'pojo' ),
				'installing' => __( 'Installing Plugin: %s', 'pojo' ), // %s = plugin name.
				'oops' => __( 'Something went wrong with the plugin API.', 'pojo' ),
				'notice_can_install_required' => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'pojo' ), // %1$s = plugin name(s).
				'notice_can_install_recommended' => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'pojo' ), // %1$s = plugin name(s).
				'notice_cannot_install' => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'pojo' ), // %1$s = plugin name(s).
				'notice_can_activate_required' => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'pojo' ), // %1$s = plugin name(s).
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'pojo' ), // %1$s = plugin name(s).
				'notice_cannot_activate' => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'pojo' ), // %1$s = plugin name(s).
				'notice_ask_to_update' => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'pojo' ), // %1$s = plugin name(s).
				'notice_cannot_update' => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'pojo' ), // %1$s = plugin name(s).
				'install_link' => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'pojo' ),
				'activate_link' => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'pojo' ),
				'return' => __( 'Return to Required Plugins Installer', 'pojo' ),
				'plugin_activated' => __( 'Plugin activated successfully.', 'pojo' ),
				'activated_successfully' => __( 'The following plugin was activated successfully:', 'pojo' ),
				'complete' => __( 'All plugins installed and activated successfully. %s', 'pojo' ), // %s = dashboard link.
				'nag_type' => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
		);

		$config = apply_filters( 'pojo_tgmpa_config_array', $config );

		tgmpa( $plugins, $config );
	}

	public function __construct() {
		$this->_plugins_dir = get_template_directory() . '/core/addons/plugins/files/';
		add_action( 'tgmpa_register', array( &$this, 'register_plugins' ) );
	}
}
new Pojo_Plugin_Activation();
