<?php

/**
 * Vendor List Map
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/shortcode/vendor-list/content-vendor.php
 *
 * @package MultiVendorX/Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $MVX, $vendor_list;
$vendor = get_mvx_vendor($vendor_id);
$image = $vendor->get_image() ? $vendor->get_image('image', array(125, 125)) : $MVX->plugin_url . 'assets/images/WP-stdavatar.png';
$rating_info = mvx_get_vendor_review_info($vendor->term_id);
$rating = round($rating_info['avg_rating'], 2);
$review_count = empty(intval($rating_info['total_rating'])) ? '' : intval($rating_info['total_rating']);
$vendor_phone = $vendor->phone ? $vendor->phone : __('No number yet', 'multivendorx');
$vendor_hide_address = get_user_meta($vendor_id, '_vendor_hide_address', true) ? get_user_meta($vendor_id, '_vendor_hide_address', true) : '';
?>
<div class="mvx-store-list mvx-store-list-vendor">
    <?php do_action('mvx_vendor_lists_single_before_image', $vendor->term_id, $vendor->id); ?>
    <div class="mvx-vendorblocks">
        <div class="mvx-vendor-details">
            <div class="vendor-heading">
                <div class="mvx-store-picture">
                    <img class="vendor_img" src="<?php echo esc_url($image); ?>" id="vendor_image_display">
                </div>
                <div class="vendor-header-icon">
                    <div class="dashicons dashicons-phone">
                        <div class="on-hover-cls">
                            <p><?php echo esc_html($vendor_phone); ?></p>
                        </div>
                    </div>
                    <div class="dashicons dashicons-location">
                        <div class="on-hover-cls">
                         <p><?php echo $vendor->get_formatted_address() ? $vendor->get_formatted_address() : __('No Address found', 'multivendorx'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mvx-vendor-name">
                <a href="<?php echo $vendor->get_permalink(); ?>" class="store-name"><?php echo esc_html($vendor->page_title); ?></a>
                <?php do_action('mvx_vendor_lists_single_after_button', $vendor->term_id, $vendor->id); ?>
                <?php do_action('mvx_vendor_lists_vendor_after_title', $vendor); ?>
            </div>
            <!-- star rating -->
            <?php
            $is_enable = mvx_seller_review_enable(absint($vendor->term_id));
            if (isset($is_enable) && $is_enable) {
            ?>
                <div class="mvx-rating-block extraCls">
                    <div class="mvx-rating-rate"><?php echo esc_html($rating); ?></div>
                    <?php
                    $MVX->template->get_template('review/rating_vendor_lists.php', array('rating_val_array' => $rating_info));
                    ?>
                    <div class="mvx-rating-review"><?php echo esc_html($review_count); ?></div>
                </div>
            <?php } ?>
            <!-- vendor description -->
            <div class="add-call-block">
                <div class="mvx-detail-block"></div>
                <div class="mvx-detail-block"></div>
                <?php if ($vendor_hide_address != 'Enable') {
                    if ($vendor && $vendor->country) : ?>
                        <div class="mvx-detail-block">
                            <i class="mvx-font ico-location-icon2" aria-hidden="true"></i>
                            <span class="descrptn_txt"><?php echo esc_html($vendor->country) . ', ' . esc_html($vendor->city); ?></span>
                        </div>
                    <?php endif;
                } ?>
            </div>
            <?php do_action('mvx_vendor_lists_vendor_top_products', $vendor); ?>
        </div>
    </div>
</div>