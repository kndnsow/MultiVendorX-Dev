<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $MVX;
$product      = $item->get_product();
$edit_product_link = ($product) ? esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'vseller_dashbaord', 'edit-product'), $product->get_id())) : '';
$product_link = ( $product && current_vendor_can( 'edit_product' ) ) ? esc_url($edit_product_link) : '';
$thumbnail    = ($product) ? apply_filters( 'mvx_vendor_dash_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
$row_class    = apply_filters( 'mvx_vendor_dash_html_order_item_class', ! empty( $class ) ? $class : '', $item, $order );
?>
<tr class="item <?php echo esc_attr( $row_class ); ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
    <td class="order-thumb">
        <?php echo '<div class="order-item-img">' . wp_kses_post( $thumbnail ) . '</div>'; ?>
    </td>
    <td>
        <div class="order-item-detail">
            <p class="primary-color">
                <?php echo $product_link ? '<a href="' . esc_url( $product_link ) . '" class="mvx-order-item-name">' . wp_kses_post( $item->get_name() ) . '</a>' : wp_kses_post( $item->get_name() ); ?>
            </p>
            <input type="hidden" class="order_item_id" name="order_item_id[]" value="<?php echo esc_attr( $item_id ); ?>" />
            <input type="hidden" name="order_item_tax_class[<?php echo absint( $item_id ); ?>]" value="<?php echo esc_attr( $item->get_tax_class() ); ?>" />
            <table>
                <?php 
                if ( $product && $product->get_sku() ) {
                    echo '<tr><th>' . esc_html__( 'SKU:', 'multivendorx' ) . '</th><td>' . esc_html( $product->get_sku() ) . '</td></tr>';
                }

                if ( $item->get_variation_id() ) {
                    echo '<tr><th>' . esc_html__( 'Variation ID:', 'multivendorx' ) . '</th><td>';
                    if ( 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
                            echo esc_html( $item->get_variation_id() );
                    } else {
                            /* translators: %s: variation id */
                            printf( esc_html__( '%s (No longer exists)', 'multivendorx' ), $item->get_variation_id() );
                    }
                    echo '</td></tr>';
                }
                ?>
                <?php do_action( 'mvx_vendor_dash_before_order_itemmeta', $item_id, $item, $product ); ?>
                <?php require 'html-order-item-meta.php'; ?>
                <?php do_action( 'mvx_vendor_dash_after_order_itemmeta', $item_id, $item, $product ); ?>
            </table>
            <?php do_action( 'mvx_vendor_dash_after_order_itemmeta_table', $item_id, $item, $product ); ?>
        </div>
    </td>

    <?php do_action( 'mvx_vendor_dash_order_item_values', $product, $item, absint( $item_id ) ); ?>

    <td class="item_cost" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, false, true ) ); ?>">
            <div class="view">
                    <?php
                            echo wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) );

                    if ( $item->get_subtotal() !== $item->get_total() ) {
                            echo '<span class="wc-order-item-discount">-' . wc_price( wc_format_decimal( $order->get_item_subtotal( $item, false, false ) - $order->get_item_total( $item, false, false ), '' ), array( 'currency' => $order->get_currency() ) ) . '</span>';
                    }
                    ?>
            </div>
    </td>
    <td class="quantity" width="1%">
            <div class="view">
                    <?php
                            echo '<small class="times">&times;</small> ' . esc_html( $item->get_quantity() );

                    if ( $refunded_qty = $order->get_qty_refunded_for_item( $item_id ) ) {
                            echo '<small class="refunded">-' . ( $refunded_qty * -1 ) . '</small>';
                    }
                    ?>
            </div>
            <div class="edit" style="display: none;">
                    <input type="number" step="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_step', '1', $product ) ); ?>" min="0" autocomplete="off" name="order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" value="<?php echo esc_attr( $item->get_quantity() ); ?>" data-qty="<?php echo esc_attr( $item->get_quantity() ); ?>" size="4" class="form-control quantity form-control" />
            </div>
            <div class="refund" style="display: none;">
                    <input type="number" step="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_step', '1', $product ) ); ?>" min="0" max="<?php echo absint( $item->get_quantity() ); ?>" autocomplete="off" name="refund_order_item_qty[<?php echo absint( $item_id ); ?>]" placeholder="0" size="4" class="form-control refund_order_item_qty form-control" />
            </div>
    </td>
    <td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( $item->get_total() ); ?>">
            <div class="view">
                    <?php
                    echo wc_price( $item->get_subtotal(), array( 'currency' => $order->get_currency() ) );

                    if ( $item->get_subtotal() !== $item->get_total() ) {
                            echo '<span class="wc-order-item-discount">-' . wc_price( wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ), array( 'currency' => $order->get_currency() ) ) . '</span>';
                    }

                    if ( $refunded = $order->get_total_refunded_for_item( $item_id ) ) {
                            echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
                    }
                    ?>
            </div>
            <div>
                <?php
                if ( $item->get_subtotal() !== $item->get_total() ) {
                        echo '<span class="wc-order-item-discount">' . wc_price( wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ), array( 'currency' => $order->get_currency() ) ) . '</span> '. __('discount', 'multivendorx');
                }
                ?>
            </div>
            <div class="edit" style="display: none;">
                    <div class="split-input">
                            <div class="input">
                                    <label><?php esc_attr_e( 'Pre-discount:', 'multivendorx' ); ?></label>
                                    <input type="text" name="line_subtotal[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->get_subtotal() ) ); ?>" class="form-control line_subtotal wc_input_price" data-subtotal="<?php echo esc_attr( wc_format_localized_price( $item->get_subtotal() ) ); ?>" />
                            </div>
                            <div class="input">
                                    <label><?php esc_attr_e( 'Total:', 'multivendorx' ); ?></label>
                                    <input type="text" name="line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->get_total() ) ); ?>" class="form-control line_total wc_input_price" data-tip="<?php esc_attr_e( 'After pre-tax discounts.', 'multivendorx' ); ?>" data-total="<?php echo esc_attr( wc_format_localized_price( $item->get_total() ) ); ?>" />
                            </div>
                    </div>
            </div>
            <div class="refund" style="display: none;">
                    <input type="text" name="refund_line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="form-control refund_line_total wc_input_price" />
            </div>
    </td>

    <?php
    if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
            foreach ( $order_taxes as $tax_item ) {
                    $tax_item_id       = $tax_item->get_rate_id();
                    $tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
                    $tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';
                    ?>
                    <td class="line_tax" width="1%">
                            <div class="view">
                                    <?php
                                    if ( '' !== $tax_item_total ) {
                                            echo wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) );
                                    } else {
                                            echo '&ndash;';
                                    }

                                    if ( $item->get_subtotal() !== $item->get_total() ) {
                                            if ( '' === $tax_item_total ) {
                                                    echo '<span class="wc-order-item-discount">&ndash;</span>';
                                            } else {
                                                    echo '<span class="wc-order-item-discount">-' . wc_price( wc_round_tax_total( $tax_item_subtotal - $tax_item_total ), array( 'currency' => $order->get_currency() ) ) . '</span>';
                                            }
                                    }

                                    if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id ) ) {
                                            echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
                                    }
                                    ?>
                            </div>
                            <div class="edit" style="display: none;">
                                    <div class="split-input">
                                            <div class="input">
                                                    <label><?php esc_attr_e( 'Pre-discount:', 'multivendorx' ); ?></label>
                                                    <input type="text" name="line_subtotal_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" class="form-control line_subtotal_tax wc_input_price" data-subtotal_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_subtotal ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
                                            </div>
                                            <div class="input">
                                                    <label><?php esc_attr_e( 'Total:', 'multivendorx' ); ?></label>
                                                    <input type="text" name="line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" class="form-control line_tax wc_input_price" data-total_tax="<?php echo esc_attr( wc_format_localized_price( $tax_item_total ) ); ?>" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
                                            </div>
                                    </div>
                            </div>
                            <div class="refund" style="display: none;">
                                    <input type="text" name="refund_line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="form-control refund_line_tax wc_input_price" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
                            </div>
                    </td>
                    <?php
            }
    }
    ?>
    <td class="mvx-item-commission">
        <div class="view">
        <?php $commission = $item->get_meta('_vendor_item_commission', true);
        echo '<div class="commission">' . wc_price($commission) . '</div>'; 
        
        if ( $refunded_commission = mvx_get_total_refunded_for_item( $item_id, $order->get_id() ) ) {
            echo '<small class="refunded">' . wc_price( $refunded_commission, array( 'currency' => $order->get_currency() ) ) . '</small>';
        }
        ?>
        </div>
    </td>
</tr>