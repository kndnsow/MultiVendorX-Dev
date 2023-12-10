<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/suspend-vendor-account.php
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

echo sprintf( __("Hello %s,", 'multivendorx' ), $user_login );

echo sprintf( __("'We are sorry to inform you that your following account with us on %s has been suspended.", 'multivendorx' ), get_option( 'blogname' ) );
echo '\n';

echo sprintf( __( "Username: %s", 'multivendorx' ), $user_login );
echo '\n';
echo _e( "Status: Suspended", 'multivendorx' ); 
echo '\n';
echo sprintf( __( "Login URL: %s", 'multivendorx' ), mvx_get_vendor_dashboard_endpoint_url( 'dashboard' ) ); 
echo '\n';
echo _e('Kindly contact your Administrator for further details.', 'multivendorx', 'multivendorx');

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );

?>