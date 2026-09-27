<?php
/**
 * Cart template override
 */
if (!defined('ABSPATH')) { exit; }

do_action('woocommerce_before_cart');
?>
<div class="container">
  <div class="content-card">
    <h1 class="page-title">سبد خرید</h1>
    <div class="muted">محصولات انتخابی خود را بررسی کنید، تعداد را تغییر دهید و سپس پرداخت را انجام دهید.</div>
    <div class="divider"></div>

    <?php wc_print_notices(); ?>

    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
      <div class="fg-cart">
        <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item):
          $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
          if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0) continue;

          $product_id = $cart_item['product_id'];
          $thumbnail  = $_product->get_image('woocommerce_thumbnail');
          $name       = $_product->get_name();
          $price      = WC()->cart->get_product_price($_product);
          $subtotal   = WC()->cart->get_product_subtotal($_product, $cart_item['quantity']);
          $remove_url = wc_get_cart_remove_url($cart_item_key);
        ?>
        <div class="fg-cart__row">
          <div class="fg-cart__thumb"><?php echo $thumbnail; ?></div>

          <div class="fg-cart__meta">
            <div class="fg-cart__name"><?php echo esc_html($name); ?></div>
            <div class="fg-cart__price"><?php echo wp_kses_post($price); ?></div>
          </div>

          <div class="fg-cart__qty">
            <?php
              echo woocommerce_quantity_input([
                'input_name'  => "cart[{$cart_item_key}][qty]",
                'input_value' => $cart_item['quantity'],
                'min_value'   => 0,
                'max_value'   => $_product->get_max_purchase_quantity(),
              ], $_product, false);
            ?>
          </div>

          <div class="fg-cart__subtotal"><?php echo wp_kses_post($subtotal); ?></div>

          <div class="fg-cart__remove">
            <a class="icon-btn" href="<?php echo esc_url($remove_url); ?>" aria-label="حذف">
              <i class="fa-solid fa-trash"></i>
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="divider"></div>

      <div class="fg-cart__actions">
        <button type="submit" class="btn btn-ghost" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
          <i class="fa-solid fa-rotate"></i> بروزرسانی سبد
        </button>

        <?php do_action('woocommerce_cart_actions'); ?>
        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
      </div>
    </form>

    <div class="divider"></div>

    <div class="fg-cart__totals">
      <?php do_action('woocommerce_cart_collaterals'); ?>
    </div>
  </div>
</div>
<?php do_action('woocommerce_after_cart'); ?>
