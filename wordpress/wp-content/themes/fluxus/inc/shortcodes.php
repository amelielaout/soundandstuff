<?php
/**
 * Shortcodes.
 *
 * @since fluxus 1.0
 */

/**
 * Removes everything that is not in between [$code]...[/$code]
 */
function fluxus_fix_shortcode_content( $content, $code ) {

    if ( is_array( $code ) ) {
        // don't preg-escape if it is an array
        $code = $code[0];
    } else {
        $code = preg_quote( $code );
    }

    /* Matches the contents and the open and closing tags */
    $pattern_full = '{(\[' . $code . '.*?\].*?\[/' . $code . '\])}is';

    /* Matches just the contents */
    $pattern_contents = '{\[' . $code . '.*?\](.*?)\[/' . $code . '\]}is';

    /* Divide content into pieces */
    $pieces = preg_split( $pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE );
    $new_content = '';

    /* Loop over pieces and look if there are pieces that do not contain $code */
    foreach ( $pieces as $piece ) {

        // if is wrapped in shortcode, then include it.
        if ( preg_match($pattern_contents, $piece, $matches) ) {
            $new_content .= $piece;
        }
    }

    return $new_content;

}

/**
 * Removes </p> in the front of the content,
 * and <p> in the end of the content.
 */
function fluxus_remove_broken_tags( $content ) {

    // removes </p> at the front of the content
    $content = preg_replace( '/^\s*\<\/p\>/is', '', $content );

    // removes <p> at the end of the content
    $content = preg_replace( '/\<p\>\s*$/is', '', $content );

    return $content;

}


/**
 * Social shortcode.
 */
function fluxus_shortcode_social( $args = null, $content = null ) {

    if ( isset( $args['icon'] ) && ! empty( $args['icon'] ) ) {
        $icon = $args['icon'];
    } else {
        return '';
    }

    $url = isset( $args['url'] ) && ! empty( $args['url'] ) ? esc_url( $args['url'] ) : '#';

    $html = '<a target="_blank" href="' . $url . '" class="icon-social icon-' . $icon . '"></a>';

    return $html;

}
add_shortcode( 'social', 'fluxus_shortcode_social' );


/**
 * Services shortcode.
 */
function fluxus_shortcode_service( $args = null, $content = null, $code = null ) {

    global $fluxus_shortcode_service_count;
    global $fluxus_shortcode_service_index;
    global $fluxus_shortcode_service_columns;

    $column_to_span = array(
            2 => 'span6',
            3 => 'span4',
            4 => 'span3',
            5 => 'span2'
        );

    $content = fluxus_remove_broken_tags( $content );

    $html = '';

    if ( 'service' == $code ) {

        $columns = $fluxus_shortcode_service_columns;

        $fluxus_shortcode_service_index++;

        if ( $fluxus_shortcode_service_count > $columns ) {

            if ( ($fluxus_shortcode_service_index > $columns) && ($fluxus_shortcode_service_index % $columns == 1) ) {
                // start new row
                $html .= '</div><div class="services row-fluid">';
            }

        }

        $span = '<div class="' . $column_to_span[$fluxus_shortcode_service_columns] . '">';

        $html .= $span . '<div class="service">';

        if ( isset($args['title']) && !empty($args['title']) ) {

            $html .= '<h2 class="service-name">' . $args['title'] . '</h2>';

        }

        $icon = '';
        if ( isset($args['icon']) && !empty($args['icon'])) {

            $icon = '<div class="icon icon-' . esc_attr( $args['icon'] ) . '"></div>';

        }

        if ( isset($args['image']) && !empty($args['image'])) {

            $html .= '<div class="service-icon">';
            $html .=    '<img src="' . esc_url( $args['image'] ) . '" alt="" />';
            $html .=    '<div class="service-mask"></div>';
            $html .=    $icon;
            $html .= '</div>';

        } else {

            $html .= '<div class="service-icon service-icon-no-image">';
            $html .=    '<div class="service-mask"></div>';
            $html .=    $icon;
            $html .= '</div>';

        }

        if ( ! empty( $content )) {
            $html .= '<div class="service-content">' . do_shortcode( $content ) . '</div></div>';
        } else {
            $html .= '</div>';
        }

        $html .= '</div>';

    }

    if ( 'services' == $code ) {

        $content = fluxus_fix_shortcode_content( $content, 'service' );

        $count = count( explode( '[/service]', $content ) ) - 1;

        if ( isset( $args['columns'] ) && in_array( $args['columns'], array( 2, 3, 4, 5 ) ) ) {

            // if we have a valid column setting, then use it
            $columns = $args['columns'];

        } else {

            // otherwise automatically select the best option
            if ( $count <= 2 ) {
                $columns = 2;
            } elseif ( $count == 3 ) {
                $columns = 3;
            } else {
                $columns = 4;
            }

        }

        $fluxus_shortcode_service_index = 0;
        $fluxus_shortcode_service_count = $count;
        $fluxus_shortcode_service_columns = $columns;

        $html .= '<div class="services row-fluid">' . do_shortcode( $content ) . '</div>';

    }

    return $html;

}
add_shortcode( 'services', 'fluxus_shortcode_service' );
add_shortcode( 'service', 'fluxus_shortcode_service' );

/**
 * Accordion shortcode.
 */
function fluxus_shortcode_accordion( $args = null, $content = null, $code = null ) {

    global $fluxus_shortcode_accordion_panel_index;
    global $fluxus_shortcode_accordion_panel_length;

    $content = fluxus_remove_broken_tags( $content );

    if ( 'accordion' == $code ) {

        $content = fluxus_fix_shortcode_content( $content, 'panel' );

        $fluxus_shortcode_accordion_panel_index = 0;
        $fluxus_shortcode_accordion_panel_length = count( explode('[/panel]', $content) ) - 1;

        return '<div class="accordion">' . do_shortcode( $content ) . '</div>';

    }

    if ( 'panel' == $code ) {

        $fluxus_shortcode_accordion_panel_index++;
        $last = $fluxus_shortcode_accordion_panel_length == $fluxus_shortcode_accordion_panel_index;
        $last = $last ? ' panel-last' : '';

        $active = 1 == $fluxus_shortcode_accordion_panel_index;
        $active = $active ? ' panel-active' : '';

        $html = '<div class="panel panel-' . $fluxus_shortcode_accordion_panel_index . $last . $active . '">';

        if ( isset( $args['title'] ) && !empty( $args['title'] ) ) {
            $html .= '<h3 class="panel-title"><a href="#">' . $args['title'] . '</a></h3>';
        } else {
            $html .= '<h3 class="panel-title"><a href="#">' . __( 'Untitled', 'fluxus' ) . '</a></h3>';
        }

        $html .= '<div class="panel-content">' . do_shortcode( $content ) . '</div></div>';

        return $html;

    }

}
add_shortcode( 'accordion', 'fluxus_shortcode_accordion' );
add_shortcode( 'panel', 'fluxus_shortcode_accordion' );

/**
 * Alert shortcode.
 */
function fluxus_shortcode_alert( $args = null, $content = null) {
    $class = isset( $args['type'] ) ? ' alert-' . $args['type'] : '';
    $content = fluxus_remove_broken_tags( $content );
    return '<div class="alert'.$class.'">' . do_shortcode( $content ) . '</div>';
}
add_shortcode( 'alert', 'fluxus_shortcode_alert' );

/**
 * Disables shortcodes for wrapped content.
 */
function fluxus_shortcode_raw( $args = null, $content = null ) {
    return $content;
}
add_shortcode( 'raw', 'fluxus_shortcode_raw' );


/**
 * Button shortcode.
 */
function fluxus_shortcode_button( $args = null, $content = null ) {
    $url    = isset( $args['url'] ) ? esc_url( $args['url'] ) : '#';
    $class  = isset( $args['style'] ) ? 'button button-' . $args['style'] : 'button';
    $size   = isset( $args['size'] ) && ( $args['size'] == 'big' ) ? ' button-big' : '';
    $style  = isset( $args['css-style'] ) ? ' style="' . esc_attr( $args['css-style'] ) . '"' : '';

    $html = '<a' . $style . ' href="' . $url . '" class="' . esc_attr( $class . $size ) . '">' . $content . '</a>';

    return $html;
}
add_shortcode( 'button', 'fluxus_shortcode_button' );


/**
 * Breaking line shortcut.
 */
function fluxus_shortcode_break( $args = null, $content = null ) {
    return '<div class="horizontal-break"></div>';
}
add_shortcode( 'break', 'fluxus_shortcode_break' );


/**
 * Shortcode for pushing content to the sidebar.
 */
function fluxus_shortcode_aside( $args = null, $content = null ) {
    $css = '';

    if ( isset($args['position']) && ($args['position'] == 'absolute') ) {
        return '<aside class="aside-content"><div class="position-absolute">' . do_shortcode ( $content ) . '</div></aside>';
    }
    return '<aside class="aside-content' . $css . '">' . do_shortcode ( $content ) . '</aside>';
}
add_shortcode( 'aside', 'fluxus_shortcode_aside' );


/**
 * Tabs Shortcode.
 */
function fluxus_shortcode_tabs( $args = null, $content = null, $code = '' ) {

    global $fluxus_shortcode_tabs;

    $html = '';

    $content = fluxus_remove_broken_tags( $content );

    if ( !is_array( $fluxus_shortcode_tabs ) ) {

        $fluxus_shortcode_tabs = array();

        $html = '<div class="tabs">';

    }

    if ( $code == 'tabs' ) {

        $fluxus_shortcode_tabs = array();

        $content = fluxus_fix_shortcode_content( $content, 'tab' );

        $content = do_shortcode( $content );

        if ( $fluxus_shortcode_tabs && ( count($fluxus_shortcode_tabs) > 0 ) ) {

            $html .= '<nav class="tabs-menu"><ul>';

            foreach ( $fluxus_shortcode_tabs as $k => $tab_menu_item ) {
                $act = $k == 0 ? ' class="active"' : '';
                $html .= '<li' . $act . '><a href="#">' . $tab_menu_item . '</a></li>';
            }

            $html .= '</ul></nav>';

        }

        $content = preg_replace( '/<p>\s+?<\/p>/', '<p class="pidar"></p>', $content );

        $html .= '<div class="tabs-content">' . $content . '</div></div>';

    } else {

        $fluxus_shortcode_tabs[] = isset( $args['title'] ) ? $args['title'] : 'Untitled tab';

        $css = " tab-index-" . count( $fluxus_shortcode_tabs );
        $html = '<div class="tab' . $css . '">' . $content . '</div>';

    }

    return $html;

}
add_shortcode( 'tabs', 'fluxus_shortcode_tabs' );
add_shortcode( 'tab', 'fluxus_shortcode_tabs' );


function fluxus_shortcode_standfirst( $args = null, $content = null ) {

    $content = fluxus_remove_broken_tags( $content );

    return '<div class="standfirst">' . $content . '</div>';

}
add_shortcode( 'standfirst', 'fluxus_shortcode_standfirst' );


/**
 * Columns shortcode.
 */
function fluxus_shortcode_columns( $args = null, $content = null, $code = null ) {

    global $fluxus_shortcode_columns;

    $content = fluxus_remove_broken_tags( $content );

    if ( 'columns' == $code ) {

        $content = fluxus_fix_shortcode_content( $content, array( 'column\d+' ) );

        $content = '<div class="row-fluid">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column1' == $code ) {
        $content = '<div class="span1">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column2' == $code ) {
        $content = '<div class="span2">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column3' == $code ) {
        $content = '<div class="span3">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column4' == $code ) {
        $content = '<div class="span4">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column5' == $code ) {
        $content = '<div class="span5">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column6' == $code ) {
        $content = '<div class="span6">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column7' == $code ) {
        $content = '<div class="span7">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column8' == $code ) {
        $content = '<div class="span8">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column9' == $code ) {
        $content = '<div class="span9">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column10' == $code ) {
        $content = '<div class="span10">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column11' == $code ) {
        $content = '<div class="span11">' . do_shortcode( $content ) . '</div>';
    }

    if ( 'column12' == $code ) {
        $content = '<div class="span12">' . do_shortcode( $content ) . '</div>';
    }

    return $content;

}
add_shortcode( 'columns', 'fluxus_shortcode_columns' );
add_shortcode( 'column1', 'fluxus_shortcode_columns' );
add_shortcode( 'column2', 'fluxus_shortcode_columns' );
add_shortcode( 'column3', 'fluxus_shortcode_columns' );
add_shortcode( 'column4', 'fluxus_shortcode_columns' );
add_shortcode( 'column5', 'fluxus_shortcode_columns' );
add_shortcode( 'column6', 'fluxus_shortcode_columns' );
add_shortcode( 'column7', 'fluxus_shortcode_columns' );
add_shortcode( 'column8', 'fluxus_shortcode_columns' );
add_shortcode( 'column9', 'fluxus_shortcode_columns' );
add_shortcode( 'column10', 'fluxus_shortcode_columns' );
add_shortcode( 'column11', 'fluxus_shortcode_columns' );
add_shortcode( 'column12', 'fluxus_shortcode_columns' );


/**
 * Override default WP gallery.
 */
function fluxus_shortcode_gallery( $foo, $attr ) {

    global $post;

    static $instance = 0;
    $instance++;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] ) {
            unset( $attr['orderby'] );
        }
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'dl',
        'icontag'    => 'dt',
        'captiontag' => 'dd',
        'columns'    => 3,
        'size'       => 'fluxus-gallery-thumbnail',
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);

    if ( 'RAND' == $order ) {
        $orderby = 'none';
    }

    if ( !empty( $include ) ) {
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }

    } elseif ( !empty($exclude) ) {
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

    }

    if ( empty($attachments) ) {
        return '';
    }

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag    = tag_escape( $itemtag );
    $captiontag = tag_escape( $captiontag );
    $columns    = intval( $columns );

    $itemwidth  = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = $gallery_div = '';

    $link_to_file = isset($attr['link']) && 'file' == $attr['link'];
    $link_css = $link_to_file ? ' gallery-link-file' : '';

    $size_class = sanitize_html_class( $size );
    $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}{$link_css}'>";
    $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

    $i = 0;

    foreach ( $attachments as $id => $attachment ) {

        $link = $link_to_file ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

        $link = preg_replace( '/\/\>\<\/a\>$/', '/><span class="gallery-hover"></span></a>', $link );

        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "
            <{$icontag} class='gallery-icon'>
                $link
            </{$icontag}>";
        if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
                <{$captiontag} class='wp-caption-text gallery-caption'>
                " . wptexturize($attachment->post_excerpt) . "
                </{$captiontag}>";
        }
        $output .= "</{$itemtag}>";

    }

    $output .= "</div>";

    return $output;

}

add_filter( 'post_gallery', 'fluxus_shortcode_gallery', 2, 2 );

