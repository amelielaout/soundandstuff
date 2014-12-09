<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @since fluxus 1.0
 */


/**
 * Set excerpt length.
 */
function fluxus_excerpt_lenght( $length ) {
    return 50;
}
add_filter( 'excerpt_length', 'fluxus_excerpt_lenght', 1000 );


/**
 * Used to increase excerpt length on certain post types.
 */
function fluxus_increased_excerpt_lenght( $length ) {
    return 100;
}


function fluxus_continue_reading_link() {
    return '<div class="wrap-excerpt-more"><a class="excerpt-more" href="' . esc_url( get_permalink() ) . '">' . __( 'Continue reading', 'fluxus' ) . '</a></div>';
}


function fluxus_auto_excerpt_more( $more ) {
    return ' &hellip;' . fluxus_continue_reading_link();
}
add_filter( 'excerpt_more', 'fluxus_auto_excerpt_more' );


function fluxus_get_the_excerpt( $content ) {

    global $post;

    if ( has_excerpt() && ! is_attachment() && ( $post->post_type != 'fluxus_portfolio' ) ) {
        $content .= fluxus_continue_reading_link();
    }

    return $content;

}
add_filter( 'get_the_excerpt', 'fluxus_get_the_excerpt' );


/**
 * Modifies default [wp_caption] shortcode to remove
 * style="width: width+10" from output.
 */
function fluxus_image_caption( $foo, $attr, $content = null ) {

    extract(shortcode_atts(array(
        'id'    => '',
        'align' => 'alignnone',
        'width' => '',
        'caption' => ''
    ), $attr));

    if ( 1 > (int) $width || empty($caption) )
        return $content;

    if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

    return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '">'
    . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';

}
add_filter( 'img_caption_shortcode', 'fluxus_image_caption', 1, 3 );


function fluxus_add_image_link_class( $content ) {

    // find all links to images
    if ( preg_match_all( '/<a.*? href="(.*?)\.(png|jpg|jpeg|gif)">/i', $content, $matches ) ) {

        foreach ( $matches[0] as $match ) {

            if ( preg_match( '/class=".*?"/i', $match ) ) {
                $replacement = preg_replace( '/(<a.*? class=".*?)(".*?>)/', '$1 link-to-image$2', $match );
            } else {
                $replacement = preg_replace( '/(<a.*?)>/', '$1 class="link-to-image">', $match );
            }

            // replace them using links with classes
            $content = str_replace( $match, $replacement, $content );

        }

    }

    return $content;

}
add_filter( 'the_content', 'fluxus_add_image_link_class' );


/**
 * Filters that adds custom classes to comments paging navigation.
 */
function fluxus_comment_previous_page() {
    return ' class="button-minimal button-icon-right icon-right-open-big" ';
}
add_filter( 'previous_comments_link_attributes', 'fluxus_comment_previous_page' );


function fluxus_comment_next_page() {
    return ' class="button-minimal button-icon-left icon-left-open-big" ';
}
add_filter( 'next_comments_link_attributes', 'fluxus_comment_next_page' );


/**
 * Filters that adds a wrapping <span class="count" /> around item count in widgets.
 * Used for styling purposes.
 */
function fluxus_wp_list_categories_filter( $output ) {
    return preg_replace( '/\<\/a\>\s+?\((\d+)\)/', '</a><span class="count">[$1]</span>', $output );
}
add_filter( 'wp_list_categories', 'fluxus_wp_list_categories_filter' );


function fluxus_get_archives_link_filter( $output ) {
    return preg_replace( '/\<\/a\>(&nbsp;)?(\s+)?\((\d+)\)/', '</a><span class="count">[$3]</span>', $output );
}
add_filter( 'get_archives_link', 'fluxus_get_archives_link_filter' );


function fluxus_wp_list_bookmarks_filter( $output ) {
    return preg_replace( '/\<\/a\>(&nbsp;)?(\s+)?(\d+)/', '</a><span class="count">[$3]</span>', $output );
}
add_filter( 'wp_list_bookmarks', 'fluxus_wp_list_bookmarks_filter' );


