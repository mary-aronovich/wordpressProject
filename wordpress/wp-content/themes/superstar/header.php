<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$logo_img = get_theme_mod( 'image_logo' ); // Getting from option your choice.
$mobile_logo_img = get_theme_mod( 'image_header_logo_mobile' );

if ( empty( $mobile_logo_img ) )
	$mobile_logo_img = $logo_img;


?><!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="container">
	<?php po_change_loop_to_parent( 'change' ); ?>

	<?php if ( ! pojo_is_blank_page() && ! pojo_elementor_theme_do_location( 'header' ) ) : ?>
		<div id="header">
			<div class="container">
				<header id="logo" role="banner">
					<?php if ( ! empty( $logo_img ) ) : ?>
					<div class="logo-img">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<img src="<?php echo $logo_img; ?>" alt="<?php bloginfo( 'name' ); ?>" class="pojo-hidden-phone" />
							<img src="<?php echo esc_attr( $mobile_logo_img ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="pojo-visible-phone" />
						</a>
					</div>
					<?php else : ?>
					<div class="logo-text">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					</div>
					<?php endif; ?>
					
					<?php if ( pojo_has_nav_menu( 'primary' ) ) : ?>
					<button type="button" class="navbar-toggle visible-xs" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only"><?php _e( 'Toggle navigation', 'pojo' ); ?></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<?php endif; ?>
				</header>

				<nav id="nav-main" class="row">
					<div class="navbar-collapse collapse" role="navigation" >
						<?php if ( has_nav_menu( 'primary' ) ) : ?>
							<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'sf-menu hidden-xs', 'walker' => new Pojo_Navbar_Nav_Walker() ) );
							wp_nav_menu( array( 'theme_location' => has_nav_menu( 'primary_mobile' ) ? 'primary_mobile' : 'primary', 'container' => false, 'menu_class' => 'mobile-menu visible-xs', 'walker' => new Pojo_Navbar_Nav_Walker() ) ); ?>
						<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
							<mark class="menu-no-found"><?php printf( __( 'Please setup Menu <a href="%s">here</a>', 'pojo' ), admin_url( 'nav-menus.php?action=locations' ) ); ?></mark>
						<?php endif; ?>
					</div>
				</nav><!-- /#nav-menu -->

				<footer id="footer" class="row hidden-xs" role="contentinfo">

					<?php get_sidebar( 'footer' ); ?>

					<div id="copyright" class="<?php echo WRAP_CLASSES; ?>">
						<div class="footer-text-left">
							<?php echo nl2br( pojo_get_option( 'txt_copyright_left' ) ); ?>
						</div>
						<div class="footer-text-right">
							<?php echo nl2br( pojo_get_option( 'txt_copyright_right' ) ); ?>
						</div>
					</div>
				</footer>
			</div><!-- /.container -->
		</div><!-- /#header -->
	<?php endif; // end blank page ?>

	<div id="primary">
		<?php po_change_loop_to_parent(); ?>
		<?php pojo_print_titlebar(); ?>
		<div class="<?php echo WRAP_CLASSES; ?>">
			<div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
