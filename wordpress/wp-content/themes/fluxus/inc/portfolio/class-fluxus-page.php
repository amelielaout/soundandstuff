<?php

abstract class FluxusPage {

    protected $post_id = null;
    protected $meta_data = array();
    protected $meta_data_defaults = array();
    protected $meta_data_loaded = false;
    protected $wp_post_object = null;
    protected $META_PREFIX = '';

    function __construct( $page_id ) {
        $this->post_id = $page_id;
    }

    function __set( $name, $value ) {

        if ( 'meta_' == substr( $name, 0, 5 ) ) {

            // Load post meta data.
            $this->fetch_post_meta();

            $key = substr( $name, 5 );

            // If this is a defined meta key
            if ( isset( $this->meta_data_defaults[$key] ) ) {

                // Call set_meta_$key if exists
                if ( method_exists( $this, 'set_meta_' . $key ) ) {
                    call_user_func( array( $this, 'set_meta_' . $key ), $value );

                // Set $this->meta_data array value
                } else {
                    $this->meta_data[$key] = $value;
                }
            }

        }

    }

    function __get( $name ) {

        if ( 'meta_' == substr( $name, 0, 5 ) ) {

            $this->fetch_post_meta();

            $key = substr( $name, 5 );

            // If this is a defined meta key
            if ( isset( $this->meta_data_defaults[$key] ) ) {

                // Call and return get_meta_$key() if exists
                if ( method_exists( $this, 'get_meta_' . $key ) ) {
                    return call_user_func( array( $this, 'get_meta_' . $key ), $value );

                // Return the value from $this->meta_data array()
                } else {

                    if ( isset( $this->meta_data[$key]) ) {
                        return $this->meta_data[$key];
                    }

                }
            }

        } elseif ( method_exists( $this, 'get_' . $name ) ) {

            return call_user_func( array( $this, 'get_' . $name ) );

        }

    }

    function metadata() {

        return $this->meta_data;

    }

    /**
     * Load WP post object.
     */
    function fetch_post() {

        if ( null === $this->wp_post_object ) {
            $this->wp_post_object = get_post( $this->post_id );

            // get_post() returns null on failure.
            if ( ! $this->wp_post_object ) {
                $this->wp_post_object = false;
            }
        }

        return $this;

    }

    /**
     * Loads post's meta fields.
     */
    function fetch_post_meta() {

        if ( false == $this->meta_data_loaded ) {
            $this->meta_data_loaded = true;
            foreach ( $this->meta_data_defaults as $key => $value ) {

                if ( metadata_exists( 'post', $this->post_id, $this->META_PREFIX . $key) ) {
                    $this->meta_data[$key] = get_post_meta( $this->post_id, $this->META_PREFIX . $key, true );
                } else {
                    $this->meta_data[$key] = $this->meta_data_defaults[$key];
                }

            }
        }

        return $this;

    }

    /**
     * Return post object.
     */
    function get_post() {
        return $this->fetch_post()->wp_post_object;
    }

    function get_post_id() {
        return $this->post_id;
    }

    /**
     * Check if post object exists.
     */
    function exists() {
        return (bool) $this->fetch_post()->wp_post_object;
    }

    function save() {

        if ( $this->wp_post_object ) {
            wp_update_post( $this->wp_post_object );
        }

        foreach ( $this->meta_data_defaults as $key => $value ) {
            $value = isset( $this->meta_data[$key] ) ? $this->meta_data[$key] : $value;
            update_post_meta( $this->post_id, $this->META_PREFIX . $key, $value );
        }
        return $this;
    }

    function delete() {

        $this->fetch_post();
        return wp_delete_post( $this->post_id, true );

    }

    function update_from_array( $array, $key_prefix = true ) {

        if ( $key_prefix === true ) {
            $key_prefix = $this->META_PREFIX;
        }

        if ( $key_prefix === false ) {
            $key_prefix = '';
        }

        foreach ( $this->meta_data_defaults as $key => $value ) {
            $array_key = $key_prefix . $key;

            if ( isset( $array[$array_key] ) ) {
                $key = 'meta_' . $key;
                $this->$key = $array[$array_key];
            }
        }

        return $this;

    }

}
