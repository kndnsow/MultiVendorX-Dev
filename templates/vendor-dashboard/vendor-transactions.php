<?php
/**
 * The template for displaying vendor transaction details
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-transaction_detail.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $MVX;
$transactions_list_table_headers = apply_filters('mvx_datatable_vendor_transactions_list_table_headers', array(
    'select_transaction'  => array('label' => '', 'class' => 'text-center'),
    'date'      => array('label' => __( 'Date', 'multivendorx' )),
    'order_id'  => array('label' => __( 'Order IDs', 'multivendorx' )),
    'transaction_id'    => array('label' => __( 'Transc.ID', 'multivendorx' )),
    'commission_ids'=> array('label' => __( 'Commission IDs', 'multivendorx' )),
    'fees'  => array('label' => __( 'Fee', 'multivendorx' )),
    'net_earning'        => array('label' => __( 'Net Earnings', 'multivendorx' )),
), get_current_user_id());
?>
<div class="col-md-12">
    
    <div class="panel panel-default">
        <div class="panel-body">
            <div id="vendor_transactions_date_filter" class="form-inline datatable-date-filder">
                <div class="form-group">
                    <input type="date" id="mvx_from_date" class="form-control" name="from_date" class="pickdate gap1" placeholder="From" value ="<?php echo date('Y-m-01'); ?>"/>
                </div>
                <div class="form-group">
                    <input type="date" id="mvx_to_date" class="form-control" name="to_date" class="pickdate" placeholder="To" value ="<?php echo   date('Y-m-d'); ?>"/>
                </div>
                <button type="button" name="order_export_submit" id="do_filter"  class="btn btn-default" ><?php _e('Show', 'multivendorx') ?></button>
            </div>  
            <form method="post" name="export_transaction">
                <div class="mvx_table_holder">
                    <table id="vendor_transactions" class="get_mvx_transactions table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                            <?php 
                                if($transactions_list_table_headers) :
                                    foreach ($transactions_list_table_headers as $key => $header) {
                                        if($key == 'select_transaction'){ ?>
                                <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><input type="checkbox" class="select_all_transaction" onchange="toggleAllCheckBox(this, 'vendor_transactions');" /></th>
                                    <?php }else{ ?>
                                <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><?php if(isset($header['label'])) echo $header['label']; ?></th>         
                                    <?php }
                                    }
                                endif;
                            ?>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
                <div id="export_transaction_wrap" class="mvx-action-container mvx_table_loader" style="display: none;">
                    <input type="hidden" id="export_transaction_start_date" name="from_date" value="<?php echo date('Y-m-01'); ?>" />
                    <input id="export_transaction_end_date" type="hidden" name="to_date" value="<?php echo date('Y-m-d'); ?>" />
                    <button type="submit" name="export_transaction" class="btn btn-default"><?php _e('Download CSV', 'multivendorx'); ?></button>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>  
</div>
<script>
jQuery(document).ready(function($) {

    var vendor_transactions;
    var columns = [];
    <?php if($transactions_list_table_headers) {
     foreach ($transactions_list_table_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    vendor_transactions = $('#vendor_transactions').DataTable({
        ordering  : false,
        searching  : false,
        processing: true,
        serverSide: true,
        responsive: true,
        language: {
            "emptyTable": "<?php echo trim(__('Sorry. No transactions are available.','multivendorx')); ?>",
            "processing": "<?php echo trim(__('Processing...', 'multivendorx')); ?>",
            "info": "<?php echo trim(__('Showing _START_ to _END_ of _TOTAL_ transactions','multivendorx')); ?>",
            "infoEmpty": "<?php echo trim(__('Showing 0 to 0 of 0 transactions','multivendorx')); ?>",
            "lengthMenu": "<?php echo trim(__('Number of rows _MENU_','multivendorx')); ?>",
            "zeroRecords": "<?php echo trim(__('No matching transactions found','multivendorx')); ?>",
            "search": "<?php echo trim(__('Search:','multivendorx')); ?>",
            "paginate": {
                "next":  "<?php echo trim(__('Next','multivendorx')); ?>",
                "previous":  "<?php echo trim(__('Previous','multivendorx')); ?>"
            }
        },
        initComplete: function (settings, json) {
            var info = this.api().page.info();
            if (info.recordsTotal > 0) {
                $('#export_transaction_wrap').show();
            }
            $('#display_trans_from_dt').text($('#mvx_from_date').val());
            $('#export_transaction_start_date').val($('#mvx_from_date').val());
            $('#display_trans_to_dt').text($('#mvx_to_date').val());
            $('#export_transaction_end_date').val($('#mvx_to_date').val());
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'mvx_vendor_transactions_list', $MVX->ajax_url() ); ?>', 
            type: "post",
            data: function (data) {
                data.from_date = $('#mvx_from_date').val();
                data.to_date = $('#mvx_to_date').val();
                data.security = '<?php echo wp_create_nonce('mvx-transaction'); ?>';
            },
            error: function(xhr, status, error) {
                $("#vendor_transactions tbody").append('<tr class="odd"><td valign="top" colspan="6" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'multivendorx'); ?></a></td></tr>');
                $("#vendor_transactions_processing").css("display","none");
            }
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( vendor_transactions );
    $(document).on('click', '#vendor_transactions_date_filter #do_filter', function () {
        $('#display_trans_from_dt').text($('#mvx_from_date').val());
        $('#export_transaction_start_date').val($('#mvx_from_date').val());
        $('#display_trans_to_dt').text($('#mvx_to_date').val());
        $('#export_transaction_end_date').val($('#mvx_to_date').val());
        vendor_transactions.ajax.reload();
    });
});
</script>