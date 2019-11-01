<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Menus {
	// Types list
	const TYPE_TEXT = 'text';
	const TYPE_SELECT = 'select';
	
	protected $_fields = null;

	public function get_fields() {
		if ( is_null( $this->_fields ) ) {
			$this->_fields = apply_filters( 'pojo_menus_register_fields', array() );
		}
		return $this->_fields;
	}

	public function wp_edit_nav_menu_walker( $walker ) {
		if ( ! class_exists( 'Pojo_Menu_CF_Walker' ) ) {
			include( POJO_CORE_DIRECTORY . '/helpers/walkers/class-pojo-menu-cf-walker.php' );
		}
		$walker = 'Pojo_Menu_CF_Walker';
		return $walker;
	}

	public function save_fields( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			return;
		
		//check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );
		if ( empty( $_POST['pojo-menu-item'] ) )
			return;
		
		foreach ( $this->get_fields() as $field ) {
			$key = 'pojo-menu-item-' . $field['id'];
			$value = null;
			
			// Sanitize
			if ( ! empty( $_POST['pojo-menu-item'][ $field['id'] ][ $menu_item_db_id ] ) ) {
				$value = $_POST['pojo-menu-item'][ $field['id'] ][ $menu_item_db_id ];
			}
			
			// Update
			if ( ! is_null( $value ) ) {
				update_post_meta( $menu_item_db_id, $key, $value );
			}
			else {
				delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}

	public function custom_fields( $id, $item, $depth, $args ) {
		foreach ( $this->get_fields() as $field ) :
			$input_id = sprintf( 'pojo-menu-item-%s-%d', $field['id'], $item->ID );
			$input_name = sprintf( 'pojo-menu-item[%s][%d]', $field['id'], $item->ID );
			$input_value = get_post_meta( $item->ID, 'pojo-menu-item-' . $field['id'], true );
			
			if ( in_array( $field['type'], array( 'text', 'number' ) ) ) : ?>
				<p class="description description-thin">
					<label for="<?php echo esc_attr( $input_id ); ?>">
						<?php echo $field['title']; ?><br />
						<input id="<?php echo esc_attr( $input_id ); ?>" class="widefat" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" type="<?php echo $field['type']; ?>" />
					</label>
				</p>
			<?php elseif ( self::TYPE_SELECT === $field['type'] ) : ?>
				<p class="description description-thin">
					<label for="<?php echo esc_attr( $input_id ); ?>">
						<?php echo $field['title']; ?><br />
						<select name="<?php echo esc_attr( $input_name ); ?>" id="<?php echo esc_attr( $input_id ); ?>">
							<?php foreach ( $field['options'] as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $input_value, $key ); ?>><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</p>
			<?php endif; ?>
		<?php endforeach;
	}

	public function post_metabox_init_fields( $fields, $cpt ) {
		$locations = get_registered_nav_menus();
		$menus     = wp_get_nav_menus();
		$options   = array(
			'' => __( 'Default', 'pojo' ),
			'hide' => __( 'Hide', 'pojo' ),
		);

		if ( $menus ) {
			foreach ( $menus as $menu ) {
				$options[ $menu->term_id ] = wp_html_excerpt( $menu->name, 40, '&hellip;' );
			}
		}

		$fields[] = array(
			'id'    => 'heading_menus',
			'title' => __( 'Menus', 'pojo' ),
			'type'  => Pojo_MetaBox::FIELD_HEADING,
		);

		foreach ( $locations as $location => $description ) {
			$fields[] = array(
				'id'    => "override_nav_menu_location_{$location}",
				'title' => $description,
				'type'  => Pojo_MetaBox::FIELD_SELECT,
				'options' => $options,
				'std' => '',
			);
		}
		
		return $fields;
	}
	
	public function wp_nav_menu_sticky_menu_fallback( $args ) {
		if ( empty( $args['theme_location'] ) || 'sticky_menu' !== $args['theme_location'] )
			return $args;
		
		if ( ! has_nav_menu( $args['theme_location'] ) )
			$args['theme_location'] = 'primary';
		
		return $args;
	}

	public function wp_nav_menu_args( $args ) {
		if ( empty( $args['theme_location'] ) )
			return $args;

		$args['menu'] = pojo_get_nav_menu_location( $args['theme_location'] );

		return $args;
	}

	public function pre_wp_nav_menu( $return, $args ) {
		if ( ! isset( $args->theme_location ) || empty( $args->theme_location ) )
			return $return;
		
		if ( ! pojo_has_nav_menu( $args->theme_location ) )
			$return = '';
		
		return $return;
	}
	
	public function __construct() {
		include( 'helpers.php' );
		
		// Menus Admin
		add_filter( 'wp_edit_nav_menu_walker', array( &$this, 'wp_edit_nav_menu_walker' ), 99 );
		add_action( 'wp_nav_menu_item_custom_fields', array( &$this, 'custom_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( &$this, 'save_fields' ), 10, 3 );
		
		// Post Setting for replace menus
		add_filter( 'po_init_fields', array( &$this, 'post_metabox_init_fields' ), 15, 2 );
		
		// Override Menu in Post
		add_filter( 'wp_nav_menu_args', array( &$this, 'wp_nav_menu_sticky_menu_fallback' ), 4 );
		add_filter( 'wp_nav_menu_args', array( &$this, 'wp_nav_menu_args' ), 5 );
		
		add_filter( 'pre_wp_nav_menu', array( &$this, 'pre_wp_nav_menu' ), 20, 2 );
	}
	
}
