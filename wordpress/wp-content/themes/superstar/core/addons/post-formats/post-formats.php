<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Post_Format {
	
	const META_BOX_CONTEXT = 'pojo-formats-panel';
	
	public function register_metabox_formats( $meta_boxes = array() ) {
		if ( current_theme_supports( 'post-formats' ) && current_theme_supports( 'pojo-post-formats' ) ) {
			$cpt_with_formats = array();
			$post_types       = get_post_types();

			foreach ( $post_types as $cpt ) {
				if ( post_type_supports( $cpt, 'post-formats' ) )
					$cpt_with_formats[] = $cpt;
			}

			if ( ! empty( $cpt_with_formats ) ) {
				$supports = get_theme_support( 'post-formats' );
				$supports = $supports[0];
				
				if ( in_array( 'link', $supports ) )
					$meta_boxes[] = $this->_format_link( $cpt_with_formats );
				
				if ( in_array( 'gallery', $supports ) )
					$meta_boxes[] = $this->_format_gallery( $cpt_with_formats );
				
				if ( in_array( 'video', $supports ) )
					$meta_boxes[] = $this->_format_video( $cpt_with_formats );
				
				if ( in_array( 'audio', $supports ) )
					$meta_boxes[] = $this->_format_audio( $cpt_with_formats );
			}
		}
		
		return $meta_boxes;
	}

	public function print_metaboxes() {
		global $post;
		do_meta_boxes( get_current_screen(), self::META_BOX_CONTEXT, $post );
	}

	protected function _format_link( $post_types ) {
		$fields = array();

		$fields[] = array(
			'id' => 'link_description',
			'type' => Pojo_MetaBox::FIELD_DESCRIPTION,
			'desc' => __( 'Input your link.', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'link_url',
			'title' => __( 'Link URL', 'pojo' ),
			'placeholder' => 'http://pojo.me/',
			'std' => '',
		);

		$fields[] = array(
			'id'      => 'target_link',
			'title'   => __( 'Open Link in', 'pojo' ),
			'type'    => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Same Window', 'pojo' ),
				'blank' => __( 'New Tab or Window', 'pojo' ),
			),
			'std'     => '',
		);
		
		return array(
			'id'         => 'pojo-format-link',
			'title'      => __( 'Format: Link', 'pojo' ),
			'post_types' => $post_types,
			'context'    => self::META_BOX_CONTEXT,
			'priority'   => 'core',
			'prefix'     => 'format_',
			'fields'     => $fields,
		);
	}

	protected function _format_gallery( $post_types ) {
		$fields = array();

		$fields[] = array(
			'id' => 'gallery_description',
			'type' => Pojo_MetaBox::FIELD_DESCRIPTION,
			'desc' => __( 'Insert your Gallery', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'gallery',
			'title' => __( 'Gallery', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_GALLERY,
			'std' => '',
		);
		
		return array(
			'id'         => 'pojo-format-gallery',
			'title'      => __( 'Format: Gallery', 'pojo' ),
			'post_types' => $post_types,
			'context'    => self::META_BOX_CONTEXT,
			'priority'   => 'core',
			'prefix'     => 'format_',
			'fields'     => $fields,
		);
	}

	protected function _format_video( $post_types ) {
		$fields = array();

		$fields[] = array(
			'id' => 'video_description',
			'type' => Pojo_MetaBox::FIELD_DESCRIPTION,
			'desc' => __( 'These settings enable you to embed videos into your posts.', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'aspect_ratio',
			'title' => __( 'Embed Ratio', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'desc' => __( 'Select the aspect ratio for your video.', 'pojo' ),
			'options' => array(
				'16:9' => __( '16:9', 'pojo' ),
				'4:3' => __( '4:3', 'pojo' ),
				'3:2' => __( '3:2', 'pojo' ),
			),
			'std' => '16:9',
		);

		$fields[] = array(
			'id' => 'video_link',
			'title' => __( 'Video Link', 'pojo' ),
			'desc' => sprintf( __( 'Insert the URL of the media. Read more about <a href="%s" target="_blank">WordPress Embeds</a>', 'pojo' ), 'http://codex.wordpress.org/Embeds' ),
			'std' => '',
		);
		
		return array(
			'id'         => 'pojo-format-video',
			'title'      => __( 'Format: Video', 'pojo' ),
			'post_types' => $post_types,
			'context'    => self::META_BOX_CONTEXT,
			'priority'   => 'core',
			'prefix'     => 'format_',
			'fields'     => $fields,
		);
	}

	protected function _format_audio( $post_types ) {
		$fields = array();

		$fields[] = array(
			'id' => 'audio_description',
			'type' => Pojo_MetaBox::FIELD_DESCRIPTION,
			'desc' => __( 'These settings enable you to embed audio into your posts. You must provide both .mp3 and .agg/.oga file formats in order for self hosted audio to function accross all browsers.', 'pojo' ),
		);

		$fields[] = array(
			'id' => 'mp3_url',
			'title' => __( 'MP3 File URL', 'pojo' ),
			'desc' => __( 'The URL to the .mp3 or .m4a audio file.', 'pojo' ),
			'std' => '',
		);

		$fields[] = array(
			'id' => 'oga_url',
			'title' => __( 'OGA File URL', 'pojo' ),
			'desc' => __( 'The URL to the .oga or .ogg audio file.', 'pojo' ),
			'std' => '',
		);

		$fields[] = array(
			'id' => 'embed_url',
			'title' => __( 'Embed URL', 'pojo' ),
			'desc' => sprintf( __( 'Insert the URL of the media. Read more about <a href="%s" target="_blank">WordPress Embeds</a>', 'pojo' ), 'http://codex.wordpress.org/Embeds' ),
			'std' => '',
		);
		
		return array(
			'id'         => 'pojo-format-audio',
			'title'      => __( 'Format: Audio', 'pojo' ),
			'post_types' => $post_types,
			'context'    => self::META_BOX_CONTEXT,
			'priority'   => 'core',
			'prefix'     => 'format_',
			'fields'     => $fields,
		);
	}

	public function __construct() {
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_metabox_formats' ) );
		add_action( 'edit_form_after_title', array( &$this, 'print_metaboxes' ) );
	}
	
}
new Pojo_Post_Format();
