<?php
/**
 * Portfolio Single Sidebar.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

?>
<div class="sidebar sidebar-portfolio widget-area">

    <?php do_action( 'before_sidebar' ); ?>

    <div class="scroll-container">
        <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
        <div class="viewport">
            <div class="overview"><?php

                if ( ! dynamic_sidebar( 'sidebar-portfolio' ) ) :

                    the_widget( 'Fluxus_Widget_Project_Types', null, fluxus_get_default_widget_params() );

                endif; ?>

            </div>
        </div>
    </div>

</div>