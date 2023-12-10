<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/approved-vendor-account.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
global $MVX;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf( esc_html__( "Congratulations! Your vendor application on %s has been approved!", 'multivendorx' ), get_option( 'blogname' ) ); ?></p>
<p>
	<?php _e( "Application status: Approved",  'multivendorx' ); ?><br/>
	<?php printf( esc_html__( "Applicant Username: %s",  'multivendorx' ), $user_login ); ?>
</p>
<p><?php _e('You have been cleared for landing! Congratulations and welcome aboard!', 'multivendorx') ?> <p>
<?php do_action( 'mvx_email_footer' );?>