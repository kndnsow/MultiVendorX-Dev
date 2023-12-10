<?php
/**
 * The template for displaying vendor stats report.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-orders-stats-report.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;
$t_sale = isset($report_data['stats']['sales_total']) ? $report_data['stats']['sales_total'] : 0;
$t_earning = isset($report_data['stats']['earning']) ? $report_data['stats']['earning'] : 0;
$t_withdrawal = isset($report_data['stats']['withdrawal']) ? $report_data['stats']['withdrawal'] : 0;
$t_orders_no = isset($report_data['stats']['orders_no']) ? $report_data['stats']['orders_no'] : 0;
echo $email_heading . "\n\n"; 
printf(__( 'Hello %s,<br>Your %s store orders report stats are as follows:', 'multivendorx' ),  $vendor->page_title, $report_data['period']); 
echo "****************************************************\n\n";

printf(__( '%s sale: %s', 'multivendorx' ), ucfirst($report_data['period']), wc_price($t_sale));
printf(__( '%s earning: %s', 'multivendorx' ), ucfirst($report_data['period']), wc_price($t_earning));
printf(__( '%s withdrawal: %s', 'multivendorx' ), ucfirst($report_data['period']), wc_price($t_withdrawal));
printf(__( '%s no of orders: %s', 'multivendorx' ), ucfirst($report_data['period']), $t_orders_no);
echo __( 'Period', 'multivendorx' ).' : '.isset($report_data['period']) ? ucfirst($report_data['period']) : '';
echo __( 'From Date', 'multivendorx' ).' : '.isset($report_data['start_date']) ? $report_data['start_date'] : '';
echo __( 'To Date', 'multivendorx' ).' : '.isset($report_data['end_date']) ? $report_data['end_date'] : '';

echo "\n****************************************************\n";
if($attachments && count($attachments) > 0 && $report_data['order_data'] && count($report_data['order_data']) > 0 ){
    echo __( 'Please find your report attachment', 'multivendorx' );
}else{
    echo __( 'There is no stats report available.', 'multivendorx' );
}
echo "\n****************************************************\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );