<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-new-account.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
global  $MVX;
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( "Thanks for creating an account on %s. We will process your application and revert shortly.",  'multivendorx' ), esc_html( $blogname ), esc_html( $user_login ) ); ?></p>
<?php if ( get_option( 'woocommerce_registration_generate_password' ) == 'yes' && $password_generated ) : ?>
<p><?php printf( esc_html__( "Your password has been automatically generated: %s",  'multivendorx' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>
<?php endif; ?>
<p><?php printf( esc_html__( 'You can access your account area here: %s.',  'multivendorx' ), get_permalink( mvx_vendor_dashboard_page_id() ) ); ?></p>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
	echo wp_kses_post(wpautop(wptexturize($additional_content)));
} ?>

<?php do_action( 'mvx_email_footer' ); ?>