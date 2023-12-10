<?php

/**
 * Attribute product tab template
 *
  * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/product-manager/views/html-product-data-attributes.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.3.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div role="tabpanel" class="tab-pane fade collapsable-component-wrapper" id="product_attributes_data">
    <div class="row-padding">
        <div class="row">
            <div class="col-md-6">
                <div class="add-variation-wrapper">
                    <select name="attribute_taxonomy" class="attribute_taxonomy form-control inline-select">
                        <?php if ( apply_filters( 'vendor_can_add_custom_attribute', true ) ) : ?>
                            <option value=""><?php esc_html_e( 'Custom product attribute', 'multivendorx' ); ?></option>
                        <?php endif; ?>
                        <?php
                        // Array of defined attribute taxonomies
                        $attribute_taxonomies = wc_get_attribute_taxonomies();

                        if ( ! empty( $attribute_taxonomies ) ) {
                            foreach ( $attribute_taxonomies as $tax ) {
                                $attribute_taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );
                                $label = $tax->attribute_label ? $tax->attribute_label : $tax->attribute_name;
                                echo '<option value="' . esc_attr( $attribute_taxonomy_name ) . '">' . esc_html( $label ) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <button type="button" class="btn btn-default add_attribute"><?php esc_html_e( 'Add', 'multivendorx' ); ?></button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="toolbar float-right">
                    <span class="expand-close">
                        <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'multivendorx' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'multivendorx' ); ?></a>
                    </span>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="product_attributes wc-metaboxes product-variations-wrapper">  
                    <?php
                    // Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
                    $attributes = isset( $_POST['wc_attributes'] ) ? mvx_woo()->prepare_attributes( $_POST['wc_attributes'] ) : $product_object->get_attributes( 'edit' );
                    $i = -1;
                    foreach ( $attributes as $attribute ) {
                        $i ++;
                        $metabox_class = array();

                        if ( $attribute->is_taxonomy() ) {
                            $metabox_class[] = 'taxonomy';
                            $metabox_class[] = $attribute->get_name();
                        }

                        include( 'html-product-attribute.php' );
                    }
                    ?>
                </div>
            </div>
        </div> 
        <div class="button-group">
            <button type="button" class="btn btn-default save_attributes button-primary"><?php esc_html_e( 'Save attributes', 'multivendorx' ); ?></button>
            <div class="toolbar float-right">
                <span class="expand-close">
                    <a href="#" class="expand_all"><?php esc_html_e( 'Expand', 'multivendorx' ); ?></a> / <a href="#" class="close_all"><?php esc_html_e( 'Close', 'multivendorx' ); ?></a>
                </span>
            </div>
        </div>
    </div>
    <?php do_action( 'mvx_frontend_dashboard_product_options_attribute', $post->ID, $product_object, $post ); ?>
</div>