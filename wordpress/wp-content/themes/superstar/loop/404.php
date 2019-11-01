<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$has_custom_page = $custom_404_query = false;

$pojo_404_page_id = get_theme_mod( 'pojo_404_page_id', 0 );
if ( ! empty ( $pojo_404_page_id ) ) :
	$custom_404_query = new WP_Query( array( 'p' => $pojo_404_page_id, 'post_type' => 'any' ) );

	if ( $custom_404_query->have_posts() ) :
		$has_custom_page = true;
	endif;
endif;
?>
	<article id="post-0" class="post no-results not-found hentry">
		<div class="entry-page">
			<?php
			if ( $has_custom_page ) :

				$custom_404_query->the_post();
				
				if ( ! Pojo_Core::instance()->builder->display_builder( $pojo_404_page_id ) ) :
					the_content();
				endif;

				wp_reset_postdata();
			
			else : ?>
				<h1><?php _e( '404', 'pojo' ); ?></h1>
				<h2><?php _e( 'Not Found', 'pojo' ); ?></h2>
				<h3><?php _e( 'Sorry, this page could not be found!', 'pojo' ); ?></h3>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div>
	</article><!-- #post-0 -->
