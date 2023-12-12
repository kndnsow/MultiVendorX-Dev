<?php

/**
 * MVX Order Functions
 *
 * Functions for order specific things.
 *
 * @package MultiVendorX/Functions
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get all orders.
 *
 * @since 3.4.0
 * @param $args query_args
 * @param $return_type return types
 * @param $subonly suborder only
 * @return array
 */
function mvx_get_orders($args = array(), $return_type = 'ids', $subonly = false) {
    
    $default = array(
	'posts_per_page'   => -1,
	'orderby'          => 'date',
	'order'            => 'DESC',
	'post_type'        => 'shop_order',
	'post_status'      => array('wc-processing', 'wc-completed'),
	'fields'           => 'ids',
    );
    if( $subonly ) {
        $default['meta_key'] = '_created_via';
        $default['meta_value'] = 'mvx_vendor_order';
    }
    $args = wp_parse_args($args, $default);
    $query = new WP_Query( apply_filters( 'mvx_get_orders_query_args', $args ) );
    if(strtolower($return_type) == 'object'){
        $orders = array();
        foreach ($query->get_posts() as $post_id) {
            $orders[$post_id] = wc_get_order($post_id);
        }
        return $orders;
    }
    return $query->get_posts();
}

/**
 * Get Vendor order object.
 *
 * @since 3.4.0
 * @return object/false Vendor order object
 */
function mvx_get_order($id){
    global $MVX;
    if($id){
        if(!class_exists('mvx_vendor_order')){
            // Init MVX Vendor Order class
            $MVX->load_class('vendor-order');
        }
        $vendor_order = new mvx_vendor_order($id);
        if(!$vendor_order->vendor_id) return false;
        return $vendor_order;
    }else{
        return false;
    }
}

/**
 * Checking order is vendor order or not.
 *
 * @since 3.4.0
 * @param $order integer/object
 * @param $current_vendor boolean. Default false
 * @return boolean
 */
function is_mvx_vendor_order( $order, $current_vendor = false ) {
    $order_id = 0;
    if( is_object( $order ) ){
        $order_id = $order->get_id();
    }else{
        $order_id = absint( $order );
    }
    $vendor_order = mvx_get_order( $order_id );
    if( $current_vendor ){
        return ( $vendor_order && $vendor_order->vendor_id === get_current_user_id() ) ? true : false;
    }
    return ( $vendor_order ) ? true : false;
}

/**
 * Get total refunded commission amount associated with refund.
 *
 * @since 3.4.0
 * @return boolean
 */
function get_refund_commission_amount($refund_id, $context = 'view') {
    if( $refund_id ){
        $order_id = wp_get_post_parent_id( $refund_id );
        $commission_id = get_post_meta( $order_id, '_commission_id', true );
        $commission_refunded_data = get_post_meta( $commission_id, '_commission_refunded_data', true );
        if( isset($commission_refunded_data[$refund_id][$commission_id]) ){
            $refund_commission_data = $commission_refunded_data[$refund_id][$commission_id];
            return array_sum($refund_commission_data);
        }
    }
    return false;
}

/**
 * Get total refunded item commission amount associated with refund.
 *
 * @since 3.4.0
 * @return boolean
 */
function mvx_get_total_refunded_for_item( $item_id, $order_id ) {
    if( $item_id && $order_id ) {
        $commission_id = get_post_meta( $order_id, '_commission_id', true );
        $commission_refunded_items_data = get_post_meta( $commission_id, '_commission_refunded_items_data', true );
        $refunds = wc_get_orders(
            array(
                'type'   => 'shop_order_refund',
                'parent' => $order_id,
                'limit'  => -1,
            )
        );
        $item_total = 0;
        if($refunds){
            foreach ( $refunds as $refund ) {
                foreach ( $refund->get_items( 'line_item' ) as $refunded_item ) {
                    if ( absint( $refunded_item->get_meta( '_refunded_item_id' ) ) === $item_id ) {
                        if( isset($commission_refunded_items_data[$refund->get_id()][$item_id]) )
                            $item_total += $commission_refunded_items_data[$refund->get_id()][$item_id];
                    }
                }
            }
        }
        return $item_total;
    }
    return false;
}

/**
 * Get MVX suborders if available.
 *
 * @param int $order_id.
 * @param array $args.
 * @param boolean $object.
 * @return object suborders.
 */
function get_mvx_suborders( $order_id, $args = array(), $object = true ) {
    $default = array(
        'post_parent' => $order_id,
        'post_type' => 'shop_order',
        'numberposts' => -1,
        'post_status' => 'any'
    );
    $args = ( $args ) ? wp_parse_args( $args, $default ) : $default;
    $orders = array();
    $posts = get_posts( $args );
    foreach ( $posts as $post ) {
        $orders[] = ( $object ) ? wc_get_order( $post->ID ) : $post->ID;
    }
    return $orders;
}

/**
 * Get MVX commisssion order
 *
 * @param int $commission_id.
 * @return object MVX vendor order class object.
 */
function get_mvx_order_by_commission( $commission_id ) {
    $order_id = mvx_get_commission_order_id( $commission_id );
    if( $order_id ){
        $vendor_order = mvx_get_order( $order_id );
        return $vendor_order;
    }
    return false;
}

/**
 * Get Parent shipping item id
 *
 * @param int $commission_id.
 * @return object MVX vendor order class object.
 */
function get_vendor_parent_shipping_item_id( $order_id, $vendor_id ) {
    if( $order_id ){
        $order = wc_get_order( $order_id );
        $line_items_shipping = $order->get_items( 'shipping' );
        foreach ( $line_items_shipping as $item_id => $item ){
            $shipping_vendor_id = $item->get_meta('vendor_id', true);
            if( $shipping_vendor_id == $vendor_id ) return $item_id;
        }
    }
    return false;
}

/**
 * Get commission order ID
 *
 * @param int $commission_id.
 * @return order ID
 */
function mvx_get_commission_order_id( $commission_id ) {
    $order_id = get_post_meta( $commission_id, '_commission_order_id', true );
    return ( $order_id ) ? $order_id : false;
}

/**
 * Get order commission ID
 *
 * @param int $order_id.
 * @return commission ID
 */
function mvx_get_order_commission_id( $order_id ) {
    $commission_id = get_post_meta( $order_id, '_commission_id', true );
    return ( $commission_id ) ? $commission_id : false;
}

/**
 * Get customer order refund related message
 *
 * @param int $order Object.
 * @param array $settings Settings.
 * @return $msg Message
 */
function mvx_get_customer_refund_order_msg( $order, $settings = array() ) {
    if( !$order ) return false;
    $default_msg = apply_filters( 'mvx_customer_my_account_refund_order_messages', array(
        'order_status_not_allowed' => __( 'Your Refund is not allowed for this order status', 'multivendorx' ),
        'order_refund_period_overed' => __( 'Your Refund Period is over. Please contact with your seller for further information' , 'multivendorx' ),
        'order_refund_rejected' => __( '*** Your Request Is Rejected ***', 'multivendorx' ),
        'order_refund_request_pending' => __( 'Your Request Is pending', 'multivendorx' ),
        'order_refund_request_accepted' => __( '*** Your Request is Accepted *** ', 'multivendorx' ),
    ), $order, $settings );
    $cust_refund_status = get_post_meta( $order->get_id(), '_customer_refund_order', true ) ? get_post_meta( $order->get_id(), '_customer_refund_order', true ) : '';
    $refund_days_limit = get_mvx_global_settings('refund_days') ? absint( get_mvx_global_settings('refund_days') ) : apply_filters( 'mvx_customer_refund_order_default_days_limit', 10, $order );
    $order_date = $order->get_date_created()->format('Y-m-d');
    $order_place_days = time() - strtotime( $order_date );
    $message = array();

    if( abs( round( $order_place_days / 86400 ) ) > $refund_days_limit ) {
        $message['type'] = 'info';
        $message['msg'] = isset( $default_msg['order_refund_period_overed'] ) ? $default_msg['order_refund_period_overed'] : __( 'Your Refund Period is over. Please contact with your seller for further information', 'multivendorx' );
    }
    if( is_array(get_mvx_global_settings('customer_refund_status')) && !in_array( $order->get_status() , get_mvx_global_settings('customer_refund_status') ) ) {
        $message['type'] = 'info';
        $message['msg'] = isset( $default_msg['order_status_not_allowed'] ) ? $default_msg['order_status_not_allowed'] : __( 'Your Refund is not allowed for this order status', 'multivendorx' );
    }
    if( $cust_refund_status == 'refund_reject' ) {
        $message['type'] = 'error';
        $message['msg'] = isset( $default_msg['order_refund_rejected'] ) ? $default_msg['order_refund_rejected'] : __( 'Sorry!! Your Request Is Reject', 'multivendorx' );
    }elseif( $cust_refund_status == 'refund_request' ) {
        $message['type'] = 'warning';
        $message['msg'] = isset( $default_msg['order_refund_request_pending'] ) ? $default_msg['order_refund_request_pending'] : __( 'Your Request Is pending', 'multivendorx' );
    }elseif( $cust_refund_status == 'refund_accept' ) {
        $message['type'] = 'success';
        $message['msg'] = isset( $default_msg['order_refund_request_accepted'] ) ? $default_msg['order_refund_request_accepted'] : __( 'Congratulation: *** Your Request is Accepted *** ', 'multivendorx' );
    }

    return $message;
}