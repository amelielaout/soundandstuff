<?php

class PortfolioProject extends FluxusPage {

    protected $META_PREFIX = 'fluxus_project_';
    protected $meta_data_defaults = array(
            'subtitle' => '',
            'link'     => '',
            'info'     => array()
        );

    function get_featured_media() {
        return PortfolioMedia::get_featured_media( $this->post_id );
    }

    function get_media() {
        return PortfolioMedia::all( $this->post_id );
    }

    function get_tags() {
        return wp_get_post_terms( $this->post_id, 'fluxus-project-type' );
    }

    static function all( $args = array() ) {

        $defaults = array(
            'post_type'      => 'fluxus_portfolio',
            'posts_per_page' => -1,
            'orderby'        => 'ID',
            'post_status'    => 'any',
            'order'          => 'DESC'
        );

        $args = array_merge( $defaults, $args );

        $posts = get_posts( $args );

        if ( $posts ) {

            foreach ( $posts as $k => $post ) {
                $posts[$k] = new PortfolioProject( $post->ID );
            }

            return $posts;

        } else {

            return array();

        }

    }

    static function posts_to_projects( $posts ) {

        $projects = array();

        if ( is_array( $posts ) ) {
            foreach ( $posts as $post ) {
                if ( $post->ID ) {
                    $projects[] = new PortfolioProject( $post->ID );
                }
            }
        }

        return $projects;

    }

    static function find_related_projects( $current_project_id, $number_to_display = 8 ) {

        $all = fluxus_query_portfolio();
        wp_reset_query();

        $count = count( $all );

        // if we don't have enough projects, return all
        if ( $count <= $number_to_display ) {
            return self::posts_to_projects( $all );
        }

        $current_project_index = false;
        foreach ( $all as $index => $project ) {
            if ( $current_project_id == $project->ID ) {
                $current_project_index = $index;
                break;
            }
        }

        // if we can't find current project, return all
        if ( $current_project_index === false ) {
            return self::posts_to_projects( $all );
        }

        if ( $current_project_index + $number_to_display > $count ) {
            /**
             * Means that our current project is in the last N.
             * Let's return last N.
             */
            return self::posts_to_projects( array_slice( $all, $count - $number_to_display ) );
        }

        $slice_offset = $current_project_index - 3;

        if ( $slice_offset < 0 ) {
            $slice_offset = 0;
        }

        return self::posts_to_projects( array_slice( $all, $slice_offset, $number_to_display ) );

    }

}

