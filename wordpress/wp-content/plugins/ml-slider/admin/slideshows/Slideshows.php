<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 *  Class to handle slideshows
 */
class MetaSlider_Slideshows {

	/**
	 * Themes class
	 * 
	 * @var object
	 */
	private $themes;

	/**
	 * Theme instance
	 * 
	 * @var object
	 * @see get_instance()
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		if (!class_exists('MetaSlider_Themes')) {
			require_once plugin_dir_path(__FILE__) . 'Themes.php';
		}
		$this->themes = MetaSlider_Themes::get_instance();
	}

	/**
	 * Used to access the instance
	 */
	public static function get_instance() {
		if (null === self::$instance) self::$instance = new self();
		return self::$instance;
	}

	/**
	 * Method to add a slideshow
	 * 
	 * @return int
	 */
	public function create() {

		// Duplicate settings from their recently modified slideshow, or use defaults.
		$recent_slideshow =  $this->get_recent_slideshow();
		$latest_slideshow_id = !empty($recent_slideshow) ? $recent_slideshow['id'] : null;
		$settings = new MetaSlider_Slideshow_Settings($latest_slideshow_id);
		
		// Insert the slideshow
		// TODO: Maybe have a list of 100 random words that could be slideshow titles
        $slideshow_id = wp_insert_post(array(
			'post_title' => __("New Slideshow", "ml-slider"),
			'post_status' => 'publish',
			'post_type' => 'ml-slider'
		));
		
		if (is_wp_error($slideshow_id)) {
			// No translation as this wont show to the user (but will in the payload)
			return new WP_Error('post_create_failed', 'A new, blank slideshow could not be created', array('status' => 409));
		}

		// TODO: Perhaps we create a settings page and let the user select defaults
        add_post_meta($slideshow_id, 'ml-slider_settings', $settings->get_settings(), true);

		// Needed for creating a relationship with slides
		wp_insert_term($slideshow_id, 'ml-slider');
		
		return $slideshow_id;
	}

	/**
	 * Method to save a slideshow
	 * 
	 * @param int|string $slideshow_id - The id of the slideshow
	 * @param array		 $new_settings - The settings
	 * 
	 * @return int - id of the slideshow
	 */
	public function save($slideshow_id, $new_settings) {

		// TODO: This is old code copied over and should eventually be refactored to not require hard-coded values
		$old_settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);

		// convert submitted checkbox values from 'on' or 'off' to boolean values
		$checkboxes = apply_filters("metaslider_checkbox_settings", array('noConflict', 'fullWidth', 'hoverPause', 'links', 'reverse', 'random', 'printCss', 'printJs', 'smoothHeight', 'center', 'carouselMode', 'autoPlay', 'firstSlideFadeIn', 'responsive_thumbs'));

		foreach ($checkboxes as $checkbox) {
			$new_settings[$checkbox] = (isset($new_settings[$checkbox]) && 'on' == $new_settings[$checkbox]) ? 'true' : 'false';
		}

		$settings = array_merge((array) $old_settings, $new_settings);

		update_post_meta($slideshow_id, 'ml-slider_settings', $settings);
		
		return $slideshow_id;
	}

	/**
	 * Method to duplicate a slideshow
	 * 
	 * @param int|string $slideshow_id - The id of the slideshow to duplicate
	 * 
	 * @throws Exception - handled within method.
	 * @return int|boolean - id of the new slideshow to show, or false
	 */
	public function duplicate($slideshow_id) {
		$new_slideshow_id = 0;

		try {
			$new_slideshow_id = wp_insert_post(array(
				'post_title' => get_the_title($slideshow_id),
				'post_status' => 'publish',
				'post_type' => 'ml-slider'
			), true);
	
			if (is_wp_error($new_slideshow_id)) {
				throw new Exception($new_slideshow_id->get_error_message());
			}
	
			foreach (get_post_meta($slideshow_id) as $key => $value) {
				update_post_meta($new_slideshow_id, $key, maybe_unserialize($value[0]));
			}

			// Not used at the moment, but indicates this is a copy
			update_post_meta($new_slideshow_id, 'metaslider_copy_of', $slideshow_id);

			// Slides are associated to a slideshow via post terms
			$term = wp_insert_term($new_slideshow_id, 'ml-slider');
			
			// Duplicate each slide
			foreach ($this->active_slide_ids($slideshow_id) as $slide_id) {

				$type = get_post_meta($slide_id, 'ml-slider_type', true);
				$new_slide_id = wp_insert_post(array(
					'post_title' => "Slider {$new_slideshow_id} - {$type}",
					'post_status' => 'publish',
					'post_type' => 'ml-slide',
					'post_excerpt' => get_post_field('post_excerpt', $slide_id),
					'menu_order' => get_post_field('menu_order', $slide_id)
				), true);

				if (is_wp_error($new_slide_id)) {
					throw new Exception($new_slideshow_id->get_error_message());
				}
				
				foreach (get_post_meta($slide_id) as $key => $value) {
					add_post_meta($new_slide_id, $key, maybe_unserialize($value[0]));
				}

				wp_set_post_terms($new_slide_id, $term['term_id'], 'ml-slider', true);
			}

		} catch (Exception $e) {

			// If there was a failure somewhere, clean up
			wp_trash_post($new_slideshow_id);
			$this->delete_all_slides($new_slideshow_id);
			
			return new WP_Error('slide_duplication_failed', $e->getMessage());
		}

		// External modules manipulate data here
		do_action('metaslider_slideshow_duplicated', $slideshow_id, $new_slideshow_id);

		return $new_slideshow_id;
	}

	/**
	 * Method to delete a slideshow
	 * 
	 * @param int|string $slideshow_id - The id of the slideshow to delete
	 * 
	 * @return int|boolean - id of the next slideshow to show, or false
	 */
	public function delete($slideshow_id) {

        // Send the post to trash
		$id = wp_update_post(array(
			'ID' => $slideshow_id,
			'post_status' => 'trash'
		));

		$this->delete_all_slides($slideshow_id);

		$recent_slideshow = $this->get_recent_slideshow();
		return !empty($recent_slideshow) ? $recent_slideshow['id'] : false;
	}


	/**
     * Method to disassociate slides from a slideshow
     *
     * @param int $slideshow_id - the id of the slideshow
	 * 
	 * @return int
     */
	public function delete_all_slides($slideshow_id) {
		$args = array(
			'force_no_custom_order' => true,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'post_type' => array('ml-slide'),
			'post_status' => array('publish'),
			'lang' => '', // polylang, ingore language filter
			'suppress_filters' => 1, // wpml, ignore language filter
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'ml-slider',
					'field' => 'slug',
					'terms' => $slideshow_id
				)
			)
		);

		// I believe if this fails there's no real harm done
		// because slides don't really need to be linked to their parent slideshow
		$query = new WP_Query($args);
		while ($query->have_posts()) {
			$query->next_post();
			wp_trash_post($query->post->ID);
		}

		return $slideshow_id;
	}


	/**
	 * Method to get the most recently modified slideshow
	 * 
	 * @return array The id of the slideshow
	 */
	public function get_recent_slideshow() {

        $args = array(
            'force_no_custom_order' => true,
            'post_type' => 'ml-slider',
            'num_posts' => 1,
            'post_status' => 'publish',
            'suppress_filters' => 1, // wpml, ignore language filter
            'orderby' => 'modified',
            'order' => 'DESC'
        );

		$slideshow = get_posts(apply_filters('metaslider_all_meta_sliders_args', $args));

        return isset($slideshow[0]) ? $this->build_slideshow_object($slideshow[0]) : array();
	}

	/**
	 * Method to get all slideshows from the database
	 * 
	 * @return array 
	 */
	public function get_all_slideshows() {

        $args = array(
            'post_type' => 'ml-slider',
            'post_status' => array('inherit', 'publish'),
            'orderby' => 'modified',
            'suppress_filters' => 1, // wpml, ignore language filter
            'posts_per_page' => -1
		);

		$slideshows = get_posts(apply_filters('metaslider_all_meta_sliders_args', $args));

        return array_map(array($this, 'build_slideshow_object'), $slideshows);
	}

	/**
     * Method to build out the slideshow object
	 * For now this wont include slides. They will be handled separately.
     *
	 * @param object $slideshow - The slideshow object
     * @return array
     */
	public function build_slideshow_object($slideshow) {

		if (empty($slideshow)) return array();

		$slideshows = array(
			'id' => $slideshow->ID,
			'title' => $slideshow->post_title,
			'created_at' => $slideshow->post_date,
			'modified_at' => $slideshow->post_modified,
			'modified_at_gmt' => $slideshow->post_modified_gmt,
			'slides' => $this->active_slide_ids($slideshow->ID)
		);

		foreach (get_post_meta($slideshow->ID) as $key => $value) {
			$key = str_replace('ml-slider_settings', 'settings', $key);
			$key = str_replace('metaslider_slideshow_theme', 'theme', $key);
			$slideshows[$key] = maybe_unserialize($value[0]);
		}

		return $slideshows;
	}

	/**
     * Method to get the slide ids
	 * 
	 * @param int|string $id - The id of the slideshow
	 * @return array - Returns an array of just the slide IDs
     */
	public function active_slide_ids($id) {
		$slides = get_posts(array(
			'force_no_custom_order' => true,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'post_type' => array('attachment', 'ml-slide'),
			'post_status' => array('inherit', 'publish'),
			'lang' => '',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'ml-slider',
					'field' => 'slug',
					'terms' => $id
				)
			)
		));

		$slide_ids = array();
		foreach ($slides as $slide) {
			$type = get_post_meta($slide->ID, 'ml-slider_type', true);
            $type = $type ? $type : 'image'; // Default ot image

			// If this filter exists, that means the slide type is available (i.e. pro slides)
			if (has_filter("metaslider_get_{$type}_slide")) {
				array_push($slide_ids, $slide->ID);
			}
		}
		return $slide_ids;
	}

	/**
     * Method to get the latest slideshow
     */
	public function recently_modified() {}

	/**
     * Method to get a single slideshow from the database
	 * 
	 * @param string $id - The id of a slideshow
     */
	public function single($id) {}
	
	/**
     * Returns the shortcode of the slideshow
	 * 
	 * @param string|int  $id 		   - The id of a slideshow
	 * @param string|int  $restrict_to - page to limit the slideshow to
	 * @param string|null $theme_id    - load a theme, defaults to the current theme
     */
	public function shortcode($id = null, $restrict_to = null, $theme_id = null) {

		// if no id is given, try to find the first available slideshow
		if (is_null($id)) {
			$the_query = get_posts(array('orderby' => 'rand', 'posts_per_page' => '1'));
			$id = isset($the_query[0]) ? $the_query[0]->ID : $id;
		}

		return "[metaslider id='{$id}' restrict_to='{$restrict_to}' theme='{$theme_id}']";
	}

	/**
	 * Return the preview
	 * 
	 * @param int|string $slideshow_id The id of the current slideshow
	 * @param string 	 $theme_id 	   The folder name of the theme
	 * 
	 * @return string|WP_Error whether the file was included, or error class
	 */
	public function preview($slideshow_id, $theme_id = null) {
		if (!class_exists('MetaSlider_Slideshow_Settings')) {
			require_once plugin_dir_path(__FILE__) . 'Settings.php';
		}
		$settings = new MetaSlider_Slideshow_Settings($slideshow_id);

        try {
            ob_start();

            // Remove the admin bar
            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
            
            // Load in theme if set. Note that the shortcode below is set to 'none'
            $this->themes->load_theme($slideshow_id, $theme_id); ?>

<!DOCTYPE html>
<html>
	<head>
		<style type='text/css'>
			<?php ob_start(); ?>
			body, html {
				overflow: auto;
				height:100%;
				margin:0;
				padding:0;
				box-sizing: border-box;
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; 
		        font-size: 14px; 
			}
			body {
				padding: 60px 40px 40px;
			}
			#preview-container {
				min-height: 100%;
				max-width: <?php echo $settings->get_single('width'); ?>px;
				margin: 0 auto;
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-webkit-box-align: center;
				   -ms-flex-align: center;
				      align-items: center;
				-webkit-box-pack: center;
				   -ms-flex-pack: center;
				 justify-content: center;
			}
			#preview-inner {
				width: 100%;
				height: 100%;
			}
			.metaslider {
				margin: 0 auto;
			}
			<?php echo apply_filters('metaslider_preview_styles', ob_get_clean()); ?>
		</style>
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Expires" content="0">
	</head>
	<body>
		<div id="preview-container">
			<div id="preview-inner">
				<?php echo do_shortcode($this->shortcode(absint($slideshow_id), null, 'none')); ?>
			</div>
		</div>
		<?php wp_footer(); ?>
	</body>
</html>
			<?php return preg_replace('/\s+/S', " ", ob_get_clean());
		} catch (Exception $e) {
			ob_clean();
			return new WP_Error('preview_failed', $e->getMessage());
		}
	}
}
