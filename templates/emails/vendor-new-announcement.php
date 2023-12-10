<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-new-announcement.php
 *
 * @author 		MultiVendorX
 * @package     MultiVendorX/Templates
 * @version   0.0.1
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly 
global $MVX;
do_action( 'woocommerce_email_header', $email_heading, $email );
$text_align = is_rtl() ? 'right' : 'left';
?>

<p><?php printf(esc_html__('%s', 'multivendorx'),  $post_title); ?></p>

<?php $announcement_link = esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_announcements_endpoint', 'seller_dashbaord', 'vendor-announcements'))); ?>

<p><?php printf(esc_html__('This is to inform you that we recently updated the article %s :', 'multivendorx'), $post_title); ?></p>

<p><?php printf(esc_html__('Vendor Name: %s', 'multivendorx'), $vendor->page_title); ?></p>

<?php printf(apply_filters('mvx_announcement_content', $post_content)); ?>

<p><?php printf(esc_html__('You can always check the changes from here  %s. We would request you to check the same and take the necessary action if required.', 'multivendorx'), $announcement_link ); ?></p>

<p><?php printf(esc_html__('%s continued use of the Store, will be subject to the updated terms.', 'multivendorx'), $single); ?></p>

<?php do_action('mvx_email_footer'); ?>