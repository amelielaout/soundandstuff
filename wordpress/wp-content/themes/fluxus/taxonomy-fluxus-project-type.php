<?php

/**
 * Display projects filtered by type.
 */

global $wp_query;

/**
 * Use Grid Layout only if Horizontal Portfolio does not exist and Grid Portfolio does,
 * otherwise use Horizontal Portfolio.
 */
$template = 'template-portfolio.php';

$horizontal_portfolio = it_find_page_by_template( 'template-portfolio.php', array( 'post_status' => 'publish' ) );

if ( $horizontal_portfolio ) {
    $template = 'template-portfolio.php';
} else {
    $grid_portfolio = it_find_page_by_template( 'template-portfolio-grid.php', array( 'post_status' => 'publish' ) );

    if ( $grid_portfolio ) {
        $template = 'template-portfolio-grid.php';
    }
}

$wp_query->set( 'posts_per_page', 3000 );
$wp_query->query( $wp_query->query_vars );

require_once dirname( __FILE__ ) . '/' . $template;