<?php
/**
 * @package fluxus
 * @since fluxus 1.0
 */

$post_categories = get_the_category();
$posted_in = array();

if ( $post_categories && fluxus_categorized_blog() ) {

	if ( count( $post_categories ) > 1 ) {

		foreach ( $post_categories as $post_cat ) {
			if ( $post_cat->term_id != 1 ) {
				$posted_in[] = $post_cat->name;
			}
		}

	} elseif ( !in_category( 1 ) ) {
		$posted_in[] = $post_categories[0]->name;
	}

}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header"><?php

		if ( has_post_thumbnail() ) :

			$image_info = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'fluxus-max' );
			$image = $image_info[0];

			?>
			<div class="post-image">
				<img src="<?php echo esc_url( $image ); ?>" width="<?php echo $image_info[1]; ?>" height="<?php echo $image_info[2]; ?>" alt="" /><?php

				if ( get_post_format() == 'quote' ) :
					fluxus_quote();

				elseif ( get_post_format() == 'link' ) :
					fluxus_link();

				else : ?>
					<div class="js-vertical-center">
						<div class="cover">
							<h1><?php the_title(); ?></h1>
							<?php

								if ( $posted_in ) {
									printf( '<p>' . __( 'posted in %s', 'fluxus' ) . '</p> ', join( ', ', $posted_in ) );
								}

							?>
						</div>
					</div><?php

				endif;

				?>
			</div><?php

		elseif ( get_post_format() == 'quote' ) :
			/**
			 * Post without a thumbnail. Show Quote on top of solid color.
			 */
			fluxus_quote();


		elseif ( get_post_format() == 'video' ) :

			/**
			 * Post type is video, show video in place of the thumbnail image.
			 */
			fluxus_video();


		elseif ( get_post_format() == 'link' ) :

			/**
			 * Show big link.
			 */
			fluxus_link();


		endif; ?>
		<h1 class="entry-title">
			<?php the_title(); ?>
		</h1>
		<div class="entry-meta">
			<?php fluxus_posted_by(); ?>
			<span class="sep"></span>
			<?php fluxus_posted_on(); ?>
		</div>

	</header>

	<div class="entry-content">
		<?php the_content(); ?>
		<div class="entry-navigation"><?php
			the_fluxus_tags( __( 'tagged with', 'fluxus' ) );
			fluxus_post_nav(); ?>
		</div>
		<?php edit_post_link( __( 'Edit', 'fluxus' ), '<span class="edit-link">', '</span>' ); ?>
	</div>

</article>