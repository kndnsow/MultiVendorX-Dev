<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/admin-new-vendor-account.php
 *
 * @author 		MultiVendorX
 * @package 	MultiVendorX/Templates
 * @version 	0.0.1
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly 
$vendor_application_admin_url = apply_filters('mvx_admin_new_vendor_email_vendor_application_url', admin_url('admin.php?page=mvx#&submenu=vendor&ID=' . $user_object->ID . '&name=vendor-application'));

echo "= " . $email_heading . " =\n\n";

echo sprintf(__("A user has recently applied to become a vendor on your platform %s.", 'multivendorx'), esc_html($blogname));

echo sprintf(__('You can access and review the vendor application from %s.', 'multivendorx'), '<a href='.esc_url($vendor_application_admin_url).'><strong>here</strong></a>') . "\n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters('mvx_email_footer_text', get_option('mvx_email_footer_text'));