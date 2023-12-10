<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/plugin-deactivated-mail.php
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
echo sprintf( __("Hi %s, We are sorry to see that you have deactivated MultiVendorX.", 'multivendorx' ), $user_login );
echo '\n';
echo _e('As we always look out for our users, even though they are not using our plugin, can you tell us why you deactivate them?', 'multivendorx');
echo '\n';
echo _e('Maybe we can help set up the plugin or assist you in any way possible.', 'multivendorx');
echo '\n';
echo _e('Hope to hear from you soon.', 'multivendorx');
echo '\n';
echo _e('Have a great day.', 'multivendorx');

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );

?>