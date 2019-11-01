<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Catalog extends Pojo_Widget_Base {
	
	protected static $catalog_index = 1;

	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);

		$repeater_fields = array();

		$repeater_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'desc' => __( 'Enter the list item title here.', 'pojo' ),
			'std' => '',
		);

		$repeater_fields[] = array(
			'id' => 'description',
			'title' => __( 'Description:', 'pojo' ),
			'desc' => __( 'Enter the item description here.', 'pojo' ),
			'type' => 'textarea',
			'std' => '',
			'filter' => array( &$this, '_filter_wysiwyg' ),
		);

		$repeater_fields[] = array(
			'id' => 'pricing',
			'title' => __( 'Pricing:', 'pojo' ),
			'desc' => __( 'Enter the price for the item here, Eg: 34$, 55.5€, £12, 14₪.', 'pojo' ),
			'std' => '',
		);

		$repeater_fields[] = array(
			'id' => 'image',
			'title' => __( 'Choose Image:', 'pojo' ),
			'desc' => __( 'Upload a new, or choose a image from your media library.', 'pojo' ),
			'type' => 'image',
			'std' => '',
		);

		$repeater_fields[] = array(
			'id' => 'item_link',
			'title' => __( 'Item Link', 'pojo' ),
			'desc' => __( 'Where should your item link to?', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'No link', 'pojo' ),
				'thumbnail' => __( 'Link to thumbnail in lightbox (image needs to be set).', 'pojo' ),
				'custom_link' => __( 'Set custom link', 'pojo' ),
			),
			'std' => '',
			//'filter' => array( &$this, '_valid_by_options' ),
		);

		$repeater_fields[] = array(
			'id' => 'link_to',
			'title' => __( 'Link:', 'pojo' ),
			'placeholder' => 'http://',
			'std' => '',
			'filter' => 'esc_url_raw',
		);
		
		$repeater_fields[] = array(
			'id' => 'target_link',
			'title' => __( 'Open Link in', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'Same Window', 'pojo' ),
				'blank' => __( 'New Tab or Window', 'pojo' ),
			),
			'std' => '',
			//'filter' => array( &$this, '_valid_by_options' ),
		);
		
		$repeater_fields[] = array(
			'id' => 'disable_item',
			'title' => __( 'Disable item', 'pojo' ),
			'desc' => __( 'Temporarily disable and hide the item without deleting it.', 'pojo' ),
			'type' => 'checkbox',
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'items',
			'title' => __( 'Items', 'pojo' ),
			'type' => 'repeater',
			'fields' => $repeater_fields,
			'std' => array(),
		);

		$this->_form_fields[] = array(
			'id' => 'thumb_shape',
			'title' => __( 'Thumbnail Shape:', 'pojo' ),
			'type' => 'select',
			'std' => 'circle',
			'options' => array(
				'circle' => __( 'Circle', 'pojo' ),
				'square' => __( 'Square', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'thumb_size',
			'title' => __( 'Thumbnail Size:', 'pojo' ),
			'type' => 'select',
			'std' => 'small',
			'options' => array(
				'small' => __( 'Small', 'pojo' ),
				'medium' => __( 'Medium', 'pojo' ),
				'large' => __( 'Large', 'pojo' ),
				'fullsize' => __( 'Full size', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		parent::__construct(
			'pojo_catalog',
			__( 'Catalog', 'pojo' ),
			array( 'description' => __( 'Display a product catalog or pricing list.', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		if ( empty( $instance['items'] ) )
			return;

		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );

		$panels = array();
		foreach ( $instance['items'] as $item_index => $item ) :
			$return = $this->_get_front_item( $item, $instance );
			if ( ! empty( $return ) )
				$panels[] = $return;
		endforeach;
		
		if ( empty( $panels ) )
			return;

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		$ul_classes = array(
			'pojo-catalog-list',
			'thumbnail-shape-' . $instance['thumb_shape'],
			'thumbnail-size-' . $instance['thumb_size'],
		);

		echo '<div class="pojo-catalog">';
		echo '<ul class="' . esc_attr( implode( ' ', $ul_classes ) ) . '">';
		echo implode( '', $panels );
		echo '</ul>';
		echo '</div>';
		
		echo $args['after_widget'];
		
		self::$catalog_index++;
	}

	protected function _get_front_item( $item, $instance ) {
		if ( isset( $item['disable_item'] ) && '1' === $item['disable_item'] )
			return '';
		
		ob_start();

		$title     = ! empty( $item['title'] ) ? $item['title'] : '';
		$thumbnail = '';
		if ( ! empty( $item['image'] ) ) {
			if ( 'fullsize' !== $instance['thumb_size'] ) {
				$thumbnail = Pojo_Thumbnails::get_thumb( $item['image'], array( 'width' => 100, 'height' => 100 ) );
			} else {
				$thumbnail = $item['image']; // Full size
			}
		}

		if ( ! empty( $thumbnail ) ) : ?>
			<img class="catalog-item-thumbnail" src="<?php echo esc_attr( $thumbnail ); ?>" alt="<?php echo esc_attr( $title ); ?>" />
		<?php endif; ?>
		<div class="catalog-item-content">
			<?php if ( ! empty( $title ) ) : ?>
				<h5 class="catalog-item-title"><?php echo $title; ?></h5>
			<?php endif; ?>
			<?php if ( ! empty( $item['description'] ) ) : ?>
				<div class="catalog-item-description"><?php echo wpautop( $item['description'] ); ?></div>
			<?php endif; ?>
		</div>
		<?php if ( ! empty( $item['pricing'] ) ) : ?>
			<div class="catalog-item-price"><span><?php echo $item['pricing']; ?></span></div>
		<?php endif;
		
		$content = sprintf( '<li>%s</li>', $this->_get_item_link_wrapper( ob_get_clean(), $item ) );
		return $content;
	}
	
	protected function _get_item_link_wrapper( $content, $item ) {
		$url = '';
		$link_attributes = array();
		
		if ( ! empty( $item['item_link'] ) ) {
			switch ( $item['item_link'] ) {
				case 'thumbnail' :
					if ( ! empty( $item['image'] ) ) {
						$url                    = $item['image'];
						$link_attributes['rel'] = 'lightbox[catalog-' . self::$catalog_index . ']';
					}

					break;

				case 'custom_link' :
					if ( ! empty( $item['link_to'] ) ) {
						if ( ! empty( $item['target_link'] ) && 'blank' === $item['target_link'] ) {
							$link_attributes['target'] = '_blank';
						}
						$url = $item['link_to'];
					}
					break;
			}
		}
		
		if ( ! empty( $url ) ) {
			$content = sprintf( '<a class="catalog-item-link pojo-catalog-item" href="%s"%s>%s</a>', $url, pojo_array_to_attributes( $link_attributes ), $content );
		} else {
			$content = sprintf( '<div class="pojo-catalog-item">%s</div>', $content );
		}
		return $content;
	}

}