<?php
/**
 * WooCommerce integration (lightweight, safe)
 */
if (!defined('ABSPATH')) { exit; }


/*
 * نکته: wrapper های <main>/<div class="container"> عمداً اینجا ثبت نمی‌شوند.
 * قالب woocommerce.php خودش این wrapper ها را برای همه صفحات ووکامرس چاپ می‌کند؛
 * ثبت مجدد در این هوک‌ها باعث main تودرتو و HTML نامعتبر می‌شد.
 */

add_filter('woocommerce_enqueue_styles', '__return_empty_array');


add_filter('woocommerce_checkout_fields', function ($fields) {

    // Keep only: first name, last name, phone, address.
    $keep_billing = [
        'billing_first_name',
        'billing_last_name',
        'billing_phone',
        'billing_address_1',
        // Optional Fernosa delivery/map fields are preserved when another
        // component registers them before this filter runs.
        'fg_delivery_slot',
        'fg_map_lat',
        'fg_map_lng',
    ];

    if (isset($fields['billing']) && is_array($fields['billing'])) {
        foreach (array_keys($fields['billing']) as $key) {
            if (!in_array($key, $keep_billing, true)) {
                unset($fields['billing'][$key]);
            }
        }
    }

    // Remove shipping fields entirely (Tehran-only / single address)
    if (isset($fields['shipping'])) {
        $fields['shipping'] = [];
    }

    // Remove order notes
    if (isset($fields['order']['order_comments'])) {
        unset($fields['order']['order_comments']);
    }

    // Persian labels & required flags
    if (isset($fields['billing']['billing_first_name'])) {
        $fields['billing']['billing_first_name']['label'] = 'نام';
        $fields['billing']['billing_first_name']['required'] = true;
        $fields['billing']['billing_first_name']['priority'] = 10;
    }

    if (isset($fields['billing']['billing_last_name'])) {
        $fields['billing']['billing_last_name']['label'] = 'نام خانوادگی';
        $fields['billing']['billing_last_name']['required'] = true;
        $fields['billing']['billing_last_name']['priority'] = 20;
    }

    if (isset($fields['billing']['billing_phone'])) {
        $fields['billing']['billing_phone']['label'] = 'شماره تماس';
        $fields['billing']['billing_phone']['required'] = true;
        $fields['billing']['billing_phone']['priority'] = 30;
        $fields['billing']['billing_phone']['placeholder'] = 'مثال: 09xxxxxxxxx';
    }

    if (isset($fields['billing']['billing_address_1'])) {
        $fields['billing']['billing_address_1']['label'] = 'آدرس';
        $fields['billing']['billing_address_1']['required'] = true;
        $fields['billing']['billing_address_1']['priority'] = 40;
        $fields['billing']['billing_address_1']['placeholder'] = 'خیابان، پلاک، واحد...';
    }

    return $fields;
});


// Disable shipping address section + order notes UI.
add_filter('woocommerce_enable_order_notes_field', '__return_false', 20);





/**
 * AJAX: Lazy-load products for a given product category (infinite scroll inside accordion).
 * POST: term_id, offset, limit
 */
function fernosa_load_products() {
    check_ajax_referer('fernosa_products_load', 'nonce');

    if (!class_exists('WooCommerce')) {
        wp_send_json_error(['message' => 'WooCommerce not active.'], 400);
    }

    $term_id = isset($_POST['term_id']) ? absint($_POST['term_id']) : 0;
    $offset  = isset($_POST['offset']) ? max(0, intval($_POST['offset'])) : 0;
    $limit   = isset($_POST['limit'])  ? intval($_POST['limit']) : 9;
    $limit   = max(1, min(24, $limit)); // safety cap

    if (!$term_id) {
        wp_send_json_error(['message' => 'Missing term_id'], 400);
    }

    $term = get_term($term_id, 'product_cat');
    if (!$term || is_wp_error($term)) {
        wp_send_json_error(['message' => 'Invalid category'], 404);
    }

    $q = new WC_Product_Query([
        'limit'    => $limit,
        'offset'   => $offset,
        'status'   => 'publish',
        'orderby'  => 'menu_order',
        'order'    => 'ASC',
        'tax_query' => [[
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => [$term_id],
            'include_children' => true,
        ]],
            'type'    => ['simple','variable','grouped','external'],
]);

    $products = $q->get_products();
    if (is_array($products)) {
        $uniq = [];
        foreach ($products as $p) {
            if (!is_object($p) || !method_exists($p, 'get_id')) continue;
            $uniq[$p->get_id()] = $p;
        }
        $products = array_values($uniq);
    }

    $html = '';
    if (!empty($products)) {
        foreach ($products as $p) {
            $html .= fernosa_product_card_html($p);
        }
    }

    $loaded_total = $offset + (is_array($products) ? count($products) : 0);
    $count_q = new WP_Query([
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'tax_query' => [[
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => [$term_id],
            'include_children' => true,
        ]],
        'no_found_rows' => false,
    ]);
    $total = isset($count_q->found_posts) ? (int)$count_q->found_posts : $loaded_total;
    $has_more = $loaded_total < $total;

    wp_send_json_success([
        'html'        => $html,
        'next_offset' => $loaded_total,
        'has_more'    => $has_more ? 1 : 0,
        'returned'    => is_array($products) ? count($products) : 0,
        'total'       => $total,
    ]);
}

add_action('wp_ajax_fernosa_load_products', 'fernosa_load_products');
add_action('wp_ajax_nopriv_fernosa_load_products', 'fernosa_load_products');
