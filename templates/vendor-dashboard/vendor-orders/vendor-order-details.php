<?php
/**
 * The template for displaying vendor order detail and called from vendor_order_item.php template
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-orders/vendor-order-details.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly    
    exit;
}
global $woocommerce, $MVX;
$vendor = get_current_vendor();
$order = wc_get_order($order_id);
if (!$order || !is_mvx_vendor_order($order, apply_filters( 'mvx_current_vendor_order_capability' ,true ))) {
    ?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <?php _e('Invalid order', 'multivendorx'); ?>
        </div>
    </div>
    <?php
    return;
}
// Get the payment gateway
$payment_gateway = wc_get_payment_gateway_by_order( $order );
$vendor_order = mvx_get_order($order_id);
$vendor_shipping_method = get_mvx_vendor_order_shipping_method($order->get_id(), $vendor->id);
$subtotal = 0;
$disallow_vendor_order_status = get_mvx_vendor_settings('disallow_vendor_order_status', 'order') && !empty(get_mvx_vendor_settings('disallow_vendor_order_status', 'order')) ? true : false;
?>
<div id="mvx-order-details" class="col-md-12">
    <div class="panel panel-default panel-pading pannel-outer-heading mt-0 order-detail-top-panel">
        <div class="panel-heading d-flex clearfix">
            <h3 class="pull-left">
                <?php 
                /* translators: 1: order type 2: order number */
                printf(
                        esc_html__( 'Order details #%1$s', 'multivendorx' ),
                        esc_html( $order->get_order_number() )
                ); ?>
                <input type="hidden" id="order_ID" value="<?php echo $order->get_id(); ?>" />
            </h3>
            <div class="change-status d-flex">
                <div class="order-status-text pull-left <?php echo 'wc-' . $order->get_status( 'edit' ); ?>">
                    <i class="mvx-font ico-pendingpayment-status-icon"></i>
                    <span class="order_status_lbl"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
                </div>
                <?php if( $order->get_status( 'edit' ) != 'cancelled' && !$disallow_vendor_order_status ) : ?>
                <div class="dropdown-order-statuses dropdown pull-left clearfix">
                    <span class="order-status-edit-button pull-left dropdown-toggle" data-toggle="dropdown"><u><?php _e( 'Edit', 'multivendorx' ); ?></u></span>
                    <input type="hidden" id="order_current_status" value="<?php echo 'wc-' . $order->get_status( 'edit' ); ?>" />
                    <ul id="order_status" class="dropdown-menu dropdown-menu-right" style="margin-top:9px;">
                            <?php
                            $statuses = apply_filters( 'mvx_vendor_order_statuses', wc_get_order_statuses(), $order );
                            foreach ( $statuses as $status => $status_name ) {
                                    echo '<li class="dropdown-item"><a href="javascript:void(0);" data-status="' . esc_attr( $status ) . '" ' . selected( $status, 'wc-' . $order->get_status( 'edit' ), false ) . '>' . esc_html( $status_name ) . '</a></li>';
                            }
                            ?>
                    </ul>   
                </div>   
                <?php endif; ?>
            </div>
        </div>
        <?php
        $MVX->template->get_template( 'vendor-dashboard/vendor-orders/views/html-order-info.php', array( 'order' => $order, 'vendor_order' => $vendor_order, 'vendor' => $vendor ) );
        ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $MVX->template->get_template( 'vendor-dashboard/vendor-orders/views/html-order-items.php', array( 'order' => $order, 'vendor_order' => $vendor_order, 'vendor' => $vendor ) );
            ?>
        </div>
        
        <div class="col-md-8">
            <!-- Downloadable product permissions -->
            <?php
            $MVX->template->get_template( 'vendor-dashboard/vendor-orders/views/html-order-downloadable-permissions.php', array( 'order' => $order, 'vendor_order' => $vendor_order, 'vendor' => $vendor ) );
            ?>
            <!-- Customer refund request -->
            <?php
            do_action('mvx_action_before_customer_refund_request', $order, $vendor_order, $vendor);
            if( apply_filters( 'mvx_vendor_refund_capability' ,true ) ){
                $MVX->template->get_template( 'vendor-dashboard/vendor-orders/views/html-order-refund-customer.php', array( 'order' => $order, 'vendor_order' => $vendor_order, 'vendor' => $vendor ) );
            }
            do_action('mvx_action_after_customer_refund_request', $order, $vendor_order, $vendor);
            ?>
        </div>
        
        <div class="col-md-4">
            <?php
            $MVX->template->get_template( 'vendor-dashboard/vendor-orders/views/html-order-notes.php', array( 'order' => $order, 'vendor_order' => $vendor_order, 'vendor' => $vendor ) );
            do_action('mvx_action_after_order_notes', $order, $vendor_order, $vendor);
            ?>
        </div>
        
    </div>
</div>


