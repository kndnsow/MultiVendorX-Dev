<?php
/**
 * Shows a shipping line
 *
 * @var object $item The item being displayed
 * @var int $item_id The id of the item being displayed
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<tr class="shipping shipping-row <?php echo ( ! empty( $class ) ) ? esc_attr( $class ) : ''; ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>">
    <td>
        <div class="order-item-img icon-item">
            <i class="mvx-font ico-shippingnew-icon"></i>
        </div>
    </td>
    <td colspan="3">
        <div class="order-item-detail">
            <p><strong><?php echo esc_html( $item->get_name() ? $item->get_name() : __( 'Shipping', 'multivendorx' ) ); ?></strong></p>
            <table>
                <?php do_action( 'mvx_vendor_dash_before_order_itemmeta', $item_id, $item, null ); ?>
		<?php require 'html-order-item-meta.php'; ?>
		<?php do_action( 'mvx_vendor_dash_after_order_itemmeta', $item_id, $item, null ); ?>
            </table>
        </div>        
    </td>
    <td>
        <div class="view">
                <?php
                echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );
                $refunded = $order->get_total_refunded_for_item( $item_id, 'shipping' );
                if ( $refunded ) {
                        echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
                }
                ?>
        </div>
        <div class="edit" style="display: none;">
                <input type="text" name="shipping_cost[<?php echo esc_attr( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo esc_attr( wc_format_localized_price( $item->get_total() ) ); ?>" class="form-control line_total wc_input_price" />
        </div>
        <div class="refund" style="display: none;">
                <input type="text" name="refund_line_total[<?php echo absint( $item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="form-control refund_line_total wc_input_price" />
        </div>
    </td>
    <?php
	if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
		foreach ( $order_taxes as $tax_item ) {
			$tax_item_id    = $tax_item->get_rate_id();
			$tax_item_total = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
			?>
			<td colspan="2" class="line_tax" width="1%">
				<div class="view">
					<?php
					echo ( '' !== $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
					$refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'shipping' );
					if ( $refunded ) {
						echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
					}
					?>
				</div>
				<div class="edit" style="display: none;">
					<input type="text" name="shipping_taxes[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" value="<?php echo ( isset( $tax_item_total ) ) ? esc_attr( wc_format_localized_price( $tax_item_total ) ) : ''; ?>" class="form-control line_tax wc_input_price" />
				</div>
				<div class="refund" style="display: none;">
					<input type="text" name="refund_line_tax[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $tax_item_id ); ?>]" placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>" class="form-control refund_line_tax wc_input_price" data-tax_id="<?php echo esc_attr( $tax_item_id ); ?>" />
				</div>
			</td>
			<?php
		}
	}
	?>
</tr>
