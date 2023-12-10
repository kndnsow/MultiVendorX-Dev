<?php
/**
 * Show order refund
 *
 * @var object $refund The refund object.
 * @package MultiVendorX\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$who_refunded = new WP_User( $refund->get_refunded_by() );
?>
<tr class="refund <?php echo ( ! empty( $class ) ) ? esc_attr( $class ) : ''; ?>" data-order_refund_id="<?php echo esc_attr( $refund->get_id() ); ?>">
    <td>
        <div class="order-item-img icon-item">
            <i class="mvx-font ico-price2-icon"></i>
        </div>
    </td>
    <td colspan="3">
        <div class="order-item-detail">
            <?php
		if ( $who_refunded->exists() ) {
			printf(
				/* translators: 1: refund id 2: refund date 3: username */
				esc_html__( 'Refund #%1$s - %2$s by %3$s', 'multivendorx' ),
				esc_html( $refund->get_id() ),
				esc_html( wc_format_datetime( $refund->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) ),
				sprintf(
					'<abbr class="refund_by" title="%1$s">%2$s</abbr>',
					/* translators: 1: ID who refunded */
					sprintf( esc_attr__( 'ID: %d', 'multivendorx' ), absint( $who_refunded->ID ) ),
					esc_html( $who_refunded->display_name )
				)
			);
		} else {
			printf(
				/* translators: 1: refund id 2: refund date */
				esc_html__( 'Refund #%1$s - %2$s', 'multivendorx' ),
				esc_html( $refund->get_id() ),
				esc_html( wc_format_datetime( $refund->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) )
			);
		}
		?>
		<?php if ( $refund->get_reason() ) : ?>
			<p class="description"><?php echo esc_html( $refund->get_reason() ); ?></p>
		<?php endif; ?>
		<input type="hidden" class="order_refund_id" name="order_refund_id[]" value="<?php echo esc_attr( $refund->get_id() ); ?>" />
        </div>        
    </td>

    <?php do_action( 'mvx_vendor_dash_order_item_values', null, $refund, $refund->get_id() ); ?>

    <td colspan="3" class="line_cost">
            <div class="view">
                    <?php
                    echo wp_kses_post(
                            wc_price( '-' . $refund->get_amount(), array( 'currency' => $refund->get_currency() ) )
                    );
                    
                    $refunded_commission_amt = get_refund_commission_amount( $refund->get_id() );
                    echo '<small class="refunded">' . wc_price( $refunded_commission_amt, array( 'currency' => $refund->get_currency() ) ) . '</small>';
                    ?>
            </div>
    </td>

</tr>
