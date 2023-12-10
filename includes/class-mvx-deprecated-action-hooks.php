<?php
/**
 * Deprecated action hooks
 *
 * @package MultiVendorX\Abstracts
 * @since   4.0.0
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles deprecation notices and triggering of legacy action hooks.
 */
class MVX_Deprecated_Action_Hooks extends WC_Deprecated_Hooks {

	/**
	 * Array of deprecated hooks we need to handle. Format of 'new' => 'old'.
	 *
	 * @var array
	 */
	protected $deprecated_hooks = array(
        'mvx_rest_api_request' => 'wcmp_rest_api_request',
        'mvx_rest_insert_vendor_review' => 'wcmp_rest_insert_vendor_review',
        'mvx_before_orders_list_query_bind' => 'before_wcmp_orders_list_query_bind',
        'mvx_vendor_order_edit_status' => 'wcmp_vendor_order_edit_status',
        'mvx_orders_list_do_handle_bulk_actions' => 'wcmp_orders_list_do_handle_bulk_actions',
        'mvx_orders_list_do_handle_filter_actions' => 'wcmp_orders_list_do_handle_filter_actions',
        'mvx_create_duplicate_product' => 'wcmp_create_duplicate_product',
        'mvx_todo_done_pending_transaction' => 'wcmp_todo_done_pending_transaction',
        'mvx_before_products_list_query_bind' => 'before_wcmp_products_list_query_bind',
        'mvx_products_list_do_handle_bulk_actions' => 'wcmp_products_list_do_handle_bulk_actions',
        'mvx_product_qna_after_question_submitted' => 'wcmp_product_qna_after_question_submitted',
        'mvx_product_qna_after_answer_submitted' => 'wcmp_product_qna_after_answer_submitted',
        'mvx_product_qna_after_vote_submitted' => 'wcmp_product_qna_after_vote_submitted',
        'mvx_product_qna_after_update_answer_submitted' => 'wcmp_product_qna_after_update_answer_submitted',
        'mvx_before_update_shipping_method' => 'wcmp_before_update_shipping_method',
        'mvx_order_refunded' => 'wcmp_order_refunded',
        'mvx_after_translated_new_product' => 'wcmp_after_translated_new_product',
        'mvx_after_calculate_commission' => 'after_wcmp_calculate_commission',
        'mvx_create_commission_refund_after_commission_note' => 'wcmp_create_commission_refund_after_commission_note',
        'mvx_commission_fully_refunded' => 'wcmp_commission_fully_refunded',
        'mvx_commission_partially_refunded' => 'wcmp_commission_partially_refunded',
        'mvx_after_create_commission_refunds' => 'wcmp_after_create_commission_refunds',
        'mvx_vendor_commission_created' => 'wcmp_vendor_commission_created',
        'mvx_after_update_vendor_role_capability' => 'wcmp_after_update_vendor_role_capability',
        'mvx_orders_migration_order_created' => 'wcmp_orders_migration_order_created',
        'mvx_add_shipping_package_meta' => 'wcmp_add_shipping_package_meta_data',
        'mvx_frontend_enqueue_scripts' => 'wcmp_frontend_enqueue_scripts',
        'mvx_vendor_shop_page_policies' => 'wcmp_vendor_shop_page_policies',
        'mvx_store_widget_contents' => 'wcmp_store_widget_contents',
        'mvx_jqvmap_enqueue_scripts' => 'wcmp_jqvmap_enqueue_scripts',
        'mvx_prevent_vendor_order_emails_trigger_action' => 'wcmp_prevent_vendor_order_emails_trigger_action',
        'mvx_after_suborder_details' => 'wcmp_after_suborder_details',
        'mvx_checkout_vendor_order_processed' => 'wcmp_checkout_vendor_order_processed',
        'mvx_checkout_create_order' => 'wcmp_checkout_create_order',
        'mvx_checkout_update_order_meta' => 'wcmp_checkout_update_order_meta',
        'mvx_vendor_create_order_line_item' => 'wcmp_vendor_create_order_line_item',
        'mvx_vendor_create_order_shipping_item' => 'wcmp_vendor_create_order_shipping_item',
        'mvx_checkout_create_order_coupon_item' => 'wcmp_checkout_create_order_coupon_item',
        'mvx_vendor_order_on_cancelled_commission' => 'wcmp_vendor_order_on_cancelled_commission',
        'mvx_transaction_update_meta' => 'wcmp_transaction_update_meta_data',
        'mvx_transaction_email_notification' => 'wcmp_transaction_email_notification',
        'mvx_commission_update_commission_meta' => 'wcmp_commission_update_commission_meta',
        'mvx_commission_before_save_commission_total' => 'wcmp_commission_before_save_commission_total',
        'mvx_commission_after_save_commission_total' => 'wcmp_commission_after_save_commission_total',
        'mvx_admin_commission_order_item_headers' => 'wcmp_admin_commission_order_item_headers',
        'mvx_admin_commission_before_order_itemmeta' => 'wcmp_admin_commission_before_order_itemmeta',
        'mvx_admin_commission_after_order_itemmeta' => 'wcmp_admin_commission_after_order_itemmeta',
        'mvx_admin_commission_admin_order_item_values' => 'wcmp_admin_commission_admin_order_item_values',
        'mvx_admin_commission_order_items_after_line_items' => 'wcmp_admin_commission_order_items_after_line_items',
        'mvx_admin_commission_order_item_values' => 'wcmp_admin_commission_order_item_values',
        'mvx_admin_commission_order_items_after_shipping' => 'wcmp_admin_commission_order_items_after_shipping',
        'mvx_admin_commission_order_items_after_fees' => 'wcmp_admin_commission_order_items_after_fees',
        'mvx_admin_commission_order_items_after_refunds' => 'wcmp_admin_commission_order_items_after_refunds',
        'mvx_admin_commission_order_totals_after_shipping' => 'wcmp_admin_commission_order_totals_after_shipping',
        'mvx_admin_commission_order_totals_after_tax' => 'wcmp_admin_commission_order_totals_after_tax',
        'mvx_admin_commission_order_totals_after_total' => 'wcmp_admin_commission_order_totals_after_total',
        'mvx_admin_commission_order_totals_after_refunded' => 'wcmp_admin_commission_order_totals_after_refunded',
        'mvx_new_commission_note' => 'wcmp_new_commission_note',
        'mvx_product_qna_delete_question' => 'wcmp_product_qna_delete_question',
        'mvx_product_qna_delete_answer' => 'wcmp_product_qna_delete_answer',
        'mvx_product_options_policy_on_single_page' => 'wcmp_product_options_policy_product_data',
        'mvx_new_product_note' => 'wcmp_new_product_note',
        'mvx_set_user_role' => 'wcmp_set_user_role',
        'mvx_after_register_vendor' => 'after_register_wcmp_vendor',
        'mvx_after_vendor_withdrawal_transaction_success' => 'wcmp_after_vendor_withdrawal_transaction_success',
        'mvx_vendor_tools_handler' => 'wcmp_vendor_tools_handler',
        'mvx_vendor_shipping_settings' => 'wcmp_vendor_shipping_settings',
        'mvx_save_custom_store' => 'wcmp_save_custom_store_data',
        'mvx_before_post_update' => 'wcmp_before_post_update',
        'mvx_process_product_object' => 'wcmp_process_product_object',
        'mvx_frontend_dashboard_before_coupon_post_update' => 'wcmp_afm_before_coupon_post_update',
        'mvx_frontend_dashboard_coupon_options_save' => 'wcmp_afm_coupon_options_save',
        'mvx_vendor_head' => 'wcmp_vendor_head',
        'mvx_vendor_setup_wizard_footer' => 'wcmp_vendor_setup_wizard_footer',
        'mvx_before_vendor_order_item_table' => 'wcmp_before_vendor_order_item_table',
        'mvx_after_vendor_order_item_table' => 'wcmp_after_vendor_order_item_table',
        'mvx_vendors_vendor_ship' => 'wcmp_vendors_vendor_ship',
        'mvx_after_vendor_ship_save' => 'wcmp_after_vendor_ship_save',
        'mvx_vendor_clear_all_transients' => 'wcmp_vendor_clear_all_transients',
        'mvx_before_vendor_dashboard_content' => 'before_wcmp_vendor_dashboard_content',
        'mvx_after_vendor_dashboard_content' => 'after_wcmp_vendor_dashboard_content',
        'mvx_befor_vendor_dashboard_order_list_actions' => 'wcmp_befor_vendor_dashboard_order_list_actions',
        'mvx_before_reapply_vendor_application_save' => 'wcmp_before_reapply_vendor_application_save',
        'mvx_after_reapply_vendor_application_save' => 'wcmp_after_reapply_vendor_application_save',
        'mvx_after_reapply_vendor_application_saved_notes' => 'wcmp_after_reapply_vendor_application_saved_notes',
        'mvx_init' => 'wcmp_init',
        'mvx_frontend_dashboard_after_add_coupon_endpoint_load' => 'wcmp_afm_after_add_coupon_endpoint_load',
        'mvx_frontend_dashboard_add_coupon_template_load' => 'wcmp_afm_add_coupon_template_load',
        'mvx_ledger_data_store_after_update' => 'wcmp_ledger_data_store_after_update_data',
        'mvx_after_edit_product_endpoint_load' => 'after_wcmp_edit_product_endpoint_load',
        'mvx_edit_product_template_load' => 'wcmp_edit_product_template_load',
        'mvx_pre_get_vendors' => 'pre_get_wcmp_vendors',
        'mvx_report_admin_overview' => 'wcmp_report_admin_overview',
        'mvx_frontend_report_product_filter' => 'wcmp_frontend_report_product_filter',
        'mvx_frontend_report_vendor_filter' => 'wcmp_frontend_report_vendor_filter',
        'mvx_dashboard_setup' => 'wcmp_dashboard_setup',
        'mvx_widget_before_vendor_product_search_form' => 'wcmp_widget_before_vendor_product_search_form',
        'mvx_order_processed' => 'wcmp_order_processed',
        'mvx_get_latlng_distance' => 'wcmp_get_latlng_distance',
        'mvx_spmv_products_map_do_action' => 'wcmp_spmv_products_map_do_action',
        'mvx_spmv_set_object_terms_handler' => 'wcmp_spmv_set_object_terms_handler',
        'mvx_spmv_term_exclude_products_handler' => 'wcmp_spmv_term_exclude_products_handler',
        'mvx_vendor_store_header_social_link' => 'wcmp_vendor_store_header_social_link',
        'mvx_additional_button_at_banner' => 'wcmp_additional_button_at_banner',
        'mvx_after_vendor_information' => 'after_wcmp_vendor_information',
        'mvx_after_vendor_description' => 'after_wcmp_vendor_description',
        'mvx_before_main_content' => 'wcmp_before_main_content',
        'mvx_archive_description' => 'wcmp_archive_description',
        'mvx_after_main_content' => 'wcmp_after_main_content',
        'mvx_sidebar' => 'wcmp_sidebar',
        'mvx_after_vendor_tab' => 'wcmp_after_vendor_tab',
        'mvx_store_tab_widget_contents' => 'wcmp_store_tab_widget_contents',
        'mvx_before_customer_qna_form' => 'before_wcmp_customer_qna_form',
        'mvx_after_customer_qna_form' => 'after_wcmp_customer_qna_form',
        'mvx_email_footer' => 'wcmp_email_footer',
        'mvx_before_vendor_order_table_header' => 'wcmp_before_vendor_order_table_header',
        'mvx_after_vendor_order_table_header' => 'wcmp_after_vendor_order_table_header',
        'mvx_before_reapply_vendor_application_form' => 'wcmp_before_reapply_vendor_application_form',
        'mvx_after_reapply_vendor_application_form' => 'wcmp_after_reapply_vendor_application_form',
        'mvx_vendor_review_before_comment_meta' => 'wcmp_vendor_review_before_comment_meta',
        'mvx_vendor_review_before_comment_text' => 'wcmp_vendor_review_before_comment_text',
        'mvx_vendor_review_after_comment_text' => 'wcmp_vendor_review_after_comment_text',
        'mvx_before_pending_vendor_dashboard' => 'before_wcmp_pending_vendor_dashboard',
        'mvx_vendor_dashboard_navigation' => 'wcmp_vendor_dashboard_navigation',
        'mvx_after_pending_vendor_dashboard' => 'after_wcmp_pending_vendor_dashboard',
        'mvx_before_rejected_vendor_dashboard' => 'before_wcmp_rejected_vendor_dashboard',
        'mvx_rejected_vendor_dashboard_content' => 'wcmp_rejected_vendor_dashboard_content',
        'mvx_after_rejected_vendor_dashboard' => 'after_wcmp_rejected_vendor_dashboard',
        'mvx_before_vendor_list' => 'wcmp_before_vendor_list',
        'mvx_before_vendor_list_map_section' => 'wcmp_before_vendor_list_map_section',
        'mvx_vendor_list_map_section' => 'wcmp_vendor_list_map_section',
        'mvx_after_vendor_list_map_section' => 'wcmp_after_vendor_list_map_section',
        'mvx_before_vendor_list_vendors_section' => 'wcmp_before_vendor_list_vendors_section',
        'mvx_vendor_list_vendors_section' => 'wcmp_vendor_list_vendors_section',
        'mvx_after_vendor_list_vendors_section' => 'wcmp_after_vendor_list_vendors_section',
        'mvx_after_vendor_list' => 'wcmp_after_vendor_list',
        'mvx_before_vendor_dashboard' => 'before_wcmp_vendor_dashboard',
        'mvx_vendor_dashboard_content' => 'wcmp_vendor_dashboard_content',
        'mvx_after_vendor_dashboard' => 'after_wcmp_vendor_dashboard',
        'mvx_vendor_list_sort_distanceSelect_extra_options' => 'wcmp_vendor_list_sort_distanceSelect_extra_options',
        'mvx_vendor_list_vendor_sort_map_extra_filters' => 'wcmp_vendor_list_vendor_sort_map_extra_filters',
        'mvx_vendor_list_vendor_sort_extra_attributes' => 'wcmp_vendor_list_vendor_sort_extra_attributes',
        'mvx_vendor_lists_single_before_image' => 'wcmp_vendor_lists_single_before_image',
        'mvx_vendor_lists_vendor_store_badges' => 'wcmp_vendor_lists_vendor_store_badges',
        'mvx_vendor_lists_single_after_image' => 'wcmp_vendor_lists_single_after_image',
        'mvx_vendor_lists_vendor_before_store_details' => 'wcmp_vendor_lists_vendor_before_store_details',
        'mvx_vendor_lists_single_after_button' => 'wcmp_vendor_lists_single_after_button',
        'mvx_vendor_lists_vendor_after_title' => 'wcmp_vendor_lists_vendor_after_title',
        'mvx_vendor_lists_vendor_after_store_details' => 'wcmp_vendor_lists_vendor_after_store_details',
        'mvx_vendor_register_form' => 'wcmp_vendor_register_form',
        'mvx_vendor_lists_vendor_top_products' => 'wcmp_vendor_lists_vendor_top_products',
        'mvx_after_singleproductmultivendor_vendor_name' => 'after_wcmp_singleproductmultivendor_vendor_name',
        'mvx_dashboard_header_right_vendor_dropdown' => 'wcmp_dashboard_header_right_vendor_dropdown',
        'mvx_dashboard_widget' => 'wcmp_dashboard_widget',
        'mvx_before_vendor_dashboard_navigation' => 'wcmp_before_vendor_dashboard_navigation',
        'mvx_after_vendor_dashboard_navigation' => 'wcmp_after_vendor_dashboard_navigation',
        'mvx_before_vendor_dashboard_profile' => 'wcmp_before_vendor_dashboard_profile',
        'mvx_after_vendor_dashboard_profile' => 'wcmp_after_vendor_dashboard_profile',
        'mvx_before_shop_front' => 'wcmp_before_shop_front',
        'mvx_vendor_add_store' => 'wcmp_vendor_add_store_data',
        'mvx_vendor_add_extra_social_link' => 'wcmp_vendor_add_extra_social_link',
        'mvx_after_shop_front' => 'wcmp_after_shop_front',
        'mvx_vendor_stripe_connect_account_fields' => 'wcmp_vendor_stripe_connect_account_fields',
        'mvx_after_vendor_billing' => 'wcmp_after_vendor_billing',
        'mvx_vendor_order_list_add_extra_filters' => 'wcmp_vendor_order_list_add_extra_filters',
        'mvx_before_vendor_policy' => 'wcmp_before_vendor_policy',
        'mvx_after_vendor_policy' => 'wcmp_after_vendor_policy',
        'mvx_before_vendor_dashboard_products_qna_table' => 'before_wcmp_vendor_dashboard_products_qna_table',
        'mvx_after_vendor_dashboard_products_qna_table' => 'after_wcmp_vendor_dashboard_products_qna_table',
        'mvx_before_shipping_form_end_vendor_dashboard' => 'wcmp_before_shipping_form_end_vendor_dashboard',
        'mvx_before_vendor_tools_content' => 'before_wcmp_vendor_tools_content',
        'mvx_vendor_dashboard_tools_item' => 'wcmp_vendor_dashboard_tools_item',
        'mvx_after_vendor_tools_content' => 'after_wcmp_vendor_tools_content',
        'mvx_before_frontend_dashboard_add_coupon_form' => 'before_wcmp_afm_add_coupon_form',
        'mvx_frontend_dashboard_add_coupon_form_start' => 'wcmp_afm_add_coupon_form_start',
        'mvx_frontend_dashboard_coupon_write_panel_tabs' => 'wcmp_afm_coupon_write_panel_tabs',
        'mvx_frontend_dashboard_coupon_tabs_content' => 'wcmp_afm_coupon_tabs_content',
        'mvx_frontend_dashboard_add_coupon_form_end' => 'wcmp_afm_add_coupon_form_end',
        'mvx_after_frontend_dashboard_add_coupon_form' => 'after_wcmp_afm_add_coupon_form',
        'mvx_before_vendor_dashboard_coupon_list_table' => 'before_wcmp_vendor_dashboard_coupon_list_table',
        'mvx_after_vendor_dashboard_coupon_list_table' => 'after_wcmp_vendor_dashboard_coupon_list_table',
        'mvx_frontend_dashboard_before_general_coupon' => 'wcmp_afm_before_general_coupon_data',
        'mvx_frontend_dashboard_after_general_coupon' => 'wcmp_afm_after_general_coupon_data',
        'mvx_frontend_dashboard_before_usage_limit_coupon' => 'wcmp_afm_before_usage_limit_coupon_data',
        'mvx_frontend_dashboard_after_usage_limit_coupon' => 'wcmp_afm_after_usage_limit_coupon_data',
        'mvx_frontend_dashboard_before_usage_restriction_coupon' => 'wcmp_afm_before_usage_restriction_coupon_data',
        'mvx_frontend_dashboard_after_usage_restriction_coupon' => 'wcmp_afm_after_usage_restriction_coupon_data',
        'mvx_before_vendor_pending_shipping' => 'before_wcmp_vendor_pending_shipping',
        'mvx_after_vendor_pending_shipping' => 'after_wcmp_vendor_pending_shipping',
        'mvx_before_vendor_dashboard_products_cust_qna' => 'before_wcmp_vendor_dashboard_products_cust_qna',
        'mvx_after_vendor_dashboard_products_cust_qna' => 'after_wcmp_vendor_dashboard_products_cust_qna',
        'mvx_before_vendor_stats_reports' => 'before_wcmp_vendor_stats_reports',
        'mvx_after_vendor_stats_reports' => 'after_wcmp_vendor_stats_reports',
        'mvx_before_vendor_visitors_map' => 'before_wcmp_vendor_visitors_map',
        'mvx_after_vendor_visitors_map' => 'after_wcmp_vendor_visitors_map',
        'mvx_frontend_product_manager_template' => 'wcmp-frontend-product-manager_template',
        'mvx_before_add_product_form' => 'before_wcmp_add_product_form',
        'mvx_add_product_form_start' => 'wcmp_add_product_form_start',
        'mvx_product_manager_right_panel_after' => 'wcmp_product_manager_right_panel_after',
        'mvx_product_write_panel_tabs' => 'wcmp_product_write_panel_tabs',
        'mvx_after_attribute_product_tabs_content' => 'wcmp_after_attribute_product_tabs_content',
        'mvx_product_tabs_content' => 'wcmp_product_tabs_content',
        'mvx_after_product_excerpt_metabox_panel' => 'wcmp_after_product_excerpt_metabox_panel',
        'mvx_frontend_dashboard_after_product_excerpt_metabox_panel' => 'wcmp_afm_after_product_excerpt_metabox_panel',
        'mvx_before_product_note_metabox_panel' => 'wcmp_before_product_note_metabox_panel',
        'mvx_after_product_note_metabox_panel' => 'wcmp_after_product_note_metabox_panel',
        'mvx_after_product_tags_metabox_panel' => 'after_wcmp_product_tags_metabox_panel',
        'mvx_add_product_form_end' => 'wcmp_add_product_form_end',
        'mvx_after_add_product_form' => 'after_wcmp_add_product_form',
        'mvx_before_vendor_dashboard_product_list_table' => 'before_wcmp_vendor_dashboard_product_list_table',
        'mvx_products_list_add_extra_filters' => 'wcmp_products_list_add_extra_filters',
        'mvx_before_vendor_dashboard_product_list_page_header_action_btn' => 'before_wcmp_vendor_dash_product_list_page_header_action_btn',
        'mvx_after_vendor_dashboard_product_list_page_header_action_btn' => 'after_wcmp_vendor_dash_product_list_page_header_action_btn',
        'mvx_after_vendor_dashboard_product_list_table' => 'after_wcmp_vendor_dashboard_product_list_table',
        'mvx_frontend_dashboard_product_option_terms' => 'wcmp_afm_product_option_terms',
        'mvx_frontend_dashboard_after_product_attribute_settings' => 'wcmp_afm_after_product_attribute_settings',
        'mvx_frontend_dashboard_product_options_advanced' => 'wcmp_afm_product_options_advanced',
        'mvx_frontend_dashboard_product_options_attribute' => 'wcmp_afm_product_options_attributes',
        'mvx_frontend_dashboard_before_general_product' => 'wcmp_afm_before_general_product_data',
        'mvx_frontend_dashboard_product_options_pricing' => 'wcmp_afm_product_options_pricing',
        'mvx_frontend_dashboard_product_options_download' => 'wcmp_afm_product_options_downloads',
        'mvx_frontend_dashboard_after_general_product' => 'wcmp_afm_after_general_product_data',
        'mvx_frontend_dashboard_product_options_sku' => 'wcmp_afm_product_options_sku',
        'mvx_frontend_dashboard_product_options_stock_description' => 'wcmp_afm_product_options_stock_description',
        'mvx_frontend_dashboard_product_options_stock' => 'wcmp_afm_product_options_stock',
        'mvx_frontend_dashboard_product_options_stock_fields' => 'wcmp_afm_product_options_stock_fields',
        'mvx_frontend_dashboard_product_options_stock_status' => 'wcmp_afm_product_options_stock_status',
        'mvx_frontend_dashboard_product_options_sold_individually' => 'wcmp_afm_product_options_sold_individually',
        'mvx_frontend_dashboard_after_inventory_section_end' => 'wcmp_afm_after_inventory_section_ends',
        'mvx_frontend_dashboard_product_inventory_options' => 'wcmp_afm_product_options_inventory_product_data',
        'mvx_frontend_dashboard_product_options_related' => 'wcmp_afm_product_options_related',
        'mvx_frontend_dashboard_before_vendor_policies' => 'wcmp_afm_before_vendor_policies',
        'mvx_frontend_dashboard_after_vendor_policies' => 'wcmp_afm_after_vendor_policies',
        'mvx_frontend_dashboard_product_options_dimensions' => 'wcmp_afm_product_options_dimensions',
        'mvx_frontend_dashboard_product_options_shipping' => 'wcmp_afm_product_options_shipping',
        'mvx_frontend_dashboard_before_product_highlights_category_wrap' => 'wcmp_afm_before_product_highlights_category_wrap',
        'mvx_frontend_dashboard_after_product_highlights_category_wrap' => 'wcmp_afm_after_product_highlights_category_wrap',
        'mvx_frontend_dashboard_before_product_highlights_title_wrap' => 'wcmp_afm_before_product_highlights_title_wrap',
        'mvx_frontend_dashboard_after_product_highlights_title_wrap' => 'wcmp_afm_after_product_highlights_title_wrap',
        'mvx_vendor_dash_order_details_before_top_left' => 'wcmp_vendor_dash_order_details_before_top_left_data',
        'mvx_vendor_dash_order_details_after_top_left' => 'wcmp_vendor_dash_order_details_after_top_left_data',
        'mvx_vendor_dash_order_details_top_right' => 'wcmp_vendor_dash_order_details_top_right_data',
        'mvx_vendor_dash_before_order_itemmeta' => 'wcmp_vendor_dash_before_order_itemmeta',
        'mvx_vendor_dash_after_order_itemmeta' => 'wcmp_vendor_dash_after_order_itemmeta',
        'mvx_vendor_dash_order_item_values' => 'wcmp_vendor_dash_order_item_values',
        'mvx_vendor_dash_order_item_headers' => 'wcmp_vendor_dash_order_item_headers',
        'mvx_vendor_dash_order_items_after_line_items' => 'wcmp_vendor_dash_order_items_after_line_items',
        'mvx_vendor_dash_order_items_after_shipping' => 'wcmp_vendor_dash_order_items_after_shipping',
        'mvx_vendor_dash_order_items_after_refunds' => 'wcmp_vendor_dash_order_items_after_refunds',
        'mvx_vendor_order_totals_after_discount' => 'wcmp_vendor_order_totals_after_discount',
        'mvx_vendor_order_totals_after_shipping' => 'wcmp_vendor_order_totals_after_shipping',
        'mvx_vendor_order_totals_after_tax' => 'wcmp_vendor_order_totals_after_tax',
        'mvx_vendor_order_totals_after_total' => 'wcmp_vendor_order_totals_after_total',
        'mvx_vendor_order_totals_after_refunded' => 'wcmp_vendor_order_totals_after_refunded',
        'mvx_order_details_add_order_action_buttons' => 'wcmp_order_details_add_order_action_buttons',
        'mvx_vendor_shipping_methods_edit_form_fields' => 'wcmp_vendor_shipping_methods_edit_form_fields',
	);

	/**
	 * Array of versions on each hook has been deprecated.
	 *
	 * @var array
	 */
	protected $deprecated_version = array(
        'wcmp_rest_api_request' => '4.0.0',
        'wcmp_rest_insert_vendor_review' => '4.0.0',
        'before_wcmp_orders_list_query_bind' => '4.0.0',
        'wcmp_vendor_order_edit_status' => '4.0.0',
        'wcmp_orders_list_do_handle_bulk_actions' => '4.0.0',
        'wcmp_orders_list_do_handle_filter_actions' => '4.0.0',
        'wcmp_create_duplicate_product' => '4.0.0',
        'wcmp_todo_done_pending_transaction' => '4.0.0',
        'before_wcmp_products_list_query_bind' => '4.0.0',
        'wcmp_products_list_do_handle_bulk_actions' => '4.0.0',
        'wcmp_product_qna_after_question_submitted' => '4.0.0',
        'wcmp_product_qna_after_answer_submitted' => '4.0.0',
        'wcmp_product_qna_after_vote_submitted' => '4.0.0',
        'wcmp_product_qna_after_update_answer_submitted' => '4.0.0',
        'wcmp_before_update_shipping_method' => '4.0.0',
        'wcmp_order_refunded' => '4.0.0',
        'wcmp_after_translated_new_product' => '4.0.0',
        'after_wcmp_calculate_commission' => '4.0.0',
        'wcmp_create_commission_refund_after_commission_note' => '4.0.0',
        'wcmp_commission_fully_refunded' => '4.0.0',
        'wcmp_commission_partially_refunded' => '4.0.0',
        'wcmp_after_create_commission_refunds' => '4.0.0',
        'wcmp_vendor_commission_created' => '4.0.0',
        'wcmp_after_update_vendor_role_capability' => '4.0.0',
        'wcmp_orders_migration_order_created' => '4.0.0',
        'wcmp_add_shipping_package_meta_data' => '4.0.0',
        'wcmp_frontend_enqueue_scripts' => '4.0.0',
        'wcmp_vendor_shop_page_policies' => '4.0.0',
        'wcmp_store_widget_contents' => '4.0.0',
        'wcmp_jqvmap_enqueue_scripts' => '4.0.0',
        'wcmp_prevent_vendor_order_emails_trigger_action' => '4.0.0',
        'wcmp_after_suborder_details' => '4.0.0',
        'wcmp_checkout_vendor_order_processed' => '4.0.0',
        'wcmp_checkout_create_order' => '4.0.0',
        'wcmp_checkout_update_order_meta' => '4.0.0',
        'wcmp_vendor_create_order_line_item' => '4.0.0',
        'wcmp_vendor_create_order_shipping_item' => '4.0.0',
        'wcmp_checkout_create_order_coupon_item' => '4.0.0',
        'wcmp_vendor_order_on_cancelled_commission' => '4.0.0',
        'wcmp_transaction_update_meta_data' => '4.0.0',
        'wcmp_transaction_email_notification' => '4.0.0',
        'wcmp_commission_update_commission_meta' => '4.0.0',
        'wcmp_commission_before_save_commission_total' => '4.0.0',
        'wcmp_commission_after_save_commission_total' => '4.0.0',
        'wcmp_admin_commission_order_item_headers' => '4.0.0',
        'wcmp_admin_commission_before_order_itemmeta' => '4.0.0',
        'wcmp_admin_commission_after_order_itemmeta' => '4.0.0',
        'wcmp_admin_commission_admin_order_item_values' => '4.0.0',
        'wcmp_admin_commission_order_items_after_line_items' => '4.0.0',
        'wcmp_admin_commission_order_item_values' => '4.0.0',
        'wcmp_admin_commission_order_items_after_shipping' => '4.0.0',
        'wcmp_admin_commission_order_items_after_fees' => '4.0.0',
        'wcmp_admin_commission_order_items_after_refunds' => '4.0.0',
        'wcmp_admin_commission_order_totals_after_shipping' => '4.0.0',
        'wcmp_admin_commission_order_totals_after_tax' => '4.0.0',
        'wcmp_admin_commission_order_totals_after_total' => '4.0.0',
        'wcmp_admin_commission_order_totals_after_refunded' => '4.0.0',
        'wcmp_new_commission_note' => '4.0.0',
        'wcmp_product_qna_delete_question' => '4.0.0',
        'wcmp_product_qna_delete_answer' => '4.0.0',
        'wcmp_product_options_policy_product_data' => '4.0.0',
        'wcmp_new_product_note' => '4.0.0',
        'wcmp_set_user_role' => '4.0.0',
        'after_register_wcmp_vendor' => '4.0.0',
        'wcmp_after_vendor_withdrawal_transaction_success' => '4.0.0',
        'wcmp_vendor_tools_handler' => '4.0.0',
        'wcmp_vendor_shipping_settings' => '4.0.0',
        'wcmp_save_custom_store_data' => '4.0.0',
        'wcmp_before_post_update' => '4.0.0',
        'wcmp_process_product_object' => '4.0.0',
        'wcmp_afm_before_coupon_post_update' => '4.0.0',
        'wcmp_afm_coupon_options_save' => '4.0.0',
        'wcmp_vendor_head' => '4.0.0',
        'wcmp_vendor_setup_wizard_footer' => '4.0.0',
        'wcmp_before_vendor_order_item_table' => '4.0.0',
        'wcmp_after_vendor_order_item_table' => '4.0.0',
        'wcmp_vendors_vendor_ship' => '4.0.0',
        'wcmp_after_vendor_ship_save' => '4.0.0',
        'wcmp_vendor_clear_all_transients' => '4.0.0',
        'before_wcmp_vendor_dashboard_content' => '4.0.0',
        'after_wcmp_vendor_dashboard_content' => '4.0.0',
        'wcmp_befor_vendor_dashboard_order_list_actions' => '4.0.0',
        'wcmp_before_reapply_vendor_application_save' => '4.0.0',
        'wcmp_after_reapply_vendor_application_save' => '4.0.0',
        'wcmp_after_reapply_vendor_application_saved_notes' => '4.0.0',
        'wcmp_init' => '4.0.0',
        'wcmp_afm_after_add_coupon_endpoint_load' => '4.0.0',
        'wcmp_afm_add_coupon_template_load' => '4.0.0',
        'wcmp_ledger_data_store_after_update_data' => '4.0.0',
        'after_wcmp_edit_product_endpoint_load' => '4.0.0',
        'wcmp_edit_product_template_load' => '4.0.0',
        'pre_get_wcmp_vendors' => '4.0.0',
        'wcmp_report_admin_overview' => '4.0.0',
        'wcmp_frontend_report_product_filter' => '4.0.0',
        'wcmp_frontend_report_vendor_filter' => '4.0.0',
        'wcmp_dashboard_setup' => '4.0.0',
        'wcmp_widget_before_vendor_product_search_form' => '4.0.0',
        'wcmp_order_processed' => '4.0.0',
        'wcmp_get_latlng_distance' => '4.0.0',
        'wcmp_spmv_products_map_do_action' => '4.0.0',
        'wcmp_spmv_set_object_terms_handler' => '4.0.0',
        'wcmp_spmv_term_exclude_products_handler' => '4.0.0',
        'wcmp_vendor_store_header_social_link' => '4.0.0',
        'wcmp_additional_button_at_banner' => '4.0.0',
        'after_wcmp_vendor_information' => '4.0.0',
        'after_wcmp_vendor_description' => '4.0.0',
        'wcmp_before_main_content' => '4.0.0',
        'wcmp_archive_description' => '4.0.0',
        'wcmp_after_main_content' => '4.0.0',
        'wcmp_sidebar' => '4.0.0',
        'wcmp_after_vendor_tab' => '4.0.0',
        'wcmp_store_tab_widget_contents' => '4.0.0',
        'before_wcmp_customer_qna_form' => '4.0.0',
        'after_wcmp_customer_qna_form' => '4.0.0',
        'wcmp_email_footer' => '4.0.0',
        'wcmp_before_vendor_order_table_header' => '4.0.0',
        'wcmp_after_vendor_order_table_header' => '4.0.0',
        'wcmp_before_reapply_vendor_application_form' => '4.0.0',
        'wcmp_after_reapply_vendor_application_form' => '4.0.0',
        'wcmp_vendor_review_before_comment_meta' => '4.0.0',
        'wcmp_vendor_review_before_comment_text' => '4.0.0',
        'wcmp_vendor_review_after_comment_text' => '4.0.0',
        'before_wcmp_pending_vendor_dashboard' => '4.0.0',
        'wcmp_vendor_dashboard_navigation' => '4.0.0',
        'after_wcmp_pending_vendor_dashboard' => '4.0.0',
        'before_wcmp_rejected_vendor_dashboard' => '4.0.0',
        'wcmp_rejected_vendor_dashboard_content' => '4.0.0',
        'after_wcmp_rejected_vendor_dashboard' => '4.0.0',
        'wcmp_before_vendor_list' => '4.0.0',
        'wcmp_before_vendor_list_map_section' => '4.0.0',
        'wcmp_vendor_list_map_section' => '4.0.0',
        'wcmp_after_vendor_list_map_section' => '4.0.0',
        'wcmp_before_vendor_list_vendors_section' => '4.0.0',
        'wcmp_vendor_list_vendors_section' => '4.0.0',
        'wcmp_after_vendor_list_vendors_section' => '4.0.0',
        'wcmp_after_vendor_list' => '4.0.0',
        'before_wcmp_vendor_dashboard' => '4.0.0',
        'wcmp_vendor_dashboard_content' => '4.0.0',
        'after_wcmp_vendor_dashboard' => '4.0.0',
        'wcmp_vendor_list_sort_distanceSelect_extra_options' => '4.0.0',
        'wcmp_vendor_list_vendor_sort_map_extra_filters' => '4.0.0',
        'wcmp_vendor_list_vendor_sort_extra_attributes' => '4.0.0',
        'wcmp_vendor_lists_single_before_image' => '4.0.0',
        'wcmp_vendor_lists_vendor_store_badges' => '4.0.0',
        'wcmp_vendor_lists_single_after_image' => '4.0.0',
        'wcmp_vendor_lists_vendor_before_store_details' => '4.0.0',
        'wcmp_vendor_lists_single_after_button' => '4.0.0',
        'wcmp_vendor_lists_vendor_after_title' => '4.0.0',
        'wcmp_vendor_lists_vendor_after_store_details' => '4.0.0',
        'wcmp_vendor_register_form' => '4.0.0',
        'wcmp_vendor_lists_vendor_top_products' => '4.0.0',
        'after_wcmp_singleproductmultivendor_vendor_name' => '4.0.0',
        'wcmp_dashboard_header_right_vendor_dropdown' => '4.0.0',
        'wcmp_dashboard_widget' => '4.0.0',
        'wcmp_before_vendor_dashboard_navigation' => '4.0.0',
        'wcmp_after_vendor_dashboard_navigation' => '4.0.0',
        'wcmp_before_vendor_dashboard_profile' => '4.0.0',
        'wcmp_after_vendor_dashboard_profile' => '4.0.0',
        'wcmp_before_shop_front' => '4.0.0',
        'wcmp_vendor_add_store_data' => '4.0.0',
        'wcmp_vendor_add_extra_social_link' => '4.0.0',
        'wcmp_after_shop_front' => '4.0.0',
        'wcmp_vendor_stripe_connect_account_fields' => '4.0.0',
        'wcmp_after_vendor_billing' => '4.0.0',
        'wcmp_vendor_order_list_add_extra_filters' => '4.0.0',
        'wcmp_before_vendor_policy' => '4.0.0',
        'wcmp_after_vendor_policy' => '4.0.0',
        'before_wcmp_vendor_dashboard_products_qna_table' => '4.0.0',
        'after_wcmp_vendor_dashboard_products_qna_table' => '4.0.0',
        'wcmp_before_shipping_form_end_vendor_dashboard' => '4.0.0',
        'before_wcmp_vendor_tools_content' => '4.0.0',
        'wcmp_vendor_dashboard_tools_item' => '4.0.0',
        'after_wcmp_vendor_tools_content' => '4.0.0',
        'before_wcmp_afm_add_coupon_form' => '4.0.0',
        'wcmp_afm_add_coupon_form_start' => '4.0.0',
        'wcmp_afm_coupon_write_panel_tabs' => '4.0.0',
        'wcmp_afm_coupon_tabs_content' => '4.0.0',
        'wcmp_afm_add_coupon_form_end' => '4.0.0',
        'after_wcmp_afm_add_coupon_form' => '4.0.0',
        'before_wcmp_vendor_dashboard_coupon_list_table' => '4.0.0',
        'after_wcmp_vendor_dashboard_coupon_list_table' => '4.0.0',
        'wcmp_afm_before_general_coupon_data' => '4.0.0',
        'wcmp_afm_after_general_coupon_data' => '4.0.0',
        'wcmp_afm_before_usage_limit_coupon_data' => '4.0.0',
        'wcmp_afm_after_usage_limit_coupon_data' => '4.0.0',
        'wcmp_afm_before_usage_restriction_coupon_data' => '4.0.0',
        'wcmp_afm_after_usage_restriction_coupon_data' => '4.0.0',
        'before_wcmp_vendor_pending_shipping' => '4.0.0',
        'after_wcmp_vendor_pending_shipping' => '4.0.0',
        'before_wcmp_vendor_dashboard_products_cust_qna' => '4.0.0',
        'after_wcmp_vendor_dashboard_products_cust_qna' => '4.0.0',
        'before_wcmp_vendor_stats_reports' => '4.0.0',
        'after_wcmp_vendor_stats_reports' => '4.0.0',
        'before_wcmp_vendor_visitors_map' => '4.0.0',
        'after_wcmp_vendor_visitors_map' => '4.0.0',
        'wcmp-frontend-product-manager_template' => '4.0.0',
        'before_wcmp_add_product_form' => '4.0.0',
        'wcmp_add_product_form_start' => '4.0.0',
        'wcmp_product_manager_right_panel_after' => '4.0.0',
        'wcmp_product_write_panel_tabs' => '4.0.0',
        'wcmp_after_attribute_product_tabs_content' => '4.0.0',
        'wcmp_product_tabs_content' => '4.0.0',
        'wcmp_after_product_excerpt_metabox_panel' => '4.0.0',
        'wcmp_afm_after_product_excerpt_metabox_panel' => '4.0.0',
        'wcmp_before_product_note_metabox_panel' => '4.0.0',
        'wcmp_after_product_note_metabox_panel' => '4.0.0',
        'after_wcmp_product_tags_metabox_panel' => '4.0.0',
        'wcmp_add_product_form_end' => '4.0.0',
        'after_wcmp_add_product_form' => '4.0.0',
        'before_wcmp_vendor_dashboard_product_list_table' => '4.0.0',
        'wcmp_products_list_add_extra_filters' => '4.0.0',
        'before_wcmp_vendor_dash_product_list_page_header_action_btn' => '4.0.0',
        'after_wcmp_vendor_dash_product_list_page_header_action_btn' => '4.0.0',
        'after_wcmp_vendor_dashboard_product_list_table' => '4.0.0',
        'wcmp_afm_product_option_terms' => '4.0.0',
        'wcmp_afm_after_product_attribute_settings' => '4.0.0',
        'wcmp_afm_product_options_advanced' => '4.0.0',
        'wcmp_afm_product_options_attributes' => '4.0.0',
        'wcmp_afm_before_general_product_data' => '4.0.0',
        'wcmp_afm_product_options_pricing' => '4.0.0',
        'wcmp_afm_product_options_downloads' => '4.0.0',
        'wcmp_afm_after_general_product_data' => '4.0.0',
        'wcmp_afm_product_options_sku' => '4.0.0',
        'wcmp_afm_product_options_stock_description' => '4.0.0',
        'wcmp_afm_product_options_stock' => '4.0.0',
        'wcmp_afm_product_options_stock_fields' => '4.0.0',
        'wcmp_afm_product_options_stock_status' => '4.0.0',
        'wcmp_afm_product_options_sold_individually' => '4.0.0',
        'wcmp_afm_after_inventory_section_ends' => '4.0.0',
        'wcmp_afm_product_options_inventory_product_data' => '4.0.0',
        'wcmp_afm_product_options_related' => '4.0.0',
        'wcmp_afm_before_vendor_policies' => '4.0.0',
        'wcmp_afm_after_vendor_policies' => '4.0.0',
        'wcmp_afm_product_options_dimensions' => '4.0.0',
        'wcmp_afm_product_options_shipping' => '4.0.0',
        'wcmp_afm_before_product_highlights_category_wrap' => '4.0.0',
        'wcmp_afm_after_product_highlights_category_wrap' => '4.0.0',
        'wcmp_afm_before_product_highlights_title_wrap' => '4.0.0',
        'wcmp_afm_after_product_highlights_title_wrap' => '4.0.0',
        'wcmp_vendor_dash_order_details_before_top_left_data' => '4.0.0',
        'wcmp_vendor_dash_order_details_after_top_left_data' => '4.0.0',
        'wcmp_vendor_dash_order_details_top_right_data' => '4.0.0',
        'wcmp_vendor_dash_before_order_itemmeta' => '4.0.0',
        'wcmp_vendor_dash_after_order_itemmeta' => '4.0.0',
        'wcmp_vendor_dash_order_item_values' => '4.0.0',
        'wcmp_vendor_dash_order_item_headers' => '4.0.0',
        'wcmp_vendor_dash_order_items_after_line_items' => '4.0.0',
        'wcmp_vendor_dash_order_items_after_shipping' => '4.0.0',
        'wcmp_vendor_dash_order_items_after_refunds' => '4.0.0',
        'wcmp_vendor_order_totals_after_discount' => '4.0.0',
        'wcmp_vendor_order_totals_after_shipping' => '4.0.0',
        'wcmp_vendor_order_totals_after_tax' => '4.0.0',
        'wcmp_vendor_order_totals_after_total' => '4.0.0',
        'wcmp_vendor_order_totals_after_refunded' => '4.0.0',
        'wcmp_order_details_add_order_action_buttons' => '4.0.0',
        'wcmp_vendor_shipping_methods_edit_form_fields' => '4.0.0'
	);

	/**
	 * Hook into the new hook so we can handle deprecated hooks once fired.
	 *
	 * @param string $hook_name Hook name.
	 */
	public function hook_in( $hook_name ) {
		add_action( $hook_name, array( $this, 'maybe_handle_deprecated_hook' ), -1000, 8 );
	}

	/**
	 * If the old hook is in-use, trigger it.
	 *
	 * @param  string $new_hook          New hook name.
	 * @param  string $old_hook          Old hook name.
	 * @param  array  $new_callback_args New callback args.
	 * @param  mixed  $return_value      Returned value.
	 * @return mixed
	 */
	public function handle_deprecated_hook( $new_hook, $old_hook, $new_callback_args, $return_value ) {
		if ( has_action( $old_hook ) ) {
			$this->display_notice( $old_hook, $new_hook );
			$return_value = $this->trigger_hook( $old_hook, $new_callback_args );
		}
		return $return_value;
	}

	/**
	 * Fire off a legacy hook with it's args.
	 *
	 * @param  string $old_hook          Old hook name.
	 * @param  array  $new_callback_args New callback args.
	 * @return mixed
	 */
	protected function trigger_hook( $old_hook, $new_callback_args ) {
		switch ( $old_hook ) {
			case 'woocommerce_order_add_shipping':
			case 'woocommerce_order_add_fee':
				$item_id  = $new_callback_args[0];
				$item     = $new_callback_args[1];
				$order_id = $new_callback_args[2];
				if ( is_a( $item, 'WC_Order_Item_Shipping' ) || is_a( $item, 'WC_Order_Item_Fee' ) ) {
					do_action( $old_hook, $order_id, $item_id, $item );
				}
				break;
			case 'woocommerce_order_add_coupon':
				$item_id  = $new_callback_args[0];
				$item     = $new_callback_args[1];
				$order_id = $new_callback_args[2];
				if ( is_a( $item, 'WC_Order_Item_Coupon' ) ) {
					do_action( $old_hook, $order_id, $item_id, $item->get_code(), $item->get_discount(), $item->get_discount_tax() );
				}
				break;
			case 'woocommerce_order_add_tax':
				$item_id  = $new_callback_args[0];
				$item     = $new_callback_args[1];
				$order_id = $new_callback_args[2];
				if ( is_a( $item, 'WC_Order_Item_Tax' ) ) {
					do_action( $old_hook, $order_id, $item_id, $item->get_rate_id(), $item->get_tax_total(), $item->get_shipping_tax_total() );
				}
				break;
			case 'woocommerce_add_shipping_order_item':
				$item_id  = $new_callback_args[0];
				$item     = $new_callback_args[1];
				$order_id = $new_callback_args[2];
				if ( is_a( $item, 'WC_Order_Item_Shipping' ) ) {
					do_action( $old_hook, $order_id, $item_id, $item->legacy_package_key );
				}
				break;
			case 'woocommerce_add_order_item_meta':
				$item_id  = $new_callback_args[0];
				$item     = $new_callback_args[1];
				$order_id = $new_callback_args[2];
				if ( is_a( $item, 'WC_Order_Item_Product' ) ) {
					do_action( $old_hook, $item_id, $item->legacy_values, $item->legacy_cart_item_key );
				}
				break;
			case 'woocommerce_add_order_fee_meta':
				$item_id  = $new_callback_args[0];
				$item     = $new_callback_args[1];
				$order_id = $new_callback_args[2];
				if ( is_a( $item, 'WC_Order_Item_Fee' ) ) {
					do_action( $old_hook, $order_id, $item_id, $item->legacy_fee, $item->legacy_fee_key );
				}
				break;
			case 'woocommerce_order_edit_product':
				$item_id  = $new_callback_args[0];
				$item     = $new_callback_args[1];
				$order_id = $new_callback_args[2];
				if ( is_a( $item, 'WC_Order_Item_Product' ) ) {
					do_action( $old_hook, $order_id, $item_id, $item, $item->get_product() );
				}
				break;
			case 'woocommerce_order_update_coupon':
			case 'woocommerce_order_update_shipping':
			case 'woocommerce_order_update_fee':
			case 'woocommerce_order_update_tax':
				if ( ! is_a( $item, 'WC_Order_Item_Product' ) ) {
					do_action( $old_hook, $order_id, $item_id, $item );
				}
				break;
			default:
				do_action_ref_array( $old_hook, $new_callback_args );
				break;
		}
	}
}
