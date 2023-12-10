<?php

/**
* @deprecated 4.0.0
* 	wc_deprecated_function(new, version, old)
*/
function do_wcmp_migrate() {
	wc_deprecated_function( 'do_wcmp_migrate', '4.0.0', 'do_mvx_migrate');
	return do_mvx_migrate();
}

function wcmp_plugin_init() {
	wc_deprecated_function('wcmp_plugin_init', '4.0.0', 'mvx_plugin_init');
	return mvx_plugin_init();
}

function wcmp_delete_woocomerce_transient_redirect_to_wcmp_setup() {
	wc_deprecated_function('wcmp_delete_woocomerce_transient_redirect_to_wcmp_setup', '4.0.0', 'mvx_delete_woocomerce_transient_redirect_to_mvx_setup');
	return mvx_delete_woocomerce_transient_redirect_to_mvx_setup();
}

function wcmp_admin_notice() {
	wc_deprecated_function('wcmp_admin_notice', '4.0.0', 'mvx_admin_notice');
	return mvx_admin_notice();
}

function wcmp_namespace_approve( $value ) {
	wc_deprecated_function('wcmp_namespace_approve', '4.0.0', 'mvx_namespace_approve');
	return mvx_namespace_approve( $value );
}

function wcmp_taxonomy_slug_input() {
	wc_deprecated_function('wcmp_taxonomy_slug_input', '4.0.0', 'mvx_taxonomy_slug_input');
	return mvx_taxonomy_slug_input();
}

function wcmp_admin_menu() {
	wc_deprecated_function('wcmp_admin_menu', '4.0.0', 'mvx_admin_menu');
	return mvx_admin_menu();
}

function wcmp_submenu_count() {
	wc_deprecated_function('wcmp_submenu_count', '4.0.0', 'mvx_submenu_count');
	return mvx_submenu_count();
}

function wcmp_kill_auto_save() {
	wc_deprecated_function('wcmp_kill_auto_save', '4.0.0', 'mvx_kill_auto_save');
	return mvx_kill_auto_save();
}

function wcmp_remove_wp_dashboard_widget() {
	wc_deprecated_function('wcmp_remove_wp_dashboard_widget', '4.0.0', 'mvx_remove_wp_dashboard_widget');
	return mvx_remove_wp_dashboard_widget();
}

function wcmp_vendor_shipping_admin_capability($current_id) {
	wc_deprecated_function('wcmp_vendor_shipping_admin_capability', '4.0.0', 'mvx_vendor_shipping_admin_capability');
	return mvx_vendor_shipping_admin_capability($current_id);
}

function wcmp_crop_image() {
	wc_deprecated_function('wcmp_crop_image', '4.0.0', 'mvx_crop_image');
	return mvx_crop_image();
}

function wcmp_datatable_get_vendor_orders() {
	wc_deprecated_function('wcmp_datatable_get_vendor_orders', '4.0.0', 'mvx_datatable_get_vendor_orders');
	return mvx_datatable_get_vendor_orders();
}

function wcmp_get_loadmorebutton_single_product_multiple_vendors() {
	wc_deprecated_function('wcmp_get_loadmorebutton_single_product_multiple_vendors', '4.0.0', 'mvx_get_loadmorebutton_single_product_multiple_vendors');
	return mvx_get_loadmorebutton_single_product_multiple_vendors();
}

function wcmp_load_more_review_rating_vendor() {
	wc_deprecated_function('wcmp_load_more_review_rating_vendor', '4.0.0', 'mvx_load_more_review_rating_vendor');
	return mvx_load_more_review_rating_vendor();
}

function wcmp_add_review_rating_vendor() {
	wc_deprecated_function('wcmp_add_review_rating_vendor', '4.0.0', 'mvx_add_review_rating_vendor');
	return mvx_add_review_rating_vendor();
}

function wcmp_copy_to_new_draft() {
	wc_deprecated_function('wcmp_copy_to_new_draft', '4.0.0', 'mvx_copy_to_new_draft');
	return mvx_copy_to_new_draft();
}

function wcmp_create_duplicate_product() {
	wc_deprecated_function('wcmp_create_duplicate_product', '4.0.0', 'mvx_create_duplicate_product');
	return mvx_create_duplicate_product();
}

function wcmp_auto_suggesion_product() {
	wc_deprecated_function('wcmp_auto_suggesion_product', '4.0.0', 'mvx_auto_suggesion_product');
	return mvx_auto_suggesion_product();
}

function wcmp_dismiss_dashboard_message() {
	wc_deprecated_function('wcmp_dismiss_dashboard_message', '4.0.0', 'mvx_dismiss_dashboard_message');
	return mvx_dismiss_dashboard_message();
}

function wcmp_msg_refresh_tab_data() {
	wc_deprecated_function('wcmp_msg_refresh_tab_data', '4.0.0', 'mvx_msg_refresh_tab_data');
	return mvx_msg_refresh_tab_data();
}

function wcmp_vendor_messages_operation() {
	wc_deprecated_function('wcmp_vendor_messages_operation', '4.0.0', 'mvx_vendor_messages_operation');
	return mvx_vendor_messages_operation();
}

function wcmp_frontend_sale_get_row_callback() {
	wc_deprecated_function('wcmp_frontend_sale_get_row_callback', '4.0.0', 'mvx_frontend_sale_get_row_callback');
	return mvx_frontend_sale_get_row_callback();
}

function wcmp_frontend_pending_shipping_get_row_callback() {
	wc_deprecated_function('wcmp_frontend_pending_shipping_get_row_callback', '4.0.0', 'mvx_frontend_pending_shipping_get_row_callback');
	return mvx_frontend_pending_shipping_get_row_callback();
}

function wcmp_vendor_csv_download_per_order() {
	wc_deprecated_function('wcmp_vendor_csv_download_per_order', '4.0.0', 'mvx_vendor_csv_download_per_order');
	return mvx_vendor_csv_download_per_order();
}

function wcmp_vendor_product_list() {
	wc_deprecated_function('wcmp_vendor_product_list', '4.0.0', 'mvx_vendor_product_list');
	return mvx_vendor_product_list();
}

function wcmp_vendor_unpaid_order_vendor_withdrawal_list() {
	wc_deprecated_function('wcmp_vendor_unpaid_order_vendor_withdrawal_list', '4.0.0', 'mvx_vendor_unpaid_order_vendor_withdrawal_list');
	return mvx_vendor_unpaid_order_vendor_withdrawal_list();
}

function wcmp_vendor_coupon_list() {
	wc_deprecated_function('wcmp_vendor_coupon_list', '4.0.0', 'mvx_vendor_coupon_list');
	return mvx_vendor_coupon_list();
}

function wcmp_vendor_transactions_list() {
	wc_deprecated_function('wcmp_vendor_transactions_list', '4.0.0', 'mvx_vendor_transactions_list');
	return mvx_vendor_transactions_list();
}

function wcmp_customer_ask_qna_handler() {
	wc_deprecated_function('wcmp_customer_ask_qna_handler', '4.0.0', 'mvx_customer_ask_qna_handler');
	return mvx_customer_ask_qna_handler();
}

function wcmp_vendor_dashboard_reviews_data() {
	wc_deprecated_function('wcmp_vendor_dashboard_reviews_data', '4.0.0', 'mvx_vendor_dashboard_reviews_data');
	return mvx_vendor_dashboard_reviews_data();
}

function wcmp_vendor_dashboard_customer_questions_data() {
	wc_deprecated_function('wcmp_vendor_dashboard_customer_questions_data', '4.0.0', 'mvx_vendor_dashboard_customer_questions_data');
	return mvx_vendor_dashboard_customer_questions_data();
}

function wcmp_vendor_products_qna_list() {
	wc_deprecated_function('wcmp_vendor_products_qna_list', '4.0.0', 'mvx_vendor_products_qna_list');
	return mvx_vendor_products_qna_list();
}

function wcmp_question_verification_approval() {
	wc_deprecated_function('wcmp_question_verification_approval', '4.0.0', 'mvx_question_verification_approval');
	return mvx_question_verification_approval();
}

function wcmp_product_tag_add() {
	wc_deprecated_function('wcmp_product_tag_add', '4.0.0', 'mvx_product_tag_add');
	return mvx_product_tag_add();
}

function wcmp_widget_vendor_pending_shipping() {
	wc_deprecated_function('wcmp_widget_vendor_pending_shipping', '4.0.0', 'mvx_widget_vendor_pending_shipping');
	return mvx_widget_vendor_pending_shipping();
}

function wcmp_widget_vendor_product_sales_report() {
	wc_deprecated_function('wcmp_widget_vendor_product_sales_report', '4.0.0', 'mvx_widget_vendor_product_sales_report');
	return mvx_widget_vendor_product_sales_report();
}

function wcmp_get_shipping_methods_by_zone() {
	wc_deprecated_function('wcmp_get_shipping_methods_by_zone', '4.0.0', 'mvx_get_shipping_methods_by_zone');
	return mvx_get_shipping_methods_by_zone();
}

function wcmp_add_shipping_method() {
	wc_deprecated_function('wcmp_add_shipping_method', '4.0.0', 'mvx_add_shipping_method');
	return mvx_add_shipping_method();
}

function wcmp_update_shipping_method() {
	wc_deprecated_function('wcmp_update_shipping_method', '4.0.0', 'mvx_update_shipping_method');
	return mvx_update_shipping_method();
}

function wcmp_delete_shipping_method() {
	wc_deprecated_function('wcmp_delete_shipping_method', '4.0.0', 'mvx_delete_shipping_method');
	return mvx_delete_shipping_method();
}

function wcmp_toggle_shipping_method() {
	wc_deprecated_function('wcmp_toggle_shipping_method', '4.0.0', 'mvx_toggle_shipping_method');
	return mvx_toggle_shipping_method();
}

function wcmp_configure_shipping_method() {
	wc_deprecated_function('wcmp_configure_shipping_method', '4.0.0', 'mvx_configure_shipping_method');
	return mvx_configure_shipping_method();
}

function wcmp_vendor_configure_shipping_method() {
	wc_deprecated_function('wcmp_vendor_configure_shipping_method', '4.0.0', 'mvx_vendor_configure_shipping_method');
	return mvx_vendor_configure_shipping_method();
}

function wcmp_product_classify_next_level_list_categories() {
	wc_deprecated_function('wcmp_product_classify_next_level_list_categories', '4.0.0', 'mvx_product_classify_next_level_list_categories');
	return mvx_product_classify_next_level_list_categories();
}

function wcmp_product_classify_search_category_level() {
	wc_deprecated_function('wcmp_product_classify_search_category_level', '4.0.0', 'mvx_product_classify_search_category_level');
	return mvx_product_classify_search_category_level();
}

function wcmp_list_a_product_by_name_or_gtin() {
	wc_deprecated_function('wcmp_list_a_product_by_name_or_gtin', '4.0.0', 'mvx_list_a_product_by_name_or_gtin');
	return mvx_list_a_product_by_name_or_gtin();
}

function wcmp_set_classified_product_terms() {
	wc_deprecated_function('wcmp_set_classified_product_terms', '4.0.0', 'mvx_set_classified_product_terms');
	return mvx_set_classified_product_terms();
}

function wcmp_do_refund() {
	wc_deprecated_function('wcmp_do_refund', '4.0.0', 'mvx_do_refund');
	return mvx_do_refund();
}

function wcmp_json_search_downloadable_products_and_variations() {
	wc_deprecated_function('wcmp_json_search_downloadable_products_and_variations', '4.0.0', 'mvx_json_search_downloadable_products_and_variations');
	return mvx_json_search_downloadable_products_and_variations();
}

function wcmp_json_search_products_and_variations() {
	wc_deprecated_function('wcmp_json_search_products_and_variations', '4.0.0', 'mvx_json_search_products_and_variations');
	return mvx_json_search_products_and_variations();
}

function wcmp_grant_access_to_download() {
	wc_deprecated_function('wcmp_grant_access_to_download', '4.0.0', 'mvx_grant_access_to_download');
	return mvx_grant_access_to_download();
}

function wcmp_order_status_changed() {
	wc_deprecated_function('wcmp_order_status_changed', '4.0.0', 'mvx_order_status_changed');
	return mvx_order_status_changed();
}

function wcmp_vendor_banking_ledger_list() {
	wc_deprecated_function('wcmp_vendor_banking_ledger_list', '4.0.0', 'mvx_vendor_banking_ledger_list');
	return mvx_vendor_banking_ledger_list();
}

function wcmp_follow_store_toggle_status() {
	wc_deprecated_function('wcmp_follow_store_toggle_status', '4.0.0', 'mvx_follow_store_toggle_status');
	return mvx_follow_store_toggle_status();
}

function wcmp_vendor_zone_shipping_order() {
	wc_deprecated_function('wcmp_vendor_zone_shipping_order', '4.0.0', 'mvx_vendor_zone_shipping_order');
	return mvx_vendor_zone_shipping_order();
}

function wcmp_create_commission($vendor_order_id, $posted_data, $order) {
	wc_deprecated_function('wcmp_create_commission', '4.0.0', 'mvx_create_commission');
	return mvx_create_commission($vendor_order_id, $posted_data, $order);
}

function wcmp_vendor_new_order_mail( $order_id, $from_status, $to_status ) {
	wc_deprecated_function('wcmp_vendor_new_order_mail', '4.0.0', 'mvx_vendor_new_order_mail');
	return mvx_vendor_new_order_mail( $order_id, $from_status, $to_status );
}

function wcmp_create_commission_refunds($vendor_order_id, $refund_id) {
	wc_deprecated_function('wcmp_create_commission_refunds', '4.0.0', 'mvx_create_commission_refunds');
	return mvx_create_commission_refunds($vendor_order_id, $refund_id);
}

function wcmp_order_reverse_action() {
	wc_deprecated_function('wcmp_order_reverse_action', '4.0.0', 'mvx_order_reverse_action');
	return mvx_order_reverse_action();
}

function wcmp_due_commission_reverse($order_id) {
	wc_deprecated_function('wcmp_due_commission_reverse', '4.0.0', 'mvx_due_commission_reverse');
	return mvx_due_commission_reverse($order_id);
}

function wcmp_order_complete_action() {
	wc_deprecated_function('wcmp_order_complete_action', '4.0.0', 'mvx_order_complete_action');
	return mvx_order_complete_action();
}

function wcmp_process_commissions($order_id) {
	wc_deprecated_function('wcmp_process_commissions', '4.0.0', 'mvx_process_commissions');
	return mvx_process_commissions($order_id);
}

function wcmp_get_commission_as_per_product_price( $product_id = 0, $line_total = 0, $item_quantity = 0, $commission_rule = array() ) {
	wc_deprecated_function('wcmp_get_commission_as_per_product_price', '4.0.0', 'mvx_get_commission_as_per_product_price');
	return mvx_get_commission_as_per_product_price( $product_id = 0, $line_total = 0, $item_quantity = 0, $commission_rule = array() );
}

function wcmp_get_commission_rule_by_quantity_rule($product_id = 0, $line_total = 0, $item_quantity = 0, $commission_rule = array()) {
	wc_deprecated_function('wcmp_get_commission_rule_by_quantity_rule', '4.0.0', 'mvx_get_commission_rule_by_quantity_rule');
	return mvx_get_commission_rule_by_quantity_rule($product_id = 0, $line_total = 0, $item_quantity = 0, $commission_rule = array());
}

function wcmp_product_type_selector($product_types) {
	wc_deprecated_function('wcmp_product_type_selector', '4.0.0', 'mvx_product_type_selector');
	return mvx_product_type_selector($product_types);
}

function wcmp_product_type_options($product_type_options) {
	wc_deprecated_function('wcmp_product_type_options', '4.0.0', 'mvx_product_type_options');
	return mvx_product_type_options($product_type_options);
}

function wcmp_wc_product_sku_enabled($state) {
	wc_deprecated_function('wcmp_wc_product_sku_enabled', '4.0.0', 'mvx_wc_product_sku_enabled');
	return mvx_wc_product_sku_enabled($state);
}

function wcmp_after_add_to_cart_form() {
	wc_deprecated_function('wcmp_after_add_to_cart_form', '4.0.0', 'mvx_after_add_to_cart_form');
	return mvx_after_add_to_cart_form();
}

function wcmp_delete_coupon_action() {
	wc_deprecated_function('wcmp_delete_coupon_action', '4.0.0', 'mvx_delete_coupon_action');
	return mvx_delete_coupon_action();
}

function wcmp_clear_scheduled_event() {
	wc_deprecated_function('wcmp_clear_scheduled_event', '4.0.0', 'mvx_clear_scheduled_event');
	return mvx_clear_scheduled_event();
}

function wcmp_spmv_excluded_products_map() {
	wc_deprecated_function('wcmp_spmv_excluded_products_map', '4.0.0', 'mvx_spmv_excluded_products_map');
	return mvx_spmv_excluded_products_map();
}

function wcmp_spmv_product_meta_update() {
	wc_deprecated_function('wcmp_spmv_product_meta_update', '4.0.0', 'mvx_spmv_product_meta_update');
	return mvx_spmv_product_meta_update();
}

function wcmp_reset_product_mapping_data($map_id) {
	wc_deprecated_function('wcmp_reset_product_mapping_data', '4.0.0', 'mvx_reset_product_mapping_data');
	return mvx_reset_product_mapping_data($map_id);
}

function wcmp_orders_migration() {
	wc_deprecated_function('wcmp_orders_migration', '4.0.0', 'mvx_orders_migration');
	return mvx_orders_migration();
}

function wcmp_email_classes($emails) {
	wc_deprecated_function('wcmp_email_classes', '4.0.0', 'mvx_email_classes');
	return mvx_email_classes($emails);
}

function wcmp_settings_email($settings) {
	wc_deprecated_function('wcmp_settings_email', '4.0.0', 'mvx_settings_email');
	return mvx_settings_email($settings);
}

function wcmp_email_footer() {
	wc_deprecated_function('wcmp_email_footer', '4.0.0', 'mvx_email_footer');
	return mvx_email_footer();
}

function wcmp_vendor_messages_customer_support( $order, $sent_to_admin = false, $plain_text = false ) {
	wc_deprecated_function('wcmp_vendor_messages_customer_support', '4.0.0', 'mvx_vendor_messages_customer_support');
	return mvx_vendor_messages_customer_support( $order, $sent_to_admin = false, $plain_text = false );
}

function wcmp_parse_request() {
	wc_deprecated_function('wcmp_parse_request', '4.0.0', 'mvx_parse_request');
	return mvx_parse_request();
}

function wcmp_pre_get_posts($q) {
	wc_deprecated_function('wcmp_pre_get_posts', '4.0.0', 'mvx_pre_get_posts');
	return mvx_pre_get_posts($q);
}

function wcmp_save_extra_register_fields($customer_id) {
	wc_deprecated_function('wcmp_save_extra_register_fields', '4.0.0', 'mvx_save_extra_register_fields');
	return mvx_save_extra_register_fields($customer_id);
}

function wcmp_validate_extra_register_fields($username, $email, $validation_errors) {
	wc_deprecated_function('wcmp_validate_extra_register_fields', '4.0.0', 'mvx_validate_extra_register_fields');
	return mvx_validate_extra_register_fields($username, $email, $validation_errors);
}

function wcmp_vendor_register_form_callback() {
	wc_deprecated_function('wcmp_vendor_register_form_callback', '4.0.0', 'mvx_vendor_register_form_callback');
	return mvx_vendor_register_form_callback();
}

function wcmp_split_shipping_packages($packages) {
	wc_deprecated_function('wcmp_split_shipping_packages', '4.0.0', 'mvx_split_shipping_packages');
	return mvx_split_shipping_packages($packages);
}

function wcmp_checkout_order_processed($order_id, $order_posted, $order) {
	wc_deprecated_function('wcmp_checkout_order_processed', '4.0.0', 'mvx_checkout_order_processed');
	return mvx_checkout_order_processed($order_id, $order_posted, $order);
}

function wcmp_dequeue_global_style() {
	wc_deprecated_function('wcmp_dequeue_global_style', '4.0.0', 'mvx_dequeue_global_style');
	return mvx_dequeue_global_style();
}

function wcmp_vendor_dashboard_template($page_template) {
	wc_deprecated_function('wcmp_vendor_dashboard_template', '4.0.0', 'mvx_vendor_dashboard_template');
	return mvx_vendor_dashboard_template($page_template);
}

function wcmp_store_visitors_stats() {
	wc_deprecated_function('wcmp_store_visitors_stats', '4.0.0', 'mvx_store_visitors_stats');
	return mvx_store_visitors_stats();
}

function wcmp_shipping_zone_same_region_criteria( $criteria, $package, $postcode_locations ) {
	wc_deprecated_function('wcmp_shipping_zone_same_region_criteria', '4.0.0', 'mvx_shipping_zone_same_region_criteria');
	return mvx_shipping_zone_same_region_criteria( $criteria, $package, $postcode_locations );
}

function wcmp_store_page_wpml_language_switcher( $languages ) {
	wc_deprecated_function('wcmp_store_page_wpml_language_switcher', '4.0.0', 'mvx_store_page_wpml_language_switcher');
	return mvx_store_page_wpml_language_switcher( $languages );
}

function wcmp_vendor_shop_page_reviews_endpoint( $store_id, $query_vars_name ) {
	wc_deprecated_function('wcmp_vendor_shop_page_reviews_endpoint', '4.0.0', 'mvx_vendor_shop_page_reviews_endpoint');
	return mvx_vendor_shop_page_reviews_endpoint( $store_id, $query_vars_name );
}

function wcmp_vendor_shop_page_policies_endpoint( $store_id, $query_vars_name ) {
	wc_deprecated_function('wcmp_vendor_shop_page_policies_endpoint', '4.0.0', 'mvx_vendor_shop_page_policies_endpoint');
	return mvx_vendor_shop_page_policies_endpoint( $store_id, $query_vars_name );
}

function wcmp_vendor_page_query_vars() {
	wc_deprecated_function('wcmp_vendor_page_query_vars', '4.0.0', 'mvx_vendor_page_query_vars');
	return mvx_vendor_page_query_vars();
}

function wcmp_sidebar_init() {
	wc_deprecated_function('wcmp_sidebar_init', '4.0.0', 'mvx_sidebar_init');
	return mvx_sidebar_init();
}

function wcmp_sidebar() {
	wc_deprecated_function('wcmp_sidebar', '4.0.0', 'mvx_sidebar');
	return mvx_sidebar();
}

function wcmp_after_main_content() {
	wc_deprecated_function('wcmp_after_main_content', '4.0.0', 'mvx_after_main_content');
	return mvx_after_main_content();
}

function wcmp_store_tab_widget_contents() {
	wc_deprecated_function('wcmp_store_tab_widget_contents', '4.0.0', 'mvx_store_tab_widget_contents');
	return mvx_store_tab_widget_contents();
}

function wcmp_get_store_tabs( $store_id ) {
	wc_deprecated_function('wcmp_get_store_tabs', '4.0.0', 'mvx_get_store_tabs');
	return mvx_get_store_tabs( $store_id );
}

function wcmp_get_policies_url( $user_id ) {
	wc_deprecated_function('wcmp_get_policies_url', '4.0.0', 'mvx_get_policies_url');
	return mvx_get_policies_url( $user_id );
}

function wcmp_get_review_url( $user_id ) {
	wc_deprecated_function('wcmp_get_review_url', '4.0.0', 'mvx_get_review_url');
	return mvx_get_review_url( $user_id );
}

function wcmp_shop_product_callback() {
	wc_deprecated_function('wcmp_shop_product_callback', '4.0.0', 'mvx_shop_product_callback');
	return mvx_shop_product_callback();
}

function wcmp_store_widget_contents() {
	wc_deprecated_function('wcmp_store_widget_contents', '4.0.0', 'mvx_store_widget_contents');
	return mvx_store_widget_contents();
}

function wcmp_customer_followers_vendor($items) {
	wc_deprecated_function('wcmp_customer_followers_vendor', '4.0.0', 'mvx_customer_followers_vendor');
	return mvx_customer_followers_vendor($items);
}

function wcmp_customer_followers_vendor_callback() {
	wc_deprecated_function('wcmp_customer_followers_vendor_callback', '4.0.0', 'mvx_customer_followers_vendor_callback');
	return mvx_customer_followers_vendor_callback();
}

function wcmp_checkout_user_location_save( $order_id ) {
	wc_deprecated_function('wcmp_checkout_user_location_save', '4.0.0', 'mvx_checkout_user_location_save');
	return mvx_checkout_user_location_save( $order_id );
}

function wcmp_checkout_user_location_session_set( $post_data_raw ) {
	wc_deprecated_function('wcmp_checkout_user_location_session_set', '4.0.0', 'mvx_checkout_user_location_session_set');
	return mvx_checkout_user_location_session_set( $post_data_raw );
}

function wcmp_checkout_user_location_fields( $fields ) {
	wc_deprecated_function('wcmp_checkout_user_location_fields', '4.0.0', 'mvx_checkout_user_location_fields');
	return mvx_checkout_user_location_fields( $fields );
}

function wcmp_checkout_user_location_map( $checkout ) {
	wc_deprecated_function('wcmp_checkout_user_location_map', '4.0.0', 'mvx_checkout_user_location_map');
	return mvx_checkout_user_location_map( $checkout );
}

function wcmp_commission_after_save_commission_total( $commission_id, $order ) {
	wc_deprecated_function('wcmp_commission_after_save_commission_total', '4.0.0', 'mvx_commission_after_save_commission_total');
	return mvx_commission_after_save_commission_total( $commission_id, $order );
}

function wcmp_orders_migration_order_created( $order_id, $tbl_vorder_data ) {
	wc_deprecated_function('wcmp_orders_migration_order_created', '4.0.0', 'mvx_orders_migration_order_created');
	return mvx_orders_migration_order_created( $order_id, $tbl_vorder_data );
}

function wcmp_create_commission_refund_after_commission_note( $commission_id, $commissions_refunded_amt, $refund_id, $order ) {
	wc_deprecated_function('wcmp_create_commission_refund_after_commission_note', '4.0.0', 'mvx_create_commission_refund_after_commission_note');
	return mvx_create_commission_refund_after_commission_note( $commission_id, $commissions_refunded_amt, $refund_id, $order );
}

function wcmp_transaction_update_meta_data( $commission_status, $transaction_id, $vendor ) {
	wc_deprecated_function('wcmp_transaction_update_meta_data', '4.0.0', 'mvx_transaction_update_meta');
	return mvx_transaction_update_meta( $commission_status, $transaction_id, $vendor );
}

function wcmp_shop_order_columns($columns) {
	wc_deprecated_function('wcmp_shop_order_columns', '4.0.0', 'mvx_shop_order_columns');
	return mvx_shop_order_columns($columns);
}

function wcmp_show_shop_order_columns($column, $post_id) {
	wc_deprecated_function('wcmp_show_shop_order_columns', '4.0.0', 'mvx_show_shop_order_columns');
	return mvx_show_shop_order_columns($column, $post_id);
}

function wcmp_create_orders($order_id, $posted_data, $order, $backend = false) {
	wc_deprecated_function('wcmp_create_orders', '4.0.0', 'mvx_create_orders');
	return mvx_create_orders($order_id, $posted_data, $order, $backend = false);
}

function wcmp_create_orders_from_backend( $order_id, $items ) {
	wc_deprecated_function('wcmp_create_orders_from_backend', '4.0.0', 'mvx_create_orders_from_backend');
	return mvx_create_orders_from_backend( $order_id, $items );
}

function wcmp_manually_create_order_item_and_suborder( $order_id = 0, $items = '', $is_sub_create = false ) {
	wc_deprecated_function('wcmp_manually_create_order_item_and_suborder', '4.0.0', 'mvx_manually_create_order_item_and_suborder');
	return mvx_manually_create_order_item_and_suborder( $order_id = 0, $items = '', $is_sub_create = false );
}

function wcmp_create_orders_via_rest_callback( $order, $request, $creating ) {
	wc_deprecated_function('wcmp_create_orders_via_rest_callback', '4.0.0', 'mvx_create_orders_via_rest_callback');
	return mvx_create_orders_via_rest_callback( $order, $request, $creating );
}

function wcmp_parent_order_to_vendor_order_status_synchronization($order_id, $old_status, $new_status) {
	wc_deprecated_function('wcmp_parent_order_to_vendor_order_status_synchronization', '4.0.0', 'mvx_parent_order_to_vendor_order_status_synchronization');
	return mvx_parent_order_to_vendor_order_status_synchronization($order_id, $old_status, $new_status);
}

function wcmp_vendor_order_to_parent_order_status_synchronization($order_id, $old_status, $new_status) {
	wc_deprecated_function('wcmp_vendor_order_to_parent_order_status_synchronization', '4.0.0', 'mvx_vendor_order_to_parent_order_status_synchronization');
	return mvx_vendor_order_to_parent_order_status_synchronization($order_id, $old_status, $new_status);
}

function wcmp_check_order_awaiting_payment() {
	wc_deprecated_function('wcmp_check_order_awaiting_payment', '4.0.0', 'mvx_check_order_awaiting_payment');
	return mvx_check_order_awaiting_payment();
}

function wcmp_order_refunded($order_id, $parent_refund_id) {
	wc_deprecated_function('wcmp_order_refunded', '4.0.0', 'mvx_order_refunded');
	return mvx_order_refunded($order_id, $parent_refund_id);
}

function wcmp_refund_deleted($refund_id, $parent_order_id) {
	wc_deprecated_function('wcmp_refund_deleted', '4.0.0', 'mvx_refund_deleted');
	return mvx_refund_deleted($refund_id, $parent_order_id);
}

function wcmp_vendor_order_backend_restriction() {
	wc_deprecated_function('wcmp_vendor_order_backend_restriction', '4.0.0', 'mvx_vendor_order_backend_restriction');
	return mvx_vendor_order_backend_restriction();
}

function wcmp_frontend_enqueue_scripts() {
	wc_deprecated_function('wcmp_frontend_enqueue_scripts', '4.0.0', 'mvx_frontend_enqueue_scripts');
	return mvx_frontend_enqueue_scripts();
}

function wcmp_vendor_order_status_changed_actions( $order_id, $old_status, $new_status ) {
	wc_deprecated_function('wcmp_vendor_order_status_changed_actions', '4.0.0', 'mvx_vendor_order_status_changed_actions');
	return mvx_vendor_order_status_changed_actions( $order_id, $old_status, $new_status );
}

function wcmp_exclude_suborders_from_rest_api_call( $args, $request ) {
	wc_deprecated_function('wcmp_exclude_suborders_from_rest_api_call', '4.0.0', 'mvx_exclude_suborders_from_rest_api_call');
	return mvx_exclude_suborders_from_rest_api_call( $args, $request );
}

function wcmp_refund_btn_customer_my_account( $order ) {
	wc_deprecated_function('wcmp_refund_btn_customer_my_account', '4.0.0', 'mvx_refund_btn_customer_my_account');
	return mvx_refund_btn_customer_my_account( $order );
}

function wcmp_handler_cust_requested_refund() {
	wc_deprecated_function('wcmp_handler_cust_requested_refund', '4.0.0', 'mvx_handler_cust_requested_refund');
	return mvx_handler_cust_requested_refund();
}

function wcmp_refund_order_status_customer_meta() {
	wc_deprecated_function('wcmp_refund_order_status_customer_meta', '4.0.0', 'mvx_refund_order_status_customer_meta');
	return mvx_refund_order_status_customer_meta();
}

function wcmp_order_customer_refund_dd() {
	wc_deprecated_function('wcmp_order_customer_refund_dd', '4.0.0', 'mvx_order_customer_refund_dd');
	return mvx_order_customer_refund_dd();
}

function wcmp_refund_order_status_save( $post_id ) {
	wc_deprecated_function('wcmp_refund_order_status_save', '4.0.0', 'mvx_refund_order_status_save');
	return mvx_refund_order_status_save( $post_id );
}

function wcmp_suborder_hide( $args, $request ) {
	wc_deprecated_function('wcmp_suborder_hide', '4.0.0', 'mvx_suborder_hide');
	return mvx_suborder_hide( $args, $request );
}

function wcmp_mark_commission_paid($post_ids) {
	wc_deprecated_function('wcmp_mark_commission_paid', '4.0.0', 'mvx_mark_commission_paid');
	return mvx_mark_commission_paid($post_ids);
}

function wcmp_product_qna_delete_question( $ques_ID ) {
	wc_deprecated_function('wcmp_product_qna_delete_question', '4.0.0', 'mvx_product_qna_delete_question');
	return mvx_product_qna_delete_question( $ques_ID );
}

function wcmp_spmv_bulk_quick_edit_save_post( $product ) {
	wc_deprecated_function('wcmp_spmv_bulk_quick_edit_save_post', '4.0.0', 'mvx_spmv_bulk_quick_edit_save_post');
	return mvx_spmv_bulk_quick_edit_save_post( $product );
}

function wcmp_product_duplicate_update_meta($duplicate, $product) {
	wc_deprecated_function('wcmp_product_duplicate_update_meta', '4.0.0', 'mvx_product_duplicate_update_meta');
	return mvx_product_duplicate_update_meta($duplicate, $product);
}

function wcmp_edit_product_footer() {
	wc_deprecated_function('wcmp_edit_product_footer', '4.0.0', 'mvx_edit_product_footer');
	return mvx_edit_product_footer();
}

function wcmp_filter_product_category($tax_query) {
	wc_deprecated_function('wcmp_filter_product_category', '4.0.0', 'mvx_filter_product_category');
	return mvx_filter_product_category($tax_query);
}

function wcmp_delete_product_action() {
	wc_deprecated_function('wcmp_delete_product_action', '4.0.0', 'mvx_delete_product_action');
	return mvx_delete_product_action();
}

function wcmp_customer_questions_and_answers_tab($tabs) {
	wc_deprecated_function('wcmp_customer_questions_and_answers_tab', '4.0.0', 'mvx_customer_questions_and_answers_tab');
	return mvx_customer_questions_and_answers_tab($tabs);
}

function wcmp_customer_questions_and_answers_content() {
	wc_deprecated_function('wcmp_customer_questions_and_answers_content', '4.0.0', 'mvx_customer_questions_and_answers_content');
	return mvx_customer_questions_and_answers_content();
}

function wcmp_gtin_product_option() {
	wc_deprecated_function('wcmp_gtin_product_option', '4.0.0', 'mvx_gtin_product_option');
	return mvx_gtin_product_option();
}

function wcmp_save_gtin_product_option( $product_id ) {
	wc_deprecated_function('wcmp_save_gtin_product_option', '4.0.0', 'mvx_save_gtin_product_option');
	return mvx_save_gtin_product_option( $product_id );
}

function wcmp_gtin_product_search( $query ) {
	wc_deprecated_function('wcmp_gtin_product_search', '4.0.0', 'mvx_gtin_product_search');
	return mvx_gtin_product_search( $query );
}

function wcmp_gtin_get_search_query_vars() {
	wc_deprecated_function('wcmp_gtin_get_search_query_vars', '4.0.0', 'mvx_gtin_get_search_query_vars');
	return mvx_gtin_get_search_query_vars();
}

function wcmp_get_product_terms_html_selected_terms( $terms, $taxonomy = '', $id = '' ) {
	wc_deprecated_function('wcmp_get_product_terms_html_selected_terms', '4.0.0', 'mvx_get_product_terms_html_selected_terms');
	return mvx_get_product_terms_html_selected_terms( $terms, $taxonomy = '', $id = '' );
}

function wcmp_product_cat_hierarchy_meta_box() {
	wc_deprecated_function('wcmp_product_cat_hierarchy_meta_box', '4.0.0', 'mvx_product_cat_hierarchy_meta_box');
	return mvx_product_cat_hierarchy_meta_box();
}

function wcmp_product_duplicate_before_save($duplicate, $product) {
	wc_deprecated_function('wcmp_product_duplicate_before_save', '4.0.0', 'mvx_product_duplicate_before_save');
	return mvx_product_duplicate_before_save($duplicate, $product);
}

function wcmp_vendor_list_rating_rating_value($vendor_term_id, $vendor_id) {
	wc_deprecated_function('wcmp_vendor_list_rating_rating_value', '4.0.0', 'mvx_vendor_list_rating_rating_value');
	return mvx_vendor_list_rating_rating_value($vendor_term_id, $vendor_id);
}

function wcmp_review_rating_link($item_id, $item, $order) {
	wc_deprecated_function('wcmp_review_rating_link', '4.0.0', 'mvx_review_rating_link');
	return mvx_review_rating_link($item_id, $item, $order);
}

function wcmp_seller_review_rating_form() {
	wc_deprecated_function('wcmp_seller_review_rating_form', '4.0.0', 'mvx_seller_review_rating_form');
	return mvx_seller_review_rating_form();
}

function wcmp_comment_vendor_rating_callback($comment) {
	wc_deprecated_function('wcmp_comment_vendor_rating_callback', '4.0.0', 'mvx_comment_vendor_rating_callback');
	return mvx_comment_vendor_rating_callback($comment);
}

function wcmp_vendor_dashboard_shortcode() {
	wc_deprecated_function('wcmp_vendor_dashboard_shortcode', '4.0.0', 'mvx_vendor_dashboard_shortcode');
	return mvx_vendor_dashboard_shortcode();
}

function wcmp_vendor_registration_shortcode() {
	wc_deprecated_function('wcmp_vendor_registration_shortcode', '4.0.0', 'mvx_vendor_registration_shortcode');
	return mvx_vendor_registration_shortcode();
}

function wcmp_show_recent_products($atts) {
	wc_deprecated_function('wcmp_show_recent_products', '4.0.0', 'mvx_show_recent_products');
	return mvx_show_recent_products($atts);
}

function wcmp_show_products($atts) {
	wc_deprecated_function('wcmp_show_products', '4.0.0', 'mvx_show_products');
	return mvx_show_products($atts);
}

function wcmp_recent_products($atts) {
	wc_deprecated_function('wcmp_recent_products', '4.0.0', 'mvx_recent_products');
	return mvx_recent_products($atts);
}

function wcmp_show_featured_products($atts) {
	wc_deprecated_function('wcmp_show_featured_products', '4.0.0', 'mvx_show_featured_products');
	return mvx_show_featured_products($atts);
}

function wcmp_show_sale_products($atts) {
	wc_deprecated_function('wcmp_show_sale_products', '4.0.0', 'mvx_show_sale_products');
	return mvx_show_sale_products($atts);
}

function wcmp_show_top_rated_products($atts) {
	wc_deprecated_function('wcmp_show_top_rated_products', '4.0.0', 'mvx_show_top_rated_products');
	return mvx_show_top_rated_products($atts);
}

function wcmp_show_best_selling_products($atts) {
	wc_deprecated_function('wcmp_show_best_selling_products', '4.0.0', 'mvx_show_best_selling_products');
	return mvx_show_best_selling_products($atts);
}

function wcmp_show_product_category($atts) {
	wc_deprecated_function('wcmp_show_product_category', '4.0.0', 'mvx_show_product_category');
	return mvx_show_product_category($atts);
}

function wcmp_show_vendorslist($atts) {
	wc_deprecated_function('wcmp_show_vendorslist', '4.0.0', 'mvx_show_vendorslist');
	return mvx_show_vendorslist($atts);
}

function wcmp_get_the_terms($terms, $post_id, $taxonomy) {
	wc_deprecated_function('wcmp_get_the_terms', '4.0.0', 'mvx_get_the_terms');
	return mvx_get_the_terms($terms, $post_id, $taxonomy);
}

function wcmp_vendor_login($redirect, $user) {
	wc_deprecated_function('wcmp_vendor_login', '4.0.0', 'mvx_vendor_login');
	return mvx_vendor_login($redirect, $user);
}

function wcmp_woocommerce_created_customer_notification() {
	wc_deprecated_function('wcmp_woocommerce_created_customer_notification', '4.0.0', 'mvx_woocommerce_created_customer_notification');
	return mvx_woocommerce_created_customer_notification();
}

function wcmp_customer_new_account($customer_id, $new_customer_data = array(), $password_generated = false) {
	wc_deprecated_function('wcmp_customer_new_account', '4.0.0', 'mvx_customer_new_account');
	return mvx_customer_new_account($customer_id, $new_customer_data = array(), $password_generated = false);
}

function wcmp_order_emails_available($available_emails) {
	wc_deprecated_function('wcmp_order_emails_available', '4.0.0', 'mvx_order_emails_available');
	return mvx_order_emails_available($available_emails);
}

function wcmp_user_avatar_override( $avatar, $id_or_email, $size, $default, $alt, $args=array()) {
	wc_deprecated_function('wcmp_user_avatar_override', '4.0.0', 'mvx_user_avatar_override');
	return mvx_user_avatar_override( $avatar, $id_or_email, $size, $default, $alt, $args=array());
}

function wcmp_pre_user_query_filtered( $query ) {
	wc_deprecated_function('wcmp_pre_user_query_filtered', '4.0.0', 'mvx_pre_user_query_filtered');
	return mvx_pre_user_query_filtered( $query );
}

function wcmp_vendor_orders_page() {
	wc_deprecated_function('wcmp_vendor_orders_page', '4.0.0', 'mvx_vendor_orders_page');
	return mvx_vendor_orders_page();
}

function wcmp_product_options_shipping() {
	wc_deprecated_function('wcmp_product_options_shipping', '4.0.0', 'mvx_product_options_shipping');
	return mvx_product_options_shipping();
}

function wcmp_dashboard_setup() {
	wc_deprecated_function('wcmp_dashboard_setup', '4.0.0', 'mvx_dashboard_setup');
	return mvx_dashboard_setup();
}

function wcmp_add_dashboard_widget($widget_id, $widget_title, $callback, $context = 'normal', $callback_args = null, $args = array()) {
	wc_deprecated_function('wcmp_add_dashboard_widget', '4.0.0', 'mvx_add_dashboard_widget');
	return mvx_add_dashboard_widget($widget_id, $widget_title, $callback, $context = 'normal', $callback_args = null, $args = array());
}

function wcmp_vendor_stats_reports($args = array()) {
	wc_deprecated_function('wcmp_vendor_stats_reports', '4.0.0', 'mvx_vendor_stats_reports');
	return mvx_vendor_stats_reports($args = array());
}

function wcmp_vendor_pending_shipping($args = array()) {
	wc_deprecated_function('wcmp_vendor_pending_shipping', '4.0.0', 'mvx_vendor_pending_shipping');
	return mvx_vendor_pending_shipping($args = array());
}

function wcmp_customer_review() {
	wc_deprecated_function('wcmp_customer_review', '4.0.0', 'mvx_customer_review');
	return mvx_customer_review();
}

function wcmp_vendor_followers() {
	wc_deprecated_function('wcmp_vendor_followers', '4.0.0', 'mvx_vendor_followers');
	return mvx_vendor_followers();
}

function wcmp_vendor_product_stats($args = array()) {
	wc_deprecated_function('wcmp_vendor_product_stats', '4.0.0', 'mvx_vendor_product_stats');
	return mvx_vendor_product_stats($args = array());
}

function wcmp_vendor_product_sales_report() {
	wc_deprecated_function('wcmp_vendor_product_sales_report', '4.0.0', 'mvx_vendor_product_sales_report');
	return mvx_vendor_product_sales_report();
}

function wcmp_vendor_transaction_details() {
	wc_deprecated_function('wcmp_vendor_transaction_details', '4.0.0', 'mvx_vendor_transaction_details');
	return mvx_vendor_transaction_details();
}

function wcmp_vendor_products_cust_qna() {
	wc_deprecated_function('wcmp_vendor_products_cust_qna', '4.0.0', 'mvx_vendor_products_cust_qna');
	return mvx_vendor_products_cust_qna();
}

function wcmp_vendor_visitors_map() {
	wc_deprecated_function('wcmp_vendor_visitors_map', '4.0.0', 'mvx_vendor_visitors_map');
	return mvx_vendor_visitors_map();
}

function wcmp_dashboard_setup_updater() {
	wc_deprecated_function('wcmp_dashboard_setup_updater', '4.0.0', 'mvx_dashboard_setup_updater');
	return mvx_dashboard_setup_updater();
}

function wcmp_vendor_dashboard_add_product_url( $url ) {
	wc_deprecated_function('wcmp_vendor_dashboard_add_product_url', '4.0.0', 'mvx_vendor_dashboard_add_product_url');
	return mvx_vendor_dashboard_add_product_url( $url );
}

function wcmp_setup_store_setup_save() {
	wc_deprecated_function('wcmp_setup_store_setup_save', '4.0.0', 'mvx_setup_store_setup_save');
	return mvx_setup_store_setup_save();
}

function wcmp_setup_payment_save() {
	wc_deprecated_function('wcmp_setup_payment_save', '4.0.0', 'mvx_setup_payment_save');
	return mvx_setup_payment_save();
}

function wcmp_store_setup_ready() {
	wc_deprecated_function('wcmp_store_setup_ready', '4.0.0', 'mvx_store_setup_ready');
	return mvx_store_setup_ready();
}

function wcmp_get_vendor_part_from_order($order, $vendor_term_id) {
	wc_deprecated_function('wcmp_get_vendor_part_from_order', '4.0.0', 'mvx_get_vendor_part_from_order');
	return mvx_get_vendor_part_from_order($order, $vendor_term_id);
}

function wcmp_vendor_get_total_amount_due() {
	wc_deprecated_function('wcmp_vendor_get_total_amount_due', '4.0.0', 'mvx_vendor_get_total_amount_due');
	return mvx_vendor_get_total_amount_due();
}

function wcmp_vendor_transaction() {
	wc_deprecated_function('wcmp_vendor_transaction', '4.0.0', 'mvx_vendor_transaction');
	return mvx_vendor_transaction();
}

function wcmp_vendor_get_order_item_totals($order, $term_id) {
	wc_deprecated_function('wcmp_vendor_get_order_item_totals', '4.0.0', 'mvx_vendor_get_order_item_totals');
	return mvx_vendor_get_order_item_totals($order, $term_id);
}

function wcmp_create_vendor_dashboard_navigation( $args = array() ) {
	wc_deprecated_function('wcmp_create_vendor_dashboard_navigation', '4.0.0', 'mvx_create_vendor_dashboard_navigation');
	return mvx_create_vendor_dashboard_navigation( $args = array() );
}

function wcmp_get_vendor_dashboard_navigation() {
	wc_deprecated_function('wcmp_get_vendor_dashboard_navigation', '4.0.0', 'mvx_get_vendor_dashboard_navigation');
	return mvx_get_vendor_dashboard_navigation();
}

function wcmp_create_vendor_dashboard_content() {
	wc_deprecated_function('wcmp_create_vendor_dashboard_content', '4.0.0', 'mvx_create_vendor_dashboard_content');
	return mvx_create_vendor_dashboard_content();
}

function wcmp_create_vendor_dashboard_breadcrumbs( $current_endpoint, $nav = array(), $firstLevel = true ) {
	wc_deprecated_function('wcmp_create_vendor_dashboard_breadcrumbs', '4.0.0', 'mvx_create_vendor_dashboard_breadcrumbs');
	return mvx_create_vendor_dashboard_breadcrumbs( $current_endpoint, $nav = array(), $firstLevel = true );
}

function wcmp_vendor_dashboard_vendor_announcements_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_announcements_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_announcements_endpoint');
	return mvx_vendor_dashboard_vendor_announcements_endpoint();
}

function wcmp_vendor_dashboard_storefront_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_storefront_endpoint', '4.0.0', 'mvx_vendor_dashboard_storefront_endpoint');
	return mvx_vendor_dashboard_storefront_endpoint();
}

function wcmp_vendor_dashboard_profile_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_profile_endpoint', '4.0.0', 'mvx_vendor_dashboard_profile_endpoint');
	return mvx_vendor_dashboard_profile_endpoint();
}

function wcmp_vendor_dashboard_vendor_policies_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_policies_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_policies_endpoint');
	return mvx_vendor_dashboard_vendor_policies_endpoint();
}

function wcmp_vendor_dashboard_vendor_billing_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_billing_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_billing_endpoint');
	return mvx_vendor_dashboard_vendor_billing_endpoint();
}

function wcmp_vendor_dashboard_vendor_shipping_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_shipping_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_shipping_endpoint');
	return mvx_vendor_dashboard_vendor_shipping_endpoint();
}

function wcmp_vendor_dashboard_vendor_report_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_report_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_report_endpoint');
	return mvx_vendor_dashboard_vendor_report_endpoint();
}

function wcmp_vendor_dashboard_banking_overview_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_banking_overview_endpoint', '4.0.0', 'mvx_vendor_dashboard_banking_overview_endpoint');
	return mvx_vendor_dashboard_banking_overview_endpoint();
}

function wcmp_vendor_dashboard_add_product_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_add_product_endpoint', '4.0.0', 'mvx_vendor_dashboard_add_product_endpoint');
	return mvx_vendor_dashboard_add_product_endpoint();
}

function wcmp_vendor_dashboard_edit_product_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_edit_product_endpoint', '4.0.0', 'mvx_vendor_dashboard_edit_product_endpoint');
	return mvx_vendor_dashboard_edit_product_endpoint();
}

function wcmp_vendor_dashboard_products_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_products_endpoint', '4.0.0', 'mvx_vendor_dashboard_products_endpoint');
	return mvx_vendor_dashboard_products_endpoint();
}

function wcmp_vendor_dashboard_add_coupon_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_add_coupon_endpoint', '4.0.0', 'mvx_vendor_dashboard_add_coupon_endpoint');
	return mvx_vendor_dashboard_add_coupon_endpoint();
}

function wcmp_vendor_dashboard_coupons_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_coupons_endpoint', '4.0.0', 'mvx_vendor_dashboard_coupons_endpoint');
	return mvx_vendor_dashboard_coupons_endpoint();
}

function wcmp_vendor_dashboard_vendor_orders_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_orders_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_orders_endpoint');
	return mvx_vendor_dashboard_vendor_orders_endpoint();
}

function wcmp_vendor_dashboard_vendor_withdrawal_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_withdrawal_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_withdrawal_endpoint');
	return mvx_vendor_dashboard_vendor_withdrawal_endpoint();
}

function wcmp_vendor_dashboard_transaction_details_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_transaction_details_endpoint', '4.0.0', 'mvx_vendor_dashboard_transaction_details_endpoint');
	return mvx_vendor_dashboard_transaction_details_endpoint();
}

function wcmp_vendor_dashboard_vendor_knowledgebase_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_knowledgebase_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_knowledgebase_endpoint');
	return mvx_vendor_dashboard_vendor_knowledgebase_endpoint();
}

function wcmp_vendor_dashboard_vendor_tools_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_vendor_tools_endpoint', '4.0.0', 'mvx_vendor_dashboard_vendor_tools_endpoint');
	return mvx_vendor_dashboard_vendor_tools_endpoint();
}

function wcmp_vendor_dashboard_products_qna_endpoint() {
	wc_deprecated_function('wcmp_vendor_dashboard_products_qna_endpoint', '4.0.0', 'mvx_vendor_dashboard_products_qna_endpoint');
	return mvx_vendor_dashboard_products_qna_endpoint();
}

function wcmp_vendor_dashboard_endpoint_title( $title ) {
	wc_deprecated_function('wcmp_vendor_dashboard_endpoint_title', '4.0.0', 'mvx_vendor_dashboard_endpoint_title');
	return mvx_vendor_dashboard_endpoint_title( $title );
}

function wcmp_vendor_dashboard_menu_vendor_policies_capability( $cap ) {
	wc_deprecated_function('wcmp_vendor_dashboard_menu_vendor_policies_capability', '4.0.0', 'mvx_vendor_dashboard_menu_vendor_policies_capability');
	return mvx_vendor_dashboard_menu_vendor_policies_capability( $cap );
}

function wcmp_vendor_dashboard_menu_vendor_withdrawal_capability( $cap ) {
	wc_deprecated_function('wcmp_vendor_dashboard_menu_vendor_withdrawal_capability', '4.0.0', 'mvx_vendor_dashboard_menu_vendor_withdrawal_capability');
	return mvx_vendor_dashboard_menu_vendor_withdrawal_capability( $cap );
}

function wcmp_vendor_dashboard_menu_vendor_shipping_capability( $cap ) {
	wc_deprecated_function('wcmp_vendor_dashboard_menu_vendor_shipping_capability', '4.0.0', 'mvx_vendor_dashboard_menu_vendor_shipping_capability');
	return mvx_vendor_dashboard_menu_vendor_shipping_capability( $cap );
}

function wcmp_add_theme_support() {
	wc_deprecated_function('wcmp_add_theme_support', '4.0.0', 'mvx_add_theme_support');
	return mvx_add_theme_support();
}

function wcmp_register_store_sidebar() {
	wc_deprecated_function('wcmp_register_store_sidebar', '4.0.0', 'mvx_register_store_sidebar');
	return mvx_register_store_sidebar();
}

function wcmp_rm_meta_boxes() {
	wc_deprecated_function('wcmp_rm_meta_boxes', '4.0.0', 'mvx_rm_meta_boxes');
	return mvx_rm_meta_boxes();
}

function wcmp_admin_init() {
	wc_deprecated_function('wcmp_admin_init', '4.0.0', 'mvx_admin_init');
	return mvx_admin_init();
}

function wcmp_remove_woocommerce_admin_from_vendor() {
	wc_deprecated_function('wcmp_remove_woocommerce_admin_from_vendor', '4.0.0', 'mvx_remove_woocommerce_admin_from_vendor');
	return mvx_remove_woocommerce_admin_from_vendor();
}

function wcmp_get_script_data($handle, $default) {
	wc_deprecated_function('wcmp_get_script_data', '4.0.0', 'mvx_get_script_content');
	return mvx_get_script_content($handle, $default);
}

function wcmp_stripe_phpversion_required_notice() {
	wc_deprecated_function('wcmp_stripe_phpversion_required_notice', '4.0.0', 'mvx_stripe_phpversion_required_notice');
	return mvx_stripe_phpversion_required_notice();
}

function wcmp_stripe_curl_required_notice() {
	wc_deprecated_function('wcmp_stripe_curl_required_notice', '4.0.0', 'mvx_stripe_curl_required_notice');
	return mvx_stripe_curl_required_notice();
}

function wcmp_stripe_mbstring_required_notice() {
	wc_deprecated_function('wcmp_stripe_mbstring_required_notice', '4.0.0', 'mvx_stripe_mbstring_required_notice');
	return mvx_stripe_mbstring_required_notice();
}

function wcmp_stripe_json_required_notice() {
	wc_deprecated_function('wcmp_stripe_json_required_notice', '4.0.0', 'mvx_stripe_json_required_notice');
	return mvx_stripe_json_required_notice();
}

function wcmp_plugin_update_message($args) {
	wc_deprecated_function('wcmp_plugin_update_message', '4.0.0', 'mvx_plugin_update_message');
	return mvx_plugin_update_message($args);
}

function wcmp_paid_commission_from_previous_marketplace() {
	wc_deprecated_function('wcmp_paid_commission_from_previous_marketplace', '4.0.0', 'mvx_paid_commission_from_previous_marketplace');
	return mvx_paid_commission_from_previous_marketplace();
}

function wcmp_admin_menus() {
	wc_deprecated_function('wcmp_admin_menus', '4.0.0', 'mvx_admin_menus');
	return mvx_admin_menus();
}

function wcmp_migration() {
	wc_deprecated_function('wcmp_migration', '4.0.0', 'mvx_migration');
	return mvx_migration();
}

function wcmp_migration_header() {
	wc_deprecated_function('wcmp_migration_header', '4.0.0', 'mvx_migration_header');
	return mvx_migration_header();
}

function wcmp_migration_steps() {
	wc_deprecated_function('wcmp_migration_steps', '4.0.0', 'mvx_migration_steps');
	return mvx_migration_steps();
}

function wcmp_migration_content() {
	wc_deprecated_function('wcmp_migration_content', '4.0.0', 'mvx_migration_content');
	return mvx_migration_content();
}

function wcmp_migration_introduction() {
	wc_deprecated_function('wcmp_migration_introduction', '4.0.0', 'mvx_migration_introduction');
	return mvx_migration_introduction();
}

function wcmp_migration_first_step ($next_step_link) {
	wc_deprecated_function('wcmp_migration_first_step ', '4.0.0', 'mvx_migration_first_step ');
	return mvx_migration_first_step ($next_step_link);
}

function wcmp_migration_store_process() {
	wc_deprecated_function('wcmp_migration_store_process', '4.0.0', 'mvx_migration_store_process');
	return mvx_migration_store_process();
}

function wcmp_migration_third_step($get_next_step_link) {
	wc_deprecated_function('wcmp_migration_third_step', '4.0.0', 'mvx_migration_third_step');
	return mvx_migration_third_step($get_next_step_link);
}

function wcmp_migration_complete() {
	wc_deprecated_function('wcmp_migration_complete', '4.0.0', 'mvx_migration_complete');
	return mvx_migration_complete();
}

function wcmp_migration_footer() {
	wc_deprecated_function('wcmp_migration_footer', '4.0.0', 'mvx_migration_footer');
	return mvx_migration_footer();
}

function wcmp_get_products_by_vendor( $vendor_id = 0, $post_status = 'any', $custom_args = array() ) {
	wc_deprecated_function('wcmp_get_products_by_vendor', '4.0.0', 'mvx_get_products_by_vendor');
	return mvx_get_products_by_vendor( $vendor_id = 0, $post_status = 'any', $custom_args = array() );
}

function wcmp_is_marketplace() {
	wc_deprecated_function('wcmp_is_marketplace', '4.0.0', 'mvx_is_marketplace');
	return mvx_is_marketplace();
}

function wcmp_set_product_type_options( $option ) {
	wc_deprecated_function('wcmp_set_product_type_options', '4.0.0', 'mvx_set_product_type_options');
	return mvx_set_product_type_options( $option );
}

function wcmp_hide_admin_shipping( $rates, $package ) {
	wc_deprecated_function('wcmp_hide_admin_shipping', '4.0.0', 'mvx_hide_admin_shipping');
	return mvx_hide_admin_shipping( $rates, $package );
}

function wcmp_product_vendor_plugin_create_page($slug, $option, $page_title = '', $page_content = '', $post_parent = 0) {
	wc_deprecated_function('wcmp_product_vendor_plugin_create_page', '4.0.0', 'mvx_product_vendor_plugin_create_page');
	return mvx_product_vendor_plugin_create_page($slug, $option, $page_title = '', $page_content = '', $post_parent = 0);
}

function wcmp_product_vendor_plugin_create_pages() {
	wc_deprecated_function('wcmp_product_vendor_plugin_create_pages', '4.0.0', 'mvx_product_vendor_plugin_create_pages');
	return mvx_product_vendor_plugin_create_pages();
}

function wcmp_plugin_tables_install() {
	wc_deprecated_function('wcmp_plugin_tables_install', '4.0.0', 'mvx_plugin_tables_install');
	return mvx_plugin_tables_install();
}

function wcmp_woo() {
	wc_deprecated_function('wcmp_woo', '4.0.0', 'mvx_woo');
	return mvx_woo();
}

function wcmp_vendor_dashboard_page_id($language_code = '', $url = false) {
	wc_deprecated_function('wcmp_vendor_dashboard_page_id', '4.0.0', 'mvx_vendor_dashboard_page_id');
	return mvx_vendor_dashboard_page_id($language_code = '', $url = false);
}

function wcmp_vendor_registration_page_id() {
	wc_deprecated_function('wcmp_vendor_registration_page_id', '4.0.0', 'mvx_vendor_registration_page_id');
	return mvx_vendor_registration_page_id();
}

function wcmp_action_links($links) {
	wc_deprecated_function('wcmp_action_links', '4.0.0', 'mvx_action_links');
	return mvx_action_links($links);
}

function wcmp_get_all_blocked_vendors() {
	wc_deprecated_function('wcmp_get_all_blocked_vendors', '4.0.0', 'mvx_get_all_blocked_vendors');
	return mvx_get_all_blocked_vendors();
}

function wcmp_check_if_another_vendor_plugin_exits() {
	wc_deprecated_function('wcmp_check_if_another_vendor_plugin_exits', '4.0.0', 'mvx_check_if_another_vendor_plugin_exits');
	return mvx_check_if_another_vendor_plugin_exits();
}

function wcmp_paid_commission_status($commission_id) {
	wc_deprecated_function('wcmp_paid_commission_status', '4.0.0', 'mvx_paid_commission_status');
	return mvx_paid_commission_status($commission_id);
}

function wcmp_rangeWeek($datestr) {
	wc_deprecated_function('wcmp_rangeWeek', '4.0.0', 'mvx_rangeWeek');
	return mvx_rangeWeek($datestr);
}

function wcmp_seller_review_enable($vendor_term_id) {
	wc_deprecated_function('wcmp_seller_review_enable', '4.0.0', 'mvx_seller_review_enable');
	return mvx_seller_review_enable($vendor_term_id);
}

function wcmp_find_user_purchased_with_vendor($user_id, $vendor_term_id) {
	wc_deprecated_function('wcmp_find_user_purchased_with_vendor', '4.0.0', 'mvx_find_user_purchased_with_vendor');
	return mvx_find_user_purchased_with_vendor($user_id, $vendor_term_id);
}

function wcmp_get_vendor_dashboard_nav_item_css_class($endpoint, $force_active = false) {
	wc_deprecated_function('wcmp_get_vendor_dashboard_nav_item_css_class', '4.0.0', 'mvx_get_vendor_dashboard_nav_item_css_class');
	return mvx_get_vendor_dashboard_nav_item_css_class($endpoint, $force_active = false);
}

function wcmp_get_vendor_dashboard_endpoint_url($endpoint, $value = '', $withvalue = false, $lang_code = '') {
	wc_deprecated_function('wcmp_get_vendor_dashboard_endpoint_url', '4.0.0', 'mvx_get_vendor_dashboard_endpoint_url');
	return mvx_get_vendor_dashboard_endpoint_url($endpoint, $value = '', $withvalue = false, $lang_code = '');
}

function wcmp_get_all_order_of_user($user_id) {
	wc_deprecated_function('wcmp_get_all_order_of_user', '4.0.0', 'mvx_get_all_order_of_user');
	return mvx_get_all_order_of_user($user_id);
}

function wcmp_review_is_from_verified_owner($comment, $vendor_term_id) {
	wc_deprecated_function('wcmp_review_is_from_verified_owner', '4.0.0', 'mvx_review_is_from_verified_owner');
	return mvx_review_is_from_verified_owner($comment, $vendor_term_id);
}

function wcmp_get_vendor_review_info($vendor_term_id, $type = 'vendor-rating' ) {
	wc_deprecated_function('wcmp_get_vendor_review_info', '4.0.0', 'mvx_get_vendor_review_info');
	return mvx_get_vendor_review_info($vendor_term_id, $type = 'vendor-rating' );
}

function wcmp_sort_by_rating_multiple_product($more_product_array) {
	wc_deprecated_function('wcmp_sort_by_rating_multiple_product', '4.0.0', 'mvx_sort_by_rating_multiple_product');
	return mvx_sort_by_rating_multiple_product($more_product_array);
}

function wcmp_remove_comments_section_from_vendor_dashboard() {
	wc_deprecated_function('wcmp_remove_comments_section_from_vendor_dashboard', '4.0.0', 'mvx_remove_comments_section_from_vendor_dashboard');
	return mvx_remove_comments_section_from_vendor_dashboard();
}

function wcmp_process_order($order_id, $order = null) {
	wc_deprecated_function('wcmp_process_order', '4.0.0', 'mvx_process_order');
	return mvx_process_order($order_id, $order = null);
}

function wcmp_get_vendor_profile_completion($vendor_id) {
	wc_deprecated_function('wcmp_get_vendor_profile_completion', '4.0.0', 'mvx_get_vendor_profile_completion');
	return mvx_get_vendor_profile_completion($vendor_id);
}

function wcmp_save_visitor_stats($vendor_id, $data) {
	wc_deprecated_function('wcmp_save_visitor_stats', '4.0.0', 'mvx_save_visitor_stats');
	return mvx_save_visitor_stats($vendor_id, $data);
}

function wcmp_get_visitor_stats($vendor_id, $query_where = '', $query_filter = '') {
	wc_deprecated_function('wcmp_get_visitor_stats', '4.0.0', 'mvx_get_visitor_stats');
	return mvx_get_visitor_stats($vendor_id, $query_where = '', $query_filter = '');
}

function wcmp_date($date) {
	wc_deprecated_function('wcmp_date', '4.0.0', 'mvx_date');
	return mvx_date($date);
}

function wcmp_get_latlng_distance($lat1, $lon1, $lat2, $lon2, $unit = 'M') {
	wc_deprecated_function('wcmp_get_latlng_distance', '4.0.0', 'mvx_get_latlng_distance');
	return mvx_get_latlng_distance($lat1, $lon1, $lat2, $lon2, $unit = 'M');
}

function wcmp_get_vendor_list_map_store_data($vendors, $request) {
	wc_deprecated_function('wcmp_get_vendor_list_map_store_data', '4.0.0', 'mvx_get_vendor_list_map_store');
	return mvx_get_vendor_list_map_store($vendors, $request);
}

function wcmp_get_vendor_specific_order_charge($order) {
	wc_deprecated_function('wcmp_get_vendor_specific_order_charge', '4.0.0', 'mvx_get_vendor_specific_order_charge');
	return mvx_get_vendor_specific_order_charge($order);
}

function wcmp_get_geocoder_components($components = array()) {
	wc_deprecated_function('wcmp_get_geocoder_components', '4.0.0', 'mvx_get_geocoder_components');
	return mvx_get_geocoder_components($components = array());
}

function wcmp_get_available_product_types() {
	wc_deprecated_function('wcmp_get_available_product_types', '4.0.0', 'mvx_get_available_product_types');
	return mvx_get_available_product_types();
}

function wcmp_spmv_products_map($data = array(), $action = 'insert') {
	wc_deprecated_function('wcmp_spmv_products_map', '4.0.0', 'mvx_spmv_products_map');
	return mvx_spmv_products_map($data = array(), $action = 'insert');
}

function wcmp_get_available_commission_types($default = array()) {
	wc_deprecated_function('wcmp_get_available_commission_types', '4.0.0', 'mvx_get_available_commission_types');
	return mvx_get_available_commission_types($default = array());
}

function wcmp_list_categories($args = array()) {
	wc_deprecated_function('wcmp_list_categories', '4.0.0', 'mvx_list_categories');
	return mvx_list_categories($args = array());
}

function wcmp_get_shipping_zone($zoneID = '') {
	wc_deprecated_function('wcmp_get_shipping_zone', '4.0.0', 'mvx_get_shipping_zone');
	return mvx_get_shipping_zone($zoneID = '');
}

function wcmp_get_shipping_methods() {
	wc_deprecated_function('wcmp_get_shipping_methods', '4.0.0', 'mvx_get_shipping_methods');
	return mvx_get_shipping_methods();
}

function wcmp_state_key_alter($value, $key) {
	wc_deprecated_function('wcmp_state_key_alter', '4.0.0', 'mvx_state_key_alter');
	return mvx_state_key_alter($value, $key);
}

function wcmp_is_allowed_vendor_shipping() {
	wc_deprecated_function('wcmp_is_allowed_vendor_shipping', '4.0.0', 'mvx_is_allowed_vendor_shipping');
	return mvx_is_allowed_vendor_shipping();
}

function wcmp_get_post_permalink_html( $id ) {
	wc_deprecated_function('wcmp_get_post_permalink_html', '4.0.0', 'mvx_get_post_permalink_html');
	return mvx_get_post_permalink_html( $id );
}

function wcmp_get_post_permalink( $id ) {
	wc_deprecated_function('wcmp_get_post_permalink', '4.0.0', 'mvx_get_post_permalink');
	return mvx_get_post_permalink( $id );
}

function wcmp_default_product_types() {
	wc_deprecated_function('wcmp_default_product_types', '4.0.0', 'mvx_default_product_types');
	return mvx_default_product_types();
}

function wcmp_get_product_types() {
	wc_deprecated_function('wcmp_get_product_types', '4.0.0', 'mvx_get_product_types');
	return mvx_get_product_types();
}

function wcmp_is_allowed_product_type() {
	wc_deprecated_function('wcmp_is_allowed_product_type', '4.0.0', 'mvx_is_allowed_product_type');
	return mvx_is_allowed_product_type();
}

function wcmp_get_product_terms_HTML( $taxonomy, $id = null, $add_cap = false, $hierarchical = true ) {
	wc_deprecated_function('wcmp_get_product_terms_HTML', '4.0.0', 'mvx_get_product_terms_HTML');
	return mvx_get_product_terms_HTML( $taxonomy, $id = null, $add_cap = false, $hierarchical = true );
}

function wcmp_generate_term_breadcrumb_html( $args = array() ) {
	wc_deprecated_function('wcmp_generate_term_breadcrumb_html', '4.0.0', 'mvx_generate_term_breadcrumb_html');
	return mvx_generate_term_breadcrumb_html( $args = array() );
}

function wcmp_get_price_to_display( $product, $args = array() ) {
	wc_deprecated_function('wcmp_get_price_to_display', '4.0.0', 'mvx_get_price_to_display');
	return mvx_get_price_to_display( $product, $args = array() );
}

function wcmp_get_commission_statuses() {
	wc_deprecated_function('wcmp_get_commission_statuses', '4.0.0', 'mvx_get_commission_statuses');
	return mvx_get_commission_statuses();
}

function wcmp_get_product_link( $product_id ) {
	wc_deprecated_function('wcmp_get_product_link', '4.0.0', 'mvx_get_product_link');
	return mvx_get_product_link( $product_id );
}

function wcmp_get_option( $key, $default_val = '', $lang_code = '' ) {
	wc_deprecated_function('wcmp_get_option', '4.0.0', 'mvx_get_option');
	return mvx_get_option( $key, $default_val = '', $lang_code = '' );
}

function wcmp_update_option( $key, $option_val ) {
	wc_deprecated_function('wcmp_update_option', '4.0.0', 'mvx_update_option');
	return mvx_update_option( $key, $option_val );
}

function wcmp_get_user_meta( $user_id, $key, $is_single = true ) {
	wc_deprecated_function('wcmp_get_user_meta', '4.0.0', 'mvx_get_user_meta');
	return mvx_get_user_meta( $user_id, $key, $is_single = true );
}

function wcmp_update_user_meta( $user_id, $key, $meta_val ) {
	wc_deprecated_function('wcmp_update_user_meta', '4.0.0', 'mvx_update_user_meta');
	return mvx_update_user_meta( $user_id, $key, $meta_val );
}

function wcmp_is_store_page() {
	wc_deprecated_function('wcmp_is_store_page', '4.0.0', 'mvx_is_store_page');
	return mvx_is_store_page();
}

function wcmp_find_shop_page_vendor() {
	wc_deprecated_function('wcmp_find_shop_page_vendor', '4.0.0', 'mvx_find_shop_page_vendor');
	return mvx_find_shop_page_vendor();
}

function wcmp_get_attachment_url( $attachment_id ) {
	wc_deprecated_function('wcmp_get_attachment_url', '4.0.0', 'mvx_get_attachment_url');
	return mvx_get_attachment_url( $attachment_id );
}

function wcmp_mapbox_api_enabled() {
	wc_deprecated_function('wcmp_mapbox_api_enabled', '4.0.0', 'mvx_mapbox_api_enabled');
	return mvx_mapbox_api_enabled();
}

function wcmp_mapbox_design_switcher() {
	wc_deprecated_function('wcmp_mapbox_design_switcher', '4.0.0', 'mvx_mapbox_design_switcher');
	return mvx_mapbox_design_switcher();
}

function wcmp_vendor_distance_by_shipping_settings( $vendor_id = 0 ) {
	wc_deprecated_function('wcmp_vendor_distance_by_shipping_settings', '4.0.0', 'mvx_vendor_distance_by_shipping_settings');
	return mvx_vendor_distance_by_shipping_settings( $vendor_id = 0 );
}

function wcmp_vendor_different_type_shipping_options( $vendor_id = 0) {
	wc_deprecated_function('wcmp_vendor_different_type_shipping_options', '4.0.0', 'mvx_vendor_different_type_shipping_options');
	return mvx_vendor_different_type_shipping_options( $vendor_id = 0);
}

function wcmp_vendor_shipping_by_country_settings( $vendor_id = 0 ) {
	wc_deprecated_function('wcmp_vendor_shipping_by_country_settings', '4.0.0', 'mvx_vendor_shipping_by_country_settings');
	return mvx_vendor_shipping_by_country_settings( $vendor_id = 0 );
}

function wcmp_vendor_list_main_wrapper() {
	wc_deprecated_function('wcmp_vendor_list_main_wrapper', '4.0.0', 'mvx_vendor_list_main_wrapper');
	return mvx_vendor_list_main_wrapper();
}

function wcmp_vendor_list_main_wrapper_end() {
	wc_deprecated_function('wcmp_vendor_list_main_wrapper_end', '4.0.0', 'mvx_vendor_list_main_wrapper_end');
	return mvx_vendor_list_main_wrapper_end();
}

function wcmp_vendor_list_map_wrapper() {
	wc_deprecated_function('wcmp_vendor_list_map_wrapper', '4.0.0', 'mvx_vendor_list_map_wrapper');
	return mvx_vendor_list_map_wrapper();
}

function wcmp_vendor_list_form_wrapper() {
	wc_deprecated_function('wcmp_vendor_list_form_wrapper', '4.0.0', 'mvx_vendor_list_form_wrapper');
	return mvx_vendor_list_form_wrapper();
}

function wcmp_vendor_list_map_filters() {
	wc_deprecated_function('wcmp_vendor_list_map_filters', '4.0.0', 'mvx_vendor_list_map_filters');
	return mvx_vendor_list_map_filters();
}

function wcmp_vendor_list_form_wrapper_end() {
	wc_deprecated_function('wcmp_vendor_list_form_wrapper_end', '4.0.0', 'mvx_vendor_list_form_wrapper_end');
	return mvx_vendor_list_form_wrapper_end();
}

function wcmp_vendor_list_map_wrapper_end() {
	wc_deprecated_function('wcmp_vendor_list_map_wrapper_end', '4.0.0', 'mvx_vendor_list_map_wrapper_end');
	return mvx_vendor_list_map_wrapper_end();
}

function wcmp_vendor_list_display_map() {
	wc_deprecated_function('wcmp_vendor_list_display_map', '4.0.0', 'mvx_vendor_list_display_map');
	return mvx_vendor_list_display_map();
}

function wcmp_vendor_list_catalog_ordering() {
	wc_deprecated_function('wcmp_vendor_list_catalog_ordering', '4.0.0', 'mvx_vendor_list_catalog_ordering');
	return mvx_vendor_list_catalog_ordering();
}

function wcmp_vendor_list_paging_info() {
	wc_deprecated_function('wcmp_vendor_list_paging_info', '4.0.0', 'mvx_vendor_list_paging_info');
	return mvx_vendor_list_paging_info();
}

function wcmp_vendor_list_order_sort() {
	wc_deprecated_function('wcmp_vendor_list_order_sort', '4.0.0', 'mvx_vendor_list_order_sort');
	return mvx_vendor_list_order_sort();
}

function wcmp_vendor_list_content_wrapper() {
	wc_deprecated_function('wcmp_vendor_list_content_wrapper', '4.0.0', 'mvx_vendor_list_content_wrapper');
	return mvx_vendor_list_content_wrapper();
}

function wcmp_vendor_list_content_wrapper_end() {
	wc_deprecated_function('wcmp_vendor_list_content_wrapper_end', '4.0.0', 'mvx_vendor_list_content_wrapper_end');
	return mvx_vendor_list_content_wrapper_end();
}

function wcmp_vendor_list_vendors_loop() {
	wc_deprecated_function('wcmp_vendor_list_vendors_loop', '4.0.0', 'mvx_vendor_list_vendors_loop');
	return mvx_vendor_list_vendors_loop();
}

function wcmp_no_vendors_found_data() {
	wc_deprecated_function('wcmp_no_vendors_found_data', '4.0.0', 'mvx_no_vendors_found');
	return mvx_no_vendors_found();
}

function wcmp_vendor_list_pagination() {
	wc_deprecated_function('wcmp_vendor_list_pagination', '4.0.0', 'mvx_vendor_list_pagination');
	return mvx_vendor_list_pagination();
}

function wcmp_vendor_lists_vendor_top_products( $vendor ) {
	wc_deprecated_function('wcmp_vendor_lists_vendor_top_products', '4.0.0', 'mvx_vendor_lists_vendor_top_products');
	return mvx_vendor_lists_vendor_top_products( $vendor );
}

function wcmp_get_orders($args = array(), $return_type = 'ids', $subonly = false) {
	wc_deprecated_function('wcmp_get_orders', '4.0.0', 'mvx_get_orders');
	return mvx_get_orders($args = array(), $return_type = 'ids', $subonly = false);
}

function wcmp_get_order($id) {
	wc_deprecated_function('wcmp_get_order', '4.0.0', 'mvx_get_order');
	return mvx_get_order($id);
}

function wcmp_get_total_refunded_for_item( $item_id, $order_id ) {
	wc_deprecated_function('wcmp_get_total_refunded_for_item', '4.0.0', 'mvx_get_total_refunded_for_item');
	return mvx_get_total_refunded_for_item( $item_id, $order_id );
}

function wcmp_get_commission_order_id( $commission_id ) {
	wc_deprecated_function('wcmp_get_commission_order_id', '4.0.0', 'mvx_get_commission_order_id');
	return mvx_get_commission_order_id( $commission_id );
}

function wcmp_get_order_commission_id( $order_id ) {
	wc_deprecated_function('wcmp_get_order_commission_id', '4.0.0', 'mvx_get_order_commission_id');
	return mvx_get_order_commission_id( $order_id );
}

function wcmp_get_customer_refund_order_msg( $order, $settings = array() ) {
	wc_deprecated_function('wcmp_get_customer_refund_order_msg', '4.0.0', 'mvx_get_customer_refund_order_msg');
	return mvx_get_customer_refund_order_msg( $order, $settings = array() );
}


function wcmp_removeslashes( $string ) {
	wc_deprecated_function('wcmp_removeslashes', '4.0.0', 'mvx_removeslashes');
	return mvx_removeslashes( $string );
}

function wcmp_vendor_user_store_name( $defalut_name ) {
	wc_deprecated_function('wcmp_vendor_user_store_name', '4.0.0', 'mvx_vendor_user_store_name');
	return mvx_vendor_user_store_name( $defalut_name );
}

function wcmp_buddypress_capability_to_vendor( $data ) {
	wc_deprecated_function('wcmp_buddypress_capability_to_vendor', '4.0.0', 'mvx_buddypress_capability_to_vendor');
	return mvx_buddypress_capability_to_vendor( $data );
}

function wcmp_save_storefont_data( $user_data ,$user_id ) {
	wc_deprecated_function('wcmp_save_storefont_data', '4.0.0', 'mvx_save_storefont_data');
	return mvx_save_storefont_data( $user_data ,$user_id );
}

function wcmp_add_storefont_buddypress_link( $vendor ) {
	wc_deprecated_function('wcmp_add_storefont_buddypress_link', '4.0.0', 'mvx_add_storefont_buddypress_link');
	return mvx_add_storefont_buddypress_link( $vendor );
}

function wcmp_vendor_store_header_bp_link( $vendor_id ) {
	wc_deprecated_function('wcmp_vendor_store_header_bp_link', '4.0.0', 'mvx_vendor_store_header_bp_link');
	return mvx_vendor_store_header_bp_link( $vendor_id );
}

function wcmp_buddypress_tab_admin( $social_tab_options, $vendor_obj ) {
	wc_deprecated_function('wcmp_buddypress_tab_admin', '4.0.0', 'mvx_buddypress_tab_admin');
	return mvx_buddypress_tab_admin( $social_tab_options, $vendor_obj );
}

function wcmp_elementor_init() {
	wc_deprecated_function('wcmp_elementor_init', '4.0.0', 'mvx_elementor_init');
	return mvx_elementor_init();
}

function wcmp_elementor() {
	wc_deprecated_function('wcmp_elementor', '4.0.0', 'mvx_elementor');
	return mvx_elementor();
}

function wcmp_categories( $elements_manager ) {
	wc_deprecated_function('wcmp_categories', '4.0.0', 'mvx_categories');
	return mvx_categories( $elements_manager );
}

function wcmp_table_rate_shipping_admin_enqueue_scripts() {
	wc_deprecated_function('wcmp_table_rate_shipping_admin_enqueue_scripts', '4.0.0', 'mvx_table_rate_shipping_admin_enqueue_scripts');
	return mvx_table_rate_shipping_admin_enqueue_scripts();
}

function wcmp_hide_table_rate_when_disabled( $rates, $package ) {
	wc_deprecated_function('wcmp_hide_table_rate_when_disabled', '4.0.0', 'mvx_hide_table_rate_when_disabled');
	return mvx_hide_table_rate_when_disabled( $rates, $package );
}

function wcmp_table_rate_toggle_shipping_method() {
	wc_deprecated_function('wcmp_table_rate_toggle_shipping_method', '4.0.0', 'mvx_table_rate_toggle_shipping_method');
	return mvx_table_rate_toggle_shipping_method();
}

function wcmp_advance_shipping_template_table_rate($shipping_method, $postdata) {
	wc_deprecated_function('wcmp_advance_shipping_template_table_rate', '4.0.0', 'mvx_advance_shipping_template_table_rate');
	return mvx_advance_shipping_template_table_rate($shipping_method, $postdata);
}

function wcmps_advance_shipping_table_rate($settings_html, $user_id, $zone_id, $vendor_shipping_method) {
	wc_deprecated_function('wcmps_advance_shipping_table_rate', '4.0.0', 'mvxs_advance_shipping_table_rate');
	return mvxs_advance_shipping_table_rate($settings_html, $user_id, $zone_id, $vendor_shipping_method);
}

function wcmp_advance_shipping_template_table_rate_item( $option = '', $shipping_method_id = 0 ) {
	wc_deprecated_function('wcmp_advance_shipping_template_table_rate_item', '4.0.0', 'mvx_advance_shipping_template_table_rate_item');
	return mvx_advance_shipping_template_table_rate_item( $option = '', $shipping_method_id = 0 );
}




function get_wcmp_endpoints_mask() {
	wc_deprecated_function('get_wcmp_endpoints_mask', '4.0.0', 'get_mvx_endpoints_mask');
	return get_mvx_endpoints_mask();
}

function get_wcmp_comment_rating_field($comment) {
	wc_deprecated_function('get_wcmp_comment_rating_field', '4.0.0', 'get_mvx_comment_rating_field');
	return get_mvx_comment_rating_field($comment);
}

function get_wcmp_spmv_terms($args = array()) {
	wc_deprecated_function('get_wcmp_spmv_terms', '4.0.0', 'get_mvx_spmv_terms');
	return get_mvx_spmv_terms($args = array());
}

function get_wcmp_gtin_terms($args = array()) {
	wc_deprecated_function('get_wcmp_gtin_terms', '4.0.0', 'get_mvx_gtin_terms');
	return get_mvx_gtin_terms($args = array());
}

function get_wcmp_transaction_notice($transaction_id) {
	wc_deprecated_function('get_wcmp_transaction_notice', '4.0.0', 'get_mvx_transaction_notice');
	return get_mvx_transaction_notice($transaction_id);
}

function get_wcmp_vendor_policies($vendor = 0) {
	wc_deprecated_function('get_wcmp_vendor_policies', '4.0.0', 'get_mvx_vendor_policies');
	return get_mvx_vendor_policies($vendor = 0);
}

function get_wcmp_vendor_settings($name = '', $tab = '', $subtab = '', $default = false) {
	wc_deprecated_function('get_wcmp_vendor_settings', '4.0.0', 'get_mvx_vendor_settings');
	return get_mvx_vendor_settings($name = '', $tab = '', $subtab = '', $default = false);
}

function get_wcmp_global_settings($name = '', $default = false) {
	wc_deprecated_function('get_wcmp_global_settings', '4.0.0', 'get_mvx_global_settings');
	return get_mvx_global_settings($name = '', $default = false);
}

function get_wcmp_vendors($args = array(), $return = 'object') {
	wc_deprecated_function('get_wcmp_vendors', '4.0.0', 'get_mvx_vendors');
	return get_mvx_vendors($args = array(), $return = 'object');
}


function get_wcmp_vendor($vendor_id = 0) {
	wc_deprecated_function('get_wcmp_vendor', '4.0.0', 'get_mvx_vendor');
	return get_mvx_vendor($vendor_id = 0);
}

function get_wcmp_vendor_by_term($term_id) {
	wc_deprecated_function('get_wcmp_vendor_by_term', '4.0.0', 'get_mvx_vendor_by_term');
	return get_mvx_vendor_by_term($term_id);
}

function get_wcmp_product_vendors($product_id = 0) {
	wc_deprecated_function('get_wcmp_product_vendors', '4.0.0', 'get_mvx_product_vendors');
	return get_mvx_product_vendors($product_id = 0);
}

function get_wcmp_vendor_orders($args = array()) {
	wc_deprecated_function('get_wcmp_vendor_orders', '4.0.0', 'get_mvx_vendor_orders');
	return get_mvx_vendor_orders($args = array());
}

function get_wcmp_vendor_order_amount($args = array(), $vendor_id = false, $check_caps = true) {
	wc_deprecated_function('get_wcmp_vendor_order_amount', '4.0.0', 'get_mvx_vendor_order_amount');
	return get_mvx_vendor_order_amount($args = array(), $vendor_id = false, $check_caps = true);
}

function get_wcmp_product_policies($product_id = 0) {
	wc_deprecated_function('get_wcmp_product_policies', '4.0.0', 'get_mvx_product_policies');
	return get_mvx_product_policies($product_id = 0);
}

function get_wcmp_vendor_dashboard_visitor_stats_data($vendor_id = '') {
	wc_deprecated_function('get_wcmp_vendor_dashboard_visitor_stats_data', '4.0.0', 'get_mvx_vendor_dashboard_visitor_stats_data');
	return get_mvx_vendor_dashboard_visitor_stats_data($vendor_id = '');
}

function get_wcmp_vendor_dashboard_stats_reports_data($vendor = '') {
	wc_deprecated_function('get_wcmp_vendor_dashboard_stats_reports_data', '4.0.0', 'get_mvx_vendor_dashboard_stats_reports_data');
	return get_mvx_vendor_dashboard_stats_reports_data($vendor = '');
}

function get_wcmp_vendor_order_shipping_method($order_id, $vendor_id = '') {
	wc_deprecated_function('get_wcmp_vendor_order_shipping_method', '4.0.0', 'get_mvx_vendor_order_shipping_method');
	return get_mvx_vendor_order_shipping_method($order_id, $vendor_id = '');
}

function get_wcmp_spmv_products_map_data($map_id = '') {
	wc_deprecated_function('get_wcmp_spmv_products_map_data', '4.0.0', 'get_mvx_spmv_products_map_data');
	return get_mvx_spmv_products_map_data($map_id = '');
}

function get_wcmp_spmv_excluded_products_map_data() {
	wc_deprecated_function('get_wcmp_spmv_excluded_products_map_data', '4.0.0', 'get_mvx_spmv_excluded_products_map_data');
	return get_mvx_spmv_excluded_products_map_data();
}

function get_wcmp_different_terms_hierarchy( $term_list = array(), $taxonomy = 'product_cat' ) {
	wc_deprecated_function('get_wcmp_different_terms_hierarchy', '4.0.0', 'get_mvx_different_terms_hierarchy');
	return get_mvx_different_terms_hierarchy( $term_list = array(), $taxonomy = 'product_cat' );
}

function get_wcmp_default_payment_gateways() {
	wc_deprecated_function('get_wcmp_default_payment_gateways', '4.0.0', 'get_mvx_default_payment_gateways');
	return get_mvx_default_payment_gateways();
}

function get_wcmp_available_payment_gateways() {
	wc_deprecated_function('get_wcmp_available_payment_gateways', '4.0.0', 'get_mvx_available_payment_gateways');
	return get_mvx_available_payment_gateways();
}

function get_wcmp_ledger_types() {
	wc_deprecated_function('get_wcmp_ledger_types', '4.0.0', 'get_mvx_ledger_types');
	return get_mvx_ledger_types();
}

function get_wcmp_more_spmv_products( $product_id = 0 ) {
	wc_deprecated_function('get_wcmp_more_spmv_products', '4.0.0', 'get_mvx_more_spmv_products');
	return get_mvx_more_spmv_products( $product_id = 0 );
}

function get_wcmp_suborders( $order_id, $args = array(), $object = true ) {
	wc_deprecated_function('get_wcmp_suborders', '4.0.0', 'get_mvx_suborders');
	return get_mvx_suborders( $order_id, $args = array(), $object = true );
}

function get_wcmp_order_by_commission( $commission_id ) {
	wc_deprecated_function('get_wcmp_order_by_commission', '4.0.0', 'get_mvx_order_by_commission');
	return get_mvx_order_by_commission( $commission_id );
}

function get_wcmp_store_data( $prop = null ) {
	wc_deprecated_function('get_wcmp_store_data', '4.0.0', 'get_mvx_store_data');
	return get_mvx_store_data( $prop = null );
}

function is_wcmp_endpoint_url($endpoint = false) {
	wc_deprecated_function('is_wcmp_endpoint_url', '4.0.0', 'is_mvx_endpoint_url');
	return is_mvx_endpoint_url($endpoint = false);
}

function is_wcmp_version_less_3_4_0() {
	wc_deprecated_function('is_wcmp_version_less_3_4_0', '4.0.0', 'is_mvx_version_less_3_4_0');
	return is_mvx_version_less_3_4_0();
}

function is_wcmp_vendor_completed_store_setup( $user ) {
	wc_deprecated_function('is_wcmp_vendor_completed_store_setup', '4.0.0', 'is_mvx_vendor_completed_store_setup');
	return is_mvx_vendor_completed_store_setup( $user );
}

function is_wcmp_vendor_order( $order, $current_vendor = false ) {
	wc_deprecated_function('is_wcmp_vendor_order', '4.0.0', 'is_mvx_vendor_order');
	return is_mvx_vendor_order( $order, $current_vendor = false );
}

function is_wcmp_table_rate_shipping_enable($is_shipping_enable, $is_enable) {
	wc_deprecated_function('is_wcmp_table_rate_shipping_enable', '4.0.0', 'is_mvx_table_rate_shipping_enable');
	return is_mvx_table_rate_shipping_enable($is_shipping_enable, $is_enable);
}

function update_wcmp_vendor_settings($name = '', $value = '', $tab = '', $subtab = '') {
	wc_deprecated_function('pdate_wcmp_vendor_settings', '4.0.0', 'update_mvx_vendor_settings');
	return update_mvx_vendor_settings($name = '', $value = '', $tab = '', $subtab = '');
}

function delete_wcmp_vendor_settings($name = '', $tab = '', $subtab = '') {
	wc_deprecated_function('delete_wcmp_vendor_settings', '4.0.0', 'delete_mvx_vendor_settings');
	return delete_mvx_vendor_settings($name = '', $tab = '', $subtab = '');
}

function is_user_wcmp_pending_vendor($user) {
	wc_deprecated_function('is_user_wcmp_pending_vendor', '4.0.0', 'is_user_mvx_pending_vendor');
	return is_user_mvx_pending_vendor($user);
}

function is_user_wcmp_rejected_vendor($user) {
	wc_deprecated_function('is_user_wcmp_rejected_vendor', '4.0.0', 'is_user_mvx_rejected_vendor');
	return is_user_mvx_rejected_vendor($user);
}

function is_user_wcmp_vendor($user) {
	wc_deprecated_function('is_user_wcmp_vendor', '4.0.0', 'is_user_mvx_vendor');
	return is_user_mvx_vendor($user);
}