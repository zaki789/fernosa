<?php
defined( 'ABSPATH' ) || exit;
?>
<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward">
	<?php echo esc_html__( 'ادامه و پرداخت', 'fernosa-gelato' ); ?>
</a>
