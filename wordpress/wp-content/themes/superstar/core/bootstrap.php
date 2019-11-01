<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'POJO_CORE_VERSION', '1.8.1' );

define( 'POJO_CORE_DIRECTORY', get_template_directory() . '/core' );
define( 'POJO_INCLUDES_DIRECTORY', get_template_directory() . '/includes' );

final class Pojo_Core {

	private static $instance;

	/**
	 * @var Pojo_Settings
	 */
	public $settings;

	/**
	 * @var Pojo_Page_Options
	 */
	public $page_options;

	/**
	 * @var Pojo_Page_Format
	 */
	public $page_format;

	/**
	 * @var Pojo_Page_Builder
	 */
	public $builder;

	/**
	 * @var Pojo_Theme_Customize
	 */
	public $customizer;

	/**
	 * @var Pojo_Admin_UI
	 */
	public $admin_ui;

	/**
	 * @var Pojo_Login_Style
	 */
	public $login_screen;

	/**
	 * @var Pojo_Licenses
	 */
	public $licenses;

	/**
	 * @var Pojo_Menus
	 */
	public $menus;

	/**
	 * @var Pojo_Theme_Sidebars
	 */
	public $sidebars;

	/**
	 * @var Pojo_System_Info
	 */
	public $systeminfo;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Pojo_Core();
		}

		return self::$instance;
	}

	public function get_version() {
		return POJO_CORE_VERSION;
	}

	public function get_css_framework_type() {
		$default = 'bootstrap';

		if ( defined( 'POJO_CSS_FRAMEWORK_TYPE' ) ) {
			return POJO_CSS_FRAMEWORK_TYPE;
		}

		return $default;
	}

	public function includes() {
		// Include all our tools.
		include( POJO_CORE_DIRECTORY . '/config.php' );
		include( POJO_INCLUDES_DIRECTORY . '/theme-config.php' );

		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-maintenance.php' );

		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-embed-template.php' );

		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-masterslider.php' );

		// Compatibility plugins.
		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-compatibility.php' );

		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-thumbnails.php' );

		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-layouts.php' );

		// Web Fonts
		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-web-fonts.php' );

		// Breadcrumbs.
		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-breadcrumbs.php' );

		// Simple walker for menu with Bootstrap classes.
		include( POJO_CORE_DIRECTORY . '/helpers/walkers/class-pojo-navbar-nav-walker.php' );
		include( POJO_CORE_DIRECTORY . '/helpers/walkers/class-pojo-navbar-nav-mobile-walker.php' );

		// Menu dropdown. Good for mobile menu.
		include( POJO_CORE_DIRECTORY . '/helpers/walkers/class-pojo-nav-menu-dropdown-walker.php' );

		// Help class for create css code.
		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-create-css-code.php' );

		// Admin UI
		include( POJO_CORE_DIRECTORY . '/admin-ui.php' );

		include( POJO_CORE_DIRECTORY . '/helpers/class-pojo-templates.php' );

		include( POJO_CORE_DIRECTORY . '/helpers/deprecated.php' );

		// Setup things in after_setup_theme.
		include( POJO_CORE_DIRECTORY . '/setup.php' );
		include( POJO_INCLUDES_DIRECTORY . '/theme-setup.php' );

		include( POJO_INCLUDES_DIRECTORY . '/class-pojo-theme-template.php' );

		if ( is_child_theme() ) {
			$child_template_path = get_stylesheet_directory() . '/includes/class-pojo-child-template.php';
			if ( is_file( $child_template_path ) ) {
				include( $child_template_path );
			}
		}

		$template_class = 'Pojo_Child_Template';
		if ( ! class_exists( $template_class ) ) {
			$template_class = 'Pojo_Theme_Template';
		}

		new $template_class();

		include( POJO_INCLUDES_DIRECTORY . '/class-pojo-customize-register-fields.php' );

		// Units.
		include( POJO_CORE_DIRECTORY . '/units.php' );

		include( POJO_CORE_DIRECTORY . '/customize/customize.php' );

		// Initializing Settings Framework.
		include( POJO_CORE_DIRECTORY . '/settings/settings.php' );

		// Initializing Meta-box Framework.
		include( POJO_CORE_DIRECTORY . '/meta-box/meta-box.php' );

		// Initializing Theme Widgets.
		include( POJO_CORE_DIRECTORY . '/widgets.php' );

		// Enqueue Theme Scripts.
		include( POJO_CORE_DIRECTORY . '/enqueue-scripts.php' );

		// Initializing Page Format.
		include( POJO_CORE_DIRECTORY . '/page-options/page-options.php' );

		// Initializing Smart Page.
		include( POJO_CORE_DIRECTORY . '/smart-page/smart-page.php' );

		// Initializing Page Builder.
		include( POJO_CORE_DIRECTORY . '/page-builder/page-builder.php' );

		// Initializing Page Format.
		include( POJO_CORE_DIRECTORY . '/page-format/page-format.php' );

		// Initializing Menus.
		include( POJO_CORE_DIRECTORY . '/menus/menus.php' );

		// Initializing Sidebars
		include( POJO_CORE_DIRECTORY . '/sidebars/sidebars.php' );

		// Licenses
		include( POJO_CORE_DIRECTORY . '/licenses/class-pojo-licenses.php' );

		// Addons..
		include( POJO_CORE_DIRECTORY . '/addons/post-formats/post-formats.php' );

		include( POJO_CORE_DIRECTORY . '/addons/plugins/plugin-activation.php' );

		include( POJO_CORE_DIRECTORY . '/addons/galleries/class-pojo-gallery.php' );

		include( POJO_CORE_DIRECTORY . '/addons/slideshow/class-pojo-slideshow.php' );

		include( POJO_CORE_DIRECTORY . '/addons/menu-search/menu-search.php' );

		include( POJO_CORE_DIRECTORY . '/addons/advanced-widget-title/class-widget-title.php' );

		include( POJO_CORE_DIRECTORY . '/addons/scroll-up/scroll-up.php' );

		include( POJO_CORE_DIRECTORY . '/addons/login-screen/class-pojo-login-style.php' );

		// System Info
		include( POJO_CORE_DIRECTORY . '/addons/system-info/system-info.php' );

		if ( Pojo_Compatibility::is_woocommerce_installed() ) {
			include( POJO_CORE_DIRECTORY . '/addons/wc-integration/class-pojo-wc-integration.php' );
		}

		include( POJO_CORE_DIRECTORY . '/addons/elementor/elementor.php' );
	}

	public function init() {
		$this->settings     = new Pojo_Settings();
		$this->page_options = new Pojo_Page_Options();
		$this->page_format  = new Pojo_Page_Format();
		$this->builder      = new Pojo_Page_Builder();
		$this->customizer   = new Pojo_Theme_Customize();
		$this->menus        = new Pojo_Menus();
		$this->sidebars     = new Pojo_Theme_Sidebars();
		$this->admin_ui     = new Pojo_Admin_UI();
		$this->systeminfo   = new Pojo_System_Info();
		$this->licenses     = new Pojo_Licenses();
		$this->login_screen = new Pojo_Login_Style();

		do_action( 'pojo_theme_loaded' );
	}

	private function __construct() {
		$this->includes();
		$this->init();

		// X-UA-Compatible W3 Valid Fix.
		if ( ! headers_sent() ) {
			header( 'X-UA-Compatible: IE=edge,chrome=1' );
		}
	}

	/**
	 * Throw error on object clone
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo' ), '1.1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo' ), '1.1.0' );
	}

}
Pojo_Core::instance();
