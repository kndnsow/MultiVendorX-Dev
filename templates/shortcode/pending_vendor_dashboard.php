<?php
/**
 * The template for displaying pending vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/shortcode/pending_vendor_dashboard.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     3.1.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $MVX, $wp;

echo '<div class="pending-vendor-dashboard">';
do_action('mvx_before_pending_vendor_dashboard');

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
            <div class="col-md-12 text-center">
				<div class="panel mvx-pending-vendor-notice">
                    <?php echo apply_filters( 'mvx_pending_vendor_dashboard_message', __('Congratulations! You have successfully applied as a Vendor. Please wait for further notifications from the admin.', 'multivendorx') ); ?>
                </div>
			</div>
        </div>
    </div>
</div>

<?php
$MVX->template->get_template('vendor-dashboard/dashboard-footer.php');

do_action('mvx_after_pending_vendor_dashboard');
echo '</div>';
