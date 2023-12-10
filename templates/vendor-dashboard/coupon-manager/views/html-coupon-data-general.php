<?php

/**
 * General coupon tab template
 *
 * Used by add-coupon.php template
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/vendor-dashboard/coupon-manager/views/html-coupon-data-general.php.
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
<div role="tabpanel" class="tab-pane fade active in" id="general_coupon_data">
    <div class="row-padding">
        <?php do_action( 'mvx_frontend_dashboard_before_general_coupon', $post->ID, $coupon ); ?>
        <div class="form-group-row"> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="discount_type"><?php esc_html_e( 'Discount type', 'multivendorx' ); ?></label>
                <div class="col-md-6 col-sm-9">
                    <select class="form-control" id="discount_type" name="discount_type">
                        <?php
                        $coupon_types = wc_get_coupon_types();
                        $coupon_type = isset($_POST['discount_type']) ? wc_clean($_POST['discount_type']) : $coupon->get_discount_type( 'edit' );
                        foreach ( $coupon_types as $key => $value ) {
                            echo '<option value="' . $key . '" ' . selected( $coupon_type, $key, false ) . '>' . $value . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="coupon_amount">
                    <?php esc_html_e( 'Coupon amount', 'multivendorx' ); ?>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'Value of the coupon.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input id="coupon_amount" name="coupon_amount" value="<?php echo isset($_POST['coupon_amount']) ? wc_clean($_POST['coupon_amount']) : esc_attr( $coupon->get_amount( 'edit' ) ); ?>" type="text" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="form-control">
                </div>
            </div> 
            <?php
            // Free Shipping.
            if ( wc_shipping_enabled() ) :
                ?>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-md-3" for="free_shipping"><?php esc_html_e( 'Allow free shipping', 'multivendorx' ); ?></label>
                    <div class="col-md-6 col-sm-9">
                        <input id="free_shipping" name="free_shipping" type="checkbox" class="form-control" value="yes" <?php checked( wc_bool_to_string( isset($_POST['free_shipping']) ? 'yes' : $coupon->get_free_shipping( 'edit' ) ), 'yes' ); ?>>
                        <span class="form-text"><?php echo sprintf( __( 'Check this box if the coupon grants free shipping. A <a href="%s" target="_blank">free shipping method</a> must be enabled in your shipping zone and be set to require "a valid free shipping coupon" (see the "Free Shipping Requires" setting).', 'multivendorx' ), 'https://docs.woocommerce.com/document/free-shipping/' ); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <?php
            // Expiry date.
            $expiry_date = $coupon->get_date_expires( 'edit' ) ? $coupon->get_date_expires( 'edit' )->date( 'Y-m-d' ) : '';
            ?>
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="expiry_date"><?php esc_html_e( 'Coupon expiry date', 'multivendorx' ); ?></label>
                <div class="col-md-6 col-sm-9">
                    <input class="form-control" id="expiry_date" name="expiry_date" value="<?php echo isset($_POST['expiry_date']) ? wc_clean($_POST['expiry_date']) : esc_attr( $expiry_date ); ?>" type="date" placeholder="<?php esc_attr_e( 'YYYY-MM-DD', 'multivendorx' ); ?>" pattern="<?php echo esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ); ?>">
                </div>
            </div>
        </div>
        <?php do_action( 'mvx_frontend_after_general_coupon_data', $post->ID, $coupon ); ?>
    </div>
</div>