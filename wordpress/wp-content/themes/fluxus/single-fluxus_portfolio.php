<?php
/**
 * Portfolio single project template.
 */

fluxus_add_html_class( 'horizontal-page' );

the_post();

$project = new PortfolioProject( get_the_ID() );
$project_media = $project->get_media();
$featured = $project->get_featured_media();

if ( $featured ) {
    global $fluxus_theme;

    // Use featured image as page thumbnail.
    $image = '';
    if ( $featured->is_image() ) {
        $image = $featured->get_image_data( 'fluxus-max' );
    } else {
        $image = $featured->get_video_thumbnail( 'fluxus-max' );
    }

    if ( $image ) {
        $fluxus_theme->set_image( $image['src'] );
    }

    // Use project excerpt as page description.
    $excerpt = get_the_excerpt();
    if ( $excerpt ) {
        $fluxus_theme->set_description( $excerpt );
    }
}

get_header();

?>
<div id="main" class="site site-with-sidebar">
    <div id="content" class="site-content"><?php

        if ( $project_media ) : ?>

            <article class="portfolio-single horizontal-content" data-loading="<?php echo esc_attr( __( 'Please wait...', 'fluxus' ) ); ?>"><?php

                foreach ( $project_media as $media_item ) :

                    if ( ! $media_item->meta_published ) continue;

                    if ( $media_item->is_image() ) :

                        $image = $media_item->get_image_data( 'fluxus-max' );

                        if ( ! $image ) continue;

                        $attr = array(
                                'class'       => 'horizontal-item wrap-image',
                                'data-width'  => $image['width'],
                                'data-height' => $image['height']
                            );

                        if ( $image['width'] && $image['height'] ) {
                            $attr['data-ratio'] = $image['width'] / $image['height'];
                        }

                        ?>
                        <div <?php echo it_array_to_attributes( $attr ); ?>>
                            <a href="<?php echo esc_url( $image['src'] ); ?>" class="project-image-link">
                                <img class="image" src="<?php echo esc_url( $image['src'] ); ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" alt="<?php echo esc_attr( $media_item->get_alt() ); ?>" />
                            </a><?php

                            if ( $media_item->meta_description ) : ?>
                                <span class="description">
                                    <?php echo nl2br( $media_item->meta_description ); ?>
                                </span><?php
                            endif;

                            ?>
                        </div><?php

                    else:

                        if ( ! $media_item->meta_embed ) continue;

                        $video_size = $media_item->get_video_size();

                        // Don't show video, if there is no width and height set
                        if ( ! $video_size['width'] || ! $video_size['height'] ) {
                            continue;
                        }

                        $attr = array(
                                'class'       => 'horizontal-item wrap-video',
                                'data-width'  => $video_size['width'],
                                'data-height' => $video_size['height'],
                                'data-ratio'  => $video_size['ratio']
                            );

                        ?>
                        <div <?php echo it_array_to_attributes( $attr ); ?>>
                            <?php echo $media_item->meta_embed; ?>
                        </div><?php

                    endif;

                endforeach;


                /**
                 * Portfolio navigation & sharing.
                 */
                ?>
                <nav class="portfolio-navigation">
                    <header>
                        <h3><?php _e( 'Like this project?', 'fluxus' ); ?></h3>
                        <div class="feedback-buttons"><?php

                            $args = array(
                                    'class' => 'btn-appreciate',
                                    'title' => __( 'Appreciate', 'fluxus' ),
                                    'title_after' => __( 'Appreciated', 'fluxus' )
                                );
                            fluxus_appreciate( $post->ID, $args );

                            $args = array(
                                    'id' => 'sharrre-project',
                                    'data-buttons-title' => array(
                                            __( 'Share this project', 'fluxus' )
                                        )
                                );

                            $sharrre = fluxus_get_social_share( $args );

                            if ( $sharrre ) : ?>
                                <span class="choice"><span><?php _e( 'Or', 'fluxus' ); ?></span></span><?php
                                echo $sharrre;
                            endif;

                        ?>
                        </div>
                    </header>
                    <div class="navigation">
                        <h3><?php _e( 'Other projects', 'fluxus' ); ?></h3>
                        <div class="other-projects"><?php

                            $related_projects = PortfolioProject::find_related_projects( $project->post->ID );
                            $index = 0;

                            foreach ( $related_projects as $related_project ) :

                                $index++;
                                $current = $project->post->ID == $related_project->post->ID;

                                $featured_media = $related_project->get_featured_media();

                                $attr = array(
                                        'href' => array(
                                                esc_url( get_permalink( $related_project->post->ID ) )
                                            ),
                                        'class' => array(
                                                'item-' . $index,
                                                $current ? 'active' : ''
                                            )
                                    );

                                if ( $featured_media ) {
                                    if ( $featured_media->is_image() ) {
                                        $image = $featured_media->get_image_data( 'fluxus-portfolio-thumbnail' );
                                    } else {
                                        $image = $featured_media->get_video_thumbnail( 'fluxus-portfolio-thumbnail' );
                                    }
                                    if ( $image ) {
                                        $attr['style'] = array(
                                                'background-image: url(' . $image['src'] . ')'
                                            );
                                    }
                                }


                                ?>
                                <a<?php echo it_array_to_attributes( $attr ); ?>>
                                    <?php echo $related_project->post->post_title; ?>
                                    <span class="hover"><?php
                                        if ( $current ) {
                                            _e( 'Current', 'fluxus' );
                                        } else {
                                            _e( 'View', 'fluxus' );
                                        }
                                        ?>
                                    </span>
                                </a><?php

                            endforeach; ?>
                        </div><?php

                        /**
                         * Next / Previous / Back to portfolio buttons.
                         */

                        $next_project = fluxus_portfolio_get_next_project( $post );
                        $prev_project = fluxus_portfolio_get_previous_project( $post );

                        if ( $prev_project ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $prev_project->ID ) ); ?>" class="button-minimal prev-project button-icon-left icon-left-open-big"><?php _e( 'Previous', 'fluxus' ); ?></a><?php
                        endif;

                        if ( $next_project ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $next_project->ID ) ); ?>" class="button-minimal next-project button-icon-right icon-right-open-big"><?php _e( 'Next', 'fluxus' ); ?></a><?php
                        endif;

                        ?>
                        <a href="<?php echo esc_url( get_permalink( fluxus_portfolio_base_id() ) ); ?>" class="button-minimal back-portfolio"><?php _e( 'Back to portfolio', 'fluxus' ); ?></a>
                    </div>
                </nav>

            </article>

            <?php

        endif; ?>

    </div>

    <?php get_sidebar( 'portfolio-single' ); ?>
</div><?php // end of #main

get_footer();