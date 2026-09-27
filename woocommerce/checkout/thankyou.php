<?php
/**
 * Thank you page override
 */
if (!defined('ABSPATH')) { exit; }
?>
<div class="container">
  <div class="content-card">
    <h1 class="page-title">نتیجه پرداخت</h1>
    <div class="divider"></div>

    <?php if ($order): ?>
      <?php if ($order->has_status('failed')): ?>
        <div class="notice-card">
          پرداخت ناموفق بود. لطفاً دوباره تلاش کنید یا روش پرداخت را تغییر دهید.
        </div>
        <div class="divider"></div>
        <a class="btn btn-primary" href="<?php echo esc_url($order->get_checkout_payment_url()); ?>">پرداخت مجدد</a>
        <a class="btn btn-ghost" href="<?php echo esc_url(wc_get_cart_url()); ?>">بازگشت به سبد</a>
      <?php else: ?>
        <div class="notice-card">
          پرداخت با موفقیت ثبت شد. شماره سفارش: <strong><?php echo esc_html($order->get_order_number()); ?></strong>
        </div>
        <div class="divider"></div>
        <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
        <?php do_action('woocommerce_thankyou', $order->get_id()); ?>
        <div class="divider"></div>
        <a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>">بازگشت به خانه</a>
      <?php endif; ?>
    <?php else: ?>
      <div class="notice-card">از خرید شما سپاسگزاریم.</div>
      <div class="divider"></div>
      <a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>">بازگشت به خانه</a>
    <?php endif; ?>
  </div>
</div>
