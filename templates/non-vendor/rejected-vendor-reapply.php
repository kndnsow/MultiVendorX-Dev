<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/non-vendor/rejected-vendor-reapply.php
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

if(!is_user_mvx_rejected_vendor(get_current_vendor_id())) {
	if(is_user_mvx_pending_vendor(get_current_vendor_id())) {
		?>
		<div class="row">
            <div class="col-md-12 text-center">
				<div class="panel mvx-pending-vendor-notice">
					<?php echo apply_filters( 'mvx_pending_vendor_dashboard_message', __('Congratulations! You have successfully applied as a Vendor. Please wait for further notifications from the admin.', 'multivendorx') ); ?>
				</div>
			</div>
        </div>
        <?php
        return;
	}
	return;
}

$mvx_vendor_registration_form_data = mvx_get_option('mvx_new_vendor_registration_form_data');
$form_data = array();
if(isset($mvx_vendor_registration_form_data) && is_array($mvx_vendor_registration_form_data)) {
	$vendor_application_data = get_user_meta(get_current_user_id(), 'mvx_vendor_fields', true);
	foreach($mvx_vendor_registration_form_data as $key => $value) {
		if( !empty( $vendor_application_data ) ) {
			foreach($vendor_application_data as $app_key => $app_value) {
				if($value['type'] == $app_value['type'] && $value['label'] == $app_value['label'] && isset($app_value['value'])) {
					$form_data[$key]['value'] = $app_value['value'];
				}
			}
		}
	}
}

?>
<div class="col-md-12">
	<form method="post" name="reapply_vendor_application_form" class="reapply_vendor_application_form form-horizontal" enctype="multipart/form-data">
		<?php do_action('mvx_before_reapply_vendor_application_form'); ?>
			<div class="panel panel-default pannel-outer-heading">
				<div class="panel-heading d-flex">
					<h3><?php _e('Previously Submitted Details', 'multivendorx'); ?></h3>
				</div>
				<div class="panel-body panel-content-padding">
					<div class="mvx_regi_form_box">
						<?php
							$MVX->template->get_template('vendor-registration-form.php', array('mvx_vendor_registration_form_data' => $mvx_vendor_registration_form_data, 'form_data' => array('mvx_vendor_fields' => $form_data)));
						?>
						<div class="clearboth"></div>
					</div>
				</div>
			</div>
		<?php do_action('mvx_after_reapply_vendor_application_form'); ?>
		<div class="mvx-action-container">
			<button class="btn btn-default" name="reapply_vendor_application"><?php _e('Apply Again!!', 'multivendorx'); ?></button>
			<div class="clear"></div>
		</div>
	</form>
</div>
