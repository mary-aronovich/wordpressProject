<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Gallery_Front {
	
	protected $_index = 1;
	protected $_print_js = array();
	
	public function pojo_gallery_print_front( $post_id = null ) {
		if ( is_null( $post_id ) )
			$post_id = get_the_ID();
		
		if ( post_password_required( $post_id ) ) {
			return;
		}

		$gallery_type = atmb_get_field( 'gallery_galleries_type', $post_id );
		$items = atmb_get_field( 'gallery_gallery', $post_id );
		
		if ( empty( $items ) )
			return;
		
		if ( empty( $gallery_type ) )
			$gallery_type = 'simple';

		if ( 'simple' === $gallery_type ) :
		
			$link_to = atmb_get_field( 'gallery_link_to', $post_id );
			$columns = absint( atmb_get_field( 'gallery_columns', $post_id ) );
			$random_order = atmb_get_field( 'gallery_random_order', $post_id, Pojo_MetaBox::FIELD_CHECKBOX );
			$image_size = atmb_get_field( 'gallery_image_size', $post_id );
			if ( 0 === $columns )
				$columns = 1;
			if ( empty( $link_to ) )
				$link_to = 'file';
			
			$shortcode_args = array();
			$shortcode_args[] = sprintf( 'columns="%d"', $columns );
			$shortcode_args[] = sprintf( 'link="%s"', $link_to );
			$shortcode_args[] = sprintf( 'ids="%s"', $items );
			
			if ( ! empty( $image_size ) )
				$shortcode_args[] = sprintf( 'size="%s"', $image_size );
			
			if ( $random_order )
				$shortcode_args[] = 'orderby="rand"';
			
			$shortcode = sprintf( '[gallery %s]', implode( ' ', $shortcode_args ) );

			add_filter( 'wp_get_attachment_link', [ $this, 'add_elementor_lightbox_data_to_image_link' ] );
			echo do_shortcode( $shortcode );
			remove_filter( 'wp_get_attachment_link', [ $this, 'add_elementor_lightbox_data_to_image_link' ] );
		
		elseif ( 'slideshow' === $gallery_type ) :
		
			$items_ids = explode( ',', $items );
			$output_array = $pager_array = array();
			
			$caption = atmb_get_field( 'gallery_caption', $post_id );
			$transitions = atmb_get_field( 'gallery_transitions', $post_id );
			$lightbox = atmb_get_field( 'gallery_lightbox', $post_id );
			$slide_duration = atmb_get_field( 'gallery_slide_duration', $post_id );
			if ( empty( $slide_duration ) )
				$slide_duration = 5;
			
			$thumb_ratio = atmb_get_field( 'gallery_thumb_ratio', $post_id );
			
			if ( ! empty( $thumb_ratio ) && ! in_array( $thumb_ratio, array( '1_1', '4_3', 'none' ) ) )
				$thumb_ratio = '';
			
			switch ( $thumb_ratio ) {
				case '1_1' :
					$thumb_params = array(
						'width' => '75',
						'height' => '75',
						'crop' => true,
					);
					break;
				case '4_3' :
					$thumb_params = array(
						'width' => '120',
						'height' => '90',
						'crop' => true,
					);
					break;
				default :
					$thumb_params = array(
						'width' => '160',
						'height' => '90',
						'crop' => true,
					);
					break;
			}
			
			foreach ( $items_ids as $key => $item_id ) :
				$attachment = get_post( $item_id );
				$attachment_url = wp_get_attachment_image_src( $item_id, 'full' );
				if ( empty( $attachment_url ) )
					continue;
				
				$panel_html = '<div class="ms-slide" data-delay="{slide-duration}">';
				$panel_html .= '<img src="{image-blank}" data-src="{image-source}" alt="{image-alt}" />';
				if ( 'none' !== $thumb_ratio ) :
					$panel_html .= '<img class="ms-thumb" src="{image-thumbnail}" alt="{image-alt}" />';
				endif;
				
				if ( 'hide' !== $caption && ! empty( $attachment->post_excerpt ) ) :
					$panel_html .= '<div class="ms-info">' . $attachment->post_excerpt . '</div>';
				endif;
				
				if ( 'hide' !== $lightbox ) :
					$panel_html .= '<a href="{image-source}" class="ms-lightbox" data-elementor-lightbox-slideshow="{post-id}" rel="lightbox[gallery{post-id}]" title="{image-alt}">Preview</a>';
				endif;
				
				$panel_html .= '</div>';
				
				$image_alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
				if ( empty( $image_alt ) )
					$image_alt = $attachment->post_title;

				$output_array[] = strtr( $panel_html, array(
					'{image-blank}' => get_template_directory_uri() . '/core/assets/masterslider/blank.gif',
					'{image-source}' => $attachment_url[0],
					'{image-thumbnail}' => Pojo_Thumbnails::get_attachment_image_src( $item_id, $thumb_params ),
					'{image-alt}' => esc_attr( $image_alt ),
					'{post-id}' => $post_id,
					'{slide-duration}' => $slide_duration,
				) );
			endforeach;
			
			if ( ! empty( $output_array ) ) :
				$js_options = array(
					'id' => 'pojo-gallery-' . $post_id,
					'arrows' => 'hide' !== atmb_get_field( 'gallery_arrow', $post_id ),
					'lightbox' => 'hide' !== atmb_get_field( 'gallery_lightbox', $post_id ),
					'params' => array(
						'autoplay' => 'off' !== atmb_get_field( 'gallery_autoplay', $post_id ),
					),
					'thumblist_params' => array(
						//'dir' => 'bottom' === atmb_get_field( 'gallery_thumb_position', $post_id ) ? 'h' : 'v',
					),
				);
				
				if ( 'slide_vertical' === $transitions ) {
					$js_options['params']['dir'] = 'v';
				}

				$slide_width = absint( atmb_get_field( 'gallery_slide_width', $post_id ) );
				if ( ! empty( $slide_width ) && 0 !== $slide_width ) {
					$js_options['params']['width'] = $slide_width;
				}
				
				$slide_height = absint( atmb_get_field( 'gallery_slide_height', $post_id ) );
				if ( ! empty( $slide_height ) && 0 !== $slide_height ) {
					$js_options['params']['height'] = $slide_height;
				}
				
				$js_options['params']['fullwidth'] = 'no' !== atmb_get_field( 'gallery_slide_fullwidth', $post_id );
				
				$js_options['params']['autoHeight'] = 'yes' === atmb_get_field( 'gallery_slide_auto_height', $post_id );
				
				$js_options['thumblist'] = 'none' !== $thumb_ratio;
				
				switch ( $transitions ) {
					case 'slide_vertical' :
					case 'slide_horizontal' :
						$view = 'basic';
						break;
					
					case 'scale' :
						$view = 'scale';
						break;
					
					default :
						$view = 'fade';
				}

				$js_options['params']['view'] = $view;
				
				Pojo_MasterSlider::add_slider( $js_options );
				printf(
					'<div style="direction: ltr;" class="pojo-gallery%3$s">
						<div class="pojo-gallery-%1$d pojo-gallery-wrapper master-slider ms-skin-pojo" id="pojo-gallery-%1$d">%2$s</div>
					</div>',
					$post_id,
					implode( '', $output_array ),
					! empty( $thumb_ratio ) ? ' thumb-ratio-' . $thumb_ratio : ''
				);
			endif;
		
		endif;

		$this->_index++;
	}

	public function add_elementor_lightbox_data_to_image_link( $link_html ) {
		if ( Pojo_Compatibility::is_elementor_installed() ) {
			$link_html = preg_replace( '/^<a/', '<a data-elementor-lightbox-slideshow="' . $this->_index . '"', $link_html );
		}

		return $link_html;
	}
	
	public function __construct() {
		add_action( 'pojo_gallery_print_front', array( &$this, 'pojo_gallery_print_front' ) );
	}
	
}
