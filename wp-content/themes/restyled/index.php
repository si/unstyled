<?php get_header();?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<h1><?php the_title(); ?></h1>

<?php the_content(__('Continue Reading &#187;')); ?>

<?php endwhile; else: ?>

<p><?php _e('No posts to show'); ?></p>

<?php endif; ?>

<?php get_sidebar();?>
<?php get_footer(); ?>