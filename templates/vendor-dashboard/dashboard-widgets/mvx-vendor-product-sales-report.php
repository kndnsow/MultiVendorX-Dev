<?php

/*
 * The template for displaying vendor pending shipping table dashboard widget
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/dashboard-widgets/mvx-vendor-product-sales-report.php
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
$product_sales_report_table_headers = apply_filters('mvx_datatable_widget_product_sales_report_table_headers', array(
    'product'      => array('label' => __( 'Product', 'multivendorx' )),
    'revenue'    => array('label' => __( 'Revenue', 'multivendorx' )),
    'unique_purchase'=> array('label' => __( 'Unique Purchases', 'multivendorx' )),
), get_current_user_id());
?>
<table id="widget_product_sales_report" class="table table-striped product_sold_last_week table-bordered mvx-widget-dt" width="100%">
    <thead>
        <tr>
        <?php 
            if($product_sales_report_table_headers) :
                foreach ($product_sales_report_table_headers as $key => $header) { ?>
            <th class="<?php if(isset($header['class'])) echo $header['class']; ?>"><?php if(isset($header['label'])) echo $header['label']; ?></th>         
                <?php }
            endif;
        ?>
            <!--th><?php _e('Product', 'multivendorx'); ?></th>
            <th><?php _e('Revenue', 'multivendorx'); ?></th>
            <th><?php _e('Unique Purchases', 'multivendorx'); ?></th-->
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
jQuery(document).ready(function($) {
    var product_sales_report_wgt;
    var columns = [];
    <?php if($product_sales_report_table_headers) {
     foreach ($product_sales_report_table_headers as $key => $header) { ?>
        obj = {};
        obj['data'] = '<?php echo esc_js($key); ?>';
        obj['className'] = '<?php if(isset($header['class'])) echo esc_js($header['class']); ?>';
        columns.push(obj);
     <?php }
        } ?>
    product_sales_report_wgt = $('#widget_product_sales_report').DataTable({
        ordering  : false,
        paging: false,
        info: false,
        searching  : false,
        processing: false,
        serverSide: true,
        responsive: true,
        language: {
            "emptyTable": "<?php echo trim(__('Not enough data.','multivendorx')); ?>",
            "zeroRecords": "<?php echo trim(__('Not enough data.','multivendorx')); ?>",
            
        },
        ajax:{
            url : '<?php echo add_query_arg( 'action', 'mvx_widget_vendor_product_sales_report', $MVX->ajax_url() ); ?>', 
            type: "post",
            data: function (data) {
                data.security = '<?php echo wp_create_nonce('mvx-sales'); ?>';
            },
            error: function(xhr, status, error) {
                $("#widget_product_sales_report tbody").append('<tr class="odd"><td valign="top" colspan="<?php if(is_array($product_sales_report_table_headers)) count($product_sales_report_table_headers); ?>" class="dataTables_empty" style="text-align:center;">'+error+' - <a href="javascript:window.location.reload();"><?php _e('Reload', 'multivendorx'); ?></a></td></tr>');
                $("#widget_product_sales_report").css("display","none");
            }
        },
        columns: columns
    });
    new $.fn.dataTable.FixedHeader( product_sales_report_wgt );
});
</script>