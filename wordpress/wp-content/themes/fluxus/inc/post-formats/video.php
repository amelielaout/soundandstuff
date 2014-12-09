<?php
/**
 * Video Post Format functionality.
 *
 * @since fluxus 1.0
 */

function fluxus_video_get_data( $post_id ) {

    $embed = get_post_meta( $post_id, 'fluxus_embed', true );
    return array(
            'embed' => $embed
        );

}

/**
 * Displays quote markup.
 *
 * @since fluxus 1.0
 */
function fluxus_video() {
    global $post;

    if ( !$post ) {
        return;
    }

    $data = fluxus_video_get_data( $post->ID );
    extract($data);

    if ( $embed ) : ?>
        <div class="wrap-embed-video">
            <?php echo $embed; ?>
        </div><?php
        return;
    endif;

}

function fluxus_video_get_size() {
    global $post;

    if ( !$post ) {
        return;
    }

    $embed = get_post_meta( $post->ID, 'fluxus_embed', true );
    if ( !$embed ) {
        return;
    }

    $width = false;
    $height = false;

    // this is vimeo embed code
    if ( preg_match( '/src="(.+?vimeo\.com.+?)"/', $embed) ) {
        preg_match( '/width="(\d+)"/', $embed, $matches);
        if ( $matches && isset($matches[1]) && is_numeric($matches[1]) ) {
            $width = $matches[1];
        }

        preg_match( '/height="(\d+)"/', $embed, $matches);
        if ( $matches && isset($matches[1]) && is_numeric($matches[1]) ) {
            $height = $matches[1];
        }
    }

    // this is youtube embed code
    if ( preg_match( '/src="(.+?youtube\.com.+?)"/', $embed) ) {
        preg_match( '/width="(\d+)"/', $embed, $matches);
        if ( $matches && isset($matches[1]) && is_numeric($matches[1]) ) {
            $width = $matches[1];
        }

        preg_match( '/height="(\d+)"/', $embed, $matches);
        if ( $matches && isset($matches[1]) && is_numeric($matches[1]) ) {
            $height = $matches[1];
        }
    }

    if ( $width && $height ) {
        return array( $width, $height );
    } else {
        return false;
    }

}

/**
 * Meta box contents.
 */
function fluxus_video_meta_box_contents() {
    global $post;

    if ( ! $post) {
        return;
    }

    $embed = get_post_meta( $post->ID, 'fluxus_embed', true );

    ?>
    <div class="fluxus-meta-field">
        <label for="fluxus_embed"><?php _e( 'Embed Code', 'fluxus' ); ?></label>
        <div class="field">
            <textarea name="fluxus_embed" class="embed-code-area"><?php echo $embed; ?></textarea>
        </div>
    </div>

    <?php
}


/**
 * Save meta box.
 */
function fluxus_video_meta_box_save( $post_id ) {

    if ( ! it_check_save_action( $post_id ) ) {
        return $post_id;
    }

    $embed = isset($_POST['fluxus_embed']) ? $_POST['fluxus_embed'] : '';
    update_post_meta( $post_id, 'fluxus_embed', $embed );

}
add_action( 'save_post', 'fluxus_video_meta_box_save' );


/**
 * Add Meta Box in Page.
 */
function fluxus_video_meta_box() {
    add_meta_box(
            'fluxus_video_meta_box',
            __( 'Video', 'fluxus' ),
            'fluxus_video_meta_box_contents',
            'post',
            'normal'
        );
}

/**
 * Initialize Admin-Side Post Format.
 */
function fluxus_post_format_video_admin_init() {

    add_action( 'add_meta_boxes', 'fluxus_video_meta_box' );

}
add_action( 'admin_init', 'fluxus_post_format_video_admin_init', 1 );


