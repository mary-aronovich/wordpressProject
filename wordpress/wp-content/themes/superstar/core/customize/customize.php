<?php
/**
 * Theme Customize.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Theme_Customize {

	const FIELD_TEXT = 'text';
	const FIELD_TEXTAREA = 'textarea';
	const FIELD_SELECT = 'select';
	const FIELD_CHECKBOX = 'checkbox';
	const FIELD_RADIO = 'radio';
	const FIELD_COLOR = 'color';
	const FIELD_IMAGE = 'image';
	const FIELD_TYPOGRAPHY = 'typography';
	const FIELD_BACKGROUND = 'background';
	const FIELD_RADIO_IMAGE = 'radio_image';
	const FIELD_TWO_COLOR = 'two_color';
	const FIELD_DROPDOWN_PAGES = 'dropdown_pages';
	const FIELD_SELECT_SIDEBAR = 'select_sidebar';

	protected $_fields = array();
	protected $_sections = array();
	
	protected $_transport_fields = array();
	protected static $_control_priority = 20;
	protected static $_section_priority = 20;

	protected function _get_field_class( $name ) {
		return 'Pojo_Customize_Control_Field_' . ucwords( $name );
	}
	
	public function get_field_transport_type( $field = array() ) {
		$exclude_post_message = array( 'text_selection', 'bg_selection' );
		if ( ! empty( $field['selector'] ) && ! empty( $field['change_type'] ) && ! in_array( $field['change_type'], $exclude_post_message ) && true !== $field['skip_transport'] )
			return 'postMessage';
		return 'refresh';
	}
	
	public function get_theme_sections() {
		if ( empty( $this->_sections ) ) {
			$this->_sections = apply_filters( 'pojo_register_customize_sections', array() );
		}
		
		return $this->_sections;
	}
	
	public static function get_control_priority() {
		return self::$_control_priority++;
	}
	
	public static function get_section_priority() {
		return self::$_section_priority++;
	}
	
	public function init() {
		$mods = get_theme_mods();
		if ( isset( $mods[0] ) && ! $mods[0] )
			remove_theme_mod( 0 );
		unset( $mods['sidebars_widgets'] );
		if ( empty( $mods ) ) {
			$this->sync_theme_mod();
		}
	}
	
	public function wp_head() {
		$sections = $this->get_theme_sections();
		if ( empty( $sections ) )
			return;

		$google_fonts = $google_early_access_fonts = array();
		$css_code     = new Pojo_Create_CSS_Code();

		foreach ( $sections as $section ) {
			if ( empty( $section['fields'] ) )
				continue;

			foreach ( $section['fields'] as $field ) {
				if ( empty( $field['selector'] ) || empty( $field['change_type'] ) )
					continue;
				
				$option = get_theme_mod( $field['id'], $field['std'] );
				if ( empty( $option ) )
					continue;
				
				if ( 'typography' === $field['change_type'] ) {
					$option = wp_parse_args( $option, $field['std'] );
					$css_code->add_selector( $field['selector'], sprintf( 'color: %s; font-family: \'%s\', Arial, sans-serif; font-weight: %s; font-size: %s;', $option['color'], $option['family'], $option['weight'], $option['size'] ) );
					
					if ( ! isset( $option['line_height'] ) )
						$option['line_height'] = false;

					if ( ! empty( $option['transform'] ) )
						$css_code->add_value( $field['selector'], 'text-transform', $option['transform'] );

					if ( ! empty( $option['letter_spacing'] ) )
						$css_code->add_value( $field['selector'], 'letter-spacing', $option['letter_spacing'] );
					
					if ( ! empty( $option['style'] ) )
						$css_code->add_value( $field['selector'], 'font-style', $option['style'] );
					
					if ( isset( $field['std']['line_height'] ) && false === $field['std']['line_height'] )
						$option['line_height'] = false;
					
					$css_code->add_value( $field['selector'], 'line-height', $option['line_height'] );
					if ( 'googlefonts' === Pojo_Web_Fonts::get_font_type( $option['family'] ) )
						$google_fonts[] = $option['family'];
					elseif ( 'earlyaccess' === Pojo_Web_Fonts::get_font_type( $option['family'] ) )
						$google_early_access_fonts[] = $option['family'];
				} elseif ( 'background' === $field['change_type'] ) {
					$option = wp_parse_args( $option, $field['std'] );
					if ( ! empty( $option['image'] ) )
						$css_code->add_selector( $field['selector'], sprintf( 'background-image: url("%s");', $option['image'] ) );
					
					if ( ! empty( $option['color'] ) ) {
						$color_rgb = pojo_hex2rgb( $option['color'] );
						
						if ( ! isset( $option['opacity'] ) )
							$option['opacity'] = 100;
						
						$css_code->add_selector( $field['selector'], sprintf( 'background-color: rgba(%d, %d, %d, %s);', $color_rgb[0], $color_rgb[1], $color_rgb[2], ( $option['opacity'] / 100 ) ) );
					}
					$css_code->add_value( $field['selector'], 'background-position', $option['position'] );
					$css_code->add_value( $field['selector'], 'background-repeat', $option['repeat'] );
					$css_code->add_value( $field['selector'], 'background-size', $option['size'] );
					$css_code->add_value( $field['selector'], 'background-attachment', $option['attachment'] );
				} elseif ( 'color' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'color', $option );
				} elseif ( 'border_color' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'border-color', $option );
				} elseif ( 'border_left_color' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'border-left-color', $option );
				} elseif ( 'border_right_color' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'border-right-color', $option );
				} elseif ( 'two_color_link' === $field['change_type'] ) {
					$option = wp_parse_args( $option, $field['std'] );
					$css_code->add_value( $field['selector'], 'color', $option['color_1'] );
					$css_code->add_value( $field['selector'] . ':hover', 'color', $option['color_2'] );
				} elseif ( 'bg_color' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'background-color', $option );
				} elseif ( 'bg_position' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'background-position', $option );
				} elseif ( 'bg_repeat' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'background-repeat', $option );
				} elseif ( 'bg_image' === $field['change_type'] && ! empty( $option ) ) {
					$css_code->add_selector( $field['selector'], sprintf( 'background-image: url("%s");', $option ) );
				} elseif ( 'bg_size' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'background-size', $option );
				} elseif ( 'bg_attachment' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'background-attachment', $option );
				} elseif ( 'height' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'height', $option );
					$css_code->add_value( $field['selector'], 'line-height', $option );
				} elseif ( 'line_height' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'line-height', $option );
				} elseif ( 'width' === $field['change_type'] ) {
					$css_code->add_value( $field['selector'], 'width', $option );
				}
				
				// Margin / Padding
				foreach ( array( 'margin', 'padding' ) as $margin_or_padding ) {
					foreach ( array( 'top', 'bottom', 'left', 'right' ) as $direction ) {
						if ( $margin_or_padding . '_' . $direction === $field['change_type'] ) {
							$css_code->add_value( $field['selector'], $margin_or_padding . '-' . $direction, $option );
							break;
						}
					}
				}
				
				// Custom options.
				if ( 'text_selection' === $field['change_type'] ) {
					$css_code->add_value( '::selection', 'color', $option );
					$css_code->add_value( '::-moz-selection', 'color', $option );
				} elseif ( 'bg_selection' === $field['change_type'] ) {
					$css_code->add_value( '::selection', 'background', $option );
					$css_code->add_value( '::-moz-selection', 'background', $option );
				}
			}
		}
		
		if ( current_theme_supports( 'pojo-background-options' ) ) {
			if ( is_single() || is_page() ) {
				$selector = sprintf( 'body.postid-%1$d, body.page-id-%1$d', get_the_ID() );
				
				$bg_color = atmb_get_field( 'po_bg_color' );
				if ( ! empty( $bg_color ) ) {
					$bg_opacity = atmb_get_field( 'po_bg_opacity' );
					if ( $bg_opacity ) {
						$rgb_color = pojo_hex2rgb( $bg_color );
						$bg_color = sprintf( 'rgba(%d,%d,%d,%s)', $rgb_color[0], $rgb_color[1], $rgb_color[2], ( $bg_opacity / 100 ) );
					}
					$css_code->add_value( $selector, 'background-color', $bg_color );
				}
				$css_code->add_value( $selector, 'background-repeat', atmb_get_field( 'po_bg_repeat' ) );
				$css_code->add_value( $selector, 'background-position', atmb_get_field( 'po_bg_position' ) );
				$css_code->add_value( $selector, 'background-size', atmb_get_field( 'po_bg_size' ) );
				$css_code->add_value( $selector, 'background-attachment', atmb_get_field( 'po_bg_attachment' ) );
				
				if ( $attachment_id = atmb_get_field( 'po_bg_image' ) ) {
					if ( $attachment_image = wp_get_attachment_image_src( $attachment_id, 'full' ) ) {
						$css_code->add_value( $selector, 'background-image', sprintf( 'url("%s")', $attachment_image[0] ) );
					}
				}
			}
		}
		
		do_action_ref_array( 'pojo_wp_head_custom_css_code', array( $css_code ) );

		$google_fonts = array_unique( $google_fonts );
		if ( ! empty( $google_fonts ) ) {
			foreach ( $google_fonts as &$font ) {
				$font = str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
			}
			$fonts_url = sprintf( 'https://fonts.googleapis.com/css?family=%s', implode( '|', $google_fonts ) );
			
			if ( 'he-IL' === get_bloginfo( 'language' ) ) {
				$fonts_url .= '&subset=hebrew';
			}
			printf( '<link rel="stylesheet" type="text/css" href="' . $fonts_url . '">' );
		}

		$google_early_access_fonts = array_unique( $google_early_access_fonts );
		if ( ! empty( $google_early_access_fonts ) ) {
			foreach ( $google_early_access_fonts as $current_font ) {
				printf( '<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/earlyaccess/%s.css">', strtolower( str_replace( ' ', '', $current_font ) ) );
			}
		}

		$css_code_content = $css_code->get_css_code();
		$custom_css = get_theme_mod( 'pojo_custom_css' );
		if ( ! empty( $custom_css ) )
			$css_code_content .= $custom_css;
		
		if ( ! empty( $css_code_content ) ) :
			?><style type="text/css"><?php echo $css_code_content; ?></style>
		<?php endif;
	}

	public function customize_register( WP_Customize_Manager $wp_customize ) {
		$sections = $this->get_theme_sections();
		if ( empty( $sections ) )
			return;

		if ( ! empty( $this->_fields ) ) {
			if ( ! class_exists( 'Pojo_Customize_Control_Field_Base' ) )
				include( 'fields/base.php' );
			
			foreach ( $this->_fields as $field ) {
				$field_class = $this->_get_field_class( $field );
				if ( ! class_exists( $field_class ) )
					include( 'fields/' . $field . '.php' );
			}
		}
		
		$remove_core_fields = apply_filters(
			'pojo_customizer_remove_core_ids',
			array(
				//'show_on_front',
				//'page_for_posts',
				//'blogdescription',
				//'page_on_front',
				//'blogname',
			)
		);
		
		foreach ( $remove_core_fields as $remove_core_field_id ) {
			$wp_customize->remove_control( $remove_core_field_id );
		}
		
		foreach ( $sections as $section ) {
			if ( empty( $section['fields'] ) )
				continue;

			$wp_customize->add_section(
				$section['id'],
				array(
					'title' => $section['title'],
					'description' => $section['desc'],
					'priority' => self::get_section_priority(),
				)
			);

			foreach ( $section['fields'] as $field ) {
				$field = wp_parse_args( $field, array(
					'type' => self::FIELD_TEXT,
					'setting_type' => 'theme_mod',
					'choices' => array(),
					'std' => '',
					'refresh' => '',
					'selector' => '',
					'change_type' => '',
					'skip_transport' => false,
				) );
				
				$field['transport'] = $this->get_field_transport_type( $field );
				
				$field_class = $this->_get_field_class( $field['type'] );
				
				$wp_customize->remove_control( $field['id'] );
				$wp_customize->add_setting(
					$field['id'],
					array(
						'default' => $field['std'],
						'transport' => $field['transport'],
						'type' => $field['setting_type'],
					)
				);
				
				$wp_customize->add_control(
					new $field_class(
						$wp_customize,
						$field['id'],
						array(
							'label' => $field['title'],
							'section' => $section['id'],
							'choices' => $field['choices'],
							'default' => $field['std'],
							'selector' => $field['selector'],
							'change_type' => $field['change_type'],
							'priority' => self::get_control_priority(),
							'transport' => $field['transport'],
						)
					)
				);
			}
		}

		//if ( $wp_customize->is_preview() && ! is_admin() )
		//	add_action( 'wp_footer', 'themename_customize_preview', 21);
	}
	
	public function customize_preview_init() {
		wp_enqueue_script( 'pojo-customizer', get_template_directory_uri() . '/core/assets/admin-ui/theme-customizer.min.js', array( 'customize-preview' ), '20131009', true );

		wp_localize_script( 'pojo-customizer', '_pojo_webfont_list', Pojo_Web_Fonts::get_web_fonts() );
	}
	
	public function customize_controls_enqueue_scripts() {
		wp_enqueue_media();

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		
		wp_enqueue_script( 'pojo-fields-plugin', get_template_directory_uri() . '/core/assets/admin-ui/fields-plugin.js', array( 'jquery' ) );
		wp_enqueue_script( 'pojo-theme-customizer-controls', get_template_directory_uri() . '/core/assets/admin-ui/theme-customizer-controls.js', array( 'jquery', 'pojo-fields-plugin', 'wp-color-picker', 'customize-controls' ), false, true );
		wp_enqueue_style( 'pojo-theme-customizer-controls', get_template_directory_uri() . '/core/assets/admin-ui/theme-customizer-controls.css' );
	}

	private function _get_remote_customizer_url() {
		$default_langs = array(
			'en_US' => 'en',
			'he_IL' => 'he',
		);

		if ( isset( $default_langs[ get_locale() ] ) )
			$lang = $default_langs[ get_locale() ];
		else
			$lang = 'en';

		$response = wp_remote_post(
			'http://pojo.me/',
			array(
				'sslverify' => false,
				'timeout' => 30,
				'body' => array(
					'pojo_action' => 'get_import_files',
					'theme' => Pojo_Core::instance()->licenses->updater->theme_name,
					'license' => Pojo_Core::instance()->licenses->get_license_key(),
					'lang' => $lang,
				)
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$response_data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! $response_data['success'] )
			return false;

		$files = $response_data['data'];

		if ( empty( $files[ $lang ]['customizer'] ) )
			return false;

		return $files[ $lang ]['customizer'];
	}
	
	public function sync_theme_mod() {
		$url = $this->_get_remote_customizer_url();
		if ( $url ) {
			$customizer_options = json_decode( file_get_contents( $url ), true );

			if ( ! empty( $customizer_options ) ) {
				foreach ( $customizer_options as $key => $value ) {
					$option = get_theme_mod( $key );
					if ( ! $option ) {
						set_theme_mod( $key, $value );
					}
				}

				return;
			}
		}

		// Legacy defaults
		$sections = $this->get_theme_sections();
		if ( empty( $sections ) )
			return;
		
		foreach ( $sections as $section ) {
			if ( empty( $section['fields'] ) )
				continue;

			foreach ( $section['fields'] as $field ) {
				$option = get_theme_mod( $field['id'] );
				if ( ! $option ) {
					set_theme_mod( $field['id'], $field['std'] );
				}
			}
		}
	}
	
	public function __construct() {
		include( 'class-pojo-customizer-sections.php' );
		
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'wp_head', array( &$this, 'wp_head' ) );
		add_action( 'customize_register', array( &$this, 'customize_register' ) );
		add_action( 'customize_preview_init', array( &$this, 'customize_preview_init' ) );
		add_action( 'customize_controls_enqueue_scripts', array( &$this, 'customize_controls_enqueue_scripts' ) );
		//add_action( 'switch_theme', array( &$this, 'switch_theme' ), 20, 2 );
		
		$this->_fields = array(
			self::FIELD_TEXT,
			self::FIELD_TEXTAREA,
			self::FIELD_SELECT,
			self::FIELD_CHECKBOX,
			self::FIELD_RADIO,
			self::FIELD_COLOR,
			self::FIELD_IMAGE,
			self::FIELD_TYPOGRAPHY,
			self::FIELD_BACKGROUND,
			self::FIELD_RADIO_IMAGE,
			self::FIELD_TWO_COLOR,
			self::FIELD_DROPDOWN_PAGES,
			
			self::FIELD_SELECT_SIDEBAR,
		);
	}

}

function add_media_manager_template_to_customizer() {
	wp_print_media_templates();
}
add_action( 'customize_controls_print_footer_scripts', 'add_media_manager_template_to_customizer' );