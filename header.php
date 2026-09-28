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

