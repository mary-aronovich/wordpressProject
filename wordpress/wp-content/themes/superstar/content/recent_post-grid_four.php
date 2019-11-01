<?php
/**
 * Recent Post: Grid Four
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $_current_widget_instance;
?>
<div class="recent-post grid-item col-md-3 col-sm-6 col-xs-12">
	<?php if ( 'show' === $_current_widget_instance['thumbnail'] && $image_url = Pojo_Thumbnails::get_post_thumbnail_url( array( 'width' => '640', 'height' => '400', 'crop' => true, 'placeholder' => true ) ) ) : ?>
		<a class="image-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
			<img src="<?php echo $image_url; ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="media-object" />
			<div class="overlay-image"></div>
			<div class="overlay-title fa"></div>
		</a>
	<?php endif; ?>
	<div class="caption">
		<?php if ( 'show' === $_current_widget_instance['show_title'] ) : ?>
			<h4 class="grid-heading entry-title">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h4>
		<?php endif; ?>
		<?php if ( 'show' === $_current_widget_instance['except'] ) : ?>
			<?php echo pojo_get_words_limit( get_the_excerpt(), $_current_widget_instance['except_length_words'] ); ?>
		<?php endif; ?>
		<div class="entry-meta">
			<?php if ( 'show' === $_current_widget_instance['metadata_date'] ) : ?>
				<span><time datetime="<?php the_time('o-m-d'); ?>" class="entry-date date published updated"><?php echo get_the_date(); ?></time></span>
			<?php endif; ?>
			<?php if ( 'show' === $_current_widget_instance['metadata_time'] ) : ?>
				<span class="entry-time"><?php echo get_the_time(); ?></span>
			<?php endif; ?>
			<?php if ( 'show' === $_current_widget_instance['metadata_comments'] ) : ?>
				<span class="entry-comment"><?php comments_popup_link( __( 'No Comments', 'pojo' ), __( 'One Comment', 'pojo' ), __( '% Comments', 'pojo' ), 'comments' ); ?></span>
			<?php endif; ?>
			<?php if ( 'show' === $_current_widget_instance['metadata_author'] ) : ?>
				<span class="entry-user vcard author"><a class="fn" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" rel="author"><?php echo get_the_author(); ?></a></span>
			<?php endif; ?>
			<?php if ( 'show' === $_current_widget_instance['metadata_readmore'] ) : ?>
				<a href="<?php the_permalink(); ?>" class="read-more"><?php echo  ! empty( $_current_widget_instance['text_readmore_mode'] ) ? $_current_widget_instance['text_readmore_mode'] : __( 'Read More &raquo;', 'pojo' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>