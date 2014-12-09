<?php
/**
 * Appreciate Wordpress post plugin.
 *
 * All the appreciations are logged into custom table.
 * Only one appreciation is allowed from same IP.
 *
 * @since fluxus 1.0
 */



class IT_Appreciate {

    public static $version = '1.0';
    public static $table   = 'appreciate';
    public static $meta_key_count = '_appreciate_count';

    function __construct() {

        add_action( 'after_switch_theme', array( $this, 'check_install' ) );

        add_action( 'wp_ajax_appreciate', array( $this, 'ajax_appreciate' ) );
        add_action( 'wp_ajax_nopriv_appreciate', array( $this, 'ajax_appreciate' ) );

    }

    function get_ip() {

        if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
            return preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR'] );
        } else {
            return 'unknown';
        }

    }

    function get_count( $post_id ) {

        if ( !is_numeric( $post_id ) ) {
            return 0;
        }

        $count = get_post_meta( $post_id, self::$meta_key_count, true );

        if ( ! is_numeric( $count ) ) {
            return 0;
        } else {
            return $count;
        }

    }

    function has_appreciated( $post_id ) {

        global $wpdb;
        $table = self::$table;

        $ip = $this->get_ip();

        $sql = "SELECT ID FROM $table WHERE `IP` = %s AND post_id = %d";
        $sql = $wpdb->prepare( $sql, $ip, $post_id );

        $found = $wpdb->get_var( $sql );

        return $found;

    }

    function ajax_appreciate() {

        global $wpdb;

        if ( isset( $_POST['post_id'] ) && is_numeric( $_POST['post_id'] ) ) {

            if ( $this->appreciate( $_POST['post_id'] ) ) {
                die( '1' );
            }

        }

        die( '0' );

    }

    function appreciate( $post_id ) {

        if ( $this->has_appreciated( $post_id ) ) {
            return false;
        }

        global $wpdb;

        $count = $this->get_count( $post_id );
        update_post_meta( $post_id, self::$meta_key_count, $count + 1 );

        // log IP
        $wpdb->insert( IT_Appreciate::$table, array(
                'ip' => $this->get_ip(),
                'post_id' => $post_id,
                'time' => current_time('mysql'),
            )
        );

        return true;

    }

    function check_install() {

        $version = get_option( '_appreciate_version', false );

        if ( ( $version === false ) || ( $version != self::$version ) ) {
            $this->install();
        }

    }

    function install() {

        global $wpdb;

        $charset_collate = '';

        if ( ! empty($wpdb->charset) ) {
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        }

        if ( ! empty($wpdb->collate) ) {
            $charset_collate .= " COLLATE $wpdb->collate";
        }

        $table = self::$table;

        $sql = "CREATE TABLE $table (
                    ID bigint(20) unsigned NOT NULL auto_increment,
                    post_id bigint(20) unsigned NOT NULL default '0',
                    IP varchar(15) NOT NULL default '',
                    time datetime NOT NULL default '0000-00-00 00:00:00',
                    PRIMARY KEY  (ID)
                ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( $sql );

        add_option( '_appreciate_version', self::$version );

    }

}

IT_Appreciate::$table = $wpdb->prefix . IT_Appreciate::$table;

global $appreciate;
$appreciate = new IT_Appreciate();

function fluxus_appreciate( $post_id, $args = array() ) {

    global $appreciate;

    $defaults = array(
            'class' => '',
            'title' => __( 'Appreciate', 'fluxus' ),
            'title_after' => __( 'Appreciated!', 'fluxus' )
        );

    $args = array_merge( $defaults, $args );

    $count = $appreciate->get_count( $post_id );

    $attr = array(
            'class' => array(),
            'data-id' => esc_attr( $post_id ),
            'data-ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
            'href' => '#',
            'data-count' => esc_attr( $count ),
            'data-title' => $args['title'],
            'data-title-after' => $args['title_after']
        );

    if ( !empty( $args['class'] ) ) {
        $attr['class'][] = $args['class'];
    }

    if ( $appreciate->has_appreciated( $post_id ) ) {
        $attr['class'][] = 'has-appreciated';
    }

    $attr['class'] = esc_attr( join( ' ', $attr['class'] ) );

    $attributes = '';
    foreach ( $attr as $key => $value ) {
        $attributes .= ' '. $key . '="' . $value . '"';
    }

    ?>
    <a<?php echo $attributes; ?>><span class="appreciate-title"><?php echo $args['title']; ?></span></a><?php

}

