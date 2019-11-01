<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Builder_Templates {

	/**
	 * @var Pojo_Builder_Template_Type_Base[]
	 */
	private $_types = null;

	public function js_print_templates_array() {
		/**
		 * @var $type Pojo_Builder_Template_Type_Base
		 */
		$types = array();
		foreach ( $this->_types as $type ) {
			$types[ $type->get_type() ] = array(
				'type' => $type->get_type(),
				'id' => 'bt-' . $type->get_type(),
				'title' => $type->get_label(),
				'data' => array(
					'controller' => $type->get_controller(),
					'items' => $type->get_items(),
				),
			);
		}
		
		$array = array(
			'types' => $types,
			'l10n' => array(
				'save_template' => __( 'Save Template', 'pojo' ),
				'insert_template' => __( 'Insert Template', 'pojo' ),
			),
		);
		?>
		<script>var PojoBtTemplates = <?php echo json_encode( $array ); ?>;</script>
		<?php
	}

	public function print_template_button() {
		?>
		<div class="templates-library-actions">
			<a href="#" class="open-template-media button" data-target="bt-save"><?php _e( 'Save Template', 'pojo' ); ?></a>
			<a href="#" class="open-template-media button button-primary"><?php _e( 'Template Library', 'pojo' ); ?></a>
		</div>
		<?php
	}

	public function ajax_pb_import_template() {
		if ( ! empty( $_POST['template_id'] ) && ! empty( $_POST['template_type'] ) ) {
			$type = $_POST['template_type'];
			if ( isset( $this->_types[ $type ] ) ) {
				$this->_types[ $type ]->print_ajax_template( $_POST['template_id'] );
			}
		}
		
		die;
	}

	public function ajax_bt_fetch_template_items() {
		$data = array();
		
		if ( ! empty( $_REQUEST['template_type'] ) ) {
			$type = $_REQUEST['template_type'];
			if ( isset( $this->_types[ $type ] ) ) {
				$data = $this->_types[ $type ]->get_items();
			}
		}
		
		wp_send_json( $data );
	}

	public function init() {
		$this->_types = array();
		$builtin_types = array(
			'local',
			//'pojo',
		);

		include( 'types/type.php' );

		foreach ( $builtin_types as $type ) {
			/** @var $instance Pojo_Builder_Template_Type_Base */

			include( 'types/type-' . $type . '.php' );
			$class_name = 'Pojo_Builder_Template_Type_' . ucfirst( $type );
			$instance = new $class_name();

			$this->_types[ $instance->get_type() ] = $instance;
		}
	}

	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		
		add_action( 'admin_footer', array( &$this, 'js_print_templates_array' ) );
		add_action( 'wp_ajax_pb_import_template', array( &$this, 'ajax_pb_import_template' ) );
		add_action( 'wp_ajax_bt_fetch_template_items', array( &$this, 'ajax_bt_fetch_template_items' ) );
	}
	
}