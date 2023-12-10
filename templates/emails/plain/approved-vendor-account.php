<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/approved-vendor-account.php
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
echo sprintf( __("Congratulations! Your vendor application on %s has been approved!", 'multivendorx' ), get_option( 'blogname' ) );
echo '\n';
echo sprintf( __( "Application Status: %s", 'multivendorx' ), 'Approved' );
echo '\n';
echo sprintf( __( "Applicant Username: %s", 'multivendorx' ), $user_login ); 
echo '\n';
echo _e('You have been cleared for landing! Congratulations and welcome aboard!', 'multivendorx');

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );

?>