<?php
/**
 * Contains various functions for general tasks.
 *
 * @since intheme 1.0
 */


if ( ! function_exists( 'fb' ) ) {

    include_once( dirname( __FILE__ ) . '/firephp/fb.php' );

    if ( ! function_exists( 'fb' ) ) {

        function fb( $object ) {
            if ( defined(FB) ) {
                FB::log( $object );
            }
        }

    }

}


if ( ! function_exists( 'it_get_post_thumbnail' ) ) {

    function it_get_post_thumbnail( $post_id, $size = 'thumbnail' ) {

        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );

        if ( $image ) {
            return $image[0];
        } else {
            return '';
        }

    }

}


if ( ! function_exists( 'it_get_key_value' ) ) {

    function it_get_key_value( $array, $value, $default = '' ) {
        if ( isset($array[$value]) ) {
            return $array[$value];
        }
        return $default;
    }

}


if ( ! function_exists( 'it_regenerate_wp_images' ) ) {

    function it_regenerate_wp_images() {

        return false;

        set_time_limit( 0 );

        echo "<pre>";
        echo "Regenerating thumbnails...\n";

        $args = array(
            'post_type' => 'attachment',
            'numberposts' => -1,
            'post_status' => null
        );

        $attachments = get_posts($args);
        if ( $attachments ) {
            echo count( $attachments ) . " images were found.\n";
            foreach ( $attachments as $attachment ) {
                $full_size_path = get_attached_file( $attachment->ID );

                if ( false === $full_size_path || ! file_exists( $full_size_path ) ) {
                    echo "Image ID " . $attachment->ID . " was not found.\n";
                    continue;
                }

                $meta_data = wp_generate_attachment_metadata( $attachment->ID, $full_size_path );

                if ( is_wp_error( $meta_data ) ) {
                    echo "Image ID " . $attachment->ID . " raised an error: " . $mata_data->get_error_message() . "\n";
                    continue;
                }

                if ( empty( $meta_data ) ) {
                    echo "Image ID " . $attachment->ID . " failed with unknown reason\n";
                    continue;
                }

                wp_update_attachment_metadata( $attachment->ID, $meta_data );

            }
            echo "Done.";
        }

        echo "</pre>";

    }

}


if ( ! function_exists( 'it_flush_rewrite_rules' ) ) {

    function it_flush_rewrite_rules() {

        global $wp_rewrite;

        $wp_rewrite->flush_rules();
        flush_rewrite_rules();

    }

}


if ( ! function_exists( 'it_find_page_by_template' ) ) {

    function it_find_page_by_template( $template_filename, $args = array() ) {

        $defaults = array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'meta_query' => array(
                    array(
                        'key' => '_wp_page_template',
                        'value' => $template_filename,
                        'compare' => '='
                    )
                )
            );

        $args = wp_parse_args( $args, $defaults );

        return get_posts( $args );

    }

}


/**
 * Checks, if page has a specified template assigned to it.
 */
if ( ! function_exists( 'it_is_template' ) ) {

    function it_is_template( $post_id, $template_filename ) {

        $post_template = get_post_meta( $post_id, '_wp_page_template', true );

        return $template_filename == $post_template;

    }

}


/**
 * Returns post ID from $_GET / $_POST arrays.
 */
if ( ! function_exists( 'it_get_post_id' ) ) {

    function it_get_post_id() {

        if ( isset($_GET['post']) ) {
            return $_GET['post'];
        }

        if ( isset($_POST['post_ID']) ) {
            return $_POST['post_ID'];
        }

        if ( isset($_GET['post_ID']) ) {
            return $_GET['post_ID'];
        }

        if ( isset($_POST['post']) ) {
            return $_POST['post'];
        }

        if ( isset($_POST['post_id']) ) {
            return $_POST['post_id'];
        }

        if ( isset($_GET['post_id']) ) {
            return $_GET['post_id'];
        }

        return false;

    }

}


if ( ! function_exists( 'it_array_to_select_options' ) ) {

    function it_array_to_select_options( $array, $active = '' ) {

        $html = '';

        foreach ( $array as $value => $label ) {

            $selected = $active == $value ? ' selected="selected"' : '';
            $html .= '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . $label . '</option>';

        }

        return $html;

    }

}


if ( ! function_exists( 'it_array_to_attributes' ) ) {

    function it_array_to_attributes( $array ) {

        $html = '';

        foreach ( $array as $attr => $value ) {

            if ( ! is_array( $value ) ) {
                $value = array( $value );
            }

            if ( 'style' == $attr ) {
                $html .= ' style="' . esc_attr( join( '; ', $value ) ) . '"';
                continue;
            }

            if ( 'class' == $attr ) {
                $html .= ' class="' . esc_attr( join( ' ', array_unique( $value ) ) ) . '"';
                continue;
            }

            $html .= ' ' . esc_attr( $attr ) . '="' . esc_attr( join( ' ', $value ) ) . '"';

        }

        return $html;

    }

}



if ( ! function_exists( 'it_is_wp_clean' ) ) {

    function it_is_wp_clean() {

        $posts = wp_count_posts( 'post' );
        $pages = wp_count_posts( 'page' );

        if ( ( $posts->publish == 1 ) && ( $pages->publish == 1 ) ) {
            echo 'fresh';
        }

        return ( $posts->publish == 1 ) && ( $pages->publish == 1 );

    }

}


if ( ! function_exists( 'it_check_save_action' ) ) {

    function it_check_save_action( $id, $post_type = 'post', $no_inline_save = true ) {

        // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
        // to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return false;
        }

        if ( ! isset( $_POST['post_type'] ) ) {
            return false;
        }

        if ( $no_inline_save ) {
            if ( isset( $_POST['action'] ) && ( 'inline-save' == $_POST['action'] ) ) {
                return false;
            }
        }

        // Check permissions
        if ( $post_type == $_POST['post_type'] ) {

            if ( $post_type == 'page' ) {
                if ( ! current_user_can( 'edit_page', $id ) ) {
                    return false;
                }
            } else {
                if ( ! current_user_can( 'edit_post', $id ) ) {
                    return false;
                }
            }

        } else {
            // it's not our post type, we are good to go.
            return $id;
        }

        return true;

    }

}


if ( ! function_exists( 'it_key_is_numeric' ) ) {

    function it_key_is_numeric( $array, $key ) {
        return is_array( $array ) && isset( $array[$key] ) && is_numeric( $array[$key] );
    }

}

if ( ! function_exists( 'it_key_is_array' ) ) {

    function it_key_is_array( $array, $key ) {
        return is_array( $array ) && isset( $array[$key] ) && is_array( $array[$key] );
    }

}