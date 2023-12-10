<?php
/**
 * The template for displaying vendor dashboard for non-vendors
 *
 * Override this template by copying it to yourtheme/MultiVendorX/shortcode/non_vendor_dashboard.php
 *
 * @author 		MultiVendorX
 * @package 	WCMm/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $MVX;
$user = wp_get_current_user();
if ($user && !in_array('dc_pending_vendor', $user->roles) && !in_array('administrator', $user->roles)) {
    add_filter('mvx_vendor_registration_submit', function ($text) {
        return __('Apply to become a vendor', 'multivendorx');
    });
    echo '<div class="woocommerce">';
    echo do_shortcode('[vendor_registration]');
    echo '</div>';
}

if ($user && in_array('administrator', $user->roles)) {
    ?>
    <div class="container">
        <div class="well text-center mvx-non-vendor-notice">
            <p><?php echo sprintf(__('You have logged in as Administrator. Please <a href="%s">log out</a> and then view this page.', 'multivendorx'), wc_logout_url()); ?></p>
        </div>
    </div>
    <?php
}