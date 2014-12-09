<?php
/**
 * General sidebar.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

?>
<div class="sidebar sidebar-general widget-area">
    <div class="scroll-container">
        <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
        <div class="viewport">
            <div class="overview">
                <?php

                    if ( is_search() ) : ?>
                        <hgroup class="search-results-hgroup">
                            <h2 class="subtitle"><?php _e( 'Search Results For', 'fluxus' ); ?></h2>
                            <h1 class="title">&ldquo;<?php echo get_search_query(); ?>&rdquo;</h1>
                        </hgroup><?php
                    endif;

                    if ( is_archive() ) : ?>

                        <hgroup class="archive-results-hgroup">
                            <h2 class="subtitle"><?php
                                if ( is_category() ) {
                                    _e( 'Category Archives', 'fluxus' );
                                } elseif ( is_tag() ) {
                                    _e( 'Category Archives', 'fluxus' );
                                } elseif ( is_author() ) {
                                    _e( 'Author Archives', 'fluxus' );
                                } elseif ( is_day() ) {
                                    _e( 'Daily Archives', 'fluxus' );
                                } elseif ( is_month() ) {
                                    _e( 'Monthly Archives', 'fluxus' );
                                } elseif ( is_year() ) {
                                    _e( 'Yearly Archives', 'fluxus' );
                                } else {
                                    _e( 'Archives', 'fluxus' );
                                } ?>
                            </h2>
                            <h1 class="title"><?php
                                if ( is_category() ) {
                                    echo single_cat_title( '', false );
                                } elseif ( is_tag() ) {
                                    echo single_tag_title( '', false );
                                } elseif ( is_author() ) {
                                    /* Queue the first post, that way we know
                                     * what author we're dealing with (if that is the case).
                                    */
                                    the_post();
                                    echo get_the_author();
                                    /* Since we called the_post() above, we need to
                                     * rewind the loop back to the beginning that way
                                     * we can run the loop properly, in full.
                                     */
                                    rewind_posts();
                                } elseif ( is_day() ) {
                                    echo get_the_date();
                                } elseif ( is_month() ) {
                                    echo get_the_date( 'F Y' );
                                } elseif ( is_year() ) {
                                    echo get_the_date( 'Y' );
                                } ?>
                            </h1>
                        </hgroup><?php

                    endif;

                    do_action( 'before_sidebar' );

                    dynamic_sidebar( 'sidebar-blog' );

                ?>
            </div>
        </div>
    </div>

</div>