<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Two_color extends Pojo_Customize_Control_Field_Base {
	
	protected $_titles = array();
	protected $_default = array();
	
	public function __construct( $manager, $id, $args = array() ) {
		$args['type'] = 'two_color';

		$defaults = array(
			'color_1' => '#ffffff',
			'color_2' => '#888888',
		);

		if ( ! empty( $args['change_type'] ) && 'two_color_selection' === $args['change_type'] ) {
			$titles_defaults = array(
				'color_1' => __( 'Text Selection', 'pojo' ),
				'color_2' => __( 'Text Background Selection', 'pojo' ),
			);
		} else {
			$titles_defaults = array(
				'color_1' => __( 'Link', 'pojo' ),
				'color_2' => __( 'Hover', 'pojo' ),
			);
		}
		
		$this->_default = wp_parse_args( $args['default'], $defaults );
		$this->_titles = $titles_defaults;

		$args['settings'] = array();
		foreach ( $defaults as $key => $value ) {
			$child_id = $id . '[' . $key . ']';
			$transport = 'refresh';
			if ( ! empty( $args['change_type'] ) && 'two_color_link' === $args['change_type'] && 'color_1' === $key ) {
				$transport = 'postMessage';
			}
			
			$manager->add_setting( $child_id, array(
				'default'   => $value,
				'transport' => $transport,
			) );

			$args['settings'][ $child_id ] = $child_id;
		}
		unset( $args['default'] );
		parent::__construct( $manager, $id, $args );
	}

	public function render_content() {
		$this_default = $this->_default['color_1'];
		$default_attr_color_1 = '';
		if ( $this_default ) {
			if ( false === strpos( $this_default, '#' ) )
				$this_default = '#' . $this_default;
			$default_attr_color_1 = ' data-default-color="' . esc_attr( $this_default ) . '"';
		}
		
		$this_default = $this->_default['color_2'];
		$default_attr_color_2 = '';
		if ( $this_default ) {
			if ( false === strpos( $this_default, '#' ) )
				$this_default = '#' . $this_default;
			$default_attr_color_2 = ' data-default-color="' . esc_attr( $this_default ) . '"';
		}
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<div class="customize-image-picker">
		<div class="customize-control customize-control-image open">
			<div class="library">
				<ul>
					<li tabindex="0" data-customize-tab="test1"><?php echo esc_html( $this->_titles['color_1'] ); ?></li>
					<li tabindex="0" data-customize-tab="test2"><?php echo esc_html( $this->_titles['color_2'] ); ?></li>
				</ul>
				<div class="library-content" data-customize-tab="test1">
					<span class="customize-control-sub-title"><?php echo esc_html( $this->_titles['color_1'] ); ?></span>
					<input <?php $this->link( $this->id . '[color_1]' ); ?> class="typography-field-color" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'pojo' ); ?>"<?php echo $default_attr_color_1; ?> />
				</div>
				<div class="library-content" data-customize-tab="test2">
					<span class="customize-control-sub-title"><?php echo esc_html( $this->_titles['color_2'] ); ?></span>
					<input <?php $this->link( $this->id . '[color_2]' ); ?> class="typography-field-color" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'pojo' ); ?>"<?php echo $default_attr_color_2; ?> />
				</div>
			</div>
		</div>
		</div>
	<?php
	}

}
