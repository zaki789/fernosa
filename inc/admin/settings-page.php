<?php
/**
 * Fernosa Theme Admin Panel
 */
defined('ABSPATH') || exit;

function fg_settings_defaults() {
	return array(
		'show_topbar' => 1,
		'show_hero' => 1,
		'show_footer' => 1,
		'product_card_bg' => 'rgba(255,255,255,.06)',
		'category_bg' => 'rgba(255,255,255,.05)',
		'prep_time' => 'آماده‌سازی: حدود ۲۰ تا ۳۰ دقیقه',
		'delivery_time' => 'ارسال در تهران: حدود ۳۰ تا ۶۰ دقیقه',
		'show_order_info' => 1,
		'enable_map' => 1,
		'default_lat' => '35.7219',
		'default_lng' => '51.4697',
		'origin_lat' => '35.7219',
		'origin_lng' => '51.4697',
		'delivery_slots' => '10:00-12:00|12:00-14:00|14:00-16:00|16:00-18:00|18:00-20:00',
		'delivery_eta_minutes' => 45,
		'rewards_enabled' => 0,
		'points_per_order' => 10,
		'points_threshold' => 100,
		'coupon_percent' => 10,
		'coupon_days' => 30,
	);
}

function fg_get_settings() {
	$opt = get_option('fg_theme_settings', array());
	if (!is_array($opt)) $opt = array();
	return array_merge(fg_settings_defaults(), $opt);
}

add_action('admin_menu', function(){
	add_menu_page(
		'تنظیمات فرنوسا',
		'تنظیمات فرنوسا',
		'manage_options',
		'fg-theme-settings',
		'fg_render_admin_settings_page',
		'dashicons-coffee',
		61
	);
});

add_action('admin_init', function(){
	register_setting('fg_theme_settings_group', 'fg_theme_settings', array(
		'type' => 'array',
		'sanitize_callback' => 'fg_sanitize_settings',
		'default' => fg_settings_defaults(),
	));
});

function fg_sanitize_coordinate($value, $minimum, $maximum, $fallback) {
	$value = trim((string) wp_unslash($value));

	if (!is_numeric($value)) {
		return $fallback;
	}

	$value = (float) $value;
	if ($value < $minimum || $value > $maximum) {
		return $fallback;
	}

	return (string) $value;
}

function fg_sanitize_settings($input){
	$defaults = fg_settings_defaults();
	$input = is_array($input) ? wp_unslash($input) : array();
	$out = array();

	foreach ($defaults as $key => $default) {
		$out[$key] = isset($input[$key]) ? $input[$key] : $default;
	}

	foreach (array('show_topbar', 'show_hero', 'show_footer', 'show_order_info', 'enable_map', 'rewards_enabled') as $key) {
		$out[$key] = !empty($out[$key]) ? 1 : 0;
	}

	$out['delivery_eta_minutes'] = min(240, max(0, absint($out['delivery_eta_minutes'])));
	$out['points_per_order'] = min(100000, max(0, absint($out['points_per_order'])));
	$out['points_threshold'] = min(1000000, max(1, absint($out['points_threshold'])));
	$out['coupon_percent'] = min(100, max(0, absint($out['coupon_percent'])));
	$out['coupon_days'] = min(3650, max(1, absint($out['coupon_days'])));

	foreach (array('product_card_bg', 'category_bg', 'prep_time', 'delivery_time', 'delivery_slots') as $key) {
		$out[$key] = sanitize_text_field($out[$key]);
	}

	$out['default_lat'] = fg_sanitize_coordinate($out['default_lat'], -90, 90, $defaults['default_lat']);
	$out['default_lng'] = fg_sanitize_coordinate($out['default_lng'], -180, 180, $defaults['default_lng']);
	$out['origin_lat'] = fg_sanitize_coordinate($out['origin_lat'], -90, 90, $defaults['origin_lat']);
	$out['origin_lng'] = fg_sanitize_coordinate($out['origin_lng'], -180, 180, $defaults['origin_lng']);

	return $out;
}

function fg_render_admin_settings_page(){
	if (!current_user_can('manage_options')) return;
	$s = fg_get_settings();
	?>
	<div class="wrap">
		<h1>تنظیمات قالب فرنوسا ژلاتو</h1>
		<p style="color:#666;max-width:900px">این پنل برای کنترل ظاهر، پرداخت، زمان تحویل، نقشه و سیستم امتیاز طراحی شده است. بعد از ذخیره، تغییرات بلافاصله روی سایت اعمال می‌شود.</p>

		<form method="post" action="options.php">
			<?php settings_fields('fg_theme_settings_group'); ?>
			<?php $opt = get_option('fg_theme_settings', array()); ?>

			<h2 class="title">نمایش بخش‌ها</h2>
			<table class="form-table" role="presentation">
				<tr><th scope="row">منوی بالایی (هدر)</th><td><label><input type="checkbox" name="fg_theme_settings[show_topbar]" value="1" <?php checked($s['show_topbar'],1); ?>> نمایش داده شود</label></td></tr>
				<tr><th scope="row">هیرو سکشن صفحه اصلی</th><td><label><input type="checkbox" name="fg_theme_settings[show_hero]" value="1" <?php checked($s['show_hero'],1); ?>> نمایش داده شود</label></td></tr>
				<tr><th scope="row">فوتر</th><td><label><input type="checkbox" name="fg_theme_settings[show_footer]" value="1" <?php checked($s['show_footer'],1); ?>> نمایش داده شود</label></td></tr>
			</table>

			<hr>

			<h2 class="title">رنگ‌ها</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">پس‌زمینه کارت محصول</th>
					<td>
						<input type="text" class="regular-text" name="fg_theme_settings[product_card_bg]" value="<?php echo esc_attr($s['product_card_bg']); ?>">
						<p class="description">مثال: rgba(255,255,255,.06) یا #1b1b1b</p>
					</td>
				</tr>
				<tr>
					<th scope="row">پس‌زمینه آیتم دسته‌بندی</th>
					<td>
						<input type="text" class="regular-text" name="fg_theme_settings[category_bg]" value="<?php echo esc_attr($s['category_bg']); ?>">
					</td>
				</tr>
			</table>

			<hr>

			<h2 class="title">سبد و پرداخت: اطلاعات سفارش</h2>
			<table class="form-table" role="presentation">
				<tr><th scope="row">نمایش باکس اطلاعات سفارش</th><td><label><input type="checkbox" name="fg_theme_settings[show_order_info]" value="1" <?php checked($s['show_order_info'],1); ?>> نمایش داده شود</label></td></tr>
				<tr><th scope="row">متن آماده‌سازی</th><td><input type="text" class="regular-text" name="fg_theme_settings[prep_time]" value="<?php echo esc_attr($s['prep_time']); ?>"></td></tr>
				<tr><th scope="row">متن زمان ارسال</th><td><input type="text" class="regular-text" name="fg_theme_settings[delivery_time]" value="<?php echo esc_attr($s['delivery_time']); ?>"></td></tr>
			</table>

			<hr>

			<h2 class="title">پرداخت: زمان تحویل و لوکیشن روی نقشه</h2>
			<table class="form-table" role="presentation">
				<tr><th scope="row">نمایش نقشه در پرداخت</th><td><label><input type="checkbox" name="fg_theme_settings[enable_map]" value="1" <?php checked($s['enable_map'],1); ?>> فعال باشد</label></td></tr>
				<tr><th scope="row">Lat پیش‌فرض</th><td><input type="text" class="regular-text" name="fg_theme_settings[default_lat]" value="<?php echo esc_attr($s['default_lat']); ?>"></td></tr>
				
				<tr><th scope="row">Lat مبدا (کافه)</th><td><input type="text" class="regular-text" name="fg_theme_settings[origin_lat]" value="<?php echo esc_attr($s['origin_lat']); ?>"></td></tr>
				<tr><th scope="row">Lng مبدا (کافه)</th><td><input type="text" class="regular-text" name="fg_theme_settings[origin_lng]" value="<?php echo esc_attr($s['origin_lng']); ?>"></td></tr>

				<tr><th scope="row">Lng پیش‌فرض</th><td><input type="text" class="regular-text" name="fg_theme_settings[default_lng]" value="<?php echo esc_attr($s['default_lng']); ?>"></td></tr>
				<tr><th scope="row">بازه‌های زمانی تحویل</th><td><input type="text" class="large-text" name="fg_theme_settings[delivery_slots]" value="<?php echo esc_attr($s['delivery_slots']); ?>"><p class="description">با | جدا کنید. مثال: 10:00-12:00|12:00-14:00|...</p></td></tr>
				<tr><th scope="row">زمان تقریبی رسیدن (دقیقه)</th><td><input type="number" class="small-text" name="fg_theme_settings[delivery_eta_minutes]" value="<?php echo esc_attr((int)$s['delivery_eta_minutes']); ?>"></td></tr>
			</table>

			<hr>

			<h2 class="title">امتیاز و کد تخفیف مشتری</h2>
			<table class="form-table" role="presentation">
				<tr><th scope="row">فعال‌سازی سیستم امتیازدهی</th><td><label><input type="checkbox" name="fg_theme_settings[rewards_enabled]" value="1" <?php checked($s['rewards_enabled'],1); ?>> فعال باشد</label></td></tr>
				<tr><th scope="row">امتیاز هر خرید</th><td><input type="number" class="small-text" name="fg_theme_settings[points_per_order]" value="<?php echo esc_attr((int)$s['points_per_order']); ?>"></td></tr>
				<tr><th scope="row">حد امتیاز برای دریافت کد</th><td><input type="number" class="small-text" name="fg_theme_settings[points_threshold]" value="<?php echo esc_attr((int)$s['points_threshold']); ?>"></td></tr>
				<tr><th scope="row">درصد تخفیف کد جایزه</th><td><input type="number" class="small-text" name="fg_theme_settings[coupon_percent]" value="<?php echo esc_attr((int)$s['coupon_percent']); ?>"></td></tr>
				<tr><th scope="row">اعتبار کد (روز)</th><td><input type="number" class="small-text" name="fg_theme_settings[coupon_days]" value="<?php echo esc_attr((int)$s['coupon_days']); ?>"></td></tr>
			</table>

			<?php submit_button('ذخیره تنظیمات'); ?>
		</form>
	</div>
	<?php
}
