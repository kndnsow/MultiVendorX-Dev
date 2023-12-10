<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hidden_order_itemmeta = apply_filters(
        'mvx_vendor_dash_hidden_order_itemmeta', array(
    '_qty',
    '_tax_class',
    '_product_id',
    '_variation_id',
    '_line_subtotal',
    '_line_subtotal_tax',
    '_line_total',
    '_line_tax',
    'method_id',
    '_vendor_item_commission',
    'cost',
    'commission',
    '_vendor_id',
    'vendor_id',
    '_vendor_order_item_id',
    'Sold By',
    )
);
if ( $meta_data = $item->get_formatted_meta_data( '' ) ) : 
    foreach ( $meta_data as $meta_id => $meta ) :
    if ( in_array( $meta->key, $hidden_order_itemmeta, true ) ) {
            continue;
    }
    ?>
    <tr>
            <th><?php echo wp_kses_post( $meta->display_key ); ?>:</th>
            <td><?php echo wp_kses_post( force_balance_tags( $meta->display_value ) ); ?></td>
    </tr>
    <?php endforeach;
endif; 