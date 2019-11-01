<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Page_Options {

	public function register_layout_support() {
		$post_types_objects = get_post_types( array( 'public' => true ), 'objects' );
		$public_post_type = array();
		foreach ( $post_types_objects as $cpt_slug => $post_type ) {
			$public_post_type[] = $cpt_slug;
		}
		
		$public_post_type = array_unique( apply_filters( 'pojo_register_layout_support', $public_post_type ) );
		
		foreach ( $public_post_type as $cpt ) {
			add_post_type_support( $cpt, array( 'pojo-layout' ) );
		}
	}

	public function create_post_options_panel( $meta_boxes ) {
		$fields = array();
		
		/**
		 * General Settings
		 */
		$fields[] = array(
			'id'    => 'heading_general_settings',
			'title' => __( 'General Settings', 'pojo' ),
			'type'  => Pojo_MetaBox::FIELD_HEADING,
		);

		$fields[] = array(
			'id'      => 'hide_page_title',
			'title'   => __( 'Page Title', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'desc'    => __( 'If you want to Hide/Show the Page Title.', 'pojo' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
			),
			'std'     => '',
		);

		$fields[] = array(
			'id'      => 'show_page_breadcrumbs',
			'title'   => __( 'Breadcrumbs', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
			),
			'std'     => '',
		);
		
		$fields[] = array(
			'id'      => 'show_post_nav',
			'title'   => __( 'Post Navigation', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'desc' => __( 'Shows links to the next and previous post', 'pojo' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
			),
			'std'     => '',
		);
		
		if ( current_theme_supports( 'pojo-page-header' ) ) {
			$fields[] = array(
				'id' => 'heading_sub_header',
				'title' => __( 'Title Bar', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_HEADING,
			);

			$sub_header_style_options = array(
				'' => __( 'Default Background', 'pojo' ),
				'show' => __( 'Show', 'pojo' ),
				'none' => __( 'Hide', 'pojo' ),
				'transparent' => __( 'Transparent Background', 'pojo' ),
				'custom_bg' => __( 'Custom Background', 'pojo' ),
				'widgets_area' => __( 'Widgets Area', 'pojo' ),
			);

			if ( Pojo_Compatibility::is_revslider_installer() )
				$sub_header_style_options['rev_slider'] = __( 'Rev Slider', 'pojo' );

			if ( Pojo_Compatibility::is_slideshow_installed() ) {
				$sub_header_style_options['slideshow'] = __( 'Slideshow', 'pojo' );
			}

			$fields[] = array(
				'id' => 'sub_header_style',
				'title' => __( 'Background Style', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_SELECT,
				'std' => '',
				'classes' => array( 'select-show-or-hide-fields' ),
				'options' => $sub_header_style_options,
			);

			foreach ( $sub_header_style_options as $sh_key => $sh_value ) {
				if ( 'custom_bg' === $sh_key ) {
					$fields[] = array(
						'id' => 'sub_header_color',
						'title' => __( 'Background Color', 'pojo' ),
						'placeholder' => '#ffffff',
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);
					
					$fields[] = array(
						'id' => 'sub_header_opacity',
						'title' => __( 'Background Opacity', 'pojo' ),
						'type' => Pojo_MetaBox::FIELD_NUMBER,
						'placeholder' => '',
						'desc' => '%',
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);

					$fields[] = array(
						'id' => 'sub_header_image',
						'title' => __( 'Background Image', 'pojo' ),
						'type' => Pojo_MetaBox::FIELD_IMAGE,
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);

					$fields[] = array(
						'id' => 'sub_header_position',
						'title' => __( 'Background Position', 'pojo' ),
						'type' => Pojo_MetaBox::FIELD_SELECT,
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
						'std' => '',
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);

					$fields[] = array(
						'id' => 'sub_header_attachment',
						'title' => __( 'Background Attachment', 'pojo' ),
						'type' => Pojo_MetaBox::FIELD_SELECT,
						'options' => array(
							'' => __( 'Default', 'pojo' ),
							'scroll' => __( 'Scroll', 'pojo' ),
							'fixed' => __( 'fixed', 'pojo' ),
						),
						'std' => '',
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);

					$fields[] = array(
						'id' => 'sub_header_repeat',
						'title' => __( 'Background Repeat', 'pojo' ),
						'type' => Pojo_MetaBox::FIELD_SELECT,
						'options' => array(
							'' => __( 'Default', 'pojo' ),
							'no-repeat' => __( 'no-repeat', 'pojo' ),
							'repeat' => __( 'repeat', 'pojo' ),
							'repeat-x' => __( 'repeat-x', 'pojo' ),
							'repeat-y' => __( 'repeat-y', 'pojo' ),
						),
						'std' => '',
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);

					$fields[] = array(
						'id' => 'sub_header_size',
						'title' => __( 'Background Size', 'pojo' ),
						'type' => Pojo_MetaBox::FIELD_SELECT,
						'options' => array(
							'' => __( 'Default', 'pojo' ),
							'auto' => __( 'Auto', 'pojo' ),
							'cover' => __( 'Cover', 'pojo' ),
						),
						'std' => '',
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);

					$fields[] = array(
						'id' => 'height_sub_header',
						'title' => __( 'Title Bar Height', 'pojo' ),
						'desc' => __( 'For Example: 100px', 'pojo' ),
						'std' => '100px',
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);
				}

				if ( in_array( $sh_key, array( '', 'show', 'transparent', 'custom_bg' ) ) ) {
					$fields[] = array(
						'id' => 'title',
						'title' => __( 'Custom Title', 'pojo' ),
						'classes_field' => array( 'large-text' ),
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
					);
				}

				if ( Pojo_Compatibility::is_revslider_installer() && 'rev_slider' === $sh_key ) {
					/** @var $arr_sliders RevSlider[] */
					$rev_slider_options = array();

					$rev_slider  = new RevSlider();
					$arr_sliders = $rev_slider->getArrSliders();

					if ( empty( $arr_sliders ) )
						$rev_slider_options[] = __( 'No have any register Rev Slider in website', 'pojo' );
					else
						foreach ( $arr_sliders as $slider )
							$rev_slider_options[ $slider->getAlias() ] = $slider->getShowTitle();

					$fields[] = array(
						'id' => 'sub_header_rev_slider',
						'title' => __( 'Title Bar Slider', 'pojo' ),
						'type' => Pojo_MetaBox::FIELD_SELECT,
						'desc' => __( 'Choose from Revolution Slider', 'pojo' ),
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
						'options' => $rev_slider_options,
					);
				}

				if ( Pojo_Compatibility::is_slideshow_installed() && 'slideshow' === $sh_key ) {
					global $pojo_slideshow;
					$slide_options = $pojo_slideshow->helpers->get_all_sliders();
					if ( empty( $slide_options ) )
						$slide_options[] = __( 'NO Found any Slideshows', 'pojo' );

					$fields[] = array(
						'id' => 'sub_header_slideshow',
						'title' => __( 'Title Bar Slideshow', 'pojo' ),
						'type' => Pojo_MetaBox::FIELD_SELECT,
						'desc' => sprintf( '<a href="%s">%s</a>', admin_url( 'edit.php?post_type=pojo_slideshow' ), __( 'All Slideshows', 'pojo' ) ),
						'show_on' => array( 'po_sub_header_style' => $sh_key ),
						'options' => $slide_options,
					);
				}
				
				if ( 'widgets_area' === $sh_key ) {
					global $wp_registered_sidebars;
					if ( ! empty( $wp_registered_sidebars ) ) {
						$sidebar_options = array();
						foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar_args ) {
							$sidebar_options[ $sidebar_id ] = $sidebar_args['name'];
						}

						$fields[] = array(
							'id' => 'sub_header_widgets_area',
							'title' => __( 'Widgets Area', 'pojo' ),
							'type' => Pojo_MetaBox::FIELD_SELECT,
							'show_on' => array( 'po_sub_header_style' => $sh_key ),
							'options' => $sidebar_options,
						);

						$fields[] = array(
							'id' => 'sub_header_width_content',
							'title' => __( 'Width Content', 'pojo' ),
							'type' => Pojo_MetaBox::FIELD_SELECT,
							'options' => array(
								'' => __( 'Boxed', 'pojo' ),
								'100_width' => __( 'Wide', 'pojo' )
							),
							'show_on' => array( 'po_sub_header_style' => $sh_key ),
						);
					}
				}
			}
		}

		if ( current_theme_supports( 'pojo-background-options' ) ) {
			/**
			 * Background
			 */
			$fields[] = array(
				'id'    => 'heading_background',
				'title' => __( 'Custom Background', 'pojo' ),
				'type'  => Pojo_MetaBox::FIELD_HEADING,
			);

			$fields[] = array(
				'id'      => 'bg_color',
				'title'   => __( 'Background Color', 'pojo' ),
				'placeholder' => '#ffffff',
			);

			$fields[] = array(
				'id' => 'bg_opacity',
				'title' => __( 'Background Opacity', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_NUMBER,
				'placeholder' => '',
				'desc' => '%',
			);

			$fields[] = array(
				'id'      => 'bg_image',
				'title'   => __( 'Background Image', 'pojo' ),
				'type'    => Pojo_MetaBox::FIELD_IMAGE,
			);

			$fields[] = array(
				'id' => 'bg_position',
				'title' => __( 'Background Position', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_SELECT,
				'options' => array(
					''              => __( 'Default', 'pojo' ),
					'top left'      => __( 'Top Left', 'pojo' ),
					'top center'    => __( 'Top Center', 'pojo' ),
					'top right'     => __( 'Top Right', 'pojo' ),
					'center left'   => __( 'Center Left', 'pojo' ),
					'center center' => __( 'Center Center', 'pojo' ),
					'center right'  => __( 'Center Right', 'pojo' ),
					'bottom left'   => __( 'Bottom Left', 'pojo' ),
					'bottom center' => __( 'Bottom Center', 'pojo' ),
					'bottom right'  => __( 'Bottom Right', 'pojo' ),
				),
				'std' => '',
			);

			$fields[] = array(
				'id' => 'bg_attachment',
				'title' => __( 'Background Attachment', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_SELECT,
				'options' => array(
					'' => __( 'Default', 'pojo' ),
					'scroll' => __( 'Scroll', 'pojo' ),
					'fixed' => __( 'fixed', 'pojo' ),
				),
				'std' => '',
			);

			$fields[] = array(
				'id' => 'bg_repeat',
				'title' => __( 'Background Repeat', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_SELECT,
				'options' => array(
					'' => __( 'Default', 'pojo' ),
					'no-repeat' => __( 'No repeat', 'pojo' ),
					'repeat' => __( 'repeat', 'pojo' ),
					'repeat-x' => __( 'repeat-x', 'pojo' ),
					'repeat-y' => __( 'repeat-y', 'pojo' ),
				),
				'std' => '',
			);

			$fields[] = array(
				'id' => 'bg_size',
				'title' => __( 'Background Size', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_SELECT,
				'options' => array(
					'' => __( 'Default', 'pojo' ),
					'auto'  => __( 'Auto', 'pojo' ),
					'cover' => __( 'Cover', 'pojo' ),
				),
				'std' => '',
			);
		}

		if ( Pojo_Compatibility::is_pojo_sharing_installed() ) {
			$fields[] = array(
				'id'    => 'sharing',
				'title' => __( 'Sharing', 'pojo' ),
				'type'  => Pojo_MetaBox::FIELD_HEADING,
			);
	
			$fields[] = array(
				'id' => 'sharing_disabled',
				'title' => __( 'Hide sharing buttons', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_CHECKBOX,
				'std' => false,
			);
		}

		$post_types = get_post_types( array( 'public' => true ) );
		//$post_types = apply_filters( 'pojo_page_types_options_array', array( 'page', 'post' ) );
		foreach ( $post_types as $cpt ) {
			$current_fields = apply_filters( 'po_init_fields', $fields, $cpt );
			$current_fields = apply_filters( 'po_init_fields-' . $cpt, $current_fields );
			
			$meta_boxes[] = array(
				'id'         => 'post_options-' . $cpt,
				'title'      => __( 'Advanced Options', 'pojo' ),
				'post_types' => array( $cpt ),
				'priority'   => 'core',
				'context'    => 'normal',
				'prefix'     => 'po_',
				'fields'     => $current_fields,
			);
		}

		return $meta_boxes;
	}

	public function po_init_fields( $old_fields, $cpt ) {
		$fields = array();

		if ( post_type_supports( $cpt, 'pojo-layout' ) ) {
			/**
			 * Layout
			 */
			$fields[] = array(
				'id'    => 'heading_page_layout',
				'title' => __( 'Layout', 'pojo' ),
				'type'  => Pojo_MetaBox::FIELD_HEADING,
			);

			/*$fields[] = array(
				'id'      => 'chk_custom_layout',
				'title'   => __( 'Use Custom Layout', 'pojo' ),
				'type'    => Pojo_MetaBox::FIELD_CHECKBOX,
				'classes' => array( 'select-show-or-hide-fields' ),
				'std'     => false,
			);*/

			$fields[] = array(
				'id'      => 'layout',
				'title'   => __( 'Choose Layout', 'pojo' ),
				'type'    => Pojo_MetaBox::FIELD_RADIO_IMAGE,
				'std'     => '',
				'options' => Pojo_Layouts::instance()->get_cpt_layouts_with_options( $cpt, true ),
			);

			/*$fields[] = array(
				'id'      => 'sidebar',
				'title'   => __( 'Choose Sidebar', 'pojo' ),
				'type'    => Pojo_MetaBox::FIELD_SIDEBAR_SELECT,
				'std'     => atto_get_option( 'page_sidebar' ),
				'options' => array(
					'' => __( 'Default Sidebar', 'pojo' ),
				),
			);*/
		}
		
		// TODO: move to post supports
		if ( 'post' === $cpt ) {
			$fields[] = array(
				'id' => 'heading_single_metadata',
				'title' => __( 'Post Settings', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_HEADING,
			);

			$metadata_list = apply_filters(
				'po_single_metadata_list',
				array(
					'date' => __( 'Date', 'pojo' ),
					'time' => __( 'Time', 'pojo' ),
					'comments' => __( 'Comments', 'pojo' ),
					'author' => __( 'Author', 'pojo' ),
				)
			);

			foreach ( $metadata_list as $metadata_key => $metadata_title ) {
				$fields[] = array(
					'id' => 'single_metadata_' . $metadata_key,
					'title' => $metadata_title,
					'type' => Pojo_MetaBox::FIELD_SELECT,
					'options' => array(
						'' => __( 'Default', 'pojo' ),
						'hide' => __( 'Hide', 'pojo' ),
						'show' => __( 'Show', 'pojo' ),
					),
					'std' => '',
				);
			}
		}
		
		if ( current_theme_supports( 'pojo-about-author' ) && post_type_supports( $cpt, 'pojo-post-about-author' ) ) {
			$fields[] = array(
				'id' => 'about_author',
				'title' => __( 'About Author', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_SELECT,
				'options' => array(
					'' => __( 'Default', 'pojo' ),
					'hide' => __( 'Hide', 'pojo' ),
					'show' => __( 'Show', 'pojo' ),
				),
				'std' => '',
			);
		}

		if ( current_theme_supports( 'pojo-blank-page' ) ) {
			$fields[] = array(
				'id'    => 'heading_blank_page',
				'title' => __( 'Blank Page', 'pojo' ),
				'type'  => Pojo_MetaBox::FIELD_HEADING,
			);

			$fields[] = array(
				'id' => 'blank_page',
				'title' => __( 'Enable Blank Page', 'pojo' ),
				'type' => Pojo_MetaBox::FIELD_CHECKBOX,
				'desc' => __( 'No Header, No Footer - Just Primary Content', 'pojo' ),
				'std' => false,
			);
		}
		
		if ( ! empty( $fields ) ) {
			$old_fields = array_merge( $fields, $old_fields );
		}
		
		return $old_fields;
	}
	
	/*public function po_init_fields_page( $old_fields ) {
		$fields = array();
		
		if ( ! empty( $fields ) ) {
			$old_fields = array_merge( $fields, $old_fields );
		}

		return $old_fields;
	}*/

	public function body_add_blank_page_classes( $classes ) {
		if ( pojo_is_blank_page() ) {
			$classes[] = 'pojo-page-blank';
		}
		return $classes;
	}
	
	public function __construct() {
		add_filter( 'pojo_meta_boxes', array( &$this, 'create_post_options_panel' ) );
		add_filter( 'po_init_fields', array( &$this, 'po_init_fields' ), 20, 2 );
		//add_filter( 'po_init_fields-page', array( &$this, 'po_init_fields_page' ) );
		
		add_filter( 'body_class', array( &$this, 'body_add_blank_page_classes' ) );

		add_action( 'init', array( &$this, 'register_layout_support' ), 45 );
	}
	
}
