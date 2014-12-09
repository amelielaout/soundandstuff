<?php
/**
 * The Template for displaying all single posts.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

get_header();

?>
<div id="main" class="site site-with-sidebar">

	<div id="content" class="site-content"><?php

		while ( have_posts() ) : the_post();

			get_template_part( 'content', 'single' );

			// If comments are open or we have at least one comment, load up the comment template
			if ( comments_open() || '0' != get_comments_number() ) {
				comments_template( '', true );
			}

		endwhile; ?>

	</div>

	<?php get_sidebar( 'post' ); ?>

</div>

<?php get_footer(); ?>