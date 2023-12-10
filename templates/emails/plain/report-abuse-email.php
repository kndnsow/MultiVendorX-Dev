<?php
/**
 * The template for displaying report abuse via customer.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/report-abuse-email.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.3.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;
$name = isset( $object['name'] ) ? $object['name'] : '';
$from_email = isset( $object['email'] ) ? $object['email'] : '';
$user_message = isset( $object['msg'] ) ? $object['msg'] : '';
$product = wc_get_product( absint( $object['product_id'] ) );
echo $email_heading . "\n\n"; 
$message = sprintf(__("User %s (%s) is reporting an abuse on the following product: \n", 'multivendorx'), $name, $from_email);
$message .= sprintf(__("Product details: %s (ID: #%s) \n", 'multivendorx'), $product->get_title(), $product->get_id());

$message .= sprintf(__("Vendor shop: %s \n", 'multivendorx'), $vendor->page_title);

$message .= sprintf(__("Message: %s\n", 'multivendorx'), $user_message);
$message .= "\n\n\n";

$message .= sprintf(__("Product page:: %s\n", 'multivendorx'), get_the_permalink($product->get_id));

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );