<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-new-announcement.php
 *
 * @author 		MultiVendorX
 * @package     MultiVendorX/Templates
 * @version   0.0.1
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $MVX;

echo $email_heading . "\n\n";

echo sprintf( __('%s', 'multivendorx'),  $post_title) . "\n\n";

$announcement_link = esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_announcements_endpoint', 'seller_dashbaord', 'vendor-announcements')));

echo sprintf(__('This is to inform you that we recently updated the article %s :','multivendorx'), $post_title). "\n";

echo sprintf(__('Vendor Name: %s', 'multivendorx'), $vendor->page_title)."\n";

echo sprintf(apply_filters('mvx_announcement_content', $post_content))."\n";

echo sprintf( __('You can always check the changes from here  %s. We would request you to check the same and take the necessary action if required.', 'multivendorx'), $announcement_link ) . "\n";

printf( __( "View the announcement: %s",  'multivendorx' ), esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_announcements_endpoint', 'seller_dashbaord', 'vendor-announcements')))) . "\n";

printf( __('%s continued use of the Store, will be subject to the updated terms.', 'multivendorx'), $single );

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) ); 
