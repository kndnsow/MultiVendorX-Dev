<?php
/**
 * The template for displaying vendor report
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-report.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
global $MVX;
?>
<div class="col-md-12">
    
    <div class="panel panel-default panel-pading">
        <form name="mvx_vendor_dashboard_stat_report" method="POST" class="stat-date-range form-inline">
            <div class="mvx_form1 ">
                <div class="panel-heading d-lg-flex">
                    <h3><?php esc_html_e('Select Date Range :', 'multivendorx'); ?></h3> 
                    <div class="form-group">
                        <input type="date" name="mvx_stat_start_dt" value="<?php echo isset($_POST['mvx_stat_start_dt']) ? wc_clean($_POST['mvx_stat_start_dt']) : date('Y-m-01'); ?>" class="pickdate gap1 mvx_stat_start_dt form-control">
                    </div>
                    <div class="form-group">
                        <input type="date" name="mvx_stat_end_dt" value="<?php echo isset($_POST['mvx_stat_end_dt']) ? wc_clean($_POST['mvx_stat_end_dt']) : date('Y-m-d'); ?>" class="pickdate mvx_stat_end_dt form-control">
                    </div>
                    <div class="form-group">
                        <button name="submit_button" type="submit" value="Show" class="mvx_black_btn btn btn-default"><?php esc_html_e('Show', 'multivendorx'); ?></button>
                    </div> 
                    <?php if (apply_filters('mvx_can_vendor_export_orders_csv', true, get_current_vendor_id())) : ?>
                    <div class="form-group">
                        <button type="submit" class="mvx_black_btn btn btn-default" name="mvx_stat_export" value="export"><?php esc_html_e('Download CSV', 'multivendorx'); ?></button>
                    </div> 
                    <?php endif; ?>
                </div>
                <div class="panel-body">
                    <div class="mvx_ass_holder_box">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mvx_displaybox2 text-center">
                                    <h4><?php esc_html_e('Total Sales', 'multivendorx'); ?></h4>
                                    <h3><?php echo wc_price($total_vendor_sales); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mvx_displaybox2 text-center">
                                    <h4><?php esc_html_e('My Earnings', 'multivendorx'); ?></h4>
                                    <h3><?php echo wc_price($total_vendor_earning); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mvx_displaybox2 text-center">
                                    <h4><?php esc_html_e('Total number of Orders placed', 'multivendorx'); ?></h4>
                                    <h3><?php echo $total_order_count; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mvx_displaybox2 text-center">
                                    <h4><?php esc_html_e('Purchased Products', 'multivendorx'); ?></h4>
                                    <h3><?php echo $total_purchased_products; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mvx_displaybox2 text-center">
                                    <h4><?php esc_html_e('Number of Coupons used', 'multivendorx'); ?></h4>
                                    <h3><?php echo $total_coupon_used; ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mvx_displaybox2 text-center">
                                    <h4><?php esc_html_e('Total Coupon Discount', 'multivendorx'); ?></h4>
                                    <h3><?php echo wc_price($total_coupon_discount_value); ?></h3>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mvx_displaybox2 text-center">
                                    <h4><?php esc_html_e('Number of Unique Customers', 'multivendorx'); ?></h4>
                                    <h3><?php echo count($total_customers); ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
