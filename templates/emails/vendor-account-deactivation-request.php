<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-account-deactivation-request.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

global $MVX;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf( esc_html__( "Dear %s,", 'multivendorx' ), $admin_name ); ?></p>
<p>
    <?php printf( esc_html__( "Just a heads up, one of the vendors, %s, wants to delete their vendor profile. ", 'multivendorx' ), $user_login ); ?><br/>
    <?php printf( esc_html__( "Kindly assess this request at your earliest convenience and take further action from here: %s", 'multivendorx' ), get_admin_url() . 'admin.php?page=mvx#&submenu=work-board&name=activity-reminder' ); ?>
</p>
<?php do_action( 'mvx_email_footer' );?>
