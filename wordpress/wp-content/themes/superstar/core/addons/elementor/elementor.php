<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Elementor_Integration {

	public function editor_head() {
		?><style>.pojo-widget-button-collapse.widget-button-collapse { display: none; }</style><?php
	}

	/**
	 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager
	 */
	public function register_locations( $manager ) {
		$manager->register_core_location( 'header' );
		$manager->register_core_location( 'footer' );
	}

	public function register_dynamic_tags( $manager ) {
		$site_logo_tag = $manager->get_tag_info( 'site-logo' );

		if ( empty( $site_logo_tag ) ) {
			return;
		}

		require 'dynamic-tags/site-logo.php';

		$manager->unregister_tag( 'site-logo' );
		$manager->register_tag( 'Pojo_Dynamic_Tag_Site_Logo' );
	}

	public function register_pojo_categories( $elements_manager ) {
		$elements_manager->add_category( 'pojo', array(
			'title' => __( 'Pojo Themes', 'pojo' ),
			'icon' => 'eicon-pojome',
		) );
	}

	public function __construct() {
		require 'utils.php';

		add_action( 'elementor/editor/wp_head', array( $this, 'editor_head' ) );
		add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );
		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_dynamic_tags' ), 30 );

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_pojo_categories' ) );
	}
}

new Pojo_Elementor_Integration();
