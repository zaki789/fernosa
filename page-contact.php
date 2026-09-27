<?php
/**
 * Template Name: تماس با ما (Fernosa)
 */
if (!defined('ABSPATH')) { exit; }

$address = get_theme_mod('fernosa_footer_address', 'تقاطع سردار جنگلو خیابان مخبری - ضلع جنوب شرقی - فرنوسا');
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
$gmap_embed = 'https://www.google.com/maps?q=' . rawurlencode($lat . ',' . $lng) . '&output=embed';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fernosa_contact_nonce']) && wp_verify_nonce($_POST['fernosa_contact_nonce'], 'fernosa_contact')) {
    $name  = sanitize_text_field($_POST['name'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $msg   = sanitize_textarea_field($_POST['message'] ?? '');

    $to = get_option('admin_email');
    $subject = 'پیام جدید از فرم تماس - فرنوسا ژلاتو';
    $body = "نام: {$name}\nتلفن: {$phone}\n\nپیام:\n{$msg}\n";
    wp_mail($to, $subject, $body);
    $sent = true;
}

get_header(); ?>
<main class="site-main">
  <div class="container content-card">
    <h1 class="page-title">تماس با ما</h1>

    <?php if ($sent): ?>
      <div class="notice-success">پیام شما با موفقیت ارسال شد. ممنون!</div>
    <?php endif; ?>

    <div class="contact-grid">
      <form class="form-card" method="post">
        <?php wp_nonce_field('fernosa_contact', 'fernosa_contact_nonce'); ?>
        <label>نام و نام خانوادگی
          <input type="text" name="name" required>
        </label>
        <label>شماره تماس
          <input type="tel" name="phone" required>
        </label>
        <label>پیام شما
          <textarea name="message" rows="6" required></textarea>
        </label>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane"></i> ارسال پیام</button>
      </form>

      <div class="contact-info">
        <div class="info-card">
          <h3>اطلاعات تماس</h3>
          <ul class="footer-info">
            <li><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($address); ?></li>
            <li><i class="fa-solid fa-phone"></i> <?php echo esc_html($phone); ?></li>
            <li><i class="fa-brands fa-whatsapp"></i> <?php echo esc_html($mobile); ?></li>
            <li><i class="fa-solid fa-envelope"></i> <?php echo esc_html($email); ?></li>
            <li><i class="fa-solid fa-clock"></i> هر روز: <?php echo esc_html($hours); ?></li>
          </ul>
        </div>

        <div class="info-card">
          <h3>لوکیشن</h3>
          <div class="map-embed-wrap">
            <iframe class="gmap-embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              src="<?php echo esc_url($gmap_embed); ?>"></iframe>
          </div>
          <div class="map-links">
            <a class="btn btn-ghost btn-small" href="<?php echo esc_url($gmap); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-map-location-dot"></i> گوگل‌مپ</a>
          <a class="btn btn-ghost btn-small" href="<?php echo esc_url($neshan); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-location-dot"></i> نشان</a>
            <?php if (!empty($balad)): ?>
              <a class="btn btn-ghost btn-small" href="<?php echo esc_url($balad); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-route"></i> مسیر در بلد</a>
            <?php endif; ?>
          </div>
          <div class="muted" style="margin-top:10px">مختصات: <?php echo esc_html($lat); ?>, <?php echo esc_html($lng); ?></div>
        </div>
      </div>
    </div>

    <div class="divider"></div>
    <div class="page-content">
      <?php while (have_posts()): the_post(); the_content(); endwhile; ?>
    </div>
  </div>
</main>
<?php get_footer(); ?>
