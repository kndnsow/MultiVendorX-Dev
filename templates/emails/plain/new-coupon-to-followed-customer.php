<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/new-coupon-to-followed-customer.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
if ( !defined( 'ABSPATH' ) ) exit; 

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Hi there! This is to notify that a new coupon has been added in %s.",  'multivendorx' ), get_option( 'blogname' ) ); 
echo '\n'; 
echo sprintf(  __( "Coupon code: %s",  'multivendorx' ), $coupon_name ); 
echo '\n'; 
echo sprintf(  __( "Added by: %s",  'multivendorx' ), $vendor_name ); 
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );