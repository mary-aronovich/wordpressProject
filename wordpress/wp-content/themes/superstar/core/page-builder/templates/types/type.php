<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Pojo_Builder_Template_Type_Base {
	
	protected $type;
	
	protected $label;
	
	protected $controller = 'BtTemplates';
	
	abstract public function get_items();

	public function get_type() {
		return $this->type;
	}

	public function get_label() {
		return $this->label;
	}

	public function get_controller() {
		return $this->controller;
	}

	protected function _print_template( $id, $template ) {
		?>
		<script type="text/html" id="tmpl-bt-template-<?php echo esc_attr( $id ); ?>">
			<?php echo $template; ?>
		</script>
		<?php
	}

	public function get_admin_templates() {
		return array();
	}

	public function print_ajax_template( $template_id ) {}

	public function print_media_templates() {
		$templates = $this->get_admin_templates();
		
		if ( empty( $templates ) )
			return;
		
		foreach ( $templates as $key => $the_template ) {
			$id = sprintf( '%s-%s', $this->type, $key );
			$this->_print_template( $id, $the_template );
		}
	}

	public function __construct() {
		add_action( 'print_media_templates', array( &$this, 'print_media_templates' ) );
		
	}
	
}