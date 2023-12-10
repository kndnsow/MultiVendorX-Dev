<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-followed.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   	3.7
 */

if ( !defined( 'ABSPATH' ) ) exit; 

if($post->post_type == 'shop_coupon') $title = esc_html__( 'Coupon', 'multivendorx' );
else  $title = esc_html__( 'Product', 'multivendorx' );

$vendor = get_mvx_vendor( $post->post_author );
$product = wc_get_product( $post->ID );
$product_link = $product ? $product->get_permalink() : '';
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php printf( esc_html__( "Hi there! This is a notification about a new %s on %s.",  'multivendorx' ), $title, get_option( 'blogname' ) ); ?></p>
	<p>
		<?php 
        if ($product_link) {
            printf( esc_html__( "%s title: %s",  'multivendorx' ), $title, $product_link ); 
        }
        ?><br/>
		<?php printf( esc_html__( "Submitted by: %s",  'multivendorx' ), $vendor->page_title ); ?><br/><br/>
	</p>

<?php do_action( 'mvx_email_footer' ); ?>