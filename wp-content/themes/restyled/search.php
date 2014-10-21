<?php get_header();?>

<h1>Search Results for "<?php the_search_query(); ?>"</h1>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article id="post-<?php the_ID(); ?>">
	<h2><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<?php the_content(__('Continue Reading &#187;')); ?>
</article>

<?php endwhile; else: ?>
<p><?php _e('No posts found.'); ?></p>

<?php endif; ?>

<?php get_sidebar();?>
<?php get_footer();?>