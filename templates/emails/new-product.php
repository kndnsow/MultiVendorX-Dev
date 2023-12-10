<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/new-product.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; 
global  $MVX;

if($post_type == 'shop_coupon') $title = esc_html__( 'Coupon', 'multivendorx' );
else  $title = esc_html__( 'Product', 'multivendorx' );
	
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( esc_html__( "Hi there! This is a notification about a new %s on %s.",  'multivendorx' ), $title, get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( esc_html__( "%s title: %s",  'multivendorx' ), $title, $product_name ); ?><br/>
		<?php printf( esc_html__( "Submitted by: %s",  'multivendorx' ), $vendor_name ); ?><br/>
		<?php 
                $product_link = apply_filters( 'mvx_email_vendor_new_product_link', esc_url( get_edit_post_link( $post_id )));
                printf( esc_html__( "Edit %s: %s",  'multivendorx' ), $title, $product_link ); ?>
		<br/>
	</p>

<?php do_action( 'mvx_email_footer' ); ?>