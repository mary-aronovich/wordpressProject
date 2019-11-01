<?php
/**
 * Default Single
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( have_posts() ) :

	while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-post row">
				<div class="gallery-thumbnail <?php echo esc_attr( pojo_gallery_get_single_layout_class( 'thumbnail' ) ); ?>">
					<?php do_action( 'pojo_gallery_print_front', get_the_ID() ); ?>
				</div>
				<div class="gallery-content <?php echo esc_attr( pojo_gallery_get_single_layout_class( 'content' ) ); ?>">
					<header class="entry-header">
						<?php if ( po_breadcrumbs_need_to_show() ) : ?>
							<?php pojo_breadcrumbs(); ?>
						<?php endif; ?>
						<?php if ( pojo_is_show_page_title() ) : ?>
							<div class="page-title">
								<h1 class="entry-title"><?php the_title(); ?></h1>
							</div>
						<?php endif; ?>
					</header>
					<div class="entry-content">
						<?php if ( ! Pojo_Core::instance()->builder->display_builder() ): ?>
							<?php the_content(); ?>
						<?php endif; ?>
					</div>
					<?php
						// Previous/next post navigation.
						echo pojo_get_post_navigation(
							array(
								'prev_text' => __( '&laquo; Previous', 'pojo' ),
								'next_text' => __( 'Next &raquo;', 'pojo' ),
							)
						);
					?>
					<footer>
						<?php pojo_button_post_edit(); ?>
					</footer>
				</div>
			</div>
		</article>
	<?php endwhile;
else :
	pojo_get_content_template_part( 'content', 'none' );
endif;