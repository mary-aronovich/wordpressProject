<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_MasterSlider {

	/**
	 * @param $options
	 *
	 * @return bool
	 */
	public static function add_slider( $options ) {
		$options = wp_parse_args( $options, array(
			'params' => array(),
			'id' => '',
			'arrows' => true,
			'lightbox' => false,
			'thumblist' => true,
			'thumblist_params' => array(),
			'bullets' => false,
			'bullets_params' => array(),
		) );

		$options['params'] = wp_parse_args( $options['params'], array(
			'width' => 1920,
			'height' => 1080,
			'loop' => true,
			'autoplay' => true,
			'space' => 0,
			'centerControls' => false,
			'fullwidth' => true,
		) );

		$options['thumblist_params'] = wp_parse_args( $options['thumblist_params'], array(
			'dir' => 'h',
			'autohide' => false,
		) );

		$options['bullets_params'] = wp_parse_args( $options['bullets_params'], array(
			'dir' => 'h',
			'autohide' => true,
		) );
		
		if ( empty( $options['id'] ) )
			return false;
		
		$params = $options['params'];

		$slider_data = array(
			'id' => $options['id'],
			'params' => $params,
			'arrows' => $options['arrows'],
			'lightbox' => $options['lightbox'],
			'thumblist' => $options['thumblist'],
			'thumblist_params' => $options['thumblist_params'],
			'bullets' => $options['bullets'],
			'bullets_params' => $options['bullets_params'],
		);
		
		?><script>jQuery(function(){MasterSliderIntegration.createSlider(<?php echo wp_json_encode( $slider_data ); ?>);});</script><?php

		return true;
	}
}
