<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_WP_Pointers {

	public function enqueue_scripts( $hook_suffix ) {
		/*
		 * Register feature pointers
		 * Format: array( hook_suffix => pointer_id )
		 */
		$registered_pointers = array(
			'post-new.php' => 'pj_feature_builder_template',
			'post.php'     => 'pj_feature_builder_template',
		);
		
		// Check if screen related pointer is registered
		if ( empty( $registered_pointers[ $hook_suffix ] ) )
			return;

		$pointers = (array) $registered_pointers[ $hook_suffix ];

		$caps_required = array(
			// pointer_id => array( user_caps.. ),
		);
		
		// Get dismissed pointers
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		$got_pointers = false;
		foreach ( array_diff( $pointers, $dismissed ) as $pointer ) {
			if ( isset( $caps_required[ $pointer ] ) ) {
				foreach ( $caps_required[ $pointer ] as $cap ) {
					if ( ! current_user_can( $cap ) )
						continue 2;
				}
			}

			// Bind pointer print function
			add_action( 'admin_print_footer_scripts', array( __CLASS__, 'pointer_' . $pointer ) );
			$got_pointers = true;
		}

		if ( ! $got_pointers )
			return;

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}

	/**
	 * Print the pointer JavaScript data.
	 *
	 *
	 * @param string $pointer_id The pointer ID.
	 * @param string $selector The HTML elements, on which the pointer should be attached.
	 * @param array  $args Arguments to be passed to the pointer JS (see wp-pointer.js).
	 */
	private static function print_js( $pointer_id, $selector, $args ) {
		if ( empty( $pointer_id ) || empty( $selector ) || empty( $args ) || empty( $args['content'] ) )
			return;

		?>
		<script type="text/javascript">
			(function($){
				var options = <?php echo wp_json_encode( $args ); ?>, setup;

				if ( ! options )
					return;

				options = $.extend( options, {
					close: function() {
						$.post( ajaxurl, {
							pointer: '<?php echo $pointer_id; ?>',
							action: 'dismiss-wp-pointer'
						} );
					}
				});

				setup = function() {
					$('<?php echo $selector; ?>').first().pointer( options ).pointer('open');
				};

				if ( options.position && options.position.defer_loading )
					$(window).bind( 'load.wp-pointers', setup );
				else
					$(document).ready( setup );

			})( jQuery );
		</script>
		<?php
	}

	public static function pointer_pj_feature_builder_template() {
		$content  = '<h3>' . __( 'Template Library', 'pojo' ) . '</h3>';
		$content .= '<p>' . __( 'Meet our new Template Library. In this area you can save, edit or export Page Builder templates and import them into each page or site that works with Pojo Framework.', 'pojo' ) . '</p>';
		$content .= '<p>' . __( 'Please note, in every template you can add a title, description and image for a visual preview in the Template Library.', 'pojo' ) . '</p>';
		
		if ( is_rtl() )
			$position = array( 'edge' => 'bottom', 'align' => 'right' );
		else
			$position = array( 'edge' => 'bottom', 'align' => 'left' );

		self::print_js( 'pj_feature_builder_template', 'div.templates-library-actions a.open-template-media.button-primary', array(
			'content' => $content,
			'position' => $position,
		) );
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
	}
	
}