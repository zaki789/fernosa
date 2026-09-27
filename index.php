<?php
if (!defined('ABSPATH')) { exit; }
get_header(); ?>
<main class="site-main">
  <div class="container">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <article <?php post_class('post-card'); ?>>
        <h1 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
        <div class="post-excerpt"><?php the_excerpt(); ?></div>
      </article>
    <?php endwhile; the_posts_pagination(); else: ?>
      <p>محتوایی یافت نشد.</p>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
