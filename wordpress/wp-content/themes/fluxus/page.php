<?php
/**
 * The template for displaying all pages.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

get_header();

$with_sidebar = ! it_is_template( get_the_id(), 'template-full-width.php' );

?>
<div id="main" class="site<?php if ( $with_sidebar ) : ?> site-with-sidebar<?php endif; ?>">
    <div id="content" class="site-content"><?php
    	while ( have_posts() ) : the_post();
    		get_template_part( 'content', 'page' );
    	endwhile; ?>
    </div>
    <?php

        if ( $with_sidebar ) {
            get_sidebar();
        }

    ?>
</div>
<?php

get_footer();
