<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Opening_Hours extends Pojo_Widget_Base {

	protected $_week_days = array();
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$this->_week_days = array(
			__( 'Sunday', 'pojo' ),
			__( 'Monday', 'pojo' ),
			__( 'Tuesday', 'pojo' ),
			__( 'Wednesday', 'pojo' ),
			__( 'Thursday', 'pojo' ),
			__( 'Friday', 'pojo' ),
			__( 'Saturday', 'pojo' ),
		);

		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
			//'filter' => 'sanitize_text_field',
		);

		$this->_form_fields[] = array(
			'id' => 'lbl_week_start',
			'title' => sprintf( __( 'Week starts by <a href="%s" target="_blank">General Settings</a>.', 'pojo' ), admin_url( '/options-general.php#start_of_week' ) ),
			'type' => 'label',
		);
		
		foreach ( $this->_week_days as $day_num => $day_title ) {
			$this->_form_fields[] = array(
				'id' => 'day_' . $day_num,
				'title' => __( $day_title, 'pojo' ),
				'std' => '',
				'filter' => 'sanitize_text_field',
			);
		}
		
		parent::__construct(
			'pojo_opening_hours',
			__( 'Hours', 'pojo' ),
			array( 'description' => __( 'Show your business\'s opening hours', 'pojo' ), )
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
		$instance['title'] = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		$myweek = array();
		$week_begins = intval( get_option( 'start_of_week' ) );
		for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
			$myweek[] = ( $wdcount + $week_begins ) % 7;
		}
		?>
		<div class="opening-hours-wrap">
			<?php foreach ( $myweek as $day_num ) :
			$day_field = 'day_' . $day_num; ?>
			<div class="day-row">
				<span class="day-title"><?php echo $this->_week_days[ $day_num ]; ?></span>
				<?php if ( ! empty( $instance[ $day_field ] ) ) : ?>
				<span class="day-time"><?php echo $instance[ $day_field ]; ?></span>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php
		echo $args['after_widget'];
	}
	
}