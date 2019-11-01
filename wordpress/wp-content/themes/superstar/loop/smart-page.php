<?php
/**
 * Smart Page loop.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $_pojo_parent_id, $content_query;

$content_query = new WP_Query( po_get_archive_query() );
$_pojo_parent_id = get_the_ID();
$pagination = atmb_get_field( 'po_pagination' );

$display_type = po_get_display_type();

if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( po_breadcrumbs_need_to_show() ) : ?>
			<?php pojo_breadcrumbs(); ?>
		<?php endif; ?>
		<?php if ( pojo_is_show_page_title() ) : ?>
		<header class="page-title">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>
		<?php endif; ?>
		<?php if ( post_password_required( $_pojo_parent_id ) ) : ?>
			<?php echo get_the_password_form( $_pojo_parent_id ); ?>
		<?php else : ?>
			<?php if ( $content_query->have_posts() ) : ?>
				<?php do_action( 'pojo_before_content_loop', $display_type ); ?>
				<?php while ( $content_query->have_posts() ) : $content_query->the_post(); ?>
					<?php pojo_get_content_template_part( 'content', $display_type ); ?>
				<?php endwhile;
				wp_reset_postdata(); ?>
				<?php do_action( 'pojo_after_content_loop', $display_type ); ?>
				<?php if ( 'hide' !== $pagination ) : ?>
					<?php pojo_paginate( $content_query ); ?>
				<?php endif; ?>
				<?php echo apply_filters( 'the_content', '' ); ?>
			<?php else : ?>
				<?php pojo_get_content_template_part( 'content', 'none' ); ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php pojo_button_post_edit(); ?>
	<?php endwhile; ?>
<?php endif; ?>