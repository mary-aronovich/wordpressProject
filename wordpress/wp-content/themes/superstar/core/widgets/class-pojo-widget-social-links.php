<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Social_Links extends Pojo_Widget_Base {

	private static $_social_available = array(
		'Facebook',
		'Twitter',
		'Google+',
		'YouTube',
		'LinkedIn',
		'Pinterest',
		'GitHub',
		'Instagram',
		'Vimeo',
		'Flickr',
		'Foursquare',
		'Dribbble',
		'Tumblr',
		'Contact',
		'RSS',
	);

	public static function get_social_available() {
		return self::$_social_available;
	}
	
	public static function get_social_slug( $name ) {
		return strtolower( str_replace( array( '+', ' ' ), array( 'Plus', '-' ), $name ) );
	}

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
		);

		$this->_form_fields[] = array(
			'id' => 'tooltip',
			'title' => __( 'Tooltip:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				'top' => __( 'Top', 'pojo' ),
				'right' => __( 'Right', 'pojo' ),
				'left' => __( 'Left', 'pojo' ),
				'bottom' => __( 'Bottom', 'pojo' ),
				'none' => __( 'None', 'pojo' ),
			),
			'std' => 'top',
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'heading_links',
			'type' => 'heading',
			'title' => __( 'Links:', 'pojo' ),
		);
		
		foreach ( self::get_social_available() as $social ) {
			$this->_form_fields[] = array(
				'id' => 'social_' . self::get_social_slug( $social ),
				'title' => $social,
				'std' => '',
				'placeholder' => 'http://',
			);
		}
		
		parent::__construct(
			'pojo_social_links',
			__( 'Social Links', 'pojo' ),
			array( 'description' => __( 'Add links to all of your social media and sharing site profiles', 'pojo' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		$items_array = array();
		
		if ( ! self::get_social_available() )
			return;

		$social_icons = array(
			'Facebook'   => '<span class="social-icon"></span>',
			'Twitter'    => '<span class="social-icon"></span>',
			'Google+'    => '<span class="social-icon"></span>',
			'YouTube'    => '<span class="social-icon"></span>',
			'LinkedIn'   => '<span class="social-icon"></span>',
			'Pinterest'  => '<span class="social-icon"></span>',
			'GitHub'     => '<span class="social-icon"></span>',
			'Instagram'  => '<span class="social-icon"></span>',
			'Vimeo'      => '<span class="social-icon"></span>',
			'Flickr'     => '<span class="social-icon"></span>',
			'Foursquare' => '<span class="social-icon"></span>',
			'Dribbble'   => '<span class="social-icon"></span>',
			'Tumblr'     => '<span class="social-icon"></span>',
			'Contact'    => '<span class="social-icon"></span>',
			'RSS'        => '<span class="social-icon"></span>',
		);

		foreach ( self::get_social_available() as $social ) {
			$icon = $social_icons[ $social ];
			$field_key = 'social_' . self::get_social_slug( $social );
			if ( ! empty( $instance[ $field_key ] ) ) {
				$tooltip_attrs = '';
				if ( empty( $instance['tooltip'] ) )
					$instance['tooltip'] = 'top';
				
				if ( 'none' !== $instance['tooltip'] ) {
					$tooltip_attrs = sprintf( ' class="pojo-tooltip" data-placement="%s"', esc_attr( $instance['tooltip'] ) );
				}
				$items_array[] = '<li class="social-' . self::get_social_slug( $social ) . '"><a href="' . esc_attr( $instance[ $field_key ] ) . '"'. $tooltip_attrs . ' title="' . esc_attr__( $social, 'pojo' ) . '" target="_blank">' . $icon . '<span class="sr-only">' . $social . '</span></a></li>';
			}
		}

		if ( empty( $items_array ) )
			return;
		
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		echo '<ul class="social-links">';
		echo implode( '', $items_array );
		echo '</ul>';

		echo $args['after_widget'];
	}
}