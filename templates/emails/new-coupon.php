<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/new-coupon.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; 

$title = esc_html__( 'Coupon', 'multivendorx' );
	
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( esc_html__( "Hi there! This is a notification about a new %s on %s.",  'multivendorx' ), $title, get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( esc_html__( "%s title: %s",  'multivendorx' ), $title, $coupon_name ); ?><br/>
		<?php printf( esc_html__( "Submitted by: %s",  'multivendorx' ), $vendor_name ); ?><br/>
		<?php 
                $coupon_link = apply_filters( 'mvx_email_vendor_new_coupon_link', esc_url( get_edit_post_link( $post_id )));
                printf( esc_html__( "Edit %s: %s",  'multivendorx' ), $title, $coupon_link ); ?>
		<br/>
	</p>

<?php do_action( 'mvx_email_footer' ); ?>