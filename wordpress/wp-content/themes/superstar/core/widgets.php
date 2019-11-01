<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $pojo_widgets_available;

$pojo_widgets_available = array(
	'class-pojo-widget-sub-page-menu' => 'Pojo_Widget_Sub_Page_Menu',
	'class-pojo-widget-opening-hours' => 'Pojo_Widget_Opening_Hours',
	'class-pojo-widget-button' => 'Pojo_Widget_Button',
	'class-pojo-widget-social-links' => 'Pojo_Widget_Social_Links',
	'class-pojo-widget-embed-video' => 'Pojo_Widget_Embed_Video',
	'class-pojo-widget-google-maps' => 'Pojo_Widget_Google_Maps',
	'class-pojo-widget-image' => 'Pojo_Widget_Image',
	'class-pojo-widget-image-text' => 'Pojo_Widget_Image_Text',
	'class-pojo-widget-recent-posts' => 'Pojo_Widget_Recent_Posts',
	'class-pojo-widget-tabs' => 'Pojo_Widget_Tabs',
	'class-pojo-widget-testimonials' => 'Pojo_Widget_Testimonials',
	'class-pojo-widget-catalog' => 'Pojo_Widget_Catalog',
	'class-pojo-widget-animated-numbers' => 'Pojo_Widget_Animated_Numbers',
);
include( 'widgets/abstract-class-pojo-widget-base.php' );

foreach ( $pojo_widgets_available as $w_key => $widget )
	include( 'widgets/' . $w_key . '.php' );

function pojo_widgets_init() {
	global $pojo_widgets_available;

	foreach ( $pojo_widgets_available as $widget )
		register_widget( $widget );
	
	if ( current_theme_supports( 'pojo-posts-group' ) ) {
		include( 'widgets/class-pojo-widget-posts-group.php' );
		register_widget( 'Pojo_Widget_Posts_Group' );
	}
	
	do_action( 'pojo_widgets_registered' );
}
add_action( 'widgets_init', 'pojo_widgets_init' );

function pojo_admin_widget_templates() {
	// TODO: Move to other file.
	?>
	<script type="text/template" id="tmpl-pojo-admin-modal">
		<div class="pojo-admin-modal">
			<div class="wrapper">
				<div class="wrapper-inner">
					<div class="modal-header">
						<h3 class="modal-title">{header}</h3>
						<a class="media-modal-close pojo-modal-close" href="#" title="Close"><span class="media-modal-icon"></span></a>
					</div>

					<div class="modal-content">
						{content}
					</div>

					<div class="modal-footer">
						<a href="#" class="button button-primary pojo-modal-close button-large"><?php _e( 'Update', 'pojo' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="pojo-admin-modal-backdrop"></div>
	</script>
<?php
}
add_action( 'admin_footer', 'pojo_admin_widget_templates' );
add_action( 'customize_controls_print_footer_scripts', 'pojo_admin_widget_templates' );