<?php
/**
 * REST API Products controller
 *
 * Handles requests to the /orders endpoint of WooCommerce.
 *
 * @package MultiVendorX/API
 * @since   3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * REST API Orders controller
 *
 * List of Orders via get request.
 * New params
 * vendor integer vendor id whose orders will be retrived.
 * include_vendor array of vendor ids, whos order will be retrived only
 * exclude_vendor array of vendor ids want to exclude
 */

add_filter('woocommerce_rest_shop_order_object_query', 'enable_vendor_on_list_shop_order_query', 10, 2);

function enable_vendor_on_list_shop_order_query($args, $request) {
	global $wpdb;
	$vendor_list_prepared = '';
	$prepare_values = array();
	if(!empty($request['vendor'])) {
		$vendor_list_prepared = "%d";
		$prepare_values[] = $request['vendor'];
	} else if(!empty($request['include_vendor']) && is_array($request['include_vendor'])) {
		$vendor_list_prepared = implode( ', ', array_fill( 0, count( $request['include_vendor'] ), '%d' ) );
		$prepare_values = $request['include_vendor'];
	} else if(!empty($request['exclude_vendor']) && is_array($request['exclude_vendor'])) {
		$vendor_list_prepared = implode( ', ', array_fill( 0, count( $request['exclude_vendor'] ), '%d' ) );
		$prepare_values = $request['exclude_vendor'];
	}
 
	if($vendor_list_prepared != "") {
		$order_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT order_id
			FROM {$wpdb->prefix}woocommerce_order_items
			WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '_vendor_id' AND meta_value IN ( " . wc_clean($vendor_list_prepared) . " ) )
			AND order_item_type = 'line_item'
		 ", $prepare_values ) );
		
		// Force WP_Query return empty if don't found any order.
		$order_ids = ! empty( $order_ids ) ? $order_ids : array( 0 );

		if(isset($request['exclude_vendor'])) $args['post__not_in'] = array_merge($args['post__not_in'], $order_ids);
		else $args['post__in'] = array_merge($args['post__in'], $order_ids);
	}
	return $args;
}

