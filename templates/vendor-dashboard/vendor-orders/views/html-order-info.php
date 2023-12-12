<?php
/**
 * Order details information template.
 *
 * Used by vendor-order-details.php template
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-orders/views/html-order-info.php.
 * 
 * @author 		MultiVendorX
 * @package MultiVendorX/templates/vendor dashboard/vendor orders/views
 * @version     3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $MVX;

if ( WC()->payment_gateways() ) {
    $payment_gateways = WC()->payment_gateways->payment_gateways();
} else {
    $payment_gateways = array();
}
$address = get_post_meta(wp_get_post_parent_id($order->get_id()), '_mvx_user_location', true) ? get_post_meta(wp_get_post_parent_id($order->get_id()), '_mvx_user_location', true) : '';
$lat = get_post_meta(wp_get_post_parent_id($order->get_id()), '_mvx_user_location_lat', true) ? get_post_meta(wp_get_post_parent_id($order->get_id()), '_mvx_user_location_lat', true) : '';
$lng = get_post_meta(wp_get_post_parent_id($order->get_id()), '_mvx_user_location_lng', true) ? get_post_meta(wp_get_post_parent_id($order->get_id()), '_mvx_user_location_lng', true) : '';
?>
<div class="panel-body panel-content-padding top-order-note">
    <div class="vorder-info-top-left pull-left">
        <table>
            <?php do_action( 'mvx_vendor_dash_order_details_before_top_left', $order, $vendor ); ?>
            <tr>
                <th><?php esc_html_e( 'Order date', 'multivendorx' ); ?> :</th>
                <td><?php echo esc_html( $order->get_date_created()->date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ); ?></td>
            </tr>
            <?php $payment_method = $order->get_payment_method(); 
            if( $payment_method ) : ?>
            <tr>
                <th><?php esc_html_e( 'Payment method', 'multivendorx' ); ?> :</th>
                <td>
                    <?php 
                    /* translators: %s: payment method */
                    printf(
                        __( 'Payment via <u><strong>%s</strong></u>', 'multivendorx' ),
                        esc_html( isset( $payment_gateways[ $payment_method ] ) ? $payment_gateways[ $payment_method ]->get_title() : $payment_method )
                    );
                    ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($address) { ?>
                <tr>
                    <th><?php esc_html_e( 'Delivery location from map', 'multivendorx' ); ?> :</th>
                    <td>
                        <?php
                        $address = '<a href="https://google.com/maps/place/' . rawurlencode( $address ) . '/@' . $lat . ',' . $lng . '" target="_blank">' . esc_html($address) . '</a>';
                        echo wp_kses_post($address);
                        ?>
                    </td>
                </tr>
            <?php } ?>
            <?php do_action( 'mvx_vendor_dash_order_details_after_top_left', $order, $vendor ); ?>
        </table>
    </div>
    <div class="vorder-info-top-right pull-right mt-10">
        <?php do_action( 'mvx_vendor_dash_order_details_top_right', $order, $vendor ); ?>
        <!--a href="#" class="btn btn-default btn-outline">PDF invoice</a>
        <a href="#" class="btn btn-default btn-outline">PDF package slip</a-->
    </div>
</div>
<div class="panel-body panel-content-padding order-address-info">
    <div class="row">
        <?php if( apply_filters( 'is_vendor_can_see_order_billing_address', true, $vendor->id, $order ) ) : ?>
        <div class="col-md-4">
            <div class="border">
                <h3><?php esc_html_e( 'Billing address', 'multivendorx' ); ?></h3>
                <?php 
                // Display values.
                if ( $order->get_formatted_billing_address() ) {
                        echo '<p>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
                } else {
                        echo '<p class="none_set"><strong>' . __( 'Address:', 'multivendorx' ) . '</strong> ' . __( 'No billing address set.', 'multivendorx' ) . '</p>';
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if( apply_filters( 'is_vendor_can_see_order_shipping_address', true, $vendor->id, $order ) ) : ?>
        <div class="col-md-4">
            <div class="border">
                <h3><?php esc_html_e( 'Shipping address', 'multivendorx' ); ?></h3>
                <?php 
                // Display values.
                if ( $order->get_formatted_shipping_address() ) {
                        echo '<p>' . wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
                } else {
                        echo '<p class="none_set"><strong>' . __( 'Address:', 'multivendorx' ) . '</strong> ' . __( 'No shipping address set.', 'multivendorx' ) . '</p>';
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if( apply_filters( 'is_vendor_can_see_customer_details', true, $vendor->id, $order ) ) : ?>
        <div class="col-md-4">
            <div class="border">
                <h3><?php esc_html_e( 'Customer detail', 'multivendorx' ); ?></h3>
                <div class="customer-detail">
                    <?php 
                    $user = $user_id = '';
                    if ( $order->get_user_id() ) {
                        $user_id = absint( $order->get_user_id() );
                        $user    = get_user_by( 'id', $user_id );  
                    }
                    ?>
                    <div class="icon">
                        <?php echo $user ? get_avatar( $user->ID, 48 ) : ''; ?>
                    </div>
                    <div class="detail-contnt">
                        <p><?php echo $user ? $user->display_name : ''; ?></p>
                        <?php $billing_fields = apply_filters( 'mvx_vendor_dash_customer_details', array(
                            'email' => array( 'label' => __( 'Email address', 'multivendorx' ) ),
                            'phone' => array( 'label' => __( 'Phone', 'multivendorx' ) )
                        ) );
                        foreach ( $billing_fields as $key => $field ) {
                            if ( isset( $field['show'] ) && false === $field['show'] ) {
                                    continue;
                            }

                            $field_name = 'billing_' . $key;

                            if ( isset( $field['value'] ) ) {
                                    $field_value = $field['value'];
                            } elseif ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
                                    $field_value = $order->{"get_$field_name"}( 'edit' );
                            } else {
                                    $field_value = $order->get_meta( '_' . $field_name );
                            }

                            if ( 'billing_phone' === $field_name ) {
                                    $field_value = wc_make_phone_clickable( $field_value );
                            } else {
                                    $field_value = make_clickable( esc_html( $field_value ) );
                            }

                            if ( $field_value ) {
                                    echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . wp_kses_post( $field_value ) . '</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
                if( $order->get_customer_note() ) :
                ?>
                <hr>
                <h3><?php esc_html_e( 'Customer provided note:', 'multivendorx' ); ?></h3>
                <div class="order_note">
                    <?php
                    $order_customer_note = $order->get_customer_note();
                    if ( apply_filters( 'mvx_vendor_order_customer_notes_enabled', 'yes' == get_option( 'woocommerce_enable_order_comments', 'yes' ) ) && $order_customer_note ) {
                        echo nl2br( $order_customer_note );
                    }
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>