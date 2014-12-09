<?php
/**
 * File contains definition and implementation of Theme Options.
 *
 * @since fluxus 1.0
 */


/**
 * Returns all supported Social Networks.
 */
function fluxus_get_social_networks() {

    $networks = array(
            'Dribbble' => 'dribbble',
            'Facebook' => 'facebook',
            'Google Plus' => 'gplus',
            'Flickr' => 'flickr',
            'Pinterest' => 'pinterest',
            'Twitter' => 'twitter',
            'Tumblr' => 'tumblr',
            'Vimeo' => 'vimeo',
            'Linkedin' => 'linkedin',
            'Instagram' => 'instagram',
        );

    return $networks;

}


/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 */
function optionsframework_options() {

    /**
     * Options page: General Settings
     */
    $options = array();

    $options[] = array(
            'name' => __( 'General Settings', 'fluxus' ),
            'type' => 'heading'
        );

    $options[] = array(
            'name' => __( 'Custom logo', 'fluxus' ),
            'id' => 'fluxus_logo',
            'type' => 'upload',
            'desc' => __( 'The maximum width is 190px, the recommended height is under 40px.', 'fluxus' )
        );

    $options[] = array(
            'name' => __( 'Custom logo (retina)', 'fluxus' ),
            'id' => 'fluxus_logo_retina',
            'type' => 'upload',
            'desc' => __( 'Here you should upload two times bigger version of your logo. It will be displayed on high resolution devices (such as new iPads and iPhones). This will make logo look crisp.', 'fluxus' )
        );

    $options[] = array(
            'name' => __( 'Footer Copyright', 'fluxus' ),
            'desc' => __( 'Copyright displayed in the bottom. You can use HTML tags.' , 'fluxus' ),
            'id' => 'fluxus_copyright_text',
            'std' => '&copy; Fluxus Wordpress Theme',
            'type' => 'text',
            'html' => true
        );

    $options[] = array(
            'name' => __( 'Favicon', 'fluxus' ),
            'desc' => __( 'Upload a 16x16 sized png/gif image that will be used as a favicon.' , 'fluxus' ),
            'id' => 'fluxus_favicon',
            'type' => 'upload'
        );

    $options[] = array(
            'name' => __( 'Facebook Image', 'fluxus' ),
            'desc' => __( 'Image used on Facebook timeline when someone likes the website. If visitor likes a content page (blog post / gallery) then image will be taken automatically from content. Should be at least 200x200 in size.' , 'fluxus' ),
            'id' => 'fluxus_facebook_image',
            'type' => 'upload'
        );

    $options[] = array(
            'name' => __( 'Site Description', 'fluxus' ),
            'desc' => __( 'Give a short description of your website. This is visible in search results and when sharing your website. The description will not be used on content pages like blog post or portfolio project.' , 'fluxus' ),
            'id' => 'fluxus_site_description',
            'type' => 'textarea'
        );

    $options[] = array(
            'name' => __( 'Disable Fluxus Meta Tags', 'fluxus' ),
            'desc' => __( 'By default Fluxus theme will create page description and page thumbnail meta tags. If you use any SEO plugin, then you should disable Fluxus meta tag generation.' , 'fluxus' ),
            'id' => 'fluxus_disable_meta_tags',
            'std' => '0',
            'type' => 'checkbox'
        );

    $options[] = array(
            'name' => __( 'Tracking code', 'fluxus' ),
            'id' => 'fluxus_tracking_code',
            'desc' => __( 'Paste your Google Analytics or any other tracking code. Important: do not include &lt;script&gt;&lt;/script&gt; tags.' , 'fluxus' ),
            'type' => 'textarea'
        );

    /**
     * Options page: Social
     */
    $options[] = array(
            'name' => __( 'Social', 'fluxus' ),
            'type' => 'heading'
        );

    $options[] = array(
            'name' => __( 'Enable share buttons', 'fluxus' ),
            'desc' => __( 'Show social sharing buttons in the footer.' , 'fluxus' ),
            'id' => 'fluxus_share_enabled',
            'std' => '1',
            'type' => 'checkbox'
        );

    $social_networks = array(
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'googleplus' => 'Google+',
            'pinterest' => 'Pinterest',
            'linkedin' => 'LinkedIn',
            'digg' => 'Digg',
            'delicious' => 'Delicious',
            'stumbleupon' => 'StumbleUpon'
        );

    $social_networks_defaults = array(
            'facebook' => 1,
            'twitter' => 1,
            'googleplus' => 1
        );

    $options[] = array(
            'name' => __( 'Sharing Networks', 'fluxus' ),
            'desc' => __( 'Select social networks on which you want to share your website.' , 'fluxus' ),
            'id' => 'fluxus_share_services',
            'std' => false,
            'type' => 'multicheck',
            'options' => $social_networks,
            'str' => $social_networks_defaults
        );

    $options[] = array(
            'name' => __( 'Enable social networks', 'fluxus' ),
            'desc' => __( 'Show social network links in the footer.' , 'fluxus' ),
            'id' => 'fluxus_social_enabled',
            'std' => '0',
            'type' => 'checkbox'
        );

    foreach ( fluxus_get_social_networks() as $label => $network) {

        $options[] = array(
                'name' => $label . ' ' . __( 'URL', 'fluxus' ),
                'id'   => 'fluxus_' . $network . '_url',
                'type' => 'text'
            );

    }


    /**
     * Options page: Style
     */
    $options[] = array(
            'name' => __( 'Style', 'fluxus' ),
            'type' => 'heading'
        );

    $css_color_dir = get_template_directory() . '/css/skins/';
    $css_select = array();

    if ( is_dir( $css_color_dir ) ) {
        if ( $dh = opendir( $css_color_dir ) ) {
            while ( ( $file = readdir( $dh ) ) !== false ) {
                if ( pathinfo( $file, PATHINFO_EXTENSION ) == 'css' ) {
                    $css_select[ $file ] = $file;
                }
            }
            closedir($dh);
        }
    }

    $options[] = array(
            'name' => __( 'Stylesheet', 'fluxus' ),
            'id' => 'fluxus_stylesheet',
            'type' => 'select',
            'class' => 'mini',
            'options' => $css_select,
            'std' => 'light.css'
        );

    $options[] = array(
            'name' => __( 'Custom CSS', 'fluxus' ),
            'id' => 'fluxus_custom_css',
            'desc' => __( 'Add your custom CSS rules here. Note: it is better to use user.css file (located in your theme\'s css directory) to add custom rules.' , 'fluxus' ),
            'type' => 'textarea'
        );

    return $options;
}


/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */
function optionsframework_option_name() {

    // This gets the theme name from the stylesheet
    $themename = get_option( 'stylesheet' );
    $themename = preg_replace("/\W/", "_", strtolower($themename) );

    $optionsframework_settings = get_option( 'optionsframework' );
    $optionsframework_settings['id'] = $themename;
    update_option( 'optionsframework', $optionsframework_settings );

}


/**
 * ------------------------------------------------------------------------------------
 * The functions below implement Theme Options.
 * ------------------------------------------------------------------------------------
 */


/**
 * Tracking code
 */
function fluxus_tracking_code() {
    $option = of_get_option( 'fluxus_tracking_code' );
    if ( ! empty( $option ) ) {
        echo '<script>' . $option . '</script>';
    }
}

if ( ! is_admin() && ! is_preview() ) {
    add_action( 'wp_footer', 'fluxus_tracking_code', 1000 );
}


/**
 * Custom CSS
 */
function fluxus_custom_css() {
    $option = of_get_option( 'fluxus_custom_css' );
    if ( ! empty( $option ) ) {
        echo "<style>\n" . $option . "\n</style>\n";
    }
}

if ( ! is_admin() ) {
    add_action( 'wp_head', 'fluxus_custom_css' );
}


/**
 * Favicon
 */
function fluxus_favicon() {
    $option = of_get_option( 'fluxus_favicon' );
    if ( ! empty( $option ) ) {
        echo '<link rel="shortcut icon" href="' . esc_url( $option ) . '" />' . "\n";
    }
}
add_action( 'wp_head', 'fluxus_favicon' );


function fluxus_options_init() {

   global $fluxus_theme;

    $disable_meta = of_get_option( 'fluxus_disable_meta_tags' );
    if ( $disable_meta ) {

        $fluxus_theme->options['enable_meta_description'] = false;
        $fluxus_theme->options['enable_meta_image'] = false;

    } else {

        // Page Thumbnail
        $option = of_get_option( 'fluxus_facebook_image' );
        if ( $option && ! is_single() ) {
            $fluxus_theme->set_image( $option );
        }

        // Page Description
        $option = of_get_option( 'fluxus_site_description' );
        if ( $option && ! is_single() ) {
            $fluxus_theme->set_description( $option );
        }

    }

}
add_action( 'init', 'fluxus_options_init' );


/**
 * Share widget
 */
function fluxus_get_social_share( $args = array() ) {

    if ( is_404() ) {
        return false;
    }

    $option = of_get_option( 'fluxus_share_enabled' );

    if ( $option && $option == '1' ) {

        $share_services = of_get_option( 'fluxus_share_services' );
        $data_services = array();

        if ( is_array( $share_services ) && count( $share_services ) ) {

            foreach ( $share_services as $key => $service ) {
                if ( $service ) {
                    $key = $key == 'googleplus' ? 'googlePlus' : $key;
                    $data_services[] = $key;
                }
            }

        }

        if ( count( $data_services ) == 0 ) {
            return false;
        }

        $defaults = array(
                'data-url' => array(
                        esc_url( get_permalink() )
                    ),
                'data-curl' => array(
                        esc_url( get_template_directory_uri() . '/lib/sharrre.php' )
                    ),
                'data-services' => array(
                        join( ',', $data_services )
                    ),
                'data-title' => array(
                        __( 'Share', 'fluxus' )
                    ),
                'class' => array(
                        'sharrre'
                    )
            );

        $args = array_merge( $defaults, $args );

        $html = '<div' . it_array_to_attributes( $args ) . '></div>';

        return $html;

    } else {

        return false;

    }

}


/**
 * Share widget that is located in the footer.
 */
function fluxus_footer_social_share() {
    $args = array(
            'id' => 'sharrre-footer',
            'data-buttons-title' => __( 'Share this page', 'fluxus' )
        );
    $html = fluxus_get_social_share( $args );
    if ( $html ) {
        echo $html;
    }
}

if ( !is_admin() && !is_404() ) {
    add_action( 'footer_social', 'fluxus_footer_social_share' );
}


/**
 * Social networks
 */
function fluxus_social_networks() {

    $option = of_get_option( 'fluxus_social_enabled' );

    if ( $option && $option == '1' ) {

        $html = '';

        foreach ( fluxus_get_social_networks() as $network) {

            $option = of_get_option( 'fluxus_' . $network . '_url' );
            $title = esc_attr( sprintf( __( 'Connect on %s', 'fluxus' ), ucfirst( $network ) ) );

            if ( !empty( $option ) ) {
                $html .= '<a class="icon-social icon-' . $network . '-circled" href="' . esc_url ( $option ) . '" target="_blank" title="' . $title . '" rel="nofollow"></a>';
            }

        }

        if ( !empty( $html ) ) : ?>
            <div class="social-networks"><?php echo $html; ?></div><?php
        endif;

    }

}

if ( ! is_admin() ) {
    add_action( 'footer_social', 'fluxus_social_networks' );
}


/**
 * CSS Stylesheet
 */
function fluxus_css_stylesheet() {

    $color_css = of_get_option( 'fluxus_stylesheet' );

    if ( $color_css ) {
        wp_enqueue_style( 'fluxus-color', get_template_directory_uri() . '/css/skins/' . (string) $color_css );
    }

}

if ( ! is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'fluxus_css_stylesheet', 1000 );
}


/**
 * This function is called when saving custom logo.
 * It will try to retrieve logo size and save it for later use.
 */
function of_update_option_fluxus_logo( $value, $id ) {
    return of_update_image_option( $value, $id );
}


/**
 * This function is called when saving RETINA custom logo.
 * It will try to retrieve logo size and save it for later use.
 */
function of_update_option_fluxus_logo_retina( $value, $id ) {
    return of_update_image_option( $value, $id );
}


/**
 * Retrieves image size and saves it as transient.
 */
function of_update_image_option( $value, $id ) {

    if ( $value ) {

        $size = getimagesize( $value );

        if ( is_array( $size ) && isset( $size[0] ) && isset( $size[1] ) &&
             is_numeric( $size[0] ) && is_numeric( $size[1] ) &&
             $size[0] && $size[1] ) {

            set_transient( 'fluxus_option_' . $id, array( $size[0], $size[1] ) );

        } else {

            /**
             * If we are unable to set image data, then store the error.
             */
            set_transient( 'fluxus_option_' . $id, 'unable to get image data' );

        }

    } else {

        delete_transient( 'fluxus_option_' . $id );

    }

    return $value;

}


/**
 * Returns an array with logo information: url, size.
 */
function fluxus_get_logo() {

    $logo = of_get_option( 'fluxus_logo' );

    $output = array();

    if ( $logo ) {

        $output[0] = $logo;

        $size = get_transient( 'fluxus_option_fluxus_logo' );

        /**
         * If transient does not exist, then let's try to set it.
         */
        if ( $size === false ) {
            of_update_image_option( $output[0], 'fluxus_logo' );
            $size = get_transient( 'fluxus_option_fluxus_logo' );
        }

        if ( is_array( $size ) ) {

            $output[1] = $size[0];
            $output[2] = $size[1];
            $output[3] = 'width="' . esc_attr( $size[0] ) . '"';
            $output[4] = 'height="' . esc_attr( $size[1] ) . '"';
            $output['size'] = ' ' . $output[3] . ' ' . $output[4] . ' ';

        } else {

            // populate array with empty values
            $output[1] = '';
            $output[2] = '';
            $output[3] = '';
            $output[4] = '';
            $output['size'] = '';

        }

    }

    return $output;

}


/**
 * Returns an array with RETINA logo information: url, size.
 */
function fluxus_get_logo_retina() {

    $logo = of_get_option( 'fluxus_logo_retina' );

    $output = array();

    if ( $logo ) {

        $output[0] = $logo;

        $size = get_transient( 'fluxus_option_fluxus_logo_retina' );

        /**
         * If transient does not exist, then let's try to set it.
         */
        if ( $size === false ) {
            of_update_image_option( $output[0], 'fluxus_logo_retina' );
            $size = get_transient( 'fluxus_option_fluxus_logo_retina' );
        }

        if ( is_array( $size ) ) {

            $output[1] = round( $size[0] / 2 );
            $output[2] = round( $size[1] / 2 );
            $output[3] = 'width="' . esc_attr( $output[1] ) . '"';
            $output[4] = 'height="' . esc_attr( $output[2] ) . '"';
            $output['size'] = ' ' . $output[3] . ' ' . $output[4] . ' ';

        } else {

            // populate array with empty values
            $output[1] = '';
            $output[2] = '';
            $output[3] = '';
            $output[4] = '';
            $output['size'] = '';

        }

    }

    return $output;

}

