<?php
/**
 * The template for displaying vendor profile management 
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/profile.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.1.0
 */
global $MVX;

$vendor_profile_image = get_user_meta($user->ID, '_vendor_profile_image', true);
?>
<div class="col-md-12">
    <form method="post" name="profile_settings_form" class="mvx_profile_form form-horizontal">
        <?php do_action('mvx_before_vendor_dashboard_profile'); ?>
		<div class="panel panel-default pannel-outer-heading">
			<div class="panel-heading d-flex">
				<h3><?php _e('Personal Information', 'multivendorx'); ?></h3>
			</div>
			<div class="panel-body panel-content-padding">
				<div class="mvx_form1">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('First Name', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_profile_data[first_name]" value="<?php echo isset($user->first_name)? $user->first_name : ''; ?>"  placeholder="<?php _e('Enter your First Name here', 'multivendorx'); ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Last Name', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_profile_data[last_name]" value="<?php echo isset($user->last_name)? $user->last_name : ''; ?>"  placeholder="<?php _e('Enter your Last Name here', 'multivendorx'); ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Email', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_profile_data[user_email]" required value="<?php echo isset($user->user_email) ? $user->user_email : ''; ?>"  placeholder="<?php _e('Enter your Email here', 'multivendorx'); ?>">
                            <div class="mvx-do-change-pass">
                                <button type="button" class="btn btn-secondary" id="mvx-change-pass"><?php _e('Change Password', 'multivendorx'); ?></button>
                            </div>
                        </div>  
                    </div>
                    
                    <div class="form-group vendor-edit-pass-field" style="display:none;">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Current password', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="password" name="vendor_profile_data[password_current]" autocomplete="off" >
                            <div class="hints"><?php _e('Keep it blank for not to update.', 'multivendorx'); ?></div>
                        </div>  
                    </div>
                    <div class="form-group vendor-edit-pass-field" style="display:none;">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('New password', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="password" name="vendor_profile_data[password_1]" autocomplete="off" >
                            <div class="hints"><?php _e('Keep it blank for not to update.', 'multivendorx'); ?></div>
                        </div>  
                    </div>
                    <div class="form-group vendor-edit-pass-field" style="display:none;">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Confirm new password', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="password" name="vendor_profile_data[password_2]" autocomplete="off" >
                        </div>  
                    </div>             
                    
                    <?php if ($MVX->vendor_caps->vendor_can('is_upload_files')) { ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Profile Image', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                        	<img id="vendor-profile-img" src="<?php echo (isset($vendor_profile_image) && $vendor_profile_image > 0) ? $vendor_profile_image :  get_avatar_url($user->ID, array('size' => 120)); ?>" width="120" alt="dp">
							<div class="mvx-media profile-pic-btn">
								<button type="button" class="btn btn-secondary mvx_upload_btn" data-target="vendor-profile"><i class="mvx-font ico-edit-pencil-icon"></i> <?php _e('Upload image', 'multivendorx'); ?></button>
							</div>
							<input type="hidden" name="vendor_profile_data[vendor_profile_image]" id="vendor-profile-img-id" class="user-profile-fields" value="<?php echo isset($vendor_profile_image) ? $vendor_profile_image : ''; ?>"  />
						</div>  
                    </div>
                    <?php } ?>
                </div>
                <?php if (get_mvx_vendor_settings('vendor_deactivation_enabled')) { ?>
                    <!-- add deactivate button -->
                    <button type="button" id="deactivate_button" class="deactivate-button btn btn-default" data-vendor_id="<?php echo esc_attr($user->ID) ?>" onclick="mvxMarkeAsdeactivate(<?php echo esc_js($user->ID) ?>)"><?php esc_html_e('Deactivate Account', 'multivendorx'); ?></button>
                <?php } ?>
			</div>
		</div>
        <?php do_action('mvx_after_vendor_dashboard_profile'); ?>
        <?php do_action('other_exta_field_dcmv'); ?>
        <div class="mvx-action-container">
            <button class="btn btn-default" name="store_save_profile"><?php _e('Save Options', 'multivendorx'); ?></button>
            <div class="clear"></div>
        </div>
        <div id="marke-as-deactivate-modal" class="modal fade" role="dialog" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><?php _e('Specify Deactivation Reason', 'multivendorx') ?></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body"> 
                        <p id="msg-for-request-sent-sucesss" class="" ></p>
                        <p id="msg-for-request-sent-error" class="" ></p>
                        <div class="form-group">
                            <textarea class="form-control" rows="5" id="deactivate_reason" placeholder="<?php _e('Type your text here', 'multivendorx') ?>"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="mvx-marke-deactivate-vendor-id" />
                    <div class="modal-footer">
                        <button type="button" class="mvx-submit-mark-as-deactivate btn btn-default"><?php _e('Sent', 'multivendorx') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>