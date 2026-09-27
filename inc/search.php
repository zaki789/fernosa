<?php
/**
 * AJAX product search for header (typeahead)
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_ajax_fernosa_product_search', 'fernosa_product_search');
add_action('wp_ajax_nopriv_fernosa_product_search', 'fernosa_product_search');

function fernosa_product_search() {
    check_ajax_referer('fernosa_product_search', 'nonce');

    $q = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
    $q = trim($q);

    if (mb_strlen($q) < 2) {
        wp_send_json_success(['items' => []]);
    }

    if (!class_exists('WooCommerce')) {
        wp_send_json_success(['items' => []]);
    }

    $limit = isset($_POST['limit']) ? max(1, min(12, absint($_POST['limit']))) : 8;

    $query = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        's'              => $q,
        'posts_per_page' => $limit,
        'no_found_rows'  => true,
        'fields'         => 'ids',
    ]);

    $items = [];
    if (!empty($query->posts)) {
        foreach ($query->posts as $pid) {
            $p = wc_get_product($pid);
            if (!$p) continue;

            // Avoid listing hidden/out of stock variations etc.
            if ($p->get_status() !== 'publish') continue;

            $img = wp_get_attachment_image_src($p->get_image_id(), 'thumbnail');
            $img_url = $img ? $img[0] : '';

            $items[] = [
                'id'         => (int) $pid,
                'title'      => $p->get_name(),
                'priceHtml'  => wp_kses_post($p->get_price_html()),
                'image'      => esc_url_raw($img_url),
                'permalink'  => get_permalink($pid),
                'inStock'    => (bool) $p->is_in_stock(),
            ];
        }
    }

    wp_send_json_success(['items' => $items]);
}
