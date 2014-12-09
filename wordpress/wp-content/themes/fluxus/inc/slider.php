<?php
/**
 * Funcionality of Full Page Slider.
 *
 * @since fluxus 1.0
 */

/**
 * Adds a new tab in the media browser.
 */
function fluxus_slider_tab( $tabs ) {

    global $wpdb;

    $post_id = it_get_post_id();

    // if ( $post_id ) {
    //     $post = get_post( $post_id );

    //     if ( $post ) {
    //         if ( $post->post_type != 'page' ) {
    //             return array();
    //         }
    //     }
    // }

    $tabs['fluxus_slider'] = __( 'Slider', 'fluxus' );

    return $tabs;

}
add_filter( 'media_upload_tabs', 'fluxus_slider_tab' );


/**
 * Retrievs every child slide of a post and looks for
 * empty order values. If finds one, then sets it to the
 * highest value available.
 *
 * Should be run every time new slide is created.
 */
function fluxus_slider_set_order( $post_id ) {

    global $wpdb;

    $args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'post_parent'    => $post_id
    );

    $attachments = get_children( $args );

    $ids = false;

    /**
     * Checks if any of the attachments has no slide_order set.
     * If so - sets it.
     */
    foreach ( $attachments as $slide ) {

        $order = get_post_meta( $slide->ID, 'slide_order', true );

        if ( empty( $order ) ) {

            if ( $ids === false ) {
                foreach ( $attachments as $temp_slide ) {
                    $ids[] = $temp_slide->ID;
                }
                $ids_sql = join( ',', $ids );
            }

            // select the highest existing order
            $current_max_order = $wpdb->get_var( "SELECT MAX(meta_value) AS max_value FROM $wpdb->postmeta
                                                  WHERE meta_key = 'slide_order' AND post_id IN ($ids_sql)" );

            $current_max_order = ! is_numeric( $current_max_order ) ? 0 : $current_max_order;

            update_post_meta( $slide->ID, 'slide_order', $current_max_order + 1 );

        }

    }

}


/**
 * Returns all slides of a post.
 */
function fluxus_slider_get_slides( $post_id, $args = array() ) {

    $defaults = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'any',
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'slide_order',
        'order'          => 'ASC',
        'posts_per_page' => -1,
        'post_parent'    => $post_id
    );

    $r = wp_parse_args( $args, $defaults );

    return get_posts( $r );

}


/**
 * Returns only published (meta key slide_published=true) slides.
 */
function fluxus_slider_get_published_slides( $post_id ) {

    $args = array(
        'meta_query'     => array(
            array(
                'key'       => 'slide_published',
                'value'     => '1'
            )
        )
    );

    return fluxus_slider_get_slides( $post_id, $args );
}


/**
 * Gets slide custom data.
 */
function fluxus_slider_get_slide_data( $slide_id ) {

    $image = wp_get_attachment_image_src( $slide_id, 'fluxus-max' );

    if ( is_array( $image ) ) {
        $image = $image[0];
    } else {
        $image = '';
    }

    $data = get_post_meta( $slide_id, 'slide_info', true );

    $publish = get_post_meta( $slide_id, 'slide_published', true );
    if ( ! $publish ) {
        $publish = 0;
    }

    return array(
            'image'               => $image,
            'title'               => it_get_key_value( $data, 'slide_title' ),
            'subtitle'            => it_get_key_value( $data, 'slide_subtitle' ),
            'description'         => it_get_key_value( $data, 'slide_description' ),
            'background_position' => it_get_key_value( $data, 'slide_background_position', 'center center' ),
            'info_box_position'   => it_get_key_value( $data, 'slide_info_box_position', 'center' ),
            'info_box_left'       => it_get_key_value( $data, 'slide_info_box_left' ),
            'info_box_top'        => it_get_key_value( $data, 'slide_info_box_top' ),
            'info_box_text_color' => it_get_key_value( $data, 'slide_text_color', 'black' ),
            'link_portfolio'      => it_get_key_value( $data, 'slide_link_portfolio' ),
            'link'                => it_get_key_value( $data, 'slide_link' ),
            'link_title'          => it_get_key_value( $data, 'slide_link_title' ),
            'dim_background'      => it_get_key_value( $data, 'slide_dim_background', '0' ),
            'publish'             => $publish
        );

}


/**
 * Set slide info box position.
 */
function fluxus_slider_set_position( $slides ) {

    global $post;

    /**
     * Check if we are in the mode of setting the Info Box position.
     */
    if ( isset($_GET['set-infobox-position']) && isset($_GET['slide']) ) {

        if ( is_user_logged_in() && current_user_can( 'edit_pages' )) {
            $slide_id = $_GET['slide'];

            /**
             * Loop through all slides. Even if they are not published.
             */
            $slides = fluxus_slider_get_slides( $post->ID );
            foreach ( $slides as $k => $slide ) {
                if ( $slide->ID != $slide_id ) {
                    unset($slides[$k]);
                }
            }

            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'fluxus-wp-admin-slider', get_template_directory_uri() . '/js/wp-admin/slider.js', array('jquery-ui-draggable') );

            wp_enqueue_style( 'fluxus-wp-admin-slider-set-position', get_template_directory_uri() . '/css/wp-admin/slider-set-position.css' );

            ?>
            <div id="slider-set-position">
                <p>
                    <?php _e( 'Drag the box to place it anywhere on the page. Then click save.', 'fluxus'); ?>
                </p>
                <a href="#" class="save-position"><?php _e( 'Save & Close', 'fluxus'); ?></a>
            </div><?php

        }

    }

    return $slides;

}


/**
 * The content of Slider tab in media browser.
 */
function fluxus_slider_media_tab_content() {

    media_upload_header();

    // add scripts & styles that support the slider admin functionality
    wp_enqueue_style( 'fluxus-wp-admin-slider', get_template_directory_uri() . '/css/wp-admin/slider.css' );

    wp_enqueue_script( 'fluxus-wp-admin-slider', get_template_directory_uri() . '/js/wp-admin/slider.js', array( 'jquery-ui-sortable' ) );
    wp_enqueue_script( 'jquery-ui-sortable' );

    if ( ! isset($_GET['post_id']) ) {
        echo 'Post ID is not set.';
        return false;
    }

    $post_id = $_GET['post_id'];

    add_filter( 'posts_orderby_request', 'fluxus_portfolio_orderby_filter' );

    $portfolio_items = get_posts(array(
            'post_type'      => 'fluxus_portfolio',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order ID',
            'post_status'    => null,
            'order'          => 'DESC DESC'
        ));

    remove_filter( 'posts_orderby_request', 'fluxus_portfolio_orderby_filter' );

    $post = get_post( $post_id );

    if ( ! $post ) {
        echo 'Post not found.';
        return false;
    }

    fluxus_slider_set_order( $post_id );

    $slides = fluxus_slider_get_slides( $post_id );

    ?>
    <div id="fluxus-slider-tab" data-post-id="<?php echo esc_attr( $post_id ); ?>">

        <div class="slides-container"><?php

            if ( isset( $post ) && isset( $post->post_type ) && ( $post->post_type != 'page' ) ) : ?>

                <div class="fluxus-message">
                    <div class="fluxus-message-contents">
                        <?php _e( 'Slider only works with pages. Create a page with Full Page Slider template and visit this tab again.', 'fluxus' ); ?>
                    </div>
                </div><?php

            else:

                if ( ! it_is_template( $post_id, 'template-full-page-slider.php' ) ) : ?>

                    <div class="fluxus-message">
                        <div class="fluxus-message-contents">
                            <?php _e( 'Current page template is not set to Full Page Slider. To make Full Page Slider visible set the current page\'s template to Full Page Slider.', 'fluxus' ); ?>
                        </div>
                    </div><?php

                endif;


                if ( ! $slides ) : ?>

                    <div class="fluxus-message">
                        <div class="fluxus-message-contents">
                            <?php _e( 'No images were uploaded to current page. Please go to Insert Media tab and upload new images to create a Slider.', 'fluxus' ); ?>
                        </div>
                    </div><?php

                else:

                ?>
                    <table class="widefat" cellspacing="0" id="slides">
                        <thead>
                            <tr>
                                <th class="col-media"><?php _e( 'Media', 'fluxus' ); ?></th>
                                <th class="col-title"><?php _e( 'Title', 'fluxus' ); ?></th>
                                <th class="col-order"><?php _e( 'Order', 'fluxus' ); ?></th>
                                <th class="col-actions"><?php _e( 'Actions', 'fluxus' ); ?></th>
                            </tr>
                        </thead>
                        <tbody><?php

                            foreach ( $slides as $slide ) :

                                $id = $slide->ID;
                                $image = wp_get_attachment_image_src( $id, 'fluxus-slider-admin-thumbnail' );

                                $data = fluxus_slider_get_slide_data( $id );

                                ?>
                                <tr>
                                    <td>
                                        <?php echo wp_get_attachment_image( $id, 'fluxus-slider-admin-thumbnail' ); ?>
                                    </td>
                                    <td>
                                        <div class="wrap-slide-title<?php if ( empty($data['title']) ) echo ' slide-title-empty'; ?>">
                                            <i class="title-empty"><?php _e( 'Not set', 'fluxus' ); ?></i>
                                            <span class="slide-title"><?php echo $data['title']; ?></span>
                                        </div>
                                        <div class="wrap-slide-status <?php echo $data['publish'] ? 'slide-published' : 'slide-unpublished'; ?>">
                                            <span class="slide-status-published"><?php _e( 'Published', 'fluxus' ); ?></span>
                                            <span class="slide-status-unpublished"><?php _e( 'Unpublished', 'fluxus' ); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" value="<?php echo $id; ?>" class="slide-id" />
                                        <a href="#" class="slide-move"></a>
                                    </td>
                                    <td>
                                        <a href="#" class="show-toggle" data-hide="<?php _e( 'Hide details', 'fluxus' ); ?>" data-show="<?php _e( 'Show details', 'fluxus' ); ?>"><?php _e( 'Show Details', 'fluxus' ); ?></a>
                                        <div class="details" id="slide_<?php echo $id; ?>_details">
                                            <form enctype="multipart/form-data" method="post" action="/">
                                                <input type="hidden" name="security" value="<?php echo wp_create_nonce('fluxus-slider-form'); ?>" />
                                                <input type="hidden" name="action" value="fluxus_slider_slide_save" />
                                                <input type="hidden" name="slide_id" value="<?php echo $id; ?>" />
                                                <div class="field">
                                                    <label><?php _e( 'Title', 'fluxus' ); ?></label>
                                                    <input type="text" name="slide_title" value="<?php echo esc_attr( $data['title'] ); ?>" class="input-text" />
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Subtitle', 'fluxus' ); ?></label>
                                                    <input type="text" name="slide_subtitle" value="<?php echo esc_attr( $data['subtitle'] ); ?>" class="input-text" />
                                                    <i><?php _e( 'eg. featured project', 'fluxus' ); ?></i>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Description', 'fluxus' ); ?></label>
                                                    <textarea name="slide_description"><?php echo $data['description']; ?></textarea>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Custom link', 'fluxus' ); ?></label>
                                                    <input type="text" name="slide_link" value="<?php echo esc_url( $data['link'] ); ?>" class="input-text" />
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Link to portfolio', 'fluxus' ); ?></label><?php

                                                    $options = array( '' => '' );
                                                    foreach ( $portfolio_items as $item ) {
                                                        $options[ $item->ID ] = $item->post_title;
                                                    }

                                                    ?>
                                                    <select name="slide_link_portfolio">
                                                        <?php echo it_array_to_select_options( $options, $data['link_portfolio'] ); ?>
                                                    </select>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Link title', 'fluxus' ); ?></label>
                                                    <input type="text" name="slide_link_title" value="<?php echo esc_attr( $data['link_title'] ); ?>" class="input-text" />
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Text color', 'fluxus' ); ?></label>
                                                    <select name="slide_text_color">
                                                        <option value="black"<?php echo $data['info_box_text_color'] == 'black' ? ' selected="selected"' : ''; ?>><?php _e( 'Black', 'fluxus' ); ?></option>
                                                        <option value="white"<?php echo $data['info_box_text_color'] == 'white' ? ' selected="selected"' : ''; ?>><?php _e( 'White', 'fluxus' ); ?></option>
                                                    </select>
                                                    <i><?php _e( 'Choose according to the picture brightness', 'fluxus' ); ?></i>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Info box position', 'fluxus' ); ?></label>
                                                    <?php

                                                        $link = get_permalink( $post_id );
                                                        $link = add_query_arg( array(
                                                                'set-infobox-position' => 1,
                                                                'slide' => $id
                                                            ), $link );

                                                        $info_box_position = $data['info_box_position'];
                                                        $position_custom_html = '';

                                                        if ( $info_box_position == 'custom' ) {
                                                            $position_custom_html = ' (' . $data['info_box_left'] . ' ' . $data['info_box_top'] . ')';
                                                        }

                                                    ?>
                                                    <select name="slide_info_box_position">
                                                        <option value="center"<?php echo $info_box_position == 'center' ? ' selected="selected"' : ''; ?>><?php _e( 'Center', 'fluxus' ); ?></option>
                                                        <option value="custom"<?php echo $info_box_position == 'custom' ? ' selected="selected"' : ''; ?> data-custom="<?php echo esc_attr( __( 'Custom', 'custom' ) ); ?>"><?php _e( 'Custom', 'custom' ); ?><?php echo $position_custom_html; ?></option>
                                                    </select>
                                                    <input type="hidden" value="<?php echo $data['info_box_left']; ?>" name="slide_info_box_left" />
                                                    <input type="hidden" value="<?php echo $data['info_box_top']; ?>" name="slide_info_box_top" />
                                                    <a href="<?php echo $link ?>" target="_blank" class="set-infobox-position"><?php _e( 'Set custom position', 'fluxus' ); ?></a>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Image scaling', 'fluxus' ); ?></label><?php

                                                    $options = array(
                                                            'top center'    => __( 'Crop Bottom', 'fluxus' ),
                                                            'center center' => __( 'Crop Top and Bottom', 'fluxus' ),
                                                            'bottom center' => __( 'Crop Top', 'fluxus' ),
                                                            'fit'           => __( 'Fit Image', 'fluxus' ),
                                                        );

                                                    ?>
                                                    <select name="slide_background_position">
                                                        <?php echo it_array_to_select_options( $options, $data['background_position'] ); ?>
                                                    </select>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Dim Background', 'fluxus' ); ?></label>
                                                    <input type="checkbox" name="slide_dim_background" value="1"<?php echo $data['dim_background'] ? ' checked="checked"' : ''; ?> />
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Published', 'fluxus' ); ?></label>
                                                    <input type="checkbox" name="slide_published" value="1"<?php echo $data['publish'] ? ' checked="checked"' : ''; ?> />
                                                </div>
                                                <div class="field">
                                                    <label for="">&nbsp;</label>
                                                    <a href="#" class="button fluxus-save-slide"><?php _e( 'Save changes', 'fluxus' ); ?></a>
                                                    <span class="saving-status" data-saving="<?php _e( 'Saving...', 'fluxus' ); ?>" data-ok="<?php _e( 'Saved!', 'fluxus' ); ?>" data-failed="<?php _e( 'Failed', 'fluxus' ); ?>"></span>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr><?php

                            endforeach;

                            ?>
                        </tbody>
                    </table><?php

                endif;

            endif;

            ?>
            <form enctype="multipart/form-data" method="post" action="/" id="fluxus-slider-order-form">
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('fluxus-slider-order-form'); ?>" />
                <input type="hidden" name="action" value="fluxus_slider_save_order" />
                <input type="hidden" name="order" value="" />
            </form>

        </div>

        <div class="fluxus-slider-sidebar"><?php

                $options = fluxus_slider_get_options( $post_id );
                $auto_slideshow = $options['slideshow'] ? ' checked="checked"' : '';

            ?>
            <form enctype="multipart/form-data" method="post" action="/" id="fluxus-slider-options-form">
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('fluxus-slider-options-form'); ?>" />
                <input type="hidden" name="action" value="fluxus_slider_save_options" />

                <h3><?php _e( 'Slider Options', 'fluxus' ); ?></h3>

                <div class="fluxus-slider-settings">

                    <label for="fluxus_slider_slideshow" class="setting">
                        <span><?php _e( 'Auto Slideshow', 'fluxus' ); ?></span>
                        <input type="checkbox" name="fluxus_slider_slideshow" id="fluxus_slider_slideshow" value="1"<?php echo $auto_slideshow; ?> />
                    </label>

                    <label for="fluxus_slider_slideshow_interval" class="setting">
                        <span><?php _e( 'Slide Change Interval', 'fluxus' ); ?></span>
                        <input type="text" name="fluxus_slider_slideshow_interval" id="fluxus_slider_slideshow_interval" value="<?php echo $options['slideshow_interval']; ?>" />
                    </label>

                </div>
            </form>

        </div>

    </div><?php

}


/**
 * Use fluxus_slider_media_tab_content output as tab content.
 */
function fluxus_insert_slider_iframe() {

    return wp_iframe( 'fluxus_slider_media_tab_content' );

}
add_action( 'media_upload_fluxus_slider', 'fluxus_insert_slider_iframe' );


/**
 * Process AJAX save slide details request
 */
function fluxus_slider_slide_save() {

    check_ajax_referer( 'fluxus-slider-form', 'security' );

    $data = $_POST;
    unset( $data['security'], $data['action'] );

    if ( ! isset( $data['slide_id'] ) ) {
        die( '0' );
    }

    $post_id = $data['slide_id'];

    unset( $data['slide_id'] );

    $published = isset( $data['slide_published'] ) ? $data['slide_published'] : 0;

    update_post_meta( $post_id, 'slide_published', $published );
    update_post_meta( $post_id, 'slide_info', $data );

    die( '1' );

}
add_action( 'wp_ajax_fluxus_slider_slide_save', 'fluxus_slider_slide_save' );


/**
 * Process AJAX save slide order request
 */
function fluxus_slider_save_order() {

    check_ajax_referer( 'fluxus-slider-order-form', 'security' );

    $data = $_POST;
    unset( $data['security'], $data['action'] );

    $index = 1;

    if ( isset( $data['order'] ) && ! empty( $data['order'] ) ) {

        $order = explode( ',', $data['order'] );

        if ( is_array( $order ) ) {
            foreach ( $order as $slide_id ) {
                update_post_meta( $slide_id, 'slide_order', $index++ );
            }
        }

    }

    die( '1' );

}
add_action( 'wp_ajax_fluxus_slider_save_order', 'fluxus_slider_save_order' );


function fluxus_slider_get_options( $post_id ) {

    $options = array();

    $options['slideshow'] = get_post_meta( $post_id, 'fluxus_slider_slideshow', true );
    $options['slideshow_interval'] = get_post_meta( $post_id, 'fluxus_slider_slideshow_interval', true );

    if ( $options['slideshow_interval'] && !is_numeric( $options['slideshow_interval'] ) ) {
        $options['slideshow_interval'] = 7;
    }

    return $options;

}

/**
 * Save slider options AJAX request.
 */
function fluxus_slider_save_options() {

    check_ajax_referer( 'fluxus-slider-options-form', 'security' );

    if ( ! isset( $_POST['post_id'] ) ) {
        die( '0' );
    }

    $allowed_keys = array(
            'fluxus_slider_slideshow',
            'fluxus_slider_slideshow_interval'
        );

    if ( isset( $_POST['key'] ) && in_array( $_POST['key'], $allowed_keys ) ) {

        if ( isset( $_POST['value'] ) && !empty( $_POST['value'] ) ) {

            update_post_meta( $_POST['post_id'], $_POST['key'], $_POST['value'] );

        } else {

            delete_post_meta( $_POST['post_id'], $_POST['key'] );

        }

        die( '1' );

    } else {

        die( '0' );

    }


}
add_action( 'wp_ajax_fluxus_slider_save_options', 'fluxus_slider_save_options' );


function fluxus_slider_admin_init() {

    // image size to be used in admin area
    add_image_size( 'fluxus-slider-admin-thumbnail', 120, 90, true );

}
add_action( 'admin_init', 'fluxus_slider_admin_init' );


function fluxus_slider_init() {

    add_filter( 'fluxus_before_slider', 'fluxus_slider_set_position', 1, 1 );

}
add_action( 'init', 'fluxus_slider_init' );
