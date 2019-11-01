<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Settings_Page_Content extends Pojo_Settings_Page_Base {
	
	public function section_content( $sections = array() ) {
		$post_types_objects = get_post_types( array( 'public' => true ), 'objects' );
		$post_type_options = array();
		foreach ( $post_types_objects as $cpt_slug => $post_type ) {
			$post_type_options[ $cpt_slug ] = $post_type->labels->name;
		}
		
		$fields = array();

		$fields[] = array(
			'id'      => 'site_breadcrumbs',
			'title'   => __( 'Breadcrumbs', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id' => 'breadcrumbs_home_text',
			'title' => __( 'Breadcrumbs Home Text', 'pojo' ),
			'std' => __( 'Home', 'pojo' ),
		);
		
		$fields[] = array(
			'id' => 'breadcrumbs_delimiter',
			'title' => __( 'Breadcrumbs Delimiter', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'lsaquo' => '&lsaquo;',
				'rsaquo' => '&rsaquo;',
				'hearts' => '&hearts;',
				'diams' => '&diams;',
				'oline' => '&oline;',
				'#58' => '&#58;',
				'#59' => '&#59;',
				'ndash' => '&ndash;',
				'mdash' => '&mdash;',
				'laquo' => '&laquo;',
				'raquo' => '&raquo;',
				'middot' => '&middot;',
				'#9679' => '&#9679;',
				'#8226' => '&#8226;',
				'#92' => '&#92;',
				'#47' => '&#47;',
				'#124' => '&#124;',
			),
			'std' => '',
		);
		
		$fields[] = array(
			'id' => 'breadcrumbs_hide_current_page',
			'title' => __( 'Hide Current Page in the Breadcrumb', 'pojo' ),
			'type' => Pojo_Settings::FIELD_CHECKBOX_LIST,
			'options' => $post_type_options,
			'std' => array(),
		);
		
		$fields[] = array(
			'id'      => 'hide_page_title',
			'title'   => __( 'Page Title', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);
		
		if ( current_theme_supports( 'pojo-page-header' ) ) {
			$fields[] = array(
				'id' => 'page_header_support',
				'title' => __( 'Title Bar', 'pojo' ),
				'type' => Pojo_Settings::FIELD_CHECKBOX_LIST,
				'options' => $post_type_options,
				'std' => apply_filters( 'pojo_title_bar_settings_default', array( 'post', 'page' ) ),
			);
		}

		$sections[] = array(
			'id' => 'section-content',
			'page' => $this->_page_id,
			'title' => '',
			'fields' => $fields,
		);

		$fields = array();

		$post_types_nav_support = $post_type_options;
		if ( Pojo_Compatibility::is_woocommerce_installed() ) {
			unset( $post_types_nav_support['product'] );
		}

		$fields[] = array(
			'id' => 'pojo_enable_post_nav',
			'title' => __( 'Turn On', 'pojo' ),
			'type' => Pojo_Settings::FIELD_CHECKBOX_LIST,
			'options' => $post_types_nav_support,
			'std' => array( 'post', 'pojo_gallery' ),
			'desc' => __( 'Shows links to the next and previous post', 'pojo' ),
		);
		
		$sections[] = array(
			'id' => 'section-content',
			'page' => $this->_page_id,
			'title' => __( 'Post Navigation', 'pojo' ),
			'fields' => $fields,
		);
		
		$fields = array();
		
		foreach ( $post_types_objects as $cpt_slug => $post_type ) {
			$taxonomies = get_object_taxonomies( $cpt_slug, 'objects' );

			if ( ! $taxonomies ) {
				continue;
			}

			$taxonomy_options = array( '' => __( 'None', 'pojo' ) );
			foreach ( $taxonomies as $key => $taxonomy ) {
				$taxonomy_options[ $key ] = $taxonomy->labels->name;
			}

			$fields[] = array(
				'id' => 'pojo_post_nav_by_taxonomy_' . $cpt_slug,
				'title' => $post_type->labels->name,
				'type' => Pojo_Settings::FIELD_SELECT,
				'options' => $taxonomy_options,
				'std' => '',
			);
		}
		
		if ( ! empty( $fields ) ) {
			$sections[] = array(
				'id' => 'section-content',
				'page' => $this->_page_id,
				'title' => __( 'Post Navigation by Taxonomy', 'pojo' ),
				'fields' => $fields,
			);
		}
		
		return $sections;
	}
	
	public function section_posts( $sections = array() ) {
		$fields = array();

		$fields = apply_filters( 'pojo_register_settings_single_before_metadata', $fields );

		$fields[] = array(
			'id'      => 'single_metadata_date',
			'title'   => __( 'Date', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'single_metadata_time',
			'title'   => __( 'Time', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'single_metadata_comments',
			'title'   => __( 'Comments', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'single_metadata_author',
			'title'   => __( 'Author', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields = apply_filters( 'pojo_register_settings_single_after_metadata', $fields );
		
		if ( current_theme_supports( 'pojo-about-author' ) ) {
			$fields[] = array(
				'id'      => 'single_about_author',
				'title'   => __( 'About Author', 'pojo' ),
				'type'    => Pojo_Settings::FIELD_SELECT,
				'options' => array(
					'' => __( 'Show', 'pojo' ),
					'hide' => __( 'Hide', 'pojo' ),
				),
				'std' => '',
			);
		}

		$sections[] = array(
			'id' => 'section-posts',
			'page' => $this->_page_id,
			'title' => __( 'Posts:', 'pojo' ),
			'fields' => $fields,
		);

		return $sections;
	}
	
	public function section_archive( $sections = array() ) {
		$fields = array();

		$display_types = array();

		$display_types = apply_filters( 'po_display_types', $display_types, 'post' );

		$base_radio_image_url = get_template_directory_uri() . '/core/assets/admin-ui/images/display_type';

		$display_types_radios = array();
		foreach ( $display_types as $d_key => $display_type ) {
			$display_types_radios[] = array(
				'id' => $d_key,
				'title' => $display_type,
				'image' => sprintf( '%s/%s.png', $base_radio_image_url, $d_key ),
			);
		}
		
		$fields[] = array(
			'id'      => 'posts_display_type',
			'title'   => __( 'Select Display Type', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_RADIO_IMAGE,
			'desc'    => __( 'Choosing the Structure of the Content from your Theme', 'pojo' ),
			'options' => $display_types_radios,
			'std' => 'blog',
		);

		$fields[] = array(
			'id'      => 'archive_pagination',
			'title'   => __( 'Pagination', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => po_get_theme_pagination_support(),
			'std'     => '',
		);

		$fields = apply_filters( 'pojo_register_settings_archive_before_metadata', $fields );

		$fields[] = array(
			'id'      => 'archive_metadata_date',
			'title'   => __( 'Date', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'archive_metadata_time',
			'title'   => __( 'Time', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'archive_metadata_comments',
			'title'   => __( 'Comments', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);
		
		$fields[] = array(
			'id'      => 'archive_metadata_author',
			'title'   => __( 'Author', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);
		
		$fields[] = array(
			'id'      => 'archive_metadata_excerpt',
			'title'   => __( 'Content', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Excerpt', 'pojo' ),
				'full' => __( 'Full Content', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);
		
		$fields[] = array(
			'id' => 'archive_excerpt_number_words',
			'title' => __( 'Excerpt Length (Words)', 'pojo' ),
			'std' => '20',
			'sanitize_callback' => array( 'Pojo_Settings_Validations', 'field_number' ),
			'classes' => array( 'small-text' ),
		);
		
		$fields[] = array(
			'id'      => 'archive_metadata_readmore',
			'title'   => __( 'Read More', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);
		
		$fields[] = array(
			'id'      => 'archive_text_readmore',
			'title'   => __( 'Text Read More', 'pojo' ),
			'std' => '',
			'classes' => array( 'medium-text' ),
		);

		$fields = apply_filters( 'pojo_register_settings_archive_after_metadata', $fields );

		$sections[] = array(
			'id' => 'section-archive',
			'page' => $this->_page_id,
			'title' => __( 'Archive:', 'pojo' ),
			'fields' => $fields,
		);
		
		return $sections;
	}

	public function section_pojo_builder( $sections ) {
		$fields = array();

		$fields[] = array(
			'id' => 'pojo_builder_enable',
			'title' => __( 'Backend Editor', 'pojo' ),
			'type' => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'' => __( 'Enable', 'pojo' ),
				'disable' => __( 'Disable', 'pojo' ),
			),
			'std' => '',
			'desc' => __( 'Keep this setting on Disable, so you can use the Elementor frontend editor and not the backend one.', 'pojo' ),
		);
		
		$sections[] = array(
			'id' => 'section-pojo-builder',
			'page' => $this->_page_id,
			'title' => __( 'Backend Builder (old version)', 'pojo' ),
			'intro'  => __( 'This control will let you switch off the backend editor of the theme, so page design will be done on Elementor, the more advanced frontend page builder.', 'pojo' ),
			'fields' => $fields,
		);

		return $sections;
	}
	
	public function __construct( $priority = 10 ) {
		$this->_page_id         = 'pojo-content';
		$this->_page_title      = __( 'Content Settings', 'pojo' );
		$this->_page_menu_title = __( 'Content', 'pojo' );
		$this->_page_type       = 'submenu';
		$this->_page_parent     = 'pojo-home';
		
		add_filter( 'pojo_register_settings_sections', array( &$this, 'section_content' ), 100 );
		add_filter( 'pojo_register_settings_sections', array( &$this, 'section_posts' ), 110 );
		add_filter( 'pojo_register_settings_sections', array( &$this, 'section_archive' ), 120 );
		add_filter( 'pojo_register_settings_sections', array( &$this, 'section_pojo_builder' ), 160 );
		
		parent::__construct( $priority );
	}
	
}
