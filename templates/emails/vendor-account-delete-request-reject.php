<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-account-delete-request-reject.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

global $MVX;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf( esc_html__( "Dear %s,", 'multivendorx' ), $user_login ); ?></p>
<p>
    <?php _e( 'We regret to inform you that your request to delete your vendor profile has been rejected.', 'multivendorx' ); ?>
    <?php printf( esc_html__( "If you have any questions or concerns, please reach out to us over %s", 'multivendorx' ), $admin_email ); ?>
</p>
<?php do_action( 'mvx_email_footer' );?>
