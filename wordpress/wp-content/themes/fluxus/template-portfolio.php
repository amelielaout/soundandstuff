<?php
/*
Template Name: Horizontal Portfolio
*/

fluxus_add_html_class( 'horizontal-page' );

get_header();

?>
<div id="main" class="site site-with-sidebar">
    <div id="content" class="site-content">
        <div class="portfolio-list horizontal-content"><?php

            if ( is_page() ) {

                /**
                 * We are on index page.
                 * Let's modify main loop to fluxus_portfolio post type.
                 *
                 * If is_page() is false, then we are on taxonomy-fluxus-project-type.php
                 * template, so our loop is already correct.
                 */

                fluxus_query_portfolio();

            }

            if ( have_posts() ) :

                while ( have_posts() ) : the_post();

                    $project = new PortfolioProject( get_the_ID() );

                    $featured = $project->get_featured_media();

                    if ( ! $featured ) continue; // We have no media on this project, nothing to show.

                    $attr['class'] = 'horizontal-item project';

                    if ( $featured->is_image() ) {
                        $image = $featured->get_image_data( 'fluxus-max' );
                    } else {
                        $image = $featured->get_video_thumbnail( 'fluxus-max' );
                    }

                    if ( ! $image ) {
                        $image = array( 'src' => get_template_directory_uri() . '/images/no-portfolio-thumbnail.png',
                                        'width' => 1920,
                                        'height' => 1280 );
                    }

                    ?>
                    <article <?php echo it_array_to_attributes( $attr ); ?>>

                        <div class="preview">

                            <img class="featured-image" src="<?php echo esc_url( $image['src'] ); ?>" width="<?php echo esc_attr( $image['width'] ); ?>" height="<?php echo esc_attr( $image['height'] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                            <div class="hover-box">
                                <div class="hover-box-contents"><?php
                                    if ( $project->meta_subtitle ) : ?>
                                        <h3 class="subtitle"><?php echo $project->meta_subtitle; ?></h3><?php
                                    endif; ?>
                                    <h2><?php the_title(); ?></h2>
                                    <div class="decoration"></div>
                                    <?php if ( $post->post_excerpt ) : ?>
                                        <div class="excerpt"><?php the_excerpt(); ?></div>
                                    <?php endif; ?>
                                    <div class="wrap-button">
                                        <a href="<?php echo get_permalink( get_the_ID() ); ?>" class="button"><?php _e( 'View Work', 'fluxus' ); ?></a>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <section class="info">
                            <h2 class="entry-title"><a href="<?php echo get_permalink( get_the_ID() ); ?>"><?php the_title(); ?></a></h2><?php
                            if ( $tags = $project->get_tags() ) : ?>
                                <div class="entry-tags"><?php
                                    foreach ( $tags as $tag ) : ?>
                                        <a href="<?php echo esc_url( get_term_link( $tag ) ); ?>"><b class="hash">#</b><?php echo $tag->name; ?></a><?php
                                    endforeach; ?>
                                </div><?php
                            endif; ?>
                        </section>
                    </article><?php

                endwhile;

            endif; ?>

        </div>
    </div><?php

    wp_reset_query();
    get_sidebar( 'portfolio' );

    ?>
</div>
<?php

get_footer();