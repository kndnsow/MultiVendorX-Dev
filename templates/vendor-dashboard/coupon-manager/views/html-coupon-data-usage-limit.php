<?php

/**
 * Data usages coupon tab template
 *
 * Used by add-coupon.php template
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/vendor-dashboard/coupon-manager/views/html-coupon-data-usage-limit.php.
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
?>
<div role="tabpanel" class="tab-pane fade" id="usage_limit_coupon_data">
    <div class="row-padding">
        <?php do_action( 'mvx_frontend_dashboard_before_usage_limit_coupon', $post->ID, $coupon ); ?>
        <div class="form-group-row"> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="usage_limit">
                    <?php esc_html_e( 'Usage limit per coupon', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'How many times this coupon can be used before it is void.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="number" id="usage_limit" name="usage_limit" class="form-control" value="<?php echo isset($_POST['usage_limit']) ? absint($_POST['usage_limit']) : esc_attr( $coupon->get_usage_limit( 'edit' ) ? $coupon->get_usage_limit( 'edit' ) : '' ); ?>" placeholder="<?php esc_attr_e( 'Unlimited usage', 'multivendorx' ); ?>" step="1" min="0">
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="limit_usage_to_x_items">
                    <?php esc_html_e( 'Limit usage to X items', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="number" id="limit_usage_to_x_items" name="limit_usage_to_x_items" class="form-control" value="<?php echo isset($_POST['limit_usage_to_x_items']) ? esc_attr($_POST['limit_usage_to_x_items']) : (esc_attr( $coupon->get_limit_usage_to_x_items( 'edit' ) ? $coupon->get_limit_usage_to_x_items( 'edit' ) : '' )); ?>" placeholder="<?php esc_attr_e( 'Apply to all qualifying items in cart', 'multivendorx' ); ?>" step="1" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="usage_limit_per_user">
                    <?php esc_html_e( 'Usage limit per user', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'How many times this coupon can be used by an individual user. Uses billing email for guests, and user ID for logged in users.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input type="number" id="usage_limit_per_user" name="usage_limit_per_user" class="form-control" value="<?php echo isset($_POST['usage_limit_per_user']) ? absint($_POST['usage_limit_per_user']) : (esc_attr( $coupon->get_usage_limit_per_user( 'edit' ) ? $coupon->get_usage_limit_per_user( 'edit' ) : '' )); ?>" placeholder="<?php esc_attr_e( 'Unlimited usage', 'multivendorx' ); ?>" step="1" min="0">
                </div>
            </div>
        </div>
        <?php do_action( 'mvx_frontend_dashboard_after_usage_limit_coupon', $post->ID, $coupon ); ?>
    </div>
</div>