<?php

/**
 * WCFM to MVX migration class 
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/core
 * @version   	3.6
 */

class MVX_WCfmMarketplace {
	
	public function __construct() {}
	
	// Get all MVX vendor
	public function get_marketplace_vendor() {
		$seller_query = new WP_User_Query( array(
			'role'	  => 'wcfm_vendor',
		) );
		$marketplace_get_vendors = $seller_query->get_results();
		return $marketplace_get_vendors;
	}

	public function store_product_vendor_migrate($vendor_id) {
		global $MVX;
		$user = new WP_User(absint($vendor_id));
		if(!in_array('dc_vendor', $user->roles)) {
			$user->set_role('dc_vendor');
			$user->remove_cap( 'wcfm_vendor');
			$vendor = get_mvx_vendor($vendor_id);
			if (!$vendor) return false;
			$term_id = get_user_meta( $vendor_id, '_vendor_term_id', true);
			$shipping_class_id = get_user_meta( $vendor_id, 'shipping_class_id', true );
			wp_update_term( absint($term_id), 'dc_vendor_shop' );
			wp_update_term( absint($shipping_class_id), 'product_shipping_class' );
			// Store data migrate
			$this->store_vendor_data_migrate( $vendor_id );
			// Store product migrate
			$this->store_product_migrate( $vendor_id, $term_id );

			$user_details = get_user_meta($vendor_id, 'wcfmmp_profile_settings', true);
			// update image
			if ( !empty($user_details) && isset($user_details['gravatar'])) {
				update_user_meta($vendor_id, '_vendor_image', $user_details['gravatar']);
			}
			// update banner
			if ( !empty($user_details) && isset($user_details['banner'])) {
				update_user_meta($vendor_id, '_vendor_banner', $user_details['banner']);
			}
			// update store name
			if ( !empty($user_details) && isset($user_details['store_name'])) {
				$vendor->update_page_title(wc_clean($user_details['store_name']));
			}
			// update store slug
			if ( !empty($user_details) && isset($user_details['store_slug'])) {
				$vendor->update_page_slug(wc_clean($user_details['store_slug']));
			}

		}
		return true;
	}

	public function store_vendor_data_migrate($vendor_id) {
		$store_setting = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
		if (isset($store_setting)) {
			if (isset($store_setting['shop_description']) && !empty($store_setting['shop_description']) ) {
				update_user_meta($vendor_id, '_vendor_description', stripslashes( html_entity_decode( $store_setting['shop_description'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
			}
			if (isset($store_setting['address']) && !empty($store_setting['address']) ) {
				update_user_meta($vendor_id, '_vendor_country', $store_setting['address']['country']);
				update_user_meta($vendor_id, '_vendor_state', $store_setting['address']['state']);
			}
			if (isset($store_setting['social']) && !empty($store_setting['social'])) {
				update_user_meta($vendor_id, '_vendor_fb_profile', $store_setting['social']['fb']);
				update_user_meta($vendor_id, '_vendor_twitter_profile', $store_setting['social']['twitter']);
				update_user_meta($vendor_id, '_vendor_linkdin_profile', $store_setting['social']['linkedin']);
				update_user_meta($vendor_id, '_vendor_youtube', $store_setting['social']['youtube']);
				update_user_meta($vendor_id, '_vendor_instagram', $store_setting['social']['instagram']);
			}
			if (isset($store_setting['store_location']) && !empty($store_setting['store_location'])) {
				update_user_meta($vendor_id, '_store_location', wc_clean($store_setting['store_location']));
			}
			if (isset($store_setting['store_lat']) && !empty($store_setting['store_lat'])) {
				update_user_meta($vendor_id, '_store_lat', wc_clean($store_setting['store_lat']));
			}
			if (isset($store_setting['store_lng']) && !empty($store_setting['store_lng'])) {
				update_user_meta($vendor_id, '_store_lng', wc_clean($store_setting['store_lng']));
			}
			// commission
			if (isset($store_setting['commission']['commission_fixed'])) {
				update_user_meta( $vendor_id, '_vendor_commission', $store_setting['commission']['commission_fixed'] );
			}
			if (isset($store_setting['commission']['commission_percent'])) {
				update_user_meta( $vendor_id, '_vendor_commission', $store_setting['commission']['commission_percent'] );
			}
		}
	}

	public function store_product_migrate( $vendor_id, $term_id ) {
		global $MVX;
		$vendor_products = $MVX->multivendor_migration->mvx_get_products_by_vendor( $vendor_id );
		if($vendor_products) {
			foreach($vendor_products as $product ) {
				if ( !$product ) continue;
				wp_delete_object_term_relationships($product->ID, $MVX->taxonomy->taxonomy_name);
				wp_set_object_terms($product->ID, (int) $term_id, $MVX->taxonomy->taxonomy_name, true);

				$commission_data = get_post_meta($product->ID, '_wcfmmp_commission', true);
				if(isset($commission_data['commission_fixed']) && isset($commission_data['commission_percent'])) {
					if ($MVX->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {
						update_post_meta($product->ID, '_commission_fixed_with_percentage', $commission_data['commission_fixed']);
						update_post_meta($product->ID, '_commission_percentage_per_product', $commission_data['commission_percent']);
					} elseif ($MVX->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {
						update_post_meta($product->ID, '_commission_fixed_with_percentage_qty', $commission_data['commission_fixed']);
						update_post_meta($product->ID, '_commission_percentage_per_product', $commission_data['commission_percent']);
					} else {
						update_post_meta($product->ID, '_commission_per_product', $commission_data['commission_fixed']);
						update_post_meta($product->ID, '_commission_fixed_with_percentage', $commission_data['commission_percent']);
					}
				}
			}
		}
	}

	public function store_order_migrate() {
		global $wpdb, $MVX;
		$wcfm_get_vendors = $this->get_marketplace_vendor();
		if( empty( $wcfm_get_vendors ) ) {
			$wcfm_vendor_order_id_list = $wpdb->get_results(esc_sql("SELECT * FROM `{$wpdb->prefix}wcfm_marketplace_orders`"));
			if ($wcfm_vendor_order_id_list) {
				foreach ($wcfm_vendor_order_id_list as $wcfm_order) {
					$order_id = $wcfm_order->order_id;
					$order = wc_get_order($order_id);
					if(!$order) continue;

					$order = wc_get_order($order_id);
					$_mvx_vendor_specific_order_migrated = get_post_meta($order_id, '_mvx_vendor_specific_order_migrated', true) ? get_post_meta($order_id, '_mvx_vendor_specific_order_migrated', true) : array();
					$set_order_id_migration = array();
					if ( !in_array($order_id, $_mvx_vendor_specific_order_migrated) ) {

						$set_order_id_migration[] = $order_id;

						// Remove previous added items
						$line_items = $order->get_items();
						$shipping_items = $order->get_items('shipping');

						foreach ($line_items as $key_items => $value_items) {
							wc_delete_order_item_meta( $key_items, '_wcfmmp_order_item_processed' ); 
							wc_delete_order_item_meta( $key_items, '_vendor_id' );
						}

						foreach ($shipping_items as $key_shipping => $value_shipping) {
							wc_delete_order_item_meta( $key_shipping, 'method_slug' ); 
						}

						$suborder_create = $MVX->order->mvx_manually_create_order_item_and_suborder($order_id, '', true);
						update_post_meta($order_id, '_mvx_vendor_specific_order_migrated', $set_order_id_migration);
					}
				}
			}
			// Paid those commission which is already paid in wcfm
			$this->mvx_paid_commission_from_previous_marketplace();
			// Deactive plugins
			$this->deactive_previous_multivendor();

			mvx_update_option('mvx_migration_orders_table_migrated', true);
			wp_clear_scheduled_hook('migrate_wcfm_multivendor_table');
		}	
	}

	public function mvx_paid_commission_from_previous_marketplace() {
		global $wpdb;
		$withdrawl_status = 'completed';
		$wcfm_vendor_paid_order_list = $wpdb->get_results($wpdb->prepare("SELECT order_id FROM `{$wpdb->prefix}wcfm_marketplace_orders` WHERE withdraw_status = %s", $withdrawl_status ));
		if ($wcfm_vendor_paid_order_list) {
			foreach ($wcfm_vendor_paid_order_list as $key_commission => $value_commission) {
				if ( wp_get_post_parent_id( $value_commission->order_id ) == 0 ) {
					$mvx_suborders = get_mvx_suborders($value_commission->order_id);
					if ( $mvx_suborders ) {
						foreach ( $mvx_suborders as $suborder ) {
							$commission_id = get_post_meta( $suborder->get_id(), '_commission_id', true );
							mvx_paid_commission_status($commission_id);
						}
					}
				}
			}
		}
	}

	// Deactive WCFM multivendor
	public function deactive_previous_multivendor() {
		// WCFM free deactive
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( is_plugin_active('wc-multivendor-marketplace/wc-multivendor-marketplace.php') ) {
	    	deactivate_plugins('wc-multivendor-marketplace/wc-multivendor-marketplace.php');    
	    }
	    // WCFM frontend manager deactive
	    if ( is_plugin_active('wc-frontend-manager/wc_frontend_manager.php') ) {
	    	deactivate_plugins('wc-frontend-manager/wc_frontend_manager.php');    
	    }
	    // WCFM membership deactive
	    if ( is_plugin_active('wc-multivendor-membership/wc-multivendor-membership.php') ) {
	    	deactivate_plugins('wc-multivendor-membership/wc-multivendor-membership.php');    
	    }
	}
	
}