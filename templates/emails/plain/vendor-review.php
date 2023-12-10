<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-review.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;
$review = isset( $review ) ? $review : '';
$rating = isset( $rating ) ? absint($rating) : 0;
echo $email_heading . "\n"; 
echo "****************************************\n\n";
echo __( "Hi,",  'multivendorx' ); 
echo "\n\n";
echo sprintf(  __( "A new Review has been added by - %s",  'multivendorx' ), $customer_name );
echo "\n"; 
echo sprintf(  __( "Vendor Name : %s",  'multivendorx' ), $vendor->page_title );
echo "\n";
	if(!empty($rating)){
		echo sprintf(  __( "Rating : %s out of 5",  'multivendorx' ), $rating );
echo "\n";
	}
echo sprintf(  __( "Comment : %s",  'multivendorx' ), $review );
echo "\n";

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );