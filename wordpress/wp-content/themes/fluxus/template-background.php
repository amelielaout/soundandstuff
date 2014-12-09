<?php
/*
Template Name: Page with Background
*/

fluxus_add_html_class( 'horizontal-page no-scroll' );
get_header();

if ( have_posts() ) : the_post();

    $data = fluxus_background_get_data( get_the_ID() );

    $slide_attr = array(
            'class' => array( 'slide' ),
            'id'    => 'slide-' . get_the_ID(),
            'data-image' => has_post_thumbnail() ? it_get_post_thumbnail( get_the_ID(), 'fluxus-max' ) : '',
            'style' => array( 'background-position: ' . $data['background_position'] )
        );

    $slide_info_attr = array(
                            'class' => array(
                                    'info',
                                    $data['text_color'],
                                    'no-fade',
                                    'style-page-with-background'
                                ),
                            'style' => array(),
                            'data-position' => $data['content_position'],
                            'data-top' => $data['top'],
                            'data-left' => $data['left']
                        );

    if ( $data['dim_background'] == '1' ) {

        $slide_info_attr['class'][] = 'dim-background';

    }

    ?>
    <div id="main" class="site">
        <div class="slider">
            <div<?php echo it_array_to_attributes( $slide_attr ); ?>>
                <article<?php echo it_array_to_attributes( $slide_info_attr ); ?>>
                    <div class="viewport">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <div class="entry-content"><?php the_content(); ?></div>
                    </div>
                </article>
            </div>
        </div>
    </div><?php

endif;

get_footer();
