<?php
defined('ABSPATH') || exit;
get_header();
?>
<main id="siteMain" class="site-main">
  <div class="container">
    <?php woocommerce_content(); ?>
  </div>
</main>
<?php get_footer(); ?>
