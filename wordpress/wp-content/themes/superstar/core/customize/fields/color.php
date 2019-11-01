<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Customize_Control_Field_Color extends Pojo_Customize_Control_Field_Base {

	//public $statuses;
	
	/**
	 * @var string
	 */
	public $type = 'color';

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	public function enqueue() {
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	public function render_content() {}

	public function to_json() {
		parent::to_json();
		$this->json['defaultValue'] = $this->setting->default;
	}

	/**
	 * Render a JS template for the content of the color picker control.
	 */
	public function content_template() {
		?>
		<# var defaultValue = '';
			if ( data.defaultValue ) {
				if ( '#' !== data.defaultValue.substring( 0, 1 ) ) {
				defaultValue = '#' + data.defaultValue;
				} else {
				defaultValue = data.defaultValue;
				}
				defaultValue = ' data-default-color=' + defaultValue; // Quotes added automatically.
			} #>
		<label>
			<# if ( data.label ) { #>
				<span class="customize-control-title">{{{ data.label }}}</span>
			<# } #>
			<# if ( data.description ) { #>
				<span class="description customize-control-description">{{{ data.description }}}</span>
			<# } #>
				<div class="customize-control-content">
				<input class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'pojo' ); ?>" {{ defaultValue }} />
				</div>
		</label>
	<?php
	}

}
