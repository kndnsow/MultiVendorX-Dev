<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/single-product/multiple-vendors-products-link.php.
 *
 * HOWEVER, on occasion MVX will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * 
 * @author 		MultiVendorX
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 2.3.4
 */
global $MVX, $product, $wpdb; 

$more_products = get_mvx_more_spmv_products( $product->get_id() );
if ( count( $more_products ) >= 1 ) {
    $button_text = apply_filters( 'mvx_more_vendors', __('More Vendors', 'multivendorx') );
    $button_text = apply_filters( 'mvx_single_product_more_vendors_text', $button_text, $product );
    echo '<a  href="#" class="goto_more_offer_tab button">' . esc_html($button_text) . '</a>';
}

