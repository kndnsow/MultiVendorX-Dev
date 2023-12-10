<?php

/**
 * Advanced product tab template
 *
  * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/product-manager/views/html-product-data-advanced.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.3.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div role="tabpanel" class="tab-pane fade" id="advanced_product_data">
    <div class="row-padding"> 
        <div class="hide_if_external hide_if_grouped">
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="_purchase_note"><?php esc_html_e( 'Purchase note', 'multivendorx' ); ?></label>
                <div class="col-md-6 col-sm-9">
                    <textarea id="_purchase_note" name="_purchase_note" class="form-control"><?php echo isset($_POST['_purchase_note']) ? wc_clean($_POST['_purchase_note']) : esc_html( $product_object->get_purchase_note( 'edit' ) ); ?></textarea>
                </div>
            </div> 
        </div> 
        <div class="form-group">
            <label class="control-label col-sm-3 col-md-3" for="menu_order"><?php esc_html_e( 'Menu order', 'multivendorx' ); ?></label>
            <div class="col-md-6 col-sm-9">
                <input id="menu_order" name="menu_order" type="number" class="form-control" value="<?php echo isset($_POST['menu_order']) ? absint($_POST['menu_order']) : esc_attr( $product_object->get_menu_order( 'edit' ) ); ?>" step="1">
            </div>
        </div> 

        <?php if ( post_type_supports( 'product', 'comments' ) ) : ?> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="comment_status"><?php esc_html_e( 'Enable reviews', 'multivendorx' ); ?></label>
                <div class="col-md-6 col-sm-9">
                    <input id="comment_status" name="comment_status" type="checkbox" class="form-control" value="<?php echo esc_attr('open'); ?>" <?php checked( $product_object->get_reviews_allowed( 'edit' ), true ); ?>>
                </div>
            </div> 
        <?php endif; ?>

        <?php do_action( 'mvx_frontend_dashboard_product_options_advanced', $post->ID, $product_object, $post ); ?>
    </div>
</div>