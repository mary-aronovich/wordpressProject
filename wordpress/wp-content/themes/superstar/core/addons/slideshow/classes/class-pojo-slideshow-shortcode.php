<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Slideshow_Shortcode {

	//protected static $_index = array();
	
	public function do_shortcode( $atts = array() ) {
		$atts = wp_parse_args( $atts, array( 'id' => 0 ) );
		
		if ( empty( $atts['id'] ) || ! $slide = get_post( $atts['id'] ) )
			return '';
		
		if ( 'pojo_slideshow' !== $slide->post_type )
			return '';

		$repeater_slides = atmb_get_field_without_type( 'slides', 'slide_',  $slide->ID );
		
		if ( empty( $repeater_slides ) )
			return '';
		
		// Global Options
		$slideshow_style = atmb_get_field( 'slide_slideshow_style', $slide->ID );
		
		$slide_title = atmb_get_field( 'slide_title', $slide->ID );
		$slide_auto_play = atmb_get_field( 'slide_auto_play', $slide->ID );
		$slide_auto_pause_hover = atmb_get_field( 'slide_auto_pause_hover', $slide->ID );

		$navigation = atmb_get_field( 'slide_navigation', $slide->ID );

		$edit_slide_link = '';
		if ( current_user_can( 'publish_posts' ) && ! is_admin() )
			$edit_slide_link = sprintf( '<a href="%s" class="button size-small edit-slideshow edit-link"><i class="fa fa-pencil"></i> %s</a>', get_edit_post_link( $slide->ID ), __( 'Edit Slideshow', 'pojo' ) );

		if ( 'carousel' === $slideshow_style ) {
			$wrapper_width = '100%';

			$slide_width = absint( atmb_get_field( 'slide_slide_width', $slide->ID ) );
			if ( empty( $slide_width ) || 0 === $slide_width )
				$slide_width = '200';

			$slide_height = absint( atmb_get_field( 'slide_slide_height', $slide->ID ) );
			if ( empty( $slide_height ) || 0 === $slide_height )
				$slide_height = '200';

			$hover_animation = atmb_get_field( 'slide_hover_animation', $slide->ID );

			$panels = array();
			foreach ( $repeater_slides as $repeater_slide ) {
				$image_html = '';
				if ( ! empty( $repeater_slide['image'] ) ) {
					$attachment_url = Pojo_Thumbnails::get_attachment_image_src(
						$repeater_slide['image'],
						apply_filters(
							'pojo_slideshow_carousel_thumbnail_args',
							array(
								'width' => $slide_width,
								'height' => $slide_height,
								'crop' => true,
								'placeholder' => true,
							)
						)
					);

					if ( $attachment_url ) {
						$image_title = $repeater_slide['caption'];
						if ( empty( $image_title ) )
							$image_title = get_the_title( $repeater_slide['image'] );
						
						$img_classes = array( 'carousel-image' );
						if ( ! empty( $hover_animation ) )
							$img_classes[] = 'hover-' . $hover_animation;

						$image_html = sprintf( '<div style="width: %3$s; height: %4$s;"><img src="%1$s" alt="%2$s" title="%2$s" class="%5$s" /></div>', $attachment_url, esc_attr( $image_title ), esc_attr( $wrapper_width ), esc_attr( $slide_height . 'px' ), esc_attr( implode( ' ', $img_classes ) ) );
					}
				}

				if ( empty( $image_html ) && empty( $repeater_slide['html'] ) )
					continue;

				$panel_html = '';
				if ( ! empty( $image_html ) ) {
					$panel_html = $image_html;
					if ( ! empty( $repeater_slide['link'] ) ) {
						$repeater_slide['link'] = esc_url( $repeater_slide['link'] );
						if ( ! empty( $repeater_slide['link'] ) ) {
							$target = '';
							if ( ! empty( $repeater_slide['target_link'] ) && 'blank' === $repeater_slide['target_link'] ) {
								$target = ' target="_' . $repeater_slide['target_link'] . '"';
							}
							$panel_html = sprintf( '<a href="%s"%s>%s</a>', $repeater_slide['link'], $target, $panel_html );
						}
					}
				}
				$panels[] = sprintf( '<div class="slide">%s</div>', $panel_html );
			}

			if ( empty( $panels ) )
				return '';

			$js_array = array();
			
			$js_array['slideWidth'] = $slide_width;
			
			if ( $meta = atmb_get_field( 'slide_minimum_slides', $slide->ID ) ) {
				$js_array['minSlides'] = absint( $meta );
			}

			if ( $meta = atmb_get_field( 'slide_maximum_slides', $slide->ID ) ) {
				$js_array['maxSlides'] = absint( $meta );
			}
			
			if ( $meta = atmb_get_field( 'slide_move_slides', $slide->ID ) ) {
				$js_array['moveSlides'] = absint( $meta );
			}

			$slide_margin = atmb_get_field( 'slide_slide_margin', $slide->ID );
			if ( ! $slide_margin && '0' !== $slide_margin )
				$slide_margin = 10;
			$js_array['slideMargin'] = absint( $slide_margin );
		

			$meta = absint( atmb_get_field( 'slide_slide_duration', $slide->ID ) );
			if ( empty( $meta ) || 0 === $meta )
				$meta = 5000;
			$js_array['pause'] = $meta;

			$meta = absint( atmb_get_field( 'slide_transition_speed', $slide->ID ) );
			if ( empty( $meta ) || 0 === $meta )
				$meta = 200;
			
			$js_array['speed']     = $meta;
			$js_array['captions']  = 'hide' !== $slide_title;
			$js_array['autoStart'] = 'off' !== $slide_auto_play;
			$js_array['autoHover'] = 'off' !== $slide_auto_pause_hover;

			$js_array['auto'] = true;

			$js_array['pager']    = 'bullets' === $navigation || 'both' === $navigation;
			$js_array['controls'] = empty( $navigation ) || 'both' === $navigation;


			$js_json = ! empty( $js_array ) ? json_encode( $js_array ) : '';
			$print_js = '<script>jQuery(function($){$("div.pojo-slideshow-' . $slide->ID . '").bxSlider(' . $js_json . ');});</script>';

			return sprintf(
				'%s<div style="width: %s; height: %s; direction: ltr;" class="pojo-slideshow%s"><div class="pojo-slideshow-%d pojo-slideshow-wrapper">%s</div>%s</div>',
				$print_js,
				esc_attr( $wrapper_width ),
				esc_attr( $slide_height . 'px' ),
				$js_array['pager'] ? ' slideshow-bullets' : '',
				$slide->ID,
				implode( '', $panels ),
				$edit_slide_link
			);
		} elseif ( empty( $slideshow_style ) || 'slider' === $slideshow_style ) {
			$slide_duration = atmb_get_field( 'slide_slideshow_duration', $slide->ID );
			if ( empty( $slide_duration ) )
				$slide_duration = 5;
			
			$output_array = array();
			foreach ( $repeater_slides as $repeater_slide ) {
				if ( ! empty( $repeater_slide['image'] ) ) {
					$attachment_url = wp_get_attachment_image_src( $repeater_slide['image'], 'full' );
					if ( ! $attachment_url )
						continue;
				
					$image_title = $repeater_slide['caption'];
					if ( empty( $image_title ) )
						$image_title = get_the_title( $repeater_slide['image'] );

					$panel_html  = '<div class="ms-slide" data-delay="{slide-duration}">';
					$panel_html .= '<img src="{image-blank}" data-src="{image-source}" alt="{image-alt}" />';
					if ( 'hide' !== $slide_title && ! empty( $image_title ) ) {
						$panel_html .= '<div class="ms-info">{caption}</div>';
					}
					if ( ! empty( $repeater_slide['link'] ) ) {
						$panel_html .= '<a href="{link}"{link-target}>{caption}</a>';
					} else {
						$repeater_slide['link'] = '';
					}
					$panel_html .= '</div>';

					$output_array[] = strtr(
						$panel_html,
						array(
							'{image-blank}' => get_template_directory_uri() . '/core/assets/masterslider/blank.gif',
							'{image-source}' => $attachment_url[0],
							'{image-alt}' => esc_attr( $image_title ),
							'{slide-duration}' => $slide_duration,
							'{caption}' => $image_title,
							'{link}' => $repeater_slide['link'],
							'{link-target}' => ! empty( $repeater_slide['target_link'] ) && 'blank' === $repeater_slide['target_link'] ? ' target="_' . $repeater_slide['target_link'] . '"' : '',
						)
					);
				}
			}
			
			if ( ! empty( $output_array) ) {
				if ( 0 === $slider_width = absint( atmb_get_field( 'slide_width', $slide->ID ) ) )
					$slider_width = '1920';

				if ( 0 === $slider_height = absint( atmb_get_field( 'slide_height', $slide->ID ) ) )
					$slider_height = '1080';
				
				$uniqid = uniqid();

				$js_options = array(
					'id' => 'pojo-slideshow-' . $uniqid,
					'arrows' => empty( $navigation ) || 'both' === $navigation,
					'thumblist' => false,
					'bullets' => 'bullets' === $navigation || 'both' === $navigation,
					'params' => array(
						'autoplay' => 'off' !== $slide_auto_play,
						'overPause' => 'off' !== $slide_auto_pause_hover,
						'width' => $slider_width,
						'height' => $slider_height,
						//'fillMode' => 'fit',
					),
				);

				$transitions = atmb_get_field( 'slide_transition_style', $slide->ID );
				if ( 'slide_vertical' === $transitions ) {
					$js_options['params']['dir'] = 'v';
				}

				switch ( $transitions ) {
					case 'slide_vertical' :
					case 'slide_horizontal' :
						$view = 'basic';
						break;

					default :
						$view = 'fade';
				}

				$js_options['params']['view'] = $view;
				Pojo_MasterSlider::add_slider( $js_options );
				return sprintf(
					'<div style="direction: ltr;" class="pojo-slideshow">
						<div class="pojo-slideshow-%1$s pojo-slideshow-%2$d pojo-slideshow-wrapper master-slider ms-skin-pojo" id="pojo-slideshow-%1$s">%3$s</div>%4$s
					</div>',
					$uniqid,
					$slide->ID,
					implode( '', $output_array ),
					$edit_slide_link
				);
			}
		}

		// Empty return..
		return '';
	}

	public function __construct() {
		add_shortcode( 'pojo-slideshow', array( &$this, 'do_shortcode' ) );
	}
	
}