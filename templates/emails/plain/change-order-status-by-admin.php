<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/change-order-status-by-admin.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   	3.7.2
 */

if ( !defined( 'ABSPATH' ) ) exit; 
global $MVX;


echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Hi there! This is to notify that an order #%s status has been changed on %s.",  'multivendorx' ), $order_id, get_option( 'blogname' ) );
echo '\n'; 

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );