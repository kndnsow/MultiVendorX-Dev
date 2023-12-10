<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-bank-commissions-transaction.php
 *
 * @author 		MultiVendorX
 * @package     MultiVendorX/Templates
 * @version     0.0.1
 */
 
global $MVX;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

echo "= " . $email_heading . " =\n\n";

echo __( "Your commission has been paid", 'multivendorx' );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );