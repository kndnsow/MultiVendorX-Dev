<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-new-order.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $MVX;
$vendor = get_mvx_vendor( absint( $vendor_id ) );
echo $email_heading . "\n\n";

echo sprintf( __( 'A new order was received and marked as %s from %s. Their order is as follows:',  'multivendorx' ), $order->get_status( 'edit' ), $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text );

echo sprintf( __( 'Order Number: %s',  'multivendorx'), $order->get_order_number() ) . "\n";
echo sprintf( __( 'Order Link: %s',  'multivendorx'), esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders'), $order->get_id())) ) . "\n";
echo sprintf( __( 'Order Date: %s',  'multivendorx'), date_i18n( __( 'jS F Y',  'multivendorx' ), strtotime( $order->get_date_created() ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text );


$vendor->plain_vendor_order_item_table($order, $vendor->term_id); 

echo "----------\n\n";
if(apply_filters('show_cust_order_calulations_field', true, $vendor->id)) {
    $totals = $vendor->mvx_vendor_get_order_item_totals($order, $vendor->term_id);
	if ( $totals ) {
		foreach ( $totals as $total ) {
			echo $total['label'] . "\t " . $total['value'] . "\n";
		}
	}
}
if ( $order->get_customer_note() ) {
    echo esc_html__( 'Note:', 'multivendorx' ) . "\t " . wp_kses_post( wptexturize( $order->get_customer_note() ) ) . "\n";
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text );
if(apply_filters('show_cust_address_field', true, $vendor->id)) {
	echo __( 'Customer Details',  'multivendorx' ) . "\n";

	if ( $order->get_billing_email() )
		echo __( 'Email:',  'multivendorx' ); echo $order->get_billing_email() . "\n";

	if ( $order->get_billing_phone() )
		echo __( 'Telephone:',  'multivendorx' ); ?> <?php echo $order->get_billing_phone() . "\n";
}

if(apply_filters('show_cust_billing_address_field', true, $vendor->id)) {
	echo "\n" . __( 'Billing Address',  'multivendorx' ) . ":\n";
	echo $order->get_formatted_billing_address() . "\n\n";
}
if(apply_filters('show_cust_shipping_address_field', true, $vendor->id)) {
	if ( get_option( 'woocommerce_ship_to_billing_address_only' ) == 'no' && ( $shipping = $order->get_formatted_shipping_address() ) ) {
	
		echo __( 'Shipping Address',  'multivendorx' ) . ":\n";
	
		echo $shipping . "\n\n";
	
	}
}

echo "\n****************************************************\n\n";

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );