<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_System_Info {

	private $capability = 'manage_options';

	/**
	 * @var array
	 */
	private $settings = array();

	public function __construct() {
		$this->require_files();
		$this->init_settings();
		$this->add_actions();
	}

	private function require_files() {
		require POJO_CORE_DIRECTORY . '/addons/system-info/classes/class-pojo-info-base-reporter.php';
		require POJO_CORE_DIRECTORY . '/addons/system-info/helpers/class-pojo-info-model-helper.php';
	}

	/**
	 * @param array $properties
	 *
	 *@return WP_Error|false|Pojo_Info_Base_Reporter
	 */
	public function create_reporter( array $properties ) {
		$properties = Pojo_Model_Helper::prepare_properties( $this->get_settings( 'reporterProperties' ), $properties );

		$reporter_class = $properties['className'] ? $properties['className'] : $this->get_reporter_class( $properties['name'] );

		$reporter = new $reporter_class( $properties );

		if ( ! ( $reporter instanceof Pojo_Info_Base_Reporter ) ) {
			return new WP_Error( 'Each reporter must to be an instance or sub-instance of Pojo_Info_Base_Reporter class' );
		}

		if(! $reporter->is_enabled()){
			return false;
		}

		return $reporter;
	}

	private function add_actions() {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 501 );

		add_action( 'wp_ajax_pojo_system_info_download_file', array( $this, 'download_file' ) );
	}

	public function display_page() {
		$reportsInfo = self::get_allowed_reports();

		$reports = $this->load_reports( $reportsInfo );
		
		?>
		<div id="pojo-system-info">
			<h3><?php _e( 'System Info', 'pojo' ); ?></h3>
			<div><?php $this->print_report( $reports, 'html' ); ?></div>
			<h3><?php _e('Copy & Paste Info', 'pojo'); ?></h3>
			<div id="pojo-system-info-raw">
				<label id="pojo-system-info-raw-code-label"
				       for="pojo-system-info-raw-code"><?php echo __( 'You can copy the below info as simple text with Ctrl+C / Ctrl+V:', 'pojo' ) ?></label>
				<textarea id="pojo-system-info-raw-code"
				          readonly><?php $this->print_report( $reports, 'raw' ); ?></textarea>
				<script>
					var textarea = document.getElementById( 'pojo-system-info-raw-code' );
					var selectRange = function () {
						textarea.setSelectionRange( 0, textarea.value.length );
					};
					textarea.onfocus = textarea.onblur = textarea.onclick = selectRange;
					textarea.onfocus();
				</script>
			</div>
			<hr>
			<form action="<?php echo admin_url( 'admin-ajax.php' ) ?>" method="post">
				<input type="hidden" name="action" value="pojo_system_info_download_file">
				<input type="submit" class="button button-primary" value="<?php _e( 'Download System Info', 'pojo' ); ?>">
			</form>
		</div>
		<?php
	}

	public function download_file() {
		if ( ! current_user_can( $this->capability ) ) {
			wp_die( __( 'You don\'t have a permission to download this file', 'pojo' ) );
		}

		$reports_info = self::get_allowed_reports();
		$reports = $this->load_reports( $reports_info );

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition:attachment; filename=system-info-' . $_SERVER['HTTP_HOST'] . '-' . date( 'd-m-Y' ) . '.txt' );

		$this->print_report( $reports );

		die;
	}

	public function get_reporter_class( $reporter_type ) {
		return 'Pojo_' . ucfirst( $reporter_type ) . '_Reporter';
	}

	public function load_reports( $reports ) {
		$result = array();

		$settings = $this->get_settings();

		foreach ( $reports as $report_name => $report_info ) {
			require_once $settings['dirs']['classes'] . $settings['reportFilePrefix'] . $report_name . '.php';

			$reporter_params = array(
				'name' => $report_name,
			);

			$reporter = $this->create_reporter( $reporter_params );

			if(! $reporter instanceof Pojo_Info_Base_Reporter){
				continue;
			}

			$result[ $report_name ] = array(
				'report' => $reporter->get_report(),
				'label' => $reporter->get_title(),
			);

			if ( ! empty($report_info['sub']) ) {
				$result[ $report_name ]['sub'] = $this->load_reports( $report_info['sub'] );
			}
		}

		return $result;
	}

	public function print_report( $reports, $template = 'raw' ) {
		static $tabs_count = 0;

		static $required_plugins_properties = array(
			'Name',
			'Version',
			'URL',
			'Author',
		);

		$templatePath = $this->get_settings( 'dirs.templates' ) . $template . '.php';

		require $templatePath;
	}

	public function register_menu() {
		$system_info_text = __( 'System Info', 'pojo' );

		add_submenu_page(
			'pojo-home',
			$system_info_text,
			$system_info_text,
			$this->capability,
			'pojo-system-info',
			array( $this, 'display_page' )
		);
	}

	protected function get_default_settings() {
		$settings = array();

		$reporter_properties = Pojo_Info_Base_Reporter::get_properties_keys();
		array_push( $reporter_properties, 'category', 'name', 'className' );
		$settings['reporterProperties'] = $reporter_properties;

		$base_lib_dir = POJO_CORE_DIRECTORY . '/addons/system-info/';
		$settings['dirs'] = array(
			'lib'       => $base_lib_dir,
			'templates' => $base_lib_dir . 'templates/',
			'classes'   => $base_lib_dir . 'classes/',
		);

		$settings['reportFilePrefix'] = 'class-pojo-info-';
		return $settings;
	}

	private function init_settings() {
		$this->settings = $this->get_default_settings();
	}

	/**
	 * @param string $setting
	 * @param array $container
	 *
	 * @return mixed
	 */
	public final function get_settings( $setting = null, array $container = null ) {
		if ( ! $container ) {
			$container = $this->settings;
		}

		if ( $setting ) {
			$setting_thread = explode( '.', $setting );
			$parent_thread = array_shift( $setting_thread );

			if ( $setting_thread ) {
				return $this->get_settings( implode( '.', $setting_thread ), $container[ $parent_thread ] );
			}

			return $container[ $parent_thread ];
		}
		return $container;
	}

	public static function get_allowed_reports() {
		return array(
			'server' => array(),
			'wordpress' => array(),
			'theme' => array(),
			'user' => array(),
			'plugins' => array(),
			'network_plugins' => array(),
			'mu_plugins' => array(),
		);
	}
}