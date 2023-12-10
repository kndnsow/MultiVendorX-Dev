<?php
/**
 * The template for displaying report abuse via customer.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/customer-order-refund-request.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $MVX;
$text_align = is_rtl() ? 'right' : 'left';

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<div style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px;">
    <?php if( $user_type != 'customer' ) { ?>
		<h2><?php esc_html_e( 'Refund details', 'multivendorx' ); ?></h2>
		<ul>
		<li><strong><?php _e( 'Order ID', 'multivendorx' ); ?>:</strong> <span class="text"><a href="<?php echo esc_url( mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings('mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders'), $order->get_id() ) ); ?>" target="_blank">#<?php echo $order->get_id(); ?></a></span></li>
        <li><strong><?php printf(__( 'Admin order link : <a href="%s" title="%s">#%s</a> ', 'multivendorx' ), admin_url( 'post.php?post=' . absint( $order->get_id() ) . '&action=edit' ) , sanitize_title($order->get_status()), $order->get_order_number()  ); ?></span></li>
        <li><strong><?php printf(__( 'Vendor Dashboard order link : <a href="%s" title="%s">#%s</a> ', 'multivendorx' ), esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders'), $order->get_id())) , sanitize_title($order->get_status()), $order->get_order_number()  ); ?></span></li>
        <li><strong><?php _e( 'Refund Reason', 'multivendorx' ); ?>:</strong> <span class="text"><?php echo $refund_details['refund_reason']; ?></span></li>
        <li><strong><?php _e( 'Additional Information', 'multivendorx' ); ?>:</strong> <span class="text"><?php echo $refund_details['addi_info']; ?></span></li>
        <li><strong><?php _e( 'Refund Status', 'multivendorx' ); ?>:</strong> <span class="text"><?php echo $refund_details['status']; ?></span></li>
		</ul>
    <?php }else{ ?>
    <p><?php printf(esc_html__( 'Your refund request for order <a href="%s">#%s</a> is %s', 'multivendorx' ), esc_url( $order->get_view_order_url() ), $order->get_id(), $refund_details['status'] ); 
			?></p>
    <p><?php printf(esc_html__( 'Reason given by seller is %s', 'multivendorx' ), $refund_details['admin_reason'] ); 
        ?></p>
    <?php } ?>
</div>

<?php do_action( 'mvx_email_footer' ); ?>