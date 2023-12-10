<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-account-deletion.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

global $MVX;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p><?php printf( esc_html__( "Hi %s,", 'multivendorx' ), $user_login ); ?></p>
<p>
    <?php _e( 'We wanted to let you know that your vendor profile has been deleted by the admin.', 'multivendorx' ); ?>
    <?php _e( 'If you have any questions or need further assistance, feel free to reach out.', 'multivendorx' ); ?>
</p>
<?php do_action( 'mvx_email_footer' );?>
