<?php
/**
 * The template for displaying vendor orders
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-refund-list.php
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

$refund_list_table_headers = apply_filters('mvx_datatable_refund_list_table_headers', array(
    'order_id'  => array('label' => __( 'Order ID', 'multivendorx' )),
    'order_status'  => array('label' => __( 'Order status', 'multivendorx' )),
    'refund_status'  => array('label' => __( 'Refund status', 'multivendorx' )),
    'refund_reason'  => array('label' => __( 'Refund reason', 'multivendorx' )),
    'payment_gateway'  => array('label' => __( 'Payment gateway', 'multivendorx' )),
    'action'   => array('label' => __( 'Action', 'multivendorx' )),
), get_current_user_id());

?>
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped table-bordered" id="mvx-vendor-refund" width="100%">
                <thead>
                    <tr>
                    <?php 
                        if ($refund_list_table_headers) :
                            foreach ($refund_list_table_headers as $header) { ?>
                                <th class="<?php if(isset($header['class'])) echo esc_attr($header['class']); ?>"><?php if(isset($header['label'])) echo esc_html($header['label']); ?></th>
                            <?php }
                        endif;
                    ?>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var refund_table;
        var columns = [];
        <?php if ($refund_list_table_headers) {
            foreach ($refund_list_table_headers as $key => $header) { ?>
                obj = {};
                obj['data'] = '<?php echo esc_js($key); ?>';
                obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
                columns.push(obj);
            <?php }
        }
        ?>
        refund_table = $('#mvx-vendor-refund').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            responsive: true,
            drawCallback: function (settings) {
                if (settings.json.notices.length > 0 ) {
                    $('.mvx-wrapper .notice-wrapper').html('');
                    $.each(settings.json.notices, function( index, notice ) {
                        if (notice.type == 'success') {
                            $('.mvx-wrapper .notice-wrapper').append('<div class="woocommerce-message" role="alert">'+notice.message+'</div>');
                        } else {
                            $('.mvx-wrapper .notice-wrapper').append('<div class="woocommerce-error" role="alert">'+notice.message+'</div>');
                        }
                    });
                }
            },
            language: {
                emptyTable: "<?php echo trim(__('No refunds found!', 'multivendorx')); ?>",
                processing: "<?php echo trim(__('Processing...', 'multivendorx')); ?>",
                info: "<?php echo trim(__('Showing _START_ to _END_ of _TOTAL_ refunds', 'multivendorx')); ?>",
                infoEmpty: "<?php echo trim(__('Showing 0 to 0 of 0 refunds', 'multivendorx')); ?>",
                lengthMenu: "<?php echo trim(__('Number of rows _MENU_', 'multivendorx')); ?>",
                zeroRecords: "<?php echo trim(__('No matching refunds found', 'multivendorx')); ?>",
                paginate: {
                    next: "<?php echo trim(__('Next', 'multivendorx')); ?>",
                    previous: "<?php echo trim(__('Previous', 'multivendorx')); ?>"
                }
            },
            ajax: {
                url: '<?php echo add_query_arg( 'action', 'mvx_datatable_get_vendor_refund', $MVX->ajax_url() ); ?>',
                type: "post",
                data: function (data) {
                    data.security = '<?php echo wp_create_nonce('mvx-dashboard'); ?>';
                },
                error: function(xhr, status, error) {
                    $("#mvx-vendor-refund tbody").append('<tr class="odd"><td valign="top" colspan="6" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'multivendorx'); ?></a></td></tr>');
                    $("#mvx-vendor-refund_processing").css("display","none");
                }
            },
            columns: columns
        });
        new $.fn.dataTable.FixedHeader( refund_table );
    });

</script>