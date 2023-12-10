<?php global $MVX; ?>
<?php wc_print_notices(); ?>
<div class="mvx_regi_main">
    <form class="register" role="form" method="post" enctype="multipart/form-data">
        <?php $mvx_vendor_registration_form_data = mvx_get_option('mvx_new_vendor_registration_form_data'); ?>
        <h2 class="reg_header1"><?php echo isset($mvx_vendor_registration_form_data[0]['label']) && !empty($mvx_vendor_registration_form_data[0]['label']) ? $mvx_vendor_registration_form_data[0]['label'] : apply_filters('mvx_vendor_registration_header_text',__('Vendor Registration Form','multivendorx')); ?></h2>
        <h7 class=""><?php echo isset($mvx_vendor_registration_form_data[0]['label']) && !empty($mvx_vendor_registration_form_data[0]['description']) ? $mvx_vendor_registration_form_data[0]['description'] : apply_filters('mvx_vendor_registration_description_text', ''); ?></h7>

        <div class="mvx_regi_form_box">
            <?php if(!is_user_logged_in()) : 
                $mvx_vendor_general_settings_name = mvx_get_option('mvx_vendor_general_settings_name');?>
            <h3 class="reg_header2"><?php echo apply_filters('woocommerce_section_label', __('Account Details', 'multivendorx')); ?></h3>
            <?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
                <div class="mvx-regi-12">
                    <label for="reg_username"><?php _e('Username', 'multivendorx'); ?> <span class="required">*</span></label>
                    <input type="text"  name="username" id="reg_username" value="<?php if (!empty($_POST['username'])) echo esc_attr($_POST['username']); ?>" required="required" />
                </div>
            <?php endif; ?>
            <div class="mvx-regi-12">
                <label for="reg_email"><?php _e('Email address', 'multivendorx'); ?> <span class="required">*</span></label>
                <input type="email" required="required"  name="email" id="reg_email" value="<?php if (!empty($_POST['email'])) echo esc_attr($_POST['email']); ?>" />
            </div>
            <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
                <div class="mvx-regi-12">
                    <label for="reg_password"><?php _e('Password', 'multivendorx'); ?> <span class="required">*</span></label>
                    <input type="password" required="required" name="password" id="reg_password" />
                </div>
            <?php endif; ?>
            <div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e('Anti-spam', 'multivendorx'); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>
            <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce');  ?>
            <?php endif; ?>
            <?php do_action('mvx_vendor_register_form'); ?>
            <div class="clearboth"></div>
        </div>
        <?php //do_action('register_form'); ?> 
        <?php if(is_user_logged_in()){ echo '<input type="hidden" name="vendor_apply" />'; }  ?>
        <input type="hidden" value="true" name="pending_vendor" />
        <?php do_action( 'woocommerce_register_form' ); ?>
        <p class="woocomerce-FormRow form-row">
            <?php 
            $button_text = apply_filters('mvx_vendor_registration_submit',__('Register', 'multivendorx'));
            ?>
            <button type="submit" class="woocommerce-Button button" name="register"><?php echo $button_text; ?></button>
        </p>
        <?php do_action('woocommerce_register_form_end'); ?>
    </form>
</div>