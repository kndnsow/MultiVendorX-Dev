<?php
/*
 * The template for displaying vendor coupons
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/coupon-manager/coupons.php
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
do_action('mvx_before_vendor_dashboard_coupon_list_table');
$coupon_list_table_headers = apply_filters('mvx_datatable_coupon_list_table_headers', array(
    'coupons'      => array('label' => __( 'Coupon(s)', 'multivendorx' ), 'class' => 'name'),
    'type'    => array('label' => __( 'Coupon type', 'multivendorx' )),
    'amount'    => array('label' => __( 'Coupon Amount', 'multivendorx' )),
    'uses_limit'=> array('label' => __( 'Usage / Limit', 'multivendorx' )),
    'expiry_date'  => array('label' => __( 'Expiry Date', 'multivendorx' )),
    'actions'  => array('label' => __( 'Actions', 'multivendorx' )),
), get_current_user_id());
?>
<div class="col-md-12">
    <div class="panel panel-default panel-pading">
        <table id="coupons_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <?php 
                        if($coupon_list_table_headers) :
                            foreach ($coupon_list_table_headers as $key => $header) { ?>
                        <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><?php if(isset($header['label'])) echo $header['label']; ?></th>         
                            <?php }
                        endif;
                    ?>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <div class="mvx-action-container">
            <a href="<?php echo mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_add_coupon_endpoint', 'seller_dashbaord', 'add-coupon'));?>" class="btn btn-default"><?php echo __('Add Coupon', 'multivendorx');?></a>
        </div>
    </div>
</div>
<style>
    .vendor-coupon .row-actions{ visibility: hidden;}
    .vendor-coupon:hover .row-actions{ visibility: visible;}
    span.delete a{color: #a00;}
</style>
<script>
jQuery(document).ready(function($) {
    var vendor_coupons;
    var columns = [];
    <?php if($coupon_list_table_headers) {
     foreach ($coupon_list_table_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    vendor_coupons = $('#coupons_table').DataTable({
        columnDefs: [
            { width: 80, targets: 5 }
        ],
        ordering  : false,
        searching  : false,
        processing: true,
        serverSide: true,
        responsive: true,
        language: {
            emptyTable: "<?php echo trim(__('No coupons found!','multivendorx')); ?>",
            processing: "<?php echo trim(__('Processing...', 'multivendorx')); ?>",
            info: "<?php echo trim(__('Showing _START_ to _END_ of _TOTAL_ coupons','multivendorx')); ?>",
            infoEmpty: "<?php echo trim(__('Showing 0 to 0 of 0 coupons','multivendorx')); ?>",
            lengthMenu: "<?php echo trim(__('Number of rows _MENU_','multivendorx')); ?>",
            zeroRecords: "<?php echo trim(__('No matching coupons found','multivendorx')); ?>",
            paginate: {
                next: "<?php echo trim(__('Next', 'multivendorx')); ?>",
                previous: "<?php echo trim(__('Previous', 'multivendorx')); ?>"
            }
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'mvx_vendor_coupon_list', $MVX->ajax_url() ); ?>', 
            type: "post",
            data: function (data) {
                data.security = '<?php echo wp_create_nonce('mvx-coupon'); ?>';
            }, 
            error: function(xhr, status, error) {
                $("#coupons_table tbody").append('<tr class="odd"><td valign="top" colspan="4" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'multivendorx'); ?></a></td></tr>');
                $("#coupons_table_processing").css("display","none");
            }
        },
        createdRow: function (row, data, index) {
            $(row).addClass('vendor-coupon');
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( vendor_coupons );
});
</script>
<?php do_action('mvx_after_vendor_dashboard_coupon_list_table'); 