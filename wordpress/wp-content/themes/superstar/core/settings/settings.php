<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings {

	const FIELD_TEXT     = 'text';
	const FIELD_URL     = 'url';
	const FIELD_TEXTAREA = 'textarea';
	const FIELD_SELECT   = 'select';
	const FIELD_CHECKBOX = 'checkbox';
	const FIELD_RADIO    = 'radio';

	const FIELD_CHECKBOX_LIST = 'checkbox_list';
	
	const FIELD_IMAGE = 'image';
	const FIELD_RADIO_IMAGE = 'radio_image';

	const FIELD_SELECT_PAGES         = 'select_pages';
	const FIELD_SELECT_LANGUAGES     = 'select_languages';
	const FIELD_SELECT_START_OF_WEEK = 'select_start_of_week';

	const FIELD_COLOR = 'color';

	protected $_fields_list = array();

	/**
	 * @var Pojo_Settings_Field_Base[]
	 */
	protected $_fields = array();

	protected $_sections = array();
	protected $_defaults = array();
	protected $_pages    = array();

	protected function _get_field_class( $name ) {
		return 'Pojo_Settings_Field_' . ucwords( $name );
	}
	
	public function get_settings_pages() {
		if ( empty( $this->_pages ) ) {
			$this->_pages = apply_filters( 'pojo_register_settings_pages', array() );
		}
		return $this->_pages;
	}
	
	public function get_settings_sections() {
		if ( empty( $this->_sections ) ) {
			$this->_sections = apply_filters( 'pojo_register_settings_sections', array() );
		}
		return $this->_sections;
	}

	public function get_default_value( $key ) {
		if ( empty( $this->_defaults ) ) {
			foreach ( $this->get_settings_sections() as $section ) {
				if ( empty( $section['fields'] ) )
					continue;

				foreach ( $section['fields'] as $field ) {
					if ( isset( $field['std'] ) )
						$this->_defaults[ $field['id'] ] = $field['std'];
				}
			}
		}
		
		if ( isset( $this->_defaults[ $key ] ) )
			return $this->_defaults[ $key ];
		
		return false;
	}
	
	public function init() {
		include( 'settings-validations.php' );

		// Pages.
		include( 'pages/page-base.php' );
		include( 'pages/home.php' );
		include( 'pages/general.php' );
		include( 'pages/content.php' );
		include( 'pages/tools.php' );

		new Pojo_Settings_Page_Home();
		new Pojo_Settings_Page_General( 10 );
		new Pojo_Settings_Page_Content( 20 );

		new Pojo_Settings_Page_Tools();

		do_action( 'pojo_framework_base_settings_included' );
	}

	public function admin_init() {
		$sections = $this->get_settings_sections();
		if ( empty( $sections ) )
			return;

		if ( ! class_exists( 'Pojo_Settings_Field_Base' ) )
			include( 'fields/base.php' );
		
		foreach ( $this->_fields_list as $field_slug ) {
			$class_name = $this->_get_field_class( $field_slug );
			if ( ! class_exists( $class_name ) )
				include( 'fields/' . $field_slug . '.php' );
			
			$this->_fields[ $field_slug ] = new $class_name();
		}
		
		foreach ( $this->get_settings_sections() as $section_key => $section ) {
			add_settings_section(
				$section_key,
				$section['title'],
				array( &$this, 'add_settings_section' ),
				$section['page']
			);
			
			if ( empty( $section['fields'] ) )
				continue;
			
			foreach ( $section['fields'] as $field ) {
				add_settings_field(
					$field['id'],
					$field['title'],
					array( &$this, 'add_settings_field' ),
					$section['page'],
					$section_key,
					$field
				);

				$sanitize_callback = array( 'Pojo_Settings_Validations', 'field_html' );
				if ( ! empty( $field['type'] ) && self::FIELD_CHECKBOX_LIST === $field['type'] ) {
					$sanitize_callback = array( 'Pojo_Settings_Validations', 'field_checkbox_list' );
				}
				if ( ! empty( $field['sanitize_callback'] ) ) {
					$sanitize_callback = $field['sanitize_callback'];
				}
				
				register_setting( $section['page'], $field['id'], $sanitize_callback );
			}
		}
	}
	
	public function admin_menu() {
		foreach ( $this->get_settings_pages() as $page_id => $page_data ) {
			$page_data = wp_parse_args(
				$page_data,
				array(
					'type' => 'top',
					'parent' => '',
					'title' => '',
					'menu_title' => '',
					'capability' => 'manage_options',
					'position' => null,
				)
			);
			if ( 'submenu' === $page_data['type'] ) {
				add_submenu_page(
					$page_data['parent'],
					$page_data['title'],
					$page_data['menu_title'],
					$page_data['capability'],
					$page_id,
					array( &$this, 'display_settings_page' )
				);
			} else {
				add_menu_page(
					$page_data['title'],
					$page_data['menu_title'],
					$page_data['capability'],
					$page_id,
					array( &$this, 'display_settings_page' ),
					'',
					$page_data['position']
				);
			}
		}
	}
	
	public function display_settings_page() {
		if ( empty( $_GET['page'] ) || empty( $this->_pages[ $_GET['page'] ] ) )
			return;

		$page_id   = $_GET['page'];
		$page_data = $this->_pages[ $_GET['page'] ];
		?>
		<div class="wrap">

			<div id="icon-themes" class="icon32"></div>
			<h2><?php echo $page_data['title']; ?></h2>
			<?php settings_errors( $page_id ); ?>
			<form method="post" action="options.php">
				<?php
				settings_fields( $page_id );
				do_settings_sections( $page_id );

				submit_button();
				?>
			</form>

		</div><!-- /.wrap -->
		<?php
	}
	
	public function add_settings_section( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'id' => '',
			'title' => '',
		) );
		
		if ( empty( $this->_sections[ $args['id'] ]['intro'] ) )
			return;
		
		printf( '<p>%s</p>', $this->_sections[ $args['id'] ]['intro'] );
	}
	
	public function add_settings_field( $args = array() ) {
		if ( empty( $args ) )
			return;
		
		$args = wp_parse_args( $args, array(
			'id' => '',
			'std' => '',
			'type' => self::FIELD_TEXT,
		) );
		
		if ( empty( $args['id'] ) || empty( $this->_fields[ $args['type'] ] ) )
			return;
		
		$this->_fields[ $args['type'] ]->render( $args );
	}
	
	public function __construct() {
		// Hooks.
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ), 20 );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 20 );

		$this->_fields_list = array(
			self::FIELD_TEXT,
			self::FIELD_URL,
			self::FIELD_TEXTAREA,
			self::FIELD_SELECT,
			self::FIELD_CHECKBOX,
			
			self::FIELD_CHECKBOX_LIST,
			
			self::FIELD_IMAGE,
			self::FIELD_RADIO_IMAGE,
			self::FIELD_SELECT_PAGES,
			
			self::FIELD_SELECT_LANGUAGES,
			self::FIELD_SELECT_START_OF_WEEK,

			self::FIELD_COLOR,
		);
	}
	
}

function pojo_get_option( $key ) {
	return get_option( $key, Pojo_Core::instance()->settings->get_default_value( $key ) );
}