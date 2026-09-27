<?php
if (!defined('ABSPATH')) { exit; }
get_header(); ?>
<main class="site-main">
  <div class="container">
    <header class="archive-header">
      <h1 class="page-title"><?php the_archive_title(); ?></h1>
      <div class="archive-desc"><?php the_archive_description(); ?></div>
    </header>

    <div class="grid products-grid">
      <?php if (have_posts()): while (have_posts()): the_post(); ?>
        <article <?php post_class('card'); ?>>
          <a href="<?php the_permalink(); ?>" class="card-media"><?php the_post_thumbnail('medium'); ?></a>
          <div class="card-body">
            <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="card-excerpt"><?php the_excerpt(); ?></div>
          </div>
        </article>
      <?php endwhile; the_posts_pagination(); else: ?>
        <p>موردی یافت نشد.</p>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php get_footer(); ?>
