<?php
if (!defined('ABSPATH')) { exit; }
get_header();

$hero_title = get_theme_mod('fernosa_hero_title', 'تجربه‌ای لوکس از قهوه و ژلاتو');
$hero_desc  = get_theme_mod('fernosa_hero_desc', 'قهوه‌های تخصصی، دسرهای دست‌ساز و ژلاتوی ایتالیایی — با کیفیتی که به خاطر می‌ماند.');

$hero_bg_id = (int) get_theme_mod('fernosa_hero_bg', 0);
$hero_bg = '';
if ($hero_bg_id) {
    $src = wp_get_attachment_image_src($hero_bg_id, 'full');
    $hero_bg = $src ? $src[0] : '';
}
if (!$hero_bg) {
    $hero_bg = get_template_directory_uri() . '/assets/img/fernosa1.jpg';
}


/**
 * Build accordion sections from real WooCommerce categories.
 * - Uses top-level product categories (parent = 0)
 * - Shows only categories with products (hide_empty = true)
 */
function fernosa_get_menu_categories(int $limit = 12): array {
    if (!class_exists('WooCommerce')) return [];

    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => 0,
        'orderby'    => 'menu_order',
        'order'      => 'ASC',
        'number'     => $limit,
    ]);

    if (is_wp_error($terms) || empty($terms)) return [];
    return $terms;
}

// function fernosa_get_products_for_cat(int $term_id, int $offset = 0, int $limit = 12): array {
//     if (!class_exists('WooCommerce')) return [];

//     $limit  = max(1, min(48, (int)$limit));
//     $offset = max(0, (int)$offset);

//     $q = new WC_Product_Query([
//         'limit'   => $limit,
//         'offset'  => $offset,
//         'status'  => 'publish',
//         'orderby' => 'menu_order',
//         'order'   => 'ASC',
//         'tax_query' => [[
//             'taxonomy' => 'product_cat',
//             'field'    => 'term_id',
//             'terms'    => [$term_id],
//             'include_children' => true,
//         ]],
//         // Avoid returning variations as standalone items
//         'type'    => ['simple','variable','grouped','external'],
//         'return'  => 'objects',
//     ]);

//     $products = $q->get_products();
//     if (!is_array($products)) return [];

//     // De-duplicate by product ID (some setups/plugins can produce duplicates)
//     $uniq = [];
//     foreach ($products as $p) {
//         if (!is_object($p) || !method_exists($p, 'get_id')) continue;
//         $uniq[$p->get_id()] = $p;
//     }
//     return array_values($uniq);
// }

// function fernosa_get_products_count_for_cat(int $term_id): int {
//     if (!class_exists('WooCommerce')) return 0;

//     $q = new WP_Query([
//         'post_type'      => 'product',
//         'post_status'    => 'publish',
//         'posts_per_page' => 1,
//         'fields'         => 'ids',
//         'tax_query'      => [[
//             'taxonomy' => 'product_cat',
//             'field'    => 'term_id',
//             'terms'    => [$term_id],
//             'include_children' => true,
//         ]],
//         'no_found_rows'  => false,
//     ]);

//     return isset($q->found_posts) ? (int)$q->found_posts : 0;
// }





function fernosa_get_products_for_cat(int $term_id, int $offset = 0, int $limit = 12): array {
    if (!class_exists('WooCommerce')) return [];

    $limit  = max(1, min(48, (int)$limit));
    $offset = max(0, (int)$offset);

    // کش‌گذاری با کلید یکتا
    $cache_key = 'fg_products_' . $term_id . '_' . $offset . '_' . $limit;
    $cached = get_transient($cache_key);
    if (false !== $cached) {
        return $cached;
    }

    $q = new WC_Product_Query([
        'limit'   => $limit,
        'offset'  => $offset,
        'status'  => 'publish',
        'orderby' => 'menu_order',
        'order'   => 'ASC',
        'tax_query' => [[
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => [$term_id],
            'include_children' => true,
        ]],
        'type'    => ['simple','variable','grouped','external'],
        'return'  => 'objects',
    ]);

    $products = $q->get_products();
    if (!is_array($products)) $products = [];

    // حذف تکراری‌ها
    $uniq = [];
    foreach ($products as $p) {
        if (!is_object($p) || !method_exists($p, 'get_id')) continue;
        $uniq[$p->get_id()] = $p;
    }
    $result = array_values($uniq);

    // ذخیره در کش برای ۱ ساعت
    set_transient($cache_key, $result, HOUR_IN_SECONDS);
    return $result;
}


function fernosa_get_products_count_for_cat(int $term_id): int {
    if (!class_exists('WooCommerce')) return 0;

    $cache_key = 'fg_count_' . $term_id;
    $cached = get_transient($cache_key);
    if (false !== $cached) {
        return (int) $cached;
    }

    $q = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'tax_query'      => [[
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => [$term_id],
            'include_children' => true,
        ]],
        'no_found_rows'  => false,
    ]);

    $count = isset($q->found_posts) ? (int)$q->found_posts : 0;
    set_transient($cache_key, $count, HOUR_IN_SECONDS);
    return $count;
}


///////////////////////////////////////













$cats = fernosa_get_menu_categories(24);
$menu_view = get_theme_mod('fernosa_menu_view', 'cards');
$cats = fernosa_get_menu_categories(20);
$intro_video_id = absint(get_theme_mod('fernosa_intro_video', 0));
$intro_video = $intro_video_id ? wp_get_attachment_url($intro_video_id) : '';
?>
<main class="site-main">
  <?php if ($intro_video): ?>
    <section class="fg-intro-video" aria-label="معرفی فرنوسا">
      <video class="fg-intro-video__media" autoplay muted playsinline preload="metadata" poster="<?php echo esc_url($hero_bg); ?>">
        <source src="<?php echo esc_url($intro_video); ?>" type="video/mp4">
      </video>
      <div class="fg-intro-video__overlay"></div>
      <div class="fg-intro-video__content container">
        <span class="fg-kicker">FERNOSA GELATO</span>
        <h1>طعم یک تجربه متفاوت</h1>
        <button class="btn btn-primary fg-intro-video__menu" type="button">مشاهده منو</button>
      </div>
    </section>
  <?php endif; ?>

  <?php if ( fg_bool_opt('show_hero', get_theme_mod('fg_show_hero', true)) ) : ?>
<section class="hero" style="background-image:url('<?php echo esc_url($hero_bg); ?>');">
    <div class="hero-overlay"></div>
    <div class="container hero-inner">
      <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>
      <p class="hero-desc"><?php echo esc_html($hero_desc); ?></p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="#menu" data-scroll="#menu"><i class="fa-solid fa-book-open"></i> مشاهده منو</a>
      </div>
      <button class="scroll-down" data-scroll="#menu" aria-label="Scroll down">
        <i class="fa-solid fa-chevron-down"></i>
      </button>
    </div>
  </section>
<?php endif; ?>


  <?php
  $selected_new_ids = array_values(array_filter(array_map('absint', preg_split('/[\s,،]+/', (string) get_theme_mod('fernosa_new_product_ids', '')))));
  $selected_new_ids = array_slice(array_unique($selected_new_ids), 0, 12);
  $new_products = [];

  if (class_exists('WooCommerce') && !empty($selected_new_ids)) {
      $products_by_id = [];
      foreach ($selected_new_ids as $selected_id) {
          $product = wc_get_product($selected_id);
          if ($product && $product->is_visible() && $product->get_status() === 'publish') {
              $products_by_id[$selected_id] = $product;
          }
      }
      foreach ($selected_new_ids as $selected_id) {
          if (isset($products_by_id[$selected_id])) {
              $new_products[] = $products_by_id[$selected_id];
          }
      }
  }
  ?>

  <?php if (!empty($new_products)): ?>
  <section class="fg-new-products" aria-labelledby="fg-new-title">
    <div class="container">
      <div class="section-head">
        <div>
          <span class="section-kicker">JUST ARRIVED</span>
          <h2 class="section-title" id="fg-new-title">محصولات جدید</h2>
          <p class="section-sub">محصولات منتخب فرنوسا</p>
        </div>
      </div>
      <div class="fg-new-scroller" dir="rtl" tabindex="0" aria-label="محصولات جدید">
        <div class="fg-new-track">
          <?php foreach ($new_products as $p): ?>
            <div class="fg-new-slide"><?php echo fernosa_product_card_html($p); ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>


  <section class="menu-section" id="menu">
    <div class="container">
      <div class="section-head">
        <div><span class="section-kicker">DISCOVER</span><h2 class="section-title">منوی فرنوسا</h2><p class="section-sub">محصولات و طعم‌های منتخب فرنوسا</p></div>
        
      </div>

      <?php if ($menu_view === 'tabs' || $menu_view === 'cards'): ?>
      <div class="fg-catnav fg-catnav--<?php echo esc_attr($menu_view); ?>" id="fgCatNav" role="tablist" aria-label="<?php esc_attr_e('Product categories', 'fernosa-gelato'); ?>">
        <?php if (!class_exists('WooCommerce')): ?>
          <div class="notice-card">ووکامرس فعال نیست. برای نمایش دسته‌بندی‌ها و محصولات واقعی، افزونه WooCommerce را نصب/فعال کنید.</div>
        <?php else: ?>
          <?php foreach ($cats as $i => $cat):
              $thumb_id = (int) get_term_meta($cat->term_id, 'thumbnail_id', true);
              $thumb = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'thumbnail') : false;
              $thumb_url = $thumb ? $thumb[0] : '';
              $active = ($i === 0) ? 'true' : 'false';
          ?>
            <button class="fg-catbtn" type="button"
                    role="tab"
                    aria-selected="<?php echo esc_attr($active); ?>"
                    data-fg-tab="<?php echo esc_attr((int)$cat->term_id); ?>">
              <span class="acc-icon">
                <?php if ($thumb_url): ?>
                  <img class="acc-icon-img" src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($cat->name); ?>" loading="lazy">
                <?php else: ?>
                  <i class="fa-solid fa-layer-group"></i>
                <?php endif; ?>
              </span>
              <span class="acc-title"><?php echo esc_html($cat->name); ?></span>
            </button>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <?php if (class_exists('WooCommerce')): ?>
      <div class="fg-tabpanels" id="fgTabPanels">
        <?php foreach ($cats as $i => $cat):
            $initial_limit = 12;
            $products = fernosa_get_products_for_cat((int)$cat->term_id, 0, $initial_limit);
            $thumb_id = (int) get_term_meta($cat->term_id, 'thumbnail_id', true);
            $thumb = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'thumbnail') : false;
            $thumb_url = $thumb ? $thumb[0] : '';
            $active_class = ($i === 0) ? 'is-active' : '';
        ?>
          <div class="fg-panel <?php echo esc_attr($active_class); ?>" role="tabpanel" data-fg-panel="<?php echo esc_attr((int)$cat->term_id); ?>">
            <div class="product-cards">
              <?php if (empty($products)): ?>
                <div class="notice-card">برای این دسته هنوز محصولی ثبت نشده است.</div>
              <?php else: foreach ($products as $p):
                  $pid = $p->get_id();
                  $img = $p->get_image_id() ? wp_get_attachment_image_src($p->get_image_id(), 'medium') : false;
                  $img_url = $img ? $img[0] : wc_placeholder_img_src('medium');
                  $price_html = $p->get_price_html();
              ?>
                <div class="product-card reveal">
                  <div class="product-media" aria-label="<?php echo esc_attr($p->get_name()); ?>">
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($p->get_name()); ?>" loading="lazy">
                  </div>
                  <div class="product-body">
                    <h3 class="product-title"><?php echo esc_html($p->get_name()); ?></h3>
                    <?php $desc = wp_trim_words(wp_strip_all_tags($p->get_short_description() ?: $p->get_description()), 18); ?>
                    <?php if ($desc !== ''): ?>
                      <p class="product-desc"><?php echo esc_html($desc); ?></p>
                    <?php endif; ?>
                    <div class="product-price"><?php echo wp_kses_post($price_html); ?></div>

                    <div class="product-actions">
                    </div>

                  </div>
                </div>
              <?php endforeach; endif; ?>
            </div>
            <?php
              $loaded = is_array($products) ? count($products) : 0;
              $total = fernosa_get_products_count_for_cat((int)$cat->term_id);
              if ($total <= 0) { $total = $loaded; }
              $has_more = $total > $loaded;
            ?>
            <div class="fg-loadmore-wrap" data-fg-loadwrap
                 data-term-id="<?php echo esc_attr((int)$cat->term_id); ?>"
                 data-offset="<?php echo esc_attr($loaded); ?>"
                 data-limit="<?php echo esc_attr($initial_limit); ?>"
                 data-has-more="<?php echo $has_more ? '1' : '0'; ?>">
              <div class="fg-sentinel" aria-hidden="true"></div>
              <div class="fg-loading" hidden>در حال بارگذاری…</div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

<?php else: ?>
<div class="accordion" id="menuAccordion">
        <?php if (!class_exists('WooCommerce')): ?>
          <div class="notice-card">ووکامرس فعال نیست. برای نمایش دسته‌بندی‌ها و محصولات واقعی، افزونه WooCommerce را نصب/فعال کنید.</div>
        <?php elseif (empty($cats)): ?>
          <div class="notice-card">
            هنوز دسته‌بندی محصولی پیدا نشد.
            <div class="muted">Products → Categories: یک یا چند دسته بسازید و برای هر دسته چند محصول اضافه کنید.</div>
          </div>
        <?php else: ?>
          <?php foreach ($cats as $i => $cat):
              $initial_limit = 12;
              $products = fernosa_get_products_for_cat((int)$cat->term_id, 0, $initial_limit);
              $is_open = 'false';
              $open_class = '';
              $thumb_id = (int) get_term_meta($cat->term_id, 'thumbnail_id', true);
              $thumb = $thumb_id ? wp_get_attachment_image_src($thumb_id, 'thumbnail') : false;
              $thumb_url = $thumb ? $thumb[0] : '';
          ?>
          <div class="accordion-item <?php echo esc_attr($open_class); ?>">
            <button class="accordion-header" aria-expanded="<?php echo esc_attr($is_open); ?>">
              <span class="acc-icon">
                <?php if ($thumb_url): ?>
                  <img class="acc-icon-img" src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($cat->name); ?>" loading="lazy">
                <?php else: ?>
                  <i class="fa-solid fa-layer-group"></i>
                <?php endif; ?>
              </span>
              <span class="acc-title"><?php echo esc_html($cat->name); ?></span>
              <span class="acc-arrow"><i class="fa-solid fa-chevron-down"></i></span>
            </button>

            <div class="accordion-panel">
              <?php if (empty($products)): ?>
                <div class="notice-card">برای این دسته هنوز محصولی ثبت نشده است.</div>
              <?php else: ?>
                <div class="grid product-cards">
                  <?php foreach ($products as $p):
                      $id = $p->get_id();
                      $img = wp_get_attachment_image_src($p->get_image_id(), 'large');
                      $img_url = $img ? $img[0] : get_template_directory_uri() . '/assets/img/logo-icon.png';
                      $img_full = wp_get_attachment_image_src($p->get_image_id(), 'full');
                      $img_full_url = $img_full ? $img_full[0] : $img_url;
                      $badges = [];
                      if ($p->is_on_sale()) $badges[] = ['class'=>'badge-sale','text'=>'تخفیف'];
                      if ($p->get_date_created() && (time() - $p->get_date_created()->getTimestamp() < 86400*14)) $badges[] = ['class'=>'badge-new','text'=>'جدید'];
                      if ($p->get_total_sales() > 20) $badges[] = ['class'=>'badge-hot','text'=>'پرفروش'];
                      $desc = wp_trim_words(wp_strip_all_tags($p->get_short_description() ?: $p->get_description()), 18);
                  ?>
                  <div class="product-card reveal">

                    <div class="product-media" aria-label="<?php echo esc_attr($p->get_name()); ?>">
                      <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($p->get_name()); ?>" loading="lazy">
                      <?php if (!empty($badges)): ?>
                        <div class="product-badges">
                          <?php foreach ($badges as $b): ?>
                            <span class="pbadge <?php echo esc_attr($b['class']); ?>"><?php echo esc_html($b['text']); ?></span>
                          <?php endforeach; ?>
                        </div>
                      <?php endif; ?>
                    </div>

                    <div class="product-body">
                      <h3 class="product-title"><?php echo esc_html($p->get_name()); ?></h3>
                      <p class="product-desc"><?php echo esc_html($desc); ?></p>
                      <div class="product-foot">
                        <div class="price"><?php echo wp_kses_post($p->get_price_html()); ?></div>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
                <?php
                  $loaded = count($products);
                  $total = fernosa_get_products_count_for_cat((int)$cat->term_id);
                  if ($total <= 0) { $total = $loaded; }
                  $has_more = $total > $loaded;
                ?>
                <div class="fg-loadmore-wrap" data-fg-loadwrap
                     data-term-id="<?php echo esc_attr((int)$cat->term_id); ?>"
                     data-offset="<?php echo esc_attr($loaded); ?>"
                     data-limit="<?php echo esc_attr($initial_limit); ?>"
                     data-has-more="<?php echo $has_more ? '1' : '0'; ?>">
                  <div class="fg-sentinel" data-fg-sentinel aria-hidden="true"></div>
                  <div class="fg-loading" hidden>در حال بارگذاری…</div>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
<?php endif; ?>
  </section>

  <section class="about-snippet" id="about">
    <div class="container about-grid">
      <div class="about-text reveal">
        <h2 class="section-title">داستان فرنوسا</h2>
        <p class="section-sub">
          از اولین عصاره‌ی قهوه تا آخرین قاشق ژلاتو، ما دنبال «بهترین نسخه»‌ایم. نتیجه؟ یک تجربه‌ی گرم، خوش‌عطر و حسابی شیک.
        </p>
        <a class="btn btn-ghost" href="<?php echo esc_url(home_url('/about')); ?>"><i class="fa-solid fa-arrow-left"></i> درباره ما</a>
      </div>
      <div class="about-media reveal">
        <img src="<?php echo esc_url(FERNOSA_GELATO_URI . '/assets/img/fernosa1.jpg'); ?>" alt="Cafe" loading="lazy">
      </div>
    </div>
  </section>

  <section class="cta-strip">
    <div class="container cta-inner">
      <div class="cta-text">
        <h3>سفارش یا همکاری؟</h3>
        <p>پیام بدهید؛ سریع پاسخ می‌دهیم.</p>
      </div>
      <a class="btn btn-primary" href="<?php echo esc_url(home_url('/contact')); ?>"><i class="fa-solid fa-paper-plane"></i> تماس با ما</a>
    </div>
  </section>

  <!-- Product modal (no page navigation) -->
  <div class="fg-modal" id="fgModal" aria-hidden="true">
    <div class="fg-modal__overlay" data-fg-close></div>
    <div class="fg-modal__panel" role="dialog" aria-modal="true" aria-label="جزئیات محصول">
      <button class="fg-modal__close" type="button" data-fg-close aria-label="بستن"><i class="fa-solid fa-xmark"></i></button>
      <div class="fg-modal__media-only">
        <img id="fgModalImg" src="" alt="" loading="eager">
      </div>
    </div>
  </div>

</main>
<?php get_footer(); ?>
