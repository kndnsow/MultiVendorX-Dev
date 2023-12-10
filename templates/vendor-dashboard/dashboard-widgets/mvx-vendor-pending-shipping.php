<?php

/*
 * The template for displaying vendor pending shipping table dashboard widget
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/dashboard-widgets/mvx-vendor-pending-shipping.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.0.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $MVX;
$vendor = get_current_vendor();
do_action('mvx_before_vendor_pending_shipping');
?>
<table id="widget_vendor_pending_shipping" class="table table-striped table-bordered mvx-widget-dt <?php //echo $pending_shippings ? 'responsive-table' : 'blank-responsive-table'; ?>" width="100%">
<?php if($default_headers){ ?>
    <thead>
        <tr>
            <?php 
                foreach ($default_headers as $key => $value) {
                    echo '<th>'.$value.'</th>';
                }
            ?>
        </tr>
    </thead>
    <tbody>
    </tbody>
<?php } ?>
</table>
 <!-- Modal -->
<div id="marke-as-ship-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php esc_html_e('Shipment Tracking Details', 'multivendorx'); ?></h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="tracking_url"><?php esc_html_e('Enter Tracking Url', 'multivendorx'); ?> *</label>
                        <input type="url" class="form-control" id="email" name="tracking_url" required="">
                    </div>
                    <div class="form-group">
                        <label for="tracking_id"><?php esc_html_e('Enter Tracking ID', 'multivendorx'); ?> *</label>
                        <input type="text" class="form-control" id="pwd" name="tracking_id" required="">
                    </div>
                </div>
                <input type="hidden" name="order_id" id="mvx-marke-ship-order-id" />
                <?php if (isset($_POST['mvx_start_date_order'])) : ?>
                    <input type="hidden" name="mvx_start_date_order" value="<?php echo isset($_POST['mvx_start_date_order']) ? wc_clean($_POST['mvx_start_date_order']) : date('Y-m-01'); ?>" />
                <?php endif; ?>
                <?php if (isset($_POST['mvx_end_date_order'])) : ?>
                    <input type="hidden" name="mvx_end_date_order" value="<?php echo isset($_POST['mvx_end_date_order']) ? wc_clean($_POST['mvx_end_date_order']) : date('Y-m-d'); ?>" />
                <?php endif; ?>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="mvx-submit-mark-as-ship"><?php esc_html_e('Submit', 'multivendorx'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
jQuery(document).ready(function($) {
    var pending_shipping_wgt;
    var columns = [];
    <?php if($default_headers) {
     foreach ($default_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    pending_shipping_wgt = $('#widget_vendor_pending_shipping').DataTable({
        ordering  : false,
        paging: false,
        info: false,
        searching  : false,
        processing: false,
        serverSide: true,
        responsive: true,
        language: {
            "emptyTable": "<?php echo trim(__('You have no pending shipping!','multivendorx')); ?>",
            "zeroRecords": "<?php echo trim(__('You have no pending shipping!','multivendorx')); ?>",
            
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'mvx_widget_vendor_pending_shipping', $MVX->ajax_url() ); ?>', 
            type: "post",
            data: function (data) {
                data.security = '<?php echo wp_create_nonce('mvx-pending-shipping'); ?>';
            },
            error: function(xhr, status, error) {
                $("#widget_vendor_pending_shipping tbody").append('<tr class="odd"><td valign="top" colspan="<?php if(is_array($default_headers)) count($default_headers); ?>" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php esc_html_e('Reload', 'multivendorx'); ?></a></td></tr>');
                $("#widget_vendor_pending_shipping").css("display","none");
            }
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( pending_shipping_wgt );
});
function mvxMarkeAsShip(self, order_id) {
    jQuery('#mvx-marke-ship-order-id').val(order_id);
    jQuery('#marke-as-ship-modal').modal('show');
}
</script>
<?php 
do_action('mvx_after_vendor_pending_shipping');