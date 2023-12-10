<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/suspend-vendor-account.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
global $MVX;
//
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__('Hello %s,', 'multivendorx'), $user_login ); ?> <p>

<p><?php printf( esc_html__('We are sorry to inform you that your following account with us on %s has been suspended.', 'multivendorx'), get_option( 'blogname' ) ); ?> <p>
<p>
	<?php printf( esc_html__( "Username: %s",  'multivendorx' ), $user_login ); ?><br/>
	<?php _e( "Status: Suspended",  'multivendorx' ); ?><br/>
	<?php printf( esc_html__( "Login URL: %s",  'multivendorx' ), mvx_get_vendor_dashboard_endpoint_url( 'dashboard' ) ); ?><br/>
	
</p>
<p><?php _e('Kindly contact your Administrator for further details. ', 'multivendorx'); ?> <p>
<?php do_action( 'mvx_email_footer' );?>