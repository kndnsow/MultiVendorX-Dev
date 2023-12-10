<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/admin-widthdrawal-request.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $MVX;
echo $email_heading . "\n\n"; 
$amount = get_post_meta($transaction_id, 'amount', true) - get_post_meta($transaction_id, 'transfer_charge', true) - get_post_meta($transaction_id, 'gateway_charge', true);
if($transaction_mode == 'direct_bank') {
	echo apply_filters( 'mvx_admin_direct_bank_received_text', sprintf(__( 'Hello,<br> %s has successfully completed a withdrawal of $%s on %s through Paypal. The order details are as follows:', 'multivendorx'), '<a href='.$vendor->permalink.'>'.$vendor->page_title.'</a>', $amount, get_the_date( 'd/m/Y', $transaction_id )), $transaction_id ); 
} else if($transaction_mode == 'paypal_masspay'){
	echo apply_filters( 'mvx_admin_paypal_received_text', sprintf(__( 'Hello,<br>There is a new withdrawal request for $%s from a vendor %s at your site. The order details are as following:', 'multivendorx'), $amount, '<a href='.$vendor->permalink.'>'.$vendor->page_title.'</a>'), $transaction_id );
}

echo "****************************************************\n\n";

$commission_details  = $MVX->transaction->get_transaction_item_details($transaction_id); 
if(!empty($commission_details['body'])) {
	foreach ( $commission_details['body'] as $commission_detail ) {	
		foreach($commission_detail as $details) {
			foreach($details as $detail_key => $detail) {
					echo $detail_key .' : '. $detail.'\n'; 
			}
		}
	}
}
echo "----------\n\n";
if ( $totals =  $MVX->transaction->get_transaction_item_totals($transaction_id, $vendor) ) {
	foreach ( $totals as $total ) {
		echo $total['label'] .' : '. $total['value'].'\n';
	}
}
echo "\n****************************************************\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );