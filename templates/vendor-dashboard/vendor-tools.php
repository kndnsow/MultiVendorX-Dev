<?php

/**
 * The template for displaying vendor tools
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-tools.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.1.5
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}

do_action( 'mvx_before_vendor_tools_content' );
?>
<div class="col-md-12">
    <div class="panel panel-default panel-pading">
        <div class="mvx-vendor-tools panel-body">
            <div class="tools-item">
                <label class="control-label col-md-9 col-sm-6">
                    <?php _e( 'Vendor Dashboard Transients', 'multivendorx' ); ?>
                    <p class="description"><?php _e( 'This tool will clear the dashboard widget transients cache.', 'multivendorx' ); ?></p>
                </label>
                <div class="col-md-3 col-sm-6">
                    <a class="mvx_vendor_clear_transients btn btn-default" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'tools_action' => 'clear_all_transients' ), mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_clear_cache_endpoint', 'seller_dashbaord', 'vendor-tools' ) ) ), 'mvx_clear_vendor_transients' ) ); ?>"><?php _e( 'Clear transients', 'multivendorx' ) ?></a>
                </div>
            </div>
            <?php do_action( 'mvx_vendor_dashboard_tools_item' ); ?>
        </div>
    </div>
</div>
<?php
do_action( 'mvx_after_vendor_tools_content' );