<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Wysiwyg extends Pojo_Widget_Base {
	
	public function __construct() {
		$this->_form_fields = array();
		
		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo' ),
			'std' => '',
			//'filter' => 'sanitize_text_field',
		);
		
		$this->_form_fields[] = array(
			'id' => 'text',
			'title' => __( 'Editor:', 'pojo' ),
			'type' => 'wysiwyg',
			'std' => '',
			'filter' => array( &$this, '_filter_wysiwyg' ),
		);
		
		parent::__construct(
			'pojo_wysiwyg',
			__( 'Text Editor', 'pojo' ),
			array( 'description' => __( 'Text Editor', 'pojo' ), )
		);
	}
	
	public function widget( $args, $instance ) {
		//$instance['text'] = apply_filters( 'do_shortcode', $instance['text'] );
		$instance['text'] = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		//$instance['text'] = wpautop( $instance['text'] );
		$args = $this->_parse_widget_args( $args, $instance );
		
		echo $args['before_widget'];
		
		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		?>
		<div class="textwidget"><?php echo $instance['text']; ?></div>
		<?php
		
		echo $args['after_widget'];
	}
	
}