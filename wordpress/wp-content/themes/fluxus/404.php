<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package fluxus
 * @since fluxus 1.0
 */

fluxus_add_html_class( 'horizontal-page no-scroll' );
get_header();

/**
 * Find pages that have full page slider template. Search those pages for big images.
 */

$slider_pages = it_find_page_by_template( 'template-full-page-slider.php', 'post_status=publish' );

$image = false;

foreach ( $slider_pages as $slider_page ) {

	$slides = fluxus_slider_get_published_slides( $slider_page->ID );

	if ( $slides && is_array( $slides ) ) {

		shuffle( $slides );

		foreach ( $slides as $slide ) {

			$image = wp_get_attachment_image_src( $slide->ID, 'fluxus-max' );

			// We found our first image!
			if ( $image ) {
	            $image = $image[0];
	            $data = fluxus_slider_get_slide_data( $slide->ID );

	            break;
			}

		}

	}

	// Stop as soon as we find first image.
	if ( $image ) {
		break;
	}

}

$attr = array(
		'class' => 'page-with-background'
	);

if ( $image ) {

	$attr['style'] = array(
			isset( $data['background_position'] ) ? $data['background_position'] : 'center center',
			'background-image: url(' . esc_url( $image ) . ')'
		);

}

?>
<div id="main" class="site">

	<div <?php echo it_array_to_attributes( $attr ); ?>>
		<article id="post-0" class="post error404 not-found js-vertical-center">
			<header class="entry-header">
				<h1 class="entry-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'fluxus' ); ?></h1>
			</header>
			<div class="entry-content">
				<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'fluxus' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		</article>
	</div>

</div>

<?php

get_footer();


