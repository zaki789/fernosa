<?php
/**
 * Checkout coupon form (Fernosa override)
 * @see https://woocommerce.com/document/template-structure/
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) {
	return;
}
?>
<div class="fg-checkout-coupon">
	<div class="fg-coupon-toggle">
		<i class="fa-solid fa-ticket"></i>
		<span><?php echo esc_html__( 'کد تخفیف دارید؟', 'fernosa-gelato' ); ?></span>
		<button type="button" class="button fg-coupon-btn" data-fg-coupon-toggle>
			<?php echo esc_html__( 'اینجا وارد کنید', 'fernosa-gelato' ); ?>
		</button>
	</div>

	<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none" data-fg-coupon-form>
		<p class="form-row form-row-first">
			<input type="text" name="coupon_code" class="input-text" placeholder="<?php echo esc_attr__( 'کد تخفیف', 'fernosa-gelato' ); ?>" id="coupon_code" value="" />
		</p>

		<p class="form-row form-row-last">
			<button type="submit" class="button" name="apply_coupon" value="<?php echo esc_attr__( 'اعمال', 'fernosa-gelato' ); ?>">
				<?php echo esc_html__( 'اعمال', 'fernosa-gelato' ); ?>
			</button>
		</p>

		<div class="clear"></div>
	</form>
</div>
