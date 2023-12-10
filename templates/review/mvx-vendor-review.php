<?php
/**
 * Vendor Review Comments Lists Template

 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/review/mvx-vendor-review.php.
 *
 * 
 * @author 		MultiVendorX
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 3.3.5
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
global $MVX;
if(isset($reviews_lists) && count($reviews_lists) > 0) {
	foreach($reviews_lists as $reviews_list) {
		
		$MVX->template->get_template( 'review/review.php', array('comment' => $reviews_list, 'vendor_term_id'=> $vendor_term_id));
	}	
}?>
