<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-new-order.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly 
global $MVX;
$vendor = get_mvx_vendor(absint($vendor_id));
do_action( 'woocommerce_email_header', $email_heading, $email );
$text_align = is_rtl() ? 'right' : 'left';
?>

<p><?php printf(esc_html__('A new order was received and marked as %s from %s. Their order is as follows:', 'multivendorx'), $order->get_status( 'edit' ), $order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?></p>

<?php do_action('woocommerce_email_before_order_table', $order, true, false, $email); ?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <thead>
        <tr>
            <?php do_action('mvx_before_vendor_order_table_header', $order, $vendor->term_id); ?>
            <th scope="col" style="text-align:<?php echo $text_align; ?>; border: 1px solid #eee;"><?php _e('Product', 'multivendorx'); ?></th>
            <th scope="col" style="text-align:<?php echo $text_align; ?>; border: 1px solid #eee;"><?php _e('Quantity', 'multivendorx'); ?></th>
            <th scope="col" style="text-align:<?php echo $text_align; ?>; border: 1px solid #eee;"><?php _e('Commission', 'multivendorx'); ?></th>
            <?php do_action('mvx_after_vendor_order_table_header', $order, $vendor->term_id); ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $vendor->vendor_order_item_table($order, $vendor->term_id);

        ?>
    </tbody>
</table>
<?php
if (apply_filters('show_cust_order_calulations_field', true, $vendor->id)) {
    ?>
    <table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
        <?php
        $totals = $vendor->mvx_vendor_get_order_item_totals($order, $vendor->term_id);
        if ($totals) {
            foreach ($totals as $total_key => $total) {
                ?><tr>
                    <th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee;"><?php echo $total['label']; ?></th>
                    <td style="text-align:<?php echo $text_align; ?>; border: 1px solid #eee;"><?php echo $total['value']; ?></td>
                </tr><?php
            }
        }
        if ( $order->get_customer_note() ) {
            ?>
            <tr>
                <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'multivendorx' ); ?></th>
                <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
    }
    if (apply_filters('show_cust_address_field', true, $vendor->id) || apply_filters( 'is_vendor_can_see_customer_details', true, $vendor->id, $order ) ) {
    ?>
    <h2><?php _e('Customer Details', 'multivendorx'); ?></h2>
    <?php if ($order->get_billing_email()) { ?>
        <p><strong><?php _e('Customer Name:', 'multivendorx'); ?></strong> <?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></p>
        <p><strong><?php _e('Email:', 'multivendorx'); ?></strong> <?php echo $order->get_billing_email(); ?></p>
    <?php } ?>
    <?php if ($order->get_billing_phone()) { ?>
        <p><strong><?php _e('Telephone:', 'multivendorx'); ?></strong> <?php echo $order->get_billing_phone(); ?></p>
    <?php
        }
    }
    ?>
    <table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
	<tr>
            <?php if (apply_filters('show_cust_billing_address_field', true, $vendor->id)) { ?>
            <td style="text-align:<?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" valign="top" width="50%">
                <h2><?php _e( 'Billing Address', 'multivendorx' ); ?></h2>
                <address class="address">
                    <?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : esc_html__( 'N/A', 'multivendorx' ); ?>
                </address>
            </td>
            <?php } ?>
            <?php if ( apply_filters('show_cust_shipping_address_field', true, $vendor->id) && ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() ) ) : ?>
                <td style="text-align:<?php echo $text_align; ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding:0;" valign="top" width="50%">
                        <h2><?php _e('Shipping Address', 'multivendorx'); ?></h2>
                        <address class="address"><?php echo $shipping; ?></address>
                </td>
            <?php endif; ?>
	</tr>
    </table>

<?php do_action('mvx_email_footer'); ?>