<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Page_Builder_Updates {

	public function from_1_0_to_1_1( $post_id ) {
		$widget_rows = get_post_meta( $post_id, 'pb_builder_rows', true );
		if ( empty( $widget_rows ) || ! is_array( $widget_rows ) )
			return;
		
		$new_widget_rows = array();
		$column_index = 1;
		$new_base_id = current_time( 'timestamp' );
		
		// Hotfix for local templates
		if ( '_pb_templates' === get_post_type( $post_id ) )
			$new_base_id = 'COLUMN_ID-';
		
		foreach ( $widget_rows as $widget_row ) {
			$new_widgets = array();
			$column_id = $new_base_id . $column_index++;
			
			if ( ! isset( $widget_row['widgets'] ) )
				return;
			
			foreach ( $widget_row['widgets'] as $widget ) {
				$widget['parent'] = $column_id;

				$new_widgets[] = $widget;
			}

			$column = array(
				'id' => $column_id,
				'size' => 12,
				'widgets' => $new_widgets,
			);
			
			$new_widget_rows[] = array(
				'id' => $widget_row['id'],
				'row_fields' => $widget_row['row_fields'],
				'columns' => array( $column ),
			);
		}
		
		Pojo_Core::instance()->builder->save_builder_rows( $post_id, $new_widget_rows );
	}

	public function __construct() {}
	
}