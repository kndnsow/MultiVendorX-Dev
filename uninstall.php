<?php

/**
 * Multivendor X Uninstall
 *
 * Uninstalling Multivendor X deletes user roles, pages, tables, and options.
 *
 * @author 		MultiVendorX
 * @version     3.0.0
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb, $wp_version;

wp_clear_scheduled_hook('masspay_cron_start');
wp_clear_scheduled_hook('vendor_monthly_order_stats');
wp_clear_scheduled_hook('vendor_weekly_order_stats');
wp_clear_scheduled_hook('migrate_spmv_multivendor_table');
wp_clear_scheduled_hook('mvx_spmv_excluded_products_map');
wp_clear_scheduled_hook('mvx_spmv_product_meta_update');

/*
 * Only remove ALL product and page data if WC_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if (defined('MVX_REMOVE_ALL_DATA') && true === MVX_REMOVE_ALL_DATA) {
    // Roles + caps.
    include_once( dirname(__FILE__) . '/includes/mvx-core-functions.php' );
    remove_role('dc_vendor');
    remove_role('dc_pending_vendor');
    remove_role('dc_rejected_vendor');
    // Pages.
    wp_trash_post(mvx_vendor_dashboard_page_id());
    wp_trash_post(mvx_vendor_registration_page_id());

    // Tables.
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mvx_vendor_orders");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mvx_products_map");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mvx_visitors_stats");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mvx_cust_questions");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mvx_cust_answers");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mvx_shipping_zone_methods");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}mvx_shipping_zone_locations");

    // Delete options.
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'mvx\_%';");
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'dc\_%';");

    // Delete posts + data.
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'dc_commission', 'mvx_vendor_notice', 'mvx_transaction', 'mvx_university', 'mvx_vendorrequest' );");
    $wpdb->query("DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;");
    
    // Delete suborders
    $wpdb->query("DELETE wp_post, wp_postmeta, wp_woocommerce_order_items, wp_woocommerce_order_itemmeta FROM JOIN wp_post.ID = wp_postmeta.post_id, 
        wp_postmeta.post_id = wp_woocommerce_order_itemmeta.order_item_id,
        wp_post.ID = wp_woocommerce_order_items.order_item_id
        WHERE wp_post.post_type = 'shop_order' 
        AND wp_postmeta.meta_key = '_created_via' AND wp_postmeta.meta_value  = 'mvx_vendor_order';");

    // Delete terms if > WP 4.2 (term splitting was added in 4.2)
    if (version_compare($wp_version, '4.2', '>=')) {
        // Delete term taxonomie

        $wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => 'dc_vendor_shop') );

        // Delete orphan relationships
        $wpdb->query("DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;");

        // Delete orphan terms
        $wpdb->query("DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL;");

        // Delete orphan term meta
        if (!empty($wpdb->termmeta)) {
            $wpdb->query("DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL;");
        }
    }

    // Clear any cached data that has been removed
    wp_cache_flush();
}
