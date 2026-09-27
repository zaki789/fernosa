<?php
/**
 * Activation tasks
 */
if (!defined('ABSPATH')) { exit; }

add_action('after_switch_theme', function () {
    // Create required pages if missing (about/contact/home)
    $pages = [
        [
            'title' => 'خانه',
            'slug'  => 'home',
            'option'=> 'page_on_front',
            'set_front' => true,
        ],
        [
            'title' => 'درباره ما',
            'slug'  => 'about',
        ],
        [
            'title' => 'تماس با ما',
            'slug'  => 'contact',
        ],
    ];

    $created_any = false;

    foreach ($pages as $p) {
        $existing = get_page_by_path($p['slug']);
        if (!$existing) {
            $id = wp_insert_post([
                'post_type'   => 'page',
                'post_status' => 'publish',
                'post_title'  => $p['title'],
                'post_name'   => $p['slug'],
                'post_content'=> '',
            ]);
            if (!is_wp_error($id) && $id) {
                $existing = get_post($id);
                $created_any = true;
            }
        }

        if (!empty($p['set_front']) && $existing && $existing instanceof WP_Post) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $existing->ID);
        }
    }

    if ($created_any) {
        flush_rewrite_rules();
    }
});
/**
 * Ensure WooCommerce core pages exist (Cart/Checkout/My Account).
 * This prevents 404 on account/cart/checkout links if Woo pages were not created yet.
 */
function fernosa_ensure_wc_pages() {
    if (!class_exists('WooCommerce')) { return; }

    $map = [
        'cart'      => ['title' => 'سبد خرید',   'slug' => 'cart',       'shortcode' => '[woocommerce_cart]'],
        'checkout'  => ['title' => 'پرداخت',     'slug' => 'checkout',   'shortcode' => '[woocommerce_checkout]'],
        'myaccount' => ['title' => 'حساب کاربری','slug' => 'my-account', 'shortcode' => '[woocommerce_my_account]'],
    ];

    foreach ($map as $key => $cfg) {
        $opt = 'woocommerce_' . $key . '_page_id';
        $id  = (int) get_option($opt, 0);
        $post = $id > 0 ? get_post($id) : null;

        if ($post instanceof WP_Post && $post->post_status === 'publish') {
            continue;
        }

        $existing = get_page_by_path($cfg['slug']);
        if (!$existing) {
            $new_id = wp_insert_post([
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $cfg['title'],
                'post_name'    => $cfg['slug'],
                'post_content' => $cfg['shortcode'],
            ]);
            if (!is_wp_error($new_id) && $new_id) {
                update_option($opt, (int) $new_id);
            }
        } else {
            update_option($opt, (int) $existing->ID);
        }
    }
}

add_action('after_switch_theme', function () {
    if (current_user_can('manage_options')) {
        fernosa_ensure_wc_pages();
        flush_rewrite_rules();
    }
}, 30);

add_action('admin_init', function () {
    if (current_user_can('manage_options')) {
        fernosa_ensure_wc_pages();
    }
});
