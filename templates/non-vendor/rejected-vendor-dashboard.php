<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/non-vendor/rejected-vendor-dashboard.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     3.1.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
echo '<div class="col-md-12 text-center"><div class="panel mvx-rejected-vendor-notice">' . apply_filters( 'mvx_rejected_vendor_dashboard_message', __('We have reviewed your application. Unfortunately, you are not the right fit with us at this time.', 'multivendorx') ) . '</div></div>';
$mvx_vendor_rejection_notes = unserialize( get_user_meta( get_current_user_id(), 'mvx_vendor_rejection_notes', true ) );

if(is_array($mvx_vendor_rejection_notes) && count($mvx_vendor_rejection_notes) > 0) {
	echo '<div class="col-md-12"><div class="panel panel-default pannel-outer-heading"><div class="panel-heading d-flex"><h3>' . __('Notes from our reviewer', 'multivendorx') . '</h3></div>';
	echo '<div class="panel-body panel-content-padding"><div class="note-clm-wrap">';
	foreach($mvx_vendor_rejection_notes as $time => $notes) {
		echo '<div class="note-clm"><p class="note-description">' . $notes['note'] . '</p><p class="note_time note-meta">On ' . date( "Y-m-d", $time ) . '</p></div>';
	}
	echo '</div></div></div></div>';
}

echo '<div class="mvx-action-container"><a class="btn btn-default" href="' . esc_url(mvx_get_vendor_dashboard_endpoint_url('rejected-vendor-reapply')) . '">' . __('Resubmit Application', 'multivendorx') . '</a></div>';



		
	
	
	
	
