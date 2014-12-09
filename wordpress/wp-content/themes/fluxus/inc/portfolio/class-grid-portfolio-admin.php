<?php

class GridPortfolioAdmin extends FluxusAdminPage {

    function __construct( $post_id ) {

        parent::__construct( $post_id );

        // Add options meta box
        add_meta_box( 'fluxus-portfolio-grid-meta', __( 'Grid Options', 'fluxus' ),
                      array( $this, 'admin_options_content' ), 'page', 'normal', 'low' );

        add_action( 'save_post', array( $this, 'admin_options_save' ), 2 );

    }

    function admin_options_content() {

        $grid = new GridPortfolio( $this->post_id );

        ?>
        <div class="fluxus-meta-field">
            <label for="fluxus_portfolio_grid_size"><?php _e( 'Grid size', 'fluxus' ); ?></label>
            <div class="field"><?php

                $size_options = array(
                        '5 4' => '5 columns, 4 rows',
                        '5 3' => '5 columns, 3 rows',
                        '4 3' => '4 columns, 3 rows',
                        '3 3' => '3 columns, 3 rows',
                        '3 2' => '3 columns, 2 rows'
                    );

                ?>
                <select name="fluxus_portfolio_grid_size">
                    <?php echo it_array_to_select_options( $size_options, $grid->meta_grid_size ); ?>
                </select>
            </div>
        </div>
        <?php

    }

    function admin_options_save( $post_id ) {

        if ( ! it_check_save_action( $post_id, 'page' ) ) {
            return $post_id;
        }

        $grid = new GridPortfolio( $this->post_id );
        $grid->update_from_array( $_POST )->save();

    }

}
