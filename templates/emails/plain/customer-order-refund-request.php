<?php
/**
 * The template for displaying report abuse via customer.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/customer-order-refund-request.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.3.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;

echo $email_heading . "\n\n";

if( $user_type != 'customer' ) {
    echo __( 'Refund details', 'multivendorx' ) . "\n\n";
    printf( __( "Order ID: #%s",  'multivendorx' ), $order->get_id()) . "\n";
    printf( __( "Refund Reason: %s",  'multivendorx' ), $refund_details['refund_reason']) . "\n";
    printf( __( "Additional Information: %s",  'multivendorx' ), $refund_details['addi_info']) . "\n";
    printf( __( "Refund Status: %s",  'multivendorx' ), $refund_details['status']) . "\n";
}else{
    printf( __( "Your refund request for order %s is %s",  'multivendorx' ), $order->get_id(), $refund_details['status'] ) . "\n";
}

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) ); 