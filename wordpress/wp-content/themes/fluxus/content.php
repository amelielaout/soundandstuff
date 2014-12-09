<?php
/**
 * @package fluxus
 * @since fluxus 1.0
 */

global $post;

$article_width = '';

if ( get_post_format() == 'video' ) {
	$size = fluxus_video_get_size();
	if ( $size ) {
		$ratio = $size[0] / $size[1];
		$article_width = round($ratio * 320);
	}
}

$article_width = !empty( $article_width ) ? ' style="width: ' . $article_width . 'px"' : '';

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?><?php echo $article_width; ?>><?php
	if ( has_post_thumbnail() && ( ! in_array( get_post_format(), array( 'aside', 'video', 'link' ) ) ) ) :

		/**
		 * Post has a thumbnail. Show It.
		 */

		$image_info = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'fluxus-thumbnail' );
		$image = $image_info[0];

		?>
		<a class="thumbnail" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Read more about %s', 'fluxus' ), the_title_attribute( 'echo=0' ) ) ); ?>">
			<img src="<?php echo esc_url( $image ); ?>" class="resizable" width="<?php echo $image_info[1]; ?>" height="<?php echo $image_info[2]; ?>" alt="" /><?php
			/**
			 * If post format is Quote or Link, then show the quote overlay on top of the image.
			 */
			if ( get_post_format() == 'quote' ) {
				fluxus_quote();
			}

			if ( get_post_format() == 'link' ) {
				fluxus_link();
			}

		?>
		</a>
		<?php

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
	<div class="text-contents"><?php

		if ( get_post_format() != 'aside' ) : ?>

			<header class="entry-header">
				<h1 class="entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'fluxus' ), the_title_attribute( 'echo=0' ) ) ); ?>">
						<?php the_title(); ?>
					</a>
					<?php

						if ( 'post' == get_post_type() ) {
							fluxus_posted_on();
						}

						if ( is_sticky() ) : ?>
							<div class="sticky-icon icon-star" title="<?php echo esc_attr( __( 'Sticky post', 'fluxus' ) ); ?>"></div><?php
						endif;

					?>
				</h1>
			</header><?php

		endif; ?>

		<div class="entry-summary"><?php

			if ( ! fluxus_post_has_media() ) {

				// Post has no media, so there is additional space. Let's increase the excerpt length.
				add_filter( 'excerpt_length', 'fluxus_increased_excerpt_lenght', 1001 );

			}

			the_excerpt();

			remove_filter( 'excerpt_length', 'fluxus_increased_excerpt_lenght', 1001 );

			?>
		</div>

	</div>

</article>
