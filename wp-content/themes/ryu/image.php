<?php
/**
 * The template for displaying image attachments.
 *
 * @package Ryu
 */

get_header();
$content_width = 1272;
?>
	<div id="primary" class="content-area image-attachment">
		<div id="content" class="site-content" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'clear' ); ?>>
				<div class="entry-wrap wrap clear">
					<div class="entry-content">
						<div class="entry-attachment">
							<div class="attachment">
								<?php
									/**
									 * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
									 * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
									 */
									$attachments = array_values( get_children( array(
										'post_parent'    => $post->post_parent,
										'post_status'    => 'inherit',
										'post_type'      => 'attachment',
										'post_mime_type' => 'image',
										'order'          => 'ASC',
										'orderby'        => 'menu_order ID'
									) ) );
									foreach ( $attachments as $k => $attachment ) {
										if ( $attachment->ID == $post->ID )
											break;
									}
									$k++;
									// If there is more than 1 attachment in a gallery
									if ( count( $attachments ) > 1 ) {
										if ( isset( $attachments[ $k ] ) )
											// get the URL of the next image attachment
											$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
										else
											// or get the URL of the first image attachment
											$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
									} else {
										// or, if there's only 1 image, get the URL of the image
										$next_attachment_url = esc_url( wp_get_attachment_url() );
									}
								?>

								<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>" rel="attachment"><?php
									$attachment_size = apply_filters( 'ryu_attachment_size', array( 1272, 1272 ) ); // Filterable image size.
									echo wp_get_attachment_image( $post->ID, $attachment_size );
								?></a>
							</div><!-- .attachment -->
						</div><!-- .entry-attachment -->

						<?php the_content(); ?>
						<?php
							wp_link_pages( array(
								'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'ryu' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>'
							) );
						?>

						<div class="comment-status">
							<?php if ( comments_open() && pings_open() ) : // Comments and trackbacks open ?>
								<?php printf( __( '<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'ryu' ), esc_url( get_trackback_url() ) ); ?>
							<?php elseif ( ! comments_open() && pings_open() ) : // Only trackbacks open ?>
								<?php printf( __( 'Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'ryu' ), esc_url( get_trackback_url() ) ); ?>
							<?php elseif ( comments_open() && ! pings_open() ) : // Only comments open ?>
								<?php _e( 'Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'ryu' ); ?>
							<?php elseif ( ! comments_open() && ! pings_open() ) : // Comments and trackbacks closed ?>
								<?php _e( 'Both comments and trackbacks are currently closed.', 'ryu' ); ?>
							<?php endif; ?>
						</div>
					</div><!-- .entry-content -->

					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header><!-- .entry-header -->

					<footer class="entry-meta">
						<?php
							$metadata = wp_get_attachment_metadata();
							printf( __( '<span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span><span class="full-size-link"><a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a></span><span class="parent-post-link"><a href="%6$s" title="Return to %7$s" rel="gallery">%8$s</a></span>', 'ryu' ),
								esc_attr( get_the_date( 'c' ) ),
								esc_html( get_the_date() ),
								esc_url( wp_get_attachment_url() ),
								$metadata['width'],
								$metadata['height'],
								esc_url( get_permalink( $post->post_parent ) ),
								esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ),
								strip_tags( get_the_title( $post->post_parent ) )
							);
						?>
						<?php edit_post_link( __( 'Edit', 'ryu' ), '<span class="edit-link">', '</span>' ); ?>
					</footer><!-- .entry-meta -->

					<?php if ( ! empty( $post->post_excerpt ) ) : ?>
					<div class="entry-caption">
						<?php the_excerpt(); ?>
					</div><!-- .entry-caption -->
					<?php endif; ?>

				</div><!-- .entry-wrap -->
			</article><!-- #post-<?php the_ID(); ?> -->

			<nav role="navigation" id="image-navigation" class="navigation-image clear double">
				<?php next_image_link( false, __( '<div class="next"><span class="meta-nav">&rarr;</span> <span class="text-nav">Next</span></div>', 'ryu' ) ); ?>
				<?php previous_image_link( false, __( '<div class="previous"><span class="meta-nav">&larr;</span> <span class="text-nav">Previous</span></div>', 'ryu' ) ); ?>
			</nav><!-- #image-navigation -->

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template();
			?>

		<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>