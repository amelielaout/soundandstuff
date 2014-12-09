<?php
/**
 * @package fluxus
 * @since fluxus 1.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="text-contents">
        <div class="entry-summary"><?php

            add_filter( 'excerpt_length', 'fluxus_increased_excerpt_lenght', 1001 );

            the_excerpt();

            remove_filter( 'excerpt_length', 'fluxus_increased_excerpt_lenght', 1001 );
            add_filter( 'excerpt_length', 'fluxus_excerpt_lenght', 1000 );

            ?>
        </div>
    </div>

</article>