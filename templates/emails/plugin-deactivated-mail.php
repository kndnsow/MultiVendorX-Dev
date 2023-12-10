<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plugin-deactivated-mail.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

global $MVX;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf( esc_html__( "Hi %s, We are sorry to see that you have deactivated MultiVendorX.", 'multivendorx' ), $user_login ); ?></p>
<p>
	<?php _e( "As we always look out for our users, even though they are not using our plugin, can you tell us why you deactivate them?", 'multivendorx' ); ?><br/>
    <?php _e( "Maybe we can help set up the plugin or assist you in any way possible.", 'multivendorx' ); ?><br/>
    <?php _e( "Hope to hear from you soon.", 'multivendorx' ); ?><br/>
    <?php _e( "Have a great day.", 'multivendorx' ); ?>
</p>
<?php do_action( 'mvx_email_footer' );?>