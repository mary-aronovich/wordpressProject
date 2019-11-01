<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function pojo_enqueue_scripts() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
	// CSS Framework
	switch ( Pojo_Core::instance()->get_css_framework_type() ) {
		case 'materialize' :
			wp_enqueue_style( 'pojo-css-framework', get_template_directory_uri() . '/assets/materialize/css/materialize.min.css', false, POJO_VER_MATERIALIZE );

			wp_register_script( 'pojo-plugins', get_template_directory_uri() . '/assets/materialize/js/materialize.min.js', array( 'jquery' ), POJO_VER_MATERIALIZE, true );
			break;
		
		default :
			wp_enqueue_style( 'pojo-css-framework', get_template_directory_uri() . '/assets/bootstrap/css/bootstrap.min.css', false, POJO_VER_BOOTSTRAP );

			wp_register_script( 'pojo-plugins', get_template_directory_uri() . '/assets/bootstrap/js/bootstrap.min.js', array( 'jquery' ), POJO_VER_BOOTSTRAP, true );
			break;
	}
	wp_enqueue_script( 'pojo-plugins' );
	

	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/font-awesome/css/font-awesome.min.css', array(), POJO_VER_FONT_AWESOME );
	
	if ( POJO_DEVELOPER_MODE ) {
		wp_register_script( 'pojo-modernizr', get_template_directory_uri() . '/core/assets/js/modernizr-2.6.2.min.js', false, '2.6.2' );
		wp_enqueue_script( 'pojo-modernizr' );
		
		//wp_enqueue_script( 'jquery-ui-sortable' );
	
		// jQuery Easing plugin.
		//wp_register_script( 'jquery-easing', get_template_directory_uri() . '/js/jquery.easing.min.js', array( 'jquery' ), '1.3', true );
		//wp_enqueue_script( 'jquery-easing' );
	
		// Superfish plugin.
		wp_register_script( 'hover-intent', get_template_directory_uri() . '/core/assets/js/hoverIntent.js', array( 'jquery' ), '2013.03.11', true );
		
		wp_enqueue_script( 'hover-intent' );
		
		wp_register_script( 'superfish', get_template_directory_uri() . '/core/assets/js/superfish.js', array( 'jquery' ), '1.5.11', true );
		wp_enqueue_script( 'superfish' );
		
		// Isotope
		wp_register_script( 'isotope', get_template_directory_uri() . '/core/assets/js/jquery.isotope.js', array( 'jquery' ), '1.5.25', true );
		wp_enqueue_script( 'isotope' );
		
		// Infinitescroll
		wp_register_script( 'infinitescroll', get_template_directory_uri() . '/core/assets/js/jquery.pojoInfiniteScroll.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'infinitescroll' );
	
		// bxSlider
		//wp_enqueue_style( 'bxslider', get_template_directory_uri() . '/core/assets/bxslider/jquery.bxslider.css', array(), '4.1.1' );
	
		wp_enqueue_script( 'fitvids', get_template_directory_uri() . '/core/assets/bxslider/plugins/jquery.fitvids.js', array( 'jquery' ), '1.0' );
		
		wp_register_script( 'bxslider', get_template_directory_uri() . '/core/assets/bxslider/jquery.bxslider.min.js', array( 'jquery', 'fitvids' ), '4.2.5' );
		wp_enqueue_script( 'bxslider' );
		
		// Waypoints
		wp_register_script( 'waypoints', get_template_directory_uri() . '/core/assets/js/waypoints.js', array( 'jquery' ), '2.0.2' );
		wp_enqueue_script( 'waypoints' );
		
		// Our custom js scripts.
		wp_register_script( 'pojo-scripts', get_template_directory_uri() . '/core/assets/js/pojo-scripts.js', array( 'jquery' ), POJO_CORE_VERSION, true );
		wp_enqueue_script( 'pojo-scripts' );
		
		/*if ( is_rtl() )
			wp_enqueue_style( 'pojo-style-rtl', get_stylesheet_directory_uri() . '/css/rtl.css', array( 'pojo-css-framework', 'pojo-style' ), POJO_CORE_VERSION );*/
	} else {
		// Our custom js scripts.
		wp_register_script( 'pojo-scripts', get_template_directory_uri() . '/assets/js/frontend.min.js', array( 'jquery' ), POJO_CORE_VERSION, true );
		wp_enqueue_script( 'pojo-scripts' );
	}
	
	wp_register_script( 'masterslider', get_template_directory_uri() . '/core/assets/masterslider/masterslider.min.js', array( 'jquery' ), '2.9.5', true );
	
	wp_localize_script(
		'pojo-scripts',
		'Pojo',
		apply_filters(
			'pojo_localize_scripts_array',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'css_framework_type' => Pojo_Core::instance()->get_css_framework_type(),
				'superfish_args' => array(
					'delay' => 150,
					'animation' => array( 'opacity' => 'show', 'height' => 'show' ),
					'speed' => 'fast',
				),
			)
		)
	);
}
add_action( 'wp_enqueue_scripts', 'pojo_enqueue_scripts', 100 );

function pojo_enqueue_styles() {
	wp_enqueue_style( 'pojo-base-style', get_template_directory_uri() . '/core/assets/css/style.min.css', array( 'pojo-css-framework' ), POJO_CORE_VERSION );

	$suffix_css = ! is_child_theme() ? '.min' : '';
	wp_enqueue_style( 'pojo-style', get_stylesheet_directory_uri() . '/assets/css/style' . $suffix_css . '.css', array( 'pojo-css-framework' ), POJO_CORE_VERSION );

	if ( is_rtl() ) {
		wp_enqueue_style( 'pojo-base-style-rtl', get_template_directory_uri() . '/core/assets/css/rtl.min.css', array( 'pojo-css-framework', 'pojo-base-style' ), POJO_CORE_VERSION );

		wp_enqueue_style( 'pojo-style-rtl', get_stylesheet_directory_uri() . '/assets/css/rtl' . $suffix_css . '.css', array( 'pojo-css-framework', 'pojo-style' ), POJO_CORE_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'pojo_enqueue_styles', 600 );

function pojo_admin_print_scripts() {
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );

	wp_enqueue_script( 'jquery-ui-resizable' );
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	
	wp_enqueue_script( 'jquery-effects-core' );
	
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_style( 'pojo-admin-ui', get_template_directory_uri() . '/core/assets/admin-ui/css/admin-ui.min.css', array(), POJO_CORE_VERSION );
	
	if ( is_rtl() ) {
		wp_enqueue_style( 'pojo-admin-ui-rtl', get_template_directory_uri() . '/core/assets/admin-ui/css/rtl.min.css', array( 'pojo-admin-ui' ) );
	}

	if ( POJO_DEVELOPER_MODE ) {
		wp_enqueue_script( 'jquery-mousewheel', get_template_directory_uri() . '/core/assets/admin-ui/jquery.mousewheel.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-mCustomScrollbar', get_template_directory_uri() . '/core/assets/admin-ui/jquery.mCustomScrollbar.js', array( 'jquery', 'jquery-mousewheel' ) );
		
		wp_enqueue_script( 'pojo-fields-plugin', get_template_directory_uri() . '/core/assets/admin-ui/fields-plugin.js', array( 'jquery' ) );
		
		wp_enqueue_script( 'pojo-admin-script', get_template_directory_uri() . '/core/assets/admin-ui/pojo-admin.js', array( 'jquery' ) );
	} else {
		wp_enqueue_script( 'pojo-admin-script', get_template_directory_uri() . '/assets/js/admin-ui.min.js', array( 'jquery' ), POJO_CORE_VERSION );
	}

	wp_localize_script(
		'pojo-admin-script',
		'POJO_ADMIN',
		apply_filters(
			'pojo_admin_localize_scripts_array',
			array(
				'lang_remove_widget' => __( 'Do you want remove the Widget?', 'pojo' ),
				'lang_remove_row' => __( 'Do you want remove the Row?', 'pojo' ),
				'lang_remove_template' => __( 'Do you want remove this template?', 'pojo' ),
			)
		)
	);
}
add_action( 'admin_print_scripts', 'pojo_admin_print_scripts', 100 );

function pojo_wp_head() {
	$option = pojo_get_option( 'favicon_icon' );
	if ( ! empty( $option ) )
		printf( '<link rel="shortcut icon" href="%s" />', $option );

	$apple_touch_icons = array(
		// size => option_slug
		'144x144' => 'apple_touch_icon_ipad_retina',
		'114x114' => 'apple_touch_icon_iphone_retina',
		'72x72'   => 'apple_touch_icon_ipad',
		'57x57'   => 'apple_touch_icon_iphone',
	);

	foreach ( $apple_touch_icons as $size => $option_slug ) {
		$option = pojo_get_option( $option_slug );
		if ( ! empty( $option ) )
			printf( '<link rel="apple-touch-icon" sizes="%s" href="%s" />', $size, $option );
	}
}
add_action( 'wp_head', 'pojo_wp_head' );

function pojo_wp_footer() {
	$option = pojo_get_option( 'txt_google_analytics_id' );
	if ( ! empty( $option ) ) : ?>
		<script type="text/javascript">//<![CDATA[
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', '<?php echo $option; ?>']);
			_gaq.push(['_trackPageview']);
			(function (){
				var ga = document.createElement('script');
				ga.type = 'text/javascript';
				ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ga, s);
			})();
			//]]></script>
	<?php endif;
}
add_action( 'wp_footer', 'pojo_wp_footer' );
