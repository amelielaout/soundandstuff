<?php
/**
 * Displays options for contacts template.
 *
 * @since fluxus 1.0
 */


/**
 * Returns all database data that is related to contacts.
 */
function fluxus_contacts_get_data( $post_id ) {

    $latitude       = get_post_meta( $post_id, 'fluxus_map_latitude', true );
    $longitude      = get_post_meta( $post_id, 'fluxus_map_longitude', true );
    $api            = get_post_meta( $post_id, 'fluxus_map_api', true );
    $icon_latitude  = get_post_meta( $post_id, 'fluxus_map_icon_latitude', true );
    $icon_longitude = get_post_meta( $post_id, 'fluxus_map_icon_longitude', true );
    $icon_image     = get_post_meta( $post_id, 'fluxus_map_icon_image', true );
    $contacts       = get_post_meta( $post_id, 'fluxus_contact_data', true );

    return array(
            'latitude'          => $latitude,
            'longitude'         => $longitude,
            'api'               => $api,
            'icon_latitude'     => $icon_latitude,
            'icon_longitude'    => $icon_longitude,
            'icon_image'        => $icon_image,
            'contacts'          => $contacts
        );

}


/**
 * Meta box contents.
 */
function fluxus_contacts_meta_box_contents() {
    global $post;

    if (!$post) {
        return;
    }

    $data = fluxus_contacts_get_data( $post->ID );
    extract( $data );

    ?>
    <div class="fluxus-meta-field">
        <label for="fluxus_map_api"><?php _e( 'Google Maps API', 'fluxus' ); ?></label>
        <div class="field">
            <input type="text" name="fluxus_map_api" value="<?php echo esc_attr( $api ); ?>" />
        </div>
        <div class="notes"><?php
            printf( __( 'Google Maps API key is needed for maps to work. Get %s.', 'fluxus' ), '<a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">Google Maps API</a>' ); ?>
        </div>
    </div>
    <div class="fluxus-meta-group">
        <h2>
            <?php _e( 'Map center position', 'fluxus' ); ?>
        </h2>
        <div class="fluxus-meta-field">
            <label for="fluxus_map_latitude"><?php _e( 'Latitude', 'fluxus' ); ?></label>
            <div class="field">
                <input type="text" name="fluxus_map_latitude" value="<?php echo esc_attr( $latitude ); ?>" />
            </div>
            <div class="notes">
                <div class="group-notes"><?php
                    printf( __( 'Latitude and longitude of the point that will be in the center of the map. Use %s to get the coordinates.', 'fluxus' ), '<a href="http://itouchmap.com/latlong.html" target="_blank">itouchmap.com</a>' ); ?>
                </div>
            </div>
        </div>
        <div class="fluxus-meta-field">
            <label for="fluxus_map_longitude"><?php _e( 'Longitude', 'fluxus' ); ?></label>
            <div class="field">
                <input type="text" name="fluxus_map_longitude" value="<?php echo esc_attr( $longitude ); ?>" />
            </div>
        </div>
    </div>
    <div class="fluxus-meta-group">
        <h2><?php _e( 'Map icon', 'fluxus' ); ?></h2>
        <div class="fluxus-meta-field">
            <label for="fluxus_map_icon_latitude"><?php _e( 'Icon latitude', 'fluxus' ); ?></label>
            <div class="field">
                <input type="text" name="fluxus_map_icon_latitude" value="<?php echo esc_attr( $icon_latitude ); ?>" />
            </div>
        </div>
        <div class="fluxus-meta-field">
            <label for="fluxus_map_icon_longitude"><?php _e( 'Icon latitude', 'fluxus' ); ?></label>
            <div class="field">
                <input type="text" name="fluxus_map_icon_longitude" value="<?php echo esc_attr( $icon_longitude ); ?>" />
            </div>
        </div>
        <div class="fluxus-meta-field">
            <label for="fluxus_map_icon_image"><?php _e( 'Icon image url', 'fluxus' ); ?></label>
            <div class="field">
                <input type="text" name="fluxus_map_icon_image" value="<?php echo esc_attr( $icon_image ); ?>" />
            </div>
        </div>
    </div>
    <div class="fluxus-meta-group">
        <h2><?php _e( 'Contacts information', 'fluxus' ); ?></h2>
        <table class="fluxus-table fluxus-contact-information">
            <thead>
                <tr>
                    <td><?php _e( 'Title', 'fluxus' ); ?></td>
                    <td><?php _e( 'Content', 'fluxus' ); ?></td>
                </tr>
            </thead>
            <tbody><?php
                if ( $contacts && is_array($contacts) ) :
                    foreach ( $contacts as $contact ) : ?>
                        <tr>
                            <td>
                                <input type="text" name="fluxus_contact_info_title[]" value="<?php echo esc_attr( $contact['title'] ); ?>" />
                            </td>
                            <td>
                                <textarea name="fluxus_contact_info_content[]"><?php echo $contact['content']; ?></textarea>
                            </td>
                        </tr><?php
                    endforeach;
                endif; ?>
                <tr class="add-element">
                    <td colspan="2">
                        <?php _e( 'To add contact information enter the title and content fields below.', 'fluxus' ); ?>
                    </td>
                </tr>
                <tr>
                    <td><input type="text" name="fluxus_contact_info_add_title" value="" /></td>
                    <td><textarea name="fluxus_contact_info_add_content"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="#" id="fluxus-add-contact" class="button-secondary"><?php _e( 'Add contact information', 'fluxus' ); ?></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}


/**
 * Save meta box.
 */
function fluxus_contacts_meta_box_save( $post_id ) {

    /**
     * verify if this is an auto save routine. If it is our form has not been submitted,
     * so we dont want to do anything.
     */

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return $post_id;
    }

    /**
     * Check permissions.
     */
    if ( isset($_POST['post_type']) && ('page' == $_POST['post_type']) ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }
    } else {
        return $post_id;
    }

    $keys = array(
            'fluxus_map_latitude',
            'fluxus_map_longitude',
            'fluxus_map_api',
            'fluxus_map_icon_latitude',
            'fluxus_map_icon_longitude',
            'fluxus_map_icon_image',
        );

    foreach ( $keys as $key ) {
        $value = isset($_POST[$key]) ? $_POST[$key] : '';
        update_post_meta( $post_id, $key, $value );
    }

    if ( isset($_POST['fluxus_contact_info_title']) && is_array($_POST['fluxus_contact_info_title']) ) {
        $titles = $_POST['fluxus_contact_info_title'];
        $contents = $_POST['fluxus_contact_info_content'];

        $data = array();

        foreach ( $titles as $index => $title ) {

            $data[] = array(
                    'title' => $title,
                    'content' => $contents[$index]
                );

        }

        update_post_meta( $post_id, 'fluxus_contact_data', $data );
    } else {
        update_post_meta( $post_id, 'fluxus_contact_data', array() );
    }

}
add_action( 'save_post', 'fluxus_contacts_meta_box_save' );


/**
 * Add meta box to admin area.
 */
function fluxus_contacts_add_meta_box() {
    add_meta_box(
            'fluxus_contacts_meta_box',
            __( 'Contacts Page', 'fluxus' ),
            'fluxus_contacts_meta_box_contents',
            'page',
            'normal'
        );
}

/**
 * Initialize admin side.
 */
function fluxus_contacts_admin_init() {

    $post_id = it_get_post_id();

    if ( $post_id ) {

        /**
         * Initialize scripts & styles.
         */
        wp_enqueue_script( 'fluxus-wp-admin-contacts', get_template_directory_uri() . '/js/wp-admin/contacts.js' );

        /**
         * If current type is contacts, then add meta box to admin area.
         */
        if ( it_is_template( $post_id, 'template-contacts.php' ) ) {
            add_action( 'add_meta_boxes', 'fluxus_contacts_add_meta_box' );
        }

    }

}
add_action( 'admin_init', 'fluxus_contacts_admin_init', 1 );


function fluxus_contacts_has_map( $post_id ) {

    $data = fluxus_contacts_get_data( $post_id );

    return !empty($data['api']) && !empty($data['latitude']) && !empty($data['longitude']);

}

/**
 * Add Google Maps script, if API key is specified.
 */
function fluxus_contacts_enqueue_google_maps() {

    global $post;

    /**
     * If current page has contacts template, then see if we need to enqueue
     * Google Maps script.
     */
    if ( $post ) {

        if ( it_is_template( $post->ID, 'template-contacts.php' ) ) {

            if ( fluxus_contacts_has_map( $post->ID ) ) {

                $data = fluxus_contacts_get_data( $post->ID );

                wp_enqueue_script( 'google-maps-api3', 'https://maps.googleapis.com/maps/api/js?key=' . $data['api'] . '&sensor=false' );
                wp_enqueue_script( 'fluxus-maps', get_template_directory_uri() . '/js/maps.js', array( 'google-maps-api3', 'jquery' ) );

            }

        }

    }

}


