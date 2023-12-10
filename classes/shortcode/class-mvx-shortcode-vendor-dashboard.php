<?php

/**
 * MVX Vendor Dashboard Shortcode Class
 *
 * @version		2.2.0
 * @package		MultivendorX/shortcode
 * @author 		MultiVendorX
 */
class MVX_Vendor_Dashboard_Shortcode {

    public function __construct() {
        
    }

    /**
     * Output the vendor dashboard shortcode.
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output($attr) {
        global $MVX, $wp;
        $MVX->nocache();
        if (!defined('MVX_DASHBAOARD')) {
            define('MVX_DASHBAOARD', true);
        }
        if (!is_user_logged_in()) {
            if (( 'no' === get_option('woocommerce_registration_generate_password') && !is_user_logged_in())) {
                wp_enqueue_script('wc-password-strength-meter');
            }
            echo '<div class="mvx-dashboard woocommerce">';
            wc_get_template('myaccount/form-login.php');
            echo '</div>';
        } else if (!is_user_mvx_vendor(get_current_vendor_id())) {
        	$user = wp_get_current_user();
        	
        	if ($user && in_array('dc_pending_vendor', $user->roles)) {
        		$MVX->template->get_template('shortcode/pending_vendor_dashboard.php');
        	} else if ($user && in_array('dc_rejected_vendor', $user->roles)) {
        		$MVX->template->get_template('shortcode/rejected_vendor_dashboard.php');
        	} else {
        		$MVX->template->get_template('shortcode/non_vendor_dashboard.php');
            }
        } else {
            do_action('mvx_dashboard_setup');
            $MVX->template->get_template('shortcode/vendor_dashboard.php');
        }
    }

}
