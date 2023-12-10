<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-bank-commissions-transaction.php
 *
 * @author      MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
global $MVX;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php echo esc_html__( "Your commission has been paid", 'multivendorx' ); ?></p>

<?php do_action( 'mvx_email_footer' ); ?>