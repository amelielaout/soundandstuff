<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package fluxus
 * @since fluxus 1.0
 */


$is_full_width = it_is_template( get_the_ID(), 'template-full-width.php' );
$is_full_width = $is_full_width ? 'full-width' : '';

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $is_full_width ); ?>>
	<header class="entry-header"><?php
        if ( has_post_thumbnail() ) :

            $image_info = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'fluxus-max' );
            $image = $image_info[0];

            ?>
            <div class="post-image">
                <img src="<?php echo esc_url( $image ); ?>" width="<?php echo $image_info[1]; ?>" height="<?php echo $image_info[2]; ?>" alt="" />
                <div class="js-vertical-center">
                    <div class="cover">
                        <h1><?php the_title(); ?></h1>
                    </div>
                </div>
            </div><?php

        else: ?>

		  <h1 class="entry-title"><?php the_title(); ?></h1><?php

        endif; ?>
	</header>

	<div class="entry-content">
        <?php

    		the_content();
    		wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'fluxus' ), 'after' => '</div>' ) );
    		edit_post_link( __( 'Edit', 'fluxus' ), '<span class="edit-link">', '</span>' );

        ?>
	</div>
</article>
