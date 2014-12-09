<?php

abstract class FluxusAdminPage {

    public $post_id = null;
    public $styles = array();
    public $scripts = array();

    function __construct( $post_id ) {
        $this->post_id = $post_id;
        $this->load_styles();
        $this->load_scripts();
    }

    /**
     * Enqueue styles
     */
    function load_styles() {
        foreach ( $this->styles as $style ) {
            $name = isset( $style[0] ) ? $style[0] : '';
            $file = isset( $style[1] ) && ! empty( $style[1] ) ? get_template_directory_uri() . '/css/wp-admin/' . $style[1] : '';
            wp_enqueue_style( $name, $file );
        }

    }

    /**
     * Enqueue scripts
     */
    function load_scripts() {
        foreach ( $this->scripts as $script ) {
            $name = isset( $script[0] ) ? $script[0] : '';
            $file = isset( $script[1] ) && ! empty( $script[1] ) ? get_template_directory_uri() . '/js/wp-admin/' . $script[1] : '';
            $dependencies = isset( $script[2] ) ? $script[2] : array();
            wp_enqueue_script( $name, $file, $dependencies, '', true );
        }
    }

    function is_request( $type ) {
        $request = isset( $_SERVER['REQUEST_METHOD'] ) ?
                   strtoupper( $_SERVER['REQUEST_METHOD'] ) :
                   'GET';

        return $request == strtoupper( $type );
    }

    function get_put_args() {
        $put = json_decode( file_get_contents( 'php://input' ), true );
        return is_array( $put ) ? $put : array();
    }

}