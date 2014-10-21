<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Ryu
 */

if ( ! function_exists( 'ryu_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function ryu_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'navigation-post' : 'navigation-paging';

	// Add a class when both navigation items are there.
	if ( ( get_previous_posts_link() && get_next_posts_link() ) || ( is_single() && ( $next && $previous ) ) )
		$nav_class .= ' double';

	?>
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?>">
		<div class="wrap clear">
			<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'ryu' ); ?></h1>

		<?php if ( is_single() ) : // navigation links for single posts ?>

			<?php next_post_link( '<div class="next">%link</div>', '<span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'ryu' ) . '</span> <span class="text-nav">%title</span>' ); ?>

			<?php previous_post_link( '<div class="previous">%link</div>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'ryu' ) . '</span> <span class="text-nav">%title</span>' ); ?>


		<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="next"><?php previous_posts_link( __( '<span class="meta-nav">&rarr;</span> <span class="text-nav">Newer posts</span>', 'ryu' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_next_posts_link() ) : ?>
			<div class="previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> <span class="text-nav">Older posts</span>', 'ryu' ) ); ?></div>
			<?php endif; ?>

		<?php endif; ?>

		</div>
	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif; // ryu_content_nav

if ( ! function_exists( 'ryu_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function ryu_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'ryu' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'ryu' ), '<span class="edit-link">', '<span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer>
				<div class="comment-author vcard">
					<span class="comment-author-avatar"><?php echo get_avatar( $comment, 48 ); ?></span>
					<?php printf( __( '%s <span class="says">says:</span>', 'ryu' ), sprintf( '<cite class="fn genericon">%s</cite>', get_comment_author_link() ) ); ?>
				</div><!-- .comment-author .vcard -->
			</footer>

			<div class="comment-content">
				<?php comment_text(); ?>
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<p><em><?php _e( 'Your comment is awaiting moderation.', 'ryu' ); ?></em></p>
				<?php endif; ?>
			</div>

			<div class="comment-meta commentmetadata">
				<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time datetime="<?php comment_time( 'c' ); ?>">
				<?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'ryu' ), get_comment_date(), get_comment_time() ); ?>
				</time></a>
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				<?php edit_comment_link( __( 'Edit', 'ryu' ), '<span class="edit-link">', '<span>' ); ?>
			</div><!-- .comment-meta .commentmetadata -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for ryu_comment()

if ( ! function_exists( 'ryu_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function ryu_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() )
		printf( __( '<span class="featured-post"><a href="%1$s" title="%2$s" rel="bookmark">Sticky</a></span>', 'ryu' ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() )
		);

	if ( 'post' == get_post_type() ) {
		printf( __( '<span class="entry-date"><a href="%1$s" title="%2$s" rel="bookmark"><time datetime="%3$s">%4$s</time></a></span><span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span>', 'ryu' ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'ryu' ), get_the_author() ) ),
			get_the_author()
		);
	}

	$tags_list = get_the_tag_list( '', __( ', ', 'ryu' ) );
	if ( $tags_list )
		echo '<span class="tags-links">' . $tags_list . '</span>';
}
endif;

/**
 * Returns true if a blog has more than 1 category
 */
function ryu_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so ryu_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so ryu_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in ryu_categorized_blog
 */
function ryu_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'ryu_category_transient_flusher' );
add_action( 'save_post', 'ryu_category_transient_flusher' );


if ( ! function_exists( 'ryu_tonesque_css' ) ) :
/**
 * Prints each image post specific style rules via Tonesque
 */
function ryu_tonesque_css() {
	global $post;
	$first_image = ryu_image_grabber( $post->ID, get_the_content() );
	if ( ! $first_image ) :
		return;
	else :
		$tonesque = new Tonesque( $first_image );
		$color = $tonesque->color();
		$contrast = $tonesque->contrast();
		$id = get_the_ID();
		$postid = '.post-' . $id;
		echo '<style scoped>
			' . $postid . '.format-image {background-color: #' . $color . ';}
			' . $postid . '.format-image,
			' . $postid . '.format-image a,
			' . $postid . '.format-image .entry-title,
			' . $postid . '.format-image .entry-title a {color: rgb(' . $contrast . ');}
			' . $postid . '.format-image a:hover,
			' . $postid . '.format-image .entry-title a:hover {color: rgba(' . $contrast . ', 0.7);}
			' . $postid . '.format-image .entry-format-badge {background-color: rgb(' . $contrast . ');}
			' . $postid . '.format-image .entry-format-badge:hover {background-color: rgba(' . $contrast . ', 0.7);}
			' . $postid . '.format-image a.entry-format-badge:before {color: #' . $color . ';}
			' . $postid . '.format-image .entry-meta span,
			' . $postid . '.format-image .entry-meta,
			' . $postid . '.format-image div.sharedaddy div.sd-block {border-color: rgba(' . $contrast . ', 0.1);}
			' . $postid . '.format-image .entry-content a {border-color: rgba(' . $contrast . ', 0.2);}
			' . $postid . '.format-image .entry-content a:hover {border-color: rgba(' . $contrast . ', 1); color: rgb(' . $contrast . ');}
			' . $postid . '.format-image .entry-meta span + span:before {color: rgba(' . $contrast . ', 0.2);}
		</style>';
	endif;
}
endif; // ends check for ryu_tonesque_css()