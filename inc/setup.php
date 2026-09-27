<?php
/**
 * Theme setup
 */
if (!defined('ABSPATH')) { exit; }

add_action('after_setup_theme', function () {
    load_theme_textdomain('fernosa-gelato', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);

    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    register_nav_menus([
        'primary' => __('منوی اصلی', 'fernosa-gelato'),
        'footer'  => __('منوی فوتر', 'fernosa-gelato'),
    ]);

    add_theme_support('woocommerce');
    add_theme_support('rtl-language-support');
});

add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('فوتر - ستون ۱', 'fernosa-gelato'),
        'id'            => 'footer-1',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-title">',
        'after_title'   => '</h4>',
    ]);
    register_sidebar([
        'name'          => __('فوتر - ستون ۲', 'fernosa-gelato'),
        'id'            => 'footer-2',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-title">',
        'after_title'   => '</h4>',
    ]);
    register_sidebar([
        'name'          => __('فوتر - ستون ۳', 'fernosa-gelato'),
        'id'            => 'footer-3',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-title">',
        'after_title'   => '</h4>',
    ]);
});

add_action('customize_register', function($wp_customize){
    $wp_customize->add_section('fernosa_colors', [
        'title' => __('رنگ‌بندی فرنوسا', 'fernosa-gelato'),
        'priority' => 30,
    ]);

    $wp_customize->add_setting('fernosa_primary', [
        'default' => '#013a17',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fernosa_primary', [
        'label' => __('رنگ اصلی (پس‌زمینه تیره)', 'fernosa-gelato'),
        'section' => 'fernosa_colors',
        'settings' => 'fernosa_primary',
    ]));

    $wp_customize->add_setting('fernosa_gold', [
        'default' => '#988c75',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fernosa_gold', [
        'label' => __('رنگ طلایی', 'fernosa-gelato'),
        'section' => 'fernosa_colors',
        'settings' => 'fernosa_gold',
    ]));

    // Header / Footer / Accordion colors
    $wp_customize->add_setting('fernosa_header_bg', [
        'default' => '#013a17',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fernosa_header_bg', [
        'label' => __('رنگ پس‌زمینه هدر', 'fernosa-gelato'),
        'section' => 'fernosa_colors',
        'settings' => 'fernosa_header_bg',
    ]));

    $wp_customize->add_setting('fernosa_header_bg_scrolled', [
        'default' => '#013a17',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fernosa_header_bg_scrolled', [
        'label' => __('رنگ هدر هنگام اسکرول', 'fernosa-gelato'),
        'section' => 'fernosa_colors',
        'settings' => 'fernosa_header_bg_scrolled',
    ]));

    

    $wp_customize->add_setting('fernosa_header_link', [
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fernosa_header_link', [
        'label' => __('رنگ لینک‌های منوی بالا', 'fernosa-gelato'),
        'section' => 'fernosa_colors',
        'settings' => 'fernosa_header_link',
    ]));

    $wp_customize->add_setting('fernosa_header_link_hover', [
        'default' => '#988c75',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fernosa_header_link_hover', [
        'label' => __('رنگ لینک‌ها هنگام هاور', 'fernosa-gelato'),
        'section' => 'fernosa_colors',
        'settings' => 'fernosa_header_link_hover',
    ]));

$wp_customize->add_setting('fernosa_footer_bg', [
        'default' => '#013a17',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fernosa_footer_bg', [
        'label' => __('رنگ پس‌زمینه فوتر', 'fernosa-gelato'),
        'section' => 'fernosa_colors',
        'settings' => 'fernosa_footer_bg',
    ]));

    $wp_customize->add_setting('fernosa_accordion_accent', [
        'default' => '#988c75',
        'sanitize_callback' => 'sanitize_hex_color',
    ]);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fernosa_accordion_accent', [
        'label' => __('رنگ منوی آکاردئون (اکسنت)', 'fernosa-gelato'),
        'section' => 'fernosa_colors',
        'settings' => 'fernosa_accordion_accent',
    ]));

    $wp_customize->add_section('fernosa_home', [
        'title' => __('صفحه نخست (Hero)', 'fernosa-gelato'),
        'priority' => 31,
    ]);

    $wp_customize->add_setting('fernosa_hero_title', [
        'default' => 'تجربه‌ای لوکس از قهوه و ژلاتو',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('fernosa_hero_title', [
        'label' => __('عنوان هیرو', 'fernosa-gelato'),
        'section' => 'fernosa_home',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('fernosa_hero_desc', [
        'default' => 'قهوه‌های تخصصی، دسرهای دست‌ساز و ژلاتوی ایتالیایی — با کیفیتی که به خاطر می‌ماند.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('fernosa_hero_desc', [
        'label' => __('توضیح هیرو', 'fernosa-gelato'),
        'section' => 'fernosa_home',
        'type' => 'textarea',
    ]);

    $wp_customize->add_setting('fernosa_hero_bg', [
        'default' => '',
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'fernosa_hero_bg', [
        'label' => __('تصویر پس‌زمینه هیرو', 'fernosa-gelato'),
        'section' => 'fernosa_home',
        'settings' => 'fernosa_hero_bg',
    ]));
    $wp_customize->add_setting('fernosa_intro_video', [
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('fernosa_intro_video', [
        'label' => __('ویدئوی شروع سایت (URL فایل MP4 محلی)', 'fernosa-gelato'),
        'description' => __('فقط فایل MP4 روی خود سایت؛ برای سرعت، ویدئو را با H.264 فشرده کنید.', 'fernosa-gelato'),
        'section' => 'fernosa_home',
        'type' => 'url',
    ]);

    $wp_customize->add_section('fernosa_header', [
        'title' => __('هدر (دکمه‌ها و لینک‌ها)', 'fernosa-gelato'),
        'priority' => 32,
    ]);

    $wp_customize->add_setting('fernosa_header_show_account', [
        'default' => true,
        'sanitize_callback' => function($v){ return (bool) $v; },
    ]);
    $wp_customize->add_control('fernosa_header_show_account', [
        'label' => __('نمایش دکمه حساب کاربری', 'fernosa-gelato'),
        'section' => 'fernosa_header',
        'type' => 'checkbox',
    ]);

    $wp_customize->add_section('fernosa_contact', [
        'title' => __('اطلاعات تماس (فوتر/تماس با ما)', 'fernosa-gelato'),
        'priority' => 32,
    ]);

    $wp_customize->add_setting('fernosa_contact_hours', [
        'default' => '8:00 تا 23:00',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('fernosa_contact_hours', [
        'label' => __('ساعت کاری', 'fernosa-gelato'),
        'section' => 'fernosa_contact',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('fernosa_contact_phone', [
        'default' => '021-44466911',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('fernosa_contact_phone', [
        'label' => __('تلفن تماس', 'fernosa-gelato'),
        'section' => 'fernosa_contact',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('fernosa_contact_mobile', [
        'default' => '+989105449950',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('fernosa_contact_mobile', [
        'label' => __('موبایل/واتساپ', 'fernosa-gelato'),
        'section' => 'fernosa_contact',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('fernosa_contact_email', [
        'default' => 'info@fernosagelato.ir',
        'sanitize_callback' => 'sanitize_email',
    ]);
    $wp_customize->add_control('fernosa_contact_email', [
        'label' => __('ایمیل', 'fernosa-gelato'),
        'section' => 'fernosa_contact',
        'type' => 'text',
    ]);

    // Map / Location
    $wp_customize->add_setting('fernosa_map_lat', [
        'default' => '35.756086',
        'sanitize_callback' => function($v){ return preg_replace('/[^0-9\.\-]/', '', (string)$v); },
    ]);
    $wp_customize->add_control('fernosa_map_lat', [
        'label' => __('عرض جغرافیایی (Latitude)', 'fernosa-gelato'),
        'section' => 'fernosa_contact',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('fernosa_map_lng', [
        'default' => '51.324281',
        'sanitize_callback' => function($v){ return preg_replace('/[^0-9\.\-]/', '', (string)$v); },
    ]);
    $wp_customize->add_control('fernosa_map_lng', [
        'label' => __('طول جغرافیایی (Longitude)', 'fernosa-gelato'),
        'section' => 'fernosa_contact',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('fernosa_map_balad_url', [
        'default' => 'https://balad.ir/directions/driving?destination=51.32419465618318%2C35.75611171091805',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('fernosa_map_balad_url', [
        'label' => __('لینک بلد (Directions)', 'fernosa-gelato'),
        'section' => 'fernosa_contact',
        'type' => 'url',
    ]);

    $wp_customize->add_setting('fernosa_footer_address', [
        'default' => 'تقاطع سردار جنگلو خیابان مخبری - ضلع جنوب شرقی - فرنوسا',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('fernosa_footer_address', [
        'label' => __('آدرس', 'fernosa-gelato'),
        'section' => 'fernosa_contact',
        'type' => 'text',
    ]);

    $wp_customize->add_section('fernosa_social', [
        'title' => __('شبکه‌های اجتماعی', 'fernosa-gelato'),
        'priority' => 33,
    ]);

    foreach (['instagram'=>'Instagram','telegram'=>'Telegram','whatsapp'=>'WhatsApp','youtube'=>'YouTube','linkedin'=>'LinkedIn','x'=>'X'] as $key=>$label) {
        $wp_customize->add_setting('fernosa_social_' . $key, [
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control('fernosa_social_' . $key, [
            'label' => sprintf(__('لینک %s', 'fernosa-gelato'), $label),
            'section' => 'fernosa_social',
            'type' => 'url',
        ]);
    }



    // Home menu view (Accordion / Tabs / Cards)
    $wp_customize->add_section('fernosa_menu', [
        'title' => __('منوی محصولات', 'fernosa-gelato'),
        'priority' => 31,
    ]);

    $wp_customize->add_setting('fernosa_menu_view', [
        'default' => 'accordion',
        'sanitize_callback' => function($v){
            $v = is_string($v) ? $v : 'accordion';
            return in_array($v, ['accordion','tabs','cards'], true) ? $v : 'accordion';
        },
    ]);

    $wp_customize->add_control('fernosa_menu_view', [
        'label' => __('نوع نمایش دسته‌بندی‌ها', 'fernosa-gelato'),
        'section' => 'fernosa_menu',
        'type' => 'select',
        'choices' => [
            'accordion' => __('آکاردئون', 'fernosa-gelato'),
            'tabs'      => __('تب‌ها', 'fernosa-gelato'),
            'cards'     => __('کارت‌ها', 'fernosa-gelato'),
        ],
    ]);


});
