<?php
defined('ABSPATH') || exit;
?>
<div class="content-card woocommerce-account-card">
  <?php wc_print_notices(); ?>
  <?php do_action( 'woocommerce_before_customer_login_form' ); ?>

  <div class="contact-grid">
    <div class="form-card">
      <h2 class="page-title">ورود</h2>
      <form class="woocommerce-form woocommerce-form-login login" method="post">
        <?php do_action( 'woocommerce_login_form_start' ); ?>
        <label>نام کاربری یا ایمیل
          <input type="text" name="username" autocomplete="username" required>
        </label>
        <label>رمز عبور
          <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <?php do_action( 'woocommerce_login_form' ); ?>
        <label style="display:flex; align-items:center; gap:8px; font-weight:700">
          <input type="checkbox" name="rememberme" value="forever"> مرا به خاطر بسپار
        </label>
        <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
        <button type="submit" class="btn btn-primary" name="login" value="ورود"><i class="fa-solid fa-right-to-bracket"></i> ورود</button>
        <input type="hidden" name="redirect" value="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" />
        <?php do_action( 'woocommerce_login_form_end' ); ?>
      </form>
    </div>

    <?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
    <div class="form-card">
      <h2 class="page-title">ثبت نام</h2>
      <form method="post" class="woocommerce-form woocommerce-form-register register">
        <?php do_action( 'woocommerce_register_form_start' ); ?>

        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
          <label>نام کاربری
            <input type="text" name="username" autocomplete="username" required>
          </label>
        <?php endif; ?>

        <label>ایمیل
          <input type="email" name="email" autocomplete="email" required>
        </label>

        <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
          <label>رمز عبور
            <input type="password" name="password" autocomplete="new-password" required>
          </label>
        <?php else: ?>
          <p class="muted">رمز عبور به ایمیل شما ارسال می‌شود.</p>
        <?php endif; ?>

        <?php do_action( 'woocommerce_register_form' ); ?>
        <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
        <button type="submit" class="btn btn-primary" name="register" value="ثبت نام"><i class="fa-solid fa-user-plus"></i> ثبت نام</button>

        <?php do_action( 'woocommerce_register_form_end' ); ?>
      </form>
    </div>
    <?php endif; ?>
  </div>

  <?php do_action( 'woocommerce_after_customer_login_form' ); ?>
</div>
