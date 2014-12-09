<?php
/**
 * Widgets. Contains functionality related to theme's widgets.
 *
 * @since fluxus 1.0
 */


/**
 * Include custom widgets.
 */
require_once FLUXUS_WIDGETS_DIR . '/widget-project-types.php';


/**
 * Returns theme's default widget params.
 *
 * @since fluxus 1.0
 */
function fluxus_get_default_widget_params() {
    return array(
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<div class="decoration"></div><h1 class="widget-title">',
            'after_title' => '</h1>'
        );
}


/**
 * Register widgetized area and update sidebar with default widgets.
 *
 * @since fluxus 1.0
 */
function fluxus_widgets_init() {

    /**
     * Register sidebars.
     */

    $sidebar_main = array_merge(
            array( 'name' => __( 'General Sidebar', 'fluxus' ), 'id' => 'sidebar-main' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_main );

    $sidebar_post = array_merge(
            array( 'name' => __( 'Blog Sidebar', 'fluxus' ), 'id' => 'sidebar-blog' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_post );


    $sidebar_post = array_merge(
            array( 'name' => __( 'Blog Post Sidebar', 'fluxus' ), 'id' => 'sidebar-post' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_post );


    $sidebar_portfolio = array_merge(
            array( 'name' => __( 'Portfolio Sidebar', 'fluxus' ), 'id' => 'sidebar-portfolio' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_portfolio );


    $sidebar_portfolio_single = array_merge(
            array( 'name' => __( 'Portfolio Project Sidebar', 'fluxus' ), 'id' => 'sidebar-portfolio-single' ),
            fluxus_get_default_widget_params()
        );
    register_sidebar( $sidebar_portfolio_single );


    // Removes the default styles that are packaged with the Recent Comments widget.
    add_filter( 'show_recent_comments_widget_style', '__return_false' );

}
add_action( 'widgets_init', 'fluxus_widgets_init' );