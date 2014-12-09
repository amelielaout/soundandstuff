<?php

class FluxusUpgrade {

    const VERSION = '1.1';
    const UPGRADE_OPTION = 'fluxus_version';
    protected $clean = false;   // Will delete existing data

    function __construct( $clean = false ) {

        $this->clean = $clean;
        $version = get_option( self::UPGRADE_OPTION );

        if ( ! $version ) {

            update_option( self::UPGRADE_OPTION, self::VERSION );

            $this->upgrade_v1_to_v1_1();

            $this->upgrade_finish();

        }

    }

    function upgrade_finish() {

        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        flush_rewrite_rules();

    }

    function upgrade_v1_to_v1_1() {

        /**
         * Upgrade Projects
         */

        $projects = PortfolioProject::all();

        if ( $projects ) {

            $args = array(
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC'
            );

            foreach ( $projects as $project ) {

                $args['post_parent'] = $project->post->ID;

                $media = $project->get_media();

                if ( ! $this->clean && count( $media ) ) {

                    break; // Let's not update if we have some media already

                } elseif ( $this->clean && count( $media ) ) {

                    // Delete existing assigned media
                    foreach ( $media as $m ) {
                        $m->delete();
                    }

                }

                /**
                 * Make sure featured image is added.
                 */
                $featured_id = get_post_thumbnail_id( $project->post->ID );
                $featured_image_added = false;

                $attachments = get_children( $args );

                if ( $attachments ) {

                    $max_order = 0;
                    foreach ( $attachments as $attachment ) {
                        $max_order = $attachment->menu_order > $max_order ? $attachment->menu_order : $max_order;
                    }

                    foreach ( $attachments as $attachment ) {

                        $media_item = PortfolioMedia::create( $project->post->ID );
                        $media_item->meta_type          = 'image';
                        $media_item->meta_attachment_id = $attachment->ID;
                        $media_item->post->menu_order   = $max_order - $attachment->menu_order;

                        // This is featured image
                        if ( $featured_id == $attachment->ID ) {
                            $media_item->meta_featured = 1;
                            $featured_image_added = true;
                        }

                        // If there was no featured image, then make the first one featured
                        if ( ! $featured_id && ! $featured_image_added ) {
                            $media_item->meta_featured = 1;
                            $featured_image_added = true;
                        }

                        $saved = $media_item->save();

                    }

                }

                /**
                 * Featured image was not added, let's add it in the first position.
                 */
                if ( ! $featured_image_added && $featured_id ) {

                    $media_item = PortfolioMedia::create( $project->post->ID );
                    $media_item->meta_type          = 'image';
                    $media_item->meta_attachment_id = $featured_id;
                    $media_item->meta_featured      = 1;
                    $media_item->post->menu_order   = $max_order + 1;

                    $media_item->save();

                }

            }

        }

    }

}

$upgrade = new FluxusUpgrade( false );