<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$display_type = pojo_get_option( 'posts_display_type' );
if ( empty( $display_type ) )
	$display_type = 'small_thumb';

?>
<?php if ( po_breadcrumbs_need_to_show() ) : ?>
	<?php pojo_breadcrumbs(); ?>
<?php endif; ?>
<?php if ( ! is_home() && ! is_front_page() ) : ?>
<h1 class="entry-title"><?php
	if ( is_day() ) :
		printf( __( 'Daily Archive: %s', 'pojo' ), get_the_date() );
	elseif ( is_month() ) :
		printf( __( 'Monthly Archive: %s', 'pojo' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'pojo' ) ) );
	elseif ( is_year() ) :
		printf( __( 'Yearly Archive: %s', 'pojo' ), get_the_date( _x( 'Y', 'yearly archives date format', 'pojo' ) ) );
	elseif ( is_category() ) :
		echo single_cat_title( '', false );
	elseif ( is_tag() ) :
		echo single_tag_title( '', false );
	elseif ( is_author() ) :
		global $author;
		$userdata = get_userdata( $author );
		printf( __( 'Article of: %s', 'pojo' ), $userdata->display_name );
	else :
		_e( 'Archive', 'pojo' );
	endif;
	?></h1>
<?php endif; ?>
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
