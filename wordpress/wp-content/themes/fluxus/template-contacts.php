<?php
/*
Template Name: Contacts
*/

fluxus_add_html_class( 'horizontal-page no-scroll' );

/**
 * Enqueue scripts required for displaying maps.
 */
fluxus_contacts_enqueue_google_maps();

get_header();

if ( have_posts() ) : the_post();

    $contacts_data = fluxus_contacts_get_data( get_the_ID() );

    $has_map = fluxus_contacts_has_map( get_the_ID() );

    // See fluxus_contacts_get_data() for the variables that are extracted.
    extract( $contacts_data );
    $icon_image = $icon_image && !empty( $icon_image ) ? esc_url( $icon_image ) : '';

    if ( $has_map ) {
        $view_btn = '<a id="view-map" href="#" class="button icon-location">' . __( 'View map', 'fluxus' ) . '</a>';
    } else {
        $view_btn = '';
    }

    /**
     * Show Send Message button only if there is a [contact-form-7] short tag
     * in the content.
     */
    if ( preg_match('/\[contact\-form\-7.+?\]/is', $post->post_content) ) {
        $message_btn = '<a id="send-message" href="#" class="button icon-paper-plane">' . __( 'Send message', 'fluxus' ) . '</a>';
    } else {
        $message_btn = '';
    }

    ?>
    <div id="main" class="site page-contacts"><?php

        if ( fluxus_contacts_has_map( get_the_ID() ) ) : ?>

            <div id="map" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>"
                          data-icon-latitude="<?php echo esc_attr($icon_latitude); ?>" data-icon-longitude="<?php echo esc_attr($icon_longitude); ?>"
                          data-icon-image="<?php echo $icon_image; ?>"></div><?php

        endif;

        $map_dim_class = '';

        if ( has_post_thumbnail() ) :

            $map_dim_class = ' class="dim-image"';

            $bg_image = it_get_post_thumbnail( get_the_ID(), 'fluxus-max' ); ?>
            <div class="contacts-background" style="background-image: url(<?php echo esc_url( $bg_image ); ?>)"></div><?php

        endif; ?>

        <div id="map-dim"<?php echo $map_dim_class; ?>></div>

        <div id="content" class="site-content">

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="viewport">
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content"><?php
                        if ( $contacts ) : ?>
                            <div class="row-fluid">
                                <div class="span8"><?php
                                    the_content();
                                    if ( $message_btn || $view_btn ) : ?>
                                        <p><?php echo $message_btn . $view_btn; ?></p><?php
                                    endif ?>
                                </div>
                                <div class="span4">
                                    <div class="contact-details">
                                        <?php foreach ( $contacts as $contact ) : ?>
                                            <h6><?php echo $contact['title']; ?></h6>
                                            <p><?php echo nl2br( $contact['content'] ); ?></p>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div><?php
                        else:
                            the_content();
                            if ( $message_btn || $view_btn ) : ?>
                                <p><?php echo $message_btn . $view_btn; ?></p><?php
                            endif;
                        endif; ?>
                    </div>
                </div>
            </article>

        </div><?php

        if ( $view_btn ) : ?>
            <a id="close-map" href="#" class="button icon-cancel"><?php _e( 'Close', 'fluxus' ); ?></a><?php
        endif;?>

    </div><?php

    if ( $message_btn ) : ?>
        <div id="contacts-modal" class="reveal-modal">
             <h1 class="entry-header"><?php _e( 'Send message', 'fluxus' ); ?></h1>
             <div class="modal-contents"></div>
             <a class="close-reveal-modal">&#215;</a>
        </div><?php
    endif;

endif;

get_footer();

