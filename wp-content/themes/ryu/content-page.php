<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Ryu
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-wrap wrap clear">
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-meta">
				<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'ryu' ), __( '1 Comment', 'ryu' ), __( '% Comments', 'ryu' ) ); ?></span>
				<?php endif; ?>

				<?php edit_post_link( __( 'Edit', 'ryu' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-meta -->
		</header><!-- .entry-header -->

		<div class="entry-content clear">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'ryu' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>'
				) );
			?>
		</div><!-- .entry-content -->
	</div><!-- .entry-wrap -->
</article><!-- #post-## -->