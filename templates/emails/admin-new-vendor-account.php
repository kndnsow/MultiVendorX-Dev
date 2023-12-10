<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/admin-new-vendor-account.php
 *
 * @author 		MultiVendorX
 * @package 	MultiVendorX/Templates
 * @version 	0.0.1
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly 
$vendor_application_admin_url = apply_filters('mvx_admin_new_vendor_email_vendor_application_url', admin_url('admin.php?page=mvx#&submenu=vendor&ID=' . $user_object->ID . '&name=vendor-application'));
?>
<?php do_action('woocommerce_email_header', $email_heading, $email); ?>

<p><?php printf(esc_html__("A user has recently applied to become a vendor on your platform %s.", 'multivendorx'), esc_html($blogname)); ?></p>

<p><?php printf(esc_html__("You can access and review the vendor application from %s.", 'multivendorx'), '<a href='.esc_url($vendor_application_admin_url).'><strong>here</strong></a>'); ?></p>

<?php do_action('mvx_email_footer'); ?>