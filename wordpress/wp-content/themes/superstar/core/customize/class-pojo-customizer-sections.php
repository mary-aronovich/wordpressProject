<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customizer_Sections {

	public function section_layout( $sections = array() ) {
		$fields = array();

		$fields[] = array(
			'id'      => 'pojo_general_layouts',
			'title'   => __( 'General Layout', 'pojo' ),
			'type'    => Pojo_Theme_Customize::FIELD_RADIO_IMAGE,
			'std'     => Pojo_Layouts::instance()->get_default_layout(),
			'choices' => Pojo_Layouts::instance()->get_cpt_layouts_with_options(),
		);

		$fields[] = array(
			'id'      => 'pojo_general_main_sidebar',
			'title'   => __( 'General Main Sidebar', 'pojo' ),
			'type'    => Pojo_Theme_Customize::FIELD_SELECT_SIDEBAR,
			'std'     => '',
			'choices' => array(
				'' => __( 'Default', 'pojo' ),
			),
		);

		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $post_types as $cpt => $cpt_object ) {
			foreach ( array( 'archive', 'single' ) as $view ) {
				if ( 'archive' === $view && 'page' === $cpt )
					continue;

				$field_id = $cpt . '_layout';
				if ( 'archive' === $view )
					$field_id .= '_' . $view;

				$fields[] = array(
					'id'      => $field_id,
					'title'   => sprintf( _x( '%1$s %2$s', 'Customizer Layout. 1: PostType Label; 2: View type', 'pojo' ), $cpt_object->labels->name, ucfirst( $view ) ),
					'type'    => Pojo_Theme_Customize::FIELD_RADIO_IMAGE,
					'std'     => Pojo_Layouts::instance()->get_default_layout( $cpt ),
					'choices' => Pojo_Layouts::instance()->get_cpt_layouts_with_options( $cpt ),
				);
				
				$field_id = $cpt . '_main_sidebar';
				if ( 'archive' === $view )
					$field_id .= '_' . $view;

				$fields[] = array(
					'id'      => $field_id,
					'title'   => sprintf( _x( 'Sidebar: %1$s %2$s', 'Customizer Main Sidebar. 1: PostType Label; 2: View type', 'pojo' ), $cpt_object->labels->name, ucfirst( $view ) ),
					'type'    => Pojo_Theme_Customize::FIELD_SELECT_SIDEBAR,
					'std'     => '',
					'choices' => array(
						'' => __( 'Default', 'pojo' ),
					),
				);
			}
		}

		$fields[] = array(
				'id'      => 'pojo_404_layouts',
				'title'   => __( '404 Page', 'pojo' ),
				'type'    => Pojo_Theme_Customize::FIELD_RADIO_IMAGE,
				'std'     => Pojo_Layouts::instance()->get_404_default_layout(),
				'choices' => Pojo_Layouts::instance()->get_404_layouts_with_options(),
		);
		
		$fields[] = array(
				'id'      => 'pojo_404_page_id',
				'title'   => __( 'Select 404 Page Content', 'pojo' ),
				'type'    => Pojo_Theme_Customize::FIELD_DROPDOWN_PAGES,
				'std'     => '0',
		);
		
		$sections[] = array(
			'id' => 'layout',
			'title'      => __( 'Layout', 'pojo' ),
			'desc'       => '',
			'fields'     => $fields,
		);

		return $sections;
	}

	public function section_custom( $sections = array() ) {
		$custom_css = get_theme_mod( 'pojo_custom_css' );
		if ( empty( $custom_css ) ) {
			return $sections;
		}
		
		$fields = array();

		$fields[] = array(
			'id' => 'pojo_custom_css',
			'title' => '',
			'type' => Pojo_Theme_Customize::FIELD_TEXTAREA,
			'std' => apply_filters( 'pojo_customizer_custom_css_default', '' ),
		);

		$sections[] = array(
			'id' => 'custom',
			'title' => __( 'Live CSS', 'pojo' ),
			'desc' => __( 'Enter your custom CSS here', 'pojo' ),
			'fields' => $fields,
		);

		return $sections;
	}

	public function __construct() {
		add_filter( 'pojo_register_customize_sections', array( &$this, 'section_layout' ), 200 );
		add_filter( 'pojo_register_customize_sections', array( &$this, 'section_custom' ), 400 );
	}
	
}
new Pojo_Customizer_Sections();