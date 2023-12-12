<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

 defined( 'ABSPATH' ) || exit;

 $order = wc_get_order( $order_id ); 
 
 if ( ! $order ) {
     return;
 }
 
 $order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
 $show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
 $show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
 $downloads             = $order->get_downloadable_items();
 $show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();
 
 if ( $show_downloads ) {
     wc_get_template(
         'order/order-downloads.php',
         array(
             'downloads'  => $downloads,
             'show_title' => true,
         )
     );
 }
 ?>
 <section class="woocommerce-order-details">
     <?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>
 
     <h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'multivendorx' ); ?></h2>
     <?php
      $suborders = array_reverse(get_mvx_suborders($order_id));
      if (count($suborders) > 1) {
        wc_print_notice(esc_html__('Since your order contains products sold by different vendors, it has been split into multiple sub-orders. Each sub-order will be handled by their respective vendor independently.','multivendorx'), 'notice' );
      }
     foreach ($suborders as $suborder){
         order_details_vendor_table($suborder, $order, $order_items, $show_purchase_note, $show_customer_details, $downloads, $show_downloads);
     }
     ?>
 
     <h4><?php esc_html_e('Order Totals','multivendorx');?></h4>
     <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
 
         <tfoot>
             <?php
             foreach ( $order->get_order_item_totals() as $key => $total ) {
                 ?>
                     <tr>
                         <th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
                         <td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] );  ?></td>
                     </tr>
                     <?php
             }
             ?>
             <?php if ( $order->get_customer_note() ) : ?>
                 <tr>
                     <th><?php esc_html_e( 'Note:', 'multivendorx' ); ?></th>
                     <td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
                 </tr>
             <?php endif; ?>
         </tfoot>
     </table>
 
     <?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
 </section>
 
 <?php
 function order_details_vendor_table($suborder, $order, $order_items, $show_purchase_note, $show_customer_details, $downloads, $show_downloads){
     // get vendor id
     $suborder_id = $suborder->get_id();
     $vendor_id = get_post_meta( $suborder_id, '_vendor_id', true );
     $vendor = get_mvx_vendor($vendor_id);
     $store_name =$vendor->page_title;
     ?>
     <div class="order_details_vendor_table_header">
         <div class="order_details_vendor_table_title">
             <?php echo '<h4>'.esc_html__('Products sold by ','multivendorx').$store_name.'</h4>'; ?>
         </div>
         <div class="order_details_vendor_table_view_order">
             <a href="<?php echo esc_attr($suborder->get_view_order_url());?>"><button class="woocommerce-button button view"><?php echo esc_html__( 'View Order', 'multivendorx' ).' #'.esc_html($suborder_id); ?></button></a>
         </div>
     </div>
 
 
     <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
         <thead>
             <tr>
                 <th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'multivendorx' ); ?></th>
                 <th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'multivendorx' ); ?></th>
             </tr>
         </thead>
 
         <tbody>
             <?php
             do_action( 'woocommerce_order_details_before_order_table_items', $order );
             $vendor_subtotal = 0;
 
             foreach ( $order_items as $item_id => $item ) {
                 $product = $item->get_product();
 
                 // if vendor is not vendor id, skip.
                 $product_vendor_id = get_mvx_product_vendors( $product->get_id() ) ? get_mvx_product_vendors( $product->get_id() )->id : 0;
                 if ($vendor_id !== $product_vendor_id){
                     continue;
                 }
 
                 // add to vendor subtotal
                 if (!isset($tax_display)){
                     $tax_display = get_option( 'woocommerce_tax_display_cart' );
                 }
 
                 if ( 'excl' === $tax_display ) {
                   $vendor_subtotal+=$order->get_line_subtotal( $item );
                 } else {
                   $vendor_subtotal+=$order->get_line_subtotal( $item, true );
                 }
 
 
                 wc_get_template(
                     'order/order-details-item.php',
                     array(
                         'order'              => $order,
                         'item_id'            => $item_id,
                         'item'               => $item,
                         'show_purchase_note' => $show_purchase_note,
                         'purchase_note'      => $product ? $product->get_purchase_note() : '',
                         'product'            => $product,
                     )
                 );
             }
 
             do_action( 'woocommerce_order_details_after_order_table_items', $order );
             ?>
         </tbody>
 
         <tfoot>
             
             <?php
             foreach ( $suborder->get_order_item_totals() as $key => $total ) {
                 ?>
                     <tr>
                         <th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
                         <td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] );  ?></td>
                     </tr>
                     <?php
             }
             ?>
             <?php if ( $suborder->get_customer_note() ) : ?>
                 <tr>
                     <th><?php esc_html_e( 'Note:', 'multivendorx' ); ?></th>
                     <td><?php echo wp_kses_post( nl2br( wptexturize( $suborder->get_customer_note() ) ) ); ?></td>
                 </tr>
             <?php endif; ?>
                 
         </tfoot>
     </table><br>
     <?php
 }
 
 
 /**
  * Action hook fired after the order details.
  *
  * @since 4.4.0
  * @param WC_Order $order Order data.
  */
 do_action( 'woocommerce_after_order_details', $order );
 
 if ( $show_customer_details ) {
     wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
 }
 