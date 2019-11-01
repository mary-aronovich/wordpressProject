<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Widget_Tabs extends Pojo_Widget_Base {
	/**
	 * @var int
	 */
	static protected $index = 1;

	const TYPE_TABS      = 'tabs';
	const TYPE_TOGGLE    = 'toggle';
	const TYPE_ACCORDION = 'accordion';
	
	protected function _get_tab_item_id( $index ) {
		return sprintf( 'pojo-tab-item-%d-%d', self::$index, $index );
	}

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
			'title' => __( 'Title', 'pojo' ),
			'std' => '',
			'filter' => array( &$this, '_filter_wysiwyg' ),
		);

		$repeater_fields[] = array(
			'id' => 'content',
			'title' => __( 'Content:', 'pojo' ),
			'type' => 'textarea',
			'std' => '',
			'filter' => array( &$this, '_filter_wysiwyg' ),
		);

		$repeater_fields[] = array(
			'id' => 'html_mode',
			'title' => __( 'Don\'t add automatic paragraphs (for HTML code)', 'pojo' ),
			'type' => 'checkbox',
			'std' => '',
		);
		
		$this->_form_fields[] = array(
			'id' => 'tabs',
			'title' => __( 'Tabs', 'pojo' ),
			'type' => 'repeater',
			'fields' => $repeater_fields,
			'std' => array(),
		);

		$this->_form_fields[] = array(
			'id' => 'type',
			'title' => __( 'Type:', 'pojo' ),
			'type' => 'select',
			'options' => array(
				self::TYPE_TABS => __( 'Tabs', 'pojo' ),
				self::TYPE_TOGGLE => __( 'Toggle', 'pojo' ),
				self::TYPE_ACCORDION => __( 'Accordion', 'pojo' ),
			),
			'std' => 'tabs',
			'filter' => array( &$this, '_valid_by_options' ),
		);

		parent::__construct(
			'pojo_tabs',
			__( 'Tabs', 'pojo' ),
			array( 'description' => __( 'Add tabs, an accordion or toggles to your site', 'pojo' ), )
		);
	}

	public function widget( $args, $instance ) {
		if ( empty( $instance['tabs'] ) )
			return;
		
		$instance = wp_parse_args( $instance, $this->_get_default_values() );
		$args = $this->_parse_widget_args( $args, $instance );
		
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		if ( self::TYPE_TABS === $instance['type'] ) : ?>
			<ul class="nav nav-tabs pojo-tabs">
				<?php foreach ( $instance['tabs'] as $t_key => $tab ) : ?>
				<li<?php if ( 0 === $t_key ) echo ' class="active"'; ?>><a href="#<?php echo $this->_get_tab_item_id( $t_key ); ?>" data-toggle="tab"><?php echo $tab['title']; ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content pojo-tab-content">
				<?php foreach ( $instance['tabs'] as $t_key => $tab ) :
					if ( ! isset( $tab['html_mode'] ) || '1' !== $tab['html_mode'] )
						$tab['content'] = nl2br( $tab['content'] );
					?>
					<div class="tab-pane fade<?php if ( 0 === $t_key ) echo ' in active'; ?>" id="<?php echo $this->_get_tab_item_id( $t_key ); ?>"><?php echo do_shortcode( $tab['content'] ); ?></div>
				<?php endforeach; ?>
			</div>
		<?php elseif ( self::TYPE_ACCORDION === $instance['type'] ) : ?>
			<div class="panel-group pojo-accordion" id="pojo-accordion-<?php echo self::$index; ?>">
				<?php foreach ( $instance['tabs'] as $t_key => $tab ) :
					if ( ! isset( $tab['html_mode'] ) || '1' !== $tab['html_mode'] )
						$tab['content'] = nl2br( $tab['content'] );
					?>
				<div class="panel">
					<div class="panel-heading">
						<a class="panel-title<?php if ( 0 !== $t_key ) echo ' collapsed'; ?>" data-toggle="collapse" data-parent="#pojo-accordion-<?php echo self::$index; ?>" href="#<?php echo $this->_get_tab_item_id( $t_key ); ?>">
							<?php echo $tab['title']; ?>
						</a>
					</div>
					<div id="<?php echo $this->_get_tab_item_id( $t_key ); ?>" class="panel-collapse collapse<?php if ( 0 === $t_key ) echo ' in'; ?>">
						<div class="panel-body">
							<?php echo do_shortcode( $tab['content'] ); ?>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		<?php elseif ( self::TYPE_TOGGLE === $instance['type'] ) : ?>
			<?php foreach ( $instance['tabs'] as $t_key => $tab ) :
				if ( ! isset( $tab['html_mode'] ) || '1' !== $tab['html_mode'] )
					$tab['content'] = nl2br( $tab['content'] );
				?>
			<div class="pojo-toggle panel">
				<div class="panel-heading">
					<a class="panel-title collapsed" data-toggle="collapse" href="#<?php echo $this->_get_tab_item_id( $t_key ); ?>">
						<?php echo $tab['title']; ?>
					</a>
				</div>
				<div id="<?php echo $this->_get_tab_item_id( $t_key ); ?>" class="collapse">
					<div class="panel-body">
						<?php echo do_shortcode( $tab['content'] ); ?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		<?php endif;
		
		echo $args['after_widget'];
		
		self::$index++;
	}

}