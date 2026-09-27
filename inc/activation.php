<?php
/** Ensure WooCommerce checkout and account pages exist. */
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
