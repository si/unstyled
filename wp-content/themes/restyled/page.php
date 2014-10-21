<?php get_header();?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article id="post-<?php the_ID(); ?>">
  <h1><?php the_title(); ?></h1>

  <?php the_content(__('Continue Reading &#187;')); ?>

  <?php $sub_pages = wp_list_pages( 'sort_column=menu_order&depth=1&title_li=&echo=0&child_of=' . $id );?>
  <?php if ($sub_pages <> "" ): ?>
  <ul>
    <?php echo $sub_pages; ?>
  </ul>
  <?php endif; ?>

</article>
<?php endwhile; else: ?>
  <p><?php _e('No content found.'); ?></p>

<?php endif; ?>
<?php get_sidebar();?>
<?php get_footer();?>