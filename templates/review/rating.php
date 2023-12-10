<?php
/**
 * Vendor Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/review/rating.php.
 *
 * HOWEVER, on occasion Multivendor X will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * 
 * @author 		MultiVendorX
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 3.7
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $MVX;
$rating = round($rating_val_array['avg_rating'], 1);
$count = intval($rating_val_array['total_rating']);
$rating_type = $rating_val_array['rating_type'];
$rating_url = $MVX->frontend->mvx_get_review_url( mvx_find_shop_page_vendor() );
if( $rating_type == 'product-rating' ) {
    $review_text = $count > 1 ? __('Products reviews', 'multivendorx') : __('Product review', 'multivendorx');
} else {
    $review_text = $count > 1 ? __('Reviews', 'multivendorx') : __('Review', 'multivendorx');
}

?> 
<div style="clear:both; width:100%;"></div> 
<?php if ($count > 0) { ?>
    <span class="mvx_total_rating_number"><?php echo esc_html(sprintf(' %s ', $rating)); ?></span>
<?php } ?>
<?php if ( apply_filters( 'mvx_load_default_vendor_store', false ) ) { ?>
        <a href="#<?php echo ($rating_type != 'product-rating' ) ? 'reviews' : ''; ?>">
<?php } else { ?>
    <a href="<?php echo ($rating_type != 'product-rating' ) ? esc_url($rating_url) : ''; ?>">
<?php } ?>
<?php if ($count > 0) { ?>  
        <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf(__('Rated %s out of 5', 'multivendorx'), $rating) ?>">
            <span style="width:<?php echo ( round($rating_val_array['avg_rating']) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo esc_html($rating); ?></strong> <?php esc_html_e('out of 5', 'multivendorx'); ?></span>
        </span>
        <?php echo esc_html(sprintf(' %s %s', $count, $review_text)); ?>

    <?php
} else {
    ?>
        <?php echo __(' No Review Yet ', 'multivendorx'); ?>
    <?php } ?>
</a>
