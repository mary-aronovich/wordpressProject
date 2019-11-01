<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$display_type = pojo_get_option( 'gallery_display_type' );
if ( empty( $display_type ) )
	$display_type = 'gallery_grid_three';
?>
<?php if ( po_breadcrumbs_need_to_show() ) : ?>
	<?php pojo_breadcrumbs(); ?>
<?php endif; ?>
<h1 class="entry-title"><?php _e( 'Archive Galleries', 'pojo' ); ?></h1>
<?php if ( have_posts() ) : ?>
	<?php do_action( 'pojo_before_content_loop', $display_type ); ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php pojo_get_content_template_part( 'content', $display_type ); ?>
	<?php endwhile; ?>
	<?php do_action( 'pojo_after_content_loop', $display_type ); ?>
	<?php if ( 'hide' !== pojo_get_option( 'archive_pagination' ) ) : ?>
		<?php pojo_paginate(); ?>
	<?php endif; ?>
<?php else : ?>
	<?php pojo_get_content_template_part( 'content', 'none' ); ?>
<?php endif; ?>
