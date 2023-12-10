<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-notify-shipped.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $MVX;
echo $email_heading . "\n\n";
echo sprintf( __( 'Some of the items you had ordered have been shipped. The items that have been shipped are as follows:',  'multivendorx' ), $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

echo sprintf( __( 'Order Number: %s',  'multivendorx'), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Order Link: %s',  'multivendorx'), admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ) . "\n";
echo sprintf( __( 'Order Date: %s',  'multivendorx'), date_i18n( __( 'jS F Y',  'multivendorx' ), strtotime( $order->get_date_created() ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );

$vendor = new MVX_Vendor( absint( $vendor_id ) );
$vendor_items_dtl = $vendor->plain_vendor_order_item_table($order, $vendor_id, true); 
echo $vendor_items_dtl;

echo "----------\n\n";
if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );
echo __( 'Customer Details', 'multivendorx' ) . "\n";

if ( $order->get_billing_email() )
	echo __( 'Email:',  'multivendorx' ); echo $order->get_billing_email() . "\n";

if ( $order->get_billing_phone() )
	echo __( 'Telephone:',  'multivendorx' ); ?> <?php echo $order->get_billing_phone() . "\n";

echo "\n" . __( 'Billing Address',  'multivendorx' ) . ":\n";
echo $order->get_formatted_billing_address() . "\n\n";
if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && ( $shipping = $order->get_formatted_shipping_address() ) ) {
	echo __( 'Shipping Address',  'multivendorx' ) . ":\n";
	echo $shipping . "\n\n";
}

echo "\n****************************************************\n\n";

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );