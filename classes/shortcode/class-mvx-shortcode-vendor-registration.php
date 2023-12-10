<?php

/**
 * MVX Vendor Registration Shortcode Class
 *
 * @version		2.4.3
 * @package		MultivendorX/shortcode
 * @author 		MultiVendorX
 */
class MVX_Vendor_Registration_Shortcode {

    /**
     * Output the vendor Registration shortcode.
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output($attr) {
        global $MVX;
        $enable_registration = ( get_option('users_can_register') ) ? get_option('users_can_register') : false;
        $enable_registration = ( !$enable_registration ) ? get_option( 'woocommerce_enable_myaccount_registration' ) != 'yes' ? false : true : $enable_registration;
        if (!apply_filters('mvx_enable_users_can_register_for_vendor_registration', $enable_registration)) {
            echo ' ' . __('Signup has been disabled.', 'multivendorx');
            return;
        }
        $frontend_style_path = $MVX->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace(array('http:', 'https:'), '', $frontend_style_path);
        $suffix = defined('MVX_SCRIPT_DEBUG') && MVX_SCRIPT_DEBUG ? '' : '.min';
        if (( 'no' === get_option('woocommerce_registration_generate_password') && !is_user_logged_in())) {
            wp_enqueue_script('wc-password-strength-meter');
        }
        wp_enqueue_script( 'wc-country-select' );
        wp_enqueue_script( 'mvx_country_state_js' );
        wp_enqueue_style('mvx_vandor_registration_css', $frontend_style_path . 'vendor-registration' . $suffix . '.css', array(), $MVX->version);
        $MVX->template->get_template('shortcode/vendor_registration.php');
    }

}
