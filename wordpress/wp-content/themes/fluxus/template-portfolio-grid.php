<?php
/*
Template Name: Grid Portfolio
*/

fluxus_add_html_class( 'horizontal-page layout-portfolio-grid' );

get_header();

if ( is_page() ) {
    fluxus_query_portfolio();
}

if ( have_posts() ) :

    $grid_portfolio = new GridPortfolio( get_the_ID() );

    ?>
    <div id="main" class="site">

        <div class="portfolio-grid" data-columns="<?php echo $grid_portfolio->get_grid_column_count(); ?>" data-rows="<?php echo $grid_portfolio->get_grid_row_count(); ?>"><?php

            while ( have_posts() ) :

                the_post();

                $project = new PortfolioProject( get_the_ID() );
                $featured = $project->get_featured_media();

                if ( ! $featured ) continue; // We have no media on this project, nothing to show.

                if ( $featured->is_image() ) {
                    $image = $featured->get_image_data( 'fluxus-thumbnail' );
                    $image = $image ? $image['src'] : '';
                } else {
                    $image = $featured->get_video_thumbnail( 'fluxus-thumbnail' );
                    if ( ! $image ) {
                        $image = array( 'src' => get_template_directory_uri() . '/images/no-portfolio-thumbnail.png' );
                    }
                    $image = $image['src'];
                }

                ?>
                <article class="grid-project">
                    <a href="<?php the_permalink(); ?>" class="preview" style="background-image: url(<?php echo esc_url( $image ); ?>);">
                        <span class="hover-box">

                            <span class="inner"><?php
                                if ( $project->meta_subtitle ) : ?>
                                    <i><?php echo $project->meta_subtitle; ?></i><?php
                                endif; ?>
                                <b><?php the_title(); ?></b>
                            </span>
                        </span>
                    </a>
                </article><?php

            endwhile; ?>

        </div>

    </div>

<?php

endif;

wp_reset_query();

get_footer();