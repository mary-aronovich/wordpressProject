<?php
/**
 * Recent Gallery: Grid Three
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $_current_widget_instance;

$categories       = '';
$categories_terms = get_the_terms( null, 'pojo_gallery_cat' );
if ( ! empty( $categories_terms ) && ! is_wp_error( $categories_terms ) )
	$categories = wp_list_pluck( $categories_terms, 'name' );
?>
<div class="recent-gallery grid-item gallery-grid col-md-4 col-sm-6 col-xs-12">
	<a class="image-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
		<?php if ( $image_url = Pojo_Thumbnails::get_post_thumbnail_url( array( 'width' => '640', 'height' => '400', 'crop' => true, 'placeholder' => true ) ) ) : ?>
			<img src="<?php echo $image_url; ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="media-object" />
		<?php endif; ?>
		<div class="overlay-image"></div>
		<div class="caption overlay-title">
			<h4 class="grid-heading">
				<?php the_title(); ?>
				<?php if ( ! empty( $categories ) ) : ?>
					<small><?php echo implode( ', ', $categories ); ?></small>
				<?php endif; ?>
			</h4>
		</div>
	</a>
</div>