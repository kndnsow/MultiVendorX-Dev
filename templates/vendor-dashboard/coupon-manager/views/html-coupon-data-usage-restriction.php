<?php

/**
 * Usage restriction coupon tab template
 *
 * Used by add-coupon.php template
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/vendor-dashboard/coupon-manager/views/html-coupon-data-usage-restriction.php.
 *
 * HOWEVER, on occasion MVX will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/templates/vendor dashboard/coupon manager/views
 * @version     3.3.0
 */
defined( 'ABSPATH' ) || exit;
global $wpdb;
$vendor = apply_filters( 'mvx_vendor_select_product_for_add_coupon' , get_mvx_vendor( get_current_user_id() ) );
?>
<div role="tabpanel" class="tab-pane fade" id="usage_restriction_coupon_data">
    <div class="row-padding">
        <?php do_action( 'mvx_frontend_dashboard_before_usage_restriction_coupon', $post->ID, $coupon ); ?>
        <div class="form-group-row"> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="minimum_amount">
                    <?php esc_html_e( 'Minimum spend', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'This field allows you to set the minimum spend (subtotal) allowed to use the coupon.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="text" id="minimum_amount" name="minimum_amount" class="form-control" value="<?php echo isset($_POST['minimum_amount']) ? absint($_POST['minimum_amount']) : esc_attr( $coupon->get_minimum_amount( 'edit' ) ); ?>" placeholder="<?php esc_attr_e( 'No minimum', 'multivendorx' ); ?>">
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="maximum_amount">
                    <?php esc_html_e( 'Maximum spend', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'This field allows you to set the maximum spend (subtotal) allowed when using the coupon.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="text" id="maximum_amount" name="maximum_amount" class="form-control" value="<?php echo isset($_POST['maximum_amount']) ? absint($_POST['maximum_amount']) : esc_attr( $coupon->get_maximum_amount( 'edit' ) ); ?>" placeholder="<?php esc_attr_e( 'No maximum', 'multivendorx' ); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="individual_use">
                    <?php esc_html_e( 'Individual use only', 'multivendorx' ); ?>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="checkbox" id="individual_use" name="individual_use" class="form-control" value="yes" <?php checked( wc_bool_to_string( isset($_POST['individual_use']) ? wc_clean($_POST['individual_use']) : $coupon->get_individual_use( 'edit' ) ), 'yes' ); ?>>
                    <span class="form-text"><?php esc_html_e( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'multivendorx' ); ?></span>
                </div>
            </div> 
            <div class="form-group ">
                <label class="control-label col-sm-3 col-md-3" for="exclude_sale_items">
                    <?php esc_html_e( 'Exclude sale items', 'multivendorx' ); ?>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="checkbox" id="exclude_sale_items" name="exclude_sale_items" class="form-control" value="yes" <?php checked( wc_bool_to_string( isset($_POST['exclude_sale_items']) ? wc_clean($_POST['exclude_sale_items']) : $coupon->get_exclude_sale_items( 'edit' ) ), 'yes' ); ?>>
                    <span class="form-text"><?php esc_html_e( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are items in the cart that are not on sale.', 'multivendorx' ); ?></span>
                </div>
            </div> 
        </div>
        <div class="form-group-row">
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3">
                    <?php esc_html_e( 'Products', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'Products that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9 coupon-products-wrap">
                    <select id="products" class="form-control wc-enhanced-select" multiple="multiple" name="product_ids[]" data-placeholder="<?php esc_attr_e( 'Any products', 'multivendorx' ); ?>">
                        <?php
                        $clause['where'] = " AND ".$wpdb->prefix."posts.post_status = 'publish' OR {$wpdb->prefix}posts.post_type = 'product_variation' AND ".$wpdb->prefix."posts.post_author = ". $vendor->id;
                        $vendor_product_ids = wp_list_pluck( $vendor->get_products_ids($clause), 'ID' );
                        $product_ids = isset($_POST['product_ids']) ? array_filter(wc_clean($_POST['product_ids'])) : $coupon->get_product_ids( 'edit' );
                        foreach ( $vendor_product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                echo '<option value="' . esc_attr( $product_id ) . '"' . wc_selected( $product_id, $product_ids ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <button type="button" class="button plus btn btn-secondary select_all_attributes"><?php esc_html_e( 'Select all', 'multivendorx' ); ?></button>
                    <button type="button" class="button minus btn btn-secondary select_no_attributes"><?php esc_html_e( 'Select none', 'multivendorx' ); ?></button>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3">
                    <?php esc_html_e( 'Exclude products', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <select id="exclude_products" class="form-control wc-enhanced-select" multiple="multiple" name="exclude_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'multivendorx' ); ?>">
                        <?php
                        $product_ids = isset($_POST['exclude_product_ids']) ? array_filter(wc_clean($_POST['exclude_product_ids'])) : $coupon->get_excluded_product_ids( 'edit' );
                        $vendor_product_ids = wp_list_pluck( $vendor->get_products_ids(), 'ID' );
                        foreach ( $vendor_product_ids as $product_id ) {
                            $product = wc_get_product( $product_id );
                            if ( is_object( $product ) ) {
                                echo '<option value="' . esc_attr( $product_id ) . '"' . wc_selected( $product_id, $product_ids ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group-row">
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="product_categories">
                    <?php esc_html_e( 'Product categories', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'Product categories that the coupon will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <select class="form-control wc-enhanced-select" multiple="multiple" id="product_categories" name="product_categories[]" data-placeholder="<?php esc_attr_e( 'Any category', 'multivendorx' ); ?>">
                        <?php
                        $category_ids = isset($_POST['product_categories']) ? array_filter(wc_clean($_POST['product_categories'])) : $coupon->get_product_categories( 'edit' );
                        $categories = get_terms( 'product_cat', apply_filters('mvx_edit_coupon_product_categories', array(
                                'orderby'   => 'name',
                                'hide_empty'=> 0
                            ) ) );

                        if ( $categories ) {
                            foreach ( $categories as $cat ) {
                                echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="exclude_product_categories">
                    <?php esc_html_e( 'Exclude categories', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'Product categories that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <select class="form-control wc-enhanced-select" multiple="multiple" id="exclude_product_categories" name="exclude_product_categories[]" data-placeholder="<?php esc_attr_e( 'No categories', 'multivendorx' ); ?>">
                        <?php
                        $category_ids = isset($_POST['exclude_product_categories']) ? array_filter(wc_clean($_POST['exclude_product_categories'])) : $coupon->get_excluded_product_categories( 'edit' );
                        $categories = get_terms( 'product_cat', apply_filters('mvx_edit_coupon_exclude_product_categories', array(
                                'orderby'   => 'name',
                                'hide_empty'=> 0
                            ) ) );

                        if ( $categories ) {
                            foreach ( $categories as $cat ) {
                                echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div> 
        <div class="form-group">
            <label class="control-label col-sm-3 col-md-3" for="customer_email">
                <?php esc_html_e( 'Allowed emails', 'multivendorx' ); ?>
                <span class="img_tip" data-desc="<?php esc_html_e( 'Whitelist of billing emails to check against when an order is placed. Separate email addresses with commas. You can also use an asterisk (*) to match parts of an email. For example "*@gmail.com" would match all gmail addresses.', 'multivendorx' ); ?>"></span>
            </label>
            <div class="col-md-6 col-sm-9">
                <input type="email" id="customer_email" name="customer_email" class="form-control" value="<?php echo isset($_POST['customer_email']) ? wc_clean($_POST['customer_email']) : esc_attr( implode( ', ', (array) $coupon->get_email_restrictions( 'edit' ) ) ); ?>" placeholder="<?php esc_attr_e( 'No restrictions', 'multivendorx' ); ?>" multiple="multiple">
            </div>
        </div> 
        <?php do_action( 'mvx_frontend_dashboard_after_usage_restriction_coupon', $post->ID, $coupon ); ?>
    </div>
</div>