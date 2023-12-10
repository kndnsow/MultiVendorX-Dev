<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/product-rejected.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; 
global  $MVX;

$title = esc_html__( 'Product', 'multivendorx' );
	
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( esc_html__( "Hi there! This is a notification about a %s rejection on %s.",  'multivendorx' ), $title, get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( esc_html__( "%s title: %s",  'multivendorx' ), $title, $product_name ); ?><br/>
		<?php printf( esc_html__( "Submitted by: %s",  'multivendorx' ), $vendor_name ); ?><br/>
		<?php printf( esc_html__( "Rejection Note: %s",  'multivendorx' ), $reason ); ?><br/>
		<?php 
                $product_link = apply_filters( 'mvx_email_vendor_rejected_product_link', esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product'), $post_id)));
                printf( esc_html__( "Edit %s: %s",  'multivendorx' ), $title, $product_link ); ?>
		<br/>
	</p>

<?php do_action( 'mvx_email_footer' ); ?>