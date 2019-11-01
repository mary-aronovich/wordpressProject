<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Testimonials extends Pojo_Widget_Base {

	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);

		$repeater_fields = array();

		$repeater_fields[] = array(
			'id' => 'name',
			'title' => __( 'Name:', 'pojo' ),
			'std' => '',
		);

		$repeater_fields[] = array(
			'id' => 'image',
			'title' => __( 'Choose Image:', 'pojo' ),
			'type' => 'image',
			'std' => '',
		);

		$repeater_fields[] = array(
			'id' => 'subtitle',
			'title' => __( 'Company / Subtitle Below Name:', 'pojo' ),
			'std' => '',
		);

		$repeater_fields[] = array(
			'id' => 'link_to',
			'title' => __( 'Link:', 'pojo' ),
			'placeholder' => 'http://',
			'std' => '',
			'filter' => 'esc_url_raw',
		);

		$repeater_fields[] = array(
			'id' => 'link_text',
			'title' => __( 'Link Text:', 'pojo' ),
			'std' => '',
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
			'id' => 'content',
			'title' => __( 'Content / Quote:', 'pojo' ),
			'type' => 'textarea',
			'std' => '',
			'filter' => array( &$this, '_filter_wysiwyg' ),
		);

		$this->_form_fields[] = array(
			'id' => 'items',
			'title' => __( 'Items', 'pojo' ),
			'type' => 'repeater',
			'fields' => $repeater_fields,
			'std' => array(),
		);

		$this->_form_fields[] = array(
			'id' => 'align',
			'title' => __( 'Align:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'None', 'pojo' ),
				'right' => __( 'Right', 'pojo' ),
				'left' => __( 'Left', 'pojo' ),
				'center' => __( 'Center', 'pojo' ),
			),
			'std' => '',
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'slider_duration',
			'title' => __( 'Autorotation:', 'pojo' ),
			'type' => 'select',
			'desc' => __( 'Slider will rotate every X seconds', 'pojo' ),
			'options' => array(
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
				'6' => '6',
				'7' => '7',
				'8' => '8',
				'9' => '9',
				'10' => '10',
				'15' => '15',
				'20' => '20',
				'25' => '25',
				'30' => '30',
				'40' => '40',
				'50' => '50',
				'60' => '60',
				'120' => '120',
			),
			'std' => '5',
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'slider_navigation',
			'title' => __( 'Navigation:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => array(
				'' => __( 'None', 'pojo' ),
				'arrows' => __( 'Arrows', 'pojo' ),
				'bullets' => __( 'Bullets', 'pojo' ),
				'both' => __( 'Both', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'slider_transition_style',
			'title' => __( 'Transition Style:', 'pojo' ),
			'type' => 'select',
			'std' => 'fade',
			'options' => array(
				'' => __( 'Fade', 'pojo' ),
				'slide_horizontal' => __( 'Slide Horizontal', 'pojo' ),
				'slide_vertical' => __( 'Slide Vertical', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'thumb_size',
			'title' => __( 'Thumbnail Size:', 'pojo' ),
			'type' => 'select',
			'std' => '',
			'options' => array(
				'' => __( 'Small Circle', 'pojo' ),
				'fullsize' => __( 'Full Size', 'pojo' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		parent::__construct(
			'pojo_testimonials',
			__( 'Testimonials', 'pojo' ),
			array( 'description' => __( 'Show testimonials regarding your products and services', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		if ( empty( $instance['items'] ) )
			return;

		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		$transition_style = ! empty( $instance['slider_transition_style'] ) ? $instance['slider_transition_style'] : '';
		$navigation       = ! empty( $instance['slider_navigation'] ) ? $instance['slider_navigation'] : '';

		$slider_data = array(
			'auto' => true,
			'pause' => $instance['slider_duration'] * 1000,
			'pager' => 'bullets' === $navigation || 'both' === $navigation,
			'controls' => 'arrows' === $navigation || 'both' === $navigation,
			'adaptiveHeight' => true,
		);

		if ( empty( $transition_style ) )
			$slider_data['mode'] = 'fade';
		elseif ( 'slide_vertical' === $transition_style )
			$slider_data['mode'] = 'vertical';

		$align_class = 'testimonial-align-';
		if ( ! in_array( $instance['align'], array( 'left', 'right', 'center' ) ) )
			$instance['align'] = '';

		if ( empty( $instance['align'] ) )
			$instance['align'] = 'none';

		$align_class .= $instance['align'];
		?>
		<div class="pojo-testimonials <?php echo esc_attr( $align_class ); ?>">
			<ul class="pojo-bxslider-handle" data-slider_options='<?php echo json_encode( $slider_data ); ?>'>
				<?php foreach ( $instance['items'] as $t_key => $item ) :
					$this->_print_item( $t_key, $item, $instance );
				endforeach; ?>
			</ul>
		</div>
		<?php
		echo $args['after_widget'];
	}
	
	protected function _print_item( $t_key, $item, $instance ) {
		$name        = ! empty( $item['name'] ) ? $item['name'] : '';
		$content     = ! empty( $item['content'] ) ? $item['content'] : '';
		$link_to     = ! empty( $item['link_to'] ) ? $item['link_to'] : '';
		$link_text   = ! empty( $item['link_text'] ) ? $item['link_text'] : '';
		$target_link = ! empty( $item['target_link'] ) && 'blank' === $item['target_link'] ? ' target="_blank"' : '';

		$image_url = '';
		if ( ! empty( $item['image'] ) ) {
			if ( empty( $instance['thumb_size'] ) )
				$image_url = Pojo_Thumbnails::get_thumb( $item['image'], array( 'width' => 96, 'height' => 96 ) );
			else
				$image_url = $item['image']; // Full size
		}
		?>
		<li class="pojo-testimonial">
			<?php if ( ! empty( $content ) ) : ?>
				<blockquote><?php echo wpautop( $content ); ?></blockquote>
			<?php endif; ?>
			<?php if ( ! empty( $image_url ) ) : ?>
				<div class="testimonial-thumbnail">
					<img src="<?php echo esc_attr( $image_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
				</div>
			<?php endif; ?>
			<div class="testimonial-meta">
				<?php if ( ! empty( $name ) ) : ?>
					<strong class="testimonial-author"><?php echo $name; ?></strong>
					<div class="testimonial-description">
						<?php if ( ! empty( $item['subtitle'] ) ) : ?>
							<span class="testimonial-subtitle"><?php echo $item['subtitle']; ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $link_to ) ) : ?>
							<span class="testimonial-link">- <a href="<?php echo esc_attr( $link_to ); ?>"<?php echo $target_link; ?>><?php echo ! empty( $link_text ) ? $link_text : $link_to; ?></a></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</li>
	<?php
	}

}