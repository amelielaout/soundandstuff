<?php
/**
 * Post Sidebar.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

global $post;

?>
<div class="sidebar sidebar-post widget-area"><?php

    /**
     * Show attachment navigation if we are on
     * an attachment page and there is at least one sibling.
     */
    if ( is_attachment() ) :

        $prev = fluxus_get_previous_image_link( false );
        $next = fluxus_get_next_image_link( false );

        if ( $prev || $next ) : ?>

            <nav class="widget image-navigation"><?php
                if ( $prev ) : ?>
                    <a href="<?php echo esc_url( $prev ); ?>" class="button-minimal button-icon-left icon-left-open-big"><?php _e( 'Previous', 'fluxus' ); ?></a><?php
                endif;

                if ( $next ) : ?>
                    <a href="<?php echo esc_url( $next ); ?>" class="button-minimal button-icon-right icon-right-open-big"><?php _e( 'Next', 'fluxus' ); ?></a><?php
                endif; ?>
            </nav><?php

        endif;

    endif;

    do_action( 'before_sidebar' );

    dynamic_sidebar( 'sidebar-post' );

    ?>
</div>
