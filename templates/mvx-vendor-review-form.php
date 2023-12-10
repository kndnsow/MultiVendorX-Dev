<?php
/**
 * The template for displaying Seller Review form 
 *
 * Override this template by copying it to yourtheme/MultiVendorX/mvx-vendor-review-form.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     3.7
 */
global $MVX, $wpdb;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$vendor_term_id = get_user_meta($vendor_id, '_vendor_term_id', true);
$vendor = get_mvx_vendor_by_term($vendor_term_id);
$shop_name = $vendor->page_title;
$vendor_id = $vendor->id;
$count = $vendor->get_review_count();
$is_enable = mvx_seller_review_enable($vendor_term_id);
$current_user = wp_get_current_user();
$reviews_lists = $vendor->get_reviews_and_rating(0);
// Multi review
$review_options_data = mvx_get_option('mvx_review_management_tab_settings');
$mvx_review_categories = isset($review_options_data['mvx_review_categories']) && !empty(array_column($review_options_data['mvx_review_categories'], 'category')) ? array_column($review_options_data['mvx_review_categories'], 'category') : array();
$is_start_with_full_rating = apply_filters('mvx_is_start_with_full_rating', false);
?>
<div class="wocommerce" >
    <div id="reviews" >
        <div id="mvx_vendor_reviews">
            <?php if (isset($is_enable) && $is_enable) { ?>
                <?php
                if (!is_customer_not_given_review_to_vendor( $vendor_id, get_current_user_id())) {
                    ?>
                    <div class="woocommerce-info">
                        <?php esc_html_e('You have already reviewed this vendor', 'multivendorx'); ?>
                    </div>
                    <?php
                }
                ?>
                <div id="review_form_wrapper">
                    <div id="review_form">
                        <div id="respond" class="comment-respond">
                            <?php if ($vendor->id != get_current_vendor_id() && is_customer_not_given_review_to_vendor( $vendor_id, get_current_user_id()) && mvx_seller_review_enable($vendor_term_id) && apply_filters('customer_can_share_review_only_once', true)) : ?>
                                <h3 id="reply-title" class="comment-reply-title"><?php
                                    if ($count == 0) {
                                        echo sprintf(__('Be the first to review “%s”', 'multivendorx'), $shop_name);
                                    } else {
                                        echo sprintf(__('Add a review to “%s”', 'multivendorx'), $shop_name);
                                    }
                                    ?></h3>                
                                <form action="" method="post" id="commentform" class="comment-form" novalidate="">
                                    <p id="mvx_seller_review_rating"></p>
                                    <p class="comment-form-rating"><label for="rating"><?php esc_html_e('Your Rating', 'multivendorx'); ?></label>
                                    <div class="mvx-star-rating-content">
                                        <?php if ($mvx_review_categories) { ?>
                                            <?php foreach( $mvx_review_categories as $mvx_review_cat_key => $mvx_review_category ) { ?>
                                            <div class="mvx-star-rating-heading">
                                                <select name="rating" id="rating">
                                                    <option value=""><?php esc_html_e('Rate...', 'multivendorx'); ?></option>
                                                    <option value="5"><?php esc_html_e('Perfect', 'multivendorx'); ?></option>
                                                    <option value="4"><?php esc_html_e('Good', 'multivendorx'); ?></option>
                                                    <option value="3"><?php esc_html_e('Average', 'multivendorx'); ?></option>
                                                    <option value="2"><?php esc_html_e('Not that bad', 'multivendorx'); ?></option>
                                                    <option value="1"><?php esc_html_e('Very Poor', 'multivendorx'); ?></option>
                                                </select>
                                                <span><span class="rating_text <?php echo $mvx_review_category ?>"><?php if( $is_start_with_full_rating ) { echo '5'; } else { echo '0'; } ?></span>.0 <?php esc_html_e( $mvx_review_category, 'multivendorx' ); ?></span>
                                                <input type="hidden" class="rating_value" name="mvx_store_review_category[<?php echo $mvx_review_cat_key; ?>]" value="<?php if( $is_start_with_full_rating ) { echo '5'; } else { echo '0'; } ?>" />
                                            </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                        <select name="rating" id="rating">
                                            <option value=""><?php esc_html_e('Rate...', 'multivendorx'); ?></option>
                                            <option value="5"><?php esc_html_e('Perfect', 'multivendorx'); ?></option>
                                            <option value="4"><?php esc_html_e('Good', 'multivendorx'); ?></option>
                                            <option value="3"><?php esc_html_e('Average', 'multivendorx'); ?></option>
                                            <option value="2"><?php esc_html_e('Not that bad', 'multivendorx'); ?></option>
                                            <option value="1"><?php esc_html_e('Very Poor', 'multivendorx'); ?></option>
                                       </select>
                                        <?php } ?>
                                    </div>
                                    </p>
                                    <p class="comment-form-comment">
                                        <label for="comment"><?php esc_html_e('Your Review', 'multivendorx'); ?> </label>
                                        <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
                                    </p>                    
                                    <p class="form-submit">
                                        <input id="mvx_vendor_for_rating" name="mvx_vendor_for_rating" type="hidden" value="<?php echo esc_attr($vendor_id); ?>"  >
                                        <input id="author" name="author" type="hidden" value="<?php echo esc_attr($current_user->display_name); ?>" size="30" aria-required="true">                  
                                        <input id="email" name="email" type="hidden" value="<?php echo esc_attr($current_user->user_email); ?>" size="30" aria-required="true">
                                        <input name="submit" type="button" id="submit" class="submit" value="<?php esc_attr_e('Submit', 'multivendorx') ?>">

                                    </p>                
                                </form>
                                <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            <div id="comments">
                <?php
                if ($count > 0) {
                    $start = 0;
                    $posts_per_page = get_option('posts_per_page');
                    $total_pages = ceil($count / $posts_per_page);
                    ?>
                    <h2><?php printf(_n('%s review for %s', '%s reviews for %s', $count, 'multivendorx'), $count, $shop_name); ?>    </h2>
                    <form id="vendor_review_rating_pagi_form" >
                        <input type="hidden" name="pageno" id="mvx_review_rating_pageno" value="1" >
                        <input type="hidden" name="postperpage" id="mvx_review_rating_postperpage" value="<?php echo esc_attr($posts_per_page); ?>" >
                        <input type="hidden" name="totalpage" id="mvx_review_rating_totalpage" value="<?php echo esc_attr($total_pages); ?>" >
                        <input type="hidden" name="totalreview" id="mvx_review_rating_totalreview" value="<?php echo esc_attr($count); ?>" >   
                        <input type="hidden" name="term_id" id="mvx_review_rating_term_id" value = "<?php echo esc_attr($vendor_term_id); ?>">
                    </form>
                    <?php
                    if (isset($reviews_lists) && count($reviews_lists) > 0) {
                        echo '<ol class="commentlist vendor_comment_list">';
                        $MVX->template->get_template('review/mvx-vendor-review.php', array('reviews_lists' => $reviews_lists, 'vendor_term_id' => $vendor_term_id));
                        echo '</ol>';
                        if ($total_pages > 1) {
                            echo '<div class="mvx_review_loader"><img src="' . $MVX->plugin_url . 'assets/images/ajax-loader.gif" alt="ajax-loader" /></div>';
                            echo '<input name="loadmore" type="button" id="mvx_review_load_more" class="submit mvx_load_more" style="float:right;" value="' . esc_attr('Load More', 'multivendorx') . '">';
                        }
                    }
                } elseif ($count == 0) {
                    ?>
                    <p class="woocommerce-noreviews"><?php esc_html_e('There are no reviews yet.', 'multivendorx'); ?> </p>
                <?php } ?>
            </div>  
            <div class="clear"></div>

        </div>
    </div>
</div>
