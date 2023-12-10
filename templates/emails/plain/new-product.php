<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/new-product.php
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
echo sprintf(  __( "Product title: %s",  'multivendorx' ), $product_name ); 
echo '\n'; 
echo sprintf(  __( "Submitted by: %s",  'multivendorx' ), $vendor_name ); 
echo '\n'; 
$product_link = apply_filters( 'mvx_email_vendor_new_product_link', esc_url( get_edit_post_link( $post_id )));
echo sprintf(  __( "Edit product: %s",  'multivendorx' ), $product_link ); 
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );

?>