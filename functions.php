<?php
/**
 * Fernosa Gelato Theme - functions.php
 * PHP 8.0+
 */
if (!defined('ABSPATH')) { exit; }

define('FERNOSA_GELATO_VERSION', '1.0.9');
define('FERNOSA_GELATO_DIR', get_template_directory());
define('FERNOSA_GELATO_URI', get_template_directory_uri());

require_once FERNOSA_GELATO_DIR . '/inc/setup.php';
require_once FERNOSA_GELATO_DIR . '/inc/assets.php';
require_once FERNOSA_GELATO_DIR . '/inc/helpers.php';
require_once FERNOSA_GELATO_DIR . '/inc/search.php';

if (class_exists('WooCommerce')) {
    require_once FERNOSA_GELATO_DIR . '/inc/woocommerce.php';
}

require_once FERNOSA_GELATO_DIR . '/inc/activation.php';

if (is_admin()) {
    require_once FERNOSA_GELATO_DIR . '/inc/admin/settings-page.php';
}

/**
 * Preload hero background image on front page for faster LCP.
 */
add_action( 'wp_head', 'fg_preload_hero_image' );
function fg_preload_hero_image() {
    if ( ! is_front_page() ) {
        return;
    }
    $hero_bg_id = (int) get_theme_mod( 'fernosa_hero_bg', 0 );
    $hero_bg    = '';
    if ( $hero_bg_id ) {
        $src = wp_get_attachment_image_src( $hero_bg_id, 'full' );
        $hero_bg = $src ? $src[0] : '';
    }
    if ( ! $hero_bg ) {
        $hero_bg = get_template_directory_uri() . '/assets/img/fernosa1.jpg';
    }
    echo '<link rel="preload" as="image" href="' . esc_url( $hero_bg ) . '" fetchpriority="high">' . "\n";
}

/* FG_WC_POLISH_V12 */
add_action('wp_enqueue_scripts', function() {
	if ( function_exists('is_woocommerce') && ( is_woocommerce() || is_checkout() || is_account_page() ) ) {
		wp_enqueue_style('fg-woocommerce-polish', get_template_directory_uri() . '/assets/css/fg-woocommerce-polish.css', array(), fg_asset_version('/assets/css/fg-woocommerce-polish.css'));
		wp_enqueue_script('fg-checkout-coupon', get_template_directory_uri() . '/assets/js/fg-checkout-coupon.js', array(), fg_asset_version('/assets/js/fg-checkout-coupon.js'), true);
	}
});

add_filter('woocommerce_no_available_payment_methods_message', function($msg) {
	return 'درگاه پرداخت فعالی پیدا نشد. لطفاً از مسیر «ووکامرس → پیکربندی → پرداخت‌ها» یک روش پرداخت را فعال کنید.';
});

/*
 * ترجمه فارسی رشته‌های ووکامرس — یک فیلتر واحد (قبلاً ۶ فیلتر همپوشان بود)
 * FG_I18N_CONSOLIDATED
 */
add_filter('gettext', function($translated, $text, $domain) {
	if ('woocommerce' !== $domain) return $translated;

	static $map = null;
	if (null === $map) {
		$map = array(
			// فرم پرداخت
			'Billing details' => 'اطلاعات خریدار',
			'Have a coupon?' => 'کد تخفیف دارید؟',
			'Click here to enter your code' => 'اینجا وارد کنید',
			'Apply coupon' => 'اعمال کد',
			'Coupon code' => 'کد تخفیف',
			'Place order' => 'ثبت سفارش',
			// سبد خرید			'Subtotal' => 'جمع جزء',
			'Total' => 'مبلغ نهایی',
			'Product' => 'محصول',
			'Price' => 'قیمت',
			'Quantity' => 'تعداد',
			'Remove item' => 'حذف',			// ارسال
			'Shipping' => 'ارسال',
			'Free shipping' => 'ارسال رایگان',
			'Shipping to' => 'ارسال به',
			'Shipping to %s' => 'ارسال به %s',
			'Calculate shipping' => 'محاسبه هزینه ارسال',
			'Enter your address to view shipping options.' => 'برای دیدن روش‌های ارسال، آدرس را وارد کنید.',
			// سفارش
			'Order received' => 'ثبت سفارش',
			'Order details' => 'جزئیات سفارش',
			'Order notes' => 'یادداشت سفارش',
			'Notes about your order, e.g. special notes for delivery.' => 'توضیحات تکمیلی برای سفارش و ارسال.',
			'Additional information' => 'اطلاعات تکمیلی',
			'Billing address' => 'آدرس صورتحساب',
			'Shipping address' => 'آدرس ارسال',
			'Payment method' => 'روش پرداخت',
			'Payment' => 'پرداخت',
			'Change address' => 'تغییر آدرس',
			// پرداخت
			'Direct bank transfer' => 'واریز مستقیم بانکی',
			'Cash on delivery' => 'پرداخت در محل',
			'N/A' => '—',
			// محصول
			'Choose an option' => 'یک گزینه را انتخاب کنید',
			'No products were found matching your selection.' => 'محصولی مطابق جستجوی شما پیدا نشد.',
		);
	}

	return isset($map[$text]) ? $map[$text] : $translated;
}, 20, 3);

add_filter('woocommerce_enable_order_notes_field', '__return_false');
add_filter('woocommerce_cart_needs_shipping_address', '__return_false');

/* FG_SEARCH_RESULTS_STYLE */
add_action('wp_enqueue_scripts', function() {
	if ( is_search() ) {
		wp_enqueue_style('fg-search-results', get_template_directory_uri() . '/assets/css/fg-search-results.css', array(), fg_asset_version('/assets/css/fg-search-results.css'));
	}
});

/* FG_SINGLE_PRODUCT_STYLE */
add_action('wp_enqueue_scripts', function() {
	if ( function_exists('is_product') && is_product() ) {
		wp_enqueue_style('fg-single-product', get_template_directory_uri() . '/assets/css/fg-single-product.css', array(), fg_asset_version('/assets/css/fg-single-product.css'));
	}
});

/* FG_SINGLE_PRODUCT_I18N */
add_filter('woocommerce_sale_flash', function($html, $post, $product) {
	return '<span class="onsale">تخفیف</span>';
}, 10, 3);

/* FG_COUPON_SINGLE_RENDER */
add_action('wp', function() {
	if ( function_exists('is_checkout') && is_checkout() ) {
		remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
		add_action('woocommerce_before_checkout_form', function() {
			if ( function_exists('wc_get_template') ) {
				wc_get_template('checkout/form-coupon.php');
			}
		}, 10);
	}
});

/* FG_DELIVERY_INFO */
add_action('customize_register', function($wp_customize) {
	$wp_customize->add_section('fg_order_info', array(
		'title' => 'اطلاعات ارسال و آماده‌سازی',
		'priority' => 55,
	));

	$wp_customize->add_setting('fg_prep_time', array('default' => 'آماده‌سازی: حدود ۲۰ تا ۳۰ دقیقه', 'transport' => 'refresh'));
	$wp_customize->add_control('fg_prep_time', array(
		'label' => 'متن آماده‌سازی سفارش',
		'section' => 'fg_order_info',
		'type' => 'text',
	));

	$wp_customize->add_setting('fg_delivery_time', array('default' => 'ارسال در تهران: حدود ۳۰ تا ۶۰ دقیقه', 'transport' => 'refresh'));
	$wp_customize->add_control('fg_delivery_time', array(
		'label' => 'متن زمان ارسال',
		'section' => 'fg_order_info',
		'type' => 'text',
	));

	$wp_customize->add_setting('fg_show_order_info', array('default' => true, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_show_order_info', array(
		'label' => 'نمایش این بخش در پرداخت',
		'section' => 'fg_order_info',
		'type' => 'checkbox',
	));
});

function fg_render_order_info_box() {
	if ( ! fg_bool_opt('show_order_info', get_theme_mod('fg_show_order_info', true)) ) return;
	$prep = fg_opt('prep_time', get_theme_mod('fg_prep_time', ''));
	$del  = fg_opt('delivery_time', get_theme_mod('fg_delivery_time', ''));
	if ( ! $prep && ! $del ) return;
	echo '<div class="fg-order-info-box">';
	echo '<div class="fg-order-info-title"><i class="fa-solid fa-clock"></i> ' . esc_html__('اطلاعات سفارش', 'fernosa-gelato') . '</div>';
	if ($prep) echo '<div class="fg-order-info-row">' . esc_html($prep) . '</div>';
	if ($del)  echo '<div class="fg-order-info-row">' . esc_html($del) . '</div>';
	echo '</div>';
}

add_action('woocommerce_before_checkout_form', 'fg_render_order_info_box', 5);

/* FG_TOGGLES_AND_COLORS */
add_action('customize_register', function($wp_customize) {

	$wp_customize->add_section('fg_layout_toggles', array(
		'title' => 'نمایش بخش‌ها',
		'priority' => 52,
	));

	$wp_customize->add_setting('fg_show_topbar', array('default' => true, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_show_topbar', array(
		'label' => 'نمایش منوی بالایی (هدر)',
		'section' => 'fg_layout_toggles',
		'type' => 'checkbox',
	));

	$wp_customize->add_setting('fg_show_hero', array('default' => true, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_show_hero', array(
		'label' => 'نمایش هیرو سکشن صفحه اصلی',
		'section' => 'fg_layout_toggles',
		'type' => 'checkbox',
	));

	$wp_customize->add_setting('fg_show_footer', array('default' => true, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_show_footer', array(
		'label' => 'نمایش فوتر',
		'section' => 'fg_layout_toggles',
		'type' => 'checkbox',
	));

	$wp_customize->add_section('fg_colors', array(
		'title' => 'رنگ‌های کارت و دسته‌بندی',
		'priority' => 53,
	));

	$wp_customize->add_setting('fg_product_card_bg', array('default' => 'rgba(255,255,255,.06)', 'transport' => 'refresh'));
	$wp_customize->add_control('fg_product_card_bg', array(
		'label' => 'پس‌زمینه کارت محصول',
		'section' => 'fg_colors',
		'type' => 'text',
		'description' => 'مثال: rgba(255,255,255,.06) یا #1b1b1b',
	));

	$wp_customize->add_setting('fg_category_bg', array('default' => 'rgba(255,255,255,.05)', 'transport' => 'refresh'));
	$wp_customize->add_control('fg_category_bg', array(
		'label' => 'پس‌زمینه آیتم دسته‌بندی',
		'section' => 'fg_colors',
		'type' => 'text',
		'description' => 'مثال: rgba(255,255,255,.05) یا #222222',
	));
});

add_action('wp_head', function() {
	$pc = get_theme_mod('fg_product_card_bg', 'rgba(255,255,255,.06)');
	$cb = get_theme_mod('fg_category_bg', 'rgba(255,255,255,.05)');
	echo '<style>:root{--fg-product-card-bg:' . esc_attr($pc) . ';--fg-category-bg:' . esc_attr($cb) . ';}</style>';
}, 50);

/* FG_POINTS_REWARDS */
add_action('customize_register', function($wp_customize) {
	$wp_customize->add_section('fg_rewards', array(
		'title' => 'امتیاز و تخفیف مشتری',
		'priority' => 56,
	));

	$wp_customize->add_setting('fg_rewards_enabled', array('default' => false, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_rewards_enabled', array(
		'label' => 'فعال‌سازی سیستم امتیازدهی',
		'section' => 'fg_rewards',
		'type' => 'checkbox',
	));

	$wp_customize->add_setting('fg_points_per_order', array('default' => 10, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_points_per_order', array(
		'label' => 'امتیاز هر خرید (پیش‌فرض)',
		'section' => 'fg_rewards',
		'type' => 'number',
	));

	$wp_customize->add_setting('fg_points_threshold', array('default' => 100, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_points_threshold', array(
		'label' => 'حد امتیاز برای دریافت کد تخفیف',
		'section' => 'fg_rewards',
		'type' => 'number',
	));

	$wp_customize->add_setting('fg_coupon_percent', array('default' => 10, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_coupon_percent', array(
		'label' => 'درصد تخفیف کد جایزه',
		'section' => 'fg_rewards',
		'type' => 'number',
		'description' => 'مثال: 10 یعنی ۱۰٪',
	));

	$wp_customize->add_setting('fg_coupon_days', array('default' => 30, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_coupon_days', array(
		'label' => 'اعتبار کد جایزه (روز)',
		'section' => 'fg_rewards',
		'type' => 'number',
	));
});

function fg_get_user_points($user_id) {
	return (int) get_user_meta($user_id, 'fg_points', true);
}
function fg_set_user_points($user_id, $points) {
	update_user_meta($user_id, 'fg_points', max(0, (int)$points));
}

function fg_user_active_reward_coupon($user_id) {
	$code = (string) get_user_meta($user_id, 'fg_reward_coupon', true);
	if (!$code) return '';
	$coupon_id = wc_get_coupon_id_by_code($code);
	if (!$coupon_id) return '';
	$coupon = new WC_Coupon($code);
	$exp = $coupon->get_date_expires();
	if ($exp && $exp->getTimestamp() < time()) return '';
	return $code;
}

function fg_create_reward_coupon_for_user($user_id) {
	$percent = (int) fg_opt('coupon_percent', get_theme_mod('fg_coupon_percent', 10));
	$days = (int) fg_opt('coupon_days', get_theme_mod('fg_coupon_days', 30));
	$code = 'FG' . $user_id . '-' . wp_generate_password(6, false, false);

	$coupon = new WC_Coupon();
	$coupon->set_code($code);
	$coupon->set_discount_type('percent');
	$coupon->set_amount(max(1, $percent));
	$coupon->set_individual_use(true);
	$coupon->set_usage_limit(1);
	$coupon->set_usage_limit_per_user(1);
	$coupon->set_email_restrictions(array());
	$coupon->set_free_shipping(false);
	if ($days > 0) {
		$coupon->set_date_expires( (new DateTime())->modify('+' . $days . ' days') );
	}
	$coupon->save();

	update_user_meta($user_id, 'fg_reward_coupon', $code);
	return $code;
}

add_action('woocommerce_order_status_completed', function($order_id) {
	if ( ! fg_bool_opt('rewards_enabled', get_theme_mod('fg_rewards_enabled', false)) ) return;
	$order = wc_get_order($order_id);
	if (!$order) return;
	$user_id = $order->get_user_id();
	if (!$user_id) return;

	if ( get_post_meta($order_id, '_fg_points_awarded', true) ) return;

	$add = (int) fg_opt('points_per_order', get_theme_mod('fg_points_per_order', 10));
	$points = fg_get_user_points($user_id) + max(0, $add);
	fg_set_user_points($user_id, $points);
	update_post_meta($order_id, '_fg_points_awarded', 1);

	$threshold = (int) fg_opt('points_threshold', get_theme_mod('fg_points_threshold', 100));
	if ($threshold > 0 && $points >= $threshold) {
		if ( ! fg_user_active_reward_coupon($user_id) ) {
			$code = fg_create_reward_coupon_for_user($user_id);
			$user = get_user_by('id', $user_id);
			if ($user && $user->user_email) {
				$subject = 'کد تخفیف هدیه فرنوسا';
				$message = 'سلام! به خاطر خریدهای شما یک کد تخفیف هدیه گرفتید: ' . $code;
				wp_mail($user->user_email, $subject, $message);
			}
		}
	}
});

add_action('woocommerce_account_dashboard', function() {
	if ( ! fg_bool_opt('rewards_enabled', get_theme_mod('fg_rewards_enabled', false)) ) return;
	$user_id = get_current_user_id();
	if (!$user_id) return;
	$points = fg_get_user_points($user_id);
	$threshold = (int) fg_opt('points_threshold', get_theme_mod('fg_points_threshold', 100));
	$coupon = fg_user_active_reward_coupon($user_id);

	echo '<div class="fg-order-info-box" style="margin-top:14px">';
	echo '<div class="fg-order-info-title"><i class="fa-solid fa-star"></i> امتیاز شما</div>';
	echo '<div class="fg-order-info-row">امتیاز فعلی: ' . esc_html($points) . '</div>';
	if ($threshold > 0) echo '<div class="fg-order-info-row">حد دریافت جایزه: ' . esc_html($threshold) . '</div>';
	if ($coupon) echo '<div class="fg-order-info-row">کد تخفیف فعال شما: <strong style="color:#988c75">' . esc_html($coupon) . '</strong></div>';
	echo '</div>';
});

/* FG_CHECKOUT_DELIVERY_MAP */
add_action('customize_register', function($wp_customize){
	$wp_customize->add_section('fg_delivery_map', array(
		'title' => 'پرداخت: زمان تحویل و لوکیشن',
		'priority' => 57,
	));

	$wp_customize->add_setting('fg_enable_map', array('default' => true, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_enable_map', array(
		'label' => 'نمایش انتخاب لوکیشن روی نقشه در پرداخت',
		'section' => 'fg_delivery_map',
		'type' => 'checkbox',
	));

	$wp_customize->add_setting('fg_default_lat', array('default' => '35.7219', 'transport' => 'refresh'));
	$wp_customize->add_control('fg_default_lat', array(
		'label' => 'عرض جغرافیایی پیش‌فرض (Lat)',
		'section' => 'fg_delivery_map',
		'type' => 'text',
	));

	$wp_customize->add_setting('fg_default_lng', array('default' => '51.4697', 'transport' => 'refresh'));
	$wp_customize->add_control('fg_default_lng', array(
		'label' => 'طول جغرافیایی پیش‌فرض (Lng)',
		'section' => 'fg_delivery_map',
		'type' => 'text',
	));

	$wp_customize->add_setting('fg_delivery_slots', array('default' => '10:00-12:00|12:00-14:00|14:00-16:00|16:00-18:00|18:00-20:00', 'transport' => 'refresh'));
	$wp_customize->add_control('fg_delivery_slots', array(
		'label' => 'بازه‌های زمانی تحویل (با | جدا کنید)',
		'section' => 'fg_delivery_map',
		'type' => 'text',
		'description' => 'مثال: 10:00-12:00|12:00-14:00|...',
	));

	$wp_customize->add_setting('fg_delivery_eta_minutes', array('default' => 45, 'transport' => 'refresh'));
	$wp_customize->add_control('fg_delivery_eta_minutes', array(
		'label' => 'زمان تقریبی رسیدن (دقیقه) برای انیمیشن',
		'section' => 'fg_delivery_map',
		'type' => 'number',
	));
});

add_filter('woocommerce_checkout_fields', function($fields){
	$slots_raw = (string) fg_opt('delivery_slots', get_theme_mod('fg_delivery_slots', ''));
	$slots = array();
	foreach ( explode('|', $slots_raw) as $s ) {
		$s = trim($s);
		if ($s !== '') $slots[$s] = $s;
	}
	if (empty($slots)) $slots = array('12:00-14:00' => '12:00-14:00');

	$fields['billing']['fg_delivery_slot'] = array(
		'type' => 'select',
		'label' => 'بازه زمانی تحویل',
		'required' => true,
		'options' => $slots,
		'priority' => 45,
		'class' => array('form-row-wide'),
	);

	$fields['billing']['fg_map_lat'] = array(
		'type' => 'hidden',
		'required' => false,
		'priority' => 46,
	);
	$fields['billing']['fg_map_lng'] = array(
		'type' => 'hidden',
		'required' => false,
		'priority' => 47,
	);

	return $fields;
}, 30);

add_action('woocommerce_checkout_update_order_meta', function($order_id){
	if ( isset($_POST['fg_delivery_slot']) ) update_post_meta($order_id, '_fg_delivery_slot', sanitize_text_field($_POST['fg_delivery_slot']));
	if ( isset($_POST['fg_map_lat']) ) update_post_meta($order_id, '_fg_map_lat', sanitize_text_field($_POST['fg_map_lat']));
	if ( isset($_POST['fg_map_lng']) ) update_post_meta($order_id, '_fg_map_lng', sanitize_text_field($_POST['fg_map_lng']));
});

add_action('woocommerce_admin_order_data_after_billing_address', function($order){
	$slot = $order->get_meta('_fg_delivery_slot');
	$lat  = $order->get_meta('_fg_map_lat');
	$lng  = $order->get_meta('_fg_map_lng');
	if ($slot) echo '<p><strong>بازه تحویل:</strong> ' . esc_html($slot) . '</p>';
	if ($lat && $lng) echo '<p><strong>لوکیشن:</strong> ' . esc_html($lat) . ', ' . esc_html($lng) . '</p>';
});

add_action('wp_enqueue_scripts', function(){
	if ( function_exists('is_checkout') && is_checkout() && fg_bool_opt('enable_map', get_theme_mod('fg_enable_map', true)) ) {
		wp_enqueue_style('fg-leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
		wp_enqueue_script('fg-leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
		wp_enqueue_style('fg-checkout-map', get_template_directory_uri() . '/assets/css/fg-checkout-map.css', array(), fg_asset_version('/assets/css/fg-checkout-map.css'));
		wp_enqueue_script('fg-checkout-map', get_template_directory_uri() . '/assets/js/fg-checkout-map.js', array('fg-leaflet'), fg_asset_version('/assets/js/fg-checkout-map.js'), true);
		wp_localize_script('fg-checkout-map', 'FG_DELIVERY', array(
			'lat' => (string) fg_opt('default_lat', get_theme_mod('fg_default_lat', '35.7219')),
			'lng' => (string) fg_opt('default_lng', get_theme_mod('fg_default_lng', '51.4697')),
			'eta' => (int) fg_opt('delivery_eta_minutes', get_theme_mod('fg_delivery_eta_minutes', 45)),
		));
	}
});

/* FG_SETTINGS_HELPERS */
function fg_opt($key, $default = null) {
	$opt = get_option('fg_theme_settings', array());
	if (is_array($opt) && array_key_exists($key, $opt)) return $opt[$key];
	return $default;
}
function fg_bool_opt($key, $default = true) {
	$val = fg_opt($key, $default ? 1 : 0);
	return !empty($val);
}

/* FG_I18N_EXTRA2 */
add_filter('gettext', function($translated, $text, $domain) {
	if ( 'woocommerce' !== $domain ) return $translated;

	$map = array(
		'Free shipping' => 'ارسال رایگان',
		'Shipping' => 'ارسال',
		'Subtotal' => 'جمع جزء',
		'Total' => 'مبلغ نهایی',		'Apply coupon' => 'اعمال کد تخفیف',
		'Coupon code' => 'کد تخفیف',
		'Have a coupon?' => 'کد تخفیف دارید؟',
		'Click here to enter your code' => 'اینجا کلیک کنید',
		'Order received' => 'ثبت سفارش',
		'Order details' => 'جزئیات سفارش',
		'Billing address' => 'آدرس صورتحساب',
		'Shipping address' => 'آدرس ارسال',
		'Payment method' => 'روش پرداخت',
		'Payment' => 'پرداخت',
		'Change address' => 'تغییر آدرس',
	);
	return isset($map[$text]) ? $map[$text] : $translated;
}, 30, 3);

/* FG_ORDER_PROGRESS_ADMIN */
add_action('add_meta_boxes', function(){
	add_meta_box('fg_order_progress', 'فرنوسا: پروسه آماده‌سازی و ارسال', 'fg_order_progress_metabox', 'shop_order', 'side', 'high');
});

function fg_order_progress_metabox($post){
	$order = wc_get_order($post->ID);
	if (!$order) return;

	$prep = (int) $order->get_meta('_fg_prep_minutes');
	$status = (string) $order->get_meta('_fg_delivery_step');
	$pickup = (string) $order->get_meta('_fg_pickup_time');

	wp_nonce_field('fg_order_progress_save', 'fg_order_progress_nonce');

	?>
	<p><label><strong>زمان آماده‌سازی (دقیقه)</strong></label><br>
	<input type="number" min="0" style="width:100%" name="fg_prep_minutes" value="<?php echo esc_attr($prep); ?>" placeholder="مثلاً 20"></p>

	<p><label><strong>مرحله سفارش</strong></label><br>
	<select name="fg_delivery_step" style="width:100%">
		<option value="preparing" <?php selected($status, 'preparing'); ?>>در حال آماده‌سازی</option>
		<option value="picked" <?php selected($status, 'picked'); ?>>تحویل پیک شد</option>
		<option value="onway" <?php selected($status, 'onway'); ?>>در مسیر</option>
		<option value="delivered" <?php selected($status, 'delivered'); ?>>تحویل شد</option>
	</select></p>

	<p style="margin-top:10px">
		<label><input type="checkbox" name="fg_set_pickup_now" value="1"> ثبت زمان «تحویل پیک شد» همین الان</label>
	</p>

	<?php if($pickup): ?>
	<p style="color:#666;margin:8px 0 0">آخرین زمان ثبت شده: <code><?php echo esc_html($pickup); ?></code></p>
	<?php endif; ?>

	<p style="margin-top:10px;color:#666">نکته: مقصد از لوکیشن پرداخت ذخیره می‌شود. مبدا از پنل «تنظیمات فرنوسا» گرفته می‌شود.</p>
	<?php
}

add_action('save_post_shop_order', function($post_id){
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if ( ! isset($_POST['fg_order_progress_nonce']) || ! wp_verify_nonce($_POST['fg_order_progress_nonce'], 'fg_order_progress_save') ) return;
	if ( ! current_user_can('edit_shop_order', $post_id) ) return;

	$order = wc_get_order($post_id);
	if (!$order) return;

	$prep = isset($_POST['fg_prep_minutes']) ? (int) $_POST['fg_prep_minutes'] : 0;
	$step = isset($_POST['fg_delivery_step']) ? sanitize_text_field($_POST['fg_delivery_step']) : 'preparing';

	$order->update_meta_data('_fg_prep_minutes', max(0, $prep));
	$order->update_meta_data('_fg_delivery_step', $step);

	if ( ! empty($_POST['fg_set_pickup_now']) ) {
		$order->update_meta_data('_fg_pickup_time', current_time('mysql'));
		if ($step === 'preparing') {
			$order->update_meta_data('_fg_delivery_step', 'picked');
		}
	}

	$order->save();
}, 10, 1);

/* FG_THANKYOU_PROGRESS */
/**
 * مختصات مبدا (کافه) — از تنظیمات پنل «تنظیمات فرنوسا» خوانده می‌شود
 * (origin_lat / origin_lng در inc/admin/settings-page.php) و در نبود آن به مقدار پیش‌فرض برمی‌گردد.
 */
function fg_get_origin_coords(){
	$lat = fg_opt('origin_lat', '35.75610');
	$lng = fg_opt('origin_lng', '51.32430');
	return array(
		'lat' => is_numeric($lat) ? (string) $lat : '35.75610',
		'lng' => is_numeric($lng) ? (string) $lng : '51.32430',
	);
}

add_action('woocommerce_thankyou', function($order_id){
	$order = wc_get_order($order_id);
	if (!$order) return;

	$slot = (string) $order->get_meta('_fg_delivery_slot');
	$lat  = (string) $order->get_meta('_fg_map_lat');
	$lng  = (string) $order->get_meta('_fg_map_lng');
	$prep = (int) $order->get_meta('_fg_prep_minutes');
	if ($prep <= 0) $prep = (int) fg_opt('delivery_eta_minutes', 20);
	$step = (string) $order->get_meta('_fg_delivery_step');
	if (!$step) $step = 'preparing';

	$pickup = (string) $order->get_meta('_fg_pickup_time');

	echo '<section class="fg-track">';
	echo '<h2 class="fg-track-title">پیگیری سفارش</h2>';
	echo '<div class="fg-track-card">';
	echo '<div class="fg-track-top">';
	echo '<div class="fg-track-badges">';
	if ($slot) echo '<span class="fg-badge"><i class="fa-regular fa-clock"></i> بازه تحویل: ' . esc_html($slot) . '</span>';
	echo '<span class="fg-badge"><i class="fa-solid fa-kitchen-set"></i> آماده‌سازی: ' . esc_html($prep) . ' دقیقه</span>';
	echo '</div>';
	echo '<div class="fg-track-est"><span class="fg-track-est-label">زمان تقریبی رسیدن:</span> <strong data-fg-eta>در حال محاسبه…</strong></div>';
	echo '</div>';

	echo '<ol class="fg-steps" data-fg-step="'.esc_attr($step).'">';
	echo '<li data-step="preparing"><span class="dot"></span><span class="t">در حال آماده‌سازی</span></li>';
	echo '<li data-step="picked"><span class="dot"></span><span class="t">تحویل پیک شد</span></li>';
	echo '<li data-step="onway"><span class="dot"></span><span class="t">در مسیر</span></li>';
	echo '<li data-step="delivered"><span class="dot"></span><span class="t">تحویل شد</span></li>';
	echo '</ol>';

	echo '<div class="fg-track-bar"><div class="fill" data-fg-fill></div></div>';
	echo '<p class="fg-track-note">این زمان تخمینی است و با توجه به ترافیک و آماده‌سازی ممکن است تغییر کند.</p>';
	echo '</div>';
	echo '</section>';

	$origin = fg_get_origin_coords();
	wp_enqueue_style('fg-lux', get_template_directory_uri() . '/assets/css/fg-lux-checkout.css', array(), fg_asset_version('/assets/css/fg-lux-checkout.css'));
	wp_enqueue_script('fg-track', get_template_directory_uri() . '/assets/js/fg-order-tracking.js', array(), fg_asset_version('/assets/js/fg-order-tracking.js'), true);
	wp_localize_script('fg-track', 'FG_TRACK', array(
		'prep' => $prep,
		'step' => $step,
		'pickup' => $pickup,
		'origin' => $origin,
		'dest' => array('lat' => $lat, 'lng' => $lng),
	));
}, 20);

/* FG_SHIPPING_DEST_TRIM */
add_filter('woocommerce_shipping_destination_html', function($html){
	$html = wp_strip_all_tags($html);
	$html = trim(preg_replace('/\s+/', ' ', $html));
	if (mb_strlen($html) > 120) {
		$html = mb_substr($html, 0, 120) . '…';
	}
	return '<p class="woocommerce-shipping-destination">' . esc_html($html) . '</p>';
}, 20);


/* FG_CHECKOUT_ORDER_NOTES */
add_filter('woocommerce_checkout_fields', function($fields){
	if (!isset($fields['order'])) $fields['order'] = array();
	$fields['order']['order_comments'] = array(
		'type' => 'textarea',
		'class' => array('form-row-wide'),
		'label' => 'یادداشت سفارش',
		'placeholder' => 'مثلاً زنگ نزنید، درِ واحد… / توضیحات برای سفارش',
		'required' => false,
		'priority' => 80,
	);
	return $fields;
}, 55);


/* ============================================
   تنظیم ترتیب نمایش محصولات
   ============================================ */
/**
 * - دسته‌بندی‌های بیکری و پیستری: بر اساس ترتیب دستی (menu_order)
 * - سایر محصولات: بر اساس عنوان (الفبا)
 * - دراپ‌داون مرتب‌سازی کاربر همچنان فعال است
 */
add_action( 'pre_get_posts', function( $query ) {
    // خروج در صورت ادمین یا نبودن کوئری اصلی
    if ( is_admin() || ! $query->is_main_query() || ! function_exists('is_shop') || ! function_exists('is_product_category') ) {
        return;
    }
    
    // فقط در صفحات محصولات (فروشگاه و دسته‌بندی)
    if ( is_shop() || is_product_category() ) {
        
        // اگر کاربر از دراپ‌داون مرتب‌سازی استفاده کرده باشد، دخالت نکن
        if ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) {
            return;
        }
        
        // بررسی کنید که در کدام دسته‌بندی هستیم
        if ( is_product_category( array( 'bikery', 'pistary' ) ) ) {
            // مرتب‌سازی بر اساس ترتیب دستی برای بیکری و پیستری
            $query->set( 'orderby', 'menu_order title' );
            $query->set( 'order', 'ASC' );
        } else {
            // مرتب‌سازی بر اساس عنوان (الفبا) برای سایر دسته‌بندی‌ها
            $query->set( 'orderby', 'title' );
            $query->set( 'order', 'ASC' );
        }
    }
}, 999 );

/* FG_DISABLE_ADD_TO_CART */
add_action('init', function () {
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
});

// Add favicon
function fernosa_add_favicon() {
    echo '<link rel="icon" type="image/png" href="' . esc_url( get_template_directory_uri() . '/assets/img/logo-icon.png' ) . '">' . "\n";
    echo '<link rel="shortcut icon" href="' . esc_url( get_template_directory_uri() . '/assets/img/logo-icon.png' ) . '">' . "\n";
}
add_action( 'wp_head', 'fernosa_add_favicon' );

/**
 * اضافه کردن defer به اسکریپت اصلی قالب
 */
add_filter( 'script_loader_tag', function( $tag, $handle, $src ) {
    if ( 'fernosa-main' === $handle ) {
        return str_replace( ' src', ' defer src', $tag );
    }
    return $tag;
}, 10, 3 );

/*
 * حذف نسخه‌های تکراری Font Awesome که افزونه‌های دیگر (نه قالب خودمان) لود می‌کنند.
 * FA لوکال خود قالب (handle: fernosa-fa6) دست نمی‌خورد.
 */
add_action('wp_enqueue_scripts', function() {
    global $wp_styles;
    if (!isset($wp_styles, $wp_styles->queue) || !is_array($wp_styles->queue)) return;

    $keep = 'fernosa-fa6'; // FA لوکال قالب
    foreach ($wp_styles->queue as $handle) {
        if (!isset($wp_styles->registered[$handle])) continue;
        $src = (string) $wp_styles->registered[$handle]->src;

        $is_fa_handle = (stripos($handle, 'font-awesome') !== false || stripos($handle, 'fontawesome') !== false);
        $is_fa_src    = ($src && stripos($src, 'font-awesome') !== false);
        // فقط نسخه‌های خارجی (CDN یا افزونه) را حذف کن، نه فایل لوکال قالب را
        $is_local_ours = (stripos($src, get_template_directory_uri()) !== false);

        if ($handle !== $keep && ($is_fa_handle || $is_fa_src) && !$is_local_ours) {
            wp_dequeue_style($handle);
        }
    }
}, 100);



/* FG_DISABLE_CART */
add_action('template_redirect', function () {
    if (function_exists('is_cart') && is_cart()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});
