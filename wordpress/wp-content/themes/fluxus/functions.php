<?php
/**
 * fluxus functions and definitions
 *
 * @package fluxus
 * @since fluxus 1.0
 */

/**
 * Define common constants.
 */
define( 'FLUXUS_IMAGES_URI',  get_template_directory_uri() . '/images' );
define( 'FLUXUS_LIB_DIR', 	  dirname(__FILE__) . '/lib' );
define( 'FLUXUS_INC_DIR', 	  dirname(__FILE__) . '/inc' );
define( 'FLUXUS_CSS_DIR', 	  dirname(__FILE__) . '/css' );
define( 'FLUXUS_JS_DIR', 	  dirname(__FILE__) . '/js' );
define( 'FLUXUS_WIDGETS_DIR', FLUXUS_INC_DIR . '/widgets' );

/**
 * Require various files.
 */
require_once FLUXUS_LIB_DIR . '/intheme-utils.php';				// File contains useful functions for general tasks.
require_once FLUXUS_LIB_DIR . '/appreciate.php';				// Appreciate Post functionality.
require_once FLUXUS_INC_DIR . '/fluxus-theme.php';
require_once FLUXUS_INC_DIR . '/slider.php';					// Full page slider functionality.
require_once FLUXUS_INC_DIR . '/portfolio/portfolio.php';		// Portfolio functionality.
require_once FLUXUS_INC_DIR . '/contacts.php';					// Contacts page functionality.
require_once FLUXUS_INC_DIR . '/background.php';				// Page with background image functionality.
require_once FLUXUS_INC_DIR . '/template-tags.php';				// Custom template tags for this theme.
require_once FLUXUS_INC_DIR . '/tweaks.php';					// Various functionality tweaks.
require_once FLUXUS_INC_DIR . '/shortcodes.php';				// Shortcodes.
require_once FLUXUS_INC_DIR . '/post-formats.php';				// Post formats.
require_once FLUXUS_INC_DIR . '/widgets.php';					// Widgets.


/**
 * Initialize Fluxus Theme.
 */
function fluxus_init() {

	/**
	 * Add custom image sizes.
	 */
	add_image_size( 'fluxus-thumbnail', 583, 328, true ); 			// used in blog index page
	add_image_size( 'fluxus-gallery-thumbnail', 500, 500, true );	// used in content gallery

	/**
	 * Maximum image size displayed on site.
	 * Used on: full page slider, portfolio, etc.
	 */
	add_image_size( 'fluxus-max', 1920, 1280, false );				// another good option 1500x1000

	/**
	 * Note, if you are changing the existing size dimensions,
	 * then Wordpress will not automatically regenerate all the images.
	 *
	 * To do so, you could try using it_regenerate_wp_images() function.
	 * Put it inside your admin_init hook, and visit admin section.
	 * After waiting for usually a long time, all the images will be
	 * available in a newly specified size.
	 */

	/**
	 * Remove admin bar for everyone
	 */
	add_filter( 'show_admin_bar' , '__return_false');

}
add_action( 'init', 'fluxus_init', 1 );


/**
 * Initialize admin side.
 */
function fluxus_admin_init() {

	/**
	 * General scripts and styles for admin area.
	 */
    wp_enqueue_script( 'fluxus-wp-admin', get_template_directory_uri() . '/js/wp-admin/admin.js' );
    wp_enqueue_style( 'fluxus-wp-admin', get_template_directory_uri() . '/css/wp-admin/admin.css' );

    add_editor_style( 'css/wp-admin/editor-styles.css' );

}
add_action( 'admin_init', 'fluxus_admin_init', 1 );


/**
 * Specify the maximum content width.
 * This is based on CSS, when screen becomes big enough the content
 * area becomes fixed, so there is no need to have bigger images.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1021;
}


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since fluxus 1.0
 */
function fluxus_setup() {

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 */
	load_theme_textdomain( 'fluxus', get_template_directory() . '/languages' );

	/**
	 * Enable theme support for standard features.
	 */
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Register menus.
	 */
	register_nav_menus( array(
		'header_primary' 	=> __( 'Header Primary Menu', 'fluxus' ),
		'header_secondary' 	=> __( 'Header Secondary Menu', 'fluxus' ),
		'footer_primary' 	=> __( 'Footer Primary Menu', 'fluxus' )
	) );


	/**
	 * Enable shortcodes for widgets.
	 */
	add_filter( 'widget_text', 'do_shortcode' );


	/**
	 * Initialize theme options.
	 */
	require_once FLUXUS_INC_DIR . '/options.php';

	if ( !function_exists( 'optionsframework_init' ) ) {
		define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/options-framework/' );
		require_once dirname(__FILE__) . '/options-framework/options-framework.php';
	}

}
add_action( 'after_setup_theme', 'fluxus_setup' );


/**
 * Enqueue scripts and styles.
 */
function fluxus_scripts_and_styles() {

	/**
	 * CSS
	 */
	wp_enqueue_style( 'global', 	 	   get_template_directory_uri() . '/css/global.css' );		// global CSS, should consist of tags only
    wp_enqueue_style( 'fluxus-grid', 	   get_template_directory_uri() . '/css/grid.css' );		// fluid grid used in content columns
    wp_enqueue_style( 'fontello-icons',    get_template_directory_uri() . '/css/fontello.css' );	// font icon containing Entypo collection, add more icons at fontello.com
	wp_enqueue_style( 'style', 		 	   get_stylesheet_uri() );									// main stylesheet
    wp_enqueue_style( 'fluxus-responsive', get_template_directory_uri() . '/css/responsive.css' );  // responsive rules

    if ( file_exists( FLUXUS_CSS_DIR . '/user.css' ) ) {
    	wp_enqueue_style( 'user',		   get_template_directory_uri() . '/css/user.css' );  		// user custom rules
    }

	/**
	 * JS
	 */
	wp_enqueue_script( 'tinyscrollbar', 	get_template_directory_uri() . '/js/jquery.tinyscrollbar.js',    array( 'jquery' ), false, true );	// scrollbar plugin
	wp_enqueue_script( 'sharrre', 			get_template_directory_uri() . '/js/jquery.sharrre-1.3.4.js',    array( 'jquery' ), false, true );	// share count plugin
	wp_enqueue_script( 'jquery-transit',	get_template_directory_uri() . '/js/jquery.transit.js', 	     array( 'jquery' ), false, true );	// css3 transition plugin
	wp_enqueue_script( 'fluxus-utils', 		get_template_directory_uri() . '/js/utils.js', 				     array( 'jquery' ), false, true ); // other tiny plugins
	wp_enqueue_script( 'fluxus-size', 		get_template_directory_uri() . '/js/size.js', 				     array( 'jquery', 'fluxus-utils' ), false, true );	// file containing size adjustmens for DOM elements on window.resize event
	wp_enqueue_script( 'fluxus-grid', 		get_template_directory_uri() . '/js/jquery.fluxus-grid.js',      array( 'jquery' ), false, true );	// grid portfolio layout plugin
	wp_enqueue_script( 'jquery-reveal',		get_template_directory_uri() . '/js/jquery.reveal.js', 		     array( 'jquery' ), false, true ); // modal box plugin
	wp_enqueue_script( 'fluxus-lightbox',	get_template_directory_uri() . '/js/jquery.fluxus-lightbox.js',  array( 'jquery', 'jquery-transit' ), false, true ); // lightbox plugin
	wp_enqueue_script( 'iscroll',  			get_template_directory_uri() . '/js/iscroll.js', 	 			 array(), false, true ); // iScroll 4
	wp_enqueue_script( 'fluxus-slider',  	get_template_directory_uri() . '/js/jquery.fluxus-slider.js', 	 array( 'jquery', 'jquery-transit', 'iscroll' ), false, true ); // full page slider plugin
	wp_enqueue_script( 'fluxus', 			get_template_directory_uri() . '/js/main.js', 					 array( 'jquery', 'fluxus-utils' ), false, true ); // main script

    if ( file_exists( FLUXUS_JS_DIR . '/user.js' ) ) {
		wp_enqueue_script( 'fluxus-user',	get_template_directory_uri() . '/js/user.js', 					 array( 'jquery', 'fluxus-utils' ), false, true ); // user custom javascript
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );	// WP standard comment reply script
	}

}
add_action( 'wp_enqueue_scripts', 'fluxus_scripts_and_styles' );


/**
 * Modify native menu walker class with some extra functionality.
 */
class Intheme_Menu_Walker extends Walker_Nav_Menu {

	function is_item_portfolio_index( $object_id ) {

		return it_is_template( $object_id, 'template-portfolio.php' ) ||
			   it_is_template( $object_id, 'template-portfolio-grid.php' );

	}

	function has_current( $elements ) {

		foreach ( $elements as $element ) {

			if ( $element->current ) {
				return true;
			}

		}

		return false;

	}

	function walk( $elements, $max_depth, $args ) {

		global $post;

		// We are on a project page
		if ( $post && $post->post_type == 'fluxus_portfolio' && is_single() ) {

			// If there's a current element, then let's not do anything
			$found = $this->has_current( $elements );

			if ( ! $found ) {

				foreach ( $elements as $key => $element ) {

					// Search only root items first
					if ( 0 == $element->menu_item_parent ) {

						// If our current menu item is a Page with Portfolio Template
						if ( $this->is_item_portfolio_index( $element->object_id ) ) {
							$elements[$key]->classes[] = 'active';
							$found = true;
							break;
						}

					}

				}

				// We were unable to find Portfolio on the root, then activate any item that has
				// template-portfolio.php or template-portfolio-grid.php
				if ( ! $found ) {

					foreach ( $elements as $key => $element ) {

						if ( $this->is_item_portfolio_index( $element->object_id ) ) {
							$elements[$key]->classes[] = 'active';
							break;
						}

					}

				}

			}

		}

		return parent::walk( $elements, $max_depth, $args );

	}

	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element ) {
			return;
		}

		$theme_location = isset( $args[0] ) && isset( $args[0]->theme_location ) ? $args[0]->theme_location : '';

		global $post;

		/**
		 * Adjust menu if our current post_type is fluxus_portfolio
		 */
		if ( $post && $post->post_type == 'fluxus_portfolio' ) {

			// If our current menu item is a Page with Portfolio Template
			if ( $this->is_item_portfolio_index( $element->object_id ) ) {

				if ( isset( $children_elements[$element->ID] ) ) {

					// Check if this Portfolio menu item has children that are
					// currently active Project Type terms. This also means
					// that our current page is Project Type archive.

					foreach ( $children_elements[$element->ID] as $child ) {
						if ( in_array( 'current-fluxus-project-type-ancestor', $child->classes ) ) {
							$element->classes[] = 'active';
						}
					}

				}


			}

		}

		$id_field = $this->db_fields['id'];

		/**
		 * Adds the "has-children" class to the current item if it has children.
		 */
		if ( ! empty( $children_elements[$element->$id_field] ) ) {
			array_push( $element->classes, 'has-children' );
		}

		/**
		 * That's it, now call the default function to do the rest.
		 */
		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

}

if ( is_admin() ) {
	require_once FLUXUS_INC_DIR . '/portfolio/upgrade.php';
}

include_once FLUXUS_INC_DIR . '/user.php';		// User modifications.