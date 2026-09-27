<?php
/**
 * Template Name: درباره ما (Fernosa)
 */
if (!defined('ABSPATH')) { exit; }
get_header(); ?>
<main class="site-main">
  <div class="container content-card">
    <h1 class="page-title">درباره فرنوسا ژلاتو</h1>
    <div class="about-page-grid">
      <div class="about-page-text">
        <p>فرنوسا ژلاتو ترکیبی از قهوه‌ی تخصصی و ژلاتوی دست‌ساز است؛ با تمرکز روی کیفیت مواد اولیه، رست دقیق و سرو حرفه‌ای.</p>
        <p>تیم ما با عشق به جزئیات کار می‌کند: از انتخاب دانه تا طراحی منو. هدف؟ تجربه‌ای که «لوکس» باشد اما صمیمی بماند.</p>
        <div class="team-strip">
          <div class="team-card">
            <img src="" alt="Barista" loading="lazy">
            <div><strong>تیم باریستا</strong><div class="muted">عاشق عصاره‌گیری</div></div>
          </div>
          <div class="team-card">
            <img src="/public_html/wp-content/themes/fernosa-gelato/assets/img/fernosa2" alt="Chef" loading="lazy">
            <div><strong>تیم دسر</strong><div class="muted">استاد شیرینی‌سازی</div></div>
          </div>
          <div class="team-card">
            <img src="" alt="Service" loading="lazy">
            <div><strong>تیم سرویس</strong><div class="muted">دقیق و خوش‌برخورد</div></div>
          </div>
        </div>
      </div>
      <div class="about-page-media">
        <img src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=1400&q=80" alt="Cafe interior" loading="lazy">
      </div>
    </div>

    <div class="divider"></div>
    <div class="page-content">
      <?php while (have_posts()): the_post(); the_content(); endwhile; ?>
    </div>
  </div>
</main>
<?php get_footer(); ?>
