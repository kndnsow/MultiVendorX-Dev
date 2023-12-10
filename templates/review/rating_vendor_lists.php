<?php
/**
 * Vendor Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/review/rating_vendor_lists.php.
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
$rating   = round( $rating_val_array['avg_rating'],2 );
$count = intval( $rating_val_array['total_rating'] );

?>
<div class="mvx_rating_wrap">
<?php if($count > 0) {?>
	<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" style="float:none;"  title="<?php echo sprintf( __( 'Rated %s out of 5', 'multivendorx' ), $rating ) ?>">
		<span style="width:<?php echo ( $rating_val_array['avg_rating'] / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'multivendorx' ); ?></span>
	</div>
<?php }else {?>
	<div><?php echo __('No Rating Yet','multivendorx'); ?></div>
<?php }?>
</div>
