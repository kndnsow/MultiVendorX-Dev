<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/widget/quick-info.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

global $MVX;
$submit_label = isset( $instance['submit_label'] ) ? $instance['submit_label'] : __( 'Submit', 'multivendorx' );
$enable_recaptcha = isset( $instance['enable_google_recaptcha'] ) ? $instance['enable_google_recaptcha'] : false;
$recaptcha_type = ( $enable_recaptcha && isset( $instance['google_recaptcha_type'] ) ) ? $instance['google_recaptcha_type'] : 'v2';

extract( $instance );
?>
<div class="mvx-quick-info-wrapper">
    <?php
    if( isset( $_GET['message'] ) ) {
        $message = sanitize_text_field( $_GET['message'] );
        echo "<div class='woocommerce-{$widget->response[ $message ]['class']}'>" . esc_html($widget->response[ $message ]['message']) . "</div>";
    } else {
        $description = !empty($description) ? $description : '';
        echo '<p>' . esc_html($description) . '</p>';
    }?>

    <form action="" method="post" id="respond" style=" padding: 0;">
    <?php 
    if( $enable_recaptcha ) {
        echo '<input type="hidden" name="enable_recaptcha" value="1" />';
        echo '<input type="hidden" name="recaptcha_type" value="'.esc_attr($recaptcha_type).'" />';
        if( $recaptcha_type == 'v2' ) { ?>
            <script src="<?php echo esc_url('https://www.google.com/recaptcha/api.js'); ?>"></script>
            <?php echo wp_kses_post($recaptcha_v2_scripts); ?>
        <?php }else{ ?>
            <script src="<?php echo esc_url('https://www.google.com/recaptcha/api.js?render='.$recaptcha_v3_sitekey); ?>"></script>
            <script>
                grecaptcha.ready(function () {
                    grecaptcha.execute('<?php echo $recaptcha_v3_sitekey; ?>', { action: 'mvx_vendor_contact_widget' }).then(function (token) {
                        var recaptchaResponse = document.getElementById('recaptchav3_response');
                        recaptchaResponse.value = token;
                    });
                });
            </script>
            <input type="hidden" id="recaptchav3_response" name="recaptchav3_response" value="" />
            <input type="hidden" name="recaptchav3_sitekey" value="<?php echo esc_html($recaptcha_v3_sitekey); ?>" />
            <input type="hidden" name="recaptchav3_secretkey" value="<?php echo esc_html($recaptcha_v3_secretkey); ?>" />
        <?php }
    }
    ?>
                    <input type="text" class="input-text " name="quick_info[name]" value="<?php echo esc_html($current_user->display_name); ?>" placeholder="<?php esc_attr_e( 'Name', 'multivendorx' ) ?>" required/>
                    <input type="text" class="input-text " name="quick_info[subject]" value="" placeholder="<?php esc_attr_e( 'Subject', 'multivendorx' ) ?>" required/>
                    <input type="email" class="input-text " name="quick_info[email]" value="<?php echo esc_html($current_user->user_email);  ?>" placeholder="<?php esc_attr_e( 'Email', 'multivendorx' ) ?>" required/>
                    <textarea name="quick_info[message]" rows="5" placeholder="<?php esc_attr_e( 'Message', 'multivendorx' ) ?>" required></textarea>
                    <input type="submit" class="submit" id="submit" name="quick_info[submit]" value="<?php echo esc_html($submit_label); ?>" />
                    <input type="hidden" name="quick_info[spam]" value="" />
                    <input type="hidden" name="quick_info[vendor_id]" value="<?php echo esc_html($vendor->id); ?>" />
                    <?php wp_nonce_field( 'dc_vendor_quick_info_submitted', 'dc_vendor_quick_info_submitted' ); ?>
    </form>
</div>