<?php

/*
 * The template for displaying vendor stats reports dashboard widget
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/dashboard-widgets/mvx-vendor-stats-reports.php
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

do_action('mvx_before_vendor_stats_reports'); 
?>
<div class="pannel panel-default pannel-outer-heading staticstics-panel-wrap">
    <div class="panel-body">
        <h2><i class="mvx-font ico-report-icon"></i> <?php printf( __( 'Your Store Report - %s', 'multivendorx' ), '<span class="_mvx_stats_period"></span>' );?></h2>
        <div class="row">
            <div class="col-md-4 key-perfomence-indicator">
                <h2><?php _e('Key Performance Indicators', 'multivendorx'); ?></h2>
                <ul class="short-stat-info-list">
                    <li>
                        <span class="stat-icon" title="<?php _e('Traffic', 'multivendorx'); ?>"><i class="mvx-font ico-visit-icon"></i></span>
                        <span class="_mvx_stats_table current_traffic_no current-stat-report"></span>
                        <span class="_mvx_stats_table previous_traffic_no prev-stat-report"></span>
                    </li>
                    <li>
                        <span class="stat-icon" title="<?php _e('Order No', 'multivendorx'); ?>"><i class="mvx-font ico-cart-icon"></i></span>
                        <span class="_mvx_stats_table current_orders_no current-stat-report"></span>
                        <span class="_mvx_stats_table previous_orders_no prev-stat-report"></span>
                    </li>
                    <li>
                        <span class="stat-icon" title="<?php _e('Sales', 'multivendorx'); ?>"><i class="mvx-font ico-price2-icon"></i></span>
                        <span class="_mvx_stats_table current_sales_total current-stat-report"></span>
                        <span class="_mvx_stats_table previous_sales_total prev-stat-report"></span>
                    </li>
                </ul>
                <ul class="short-stat-info-list">
                    <li>
                        <span class="stat-icon" title="<?php _e('Earning', 'multivendorx'); ?>"><i class="mvx-font ico-earning-icon"></i></span>
                        <span class="_mvx_stats_table current_earning current-stat-report"></span>
                        <span class="_mvx_stats_table previous_earning prev-stat-report"></span>
                    </li>
                    <li>
                        <span class="stat-icon" title="<?php _e('Withdrawal', 'multivendorx'); ?>"><i class="mvx-font ico-revenue-icon"></i></span>
                        <span class="_mvx_stats_table current_withdrawal current-stat-report"></span>
                        <span class="_mvx_stats_table previous_withdrawal prev-stat-report"></span>
                    </li>
                </ul>
            </div>
            <div class="col-md-8">
                <h2><?php _e('Store Insights', 'multivendorx'); ?></h2>
                <p class="stat-detail-info"><span><i class="mvx-font ico-avarage-order-value-icon"></i></span> <?php printf( __( 'Your average order value %1$s for this span was %2$s', 'multivendorx' ), '<strong>(AOV)</strong>', '<span class="_mvx_stats_aov stats-aov"></span>'); ?> </p>
                <p class="stat-detail-info"><span><i class="mvx-font ico-revenue-icon"></i></span> <?php printf( __( 'During this span, %1$s has been credited to your %2$s account, as commission.', 'multivendorx' ), '<mark class="_mvx_stats_table current_withdrawal withdrawal-label mark-green"></mark>', $payment_mode); ?></p>
                <div class="compare-stat-info">
                    <span><b><?php _e('Compare your store performance against', 'multivendorx'); ?></b></span>
                    <select name="" id="mvx_vendor_stats_report_filter" class="form-control" data-stats="<?php echo htmlspecialchars(wp_json_encode($vendor_report_data)); ?>">
                        <?php 
                        if($stats_reports_periods){
                            foreach ($stats_reports_periods as $key => $value) {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <ul class="mvx-website-stat-list">
                    <li>
                        <span><i class="mvx-font ico-visit-icon"></i></span>
                        <span><?php _e('Store traffic', 'multivendorx'); ?> <mark id="stats-diff-traffic" class="_mvx_diff_traffic_no "></mark></span>
                    </li>
                    <li>
                        <span><i class="mvx-font ico-cart-icon"></i></span>
                        <span><?php _e('Received orders', 'multivendorx'); ?> <mark id="stats-diff-order-no" class="_mvx_diff_orders_no "></mark></span>
                    </li> 
                    <li>
                        <span><i class="mvx-font ico-price2-icon"></i></span>
                        <span><?php _e('Total sales', 'multivendorx'); ?> <mark id="stats-diff-sales-total" class="_mvx_diff_sales_total "></mark></span>
                    </li>
                    
                    <li>
                        <span><i class="mvx-font ico-earning-icon"></i></span>
                        <span><?php _e('Your earning', 'multivendorx'); ?> <mark id="stats-diff-earning" class="_mvx_diff_earning "></mark></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
do_action('mvx_after_vendor_stats_reports');
