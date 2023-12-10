<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-new-account.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global  $MVX;

echo $email_heading . "\n\n";

echo sprintf( __( "Thanks for creating an account with %s. We have received your application for vendor registration. We will verify the information provided by you and inform you via email. Your username is %s.",  'multivendorx' ), $blogname, $user_login ) . "\n\n";

if ( get_option( 'woocommerce_registration_generate_password' ) === 'yes' && $password_generated )
	echo sprintf( __( "Your password is %s.",  'multivendorx' ), $user_pass ) . "\n\n";

echo sprintf( __( 'You can access your account area here: %s.',  'multivendorx' ), get_permalink( mvx_vendor_dashboard_page_id() ) ) . "\n\n";

echo "\n****************************************************\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
	echo esc_html(wp_strip_all_tags(wptexturize($additional_content)));
	echo "\n\n----------------------------------------\n\n";
}

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );