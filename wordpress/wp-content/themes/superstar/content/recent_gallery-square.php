<?php
/**
 * Recent Gallery: Square
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $_current_widget_instance;
?>
<div class="recent-post grid-item grid-square">
	<?php if ( $image_url = Pojo_Thumbnails::get_post_thumbnail_url( array( 'width' => '75', 'height' => '75', 'crop' => true, 'placeholder' => true ) ) ) : ?>
		<a class="image-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
			<img src="<?php echo $image_url; ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="media-object" />
			<div class="overlay-image"></div>
		</a>
	<?php endif; ?>
</div>