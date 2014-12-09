<?php

class GridPortfolio extends FluxusPage {

    protected $META_PREFIX = 'fluxus_portfolio_';

    protected $meta_data_defaults = array(
            'grid_size'          => '4 3'
        );

    function get_grid_column_count() {

        $grid_size = explode( ' ', $this->meta_grid_size );

        if ( is_array( $grid_size ) && count( $grid_size ) > 1 ) {
            return $grid_size[0];
        }

        return '4';

    }

    function get_grid_row_count() {

        $grid_size = explode( ' ', $this->meta_grid_size );

        if ( is_array( $grid_size ) && count( $grid_size ) > 1 ) {
            return $grid_size[1];
        }

        return '3';

    }

}