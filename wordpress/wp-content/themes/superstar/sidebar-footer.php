<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! is_active_sidebar( 'pojo-' . sanitize_title( 'Sticky Header' ) ) )
	return;
?>
<div id="sidebar-footer">
	<div class="<?php echo WRAP_CLASSES; ?>">
		<div class="<?php echo CONTAINER_CLASSES; ?>">
			<?php dynamic_sidebar( 'pojo-' . sanitize_title( 'Sticky Header' ) ); ?>
		</div>
	</div>
</div>