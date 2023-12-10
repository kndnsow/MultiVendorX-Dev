<?php

/**
 * The Template for displaying vendor registration form.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-registration-form.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.4.3
 */
global $MVX;
if (!empty($mvx_vendor_registration_form_data) && is_array($mvx_vendor_registration_form_data)) {
	if (isset($_POST) && is_array($_POST) && count($_POST) > 0) $form_data = $_POST;
    $sep_count = 0;
    // load tooltip librery
    $MVX->library->load_qtip_lib();
    foreach ($mvx_vendor_registration_form_data as $key => $value) {
        switch ($value['type']) {
            case 'section':
                ?>
                <div class="clearboth"></div>
                </div>
                <div class="mvx_regi_form_box">
                <h3 class="reg_header2"><?php echo esc_html_e($value['label'],'multivendorx'); ?></h3>
                <?php
                break;
            case 'textbox':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?>
                    <?php if (isset($value['tip_description']) && !empty($value['tip_description'])) { ?>
                        <span class="img_tip" data-desc="<?php echo esc_html( $value['tip_description'] ); ?>"></span>
                    <?php } ?>
                    </label>
                    <input type="text" value="<?php if (!empty($form_data['mvx_vendor_fields'][$key]["value"])) echo esc_attr($form_data['mvx_vendor_fields'][$key]["value"]); ?>" name="mvx_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if ($value['required']) { echo 'required="required"'; }?> />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="textbox" />
                </div>
                <?php
                break;
            case 'email':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="email" value="<?php if (!empty($form_data['mvx_vendor_fields'][$key]["value"])) echo esc_attr($form_data['mvx_vendor_fields'][$key]["value"]); ?>" name="mvx_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if ($value['required']) { echo 'required="required"'; }?> />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="email" />
                </div>
                <?php
                break;
            case 'textarea':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?>
                    <?php if (isset($value['tip_description']) && !empty($value['tip_description'])) { ?>
                        <span class="img_tip" data-desc="<?php echo esc_html( $value['tip_description'] ); ?>"></span>
                    <?php } ?>
                    </label>
                    <textarea <?php if (!empty($value['limit'])) { echo 'maxlength="'.$value['limit'].'"'; } ?> name="mvx_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if ($value['required']) { echo 'required'; }?>><?php if (!empty($form_data['mvx_vendor_fields'][$key]["value"])) { echo esc_attr($form_data['mvx_vendor_fields'][$key]["value"]); } ?></textarea>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="textarea" />
                </div>
                <?php
                break;
            case 'url': 
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="url" value="<?php if (!empty($form_data['mvx_vendor_fields'][$key]["value"])) echo esc_attr($form_data['mvx_vendor_fields'][$key]["value"]); ?>" name="mvx_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if ($value['required']) { echo 'required="required"'; }?> />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="url" />
                </div>
                <?php
                break;
            case 'radio':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <?php
                    if (!empty($value['options']) && is_array($value['options'])) {
                        ?>
                        <div class="mvx-regi-radio-inp-holder">
                        <?php
                        foreach ($value['options'] as $option_key => $option_value) {
                            ?>
                            <p><input type="radio" <?php if ($option_value['selected']) { echo 'checked="checked"'; } ?> name="mvx_vendor_fields[<?php echo $key; ?>][value]" value="<?php echo $option_value['value']; ?>" <?php if ($value['required']) { echo 'required="required"'; }?>> <?php echo $option_value['label']; ?></p>
                            <?php
                        }
                        ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                break;

            case 'dropdown':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <select class="select_box" name="mvx_vendor_fields[<?php echo $key; ?>][value]" <?php if ($value['required']) { echo 'required="required"'; }?>>
                    <?php
                    if (!empty($value['options']) && is_array($value['options'])) {
                        foreach ($value['options'] as $option_key => $option_value) {
                            ?>
                            <option value="<?php echo $option_value['value']; ?>" <?php if ($option_value['selected']) { echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                    </select>
                </div>
                <?php
                break;

            case 'checkboxes':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <?php
                    if (!empty($value['options']) && is_array($value['options'])) {
                        foreach ($value['options'] as $option_key => $option_value) {
                            ?>
                            <p> <input type="checkbox" <?php if ($option_value['selected']) { echo 'checked="checked"'; } ?> name="mvx_vendor_fields[<?php echo $key; ?>][value]" class="mvx-regs-multi-check" value="<?php echo esc_attr($option_value['value']); ?>"> <?php echo htmlentities($option_value['label']); ?>
                            </p>
                            
                            <?php
                        }
                    }
                    if ($value && isset($value['required'])) {
                        wp_add_inline_script('woocommerce', "(function ($) { 
                            $('.mvx_regi_main .register').submit(function(e) {
                                checked = $('.mvx-regs-multi-check:checked').length;
                                if (!checked) {
                                    e.preventDefault();
                                    $('.mvx-regs-multi-check')[0].focus();
                                    return false;
                                }
                            });
                        })(jQuery)");
                    }
                    ?>
                </div>
                <?php
                break;

            case 'multi-select':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <select class="select_box" style="min-height: 59px;" name="mvx_vendor_fields[<?php echo $key; ?>][value][]" <?php if ($value['required']) { echo 'required="required"'; }?> multiple="">
                    <?php
                    if (!empty($value['options']) && is_array($value['options'])) {
                        foreach ($value['options'] as $option_key => $option_value) {
                            ?>
                            <option value="<?php echo $option_value['value']; ?>" <?php if ($option_value['selected']) { echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                    </select>
                </div>
                <?php
                break;

            case 'checkbox':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <input type="checkbox" name="mvx_vendor_fields[<?php echo $key; ?>][value]" <?php if (!empty($form_data['mvx_vendor_fields'][$key]["value"]) && $form_data['mvx_vendor_fields'][$key]["value"] == 'on') { echo 'checked="checked"';}?> <?php if (!isset($form_data['mvx_vendor_fields'][$key]["value"]) && $value['defaultValue'] == 'checked') { echo 'checked="checked"';} ?>  <?php if ($value['required']) { echo 'required="required"'; }?> />
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="checkbox" />
                </div>
                <?php
                break;
            case 'recaptcha':
                $recaptcha_type = $value['recaptchatype'];
                $sitekey = isset($value['sitekey']) ? $value['sitekey'] : '' ;
                $secretkey = isset($value['secretkey']) ? $value['secretkey'] : '' ;
                $script_url = ($recaptcha_type == 'v3') ? 'https://www.google.com/recaptcha/api.js?render='.$sitekey : 'https://www.google.com/recaptcha/api.js';
                ?>
                <script src="<?php echo $script_url; ?>"></script>
                <?php if ($recaptcha_type == 'v3'): ?>
                <script>
                    grecaptcha.ready(function () {
                        grecaptcha.execute('<?php echo $sitekey; ?>', { action: 'mvx_vendor_registration' }).then(function (token) {
                            var recaptchaResponse = document.getElementById('recaptchav3Response');
                            recaptchaResponse.value = token;
                        });
                    });
                </script>
                <?php endif; ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo ($recaptcha_type == 'v2') ? __($value['label'],'multivendorx') : ''; ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <?php echo $value['script']; ?>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][value]" value="Verified" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="recaptcha" />
                    <?php if ($recaptcha_type == 'v3'): ?>
                    <input type="hidden" name="recaptchav3Response" id="recaptchav3Response" />
                    <input type="hidden" name="recaptchav3_sitekey" value="<?php echo $sitekey; ?>" />
                    <input type="hidden" name="recaptchav3_secretkey" value="<?php echo $secretkey; ?>" />
                    <?php endif; ?>
                    <input type="hidden" name="g-recaptchatype" value="<?php echo $recaptcha_type; ?>" />
                </div>
                <?php
                break;
            case 'attachment':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="file" name="mvx_vendor_fields[<?php echo $key; ?>][]" <?php if ($value['required']) { echo 'required="required"'; }?> <?php if ($value['muliple']) { echo 'multiple="true"'; }?> />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="file" />
                </div>
                <?php
                break;
            case 'vendor_address_1':
            case 'vendor_address_2':
            case 'vendor_phone':
            case 'vendor_city':
            case 'vendor_postcode':
            case 'vendor_paypal_email':
            case 'vendor_page_title':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="text" value="<?php if (!empty($form_data['mvx_vendor_fields'][$key]["value"])) echo esc_attr($form_data['mvx_vendor_fields'][$key]["value"]); ?>" name="mvx_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if ($value['required']) { echo 'required="required"'; }?> />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo $value['type']; ?>" />
                </div>
                <?php
                break;
            case 'vendor_country':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo $value['type']; ?>" />
                    <select class="country_to_state select_box" name="mvx_vendor_fields[<?php echo $key; ?>][value]" <?php if ($value['required']) { echo 'required="required"'; }?>>
                        <option value=""><?php esc_html_e( 'Select a country&hellip;', 'multivendorx' ); ?></option>
                        <?php 
                            foreach ( WC()->countries->get_allowed_countries() as $key => $value ) {
                                echo '<option value="' . esc_attr( $key ) . '" '. selected(apply_filters('mvx_vendor_registration_form_default_country_code', '', $key), $key).'>' . esc_html( $value ) . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <?php
                break;
            case 'vendor_state':
                ?>
                <div class="vendor_state_wrapper mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo esc_attr($value['type']); ?>" />
                    <select id="vendor_state" class="state_select select_box" name="mvx_vendor_fields[<?php echo $key; ?>][value]" <?php if ($value['required']) { echo 'required="required"'; }?>>
                        
                    </select>
                </div>
                <?php
                break;
            case 'vendor_description':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <textarea <?php if (!empty($value['limit'])) { echo 'maxlength="'.$value['limit'].'"'; } ?> name="mvx_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo esc_attr($value['placeholder']); ?>" <?php if ($value['required']) { echo 'required'; }?>><?php if (!empty($form_data['mvx_vendor_fields'][$key]["value"])) { echo esc_attr($form_data['mvx_vendor_fields'][$key]["value"]); } ?></textarea>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo $value['type']; ?>" />
                </div>
                <?php
                break;
            case 'terms_conditions':
                ?>
                <div class="mvx-regi-form-row <?php if (!empty($value['cssClass'])) { echo $value['cssClass']; } else {  echo 'mvx-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'multivendorx'); ?><?php if ($value['required']) { echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="mvx_vendor_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <p> <input type="checkbox" name="mvx_vendor_fields[<?php echo $key; ?>][value]" class="mvx-regs-multi-terms" value="<?php esc_attr_e('Checked', 'multivendorx'); ?>" <?php echo isset($value['required']) && $value['required'] ? 'required' : '' ?>> 
                        <?php echo wp_kses_post($value['placeholder']); ?>
                    </p>
                </div>
                <?php
                break;
        }
    }
}

?>
<script>
    jQuery(document).ready(function ($) {
        $('.img_tip').each(function () {
            $(this).qtip({
                content: $(this).attr('data-desc'),
                position: {
                    my: 'top center',
                    at: 'bottom center',
                    viewport: $(window)
                },
                show: {
                    event: 'mouseover',
                    solo: true,
                },
                hide: {
                    inactive: 6000,
                    fixed: true
                },
                style: {
                    classes: 'qtip-dark qtip-shadow qtip-rounded qtip-dc-css',
                    width: 200
                }
            });
        });
    });
</script>