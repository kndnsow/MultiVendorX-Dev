<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/new-coupon.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
if ( !defined( 'ABSPATH' ) ) exit; 
global  $MVX;

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Hi there! This is to notify that a new product has been submitted in %s.",  'multivendorx' ), get_option( 'blogname' ) ); 
echo '\n'; 
echo sprintf(  __( "Product title: %s",  'multivendorx' ), $coupon_name ); 
echo '\n'; 
echo sprintf(  __( "Submitted by: %s",  'multivendorx' ), $vendor_name ); 
echo '\n'; 
$coupon_link = apply_filters( 'mvx_email_vendor_new_coupon_link', esc_url( get_edit_post_link( $post_id )));
echo sprintf(  __( "Edit product: %s",  'multivendorx' ), $coupon_link ); 
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );