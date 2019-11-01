<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'pojo_comment' ) ) :
	function pojo_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment; ?>
		<li <?php comment_class( 'media' ); ?>>
			<?php if ( 0 != $args['avatar_size'] ) : // Avatar ?>
				<div class="pull-left">
					<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
				</div>
			<?php endif; ?>
			<div class="media-body">
				<header class="comment-author vcard">
					<?php echo '<cite class="fn">' . get_comment_author_link() . '</cite>'; ?>
					<time datetime="<?php comment_date( 'c' ); ?>">
						<a href="<?php echo esc_attr( get_comment_link( $comment->comment_ID ) ); ?>"><?php printf( __( '%1$s at %2$s', 'pojo' ), get_comment_date(), get_comment_time() ); ?></a>
					</time>
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					<?php edit_comment_link( __( '(Edit)', 'pojo' ), '', '' ); ?>
				</header>

				<article id="comment-<?php comment_ID(); ?>">
					<?php if ( $comment->comment_approved == '0' ) : ?>
						<?php pojo_alert( __( 'Your comment is awaiting moderation.', 'pojo' ), false, false, 'block' ); ?>
					<?php endif; ?>
					<section class="comment">
						<?php comment_text() ?>
					</section>

				</article><!-- #comment-ID -->
			</div><!-- .media-body -->
		</li><!-- .media -->
	<?php }
endif;
?>

<?php if ( post_password_required() ) : ?>
	<section id="comments">
		<?php pojo_alert( __( 'This post is password protected. Enter the password to view comments.', 'pojo' ), false, false, 'block' ); ?>
	</section><!-- /#comments -->
	<?php
	return;
endif; ?>

<?php if ( have_comments() ) : ?>
	<section id="comments">
		<h5 class="title-comments"><span><?php printf( _n( 'One Response', '%1$s Responses', get_comments_number(), 'pojo' ), number_format_i18n( get_comments_number() ), get_the_title() ); ?></span></h5>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'callback' => 'pojo_comment', 'avatar_size' => 48 ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
			<nav id="comments-nav" class="pager">
				<div class="previous"><?php previous_comments_link( __( '&larr; Older comments', 'pojo' ) ); ?></div>
				<div class="next"><?php next_comments_link( __( 'Newer comments &rarr;', 'pojo' ) ); ?></div>
			</nav>

		<?php endif; // check for comment navigation ?>
	</section><!-- /#comments -->
<?php endif; ?>

<?php if ( comments_open() ) : ?>
	<section id="respond">
		<h5 class="title-respond"><span><?php comment_form_title( __( 'Leave a Reply', 'pojo' ), __( 'Leave a Reply to %s', 'pojo' ) ); ?></span></h5>

		<p class="cancel-comment-reply"><?php cancel_comment_reply_link(); ?></p>

		<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>

			<p><?php printf( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'pojo' ), wp_login_url( get_permalink() ) ); ?></p>

		<?php else : ?>

			<form action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php" method="post" id="commentform" class="form row">
				<?php if ( is_user_logged_in() ) : ?>

					<p class="col-md-12"><?php printf( __( 'Logged in as <a href="%s/wp-admin/profile.php">%s</a>.', 'pojo' ), get_option( 'siteurl' ), $user_identity ); ?>
						<a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="<?php __( 'Log out of this account', 'pojo' ); ?>"><?php _e( 'Log out &raquo;', 'pojo' ); ?></a>
					</p>

				<?php else : ?>
					<div class="col-md-4">
						<input class="field" type="text" class="text" placeholder="<?php _e( 'Name', 'pojo' ); if ( $req ) _e( ' (required)', 'pojo' ); ?>" name="author" id="author" value="<?php echo esc_attr( $comment_author ); ?>" <?php if ( $req ) echo "aria-required='true'"; ?> />
					</div>
					<div class="col-md-4">
						<input class="field" type="email" class="text" placeholder="<?php _e( 'Email', 'pojo' ); if ( $req ) _e( ' (required)', 'pojo' ); ?>" name="email" id="email" value="<?php echo esc_attr( $comment_author_email ); ?>" <?php if ( $req ) echo "aria-required='true'"; ?> />
					</div>
					<div class="col-md-4">
						<input class="field" type="url" class="text" placeholder="<?php _e( 'Website', 'pojo' ); ?>" name="url" id="url" value="<?php echo esc_attr( $comment_author_url ); ?>" />
					</div>
				<?php endif; ?>
				<div class="clearfix"></div>

				<div class="col-md-12">
					<textarea id="comment" class="field" name="comment" placeholder="<?php _e( 'Enter your comment', 'pojo' ); ?>" cols="10" rows="10"></textarea>
					<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e( 'Send', 'pojo' ); ?>" class="button" />
				</div>

				<?php comment_id_fields(); ?>
				<?php do_action( 'comment_form', $post->ID ); ?>
			</form>

		<?php endif; // if registration required and not logged in ?>
	</section><!-- /#respond -->
<?php endif; ?>
