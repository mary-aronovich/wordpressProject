<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Login_Style {

	public function __construct() {
		$this->add_actions();
		$this->add_filters();
	}

	private function add_actions() {
		add_filter( 'login_body_class', array( $this, 'login_style' ) );
	}

	private function add_filters() {
		add_filter( 'login_headerurl', array( $this, 'login_logo_url' ) );
		add_filter( 'login_headertitle', array( $this, 'login_logo_title' ) );

		add_filter( 'pojo_register_settings_sections', array( $this, 'setting_section_login_style' ), 90 );
	}

	public function setting_section_login_style( $sections = array() ) {
		$fields = array();

		$fields[] = array(
			'id' => 'login_style_image',
			'type' => Pojo_Settings::FIELD_IMAGE,
			'title' => __( 'Logo Image', 'pojo' ),
			'class' => 'pojo-login-style-logo-image',
			'desc' => __( 'Image size recommended: 320px / 100px', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'login_style_image_url',
			'type' => Pojo_Settings::FIELD_URL,
			'placeholder' => __( 'http://pojo.me', 'pojo' ),
			'title' => __( 'Logo URL', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'login_style_image_alt',
			'type' => Pojo_Settings::FIELD_TEXT,
			'title' => __( 'Logo Alt', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'login_style_background_image',
			'type' => Pojo_Settings::FIELD_IMAGE,
			'title' => __( 'Background Image', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'login_style_background_color',
			'type' => Pojo_Settings::FIELD_TEXT,
			'placeholder' => '#f1f1f1',
			'title' => __( 'Background Color', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'login_style_background_position',
			'type' => Pojo_Settings::FIELD_SELECT,
			'title' => __( 'Background Position', 'pojo' ),
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
		);

		$fields[] = array(
			'id' => 'login_style_background_repeat',
			'type' => Pojo_Settings::FIELD_SELECT,
			'title' => __( 'Background Repeat', 'pojo' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'no-repeat' => __( 'no-repeat', 'pojo' ),
				'repeat' => __( 'repeat', 'pojo' ),
				'repeat-x' => __( 'repeat-x', 'pojo' ),
				'repeat-y' => __( 'repeat-y', 'pojo' ),
			),
		);

		$fields[] = array(
			'id' => 'login_style_background_size',
			'type' => Pojo_Settings::FIELD_SELECT,
			'title' => __( 'Background Size', 'pojo' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'auto' => __( 'Auto', 'pojo' ),
				'cover' => __( 'Cover', 'pojo' ),
			),
		);

		$fields[] = array(
			'id' => 'login_style_background_attachment',
			'type' => Pojo_Settings::FIELD_SELECT,
			'title' => __( 'Background Attachment', 'pojo' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'scroll' => __( 'Scroll', 'pojo' ),
				'fixed' => __( 'fixed', 'pojo' ),
			),
		);

		$sections[] = array(
			'id' => 'section-login-style',
			'page' => 'pojo-general',
			'title' => __( 'Login Style', 'pojo' ),
			'intro' => __( 'Here you can personally customize your wp-login. Simply add your own logo, link and background to the login screen.', 'pojo' ),
			'fields' => $fields,
		);

		return $sections;
	}

	public function login_style( $classes ) {
		$style_creator = new Pojo_Create_CSS_Code();

		$background_style_keys = array(
			'color',
			'repeat',
			'size',
			'position',
			'attachment',
		);

		foreach ( $background_style_keys as $style_key ) {
			$option = pojo_get_option( 'login_style_background_' . $style_key );
			$style_creator->add_value( 'body', 'background-' . $style_key, $option );
		}

		$bg_image = pojo_get_option( 'login_style_background_image' );
		if ( ! empty( $bg_image ) ) {
			$style_creator->add_selector( 'body', sprintf( 'background-image: url("%s");', $bg_image ) );
		}

		$logo_image = pojo_get_option( 'login_style_image' );
		if ( ! empty( $logo_image ) ) {
			$style_creator->add_data( '.login h1 a { background-image: url("' . esc_attr( $logo_image ) . '"); width: 100%; background-size: 100% 100%; height: 100px; }' );
		}

		$css_code = $style_creator->get_css_code();
		if ( ! empty( $css_code ) ) : ?>
		<style type="text/css">
			<?php echo $css_code; ?>
		</style>
		<?php endif;
		return $classes;
	}

	public function login_logo_url( $url ) {
		$custom_url = pojo_get_option( 'login_style_image_url' );
		if ( $custom_url ) {
			return $custom_url;
		}
		return $url;
	}

	public function login_logo_title( $title ) {
		$custom_title = pojo_get_option( 'login_style_image_alt' );
		if ( $custom_title ) {
			return $custom_title;
		}
		return $title;
	}
}
