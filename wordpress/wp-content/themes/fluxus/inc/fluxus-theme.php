<?php

class FluxusTheme {

    protected $title;
    protected $description;
    protected $image;

    public $options = array(
            'enable_meta_description' => true,
            'enable_meta_image' => true
        );

    function __construct() {

        add_filter( 'wp_head', array( $this, 'filter_wp_head' ) );

    }

    function filter_wp_head() {

        global $post;

        // og:description / description
        if ( $this->options['enable_meta_description'] ) {

            if ( is_single() && $post && !empty( $post->post_excerpt ) && !post_password_required() ) {

               echo '<meta property="og:description" content="' . esc_attr( $post->post_excerpt ) . '" />' . "\n";
               echo '<meta name="description" content="' . esc_attr( $post->post_excerpt ) . '" />' . "\n";

            } elseif ( $this->description ) {

                echo '<meta property="og:description" content="' . esc_attr( $this->description ) . '" />' . "\n";

                if ( is_front_page() ) {
                    echo '<meta name="description" content="' . esc_attr( $this->description ) . '" />' . "\n";
                }

            }

        }

        // og:image
        if ( $this->options['enable_meta_image'] ) {

            $image = $this->image;

            if ( $image ) {
                echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
            }

        }

    }

    function set_description( $description ) {

        $this->description = $description;

    }

    function set_image( $url ) {

        $this->image = $url;

    }

}

global $fluxus_theme;

$fluxus_theme = new FluxusTheme();

