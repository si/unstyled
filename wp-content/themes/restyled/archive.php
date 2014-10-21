<?php get_header();?>

<?php if(have_posts()) : ?>
  <?php 
  /* If this is a category archive */ 
  if (is_category()) { 
  ?>
  <h1><?php echo single_cat_title(); ?> Archives</h1>
  <?php 
  /* If this is a daily archive */ 
  } elseif (is_day()) { 
  ?>
  <h1>Archives on <?php the_time('F jS, Y'); ?></h1>

  <?php 
  /* If this is a monthly archive */ 
  } elseif (is_month()) { 
  ?>
  <h1>Archives for <?php the_time('F, Y'); ?></h1>

  <?php 
  /* If this is a yearly archive */ 
  } elseif (is_year()) { 
  ?>
  <h1>Archives for <?php the_time('Y'); ?></h1>

  <?php 
  /* If this is a search */ 
  } elseif (is_search()) { 
  ?>
  <h1>Search Results</h1>

  <?php 
  /* If this is an author archive */ 
  } elseif (is_author()) { 
  ?>
  <h1><?php the_author(); ?> Archives</h1>

  <?php 
  /* If this is a paged archive */ 
  } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { 
  ?>
  <h1>Blog Archives</h1>

  <?php } ?>
<?php endif; ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>">
  	<h2><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
    <?php the_content(__('Continue Reading &#187;')); ?>
  </article>
<?php endwhile; else: ?>
  <p><?php _e('Nothing to see here.'); ?></p>
<?php endif; ?>

<?php posts_nav_link(' - ','&#171; Prev','Next &#187;') ?>

<?php get_sidebar();?>
<?php get_footer();?>