<?php
/**
 * Order details table shown in emails.
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

$order_id = $order->get_id();
$suborders = get_mvx_suborders($order_id);

if ( count($suborders) > 1 ) { ?>
    <p style="margin: 0 0 16px;"><?php echo esc_html__('Since your order contains products sold by different vendors, it has been split into multiple sub-orders. Each sub-order will be handled by their respective vendor independently.','multivendorx');?></p>
<?php
}

if( $suborders ) {
    foreach ( $suborders as $suborder ) {
        $suborder_id = $suborder->get_id();
        $vendor_id = get_post_meta( $suborder_id, '_vendor_id', true );
        $vendor = get_mvx_vendor($vendor_id);
        if( $vendor ) {
            $store_name = $vendor->page_title;
            ?>
            <h2><?php echo esc_html__('Suborder #', 'multivendorx') . $suborder->get_order_number(); ?></h2>
            <div style="margin-bottom: 40px;">
                <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
                    <thead>
                        <tr>
                            <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'multivendorx' ); ?></th>
                            <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Quantity', 'multivendorx' ); ?></th>
                            <th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Price', 'multivendorx' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        echo wc_get_email_order_items( 
                            $suborder,
                            array(
                                'show_sku'      => $sent_to_admin,
                                'show_image'    => false,
                                'image_size'    => array( 32, 32 ),
                                'plain_text'    => $plain_text,
                                'sent_to_admin' => $sent_to_admin,
                            )
                        );
                        ?>
                    </tbody>
                    <tfoot>
                        <?php
                        $item_totals = $suborder->get_order_item_totals();
                        if ( $item_totals ) {
                            $i = 0;
                            foreach ( $item_totals as $total ) {
                                $i++;
                                ?>
                                <tr>
                                    <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
                                    <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
                                </tr>
                                <?php
                            }
                        }
                        if ( $suborder->get_customer_note() ) {
                            ?>
                            <tr>
                                <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'multivendorx' ); ?></th>
                                <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $suborder->get_customer_note() ) ) ); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tfoot>
                </table>
            </div> 
            <?php
        }
    }
}

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<h2>
    <?php
    if ( $sent_to_admin ) {
        $before = '<a class="link" href="' . esc_url( $order->get_edit_order_url() ) . '">';
        $after  = '</a>';
    } else {
        $before = '';
        $after  = '';
    }
    /* translators: %s: Order ID. */
    echo wp_kses_post( $before . sprintf( __( '[Order #%s]', 'multivendorx' ) . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
    ?>
</h2>
<h2><?php esc_html_e('Order Totals', 'multivendorx'); ?></h2>
<div style="margin-bottom: 40px;">
    <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
        <tfoot>
            <?php
            $item_totals = $order->get_order_item_totals();
            if ( $item_totals ) {
                $i = 0;
                foreach ( $item_totals as $total ) {
                    $i++;
                    ?>
                    <tr>
                        <th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
                        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
                    </tr>
                    <?php
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
        </tfoot>
    </table>
</div>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>