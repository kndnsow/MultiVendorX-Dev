<?php
/**
 * The template for displaying vendor withdrawal content
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-withdrawal/vendor-withdrawal-request.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $MVX;
$transaction = get_post($transaction_id);
$vendor = get_current_vendor();
$transaction_post_types = array('mvx_transaction', 'wcmp_transaction');
if ( !$transaction || (isset($transaction->post_type) && !in_array( $transaction->post_type, $transaction_post_types)) || $vendor->id !== get_current_user_id() ) {
    $vendor = get_mvx_vendor_by_term($transaction->post_author) ? get_mvx_vendor_by_term($transaction->post_author) : get_mvx_vendor($transaction->post_author);
    ?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <?php _e('Invalid Withdrawal details', 'multivendorx'); ?>
        </div>
    </div>
    <?php
    return;
}
?>
<div class="col-md-12">
    <div class="panel panel-default">
        <h3 class="panel-heading d-flex"><?php echo apply_filters('mvx_thankyou_transaction_received_text', sprintf(__('Withdrawal #%s details', 'multivendorx'), $transaction_id), $transaction_id); ?></h3>
        <div class="panel-body">
            <?php $transaction = get_post($transaction_id);
            $amount = (float) get_post_meta($transaction_id, 'amount', true) - (float) get_post_meta($transaction_id, 'transfer_charge', true) - (float) get_post_meta($transaction_id, 'gateway_charge', true);
            if (isset($transaction->post_type) && in_array( $transaction->post_type, $transaction_post_types)) {
                $commission_details = $MVX->transaction->get_transaction_item_details($transaction_id);
            ?>
            <div class="withdrawal-transaction-wrapper">
            <table class="table table-bordered">
                <?php if (!empty($commission_details['header'])) { 
                    echo '<thead><tr>';
                    foreach ($commission_details['header'] as $header_val) {
                        echo '<th>'.$header_val.'</th>';
                    }
                    echo '</tr></thead>';
                }
                echo '<tbody>';
                if (!empty($commission_details['body'])) {
                    
                    foreach ($commission_details['body'] as $commission_detail) {
                        echo '<tr>';
                        foreach ($commission_detail as $details) {
                            foreach ($details as $detail_key => $detail) {
                                echo '<td>'.$detail.'</td>';
                            }
                        }
                        echo '</tr>';
                    }
                    
                }
                if ($totals = $MVX->transaction->get_transaction_item_totals($transaction_id, $vendor)) {
                    foreach ($totals as $total) {
                        echo '<tr><td colspan="3" >'.$total['label'].'</td><td>'.$total['value'].'</td></tr>';
                    }
                }
                echo '</tbody>';
                ?>
            </table>
            </div>
        <?php } else { ?>
            <p class="mvx_headding3"><?php printf(__('Hello,<br>Unfortunately your request for withdrawal amount could not be completed. You may try again later, or check you PayPal settings in your account page, or contact the admin at <b>%s</b>', 'multivendorx'), get_option('admin_email')); ?></p>
        <?php } ?>
        </div>
    </div>
</div>