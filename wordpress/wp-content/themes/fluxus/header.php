<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package fluxus
 * @since fluxus 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); fluxus_html_classes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '&mdash;', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " &mdash; $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' / ' . sprintf( __( 'Page %s', 'fluxus' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link href="http://fonts.googleapis.com/css?family=Merriweather" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php

	wp_head();

?>
</head>

<body <?php body_class(); ?>>

<div id="page-wrapper">

	<header id="header" class="clearfix">
		<hgroup>
			<h1 class="site-title">
				<a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php

					$logo = fluxus_get_logo();
					$logo_retina = fluxus_get_logo_retina();

					// If both logo versions present
					if ( $logo && $logo_retina ) : ?>

						<img class="logo" src="<?php echo esc_url( $logo[0] ); ?>" alt=""<?php echo $logo['size']; ?> />
						<img class="logo-retina" src="<?php echo esc_url( $logo_retina[0] ); ?>" alt=""<?php echo $logo_retina['size']; ?> /><?php

					// If no retina version present, then use normal logo in both cases.
					elseif ( $logo ) : ?>

						<img class="logo logo-no-retina" src="<?php echo esc_url( $logo[0] ); ?>" alt=""<?php echo $logo['size']; ?> /><?php

					else: // If no logo is set, then show the default one. ?>
						<span class="default-logo"></span><?php
					endif; ?>
				</a>
			</h1>
			<?php
				// get the text under the logo
				if ( $site_description ) : ?>
					<h2 class="site-description"><?php echo $site_description; ?></h2><?php
				endif;
			?>
		</hgroup>

		<div class="site-navigation" data-menu="<?php _e( 'Menu', 'fluxus' ); ?>">
			<?php
				// show menus only if they have been assigned
				if ( has_nav_menu( 'header_primary' ) ) : ?>
					<nav class="primary-navigation"><?php
						wp_nav_menu( array( 'theme_location' => 'header_primary', 'walker' => new Intheme_Menu_Walker() ) ); ?>
					</nav><?php
				endif;

				if ( has_nav_menu( 'header_secondary' ) ) : ?>
					<nav class="secondary-navigation"><?php
						wp_nav_menu( array( 'theme_location' => 'header_secondary', 'walker' => new Intheme_Menu_Walker() ) ); ?>
					</nav><?php
				endif;
			?>
		</div>
	</header>
	<?php

		do_action( 'before' );

