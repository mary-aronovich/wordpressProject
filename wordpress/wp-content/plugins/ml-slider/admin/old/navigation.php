<?php if (!defined('ABSPATH')) die('No direct access.');
/**
 * NB: This code was taken from the main file and is only loaded if the user sets a filter.
 */
if ($tabs = metaslider_old_navigation_all_meta_sliders($current_slideshow_id)) {
	if ('tabs' == metaslider_old_navigation_get_view()) {

		// Render the tabs
		?>
		<nav class="nav-tab-wrapper wp-clearfix relative" aria-label="Secondary menu">
				<?php foreach ($tabs as $tab) {
					if (!isset($tab['title'])) continue;
					$active = isset($tab['active']) && filter_var($tab['active'], FILTER_VALIDATE_BOOLEAN);
					$title = esc_attr($tab['title']);
					$title_with_html_allowed = esc_html($tab['title']);
					if ($active) {
						echo "<div class='font-normal nav-tab nav-tab-active bg-gray-lightest' style='border-bottom-color:#f8fafc'><span>{$title}</span></div>";
					} else {
						echo "<a href='?page=metaslider&amp;id={$tab['id']}' title= '{$title}' class='font-normal nav-tab'>{$title_with_html_allowed}</a>";
					}
				}
			if ($button = metaslider_old_navigation_toggle_layout_button()) echo $button; ?>
		</nav>

	<?php // This will render the select dropdown view
	// TODO make this resemble the WP Nav menu UI perhaps
	} else {
		echo "<div class='manage-menus relative'>";
			echo "<label for='select-slideshow' class='selected-menu'>" . __("Select Slideshow", "ml-slider") . ": </label>";
			echo "<select name='select-slideshow' onchange='if (this.value) window.location.href=this.value'>";

			$tabs = metaslider_old_navigation_all_meta_sliders($current_slideshow_id, 'title');
			foreach ($tabs as $tab) {
				if (!isset($tab['title'])) continue;
				$active = isset($tab['active']) && filter_var($tab['active'], FILTER_VALIDATE_BOOLEAN);
				$selected = $active ? " selected" : "";
				$title = esc_attr($tab['title']);
				echo "<option value='?page=metaslider&amp;id={$tab['id']}'{$selected}>{$title}</option>";
			}
			echo "</select>";
			
			// TODO: Update this button and the entire nav system to a vuejs component
			if ($button = metaslider_old_navigation_toggle_layout_button()) echo $button;
		echo "</div>";
	}
}

/**
 * Return the users saved view preference.
 */
function metaslider_old_navigation_get_view() {
	global $user_ID;

	if (get_user_meta($user_ID, "metaslider_view", true)) {
		return get_user_meta($user_ID, "metaslider_view", true);
	}

	return 'tabs';
}

/**
 * Toggle Layout Buttons.
 *
 * @return string returns html button
 */
function metaslider_old_navigation_toggle_layout_button() {
	
	$view = ('tabs' == metaslider_old_navigation_get_view()) ? 'tabs' : 'dropdown';
	$view_opposite = ('dropdown' == metaslider_old_navigation_get_view()) ? 'tabs' : 'dropdown';
	$instructions = (metaslider_old_navigation_get_view() == 'tabs') ? __("Switch to dropdown view", "ml-slider") : __("Switch to tabs view", "ml-slider");
	$url = admin_url("admin-post.php?action=metaslider_switch_view&view=" . $view_opposite);
	return "<div class='absolute flex h-full items-center justify-center mr-4 right-0 top-0 z-1'><a class='tipsy-tooltip' title='{$instructions}' href='{$url}'><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"17\" height=\"17\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"feather feather-shuffle\"><polyline points=\"16 3 21 3 21 8\"/><line x1=\"4\" y1=\"20\" x2=\"21\" y2=\"3\"/><polyline points=\"21 16 21 21 16 21\"/><line x1=\"15\" y1=\"15\" x2=\"21\" y2=\"21\"/><line x1=\"4\" y1=\"4\" x2=\"9\" y2=\"9\"/></svg></a></div>";
}

/**
 * Get sliders. Returns a nicely formatted array of currently
 * published sliders.
 *
 * @param string $current_slideshow_id - ID of the slideshow
 * @param string $key 				   - Key to sort by
 * 
 * @return array All published slideshows
 */
function metaslider_old_navigation_all_meta_sliders($current_slideshow_id, $key = 'date') {

	$sliders = array();

	// list the tabs
	$args = array(
		'post_type' => 'ml-slider',
		'post_status' => 'publish',
		'orderby' => $key,
		'suppress_filters' => 1, // wpml, ignore language filter
		'order' => 'ASC',
		'posts_per_page' => -1
	);

	$args = apply_filters( 'metaslider_all_meta_sliders_args', $args );

	// WP_Query causes issues with other plugins using admin_footer to insert scripts
	// use get_posts instead
	$all_sliders = get_posts( $args );

	foreach( $all_sliders as $slideshow ) {

		$active = ( $current_slideshow_id == $slideshow->ID ) ? true : false;

		$sliders[] = array(
			'active' => $active,
			'title' => $slideshow->post_title,
			'id' => $slideshow->ID
		);

	}

	return $sliders;

}
