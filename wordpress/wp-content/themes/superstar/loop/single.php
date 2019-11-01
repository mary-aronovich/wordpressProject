<?php
/**
 * Default Single
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( have_posts() ) :

	while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( po_breadcrumbs_need_to_show() ) : ?>
				<?php pojo_breadcrumbs(); ?>
			<?php endif; ?>
			<header class="page-title">
				<div class="entry-meta">
					<?php if ( po_single_metadata_show( 'date' ) ) : ?>
						<span><time datetime="<?php the_time('o-m-d'); ?>" class="entry-date date published updated"><?php echo get_the_date(); ?></time></span>
					<?php endif; ?>
					<?php if ( po_single_metadata_show( 'time' ) ) : ?>
						<span class="entry-time"><?php echo get_the_time(); ?></span>
					<?php endif; ?>
					<?php if ( po_single_metadata_show( 'comments' ) ) : ?>
						<span class="entry-comment"><?php comments_popup_link( __( 'No Comments', 'pojo' ), __( 'One Comment', 'pojo' ), __( '% Comments', 'pojo' ), 'comments' ); ?></span>
					<?php endif; ?>
					<?php if ( po_single_metadata_show( 'author' ) ) : ?>
						<span class="entry-user vcard author"><a class="fn" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" rel="author"><?php echo get_the_author(); ?></a></span>
					<?php endif; ?>
				</div>
				<?php if ( pojo_is_show_page_title() ) : ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php endif; ?>
			</header>

			<div class="entry-content">
				<?php if ( ! Pojo_Core::instance()->builder->display_builder() ) : ?>
					<?php the_content(); ?>
					<?php pojo_link_pages(); ?>
				<?php endif; ?>
			</div>
			<?php $tags = get_the_tags(); if ( $tags ) : ?><div class="entry-tags"><?php the_tags( '', ' ' ); ?></div><?php endif; ?>

			<?php
				// Previous/next post navigation.
				echo pojo_get_post_navigation(
					array(
						'prev_text' => __( '&laquo; Previous', 'pojo' ),
						'next_text' => __( 'Next &raquo;', 'pojo' ),
					)
				);
			?>

			<?php comments_template( '', true ); ?>
			
			<footer>
				<?php pojo_button_post_edit(); ?>
			</footer>
		</article>
	<?php endwhile;
else :
	pojo_get_content_template_part( 'content', 'none' );
endif;