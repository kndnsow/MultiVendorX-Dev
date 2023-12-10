<?php
/**
 * The template for displaying vendor stats report.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-orders-stats-report.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;
$text_align = is_rtl() ? 'right' : 'left';
$t_sale = isset($report_data['stats']['sales_total']) ? $report_data['stats']['sales_total'] : 0;
$t_earning = isset($report_data['stats']['earning']) ? $report_data['stats']['earning'] : 0;
$t_withdrawal = isset($report_data['stats']['withdrawal']) ? $report_data['stats']['withdrawal'] : 0;
$t_orders_no = isset($report_data['stats']['orders_no']) ? $report_data['stats']['orders_no'] : 0;
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf(esc_html__( 'Hello %s,', 'multivendorx' ),  $vendor->page_title); ?><br>
<?php printf(esc_html__( 'Your %s store orders report stats are as follows:', 'multivendorx' ), $report_data['period']); ?></p>
<div style="margin-bottom: 40px;">
    <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
        <thead>
            <tr>
                <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php printf(esc_html__( '%s sale:', 'multivendorx' ), ucfirst($report_data['period'])); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php printf(esc_html__( '%s earning:', 'multivendorx' ), ucfirst($report_data['period'])); ?></th>
                <th class="td" scope="col" style="text-align:<?php echo $text_align; ?>;"><?php printf(esc_html__( '%s withdrawal:', 'multivendorx' ), ucfirst($report_data['period'])); ?></th>
            </tr>
        </thead>
        <tbody>
            <td class="td" scope="col" style="text-align:<?php echo $text_align; ?>;font-size:28px;font-weight:bold;"><?php echo wc_price($t_sale); ?></td>
            <td class="td" scope="col" style="text-align:<?php echo $text_align; ?>;font-size:28px;font-weight:bold;"><?php echo wc_price($t_earning); ?></td>
            <td class="td" scope="col" style="text-align:<?php echo $text_align; ?>;font-size:28px;font-weight:bold;"><?php echo wc_price($t_withdrawal); ?></td>
        </tbody>
        <tfoot>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>;"><?php printf(esc_html__( '%s no of orders:', 'multivendorx' ), ucfirst($report_data['period'])); ?></th>
                <td class="td" style="text-align:<?php echo $text_align; ?>;"><?php echo $t_orders_no; ?></td>
            </tr>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'Period', 'multivendorx' ); ?></th>
                <td class="td" style="text-align:<?php echo $text_align; ?>;"><?php echo isset($report_data['period']) ? ucfirst($report_data['period']) : ''; ?></td>
            </tr>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'From Date', 'multivendorx' ); ?></th>
                <td class="td" style="text-align:<?php echo $text_align; ?>;"><?php echo isset($report_data['start_date']) ? $report_data['start_date'] : ''; ?></td>
            </tr>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo $text_align; ?>;"><?php _e( 'To Date', 'multivendorx' ); ?></th>
                <td class="td" style="text-align:<?php echo $text_align; ?>;"><?php echo isset($report_data['end_date']) ? $report_data['end_date'] : ''; ?></td>
            </tr>
        </tfoot>
    </table>
</div>
<br>
<?php if($attachments && count($attachments) > 0 && $report_data['order_data'] && count($report_data['order_data']) > 0 ){ ?>
<p><?php echo esc_html__( 'Please find your report attachment', 'multivendorx' ); ?></p>
<?php }else{ ?>
<p><?php echo esc_html__( 'There is no stats report available.', 'multivendorx' ); ?></p>   
<?php } ?>
<?php do_action( 'mvx_email_footer' ); ?>
