<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vemdor-followed.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   	0.0.1
 */
 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Hi there! This is to notify that a new product has been submitted in %s.",  'multivendorx' ), get_option( 'blogname' ) );

if($post->post_type == 'product') {
	$product = wc_get_product( $post->ID );
	$product_link = $product ? $product->get_permalink() : '';
	$title = esc_html__( 'Product', 'multivendorx' );
	echo sprintf( __( "A new product is created %s: %s",  'multivendorx' ), $title, $product_link ) . "\n\n";
	echo '\n';
} else {
	esc_html_e('A new coupon is created:', 'multivendorx');
	echo '\n';
}
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );
?>