<?php get_header();?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<aside class="post" id="post-<?php the_ID(); ?>">

<h1><?php the_title(); ?></h1>

<?php the_content(__('Continue Reading &#187;')); ?>

<?php the_tags('Tags: ', ', ', '<br />'); ?>

</aside>

<?php endwhile; else: ?>

<p><?php _e('No count found.'); ?></p>

<?php endif; ?>

<?php get_sidebar();?>
<?php get_footer();?>