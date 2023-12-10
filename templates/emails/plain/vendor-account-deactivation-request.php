<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-account-deactivation-request.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;

echo "= " . $email_heading . " =\n\n";
echo sprintf( __("Dear %s,", 'multivendorx' ), $admin_name );
echo '\n';
echo sprintf( __('Just a heads up, one of the vendors, %s, wants to delete their vendor profile. ', 'multivendorx'), $user_login );
echo '\n';
echo sprintf( __('Kindly assess this request at your earliest convenience and take further action from here: %s', 'multivendorx'), get_admin_url() . 'admin.php?page=mvx#&submenu=work-board&name=activity-reminder' );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );

?>