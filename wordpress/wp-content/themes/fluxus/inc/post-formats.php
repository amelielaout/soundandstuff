<?php
/**
 * Every thing related to post formats.
 *
 * @since fluxus 1.0
 */

require_once dirname(__FILE__) . '/post-formats/quote.php';
require_once dirname(__FILE__) . '/post-formats/video.php';
require_once dirname(__FILE__) . '/post-formats/link.php';

/**
 * Add support for different post formats.
 */
function fluxus_post_format_setup() {

    add_theme_support( 'post-formats', array(
        'aside',
        'link',
        'quote',
        'video'
    ));

}
add_action( 'after_setup_theme', 'fluxus_post_format_setup' );


/**
 * Add scripts and styles.
 */
function fluxus_post_format_enqueue( $hook ) {

    if ( ( 'post.php' == $hook ) || ( 'post-new.php' == $hook ) ) {

        wp_enqueue_style( 'fluxus-wp-admin-post-formats', get_template_directory_uri() . '/css/wp-admin/post-formats.css' );
        wp_enqueue_script( 'fluxus-wp-admin-post-formats', get_template_directory_uri() . '/js/wp-admin/post-formats.js' );

    }

}
add_action( 'admin_enqueue_scripts', 'fluxus_post_format_enqueue', 1000 );

