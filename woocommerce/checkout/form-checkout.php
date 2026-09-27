<?php
/**
 * Checkout template override
 */
if (!defined('ABSPATH')) { exit; }

wc_print_notices();

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>
<div class="container">
  <div class="content-card">
    <h1 class="page-title">پرداخت</h1>
    <div class="muted">اطلاعات سفارش، آدرس و روش پرداخت را تکمیل کنید.</div>
    <div class="divider"></div>
    <?php if (function_exists('wc_coupons_enabled') && wc_coupons_enabled()) : ?>
      <div class="fg-checkout__section" style="margin-top:16px">
        <h3>کد تخفیف</h3>
        <?php woocommerce_checkout_coupon_form(); ?>
      </div>
    <?php endif; ?>


    <form name="checkout" method="post" class="checkout woocommerce-checkout fg-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

      <div class="fg-checkout__grid">
        <div class="fg-checkout__left">
          <?php if ($checkout->get_checkout_fields()): ?>
            <?php do_action('woocommerce_checkout_before_customer_details'); ?>

            <div class="fg-checkout__section">
              <h3>اطلاعات خریدار</h3>
              <?php do_action('woocommerce_checkout_billing'); ?>
            </div>

            <?php if (WC()->cart && WC()->cart->needs_shipping_address()): ?>
              <div class="fg-checkout__section">
                <h3>آدرس ارسال</h3>
                <?php do_action('woocommerce_checkout_shipping'); ?>
              </div>
            <?php endif; ?>

            <?php do_action('woocommerce_checkout_after_customer_details'); ?>
          <?php endif; ?>
        </div>

        <div class="fg-checkout__right">
          <div class="fg-checkout__section">
            <h3>خلاصه سفارش</h3>
            <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
            <?php do_action('woocommerce_checkout_before_order_review'); ?>
            <div id="order_review" class="woocommerce-checkout-review-order">
              <?php do_action('woocommerce_checkout_order_review'); ?>
            </div>
            <?php do_action('woocommerce_checkout_after_order_review'); ?>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>
<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
