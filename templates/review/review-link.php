<?php
/**
 * Vendor Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/review/review-link.php.
 *
 * HOWEVER, on occasion Multivendor X will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
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
if(isset($review_data) && is_array($review_data)) {
$rating = 0;	
$review_data_final = apply_filters('mvx_review_link_final_filter',$review_data);
?>
<div class="review_link_data_wappers">
<a target="_blank" class="button" href="<?php echo $review_data_final['vendor_review_link']; ?>"><?php echo __('Leave Vendor feedback','multivendorx'); ?></a> 
<a href="<?php echo $review_data_final['vendor_review_link']; ?>" target="_blank"><div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Leave Vendor feedback', 'multivendorx' ) ) ?>">
		<span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'multivendorx' ); ?></span>
	</div></a>
</div>
<?php }?>


