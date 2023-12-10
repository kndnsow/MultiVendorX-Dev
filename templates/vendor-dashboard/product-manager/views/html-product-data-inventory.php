<?php

/**
 * Inventory product tab template
 *
  * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/product-manager/views/html-product-data-inventory.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.3.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div role="tabpanel" class="tab-pane fade" id="inventory_product_data">
    <div class="row-padding"> 
        <?php if ( wc_product_sku_enabled() ) : ?> 
            <div class="form-group">
                <label class="control-label col-sm-3 col-md-3" for="_sku"><abbr title="<?php echo esc_attr__( 'Stock Keeping Unit', 'multivendorx' ); ?>">
                    <?php echo esc_html__( 'SKU', 'multivendorx' ); ?></abbr>
                    <span class="img_tip" data-desc="<?php esc_html_e( 'SKU refers to a Stock-keeping unit, a unique identifier for each distinct product and service that can be purchased.', 'multivendorx' ); ?>"></span>
                </label>
                <div class="col-md-6 col-sm-9">
                    <input class="form-control" type="text" id="_sku" name="_sku" value="<?php echo isset($_POST['_sku']) ? wc_clean($_POST['_sku']) : $product_object->get_sku( 'edit' ); ?>" />                    
                </div>
            </div> 
        <?php endif; ?>
        <?php do_action( 'mvx_frontend_product_options_sku', $post->ID, $product_object, $post ); ?>
        <?php if ( apply_filters( 'mvx_can_vendor_manage_stock', 'yes' === get_option( 'woocommerce_manage_stock' ) ) ) : ?> 
            <?php
            $manage_stock_visibility = apply_filters( 'inventory_tab_manage_stock_section', array( 'simple', 'variable' ) );
            if ( call_user_func_array( "mvx_is_allowed_product_type", $manage_stock_visibility ) ) :
                $manage_stock_classes = apply_filters( 'inventory_tab_manage_stock_class_list', implode( ' ', preg_filter( '/^/', 'show_if_', $manage_stock_visibility ) ) );
                ?>
                <div class="form-group-row <?php echo $manage_stock_classes; ?>"> 
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3" for="_manage_stock">
                            <?php esc_html_e( 'Manage stock?', 'multivendorx' ); ?>
                            <?php do_action( 'mvx_frontend_dashboard_product_options_stock_description' ); ?>
                        </label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="checkbox" id="_manage_stock" name="_manage_stock" value="yes"<?php checked( isset($_POST['_manage_stock']) && $_POST['_manage_stock'] == 'yes' ? true : $product_object->get_manage_stock( 'edit' ), true ); ?>/>
                            <span class="form-text"><?php esc_html_e( 'Enable stock management at product level', 'multivendorx' ); ?> 
                        </div>
                    </div>  
                </div>
                <?php do_action( 'mvx_frontend_dashboard_product_options_stock', $post->ID, $product_object, $post ); ?>
                <?php
                $stock_fields_visibility = apply_filters( 'inventory_tab_stock_fields_section', array( 'simple', 'variable' ) );
                if ( call_user_func_array( "mvx_is_allowed_product_type", $stock_fields_visibility ) ) :
                    $stock_fields_classes = apply_filters( 'inventory_tab_stock_fields_class_list', implode( ' ', preg_filter( '/^/', 'show_if_', $stock_fields_visibility ) ) );
                    ?>
                    <div class = "form-group-row stock_fields <?php echo $stock_fields_classes; ?>">
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-3" for="_stock">
                                <?php _e( 'Stock quantity', 'multivendorx' ); ?> 
                                <span class="img_tip" data-desc="<?php esc_html_e( 'Stock quantity. If this is a variable product this value will be used to control stock for all variations, unless you define stock at variation level.', 'multivendorx' ); ?>"></span>
                            </label>
                            <div class="col-md-6 col-sm-9">
                                <input class="form-control" type="text" id="_stock" name="_stock" value="<?php echo isset($_POST['_stock']) ? absint($_POST['_stock']) : wc_stock_amount( $product_object->get_stock_quantity( 'edit' ) ); ?>" /> 
                            </div>
                        </div>
                        <?php echo '<input type="hidden" name="_original_stock" value="' . esc_attr( wc_stock_amount( $product_object->get_stock_quantity( 'edit' ) ) ) . '" />'; ?>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-3" for="_backorders">
                                <?php _e( 'Allow backorders?', 'multivendorx' ); ?>
                                <span class="img_tip" data-desc="<?php esc_html_e( 'If managing stock, this controls whether or not backorders are allowed. If enabled, stock quantity can go below 0.', 'multivendorx' ); ?>"></span>
                            </label>
                            <div class="col-md-6 col-sm-9">
                                <select id="_backorders" name="_backorders" class="form-control">
                                    <?php foreach ( wc_get_product_backorder_options() as $key => $option ) : ?>
                                        <option value="<?php echo $key; ?>" <?php selected( isset($_POST['_backorders']) ? wc_clean($_POST['_backorders']) : $product_object->get_backorders( 'edit' ), $key ); ?>><?php echo $option; ?></option>
                                    <?php endforeach; ?>
                                </select> 
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-md-3" for="_low_stock_amount">
                                <?php _e( 'Low stock threshold', 'multivendorx' ); ?>
                                <span class="img_tip" data-desc="<?php esc_html_e( 'When product stock reaches this amount you will be notified by email', 'multivendorx' ); ?>"></span>
                            </label>
                            <div class="col-md-6 col-sm-9">
                                <input class="form-control" type="text" id="_low_stock_amount" name="_low_stock_amount" value="<?php echo isset($_POST['_low_stock_amount']) ? absint($_POST['_low_stock_amount']) : $product_object->get_low_stock_amount( 'edit' ); ?>" placeholder="<?php echo esc_attr(get_option( 'woocommerce_notify_low_stock_amount') ); ?>" /> 
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php do_action( 'mvx_frontend_dashboard_product_options_stock_fields', $post->ID, $product_object, $post ); ?>
            <?php
            $stock_status_invisibility = apply_filters( 'inventory_tab_stock_status_section_invisibility', array( 'variable', 'external' ) );
            $hide_classes = '';
            if ( call_user_func_array( "mvx_is_allowed_product_type", $stock_status_invisibility ) ) :
                $hide_classes = implode( ' ', preg_filter( '/^/', 'hide_if_', $stock_status_invisibility ) );
            endif; ?> 
            <div class="form-group-row stock_status_field <?php echo $hide_classes; ?>">
                <div class="form-group">
                    <label class="control-label col-sm-3 col-md-3" for="_stock_status"><?php _e( 'Stock status', 'multivendorx' ); ?></label>
                    <div class="col-md-6 col-sm-9">
                        <select id="_stock_status" name="_stock_status" class="form-control">
                            <?php foreach ( wc_get_product_stock_status_options() as $key => $option ) : ?>
                                <option value="<?php echo $key; ?>" <?php selected( isset($_POST['_stock_status']) ? wc_clean($_POST['_stock_status']) : $product_object->get_stock_status( 'edit' ), $key ); ?>><?php echo $option; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>    
            </div>
            <?php do_action( 'mvx_frontend_dashboard_product_options_stock_status', $post->ID, $product_object, $post ); ?> 
            
        <?php endif; ?> 
        <?php
        $sold_individually_visibility = apply_filters( 'inventory_tab_sold_individually_section', array( 'simple', 'variable' ) );
        if ( call_user_func_array( "mvx_is_allowed_product_type", $sold_individually_visibility ) ) :
            $show_classes = implode( ' ', preg_filter( '/^/', 'show_if_', $sold_individually_visibility ) );
            ?>
            <div class="form-group-row <?php echo $show_classes; ?>"> 
                <div class="form-group">
                    <label class="control-label col-sm-3 col-md-3" for="_sold_individually"><?php esc_html_e( 'Sold individually', 'multivendorx' ); ?></label>
                    <div class="col-md-6 col-sm-9">
                        <input class="form-control" type="checkbox" id="_sold_individually" name="_sold_individually" value="yes"<?php checked( isset($_POST['_sold_individually']) && $_POST['_sold_individually'] == 'yes' ? true : $product_object->get_sold_individually( 'edit' ), true ); ?>/>
                        <span class="form-text"><?php esc_html_e( 'Enable this to only allow one of this item to be bought in a single order', 'multivendorx' ); ?></span>
                    </div>
                </div> 
                <?php do_action( 'mvx_frontend_dashboard_product_options_sold_individually', $post->ID, $product_object, $post ); ?>
            </div>
        <?php endif; ?>
        <?php do_action( 'mvx_frontend_dashboard_after_inventory_section_end', $post->ID, $product_object, $post ); ?>
    </div>
    <?php do_action( 'mvx_frontend_dashboard_product_inventory_options', $post->ID, $product_object, $post ); ?>
</div>