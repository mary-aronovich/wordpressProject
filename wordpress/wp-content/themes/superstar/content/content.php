<?php
/**
 * Default content (Blog).
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $_pojo_parent_id;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( apply_filters( 'pojo_post_classes', array( 'media' ), get_post_type() ) ); ?>>
	<h3 class="media-heading entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php po_print_archive_excerpt( $_pojo_parent_id ); ?>

	<div class="entry-meta">
		<?php if ( po_archive_metadata_show( 'date', $_pojo_parent_id ) ) : ?>
			<span><time datetime="<?php the_time('o-m-d'); ?>" class="entry-date date published updated"><?php echo get_the_date(); ?></time></span>
		<?php endif; ?>
		<?php if ( po_archive_metadata_show( 'time', $_pojo_parent_id ) ) : ?>
			<span class="entry-time"><?php echo get_the_time(); ?></span>
		<?php endif; ?>
		<?php if ( po_archive_metadata_show( 'comments', $_pojo_parent_id ) ) : ?>
			<span class="entry-comment"><?php comments_popup_link( __( 'No Comments', 'pojo' ), __( 'One Comment', 'pojo' ), __( '% Comments', 'pojo' ), 'comments' ); ?></span>
		<?php endif; ?>
		<?php if ( po_archive_metadata_show( 'author', $_pojo_parent_id ) ) : ?>
			<span class="entry-user vcard author"><a class="fn" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" rel="author"><?php echo get_the_author(); ?></a></span>
		<?php endif; ?>
		<?php po_print_archive_readmore( $_pojo_parent_id ); ?>
	</div>

</article>