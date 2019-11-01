<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Gallery_CPT {
	
	protected $_assets_images = null;
	
	public function get_assets_images() {
		if ( is_null( $this->_assets_images ) )
			$this->_assets_images = get_template_directory_uri() . '/core/assets/admin-ui/images';
		return $this->_assets_images;
	}

	public function init() {
		// CPT: pojo_gallery.
		$labels = array(
			'name'               => __( 'Galleries', 'pojo' ),
			'singular_name'      => __( 'Gallery', 'pojo' ),
			'add_new'            => __( 'Add New', 'pojo' ),
			'add_new_item'       => __( 'Add New Gallery', 'pojo' ),
			'edit_item'          => __( 'Edit Gallery', 'pojo' ),
			'new_item'           => __( 'New Gallery', 'pojo' ),
			'all_items'          => __( 'All Galleries', 'pojo' ),
			'view_item'          => __( 'View Gallery', 'pojo' ),
			'search_items'       => __( 'Search Gallery', 'pojo' ),
			'not_found'          => __( 'No Galleries found', 'pojo' ),
			'not_found_in_trash' => __( 'No Galleries found in Trash', 'pojo' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Galleries', 'pojo' ),
		);
		
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'gallery' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 22,
			'supports'           => array( 'title', 'thumbnail', 'editor' ),
		);
		register_post_type(
			'pojo_gallery',
			apply_filters( 'pojo_register_post_type_gallery', $args )
		);

		// Taxonomy: pojo_gallery_cat.
		$labels = array(
			'name'              => __( 'Gallery Categories', 'pojo' ),
			'singular_name'     => __( 'Gallery Category', 'pojo' ),
			'menu_name'         => _x( 'Categories', 'Admin menu name', 'pojo' ),
			'search_items'      => __( 'Search Categories', 'pojo' ),
			'all_items'         => __( 'All Categories', 'pojo' ),
			'parent_item'       => __( 'Parent Category', 'pojo' ),
			'parent_item_colon' => __( 'Parent Category:', 'pojo' ),
			'edit_item'         => __( 'Edit Category', 'pojo' ),
			'update_item'       => __( 'Update Category', 'pojo' ),
			'add_new_item'      => __( 'Add New Category', 'pojo' ),
			'new_item_name'     => __( 'New Category Name', 'pojo' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'gallery-cat' ),
		);

		register_taxonomy(
			'pojo_gallery_cat',
			apply_filters( 'pojo_taxonomy_objects_gallery_cat', array( 'pojo_gallery' ) ),
			apply_filters( 'pojo_taxonomy_args_gallery_cat', $args )
		);
	}

	public function post_updated_messages( $messages ) {
		global $post;

		$messages['pojo_gallery'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Gallery updated. <a href="%s">View gallery</a>', 'pojo' ), esc_url( get_permalink( $post->ID ) ) ),
			2  => __( 'Custom field updated.', 'pojo' ),
			3  => __( 'Custom field deleted.', 'pojo' ),
			4  => __( 'Gallery updated.', 'pojo' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Gallery restored to revision from %s', 'pojo' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Gallery published. <a href="%s">View post</a>', 'pojo' ), esc_url( get_permalink( $post->ID ) ) ),
			7  => __( 'Gallery saved.', 'pojo' ),
			8  => sprintf( __( 'Gallery submitted. <a target="_blank" href="%s">Preview post</a>', 'pojo' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
			9  => sprintf( __( 'Gallery scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview post</a>', 'pojo' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'pojo' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ) ),
			10 => sprintf( __( 'Gallery draft updated. <a target="_blank" href="%s">Preview post</a>', 'pojo' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
		);

		return $messages;
	}
	
	public function custom_menu_order( $menus ) {
		$menus[] = 'edit.php?post_type=pojo_gallery';
		return $menus;
	}
	
	public function pojo_page_types_options_array( $post_types = array() ) {
		$post_types[] = 'pojo_gallery';
		return $post_types;
	}

	public function admin_cpt_columns( $columns ) {
		return array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Gallery Title', 'pojo' ),
			'taxonomy-pojo_gallery_cat' => __( 'Categories', 'pojo' ),
			'gallery_shortcode' => __( 'Shortcode', 'pojo' ),
			'date' => __( 'Date', 'pojo' ),
		);
	}

	public function custom_columns( $column ) {
		global $post;

		switch ( $column ) {
			case 'gallery_shortcode' :
				echo pojo_gallery_get_shortcode_text( $post->ID );
				break;
		}
	}
	
	public function dashboard_glance_items( $elements ) {
		$post_type = 'pojo_gallery';
		$num_posts = wp_count_posts( $post_type );
		if ( $num_posts && $num_posts->publish ) {
			$text = _n( '%s Gallery', '%s Galleries', $num_posts->publish, 'pojo' );
			$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
			printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text );
		}
		//return $elements;
	}
	
	public function pf_list_posts_cpt( $fields = array() ) {
		$fields[] = array(
			'id'   => 'content',
			'type' => Pojo_MetaBox::FIELD_HIDDEN,
			'std'  => 'pojo_gallery',
		);
		
		$cpt = 'pojo_gallery';

		$fields[] = array(
			'id'   => 'taxonomy',
			'type' => Pojo_MetaBox::FIELD_HIDDEN,
			'std'  => 'pojo_gallery_cat',
		);

		$fields[] = array(
			'id'       => 'taxonomy_terms',
			'title'    => __( 'Choose Category', 'pojo' ),
			'type'     => Pojo_MetaBox::FIELD_TAXONOMY_TERM_CHECKBOX,
			'taxonomy' => 'pojo_gallery_cat',
		);

		$display_types = array();
		$display_types['default'] = __( 'Default', 'pojo' );
		$display_types = apply_filters( 'po_display_types', $display_types, $cpt );

		$base_radio_image_url = $this->get_assets_images();
		
		$display_types_radios = array();
		foreach ( $display_types as $d_key => $display_type ) {
			$display_types_radios[] = array(
				'id' => $d_key,
				'title' => $display_type,
				'image' => sprintf( '%s/display_type/%s.png', $base_radio_image_url, $d_key ),
			);
		}

		$fields[] = array(
			'id'      => 'display_type',
			'title'   => __( 'Select Content Layout', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_RADIO_IMAGE,
			'desc'    => __( 'Choosing the Structure of the Galleries from your Theme', 'pojo' ),
			'classes' => array( 'select-show-or-hide-fields' ),
			'options' => $display_types_radios,
			'std'     => 'default',
		);

		$fields[] = array(
			'id'      => 'add_filter_by_category',
			'title'   => __( 'Add Filter By Category', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'gallery_advanced_settings',
			'title'   => __( 'Advanced Settings', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_BUTTON_COLLAPSE,
		);

		$fields[] = array(
			'id'      => 'posts_per_page_mode',
			'title'   => __( 'Galleries Per Page', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'classes' => array( 'select-show-or-hide-fields' ),
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'custom' => __( 'Custom', 'pojo' ),
			),
			'std'     => '',
		);

		$fields[] = array(
			'id'      => 'posts_per_page',
			'title'   => __( 'Number Galleries', 'pojo' ),
			'std'     => get_option( 'posts_per_page' ),
			'show_on' => array( 'po_posts_per_page_mode' => 'custom' ),
		);

		$fields[] = array(
			'id' => 'order_by',
			'title' => __( 'Order By', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Date', 'pojo' ),
				'menu_order' => __( 'Menu Order', 'pojo' ),
				'title' => __( 'Title', 'pojo' ),
				'rand' => __( 'Random', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id' => 'order',
			'title' => __( 'Order', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'DESC', 'pojo' ),
				'ASC' => __( 'ASC', 'pojo' ),
			),
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'offset',
			'title'   => __( 'Offset', 'pojo' ),
			'std'     => 0,
			'desc'    => __( 'Number of post to displace or pass over', 'pojo' ),
		);

		$fields[] = array(
			'id'      => 'pagination',
			'title'   => __( 'Pagination', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => po_get_theme_pagination_support(),
			'std'     => '',
		);

		$fields[] = array(
			'id'    => 'no_apply_child_posts',
			'title' => __( 'Don\'t apply the page settings to the child posts', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_CHECKBOX,
			'std' => false,
		);

		$fields[] = array(
			'id'      => 'gallery_advanced_settings',
			'title'   => __( 'Advanced Settings', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_BUTTON_COLLAPSE,
			'mode'    => 'end',
		);
		
		return $fields;
	}

	public function register_gallery_metabox( $meta_boxes = array() ) {
		$base_radio_image_url = $this->get_assets_images();
		
		$fields = array();

		$fields[] = array(
			'id' => 'gallery',
			'title' => __( 'Gallery', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_GALLERY,
			'std' => '',
		);
		
		$fields[] = array(
			'id'    => 'heading_settings',
			'title' => __( 'Settings', 'pojo' ),
			'type'  => Pojo_MetaBox::FIELD_HEADING,
		);
		
		$galleries_types = array(
			'simple' => __( 'Simple', 'pojo' ),
			'slideshow' => __( 'Slideshow', 'pojo' ),
		);
		
		$galleries_types_radios = array();
		foreach ( $galleries_types as $key => $value ) {
			$galleries_types_radios[] = array(
				'id' => $key,
				'title' => $value,
				'image' => sprintf( '%s/galleries-types/%s.png', $base_radio_image_url, $key ),
			);
		}
		
		$fields[] = array(
			'id'    => 'galleries_type',
			'type'  => Pojo_MetaBox::FIELD_RADIO_IMAGE,
			'classes' => array( 'select-show-or-hide-fields' ),
			'options' => $galleries_types_radios,
			'std' => 'simple',
		);

		$images_sizes = array();
		foreach ( get_intermediate_image_sizes() as $size ) {
			$images_sizes[ $size ] = ucwords( $size );
		}

		$images_sizes['full'] = __( 'Full', 'pojo' );
		
		$fields[] = array(
			'id' => 'image_size',
			'title' => __( 'Image Size', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => $images_sizes,
			'std' => 'thumbnail',
			'show_on' => array( 'gallery_galleries_type' => 'simple' ),
		);
	
		$fields[] = array(
			'id'    => 'link_to',
			'title' => __( 'Link To', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'file' => __( 'Full Image', 'pojo' ),
				'none' => __( 'None', 'pojo' ),
			),
			'std' => 'file',
			'show_on' => array( 'gallery_galleries_type' => 'simple' ),
		);
		
		$fields[] = array(
			'id'    => 'columns',
			'title' => __( 'Columns', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
			),
			'std' => '5',
			'show_on' => array( 'gallery_galleries_type' => 'simple' ),
		);
		
		$fields[] = array(
			'id'    => 'random_order',
			'title' => __( 'Random Order', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_CHECKBOX,
			'std' => false,
			'show_on' => array( 'gallery_galleries_type' => 'simple' ),
		);
		
		/*$fields[] = array(
			'id'    => 'thumb_position',
			'title' => __( 'Thumbnails Position', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'bottom' => __( 'Bottom', 'pojo' ),
				'right' => __( 'Right', 'pojo' ),
			),
			'std' => 'bottom',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);*/

		$fields[] = array(
			'id'    => 'slide_width',
			'title' => __( 'Slide Width (px)', 'pojo' ),
			'std' => '',
			'placeholder' => '800',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);

		$fields[] = array(
			'id'    => 'slide_height',
			'title' => __( 'Slide Height (px)', 'pojo' ),
			'std' => '',
			'placeholder' => '600',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);

		$fields[] = array(
			'id'    => 'slide_fullwidth',
			'title' => __( 'Slide Full Width', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Yes', 'pojo' ),
				'no' => __( 'No', 'pojo' ),
			),
			'std' => '',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);

		$fields[] = array(
			'id'    => 'slide_auto_height',
			'title' => __( 'Slide Auto Height', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'No', 'pojo' ),
				'yes' => __( 'Yes', 'pojo' ),
			),
			'std' => '',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);
		
		$fields[] = array(
			'id'    => 'thumb_ratio',
			'title' => __( 'Thumbnails', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'16_9' => __( '16:9', 'pojo' ),
				'1_1' => __( '1:1', 'pojo' ),
				'4_3' => __( '4:3', 'pojo' ),
				'none' => __( 'None', 'pojo' ),
			),
			'std' => '16_9',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);
		
		$fields[] = array(
			'id'    => 'transitions',
			'title' => __( 'Transitions', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'fade' => __( 'Fade', 'pojo' ),
				'slide_horizontal' => __( 'Slide Horizontal', 'pojo' ),
				'slide_vertical' => __( 'Slide Vertical', 'pojo' ),
				'scale' => __( 'Scale', 'pojo' ),
			),
			'std' => 'fade',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);
		
		$fields[] = array(
			'id'    => 'autoplay',
			'title' => __( 'Autoplay', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'on' => __( 'On', 'pojo' ),
				'off' => __( 'Off', 'pojo' ),
			),
			'std' => 'on',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);

		$fields[] = array(
			'id'    => 'slide_duration',
			'title' => __( 'Slide Duration (Seconds)', 'pojo' ),
			'std' => '',
			'placeholder' => '5',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);
		
		$fields[] = array(
			'id'    => 'arrow',
			'title' => __( 'Navigation Arrow', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => 'show',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);
		
		$fields[] = array(
			'id'    => 'lightbox',
			'title' => __( 'Open in LightBox', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => 'show',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);
		
		$fields[] = array(
			'id'    => 'caption',
			'title' => __( 'Caption', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'show' => __( 'Show', 'pojo' ),
				'hide' => __( 'Hide', 'pojo' ),
			),
			'std' => 'show',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);
		
		$meta_boxes[] = array(
			'id'         => 'pojo-gallery-galleries',
			'title'      => __( 'Images', 'pojo' ),
			'post_types' => array( 'pojo_gallery' ),
			'context'    => 'normal',
			'priority'   => 'core',
			'prefix'     => 'gallery_',
			'fields'     => $fields,
		);
		return $meta_boxes;
	}
	
	public function po_get_default_display_type( $display_type, $post_type ) {
		if ( 'pojo_gallery' === $post_type ) {
			$type = pojo_get_option( 'gallery_display_type' );
			if ( ! empty( $type ) )
				$display_type = $type;
		}
		return $display_type;
	}
	
	public function settings_section_gallery( $sections = array() ) {
		$fields = array();

		$fields[] = array(
			'id' => 'gallery_single_layout',
			'title' => __( 'Gallery Layout', 'pojo' ),
			'type' => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'wide' => __( 'Wide', 'pojo' ),
				'content_left' => __( 'Content Left', 'pojo' ),
				'content_right' => __( 'Content Right', 'pojo' ),
			),
			'std' => 'wide',
		);

		$sections[] = array(
			'id' => 'section-gallery',
			'page' => 'pojo-content',
			'title' => __( 'Gallery Single:', 'pojo' ),
			'fields' => $fields,
		);
		
		$base_radio_image_url = $this->get_assets_images();
		$fields = array();

		$display_types = apply_filters( 'po_display_types', array(), 'pojo_gallery' );

		$display_types_radios = array();
		foreach ( $display_types as $d_key => $display_type ) {
			$display_types_radios[] = array(
				'id' => $d_key,
				'title' => $display_type,
				'image' => sprintf( '%s/display_type/%s.png', $base_radio_image_url, $d_key ),
			);
		}

		$fields[] = array(
			'id'      => 'gallery_display_type',
			'title'   => __( 'Select Display Type', 'pojo' ),
			'type'    => Pojo_Settings::FIELD_RADIO_IMAGE,
			'desc'    => __( 'Gallery with 3 or 4 columns', 'pojo' ),
			'options' => $display_types_radios,
			'std'     => ! empty( $display_types_radios[0]['id'] ) ? $display_types_radios[0]['id'] : '',
		);
		
		$sections[] = array(
			'id' => 'section-gallery',
			'page' => 'pojo-content',
			'title' => __( 'Gallery Archive:', 'pojo' ),
			'fields' => $fields,
		);

		return $sections;
	}

	public function advanced_options( $fields = array() ) {
		$gallery_fields = array();

		$gallery_fields[] = array(
			'id'    => 'heading_gallery_options',
			'title' => __( 'Gallery Options', 'pojo' ),
			'type'  => Pojo_MetaBox::FIELD_HEADING,
		);
		
		$gallery_fields[] = array(
			'id' => 'gallery_single_layout',
			'title' => __( 'Gallery Layout', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'wide' => __( 'Wide', 'pojo' ),
				'content_left' => __( 'Content Left', 'pojo' ),
				'content_right' => __( 'Content Right', 'pojo' ),
			),
			'std' => '',
		);
		
		return array_merge( $gallery_fields, $fields );
	}
	
	public function pojo_get_layouts_custom_post_types( $types = array(), $layouts_options ) {
		$types[] = array(
			'id' => 'pojo_gallery',
			'title' => __( 'Choose Gallery Layout', 'pojo' ),
			'std' => 'full',
		);
		
		return $types;
	}
	
	public function po_get_archive_query( $query, $post_id ) {
		if ( 'pojo_gallery' === $query['post_type'] ) {
			if ( 'hide' !== atmb_get_field( 'po_add_filter_by_category', $post_id ) ) {
				$query['posts_per_page'] = -1;
			}
		}
		
		return $query;
	}
	
	public function pojo_post_classes( $classes, $post_type ) {
		if ( 'pojo_gallery' === $post_type ) {
			$classes = array_merge( pojo_get_list_pluck_with_prefix( get_the_terms( get_the_ID(), 'pojo_gallery_cat' ), 'term_id', 'filter-term-' ), $classes );
		}
		return $classes;
	}

	public function gallery_single_post_classes( $classes, $class, $post_id ) {
		if ( is_single() && 'pojo_gallery' === get_post_type( $post_id ) ) {
			$classes[] = 'single-gallery-' . str_replace( '_', '-', pojo_gallery_get_single_layout( $post_id ) );
		}
		
		return $classes;
	}
	
	public function pojo_before_content_loop( $display_type ) {
		if ( 'pojo_gallery' !== atmb_get_field( 'po_content' ) || 'hide' === atmb_get_field( 'po_add_filter_by_category' ) )
			return;
		
		$taxonomy_terms = atmb_get_field( 'po_taxonomy_terms', false, Pojo_MetaBox::FIELD_CHECKBOX_LIST );
		if ( empty( $taxonomy_terms ) )
			return;
		
		$terms = get_terms( 'pojo_gallery_cat', array(
			'include' => $taxonomy_terms,
		) );
		
		if ( is_wp_error( $terms ) )
			return;
		
		?><ul class="category-filters">
			<li><a href="javascript:void(0);" data-filter="*" class="active"><?php _e( 'All', 'pojo' ); ?></a></li>
			<?php foreach( $terms as $term ) : ?>
			<li><a href="javascript:void(0);" data-filter=".filter-term-<?php echo esc_attr( $term->term_id ); ?>"><?php echo $term->name; ?></a></li>
			<?php endforeach; ?>
		</ul><?php
	}

	public function post_submitbox_misc_actions() {
		global $post;

		if ( 'pojo_gallery' !== $post->post_type )
			return;
		?>
		<div class="misc-pub-section" id="gallery-preview-shortcode">
			<input type="text" class="copy-paste-shortcode" value="<?php echo esc_attr( pojo_gallery_get_shortcode_text( $post->ID ) ); ?>" readonly />
			<span><?php _e( 'Copy and paste this shortcode into your Text editor or use with Gallery Widget.', 'pojo' ); ?></span>
		</div>
	<?php
	}

	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );

		add_filter( 'pojo_custom_menu_order_after_posts', array( &$this, 'custom_menu_order' ), 50 );

		add_filter( 'manage_edit-pojo_gallery_columns', array( &$this, 'admin_cpt_columns' ) );
		add_action( 'manage_posts_custom_column', array( &$this, 'custom_columns' ) );

		add_action( 'post_submitbox_misc_actions', array( &$this, 'post_submitbox_misc_actions' ) );

		
		add_filter( 'pojo_page_types_options_array', array( &$this, 'pojo_page_types_options_array' ) );

		//add_filter( 'manage_edit-pojo_gallery_columns', array( &$this, 'admin_cpt_columns' ) );
		//add_action( 'manage_posts_custom_column', array( &$this, 'custom_columns' ) );

		add_action( 'dashboard_glance_items', array( &$this, 'dashboard_glance_items' ), 50 );

		add_filter( 'pf_format_content_list', array( &$this, 'pojo_page_types_options_array' ) );
		add_filter( 'pf_list_posts_cpt-pojo_gallery', array( &$this, 'pf_list_posts_cpt' ) );
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_gallery_metabox' ) );
		add_filter( 'po_get_default_display_type', array( &$this, 'po_get_default_display_type' ), 10, 2 );

		// Settings.
		add_filter( 'pojo_register_settings_sections', array( &$this, 'settings_section_gallery' ), 125 );
		
		// Advanced Options
		add_filter( 'po_init_fields-pojo_gallery', array( &$this, 'advanced_options' ), 50 );
		
		// Customizer
		add_filter( 'pojo_get_layouts_custom_post_types', array( &$this, 'pojo_get_layouts_custom_post_types' ), 10, 2 );
		add_filter( 'po_get_archive_query', array( &$this, 'po_get_archive_query' ), 10, 2 );
		add_filter( 'pojo_post_classes', array( &$this, 'pojo_post_classes' ), 10, 2 );
		add_filter( 'post_class', array( &$this, 'gallery_single_post_classes' ), 10, 3 );
		add_action( 'pojo_before_content_loop', array( &$this, 'pojo_before_content_loop' ), 20 );
	}

}