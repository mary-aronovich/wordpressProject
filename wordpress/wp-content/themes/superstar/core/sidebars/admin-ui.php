<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Theme_Sidebars_Admin_UI {

	public function register_sidebar_metabox( $meta_boxes = array() ) {
		global $wp_registered_sidebars;

		if ( empty( $wp_registered_sidebars ) )
			return $meta_boxes;

		$core_sidebars = Pojo_Core::instance()->sidebars->get_core_sidebars();
		if ( empty( $core_sidebars ) )
			return $core_sidebars;

		$post_types_objects = get_post_types( array( 'public' => true ), 'objects' );
		$public_post_types = array();
		foreach ( $post_types_objects as $cpt_slug => $post_type ) {
			$public_post_types[] = $cpt_slug;
		}

		$fields = array();

		$sidebar_options = array(
			'' => __( 'Default', 'pojo' ),
			'_hide' => __( 'Hide Sidebar', 'pojo' ), // Underscore prefix for any conflicts
		);
		foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar_args ) {
			$sidebar_options[ $sidebar_id ] = $sidebar_args['name'];
		}

		foreach ( $core_sidebars as $sidebar_id => $sidebar_name ) {
			$fields[] = array(
				'id' => 'override_sidebar_' . $sidebar_id,
				'title' => $sidebar_name,
				'type' => Pojo_MetaBox::FIELD_SELECT,
				'options' => $sidebar_options,
			);
		}

		$meta_boxes[] = array(
			'id' => 'pojo-override-sidebars',
			'title' => __( 'Sidebars', 'pojo' ),
			'post_types' => $public_post_types,
			'context' => 'side',
			'prefix' => 'pojo_',
			'fields' => $fields,
		);

		return $meta_boxes;
	}

	public function __construct() {
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_sidebar_metabox' ) );
	}
	
}