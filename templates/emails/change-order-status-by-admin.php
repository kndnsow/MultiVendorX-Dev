<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/change-order-status-by-admin.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   	3.7.2
 */


if ( !defined( 'ABSPATH' ) ) exit;  ?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( esc_html__( "Hi there! This is to notify that an order #%s status has been changed on %s.",  'multivendorx' ), $order_id, get_option( 'blogname' ) ); ?></p>

<?php do_action( 'mvx_email_footer' ); ?>