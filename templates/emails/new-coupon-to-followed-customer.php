<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/new-coupon-to-followed-customer.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( esc_html__( "Hi there! This is a notification about a new coupon on %s.",  'multivendorx' ), get_option( 'blogname' ) ); ?></p>
	<p>
		<?php printf( esc_html__( "Coupon code: %s",  'multivendorx' ), $coupon_name ); ?><br/>
		<?php printf( esc_html__( "Added by: %s",  'multivendorx' ), $vendor_name ); ?>
	</p>

<?php do_action( 'mvx_email_footer' ); ?>