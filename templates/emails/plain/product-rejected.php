<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/product-rejected.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
if ( !defined( 'ABSPATH' ) ) exit; 
global  $MVX;

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Hi there! This is to notify that a product has been rejected in %s.",  'multivendorx' ), get_option( 'blogname' ) ); 
echo '\n'; 
echo sprintf(  __( "Product title: %s",  'multivendorx' ), $product_name ); 
echo '\n'; 
echo sprintf(  __( "Submitted by: %s",  'multivendorx' ), $vendor_name ); 
echo '\n';
echo sprintf(  __( "Rejection Note: %s",  'multivendorx' ), $reason ); 
echo '\n'; 
$product_link = apply_filters( 'mvx_email_vendor_rejected_product_link', esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product'), $post_id)));
echo sprintf(  __( "Edit product: %s",  'multivendorx' ), $product_link ); 
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );

?>