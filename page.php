<?php
if (!defined('ABSPATH')) { exit; }

if (is_page('menu')) {
    get_header();
    ?>
    <main class="site-main fg-dedicated-menu">
      <div class="container">
        <div class="section-head">
          <span class="section-kicker">FERNOSA MENU</span>
          <h1 class="section-title">منوی فرنوسا</h1>
          <p class="section-sub">محصولات و طعم‌های منتخب فرنوسا</p>
        </div>
        <?php
        $cats = function_exists('fernosa_get_menu_categories') ? fernosa_get_menu_categories(20) : [];
        if (class_exists('WooCommerce') && !empty($cats)) :
            foreach ($cats as $cat):
                $products = function_exists('fernosa_get_products_for_cat') ? fernosa_get_products_for_cat((int) $cat->term_id, 0, 24) : [];
                ?>
                <section class="menu-category-page">
                  <h2><?php echo esc_html($cat->name); ?></h2>
                  <div class="product-cards">
                    <?php foreach ($products as $product): echo fernosa_product_card_html($product); endforeach; ?>
                  </div>
                </section>
                <?php
            endforeach;
        else:
            ?>
            <div class="notice-card">هنوز محصولی برای نمایش ثبت نشده است.</div>
            <?php
        endif;
        ?>
      </div>
    </main>
    <?php
    get_footer();
    return;
}

get_header();
?>
<main class="site-main">
  <div class="container content-card">
    <?php while (have_posts()): the_post(); ?>
      <h1 class="page-title"><?php the_title(); ?></h1>
      <div class="page-content">
        <?php the_content(); ?>
      </div>
    <?php endwhile; ?>
  </div>
</main>
<?php get_footer(); ?>
