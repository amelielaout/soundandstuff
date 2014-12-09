<?php
/**
 * The template for displaying image attachments.
 *
 * @package fluxus
 * @since fluxus 1.0
 */

get_header();

?>
<div id="main" class="site site-with-sidebar">
	<div id="content" class="site-content"><?php

		while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<div class="post-image"><?php
						echo wp_get_attachment_image( $post->ID, 'fluxus-max' ); ?>
					</div>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<div class="entry-meta">
						<?php fluxus_posted_by(); ?>
						<span class="sep"></span>
						<?php fluxus_posted_on(); ?>
					</div>
				</header>

				<div class="entry-content">

					<div class="entry-attachment">
						<?php if ( ! empty( $post->post_excerpt ) ) : ?>
						<div class="entry-caption">
							<?php the_excerpt(); ?>
						</div>
						<?php endif; ?>
					</div>

					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'fluxus' ), 'after' => '</div>' ) ); ?>

				</div>

				<?php edit_post_link( __( 'Edit', 'fluxus' ), ' <span class="edit-link">', '</span>' ); ?>

			</article><?php

			comments_template();

		endwhile; ?>
	</div>

	<?php get_sidebar( 'post' ); ?>

</div>

<?php get_footer(); ?>