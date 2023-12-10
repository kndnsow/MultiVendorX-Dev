<?php

/**
 * The template for displaying single product page vendor tab 
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor_tab.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
global $MVX, $product;
$html = '';
$vendor = get_mvx_product_vendors($product->get_id());
if ($vendor) {
    $html .= '<div class="product-vendor">';
    $html .= apply_filters('mvx_before_seller_info_tab', '');
    $html .= '<h2>' . $vendor->page_title . '</h2>';
    echo $html;
    $term_vendor = wp_get_post_terms($product->get_id(), $MVX->taxonomy->taxonomy_name);
    if (!is_wp_error($term_vendor) && !empty($term_vendor)) {
        $rating_result_array = mvx_get_vendor_review_info($term_vendor[0]->term_id);
        if (get_mvx_vendor_settings('is_sellerreview_varified', 'general') == 'Enable') {
            $term_link = get_term_link($term_vendor[0]);
            $rating_result_array['shop_link'] = $term_link;
            echo '<div style="text-align:left; float:left;">';
            $MVX->template->get_template('review/rating-vendor-tab.php', array('rating_val_array' => $rating_result_array));
            echo "</div>";
            echo '<div style="clear:both; width:100%;"></div>';
        }
    }
    $html = '';
    if ('' != $vendor->description) {
        $html .= apply_filters('the_content', $vendor->description );
    }
    $html .= '<p><a href="' . $vendor->permalink . '">' . sprintf(__('More Products from %1$s', 'multivendorx'), $vendor->page_title) . '</a></p>';
    $html .= apply_filters('mvx_after_seller_info_tab', '');
    $html .= '</div>';
    echo $html;
    do_action('mvx_after_vendor_tab');
}
?>