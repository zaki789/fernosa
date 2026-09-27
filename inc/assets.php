<?php
/**
 * Enqueue CSS/JS — همه Assetها لوکال (فونت و آیکن از خود قالب سرویس می‌شود)
 */
if (!defined('ABSPATH')) { exit; }

/** Return a cache-busting version for a theme asset, with a safe fallback. */
function fg_asset_version(string $rel): int|string {
    $path = FERNOSA_GELATO_DIR . $rel;
    if (file_exists($path)) {
        $mtime = filemtime($path);
        if (false !== $mtime) {
            return $mtime;
        }
    }
    return FERNOSA_GELATO_VERSION;
}

add_action('wp_enqueue_scripts', function () {
    $ver = FERNOSA_GELATO_VERSION;

    // 1) فونت Vazirmatn — لوکال (assets/css/fonts.css → assets/fonts/vazormatn/)
    wp_enqueue_style(
        'fernosa-fonts',
        FERNOSA_GELATO_URI . '/assets/css/fonts.css',
        [],
        fg_asset_version('/assets/css/fonts.css')
    );

    // 2) Font Awesome 6 — لوکال (assets/font-awesome/all.min.css → webfonts/)
    wp_enqueue_style(
        'fernosa-fa6',
        FERNOSA_GELATO_URI . '/assets/font-awesome/all.min.css',
        [],
        fg_asset_version('/assets/font-awesome/all.min.css')
    );

    // 3) استایل اصلی قالب
    wp_enqueue_style(
        'fernosa-theme',
        FERNOSA_GELATO_URI . '/assets/css/theme.css',
        ['fernosa-fonts', 'fernosa-fa6'],
        fg_asset_version('/assets/css/theme.css')
    );

    // 4) style.css قالب (هدر تم + اصلاحات جزئی)
    wp_enqueue_style('fernosa-style', get_stylesheet_uri(), ['fernosa-theme'], $ver);

    // 5) اسکریپت اصلی (defer از طریق script_loader_tag در functions.php)
    wp_enqueue_script(
        'fernosa-main',
        FERNOSA_GELATO_URI . '/assets/js/main.js',
        [],
        fg_asset_version('/assets/js/main.js'),
        true
    );

    // 6) جستجوی زنده هدر
    wp_enqueue_script(
        'fernosa-search',
        FERNOSA_GELATO_URI . '/assets/js/search.js',
        [],
        fg_asset_version('/assets/js/search.js'),
        true
    );

    wp_localize_script('fernosa-main', 'FERNOSA', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'homeUrl' => home_url('/'),
        'isRtl'   => is_rtl(),
        'nonce'   => wp_create_nonce('fernosa_cart_qty'),
        'productsNonce' => wp_create_nonce('fernosa_products_load'),
        'searchNonce' => wp_create_nonce('fernosa_product_search'),
    ]);
});
