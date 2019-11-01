<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Scroll_Up {

	protected function _get_icons() {
		return array(
			'01' => 'chevron-up',
			'02' => 'angle-up',
			'03' => 'angle-double-up',
			'04' => 'caret-up',
			'05' => 'arrow-up',
			'06' => 'long-arrow-up ',
			'07' => 'chevron-circle-up',
			'08' => 'arrow-circle-up',
			'09' => 'arrow-circle-o-up',
			'10' => 'caret-square-o-up',
		);
	}

	public function customize_scroll_up( $sections = array() ) {
		$fields = array();

		$fields[] = array(
			'id' => 'pojo_scroll_up_visibility',
			'title' => __( 'Scroll Up', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_SELECT,
			'choices' => array(
				'' => __( 'Show on all devices', 'pojo' ),
				'visible-desktop' => __( 'Visible Desktop', 'pojo' ),
				'visible-tablet' => __( 'Visible Tablet', 'pojo' ),
				'visible-phone' => __( 'Visible Phone', 'pojo' ),
				'hidden-desktop' => __( 'Hidden Desktop', 'pojo' ),
				'hidden-tablet' => __( 'Hidden Tablet', 'pojo' ),
				'hidden-phone' => __( 'Hidden Phone', 'pojo' ),
				'disable' => __( 'Disable', 'pojo' ),
			),
			'std' => '',
		);
		
		$icons = array();
		$base_radio_image_url = get_template_directory_uri() . '/core/addons/scroll-up/images/';
		foreach ( $this->_get_icons() as $icon_key => $icon ) {
			$icons[] = array(
				'id' => $icon_key,
				'title' => '',
				'image' => $base_radio_image_url . $icon_key . '.png',
			);
		}

		$fields[] = array(
			'id' => 'scroll_up_icon',
			'title' => __( 'Icon', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_RADIO_IMAGE,
			'std' => '01',
			'choices' => $icons,
		);
		
		$icon_size = array();
		foreach ( array_merge( range( 8, 48 ), range( 60, 100, 10 ) ) as $pixel ) {
			$icon_size[ $pixel . 'px' ] = $pixel . 'px';
		}

		$fields[] = array(
			'id' => 'scroll_up_icon_size',
			'title' => __( 'Icon Size', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_SELECT,
			'choices' => $icon_size,
			'std' => '20px',
		);
		
		$fields[] = array(
			'id' => 'scroll_up_icon_width',
			'title' => __( 'Width', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_TEXT,
			'std' => '50px',
			'selector' => '#pojo-scroll-up',
			'change_type' => 'width',
		);
		
		$fields[] = array(
			'id' => 'scroll_up_icon_height',
			'title' => __( 'Height', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_TEXT,
			'std' => '50px',
			'selector' => '#pojo-scroll-up',
			'change_type' => 'height',
		);
		
		$fields[] = array(
			'id'    => 'scroll_up_color',
			'title' => __( 'Icon Color', 'pojo' ),
			'type'  => Pojo_Theme_Customize::FIELD_COLOR,
			'std'   => '#eeeeee',
			'selector' => '#pojo-scroll-up a',
			'change_type' => 'color',
		);

		$fields[] = array(
			'id'    => 'scroll_up_background',
			'title' => __( 'Background', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_BACKGROUND,
			'std' => array(
				'color' => '#333333',
				'image'  => '',
				'position'  => 'top center',
				'repeat' => 'repeat',
				'size' => 'auto',
				'attachment' => 'scroll',
				'opacity'   => '60'
			),
			'selector' => '#pojo-scroll-up',
			'change_type' => 'background',
		);

		$fields[] = array(
			'id' => 'scroll_up_border_radius',
			'title' => __( 'Border Radius', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_TEXT,
			'placeholder' => '6px',
			'std' => '',
		);
		
		$fields[] = array(
			'id' => 'scroll_up_position',
			'title' => __( 'Position', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_SELECT,
			'choices' => array(
				'right' => __( 'Right', 'pojo' ),
				'center' => __( 'Center', 'pojo' ),
				'left' => __( 'Left', 'pojo' ),
			),
			'std' => 'right',
		);
		
		$fields[] = array(
			'id' => 'scroll_up_distance',
			'title' => __( 'Distance from Top', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_SELECT,
			'choices' => array(
				'always' => __( 'Always', 'pojo' ),
				'10' => '10%',
				'20' => '20%',
				'30' => '30%',
				'40' => '40%',
				'' => '50%',
				'60' => '60%',
				'70' => '70%',
				'80' => '80%',
				'90' => '90%',
				'100' => '100%',
			),
			'std' => '',
		);
		
		$fields[] = array(
			'id' => 'scroll_up_duration',
			'title' => __( 'Scroll Duration', 'pojo' ),
			'type' => Pojo_Theme_Customize::FIELD_SELECT,
			'choices' => array(
				'slow' => __( 'Slow', 'pojo' ),
				'normal' => __( 'Normal', 'pojo' ),
				'fast' => __( 'Fast', 'pojo' ),
			),
			'std' => 'normal',
		);
		
		$sections[] = array(
			'id' => 'scroll_up',
			'title' => __( 'Scroll Up', 'pojo' ),
			'desc' => '',
			'fields' => $fields,
		);

		return $sections;
	}

	public function add_scroll_up_in_advanced_options( $fields, $cpt ) {
		$fields[] = array(
			'id' => 'scroll_up_visibility',
			'title' => __( 'Scroll Up', 'pojo' ),
			'type' => Pojo_MetaBox::FIELD_SELECT,
			'options' => array(
				'' => __( 'Default', 'pojo' ),
				'visible-desktop' => __( 'Visible Desktop', 'pojo' ),
				'visible-tablet' => __( 'Visible Tablet', 'pojo' ),
				'visible-phone' => __( 'Visible Phone', 'pojo' ),
				'hidden-desktop' => __( 'Hidden Desktop', 'pojo' ),
				'hidden-tablet' => __( 'Hidden Tablet', 'pojo' ),
				'hidden-phone' => __( 'Hidden Phone', 'pojo' ),
				'disable' => __( 'Disable', 'pojo' ),
			),
			'std' => '',
		);

		return $fields;
	}

	public function print_html() {
		$scroll_up_visibility = atmb_get_field( 'po_scroll_up_visibility' );
		
		if ( empty( $scroll_up_visibility ) ) {
			$scroll_up_visibility = get_theme_mod( 'pojo_scroll_up_visibility' );
		}
		
		if ( 'disable' === $scroll_up_visibility )
			return;
		
		$position = get_theme_mod( 'scroll_up_position' );
		if ( empty( $position ) || ! in_array( $position, array( 'right', 'center', 'left' ) ) )
			$position = 'right';
		
		$offset = get_theme_mod( 'scroll_up_distance' );
		if ( empty( $offset ) )
			$offset = '50';

		$duration_array = array(
			'slow' => 1250,
			'normal' => 750,
			'fast' => 250,
		);

		$duration = get_theme_mod( 'scroll_up_duration' );
		if ( empty( $duration ) || ! isset( $duration_array[ $duration ] ) )
			$duration = 'normal';
		
		$icons = $this->_get_icons();
		$icon = get_theme_mod( 'scroll_up_icon' );
		if ( empty( $icon ) || ! isset( $icons[ $icon ] ) )
			$icon = '01';
		
		
		$inline_styles = array();
		$icon_size = get_theme_mod( 'scroll_up_icon_size' );
		if ( empty( $icon_size ) )
			$icon_size = '36px';
		
		$inline_styles[] = 'font-size: ' . $icon_size;
		
		$border_radius = get_theme_mod( 'scroll_up_border_radius' );
		if ( empty( $border_radius ) )
			$border_radius = '6px';
		
		$inline_styles[] = 'border-radius: ' . $border_radius;

		$wrapper_classes = array(
			'pojo-scroll-up-' . $position,
		);
		
		if ( ! empty( $scroll_up_visibility ) )
			$wrapper_classes[] = 'pojo-' . $scroll_up_visibility;
		
		?>
		<div id="pojo-scroll-up" class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-offset="<?php echo esc_attr( $offset ); ?>" data-duration="<?php echo $duration_array[ $duration ]; ?>" style="<?php echo esc_attr( implode( ';', $inline_styles ) ); ?>">
			<div class="pojo-scroll-up-inner">
				<a class="pojo-scroll-up-button" href="javascript:void(0);" title="<?php _e( 'Scroll to top', 'pojo' ); ?>">
					<span class="fa fa-<?php echo $icons[ $icon ]; ?>"></span><span class="sr-only"><?php _e( 'Scroll to top', 'pojo' ); ?></span>
				</a>
			</div>
		</div>
		<?php
	}

	public function __construct() {
		add_filter( 'pojo_register_customize_sections', array( &$this, 'customize_scroll_up' ), 600 );
		add_filter( 'po_init_fields', array( &$this, 'add_scroll_up_in_advanced_options' ), 20, 2 );
		
		add_action( 'wp_footer', array( &$this, 'print_html' ) );
	}
	
}
new Pojo_Scroll_Up();