<?php
/**
 * The template for displaying vendor orders
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-withdrawal.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
// Exit if accessed directly
exit;
}
global $woocommerce, $MVX;
$get_vendor_thresold = 0;
if (get_mvx_vendor_settings( 'commission_threshold', 'disbursement' )) {
$get_vendor_thresold = get_mvx_vendor_settings( 'commission_threshold', 'disbursement' );
}
$withdrawal_list_table_headers = apply_filters('mvx_datatable_vendor_withdrawal_list_table_headers', array(
    'select_withdrawal'  => array('label' => '', 'class' => 'text-center', 'orderable' => false),
    'order_id'      => array('label' => __( 'Order ID', 'multivendorx' ), 'orderable' => false),
    'commission_amount'    => array('label' => __( 'Commission Amount', 'multivendorx' ), 'orderable' => false),
    'shipping_amount'=> array('label' => __( 'Shipping Amount', 'multivendorx' ), 'orderable' => false),
    'tax_amount'  => array('label' => __( 'Tax Amount', 'multivendorx' ), 'orderable' => false),
    'total'        => array('label' => __( 'Total', 'multivendorx' ), 'orderable' => false),
), get_current_user_id());
?>
<?php if ($get_vendor_thresold) : ?>
<div class="col-md-12">
    <blockquote>
        <span><?php esc_html_e('Your Threshold value for withdrawals is :', 'multivendorx'); ?> <?php echo wc_price($get_vendor_thresold); ?></span>
    </blockquote>
</div>
<?php endif; ?>
<div class="col-md-12">
    <div class="panel panel-default">
        <h3 class="panel-heading d-flex"><?php esc_html_e('Withdrawal Orders', 'multivendorx'); ?></h3>
        <div class="panel-body">
            <form method="post" name="get_paid_form">
                <table id="vendor_withdrawal" class="table table-striped table-bordered" width="100%">
                    <thead>
                        <tr>
                        <?php 
                            if ($withdrawal_list_table_headers) :
                                foreach ($withdrawal_list_table_headers as $key => $header) {
                                    if ($key == 'select_withdrawal'){ ?>
                            <th class="<?php if (isset($header['class'])) echo $header['class']; ?>"><input type="checkbox" class="select_all_withdrawal" onchange="toggleAllCheckBox(this, 'vendor_withdrawal');" /></th>
                                <?php }else{ ?>
                            <th class="<?php if (isset($header['class'])) echo $header['class']; ?>"><?php if (isset($header['label'])) echo $header['label']; ?></th>         
                                <?php }
                                }
                            endif;
                        ?>
                        </tr>
                    </thead>
                    <tbody>  
                    </tbody>
                </table>
                <div class="mvx_table_loader">
                    <input type="hidden" id="total_orders_count" value = "<?php echo count($vendor_unpaid_orders); ?>" />
                    <?php if (count($vendor_unpaid_orders) > 0) { 
                        if (get_mvx_vendor_settings( 'withdrawal_request', 'disbursement' )) {
                            $total_vendor_due = $vendor->mvx_vendor_get_total_amount_due();
                            
                            if ( (get_mvx_vendor_settings( 'commission_threshold', 'disbursement' ) && !empty(get_mvx_vendor_settings( 'commission_threshold', 'disbursement' )) && $total_vendor_due > $get_vendor_thresold ) 
                                    || (get_mvx_vendor_settings( 'commission_threshold', 'disbursement' ) && empty(get_mvx_vendor_settings( 'commission_threshold', 'disbursement' )) && $vendor_unpaid_orders ) 
                                    || ( !get_mvx_vendor_settings( 'commission_threshold', 'disbursement' ) && $vendor_unpaid_orders ) ){ ?>
                            <div class="mvx-action-container">
                                <button name="vendor_get_paid" type="submit" class="btn btn-default"><?php _e('Request Withdrawals', 'multivendorx'); ?></button>
                            </div>
                    <?php
                            }
                        }
                    }
                    ?>
                    <div class="clear"></div>
                </div>
            </form>
            <?php $vendor_payment_mode = get_user_meta($vendor->id, '_vendor_payment_mode', true);
            if ($vendor_payment_mode == 'paypal_masspay' && wp_next_scheduled('masspay_cron_start')) { ?>
            <div class="mvx_admin_massege">
                <div class="mvx_mixed_msg"><?php esc_html_e('Your next scheduled payment date is on:', 'multivendorx'); ?>	<span><?php echo date('d/m/Y g:i:s A', wp_next_scheduled('masspay_cron_start')); ?></span> </div>
            </div>
        <?php } ?> 
        </div>
    </div>
</div>
<script>
jQuery(document).ready(function($) {
    var vendor_withdrawal;
    var columns = [];
    <?php if ($withdrawal_list_table_headers) {
     foreach ($withdrawal_list_table_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if (isset($header['class'])) echo esc_js($header['class']); ?>';
        obj['orderable'] = '<?php if (isset($header['orderable'])) echo esc_js($header['orderable']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    vendor_withdrawal = $('#vendor_withdrawal').DataTable({
        ordering  : <?php echo isset($table_init['ordering']) ? trim($table_init['ordering']) : 'false'; ?>,
        searching  : <?php echo isset($table_init['searching']) ? trim($table_init['searching']) : 'false'; ?>,
        processing: true,
        serverSide: true,
        responsive: true,
        language: {
            "emptyTable": "<?php echo isset($table_init['emptyTable']) ? trim($table_init['emptyTable']) : __('No orders found!','multivendorx'); ?>",
            "processing": "<?php echo isset($table_init['processing']) ? trim($table_init['processing']) : __('Processing...', 'multivendorx'); ?>",
            "info": "<?php echo isset($table_init['info']) ? trim($table_init['info']) : __('Showing _START_ to _END_ of _TOTAL_ orders','multivendorx'); ?>",
            "infoEmpty": "<?php echo isset($table_init['infoEmpty']) ? trim($table_init['infoEmpty']) : __('Showing 0 to 0 of 0 orders','multivendorx'); ?>",
            "lengthMenu": "<?php echo isset($table_init['lengthMenu']) ? trim($table_init['lengthMenu']) : __('Number of rows _MENU_','multivendorx'); ?>",
            "zeroRecords": "<?php echo isset($table_init['zeroRecords']) ? trim($table_init['zeroRecords']) : __('No matching orders found','multivendorx'); ?>",
            "search": "<?php echo isset($table_init['search']) ? trim($table_init['search']) : __('Search:','multivendorx'); ?>",
            "paginate": {
                "next":  "<?php echo isset($table_init['next']) ? trim($table_init['next']) : __('Next','multivendorx'); ?>",
                "previous":  "<?php echo isset($table_init['previous']) ? trim($table_init['previous']) : __('Previous','multivendorx'); ?>"
            }
        },
        drawCallback: function () {
            $('table.dataTable tr [type="checkbox"]').each(function(){
                if ($(this).prop('disabled')){
                    $(this).css('cursor', 'not-allowed');
                    $(this).parents('tr[role="row"]').css('background-color', '#edf0f1');
                }
            })
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'mvx_vendor_unpaid_order_vendor_withdrawal_list', $MVX->ajax_url() ); ?>', 
            type: "post",
            data: function (data) {
                data.security = '<?php echo wp_create_nonce('mvx-withdrawal'); ?>';
            },
            error: function(xhr, status, error) {
                $("#vendor_withdrawal tbody").append('<tr class="odd"><td valign="top" colspan="6" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'multivendorx'); ?></a></td></tr>');
                $("#vendor_withdrawal_processing").css("display","none");
            }
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( vendor_withdrawal );
});
</script>