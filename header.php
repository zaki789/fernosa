<?php
if (!defined('ABSPATH')) { exit; }
?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php wp_head(); ?>
</head>
<?php $invert_logo = (bool) get_theme_mod('fernosa_logo_invert', true); ?>
<body <?php body_class($invert_logo ? 'fernosa-logo-invert' : ''); ?> style="<?php echo esc_attr(fernosa_get_color_vars()); ?>">
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#siteMain"><?php esc_html_e('پرش به محتوا', 'fernosa-gelato'); ?></a>

<?php if ( fg_bool_opt('show_topbar', get_theme_mod('fg_show_topbar', true)) ) : ?>
<header class="site-header" id="siteHeader">
  <div class="container header-inner">
    <?php echo fernosa_logo_html(); ?>

    <nav class="main-nav" aria-label="<?php esc_attr_e('Main menu', 'fernosa-gelato'); ?>">
      <?php fernosa_primary_menu(); ?>
    </nav>
    <form class="header-search fg-ajax-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" autocomplete="off">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input
        type="search"
        id="fernosaSearchInput"
        name="s"
        value="<?php echo isset($_GET['s']) ? esc_attr(wp_unslash($_GET['s'])) : ''; ?>"
        placeholder="جستجوی محصول..."
        aria-label="جستجوی محصول"
        autocomplete="off"
      />
      <input type="hidden" name="post_type" value="product" />
      <div class="fg-search-dropdown" id="fernosaSearchDropdown" hidden></div>
    </form>

<div class="header-actions">
      <?php
        $show_cart    = (bool) get_theme_mod('fernosa_header_show_cart', true);
        $show_account = (bool) get_theme_mod('fernosa_header_show_account', true);
      ?>

      <?php if (class_exists('WooCommerce')): ?>
        <?php if ($show_cart): ?>
          <a class="icon-btn cart-btn" href="<?php echo esc_url(wc_get_cart_url()); ?>" aria-label="<?php esc_attr_e('Cart', 'fernosa-gelato'); ?>">
            <i class="fa-solid fa-bag-shopping"></i>
            <span class="cart-count"><?php echo esc_html(WC()->cart ? WC()->cart->get_cart_contents_count() : 0); ?></span>
          </a>
        <?php endif; ?>

        <?php if ($show_account): ?>
          <a class="icon-btn account-btn" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" aria-label="<?php esc_attr_e('Account', 'fernosa-gelato'); ?>">
            <i class="fa-regular fa-user"></i>
          </a>
        <?php endif; ?>
      <?php else: ?>
        <?php if ($show_account): ?>
          <a class="icon-btn account-btn" href="<?php echo esc_url(wp_login_url()); ?>"><i class="fa-regular fa-user"></i></a>
        <?php endif; ?>
      <?php endif; ?>

      <button class="icon-btn hamburger" id="hamburgerBtn" aria-label="<?php esc_attr_e('Open menu', 'fernosa-gelato'); ?>">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </div>

  <div class="mobile-drawer" id="mobileDrawer" aria-hidden="true">
    <div class="mobile-drawer-inner">
      <button class="icon-btn close-drawer" id="closeDrawerBtn" aria-label="<?php esc_attr_e('Close menu', 'fernosa-gelato'); ?>">
        <i class="fa-solid fa-xmark"></i>
      </button>
      <?php fernosa_primary_menu(); ?>
      <div class="mobile-quick">
        <?php if (class_exists('WooCommerce')): ?>
          <a href="<?php echo esc_url(wc_get_cart_url()); ?>"><i class="fa-solid fa-bag-shopping"></i> سبد خرید</a>
          <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><i class="fa-regular fa-user"></i> حساب کاربری</a>
        <?php else: ?>
          <a href="<?php echo esc_url(wp_login_url()); ?>"><i class="fa-regular fa-user"></i> ورود</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<?php endif; ?>
