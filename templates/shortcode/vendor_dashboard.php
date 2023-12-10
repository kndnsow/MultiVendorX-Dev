<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/shortcode/vendor_dashboard.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     2.3.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $MVX;

do_action('mvx_before_vendor_dashboard');

//wc_print_notices();
$MVX->template->get_template('vendor-dashboard/dashboard-header.php');

do_action('mvx_vendor_dashboard_navigation', array());
$is_single = !is_null($MVX->endpoints->get_current_endpoint_var()) ? '-single' : '';
?>
<div id="page-wrapper" class="side-collapse-container">
    <div id="current-endpoint-title-wrapper" class="current-endpoint-title-wrapper">
        <div class="current-endpoint">
            <?php echo $MVX->vendor_hooks->mvx_create_vendor_dashboard_breadcrumbs($MVX->endpoints->get_current_endpoint()); ?>
        </div>
    </div>
    <!-- /.row -->
    <div class="content-padding gray-bkg <?php echo $MVX->endpoints->get_current_endpoint() ? $MVX->endpoints->get_current_endpoint().$is_single : 'dashboard'; ?>">
        <div class="notice-wrapper">
            <?php wc_print_notices(); ?>
        </div>
        <div class="row">
            <?php 
            $is_block = get_user_meta(get_current_vendor_id(), '_vendor_turn_off', true);
            if($is_block) {
				?>
				<div class="col-md-12 text-center">
					<div class="panel mvx-suspended-vendor-notice content-padding">
					    <?php echo apply_filters( 'mvx_suspended_vendor_dashboard_message', sprintf( __('Your account has been suspended by the admin due to some suspicious activity. Please contact your <a href="mailto:%s">admin</a> for further information.', 'multivendorx'), get_option('admin_email')) ); ?>
					</div>
				</div>
			<?php } else {
				do_action('mvx_vendor_dashboard_content');
			}?>
        </div>
    </div>
</div>

<?php
$MVX->template->get_template('vendor-dashboard/dashboard-footer.php');

do_action('mvx_after_vendor_dashboard');
