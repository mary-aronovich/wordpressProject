<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Embed_Video extends Pojo_Widget_Base {
	
	protected $_current_instance = array();
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'link',
			'title' => __( 'Video Link:', 'pojo' ),
			'std' => '',
			'placeholder' => 'http://www.youtube.com/watch?v=5O9q0NB2HL0',
			'filter' => 'esc_url_raw',
		);
		
		$this->_form_fields[] = array(
			'id' => 'ratio',
			'title' => __( 'Ratio:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'16:9' => __( '16:9', 'pojo' ),
				'4:3' => __( '4:3', 'pojo' ),
				'3:2' => __( '3:2', 'pojo' ),
			),
			'std' => '',
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'heading_youtube',
			'title' => __( 'YouTube Options', 'pojo' ),
			'type' => 'heading',
		);
		
		$this->_form_fields[] = array(
			'id' => 'yt_autoplay',
			'title' => __( 'AutoPlay', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'no' => __( 'No', 'pojo' ),
				'yes' => __( 'Yes', 'pojo' ),
			),
			'std' => 'no',
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'yt_rel_videos',
			'title' => __( 'Show suggested videos when the video finishes', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'yes' => __( 'Yes', 'pojo' ),
				'no' => __( 'No', 'pojo' ),
			),
			'std' => 'yes',
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'yt_controls',
			'title' => __( 'Show player controls', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'yes' => __( 'Yes', 'pojo' ),
				'no' => __( 'No', 'pojo' ),
			),
			'std' => 'yes',
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$this->_form_fields[] = array(
			'id' => 'yt_showinfo',
			'title' => __( 'Show video title and player actions', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'yes' => __( 'Yes', 'pojo' ),
				'no' => __( 'No', 'pojo' ),
			),
			'std' => 'yes',
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		parent::__construct(
			'pojo_embed_video',
			__( 'Video', 'pojo' ),
			array( 'description' => __( 'Embed a video in your site', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		if ( empty( $instance['link'] ) )
			return;

		$this->_current_instance = $instance;
		
		add_filter( 'oembed_result', array( &$this, 'filter_oembed_result' ), 50, 3 );
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		printf( '<div class="custom-embed" data-save_ratio="%s">%s</div>', $instance['ratio'], wp_oembed_get( $instance['link'], wp_embed_defaults() ) );
		
		echo $args['after_widget'];
		remove_filter( 'oembed_result', array( &$this, 'filter_oembed_result' ), 50 );
	}
	
	public function filter_oembed_result( $html, $url, $args ) {
		$youtube_params = array();
		
		if ( 'yes' === $this->_current_instance['yt_autoplay'] )
			$youtube_params[] = 'autoplay=1';
		
		if ( 'no' === $this->_current_instance['yt_rel_videos'] )
			$youtube_params[] = 'rel=0';
		
		if ( 'no' === $this->_current_instance['yt_controls'] )
			$youtube_params[] = 'controls=0';
		
		if ( 'no' === $this->_current_instance['yt_showinfo'] )
			$youtube_params[] = 'showinfo=0';
		
		// TODO: Check if is youtube link
		$youtube_params[] = 'wmode=opaque';
		
		if ( ! empty( $youtube_params ) ) {
			$separator = '&amp;';
			$html = str_replace( '?feature=oembed', '?feature=oembed' . $separator . implode( $separator, $youtube_params ), $html );
		}
		
		return $html;
	}

	public function widget_plain_text( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );

		if ( empty( $instance['link'] ) )
			return;

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		echo $instance['link'];
	}
	
}