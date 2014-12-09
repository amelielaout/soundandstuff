<?php
/**
 * Displays options for template with a background.
 *
 * @since fluxus 1.0
 */

function fluxus_background_get_data( $post_id ) {

    $top                 = get_post_meta( $post_id, 'fluxus_box_position_top', true );
    $left                = get_post_meta( $post_id, 'fluxus_box_position_left', true );
    $text_color          = get_post_meta( $post_id, 'fluxus_background_text_color', true );
    $content_position    = get_post_meta( $post_id, 'fluxus_background_content_position', true );
    $background_position = get_post_meta( $post_id, 'fluxus_background_background_position', true );
    $dim_background      = get_post_meta( $post_id, 'fluxus_background_dim_background', true );

    if ( !$background_position ) {
        $background_position = 'center center';
    }

    if ( !$content_position ) {
        $content_position = 'center';
    }

    if ( !$text_color ) {
        $text_color = 'black';
    }

    if ( !$top ) {
        $top = '20%';
    }

    if ( !$left ) {
        $left = '20%';
    }

    return array(
            'top'                 => $top,
            'left'                => $left,
            'text_color'          => $text_color,
            'content_position'    => $content_position,
            'background_position' => $background_position,
            'dim_background'      => $dim_background
        );
}

function fluxus_background_get_top( $post_id ) {
    return get_post_meta( $post_id, 'fluxus_box_position_top', true );
}

function fluxus_background_get_left( $post_id ) {
    return get_post_meta( $post_id, 'fluxus_box_position_left', true );
}


/**
 * Meta box contents.
 */
function fluxus_background_meta_box_contents() {
    global $post;

    if ( ! $post) {
        return;
    }

    $data = fluxus_background_get_data( $post->ID );
    extract( $data );

    ?>
    <div class="fluxus-meta-field">
        <label for="fluxus_background_background_position"><?php _e( 'Background position', 'fluxus' ); ?></label>
        <div class="field"><?php

                $options = array(
                        'top center'    => __( 'Top', 'fluxus' ),
                        'center center' => __( 'Center', 'fluxus' ),
                        'bottom center' => __( 'Bottom', 'fluxus' )
                    );

            ?>
            <select name="fluxus_background_background_position"><?php
                echo it_array_to_select_options( $options, $background_position ); ?>
            </select>
        </div>
    </div>
    <div class="fluxus-meta-field">
        <label for="fluxus_background_text_color"><?php _e( 'Text color', 'fluxus' ); ?></label>
        <div class="field">
            <select name="fluxus_background_text_color">
                <option<?php if ( $text_color == 'black' ) { echo ' selected="selected"'; } ?> value="black"><?php _e( 'Black', 'fluxus' ); ?></option>
                <option<?php if ( $text_color == 'white' ) { echo ' selected="selected"'; } ?> value="white"><?php _e( 'White', 'fluxus' ); ?></option>
            </select>
        </div>
    </div>
    <div class="fluxus-meta-field">
        <label for="fluxus_background_dim_background"><?php _e( 'Dim background', 'fluxus' ); ?></label>
        <div class="field"><?php

                $checked = $dim_background == '1' ? ' checked="checked"' : '';

            ?>
            <input type="checkbox" name="fluxus_background_dim_background" value="1"<?php echo $checked; ?> />
        </div>
    </div>
    <div class="fluxus-meta-field">
        <label for="fluxus_background_content_position"><?php _e( 'Content position', 'fluxus' ); ?></label>
        <div class="field">
            <select name="fluxus_background_content_position">
                <option<?php if ( $content_position == 'center' ) { echo ' selected="selected"'; } ?> value="center"><?php _e( 'Center', 'fluxus' ); ?></option>
                <option<?php if ( $content_position == 'custom' ) { echo ' selected="selected"'; } ?> value="custom"><?php _e( 'Custom', 'fluxus' ); ?></option>
            </select>
        </div>
    </div>
    <div class="fluxus-meta-field">
        <label for=""><?php _e( 'Custom position', 'fluxus' ); ?></label>
        <div class="field">
            <?php
                $post_url = get_permalink( $post->ID );
                $post_url = add_query_arg( 'set-position', 1, $post_url );
            ?>
            <input type="hidden" name="fluxus_box_position_top" value="<?php echo esc_attr( $top ); ?>" />
            <input type="hidden" name="fluxus_box_position_left" value="<?php echo esc_attr( $left ); ?>" />
            <div id="fluxus-content-position">
                <?php
                    $top_html  = !empty( $top ) ? $top : __( 'not set', 'fluxus' );
                    $left_html = !empty( $left ) ? $left : __( 'not set', 'fluxus' );
                ?>
                <div>Top: <b class="top"><?php echo $top_html; ?></b></div>
                <div>Left: <b class="left"><?php echo $left_html; ?></b></div>
            </div>
            <a href="<?php echo esc_url( $post_url ); ?>" class="button-secondary" id="fluxus-set-content-position"><?php _e( 'Set custom position', 'fluxus' ); ?></a>
        </div>
    </div>

    <?php
}


/**
 * Save meta box.
 */
function fluxus_background_meta_box_save( $post_id ) {

    if ( ! it_check_save_action( $post_id, 'page' ) ) {
        return $post_id;
    }

    $text_color = it_get_key_value( $_POST, 'fluxus_background_text_color' );
    update_post_meta( $post_id, 'fluxus_background_text_color', $text_color );

    $top = it_get_key_value( $_POST, 'fluxus_box_position_top' );
    update_post_meta( $post_id, 'fluxus_box_position_top', $top );

    $left = it_get_key_value( $_POST, 'fluxus_box_position_left' );
    update_post_meta( $post_id, 'fluxus_box_position_left', $left );

    $position = it_get_key_value( $_POST, 'fluxus_background_content_position' );
    update_post_meta( $post_id, 'fluxus_background_content_position', $position );

    $bg_position = it_get_key_value( $_POST, 'fluxus_background_background_position' );
    update_post_meta( $post_id, 'fluxus_background_background_position', $bg_position );

    $bg_dim = isset( $_POST['fluxus_background_dim_background'] ) ? 1 : 0;
    update_post_meta( $post_id, 'fluxus_background_dim_background', $bg_dim );

}
add_action( 'save_post', 'fluxus_background_meta_box_save' );


/**
 * Add Meta Box in Page.
 */
function fluxus_background_add_meta_box() {
    add_meta_box(
            'fluxus_background_meta_box',
            __( 'Bage With Background Options', 'fluxus' ),
            'fluxus_background_meta_box_contents',
            'page',
            'normal'
        );
}

/**
 * Initialize Admin-Side Post Format.
 */
function fluxus_background_admin_init() {

    $post_id = it_get_post_id();

    if ( $post_id ) {

        if ( it_is_template( $post_id, 'template-background.php' ) ) {

            wp_enqueue_script( 'fluxus-wp-admin-background', get_template_directory_uri() . '/js/wp-admin/background.js' );
            wp_enqueue_style( 'fluxus-wp-admin-background', get_template_directory_uri() . '/css/wp-admin/background.css' );

            add_action( 'add_meta_boxes', 'fluxus_background_add_meta_box' );

        }

    }

}
add_action( 'admin_init', 'fluxus_background_admin_init' );


function fluxus_backgorund_set_position_ui() {

    wp_enqueue_script( 'jquery-ui-draggable' );

    wp_enqueue_script( 'fluxus-wp-admin-background', get_template_directory_uri() . '/js/wp-admin/background.js', array('jquery-ui-draggable') );
    wp_enqueue_script( 'fluxus-wp-admin-slider-set-position' );

    wp_enqueue_style( 'fluxus-wp-admin-background', get_template_directory_uri() . '/css/wp-admin/background.css' );
    wp_enqueue_style( 'fluxus-wp-admin-slider-set-position' );

    ?>
    <div id="set-position">
        <p>
            <?php _e( 'Drag the box to place it anywhere on the page. Then click save.', 'fluxus' ); ?>
        </p>
        <a href="#" id="btn-save-position"><?php _e( 'Save & Close', 'fluxus'); ?></a>
    </div><?php

}

function fluxus_background_init() {

    if ( isset( $_GET['set-position'] ) ) {

        if ( is_user_logged_in() && current_user_can( 'edit_pages' )) {

            add_action( 'before', 'fluxus_backgorund_set_position_ui' );

        }

    }

}
add_action( 'init', 'fluxus_background_init' );

