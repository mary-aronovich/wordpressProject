<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Slideshow_CPT {

	protected $_assets_images = null;

	public function get_assets_images() {
		if ( is_null( $this->_assets_images ) )
			$this->_assets_images = get_template_directory_uri() . '/core/assets/admin-ui/images';
		return $this->_assets_images;
	}

	public function init() {
		// CPT: pojo_slideshow.
		$labels = array(
			'name'               => __( 'Slideshows', 'pojo' ),
			'singular_name'      => __( 'Slideshow', 'pojo' ),
			'add_new'            => __( 'Add New', 'pojo' ),
			'add_new_item'       => __( 'Add New Slideshow', 'pojo' ),
			'edit_item'          => __( 'Edit Slideshow', 'pojo' ),
			'new_item'           => __( 'New Slideshow', 'pojo' ),
			'all_items'          => __( 'All Slideshows', 'pojo' ),
			'view_item'          => __( 'View Slideshow', 'pojo' ),
			'search_items'       => __( 'Search Slideshow', 'pojo' ),
			'not_found'          => __( 'No slideshows found', 'pojo' ),
			'not_found_in_trash' => __( 'No slideshows found in Trash', 'pojo' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Slideshows', 'pojo' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 21,
			'supports'           => array( 'title' ),
		);
		register_post_type(
			'pojo_slideshow',
			apply_filters( 'pojo_register_post_type_slideshow', $args )
		);
		
		remove_post_type_support( 'pojo_slideshow', 'editor' );
	}

	public function post_updated_messages( $messages ) {
		global $post;

		$messages['pojo_slideshow'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Slideshow updated.', 'pojo' ),
			2  => __( 'Custom field updated.', 'pojo' ),
			3  => __( 'Custom field deleted.', 'pojo' ),
			4  => __( 'Slideshow updated.', 'pojo' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Slideshow restored to revision from %s', 'pojo' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Slideshow published.', 'pojo' ),
			7  => __( 'Slideshow saved.', 'pojo' ),
			8  => __( 'Slideshow submitted.', 'pojo' ),
			9  => sprintf( __( 'Post scheduled for: <strong>%1$s</strong>.', 'pojo' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'pojo' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Slideshow draft updated.', 'pojo' ),
		);

		return $messages;
	}
	
	public function admin_cpt_columns( $columns ) {
		return array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Slider Title', 'pojo' ),
			'slideshow_preview' => __( 'Preview Slideshow', 'pojo' ),
			'slideshow_count' => __( 'Slides', 'pojo' ),
			'slideshow_shortcode' => __( 'Shortcode', 'pojo' ),
			'date' => __( 'Date', 'pojo' ),
		);
	}
	
	public function custom_columns( $column ) {
		global $post, $pojo_slideshow;

		switch ( $column ) {
			case 'slideshow_preview' :
				printf( '<a href="javascript:void(0);" class="btn-admin-preview-shortcode" data-action="slideshow_preview_shortcode" data-id="%d">%s</a>', $post->ID, __( 'Preview', 'pojo' ) );
				break;
			
			case 'slideshow_count' :
				echo sizeof( atmb_get_field_without_type( 'slides', 'slide_',  $post->ID ) );
				break;
			
			case 'slideshow_shortcode' :
				echo $pojo_slideshow->helpers->get_shortcode_text( $post->ID );
				break;
		}
	}

	public function dashboard_glance_items( $elements ) {
		$post_type = 'pojo_slideshow';
		$num_posts = wp_count_posts( $post_type );
		if ( $num_posts && $num_posts->publish ) {
			$text = _n( '%s Slideshow', '%s Slideshows', $num_posts->publish, 'pojo' );
			$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
			printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text );
		}
		//return $elements;
	}
	
	public function register_style_metabox( $meta_boxes = array() ) {
		$base_radio_image_url = $this->get_assets_images();

		$fields = array();
		
		$slideshow_styles = array(
			'slider' => __( 'Slider', 'pojo' ),
			'carousel' => __( 'Carousel', 'pojo' ),
		);

		$slideshow_styles_radios = array();
		foreach ( $slideshow_styles as $key => $value ) {
			$slideshow_styles_radios[] = array(
				'id' => $key,
				'title' => $value,
				'image' => sprintf( '%s/slider-styles/%s.png', $base_radio_image_url, $key ),
			);
		}

		$fields[] = array(
			'id'    => 'slideshow_style',
			'type'  => Pojo_MetaBox::FIELD_RADIO_IMAGE,
			'classes' => array( 'select-show-or-hide-fields' ),
			'options' => $slideshow_styles_radios,
			'std' => 'slider',
		);
		
		
		$fields[] = array(
			'id'    => 'heading_slider_settings',
			'title' => __( 'Slider Settings', 'pojo' ),
			'type'  => Pojo_MetaBox::FIELD_HEADING,
			'show_on' => array( 'slide_slideshow_style' => 'slider' ),
		);
		
		$fields[] = array(
			'id'    => 'heading_carousel_settings',
			'title' => __( 'Carousel Settings', 'pojo' ),
			'type'  => Pojo_MetaBox::FIELD_HEADING,
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
		);

		$fields[] = array(
			'id'      => 'width',
			'title'   => __( 'Width Slider (px)', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'slider' ),
			'std'     => '1920',
		);

		$fields[] = array(
			'id'      => 'height',
			'title'   => __( 'Height Slider (px)', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'slider' ),
			'std'     => '1080',
		);

		$fields[] = array(
			'id'      => 'slide_width',
			'title'   => __( 'Slide Width (px)', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			'std'     => '200',
		);

		$fields[] = array(
			'id'      => 'slide_height',
			'title'   => __( 'Slide Height (px)', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			'std'     => '200',
		);

		$fields[] = array(
			'id'      => 'slide_margin',
			'title'   => __( 'Slide Margin (px)', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			'std'     => '10',
		);

		$fields[] = array(
			'id'      => 'minimum_slides',
			'title'   => __( 'Minimum Slides', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			'std'     => '2',
		);

		$fields[] = array(
			'id'      => 'maximum_slides',
			'title'   => __( 'Maximum Slides', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			'std'     => '2',
		);

		$fields[] = array(
			'id'      => 'move_slides',
			'title'   => __( 'Move Slides', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			'std'     => '1',
		);

		$fields[] = array(
			'id'      => 'hover_animation',
			'title'   => __( 'Hover Animation', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'std'     => '',
			'options' => array(
				'' => __( 'None', 'pojo' ),
				'grow' => __( 'Grow', 'pojo' ),
				'shrink' => __( 'Shrink', 'pojo' ),
				'pulse-grow' => __( 'Pulse Grow', 'pojo' ),
				'pulse-shrink' => __( 'Pulse Shrink', 'pojo' ),
				'push' => __( 'Push', 'pojo' ),
				'pop' => __( 'Pop', 'pojo' ),
				'rotate' => __( 'Rotate', 'pojo' ),
				'grow-rotate' => __( 'Grow Rotate', 'pojo' ),
				'float' => __( 'Float', 'pojo' ),
				'sink' => __( 'Sink', 'pojo' ),
				'hover' => __( 'Hover', 'pojo' ),
				'wobble-vertical' => __( 'Wobble Vertical', 'pojo' ),
				'wobble-horizontal' => __( 'Wobble Horizontal', 'pojo' ),
				'buzz' => __( 'Buzz', 'pojo' ),
			),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			
		);

		$fields[] = array(
			'id'      => 'navigation',
			'title'   => __( 'Navigation', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'std'     => '',
			'options' => array(
				'' => __( 'Arrows', 'pojo' ),
				'bullets' => __( 'Bullets', 'pojo' ),
				'both' => __( 'Both', 'pojo' ),
				'none' => __( 'None', 'pojo' ),
			),
		);

		$fields[] = array(
			'id'      => 'title',
			'title'   => __( 'Title', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'std'     => 'hide',
			'options' => array(
				'hide' => __( 'Hide', 'pojo' ),
				'' => __( 'Show', 'pojo' ),
			),
		);

		$fields[] = array(
			'id'      => 'transition_style',
			'title'   => __( 'Transition Style', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'show_on' => array( 'slide_slideshow_style' => 'slider' ),
			'std'     => '',    
			'options' => array(
				'fade' => __( 'Fade', 'pojo' ),
				'slide_horizontal' => __( 'Slide Horizontal', 'pojo' ),
				'slide_vertical' => __( 'Slide Vertical', 'pojo' ),
			),
		);

		$fields[] = array(
			'id'      => 'transition_speed',
			'title'   => __( 'Transition Speed (in ms)', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			'std'     => '300',
		);

		$fields[] = array(
			'id'      => 'slide_duration',
			'title'   => __( 'Slide Duration (in ms)', 'pojo' ),
			'show_on' => array( 'slide_slideshow_style' => 'carousel' ),
			'std'     => '5000',
		);
		
		$fields[] = array(
			'id'      => 'auto_play',
			'title'   => __( 'Auto Play', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'std'     => '',
			'options' => array(
				'' => __( 'On', 'pojo' ),
				'off' => __( 'Off', 'pojo' ),
			),
		);

		$fields[] = array(
			'id'    => 'slideshow_duration',
			'title' => __( 'Slide Duration (Seconds)', 'pojo' ),
			'std' => '',
			'placeholder' => '5',
			'show_on' => array( 'gallery_galleries_type' => 'slideshow' ),
		);

		$fields[] = array(
			'id'      => 'auto_pause_hover',
			'title'   => __( 'Auto Pause Hover', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'std'     => '',
			'options' => array(
				'' => __( 'On', 'pojo' ),
				'off' => __( 'Off', 'pojo' ),
			),
		);

		$meta_boxes[] = array(
			'id' => 'pojo-slideshow-settings',
			'title' => __( 'Slideshow Options', 'pojo' ),
			'post_types' => array( 'pojo_slideshow' ),
			'context' => 'side',
			'prefix' => 'slide_',
			'fields' => $fields,
		);

		return $meta_boxes;
	}

	public function register_slides_metabox( $meta_boxes = array() ) {
		$repeater_fields = $fields = array();

		$repeater_fields[] = array(
			'id'      => 'image',
			'type'    => Pojo_MetaBox::FIELD_IMAGE,
			'label_add_to_post' => __( 'Add to Slide', 'pojo' ),
			'std'     => '',
		);

		$repeater_fields[] = array(
			'id'      => 'slide_info_start',
			'type'    => Pojo_MetaBox::FIELD_WRAPPER,
			'wrap_class' => 'slide-info',
		);
		
		$repeater_fields[] = array(
			'id'      => 'caption',
			'title'   => __( 'Title', 'pojo' ),
			'std'     => '',
		);
		
		$repeater_fields[] = array(
			'id'      => 'link',
			'title'   => __( 'Add Link', 'pojo' ),
			'placeholder'     => 'http://',
			'std'     => '',
		);
		
		$repeater_fields[] = array(
			'id'      => 'target_link',
			'title'   => __( 'Open Link in', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Same Window', 'pojo' ),
				'blank' => __( 'New Tab or Window', 'pojo' ),
			),
			'std'     => '',
		);

		$repeater_fields[] = array(
			'id'      => 'slide_info_end',
			'type'    => Pojo_MetaBox::FIELD_WRAPPER,
			'wrap_class' => 'slide-info',
			'mode'    => 'end',
		);

		$fields[] = array(
			'id' => 'slides',
			'type' => Pojo_MetaBox::FIELD_REPEATER,
			'add_row_text' => __( '+ Add Slide', 'pojo' ),
			'fields' => $repeater_fields,
		);
		
		$meta_boxes[] = array(
			'id'         => 'pojo-slideshow-slides',
			'title'      => __( 'Slides', 'pojo' ),
			'post_types' => array( 'pojo_slideshow' ),
			'context'    => 'normal',
			'priority'   => 'core',
			'prefix'     => 'slide_',
			'fields'     => $fields,
		);

		return $meta_boxes;
	}
	
	public function post_row_actions( $actions, $post ) {
		/** @var $post WP_Post */
		if ( 'pojo_slideshow' === $post->post_type ) {
			// Remove quick edit
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	public function post_submitbox_misc_actions() {
		global $post, $pojo_slideshow;

		if ( 'pojo_slideshow' !== $post->post_type )
			return;
		?>
		<div class="misc-pub-section" id="slideshow-preview-shortcode">
			<input type="text" class="copy-paste-shortcode" value="<?php echo esc_attr( $pojo_slideshow->helpers->get_shortcode_text( $post->ID ) ); ?>" readonly />
			<span><?php _e( 'Copy and paste this shortcode into your Text editor or use with Slideshow Widget.', 'pojo' ); ?></span>
		</div>

		<div class="misc-pub-section">
			<?php printf( '<a href="javascript:void(0);" class="btn-admin-preview-shortcode button" data-action="slideshow_preview_shortcode" data-id="%d">%s</a>', $post->ID, __( 'Preview', 'pojo' ) ); ?>
		</div>
	<?php
	}

	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );

		add_filter( 'manage_edit-pojo_slideshow_columns', array( &$this, 'admin_cpt_columns' ) );
		add_action( 'manage_posts_custom_column', array( &$this, 'custom_columns' ) );

		add_action( 'dashboard_glance_items', array( &$this, 'dashboard_glance_items' ), 50 );

		add_action( 'post_submitbox_misc_actions', array( &$this, 'post_submitbox_misc_actions' ) );
		
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_style_metabox' ) );
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_slides_metabox' ) );
		add_filter( 'post_row_actions', array( &$this, 'post_row_actions' ), 10, 2 );
		
	}

}