<?php
/**
 * File contains funcionality used for Portfolio.
 *
 * @since fluxus 1.0
 */

// Load framework classes
require_once dirname( __FILE__ ) . '/class-fluxus-page.php';
require_once dirname( __FILE__ ) . '/class-fluxus-admin-page.php';

require_once dirname( __FILE__ ) . '/class-portfolio-project.php';      // Project
require_once dirname( __FILE__ ) . '/class-portfolio-media.php';        // Project Media
require_once dirname( __FILE__ ) . '/class-grid-portfolio.php';         // Grid Portfolio

// WP Admin Pages
require_once dirname( __FILE__ ) . '/class-portfolio-project-admin.php';    // Project Admin
require_once dirname( __FILE__ ) . '/class-grid-portfolio-admin.php';       // Grid Portfolio Admin


/**
 * Initialize Portolio
 */
function fluxus_portfolio_init() {

    add_image_size( 'fluxus-portfolio-thumbnail', 90, 90, true );

    $portfolio_base = fluxus_portfolio_base_slug();

    /**
     * Cache $portfolio_base, if it has changed, then we need to flush rules.
     */
    $flush = false;
    $cached_portfolio_base = get_transient( 'fluxus_portfolio_slug' );

    if ( $cached_portfolio_base ) {
        if ( $portfolio_base != $cached_portfolio_base ) {
            $flush = true;
        }
    } else {
        $flush = true;
    }


    /**
     * First we register taxonomy, then custom post type.
     * The order is important, because of rewrite rules.
     */
    $args = array(
                    'label' => 'Project Types',
                    'singular_label' => 'Project Type',
                    'query_var' => true,
                    'show_in_nav_menus' => true,
                    'show_ui' => true,
                    'show_tagcloud' => false,
                    'hierarchical' => true,
                    'rewrite' => array(
                            'slug' => $portfolio_base
                        )
                );
    register_taxonomy( 'fluxus-project-type', 'fluxus_portfolio',  $args );


    /**
     * Register portfolio_project custom post type.
     */
    $args = array(
        'label' => __(' Portfolio', 'fluxus' ),
        'singular_label' => __( 'Project', 'fluxus' ),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'page',
        'hierarchical' => false,
        'rewrite' => false,
        'query_var' => true,
        'taxonomy' => 'fluxus-project-type',
        'has_archive' => true,
        'menu_icon' => get_template_directory_uri() . '/images/wp-admin/portfolio.png',
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' )
       );
    register_post_type( 'fluxus_portfolio' , $args );


    /**
     * Register portfolio Project Media File
     */
    $args = array(
        'label' => __(' Project Media', 'fluxus' ),
        'singular_label' => __( 'Project Media File', 'fluxus' ),
        'public' => false,
        'supports' => array( 'title' )
       );

    register_post_type( 'fluxus_portfolio_project_media' , $args );

    $portfolio_structure = '/' . $portfolio_base . '/%projecttype%/%fluxus_portfolio%';
    add_rewrite_tag( '%projecttype%', '([^&/]+)', 'fluxus_project_type=' );
    add_rewrite_tag( '%fluxus_portfolio%', '([^&/]+)', 'fluxus_portfolio=' );
    add_permastruct( 'fluxus_portfolio', $portfolio_structure, false );

    if ( $flush || true ) {
        it_flush_rewrite_rules();
        set_transient( 'fluxus_portfolio_slug', $portfolio_base, 60 * 60 * 24 );
    }

}
add_action( 'init', 'fluxus_portfolio_init', 1 );


/**
 * Initialize Portfolio Admin
 */
function fluxus_portfolio_admin_init() {

    global $pagenow;

    $post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';

    if ( $post_id = it_get_post_id() ) {
        $post = get_post( $post_id );
        $post_type = $post->post_type;
    }

    if ( $post_type == 'fluxus_portfolio' ) {

        // Project List Page
        if ( 'edit.php' == $pagenow ) {

            // Set correct order of projects in admin.
            add_filter( 'pre_get_posts', 'fluxus_portfolio_admin_project_order' );

            // Custom columns in Project List
            add_filter( 'manage_edit-fluxus_portfolio_columns', 'fluxus_portfolio_admin_project_list_columns' );
            add_action( 'manage_posts_custom_column', 'fluxus_portfolio_project_list_column_data' );

        }

        // Post Edit or Post New Page
        if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'admin-ajax.php' ) ) ) {
            new PortfolioProjectAdmin( $post_id );
        }

    }

    if ( $post_id ) {

        if ( it_is_template( $post_id, 'template-portfolio-grid.php' ) ) {
            new GridPortfolioAdmin( $post_id );
        }

    }


}
add_action( 'admin_init', 'fluxus_portfolio_admin_init' );


/**
 * Set correct order of projects in admin.
 */
function fluxus_portfolio_admin_project_order( $wp_query ) {
    if ( $wp_query->query['post_type'] == 'fluxus_portfolio' ) {
        $wp_query->set( 'orderby', 'menu_order ID' );
        $wp_query->set( 'order', 'ASC DESC' );
    }
}


/**
 * Add additional columns in project list table.
 */
function fluxus_portfolio_admin_project_list_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => 'Project',
        'description' => 'Description',
        'link' => 'Link',
        'type' => 'Type of Project',
    );

    return $columns;

}


/**
 * Populate added columns with data.
 */
function fluxus_portfolio_project_list_column_data( $column ) {
    global $post;

    $project = new PortfolioProject( $post->ID );

    switch ( $column ) {
        case 'description':
            the_excerpt();
        break;

        case 'link':
            echo $project->meta_link;
        break;

        case 'type':
            echo get_the_term_list( $post->ID, 'fluxus-project-type', '', ', ', '' );
        break;
    }

}


/**
 * Query portfolio items.
 */
function fluxus_query_portfolio( $args = array() ) {

    add_filter( 'posts_orderby_request', 'fluxus_portfolio_orderby_filter' );

    $defaults = array(
            'post_type'          => 'fluxus_portfolio',
            'posts_per_page'     => -1,
            'orderby'            => 'menu_order ID',
            'post_status'        => 'publish',
            'order'              => 'ASC DESC'
        );

    $args = array_merge( $defaults, $args );

    $result = query_posts( $args );

    remove_filter( 'posts_orderby_request', 'fluxus_portfolio_orderby_filter' );

    return $result;

}


/**
 * Orders project by menu_order ASC and ID desc.
 */
function fluxus_portfolio_orderby_filter( $orderby ) {
    /**
     * Limit the use for a very specific case.
     */
    if ( 'wp_posts.menu_order,wp_posts.ID DESC' == $orderby ) {
        return 'wp_posts.menu_order ASC, wp_posts.ID DESC';
    }

    return $orderby;

}


/**
 * Returns next project according to the specified order.
 */
function fluxus_portfolio_get_next_project( $current_project ) {
    return fluxus_portfolio_get_adjacent_project( $current_project, 'next' );
}


/**
 * Returns previous project according to the specified order.
 */
function fluxus_portfolio_get_previous_project( $current_project ) {
    return fluxus_portfolio_get_adjacent_project( $current_project, 'previous' );
}


/**
 * Get next/previous project while ordering by menu_order DESC and id DESC.
 * That is newer items with same menu_order goes first.
 */
function fluxus_portfolio_get_adjacent_project( $current_project, $sibling = 'next' ) {
    global $wpdb;

    if ( !is_object($current_project) ) {
        return false;
    }

    $compare_id = 'next' === $sibling ? '<' : '>';

    /**
     * Select next post with the same menu_order but lower ID.
     */
    $where = $wpdb->prepare("WHERE
                                p.id $compare_id %d AND
                                p.menu_order = %d AND
                                p.post_type = 'fluxus_portfolio' AND
                                p.post_status = 'publish'",
                            $current_project->ID, $current_project->menu_order );

    if ( 'next' === $sibling ) {
        $sort  = "ORDER BY p.id DESC LIMIT 1";
    } else {
        $sort  = "ORDER BY p.id ASC LIMIT 1";
    }

    $query = "SELECT p.* FROM $wpdb->posts AS p $where $sort";

    $result = $wpdb->get_row( $query );

    if ( null === $result ) {

        /**
         * No project with the same menu order found. Now select
         * a project with a lower menu order.
         */

        if ( 'next' === $sibling ) {
            $sort  = "ORDER BY p.menu_order ASC, p.id DESC LIMIT 1";
            $compare_menu_order = '>';
        } else {
            $sort  = "ORDER BY p.menu_order DESC, p.id ASC LIMIT 1";
            $compare_menu_order = '<';
        }

        $where = $wpdb->prepare("WHERE
                                p.menu_order $compare_menu_order %d AND
                                p.post_type = 'fluxus_portfolio' AND
                                p.post_status = 'publish'",
                            $current_project->menu_order );

        $query = "SELECT p.* FROM $wpdb->posts AS p $where $sort";

        $result = $wpdb->get_row( $query );

    }

    return $result;

}


/**
 * Looks for template-portfolio.php or template-portfolio-grid.php page id.
 */
function fluxus_portfolio_base_id() {

    $portfolio_page = it_find_page_by_template( 'template-portfolio.php' );
    if ( $portfolio_page ) {
        return $portfolio_page[0]->ID;
    } else {
        $portfolio_page = it_find_page_by_template( 'template-portfolio-grid.php' );
        if ( $portfolio_page ) {
            return $portfolio_page[0]->ID;
        } else {
            return 0;
        }
    }

}


/**
 * Return template-portfolio.php or template-portfolio-grid.php slug to be used
 * in portfolio project URL.
 */
function fluxus_portfolio_base_slug() {

    $portfolio_page = it_find_page_by_template( 'template-portfolio.php' );
    if ( $portfolio_page ) {
        return $portfolio_page[0]->post_name;
    } else {
        return 'portfolio';
    }

}


/**
 * Generate correct links using fluxus_portfolio_base_slug().
 */
function fluxus_portfolio_permalink( $permalink, $post, $leavename ) {

    /**
     * If there's an error with post, or this is not fluxus_portfolio
     * or we are not using fancy links.
     */
    if ( is_wp_error( $post ) || 'fluxus_portfolio' != $post->post_type || empty( $permalink ) ) {
        return $permalink;
    }

    /**
     * Find out project type.
     */
    $project_type = '';

    if ( strpos( $permalink, '%projecttype%') !== false ) {

        $terms = get_the_terms( $post->ID, 'fluxus-project-type' );

        if ( $terms ) {
            // sort terms by ID.
            usort( $terms, '_usort_terms_by_ID' );
            $project_type = $terms[0]->slug;
        } else {
            $project_type = 'uncategorized';
        }

    }

    $rewrite_codes = array(
            '%projecttype%',
            $leavename ? '' : '%fluxus_portfolio%'
        );

    $rewrite_replace = array(
            $project_type,
            $post->post_name
        );

    $permalink = str_replace( $rewrite_codes, $rewrite_replace, $permalink );

    return $permalink;

}
add_filter( 'post_type_link', 'fluxus_portfolio_permalink' , 10, 3 );

