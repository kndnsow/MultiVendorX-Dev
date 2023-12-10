<?php
/**
 * The template for displaying rejected vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/shortcode/rejected_vendor_dashboard.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     3.1.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $MVX;

echo '<div class="rejected-vendor-dashboard">';
do_action('mvx_before_rejected_vendor_dashboard');

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
        	<?php do_action('mvx_rejected_vendor_dashboard_content'); ?>
        </div>
    </div>
</div>

<?php
$MVX->template->get_template('vendor-dashboard/dashboard-footer.php');

do_action('mvx_after_rejected_vendor_dashboard');
echo '</div>';
