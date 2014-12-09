<?php

class PortfolioMedia extends FluxusPage {

    const POST_TYPE = 'project_media';

    protected $META_PREFIX = 'project_media_';

    protected $meta_data_defaults = array(
            'type'          => 'image',
            'description'   => '',
            'attachment_id' => '',
            'screenshot_id' => '',
            'embed'         => '',
            'featured'      => 0,
            'published'     => 1
        );
    protected $image_data = array();

    function to_array() {

        $this->fetch_post()->fetch_post_meta();

        $thumbnail_url = $this->get_image_data( 'thumbnail' );

        $screenshot_url = $this->get_video_thumbnail( 'thumbnail' );

        return array_merge( $this->meta_data_defaults, array(
                'id'             => $this->post->ID,
                'order'          => $this->post->menu_order,
                'filename'       => $this->get_filename(),
                'type'           => $this->meta_type,
                'description'    => $this->meta_description,
                'embed'          => $this->meta_embed,
                'featured'       => $this->meta_featured,
                'attachment_id'  => $this->meta_attachment_id,
                'thumbnail_url'  => $thumbnail_url ? $thumbnail_url['src'] : '',
                'screenshot_id'  => $this->meta_screenshot_id,
                'screenshot_url' => $screenshot_url ? $screenshot_url['src'] : '',
                'published'      => $this->meta_published
            )
        );

    }

    function get_alt() {

        return strip_tags( $this->meta_description );

    }

    function get_video_thumbnail( $size ) {

        if ( ! $this->is_video() || ! $this->meta_embed ) {
            return '';
        }

        if ( is_numeric( $this->meta_screenshot_id ) ) {

            $image = wp_get_attachment_image_src( $this->meta_data['screenshot_id'], $size );

            if ( $image ) {

                return array(
                        'src'       => $image[0],
                        'width'     => $image[1],
                        'height'    => $image[2],
                        'ratio'     => $image[2] ? $image[1] / $image[2] : ''
                    );

            }

        }

        return array();

    }

    function get_filename() {
        return pathinfo( wp_get_attachment_url( $this->meta_data['attachment_id'] ), PATHINFO_BASENAME );
    }

    function initialize_image_size( $size ) {

        $this->fetch_post_meta();

        $image = wp_get_attachment_image_src( $this->meta_data['attachment_id'], $size );

        if ( $image ) {

            $this->image_data[ $size ] = array(
                    'src'       => $image[0],
                    'width'     => $image[1],
                    'height'    => $image[2],
                    'ratio'     => $image[2] ? $image[1] / $image[2] : ''
                );

        } else {

            $this->image_data[ $size ] = '';

        }

    }

    function get_image_data( $size ) {

        if ( ! isset( $this->image_data[ $size ] ) ) {
            $this->initialize_image_size( $size );
        }

        return $this->image_data[ $size ];

    }

    function is_video() {
        return $this->meta_type == 'video';
    }

    function get_video_size() {

        $result = array(
                'width'  => '',
                'height' => '',
                'ratio'  => ''
            );

        if ( $this->is_video() && $this->meta_embed ) {

            preg_match( '/width="(\d+)"/i', $this->meta_embed, $width_matches );
            preg_match( '/height="(\d+)"/i', $this->meta_embed, $height_matches );

            if ( is_array( $width_matches ) && isset( $width_matches[1] ) ) {
                $result['width'] = $width_matches[1];
            }

            if ( is_array( $height_matches ) && isset( $height_matches[1] ) ) {
                $result['height'] = $height_matches[1];
            }

            if ( $result['width'] && $result['height'] ) {
                $ratio = $result['width'] / $result['height'];
                $result['width'] = 1280;
                $result['height'] = round(1280 / $ratio);
                $result['ratio'] = $result['width'] / $result['height'];
            }

        }

        return $result;

    }

    function is_image() {
        return $this->meta_type == 'image';
    }

    /**
     * ------------------------------------------------------------
     * Static Functions
     * ------------------------------------------------------------
     */

    static function get_featured_media( $post_id ) {

        $args = array(
            'posts_per_page'  => 1,
            'orderby'         => 'menu_order',
            'order'           => 'DESC',
            'meta_key'        => self::POST_TYPE . '_featured',
            'meta_value'      => '1',
            'post_type'       => self::POST_TYPE,
            'post_parent'     => $post_id,
            'post_status'     => 'any',
            'suppress_filters' => true );

        $featured_media = get_posts( $args );

        if ( $featured_media ) {

            return new PortfolioMedia( $featured_media[0]->ID );

        } else {

            $args['meta_key'] = null;
            $args['meta_value'] = null;

            $first_media = get_posts( $args );

            if ( $first_media ) {

                return new PortfolioMedia( $first_media[0]->ID );

            }

        }

        return false;

    }

    static function create( $parent_id, $args = array() ) {

        $all = self::all( $parent_id );

        $defaults = array(
                'post_parent'     => $parent_id,
                'post_type'       => self::POST_TYPE
            );

        $args = array_merge( $defaults, $args );

        $post_id = wp_insert_post( $args, false );

        if ( $post_id ) {
            $post = get_post( $post_id );
            $post->menu_order = $post->ID;
            wp_update_post( $post );
        }

        return new PortfolioMedia( $post_id );

    }

    static function all( $parent_id ) {

        $args = array(
                'posts_per_page'  => -1,
                'offset'          => 0,
                'orderby'         => 'menu_order',
                'order'           => 'DESC',
                'post_type'       => self::POST_TYPE,
                'post_parent'     => $parent_id,
                'post_status'     => 'any',
                'suppress_filters' => true
            );

        $posts = get_posts( $args );

        if ( $posts ) {

            foreach ( $posts as $k => $post ) {
                $posts[$k] = new PortfolioMedia( $post->ID );
            }

            return $posts;

        } else {

            return array();

        }

    }

}

