<?php
/**
 * Link Post Format functionality.
 *
 * @since fluxus 1.0
 */

/**
 * Gets all data assosiated with link post format.
 */
function fluxus_link_get_data( $post_id ) {

    $link             = get_post_meta( $post_id, 'fluxus_link', true );
    $link_title       = get_post_meta( $post_id, 'fluxus_link_title', true );
    $link_description = get_post_meta( $post_id, 'fluxus_link_description', true );

    return array(
            'link' => $link,
            'link_title' => $link_title,
            'link_description' => $link_description
        );

}


/**
 * Template tag to display link information.
 */
function fluxus_link() {

    global $post;

    if ( !$post ) {
        return;
    }

    $data = fluxus_link_get_data( $post->ID );
    extract( $data );

    if ( !$link ) {
        return;
    }

    if ( !$link_title ) {
        $link_title = $post->post_title;
    }

    ?>
    <div class="post-link resizable">
        <div class="js-vertical-center">
            <a href="<?php echo esc_url( $link ); ?>" target="_blank">
                <?php echo $link_title; ?>
            </a><?php

            if ( !empty($link_description) ) : ?>
                <p><?php echo $link_description; ?></p><?php
            endif; ?>
        </div>
    </div><?php
}


/**
 * Wordpresss Admin
 * ------------------------------------------------------------------
 */


/**
 * Meta box contents.
 */
function fluxus_link_meta_box_contents() {
    global $post;

    if ( ! $post) {
        return;
    }

    $data = fluxus_link_get_data( $post->ID );
    extract( $data );

    ?>
    <p>
        <?php _e( 'Links are displayed above post content. If a post has a featured image set, then the link will be overlayed on top of the image.', 'fluxus' ); ?>
    </p>
    <div class="fluxus-meta-field">
        <label for="fluxus_link"><?php _e( 'Link URL', 'fluxus' ); ?></label>
        <div class="field">
            <input type="text" name="fluxus_link" class="input-url" value="<?php echo esc_url( $link ); ?>" />
        </div>
    </div>
    <div class="fluxus-meta-field">
        <label for="fluxus_link_title"><?php _e( 'Title', 'fluxus' ); ?></label>
        <div class="field">
            <input type="text" name="fluxus_link_title" class="link-title" value="<?php echo esc_attr( $link_title ); ?>">
        </div>
    </div>
    <div class="fluxus-meta-field">
        <label for="fluxus_link_description"><?php _e( 'Description', 'fluxus' ); ?></label>
        <div class="field">
            <textarea name="fluxus_link_description" class="link-description"><?php echo $link_description; ?></textarea>
        </div>
    </div>

    <?php
}


/**
 * Save meta box.
 */
function fluxus_link_meta_box_save( $post_id ) {

    if ( ! it_check_save_action( $post_id ) ) {
        return $post_id;
    }

    $data = fluxus_link_get_data( $post_id );

    foreach ( array_keys( $data ) as $key ) {
        $value = isset( $_POST['fluxus_' . $key] ) ? $_POST['fluxus_' . $key] : '';
        update_post_meta( $post_id, 'fluxus_' . $key, $value );
    }

}
add_action( 'save_post', 'fluxus_link_meta_box_save' );


/**
 * Add Meta Box in Page.
 */
function fluxus_link_add_meta_box() {
    add_meta_box(
            'fluxus_link_meta_box',
            __( 'Link', 'fluxus' ),
            'fluxus_link_meta_box_contents',
            'post',
            'normal'
        );
}

/**
 * Initialize Admin-Side Post Format.
 */
function fluxus_post_format_link_admin_init() {

    add_action( 'add_meta_boxes', 'fluxus_link_add_meta_box' );

}
add_action( 'admin_init', 'fluxus_post_format_link_admin_init', 1 );


