<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to fluxus_comment().
 *
 * @package fluxus
 * @since fluxus 1.0
 */
?>

<?php
	/*
	 * If the current post is protected by a password and
	 * the visitor has not yet entered the password we will
	 * return early without loading the comments.
	 */
	if ( post_password_required() )
		return;
?>

	<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="widget-title comments-title">
			<div class="decoration"></div>
			<?php
				printf( _n( '1 comment', '%1$s comments', get_comments_number(), 'fluxus' ),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h2>

		<ol class="commentlist">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use fluxus_comment() to format the comments.
				 * If you want to overload this in a child theme then you can
				 * define fluxus_comment() and that will be used instead.
				 * See fluxus_comment() in inc/template-tags.php for more.
				 */
				wp_list_comments( array( 'callback' => 'fluxus_comment' ) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav role="navigation" id="comment-nav-below" class="comment-navigation">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'fluxus' ); ?></h1>
			<?php
				previous_comments_link( __( 'Older Comments', 'fluxus' ) );
				next_comments_link( __( 'Newer Comments', 'fluxus' ) );
			?>
		</nav>
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'fluxus' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div>