<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Menu_Search {

	public function menu_search( $items, $args ) {
		if ( 'primary' === $args->theme_location && current_theme_supports( 'pojo-menu-search' ) && get_theme_mod( 'chk_enable_menu_search' ) ) :
			ob_start(); ?>
			<li class="pojo-menu-search">
				<form role="search" action="<?php echo home_url( '/' ); ?>" method="get">
					<span class="menu-search-input">
						<input type="search" name="s" value="<?php echo esc_attr( isset( $_GET['s'] ) ? $_GET['s'] : '' ); ?>" autocomplete="off" />
					</span>
					<span class="menu-search-submit">
						<input type="submit" value="<?php _e( 'Search', 'pojo' ); ?>" />
					</span>
				</form>
			</li>
		<?php
			$items .= ob_get_clean();
		endif;
		return $items;
	}

	public function customizer_setting( $fields ) {
		if ( current_theme_supports( 'pojo-menu-search' ) ) {
			$fields[] = array(
				'id'    => 'chk_enable_menu_search',
				'title' => __( 'Add Search Button', 'pojo' ),
				'type'  => Pojo_Theme_Customize::FIELD_CHECKBOX,
				'std'   => true,
			);
		}
		
		return $fields;
	}
	
	public function __construct() {
		add_filter( 'wp_nav_menu_items', array( &$this, 'menu_search' ), 50, 2 );
		add_filter( 'pojo_customizer_section_menus_after', array( &$this, 'customizer_setting' ), 100 );
	}
	
}
new Pojo_Menu_Search();