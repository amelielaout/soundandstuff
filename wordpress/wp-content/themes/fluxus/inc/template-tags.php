<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @since fluxus 1.0
 */


/**
 * Prints <html> classes.
 *
 * @since fluxus 1.0
 */
function fluxus_html_classes( $inline_classes = '' ) {

	global $fluxus_html_classes;

	$inline_classes = explode( ' ', $inline_classes );

	$fluxus_html_classes = ! is_array( $fluxus_html_classes ) ?
						   	 $inline_classes :
						   	 array_merge( $fluxus_html_classes, $inline_classes );


	if ( ! is_array($fluxus_html_classes) ) {
		return false;
	}

	$fluxus_html_classes = array_unique( $fluxus_html_classes );
	$fluxus_html_classes = array_map( 'esc_attr', $fluxus_html_classes );

	echo ' class="' . join( ' ', $fluxus_html_classes ) . '"';

}


/**
 * Adds CSS class to <html> element.
 *
 * @since fluxus 1.0
 */
function fluxus_add_html_class( $class ) {

	global $fluxus_html_classes;

	if ( ! $fluxus_html_classes || ! is_array( $fluxus_html_classes ) ) {
		$fluxus_html_classes = array();
	}

	$fluxus_html_classes[] = $class;

	return $fluxus_html_classes;

}


/**
 * Returns TRUE, if the post has any media assigned to it.
 * (eg. has a thumbnail, a video, quote...)
 *
 * @since fluxus 1.0
 */
function fluxus_post_has_media( $post_id = false ) {

	$post_id = is_numeric( $post_id ) ? $post_id : get_the_ID();

	if ( !$post_id ) {
		return false;
	}

	if ( has_post_thumbnail( $post_id ) ) {
		return true;
	}

	$post_format = get_post_format( $post_id );

	if ( $post_format == 'quote' ) {
		$data = fluxus_quote_get_data( $post_id );
		return isset($data['quote']) && !empty($data['quote']);
	}

	if ( $post_format == 'link' ) {
		$data = fluxus_link_get_data( $post_id );
		return isset($data['link']) && !empty($data['link']);
	}

	if ( $post_format == 'video' ) {
		$data = fluxus_video_get_data( $post_id );
		return isset($data['embed']) && !empty($data['embed']);
	}

	return false;

}


/**
 * Adds .post-with-media class to the post tag,
 * if the post has any media assigned to it.
 *
 * @since fluxus 1.0
 */
function fluxus_post_classes( $classes, $class, $post_id ) {

	if ( fluxus_post_has_media( $post_id ) ) {
		$classes[] = 'post-with-media';
	}
	return $classes;

}
add_filter( 'post_class', 'fluxus_post_classes', 1, 3 );


/**
 * Displays pagination on the post list page.
 *
 * @since fluxus 1.0
 */
function fluxus_content_paging() {
	global $wp_query, $post;

	?>
	<nav class="navigation-paging">
		<div class="js-vertical-center">
			<div class="status">
				<p><?php _e( 'page', 'fluxus' ); ?></p>
				<h5><?php echo max( 1, get_query_var('paged') ); ?> / <?php echo $wp_query->max_num_pages; ?></h5>
			</div>

			<div class="paging"><?php
				if ( get_previous_posts_link() ) :
					$prev_link = esc_attr( previous_posts( false ) ); ?>
					<a href="<?php echo $prev_link ?>" class="button-minimal button-icon-left icon-left-open-big"><?php _e( 'Newer entries', 'fluxus' ); ?></a><?php
				else: ?>
					<span class="button-minimal button-icon-left icon-left-open-big button-disabled"><?php _e( 'Newer entries', 'fluxus' ); ?></span><?php
				endif;


				if ( get_next_posts_link() ) :
					$next_link = esc_attr( next_posts( 9999999, false ) ); ?>
					<a href="<?php echo $next_link; ?>" class="button-minimal button-icon-right icon-right-open-big"><?php _e( 'Older entries', 'fluxus' ); ?></a><?php
				else: ?>
					<span class="button-minimal button-icon-right icon-right-open-big button-disabled"><?php _e( 'Older entries', 'fluxus' ); ?></span><?php
				endif; ?>

				<p><?php _e( 'jump to page', 'fluxus' ); ?></p>
				<?php
					$big = 9999999;
					echo paginate_links( array(
						'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format' => '?paged=%#%',
						'current' => max( 1, get_query_var('paged') ),
						'total' => $wp_query->max_num_pages,
						'prev_next' => false
					) );
				?>
			</div>
		</div>
	</nav>
	<?php

}


/**
 * Display post tags.
 *
 * @since fluxus 1.0
 */
function the_fluxus_tags( $before = null ) {

    $tags = wp_get_post_tags( get_the_ID() );
    if ( !$tags ) {
        return false;
    }
    echo '<div class="entry-tags post-tags">';
    if ( $before != null ) {
        echo '<h3>' . $before . '</h3>';
    }
    foreach ( $tags as $tag ) :
        $link = esc_url( get_term_link( $tag ) ); ?>
        <a href="<?php echo $link; ?>"><b class="hash">#</b><?php echo $tag->name; ?></a><?php
    endforeach;
    echo '</div>';

}


/**
 * Display navigation to next/previous pages when applicable
 *
 * @since fluxus 1.0
 */
function fluxus_post_nav() {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return ;
		}
	} else {
		return;
	}

	?>
	<nav class="post-navigation">

		<h3><?php _e( 'Further reading', 'fluxus' ); ?></h3>

		<?php

		$previous_post = get_previous_post();
		if ( $previous_post ) : ?>
			<a class="button-minimal button-icon-right icon-right-open-big nav-previous" href="<?php echo esc_url( get_permalink( $previous_post->ID ) ); ?>">
				<?php _e( 'Older post', 'fluxus' ); ?>
			</a><?php
		else: ?>
			<span class="button-minimal button-icon-right icon-right-open-big nav-previous button-disabled">
				<?php _e( 'Older post', 'fluxus' ); ?>
			</span><?php
		endif;

		$next_post = get_next_post();
		if ( $next_post ) : ?>
			<a class="button-minimal button-icon-left icon-left-open-big nav-next" href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">
				<?php _e( 'Newer post', 'fluxus' ); ?>
			</a><?php
		else: ?>
			<span class="button-minimal button-icon-left icon-left-open-big nav-next button-disabled">
				<?php _e( 'Newer post', 'fluxus' ); ?>
			</span><?php
		endif;

		?>
	</nav><?php
}


/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since fluxus 1.0
 */
function fluxus_comment( $comment, $args, $depth ) {

	global $post;
	$GLOBALS['comment'] = $comment;

	switch ( $comment->comment_type ) :

		case 'pingback' :
		case 'trackback' : ?>
			<li class="post pingback">
				<p>
					<?php _e( 'Pingback:', 'fluxus' );
					comment_author_link();
					edit_comment_link( __( 'Edit', 'fluxus' ), ' ' ); ?>
				</p><?php
		break;

		default : ?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="comment-<?php comment_ID(); ?>" class="comment">
					<footer>
						<div class="comment-author vcard"><?php

							$avatar = get_avatar( $comment, 64 );
							$avatar_2x = get_avatar( $comment, 128 );

							if ( $avatar ) : ?>
								<div class="wrap-avatar">
									<?php echo $avatar . $avatar_2x; ?>
								</div><?php
							endif;

							?>
							<div class="comment-author-info">
								<?php

									printf( __( '%s', 'fluxus' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) );

									if ( $comment->user_id > 0 && $user = get_userdata($comment->user_id) ) {
										if ( $comment->user_id === $post->post_author ) : ?>
											<span class="bypostauthor-icon"><?php _e( 'Post author', 'fluxus' ); ?></span><?php
										endif;
									}

								?>
								<div class="comment-meta commentmetadata">
									<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>" class="comment-time"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
									<?php
										/* translators: 1: date, 2: time */
										printf( __( '%1$s at %2$s', 'fluxus' ), get_comment_date(), get_comment_time() ); ?>
									</time></a>
									<div class="comment-actions">
										<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
										<?php edit_comment_link( __( 'Edit', 'fluxus' ), ' ' ); ?>
									</div>
								</div>
							</div>
						</div><?php
						if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'fluxus' ); ?></em>
							<br /><?php
						endif; ?>
					</footer>
					<div class="comment-content"><?php comment_text(); ?></div>
				</article><?php
		break;

	endswitch;
}


/**
 * Prints HTML with current post date in short format.
 *
 * @since fluxus 1.0
 */
function fluxus_posted_on() {

	printf( __( '<time class="entry-date" datetime="%1$s" pubdate>%2$s</time>', 'fluxus' ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date( 'M j' ) )
	);

}


/**
 * Prints HTML with current post date in long format.
 *
 * @since fluxus 1.0
 */
function fluxus_posted_on_full_date() {

	printf( __( '<time class="entry-date" datetime="%1$s" pubdate>%2$s</time>', 'fluxus' ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date( 'M j, Y' ) )
	);

}


/**
 * Prints HTML with meta information for the current author.
 *
 * @since fluxus 1.0
 */
function fluxus_posted_by() {

	printf( __( '<span class="byline"> by <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span></span>', 'fluxus' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'fluxus' ), get_the_author() ) ),
		esc_html( get_the_author() )
	);

}


/**
 * Get previous image link that has the same post parent.
 *
 * @since 1.0
 */
function fluxus_get_previous_image_link( $size = 'thumbnail' ) {
	return fluxus_get_adjacent_image_link( true, $size );
}

/**
 * Get next image link that has the same post parent.
 *
 * @since 1.0
 */
function fluxus_get_next_image_link( $size = 'thumbnail' ) {
	return fluxus_get_adjacent_image_link( false, $size );
}

/**
 * Get next or previous image link that has the same post parent.
 * Retrieves the current attachment object from the $post global.
 *
 * @since 1.0
 */
function fluxus_get_adjacent_image_link( $prev = true, $size = 'thumbnail' ) {
	global $post;
	$post = get_post( $post );
	$attachments = array_values( get_children( array(
			'post_parent' 	 => $post->post_parent,
			'post_status' 	 => 'inherit',
			'post_type' 	 => 'attachment',
			'post_mime_type' => 'image',
			'order' 		 => 'ASC',
			'orderby' 		 => 'menu_order ID')
		) );

	foreach ( $attachments as $k => $attachment ) {
		if ( $attachment->ID == $post->ID ) {
			break;
		}
	}

	$k = $prev ? $k - 1 : $k + 1;

	if ( isset($attachments[$k]) ) {
		return get_attachment_link( $attachments[$k]->ID );
	} else {
		return false;
	}

}


/**
 * Returns true if a blog has more than 1 category.
 *
 * @since fluxus 1.0
 */
function fluxus_categorized_blog() {

	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	/**
	 * If we have more than 1 category, then this blog is catagorized.
	 */
	return '1' != $all_the_cool_cats;

}


/**
 * Flush out the transients used in fluxus_categorized_blog.
 *
 * @since fluxus 1.0
 */
function fluxus_category_transient_flusher() {

	delete_transient( 'all_the_cool_cats' );

}
add_action( 'edit_category', 'fluxus_category_transient_flusher' );
add_action( 'save_post', 'fluxus_category_transient_flusher' );

