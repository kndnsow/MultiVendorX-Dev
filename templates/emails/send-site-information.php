<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/send-site-information.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

global $MVX;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf( esc_html__( "Hi %s, thanks you for sharing the details of your site.", 'multivendorx' ), $user_login ); ?></p>
<p>
	<?php _e( "As a token of gesture, we are offering you 10% discount code on our Pro package : https://multivendorx.com/pricing", 'multivendorx' ); ?><br/>
    <?php _e( "This offer is valid for 2 days. Grab your offer now!!", 'multivendorx' ); ?><br/>
</p>
<?php do_action( 'mvx_email_footer' );?>