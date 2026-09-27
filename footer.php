<?php
if (!defined('ABSPATH')) { exit; }
$address = get_theme_mod('fernosa_footer_address', 'تقاطع سردار جنگل و خیابان مخبری - ضلع جنوب شرقی - فرنوسا');
$phone   = get_theme_mod('fernosa_contact_phone', '021-44466911');
$mobile  = get_theme_mod('fernosa_contact_mobile', '+989105449950');
$email   = get_theme_mod('fernosa_contact_email', 'info@fernosagelato.ir');
$hours   = get_theme_mod('fernosa_contact_hours', '8:00 تا 23:00');
$lat     = get_theme_mod('fernosa_map_lat', '35.756086');
$lng     = get_theme_mod('fernosa_map_lng', '51.324281');
$lat2    = get_theme_mod('fernosa_neshan_lat', '35.75625269656163');
$lng2    = get_theme_mod('fernosa_neshan_lng', '51.324252463027506');
$balad   = get_theme_mod('fernosa_map_balad_url', 'https://balad.ir/directions/driving?destination=51.32419465618318%2C35.75611171091805');
$gmap    = 'https://www.google.com/maps?q=' . rawurlencode($lat . ',' . $lng);
$neshan  = 'https://neshan.org/maps/@' . rawurlencode($lat2 . ',' . $lng2) . ',17z';

$social  = function_exists('fernosa_get_social_links') ? fernosa_get_social_links() : [];
?>
<?php if ( fg_bool_opt('show_footer', get_theme_mod('fg_show_footer', true)) ) : ?>
<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-col">
      <h4 class="footer-title">درباره فرنوسا</h4>
      <p class="footer-text">
        فرنوسا ژلاتو جایی‌ست برای عاشقان قهوه‌ی تخصصی و ژلاتوی دست‌ساز. کیفیت، جزئیات و طعمِ به‌یادماندنی، وسواسِ شیرین ماست.
      </p>
      <div class="footer-badges">
        <span class="badge"><i class="fa-solid fa-certificate"></i> گواهی کیفیت</span>
        <span class="badge"><i class="fa-solid fa-shield-halved"></i> پرداخت امن</span>
      </div>

      <?php if (!empty($social)): ?>
        <div class="footer-social" aria-label="شبکه‌های اجتماعی">
          <?php foreach ($social as $s): ?>
            <a class="social-link" href="<?php echo esc_url($s['url']); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($s['label']); ?>">
              <i class="<?php echo esc_attr($s['icon']); ?>"></i>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="footer-col">
      <h4 class="footer-title">لینک‌های سریع</h4>
      <?php
      wp_nav_menu([
        'theme_location' => 'footer',
        'container' => false,
        'menu_class' => 'footer-links',
        'fallback_cb' => function(){
            echo '<ul class="footer-links">';
            echo '<li><a href="'.esc_url(home_url('/')).'">خانه</a></li>';
            echo '<li><a href="'.esc_url(home_url('/#menu')).'">منو</a></li>';
            echo '<li><a href="'.esc_url(home_url('/about')).'">درباره ما</a></li>';
            echo '<li><a href="'.esc_url(home_url('/contact')).'">تماس با ما</a></li>';
            echo '</ul>';
        }
      ]);
      ?>
    </div>

    <div class="footer-col">
      <h4 class="footer-title">اطلاعات تماس</h4>
      <ul class="footer-info">
        <li><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($address); ?></li>
        <li><i class="fa-solid fa-phone"></i> <?php echo esc_html($phone); ?></li>
        <li><i class="fa-solid fa-envelope"></i> <?php echo esc_html($email); ?></li>
        <li><i class="fa-solid fa-clock"></i> هر روز: <?php echo esc_html($hours); ?></li>
      </ul>
      <div class="mini-map" aria-label="Map">
        <a class="mini-map-link" href="<?php echo esc_url($gmap); ?>" target="_blank" rel="noopener">
          <div class="mini-map-pin"><i class="fa-solid fa-location-crosshairs"></i></div>
          <div class="mini-map-grid"></div>
        </a>
      </div>
      <div class="map-links">
        <a class="btn btn-ghost btn-small" href="<?php echo esc_url($gmap); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-map-location-dot"></i> گوگل‌مپ</a>
        <a class="btn btn-ghost btn-small" href="<?php echo esc_url($neshan); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-location-dot"></i> مسیر در نشان</a>
        <?php if (!empty($balad)): ?>
          <a class="btn btn-ghost btn-small" href="<?php echo esc_url($balad); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-route"></i> مسیر در بلد</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="container footer-bottom">
    <div class="footer-rights">© <?php echo esc_html(date('Y')); ?> فرنوسا ژلاتو — همه حقوق محفوظ است.</div>
    <div class="footer-logos">
      <span class="trust"><i class="fa-solid fa-award"></i> نماد اعتماد</span>
      <span class="trust"><i class="fa-solid fa-receipt"></i> قوانین و مقررات</span>
    </div>
  </div>
</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
