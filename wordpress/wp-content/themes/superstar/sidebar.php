<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<aside id="sidebar" class="<?php echo SIDEBAR_CLASSES; ?>" role="complementary">
	<?php
		// Setup Default sidebar.
		$sidebar = $default_sidebar = 'pojo-' . sanitize_title( 'Main Sidebar' );
		po_dynamic_sidebar( $sidebar );
	?>
</aside>