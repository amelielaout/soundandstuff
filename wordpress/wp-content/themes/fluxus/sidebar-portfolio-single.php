<?php
/**
 * Portfolio Single Sidebar.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

$project = new PortfolioProject( get_the_ID() );

?>
<div class="sidebar sidebar-portfolio-single widget-area">

    <?php do_action( 'before_sidebar' ); ?>

    <div class="scroll-container">
        <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
        <div class="viewport">
            <div class="overview"><?php

                if ( $project->exists() ) : ?>
                    <hgroup><?php

                        if ( $project->meta_subtitle ) : ?>
                            <h2 class="subtitle"><?php echo $project->meta_subtitle; ?></h2><?php
                        endif;

                        ?>
                        <h1 class="title"><?php the_title(); ?></h1>
                    </hgroup><?php
                endif;

                $content = trim( strip_tags( $post->post_content ) );
                if ( ! empty( $content ) ) : ?>
                    <div class="widget">
                        <div class="textwidget">
                            <?php the_content(); ?>
                        </div>
                    </div><?php
                endif;

                if ( $project->meta_link ) : ?>
                    <aside class="widget widget-project-info">
                        <div class="decoration"></div>
                        <h3 class="widget-title"><?php _e( 'Project Info', 'fluxus' ); ?></h3>
                        <a class="external-link" href="<?php echo esc_url( $project->meta_link ); ?>">
                            <?php _e( 'Visit website', 'fluxus' ); ?>
                        </a>
                    </aside><?php
                endif;

                if ( $project->meta_info && is_array( $project->meta_info ) &&
                     isset( $project->meta_info[0] ) && $project->meta_info[0] ) :

                    foreach ( $project->meta_info as $info ) : ?>
                        <aside class="widget widget-project-custom-info">
                            <div class="decoration"></div>
                            <h3 class="widget-title"><?php echo $info['title']; ?></h3>
                            <div class="widget-content"><?php
                                echo $info['content']; ?>
                            </div>
                        </aside><?php
                    endforeach;

                endif;

                if ( ! dynamic_sidebar( 'sidebar-portfolio-single' ) ) :

                    the_widget( 'Fluxus_Widget_Project_Types', null, fluxus_get_default_widget_params() );

                endif; ?>

            </div>

        </div>

    </div>

</div>