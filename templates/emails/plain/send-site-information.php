<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/send-site-information.php
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
echo sprintf( __("Hi %s, thanks you for sharing the details of your site.", 'multivendorx' ), $user_login );
echo '\n';
echo _e('As a token of gesture, we are offering you 10% discount code on our Pro package : https://multivendorx.com/pricing', 'multivendorx');
echo '\n';
echo _e('This offer is valid for 2 days. Grab your offer now!!', 'multivendorx');

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );

?>