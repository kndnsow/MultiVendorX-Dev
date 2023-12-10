<?php
/**
 * The template for displaying vendor report
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-policy.php
 *
 * @author      MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
global $MVX;
$mvx_policy_settings = mvx_get_option("mvx_general_policies_settings_name");
$mvx_capabilities_settings_name = mvx_get_option("mvx_general_policies_settings_name");
$can_vendor_edit_policy_tab_label_field = apply_filters('can_vendor_edit_policy_tab_label_field', true);
$can_vendor_edit_cancellation_policy_field = apply_filters('can_vendor_edit_cancellation_policy_field', true);
$can_vendor_edit_refund_policy_field = apply_filters('can_vendor_edit_refund_policy_field', true);
$can_vendor_edit_shipping_policy_field = apply_filters('can_vendor_edit_shipping_policy_field', true);

$vendor_shipping_policy = isset($vendor_shipping_policy['value']) ? $vendor_shipping_policy['value'] : $mvx_policy_settings['shipping_policy'];
$vendor_refund_policy = isset($vendor_refund_policy['value']) ? $vendor_refund_policy['value'] : $mvx_policy_settings['refund_policy'];
$vendor_cancellation_policy = isset($vendor_cancellation_policy['value']) ? $vendor_cancellation_policy['value'] : $mvx_policy_settings['cancellation_policy'];

$_wp_editor_settings = array('tinymce' => true);
if (!$MVX->vendor_caps->vendor_can('is_upload_files')) {
    $_wp_editor_settings['media_buttons'] = false;
}
$_wp_editor_settings = apply_filters('mvx_vendor_policies_wp_editor_settings', $_wp_editor_settings);
?>
<div class="col-md-12">
    <form method="post" name="shop_settings_form" class="mvx_policy_form form-horizontal">
        <?php do_action('mvx_before_vendor_policy'); ?>
        <?php if (apply_filters('mvx_vendor_can_overwrite_policies', true) && mvx_is_module_active('store-policy')): ?>
            <div class="panel panel-default pannel-outer-heading">
                <div class="panel-heading d-flex">
                    <h3><?php _e('Policy Details', 'multivendorx'); ?></h3>
                </div>
                <div class="panel-body panel-content-padding">
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?php _e('Shipping Policy', 'multivendorx'); ?></label>
                            <div class="col-md-6 col-sm-9">
                                <?php $MVX->mvx_wp_fields->dc_generate_form_field(array("vendor_shipping_policy" => array('name' => 'vendor_shipping_policy', 'type' => 'wpeditor', 'class' => 'regular-textarea', 'value' => $vendor_shipping_policy, 'settings' => $_wp_editor_settings))); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?php _e('Refund Policy', 'multivendorx'); ?></label>
                            <div class="col-md-6 col-sm-9">
                                <?php $MVX->mvx_wp_fields->dc_generate_form_field(array("vendor_refund_policy" => array('name' => 'vendor_refund_policy', 'type' => 'wpeditor', 'class' => 'regular-textarea', 'value' => $vendor_refund_policy, 'settings' => $_wp_editor_settings))); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3"><?php _e('Cancellation/Return/Exchange Policy', 'multivendorx'); ?></label>
                            <div class="col-md-6 col-sm-9">
                                <?php $MVX->mvx_wp_fields->dc_generate_form_field(array("vendor_cancellation_policy" => array('name' => 'vendor_cancellation_policy', 'type' => 'wpeditor', 'class' => 'regular-textarea', 'value' => $vendor_cancellation_policy, 'settings' => $_wp_editor_settings))); ?>
                            </div>
                        </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (get_mvx_vendor_settings('is_customer_support_details', 'settings_general')) { ?>
            <div class="panel panel-default pannel-outer-heading">
                <div class="panel-heading d-flex">
                    <h3><?php _e('Customer Support Details', 'multivendorx'); ?></h3>
                </div>
                <div class="panel-body panel-content-padding">
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Phone', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input  class="no_input form-control" type="text" name="vendor_customer_phone" placeholder="" value="<?php echo isset($vendor_customer_phone['value']) ? $vendor_customer_phone['value'] : ''; ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Email', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input  class="no_input form-control" type="email" name="vendor_customer_email" placeholder="" value="<?php echo isset($vendor_customer_email['value']) ? $vendor_customer_email['value'] : ''; ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?php _e('Address', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="form-group">
                                <div class="col-sm-6 inp-btm-margin">
                                    <input  class="no_input form-control" type="text" placeholder="<?php _e('Address line 1', 'multivendorx'); ?>" name="vendor_csd_return_address1"  value="<?php echo isset($vendor_csd_return_address1['value']) ? $vendor_csd_return_address1['value'] : ''; ?>">
                                </div>
                                <div class="col-sm-6 inp-btm-margin">
                                    <input  class="no_input form-control" type="text" placeholder="<?php _e('Address line 2', 'multivendorx'); ?>" name="vendor_csd_return_address2"  value="<?php echo isset($vendor_csd_return_address2['value']) ? $vendor_csd_return_address2['value'] : ''; ?>">
                                </div>
                                <div class="col-sm-6 inp-btm-margin">
                                    <input  class="no_input form-control" type="text" placeholder="<?php _e('Country', 'multivendorx'); ?>" name="vendor_csd_return_country" value="<?php echo isset($vendor_csd_return_country['value']) ? $vendor_csd_return_country['value'] : ''; ?>">
                                </div>
                                <div class="col-sm-6 inp-btm-margin">
                                    <input  class="no_input form-control" type="text" placeholder="<?php _e('State', 'multivendorx'); ?>"  name="vendor_csd_return_state" value="<?php echo isset($vendor_csd_return_state['value']) ? $vendor_csd_return_state['value'] : ''; ?>">
                                </div>
                                <div class="col-sm-6 inp-btm-margin">
                                    <input  class="no_input form-control" type="text" placeholder="<?php _e('City', 'multivendorx'); ?>"  name="vendor_csd_return_city" value="<?php echo isset($vendor_csd_return_city['value']) ? $vendor_csd_return_city['value'] : ''; ?>">
                                </div>
                                <div class="col-sm-6 inp-btm-margin">
                                    <input  class="no_input form-control" type="text" placeholder="<?php _e('Zip code', 'multivendorx'); ?>" name="vendor_csd_return_zip" value="<?php echo isset($vendor_csd_return_zip['value']) ? $vendor_csd_return_zip['value'] : ''; ?>">
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php do_action('mvx_after_vendor_policy'); ?>
        <?php do_action('other_exta_field_dcmv'); ?>
        <div class="mvx-action-container">
            <button class="btn btn-default" name="store_save_policy"><?php _e('Save Options', 'multivendorx'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
</div>