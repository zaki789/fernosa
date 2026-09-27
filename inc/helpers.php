<?php
/**
 * Helpers
 */
if (!defined('ABSPATH')) { exit; }

function fernosa_get_color_vars(): string {
    $primary = sanitize_hex_color(get_theme_mod('fernosa_primary', '#013a17')) ?: '#013a17';
    $gold    = sanitize_hex_color(get_theme_mod('fernosa_gold', '#988c75')) ?: '#988c75';

    $header_bg          = sanitize_hex_color(get_theme_mod('fernosa_header_bg', '#013a17')) ?: '#013a17';
    $header_bg_scrolled = sanitize_hex_color(get_theme_mod('fernosa_header_bg_scrolled', '#013a17')) ?: '#013a17';
    $footer_bg          = sanitize_hex_color(get_theme_mod('fernosa_footer_bg', '#013a17')) ?: '#013a17';
    $accordion_accent   = sanitize_hex_color(get_theme_mod('fernosa_accordion_accent', $gold)) ?: $gold;

    $header_link        = sanitize_hex_color(get_theme_mod('fernosa_header_link', '#f8f0e2')) ?: '#f8f0e2';
    $header_link_hover  = sanitize_hex_color(get_theme_mod('fernosa_header_link_hover', '#ffffff')) ?: '#ffffff';

    return sprintf(
        '--primary-color:%1$s;--secondary-color:%2$s;--accent-color:%2$s;--header-bg:%3$s;--header-bg-scrolled:%4$s;--footer-bg:%5$s;--accordion-accent:%6$s;--header-link:%7$s;--header-link-hover:%8$s;',
        esc_attr($primary),
        esc_attr($gold),
        esc_attr($header_bg),
        esc_attr($header_bg_scrolled),
        esc_attr($footer_bg),
        esc_attr($accordion_accent),
        esc_attr($header_link),
        esc_attr($header_link_hover)
    );
}

function fernosa_logo_html(): string {
    // 1) WordPress Custom Logo has priority
    $custom_logo_id = (int) get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $img = wp_get_attachment_image($custom_logo_id, 'full', false, ['class' => 'site-logo-img', 'loading' => 'eager']);
        if ($img) {
            return '<a class="logo site-logo" href="'.esc_url(home_url('/')).'" aria-label="'.esc_attr(get_bloginfo('name')).'">'.$img.'</a>';
        }
    }

    // 2) Fallback to bundled Fernosa logos (uploaded by the user)
    $icon = get_template_directory_uri() . '/assets/img/logo-icon.png';
    $word = get_template_directory_uri() . '/assets/img/logo-wordmark.png';

    return '<a class="logo site-logo" href="'.esc_url(home_url('/')).'" aria-label="'.esc_attr(get_bloginfo('name')).'">'
        .'<img class="site-logo-icon" src="'.esc_url($icon).'" alt="'.esc_attr__('لوگوی فرنوسا', 'fernosa-gelato').'" loading="eager">'
        .'<img class="site-logo-wordmark" src="'.esc_url($word).'" alt="Fernosa Gelato" loading="eager">'
        .'</a>';
}

function fernosa_primary_menu() {
    wp_nav_menu([
        'theme_location' => 'primary',
        'container' => false,
        'menu_class' => 'nav-links',
        'fallback_cb' => function () {
            echo '<ul class="nav-links">';
            echo '<li><a href="'.esc_url(home_url('/')).'">خانه</a></li>';
            echo '<li><a href="'.esc_url(home_url('/#menu')).'">منو</a></li>';
            echo '<li><a href="'.esc_url(home_url('/about')).'">درباره ما</a></li>';
            echo '<li><a href="'.esc_url(home_url('/contact')).'">تماس با ما</a></li>';
            echo '</ul>';
        }
    ]);
}

function fernosa_get_social_links(): array {
    $map = [
        'instagram' => ['label' => 'اینستاگرام', 'icon' => 'fa-brands fa-instagram'],
        'telegram'  => ['label' => 'تلگرام',   'icon' => 'fa-brands fa-telegram'],
        'whatsapp'  => ['label' => 'واتساپ',   'icon' => 'fa-brands fa-whatsapp'],
        'youtube'   => ['label' => 'یوتیوب',   'icon' => 'fa-brands fa-youtube'],
        'linkedin'  => ['label' => 'لینکدین',  'icon' => 'fa-brands fa-linkedin'],
        'x'         => ['label' => 'X',        'icon' => 'fa-brands fa-x-twitter'],
    ];

    $out = [];
    foreach ($map as $key => $meta) {
        $url = esc_url_raw(get_theme_mod('fernosa_social_' . $key, ''));
        if ($url) {
            $out[] = [
                'key' => $key,
                'url' => $url,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
            ];
        }
    }
    return $out;
}


/**
 * Render a product card markup (used for lazy-loading products via AJAX).
 * Keeps the same classes/DOM structure used on the front-page template.
 *
 * @param WC_Product $p
 * @return string
 */
function fernosa_product_card_html($p): string {
    if (!$p || !is_object($p) || !method_exists($p, 'get_id')) return '';
    $id = (int) $p->get_id();

    $img = wp_get_attachment_image_src($p->get_image_id(), 'large');
    $img_url = $img ? $img[0] : 'https://images.unsplash.com/photo-1511920170033-f8396924c348?auto=format&fit=crop&w=900&q=80';

    $img_full = wp_get_attachment_image_src($p->get_image_id(), 'full');
    $img_full_url = $img_full ? $img_full[0] : $img_url;

    $badges = [];
    if ($p->is_on_sale()) $badges[] = ['class'=>'badge-sale','text'=>'تخفیف'];
    if ($p->get_date_created() && (time() - $p->get_date_created()->getTimestamp() < 86400*14)) $badges[] = ['class'=>'badge-new','text'=>'جدید'];
    if (method_exists($p, 'get_total_sales') && $p->get_total_sales() > 20) $badges[] = ['class'=>'badge-hot','text'=>'پرفروش'];

    $desc = wp_trim_words(wp_strip_all_tags($p->get_short_description() ?: $p->get_description()), 18);

    ob_start(); ?>
    <div id="fg-product-<?php echo esc_attr($id); ?>" class="product-card reveal" data-product-id="<?php echo esc_attr($id); ?>" data-product-modal
         data-title="<?php echo esc_attr($p->get_name()); ?>"
         data-image="<?php echo esc_url($img_full_url); ?>">
      <button class="product-media" type="button" aria-label="<?php echo esc_attr($p->get_name()); ?>">
        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($p->get_name()); ?>" loading="lazy">
        <?php if (!empty($badges)): ?>
          <div class="product-badges">
            <?php foreach ($badges as $b): ?>
              <span class="pbadge <?php echo esc_attr($b['class']); ?>"><?php echo esc_html($b['text']); ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </button>

      <div class="product-body">
        <h3 class="product-title">
          <button type="button" class="product-title-btn" data-open-modal><?php echo esc_html($p->get_name()); ?></button>
        </h3>
        <p class="product-desc"><?php echo esc_html($desc); ?></p>
        <div class="product-foot">
          <div class="price"><?php echo wp_kses_post($p->get_price_html()); ?></div>

          <a class="btn btn-ghost product-view-link" href="<?php echo esc_url(get_permalink($id)); ?>">مشاهده محصول</a>
        </div>
      </div>
    </div>
    <?php
    return (string) ob_get_clean();
}
