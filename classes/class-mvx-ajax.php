<?php

/**
 * MVX Ajax Class
 *
 * @version     2.2.0
 * @package MultiVendorX
 * @author 		MultiVendorX
 */
class MVX_Ajax {

    public function __construct() {
        add_action('wp_ajax_send_report_abuse', array(&$this, 'send_report_abuse'));
        add_action('wp_ajax_nopriv_send_report_abuse', array(&$this, 'send_report_abuse'));
        add_action('wp_ajax_mvx_vendor_csv_download_per_order', array(&$this, 'mvx_vendor_csv_download_per_order'));
        add_filter('ajax_query_attachments_args', array(&$this, 'show_current_user_attachments'), 10, 1);
        // woocommerce product enquiry form support
        if (WC_Dependencies_Product_Vendor::woocommerce_product_enquiry_form_active_check()) {
            add_filter('product_enquiry_send_to', array($this, 'send_enquiry_to_vendor'), 10, 2);
        }

        // Unsign vendor from product
        add_action('wp_ajax_unassign_vendor', array($this, 'unassign_vendor'));
        add_action('wp_ajax_mvx_frontend_sale_get_row', array(&$this, 'mvx_frontend_sale_get_row_callback'));
        add_action('wp_ajax_nopriv_mvx_frontend_sale_get_row', array(&$this, 'mvx_frontend_sale_get_row_callback'));
        add_action('wp_ajax_mvx_frontend_pending_shipping_get_row', array(&$this, 'mvx_frontend_pending_shipping_get_row_callback'));
        add_action('wp_ajax_nopriv_mvx_frontend_pending_shipping_get_row', array(&$this, 'mvx_frontend_pending_shipping_get_row_callback'));

        add_action('wp_ajax_mvx_vendor_announcements_operation', array($this, 'mvx_vendor_messages_operation'));
        add_action('wp_ajax_nopriv_mvx_vendor_announcements_operation', array($this, 'mvx_vendor_messages_operation'));
        add_action('wp_ajax_mvx_announcements_refresh_tab_data', array($this, 'mvx_msg_refresh_tab_data'));
        add_action('wp_ajax_nopriv_mvx_announcements_refresh_tab_data', array($this, 'mvx_msg_refresh_tab_data'));
        add_action('wp_ajax_mvx_dismiss_dashboard_announcements', array($this, 'mvx_dismiss_dashboard_message'));
        add_action('wp_ajax_nopriv_mvx_dismiss_dashboard_announcements', array($this, 'mvx_dismiss_dashboard_message'));

        if (mvx_is_module_active('spmv') && get_mvx_vendor_settings('is_singleproductmultiseller', 'spmv_pages')) {
            // Product duplicate
            add_action('wp_ajax_mvx_copy_to_new_draft', array($this, 'mvx_copy_to_new_draft'));
            add_action('wp_ajax_nopriv_mvx_copy_to_new_draft', array($this, 'mvx_copy_to_new_draft'));
            add_action('wp_ajax_get_loadmorebutton_single_product_multiple_vendors', array($this, 'mvx_get_loadmorebutton_single_product_multiple_vendors'));
            add_action('wp_ajax_nopriv_get_loadmorebutton_single_product_multiple_vendors', array($this, 'mvx_get_loadmorebutton_single_product_multiple_vendors'));
            add_action('wp_ajax_single_product_multiple_vendors_sorting', array($this, 'single_product_multiple_vendors_sorting'));
            add_action('wp_ajax_nopriv_single_product_multiple_vendors_sorting', array($this, 'single_product_multiple_vendors_sorting'));

            add_action('wp_ajax_mvx_create_duplicate_product', array(&$this, 'mvx_create_duplicate_product'));
            add_action('wp_ajax_mvx_show_all_products', array(&$this, 'mvx_show_all_products'));
        }
        if (mvx_is_module_active('spmv')) {
            // Product auto suggestion
            add_action('wp_ajax_mvx_auto_search_product', array($this, 'mvx_auto_suggesion_product'));
            add_action('wp_ajax_nopriv_mvx_auto_search_product', array($this, 'mvx_auto_suggesion_product'));
        }
        add_action('wp_ajax_mvx_add_review_rating_vendor', array($this, 'mvx_add_review_rating_vendor'));
        add_action('wp_ajax_nopriv_mvx_add_review_rating_vendor', array($this, 'mvx_add_review_rating_vendor'));
        // load more vendor review
        add_action('wp_ajax_mvx_load_more_review_rating_vendor', array($this, 'mvx_load_more_review_rating_vendor'));
        add_action('wp_ajax_nopriv_mvx_load_more_review_rating_vendor', array($this, 'mvx_load_more_review_rating_vendor'));

        // search filter vendors from widget
        add_action('wp_ajax_vendor_list_by_search_keyword', array($this, 'vendor_list_by_search_keyword'));
        add_action('wp_ajax_nopriv_vendor_list_by_search_keyword', array($this, 'vendor_list_by_search_keyword'));

        add_action('wp_ajax_mvx_product_tag_add', array(&$this, 'mvx_product_tag_add'));

        add_action('wp_ajax_delete_fpm_product', array(&$this, 'delete_fpm_product'));

        // Vendor dashboard product list
        add_action('wp_ajax_mvx_vendor_product_list', array(&$this, 'mvx_vendor_product_list'));
        // Vendor dashboard withdrawal list
        add_action('wp_ajax_mvx_vendor_unpaid_order_vendor_withdrawal_list', array(&$this, 'mvx_vendor_unpaid_order_vendor_withdrawal_list'));
        // Vendor dashboard transactions list
        add_action('wp_ajax_mvx_vendor_transactions_list', array(&$this, 'mvx_vendor_transactions_list'));
        // Vendor dashboard coupon list
        add_action('wp_ajax_mvx_vendor_coupon_list', array(&$this, 'mvx_vendor_coupon_list'));

        add_action('wp_ajax_mvx_datatable_get_vendor_orders', array(&$this, 'mvx_datatable_get_vendor_orders'));
        // Customer Q & A
        add_action('wp_ajax_mvx_customer_ask_qna_handler', array(&$this, 'mvx_customer_ask_qna_handler'));
        add_action('wp_ajax_nopriv_mvx_customer_ask_qna_handler', array(&$this, 'mvx_customer_ask_qna_handler'));
        // dashboard vendor reviews widget
        add_action('wp_ajax_mvx_vendor_dashboard_reviews_data', array(&$this, 'mvx_vendor_dashboard_reviews_data'));
        // dashboard customer questions widget
        add_action('wp_ajax_mvx_vendor_dashboard_customer_questions_data', array(&$this, 'mvx_vendor_dashboard_customer_questions_data'));
        // vendor products Q&As list
        add_action('wp_ajax_mvx_vendor_products_qna_list', array(&$this, 'mvx_vendor_products_qna_list'));
        // vendor products Q&As approval
        add_action('wp_ajax_mvx_question_verification_approval', array($this, 'mvx_question_verification_approval'));
        // vendor pending shipping widget
        add_action('wp_ajax_mvx_widget_vendor_pending_shipping', array(&$this, 'mvx_widget_vendor_pending_shipping'));
        // vendor product sales report widget
        add_action('wp_ajax_mvx_widget_vendor_product_sales_report', array(&$this, 'mvx_widget_vendor_product_sales_report'));

        // Image crop for vendor banner and logo
        add_action('wp_ajax_mvx_crop_image', array(&$this, 'mvx_crop_image'));
        // MVX shipping
        add_action('wp_ajax_mvx-get-shipping-methods-by-zone', array($this, 'mvx_get_shipping_methods_by_zone'));
        add_action('wp_ajax_admin-get-vendor-shipping-methods-by-zone', array($this, 'admin_get_vendor_shipping_methods_by_zone'));
        add_action('wp_ajax_mvx-add-shipping-method', array($this, 'mvx_add_shipping_method'));
        add_action('wp_ajax_mvx-update-shipping-method', array($this, 'mvx_update_shipping_method'));
        add_action('wp_ajax_mvx-delete-shipping-method', array($this, 'mvx_delete_shipping_method'));
        add_action('wp_ajax_mvx-toggle-shipping-method', array($this, 'mvx_toggle_shipping_method'));
        add_action('wp_ajax_mvx-configure-shipping-method', array($this, 'mvx_configure_shipping_method'));
        add_action('wp_ajax_mvx-vendor-configure-shipping-method', array($this, 'mvx_vendor_configure_shipping_method'));
        
        // product add new listing
        add_action('wp_ajax_mvx_product_classify_next_level_list_categories', array($this, 'mvx_product_classify_next_level_list_categories'));
        add_action('wp_ajax_mvx_product_classify_search_category_level', array($this, 'mvx_product_classify_search_category_level'));
        add_action('wp_ajax_show_product_classify_next_level_from_searched_term', array($this, 'show_product_classify_next_level_from_searched_term'));
        add_action('wp_ajax_mvx_list_a_product_by_name_or_gtin', array($this, 'mvx_list_a_product_by_name_or_gtin'));
        add_action('wp_ajax_mvx_set_classified_product_terms', array($this, 'mvx_set_classified_product_terms'));
        //ajax call to get the product attributes
        add_action('wp_ajax_mvx_edit_product_attribute', array($this, 'edit_product_attribute_callback'));
        add_action('wp_ajax_mvx_product_save_attributes', array($this, 'save_product_attributes_callback'));
        
        // Order Refund
        add_action('wp_ajax_mvx_do_refund', array(&$this, 'mvx_do_refund'));
        
        add_action('wp_ajax_mvx_json_search_downloadable_products_and_variations', array($this, 'mvx_json_search_downloadable_products_and_variations'));
        add_action('wp_ajax_mvx_json_search_products_and_variations', array($this, 'mvx_json_search_products_and_variations'));
        add_action('wp_ajax_mvx_grant_access_to_download', array($this, 'mvx_grant_access_to_download'));
        add_action('wp_ajax_mvx_order_status_changed', array($this, 'mvx_order_status_changed'));
        
        // ledger book
        add_action('wp_ajax_mvx_vendor_banking_ledger_list', array($this, 'mvx_vendor_banking_ledger_list'));

        if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) && mvx_is_module_active('wpml') ) {
            add_action( 'wp_ajax_mvx_product_translations', array( &$this, 'wpml_mvx_product_translations' ) );
            add_action( 'wp_ajax_mvx_product_new_translation', array( &$this, 'wpml_mvx_product_new_translation' ) );
        }
        // Follow ajax
        add_action('wp_ajax_mvx_follow_store_toggle_status', array($this, 'mvx_follow_store_toggle_status'));
        add_action('wp_ajax_mvx_vendor_zone_shipping_order', array($this, 'mvx_vendor_zone_shipping_order'));
        //refund table
        add_action('wp_ajax_mvx_datatable_get_vendor_refund', array($this,'mvx_datatable_get_vendor_refund'));
        //deactivation mail
        add_action('wp_ajax_mvx_sent_deactivation_request', array($this, 'mvx_sent_deactivation_request'));
    }

    /**
     * Ajax callback
     * creates a new attachment from the cropped image
     * basically taken from the custom-header class
     * send prepared attachment back to the client
     */
    public function mvx_crop_image() {
        $attachment_id = isset( $_POST['id'] ) ? absint($_POST['id']) : 0;
        check_ajax_referer('image_editor-' . $attachment_id, 'nonce');

        if (empty($attachment_id)) {
            wp_send_json_error();
        }
        $crop_details_post = isset($_POST['cropDetails']) ? wc_clean( $_POST['cropDetails'] ) : '';
        $crop_details_option = isset($_POST['cropOptions']) ? wc_clean( $_POST['cropOptions'] ) : '';
        $crop_details = apply_filters('mvx_before_crop_image_details', $crop_details_post, $attachment_id);
        $crop_options = apply_filters('mvx_before_crop_image_options', $crop_details_option, $attachment_id);

        $cropped = wp_crop_image(
                $attachment_id, (int) $crop_details['x1'], (int) $crop_details['y1'], (int) $crop_details['width'], (int) $crop_details['height'], $crop_options['maxWidth'], $crop_options['maxHeight']
        );

        if (!$cropped || is_wp_error($cropped)) {
            wp_send_json_error(array('message' => __('Image could not be processed. Please go back and try again.', 'multivendorx')));
        }

        /** This filter is documented in wp-admin/custom-header.php */
        $cropped = apply_filters('mvx_create_file_in_uploads', $cropped, $attachment_id, $crop_details, $crop_options); // For replication

        $parent = get_post($attachment_id);
        $parent_url = $parent->guid;
        $url = str_replace(basename($parent_url), basename($cropped), $parent_url);

        $size = @getimagesize($cropped);
        $image_type = ( $size ) ? $size['mime'] : 'image/jpeg';

        $object = array(
            'ID' => $attachment_id,
            'post_title' => basename($cropped),
            'post_content' => $url,
            'post_mime_type' => $image_type,
            'guid' => $url
        );
        // Its override actual image with cropped one
        if( !apply_filters( 'mvx_crop_image_override_with_original', false, $attachment_id, $_POST ) ) unset($object['ID']); 

        $attachment_id = wp_insert_attachment($object, $cropped);

        $metadata = wp_generate_attachment_metadata($attachment_id, $cropped);
        /**
         * Filter the header image attachment metadata.
         * @since 3.1.2
         * @see wp_generate_attachment_metadata()
         * @param array $metadata Attachment metadata.
         */
        $metadata = apply_filters('mvx_header_image_attachment_metadata', $metadata);
        wp_update_attachment_metadata($attachment_id, $metadata);

        $pre = wp_prepare_attachment_for_js($attachment_id);

        wp_send_json_success($pre);
    }

    public function mvx_datatable_get_vendor_orders() {
        global $wpdb, $MVX;
        if ( ! current_user_can( 'edit_shop_orders' ) ) {
                wp_die( -1 );
        }
        check_ajax_referer('mvx-dashboard', 'security');
        $requestData = ( $_REQUEST ) ? wp_unslash( $_REQUEST ) : array();
        $date_start = isset( $_POST['start_date'] ) ? wc_clean( $_POST['start_date'] ) : '';
        $date_end = isset( $_POST['end_date'] ) ? wc_clean( $_POST['end_date'] ) : '';
        $start_date = date('Y-m-d G:i:s', $date_start);
        $end_date = date('Y-m-d G:i:s', $date_end);
        $vendor = get_current_vendor();
        
        $args = array(
            'author' => $vendor->id,
            'post_status' => 'any',
            'date_query' => array(
                array(
                    'after'     => $start_date,
                    'before'    => $end_date,
                    'inclusive' => true,
                ),
            )
        );
        $vendor_all_orders = apply_filters('mvx_datatable_get_vendor_all_orders', mvx_get_orders($args), $requestData, $_POST);
        
        $filterActionData = array();
        parse_str($requestData['orders_filter_action'], $filterActionData);
        do_action('mvx_before_orders_list_query_bind', $filterActionData, $requestData, $vendor_all_orders);
        $notices = array();
        
        // Do bulk handle
        $ids = apply_filters( 'mvx_vendor_orders_bulk_action_ids', isset($filterActionData['selected_orders']) ? $filterActionData['selected_orders'] : array(), $filterActionData, $requestData );
        if (isset($requestData['bulk_action']) && $requestData['bulk_action'] != '' && isset($filterActionData['selected_orders']) && is_array($filterActionData['selected_orders'])) {
            if ( false !== strpos( $requestData['bulk_action'], 'mark_' ) ) {
                $order_statuses = wc_get_order_statuses();
                $new_status     = substr( $requestData['bulk_action'], 5 ); // Get the status name from action.
                $report_action  = 'marked_' . $new_status;
                // Sanity check: bail out if this is actually not a status, or is not a registered status.
                if ( isset( $order_statuses[ 'wc-' . $new_status ] ) ) { 
                    foreach ( $ids as $id ) {
                        $order = wc_get_order( $id );
                        $order->update_status( $new_status, __( 'Order status changed by vendor bulk edit:', 'multivendorx' ), true );
                        do_action( 'mvx_vendor_order_edit_status', $id, $new_status );
                    }
                }
                $notices[] = array(
                    'message' => ((count($filterActionData['selected_orders']) > 1) ? sprintf(__('%s orders', 'multivendorx'), count($filterActionData['selected_orders'])) : sprintf(__('%s order', 'multivendorx'), count($filterActionData['selected_orders']))) . ' ' . __('status changed to.', 'multivendorx') . $new_status,
                    'type' => 'success'
                );
               
            } else {
                do_action('mvx_orders_list_do_handle_bulk_actions', $requestData['bulk_action'], $ids, $requestData, $vendor_all_orders );
            }
        } else {
            if (isset($filterActionData['order_status']) && $filterActionData['order_status'] != 'all') {
                foreach ($vendor_all_orders as $key => $id) { 
                    if (get_post_status( $id ) != $filterActionData['order_status']) { 
                        unset($vendor_all_orders[$key]);
                    }
                }
                // filter order for rwquest refund
                if( $filterActionData['order_status'] == 'request_refund') {
                    $vendor_all_orders = mvx_get_orders($args);
                    foreach ($vendor_all_orders as $key_refund => $value_refund) {
                        $cust_refund_status = get_post_meta( $value_refund, '_customer_refund_order', true ) ? get_post_meta( $value_refund, '_customer_refund_order', true ) : '';
                        if ($cust_refund_status != 'refund_request') {
                            unset($vendor_all_orders[$key_refund]);
                        }
                    }
                }
            }
            // search by order id
            if (isset($requestData['search_keyword']) && !empty($requestData['search_keyword'])) {
                unset($args['date_query']);
                $result_ids = wc_order_search( $requestData['search_keyword'] );
                $args['post__in'] = array_merge( $result_ids, array( 0 ) );
                $vendor_all_orders = mvx_get_orders($args);
            }
            do_action('mvx_orders_list_do_handle_filter_actions', $filterActionData, $ids, $requestData, $vendor_all_orders );
        }
        
        $vendor_orders = array_slice($vendor_all_orders, $requestData['start'], $requestData['length']);
        $data = array();

        foreach ($vendor_orders as $order_id) {
            $order = wc_get_order($order_id);
            $vendor_order = mvx_get_order($order_id);
            if ($order) {
                if(in_array($order->get_status(), array('draft', 'trash'))) continue;
                $actions = array();
                $is_shipped = (array) get_post_meta($order->get_id(), 'dc_pv_shipped', true);
                if (!in_array($vendor->id, $is_shipped)) {
                    $mark_ship_title = __('Mark as shipped', 'multivendorx');
                } else {
                    $mark_ship_title = __('Shipped', 'multivendorx');
                }
                $actions['view'] = array(
                    'url' => esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders'), $order->get_id())),
                    'icon' => 'ico-eye-icon action-icon',
                    'title' => __('View', 'multivendorx'),
                );
                if (apply_filters('mvx_can_vendor_export_orders_csv', true, get_current_vendor_id())) :
                    $actions['mvx_vendor_csv_download_per_order'] = array(
                        'url' => admin_url('admin-ajax.php?action=mvx_vendor_csv_download_per_order&order_id=' . $order->get_id() . '&nonce=' . wp_create_nonce('mvx_vendor_csv_download_per_order')),
                        'icon' => 'ico-download-icon action-icon',
                        'title' => __('Download', 'multivendorx'),
                    );
                endif;
                if ($vendor->is_shipping_enable()) {
                    $vendor_shipping_method = get_mvx_vendor_order_shipping_method($order->get_id(), $vendor->id);
                    // hide shipping for local pickup
                    if ($vendor_shipping_method && !in_array($vendor_shipping_method->get_method_id(), apply_filters('hide_shipping_icon_for_vendor_order_on_methods', array('local_pickup')))) {
                        $actions['mark_ship'] = array(
                            'url' => '#',
                            'title' => $mark_ship_title,
                            'icon' => 'ico-shippingnew-icon action-icon'
                        );
                    }
                }
                $actions = apply_filters('mvx_vendor_dashboard_order_list_actions', $actions, $order->get_id());
                $action_html = '';
                foreach ($actions as $key => $action) {
                    $target = isset($action['target']) ? $action['target'] : '';
                    if ($key == 'mark_ship' && !in_array($vendor->id, $is_shipped)) {
                        $action_html .= '<a href="javascript:void(0)" title="' . $mark_ship_title . '" onclick="mvxMarkeAsShip(this,' . $order->get_id() . ')"><i class="mvx-font ' . $action['icon'] . '"></i></a> ';
                    } else if ($key == 'mark_ship') {
                        $action_html .= '<i title="' . $mark_ship_title . '" class="mvx-font ' . $action['icon'] . '"></i> ';
                    } else {
                        $action_html .= '<a href="' . $action['url'] . '" target="'. $target .'" title="' . $action['title'] . '"><i class="mvx-font ' . $action['icon'] . '"></i></a> ';
                    }
                }
                
                $data[] = apply_filters('mvx_datatable_order_list_row', array(
                    'select_order' => '<input type="checkbox" class="select_' . $order->get_status() . '" name="selected_orders[' . $order->get_id() . ']" value="' . $order->get_id() . '" />',
                    'order_id' => $order->get_id(),
                    'order_date' => mvx_date($order->get_date_created()),
                    'vendor_earning' => ($vendor_order->get_commission_total()) ? $vendor_order->get_commission_total() : '-',
                    'order_status' => esc_html(wc_get_order_status_name($order->get_status())), //ucfirst($order->get_status()),
                    'action' => apply_filters('mvx_vendor_orders_row_action_html', $action_html, $actions)
                        ), $order);
            }
        }
        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($vendor_all_orders)), // total number of records
            "recordsFiltered" => intval(count($vendor_all_orders)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data,   // total data array
            "notices" => $notices ,  // set messages or notices
        );
        wp_send_json($json_data);
    }

    function single_product_multiple_vendors_sorting() {
        global $MVX;
        $sorting_value = isset($_POST['sorting_value']) ? wc_clean($_POST['sorting_value']) : 0;
        $attrid = isset($_POST['attrid']) ? absint($_POST['attrid']) : 0;
        $more_products = $MVX->product->get_multiple_vendors_array_for_single_product($attrid);
        $more_product_array = $more_products['more_product_array'];
        $results = $more_products['results'];
        $MVX->template->get_template('single-product/multiple-vendors-products-body.php', array('more_product_array' => $more_product_array, 'sorting' => $sorting_value));
        die;
    }

    function mvx_get_loadmorebutton_single_product_multiple_vendors() {
        global $MVX;
        $MVX->template->get_template('single-product/load-more-button.php');
        die;
    }

    function mvx_load_more_review_rating_vendor() {
        global $MVX, $wpdb;

        if (!empty($_POST['pageno']) && !empty($_POST['term_id'])) {
            $vendor = get_mvx_vendor_by_term($_POST['term_id']);
            $vendor_id = $vendor->id;
            $offset = $_POST['postperpage'] * $_POST['pageno'];
            $reviews_lists = $vendor->get_reviews_and_rating($offset);
            $MVX->template->get_template('review/mvx-vendor-review.php', array('reviews_lists' => $reviews_lists, 'vendor_term_id' => $_POST['term_id']));
        }
        die;
    }

    function mvx_add_review_rating_vendor() {
        global $MVX, $wpdb;
        check_ajax_referer('mvx-review', 'security');
        $review = isset( $_POST['comment'] ) ? wc_clean( $_POST['comment'] ) : '';
        $rating = isset($_POST['rating']) ? intval( wp_unslash( $_POST['rating'] ) ): false;
        $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
        $vendor_id = isset($_POST['vendor_id']) ? absint( $_POST['vendor_id'] ) : 0;
        $current_user = wp_get_current_user();
        $comment_approve_by_settings = get_option('comment_moderation') ? 0 : 1;
        // IF vendor given multi rating
        $multiple_rating = '';
        $multi_rate_details = isset($_POST['multi_rate_details']) ? $_POST['multi_rate_details'] : '';
        if ($multi_rate_details) {
            parse_str($multi_rate_details, $mvx_settings_review_details);        
            $mvx_review_options = mvx_get_option( 'mvx_review_settings_option', array() );
            $mvx_review_categories = isset( $mvx_review_options['review_categories'] ) ? $mvx_review_options['review_categories'] : array();
            if (isset($mvx_settings_review_details['mvx_store_review_category']) && !empty($mvx_review_categories)) {
                $multiple_rating = array_combine($mvx_settings_review_details['mvx_store_review_category'], wp_list_pluck($mvx_review_categories, 'category'));
                $rating = array_sum($mvx_settings_review_details['mvx_store_review_category']) / count(wp_list_pluck($mvx_review_categories, 'category'));
            }
        }
        if (!empty($review)) {
            $time = current_time('mysql');
            if ($current_user->ID > 0) {
                $data = array(
                    'comment_post_ID' => mvx_vendor_dashboard_page_id(),
                    'comment_author' => $current_user->display_name,
                    'comment_author_email' => sanitize_email($current_user->user_email),
                    'comment_author_url' => esc_url($current_user->user_url),
                    'comment_content' => $review,
                    'comment_type' => 'mvx_vendor_rating',
                    'comment_parent' => $comment_parent,
                    'user_id' => $current_user->ID,
                    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                    'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                    'comment_date' => $time,
                    'comment_approved' => $comment_approve_by_settings,
                );
                $comment_id = wp_insert_comment($data);
                if (!is_wp_error($comment_id)) {
                    // delete transient
                    if (get_transient('mvx_dashboard_reviews_for_vendor_' . $vendor_id)) {
                        delete_transient('mvx_dashboard_reviews_for_vendor_' . $vendor_id);
                    }
                    // mark as replied
                    if ($comment_parent != 0 && $vendor_id) {
                        update_comment_meta($comment_parent, '_mark_as_replied', 1);
                    }
                    if ($rating && !empty($rating)) {
                        update_comment_meta($comment_id, 'vendor_rating', $rating);
                    }
                    if ($multiple_rating) {
                        update_comment_meta($comment_id, 'vendor_multi_rating', serialize($multiple_rating));
                    }
                    $is_updated = update_comment_meta($comment_id, 'vendor_rating_id', $vendor_id);
                    if ($is_updated) {
                        $email = WC()->mailer()->emails['WC_Email_Vendor_Review'];
                        $recipient_details = $comment_parent != 0 ? get_user_by( 'login', get_comment_author($comment_parent) ) : get_userdata( $vendor_id );
                        $email->trigger( $recipient_details, $rating, $review, $current_user->display_name );
                        echo 1;
                    }
                }
            }
        } else {
            echo 0;
        }
        die;
    }

    function mvx_copy_to_new_draft() {
        $post_id = isset($_POST['postid']) ? absint($_POST['postid']) : 0;
        $post = get_post($post_id);
        echo wp_nonce_url(admin_url('edit.php?post_type=product&action=duplicate_product&post=' . $post->ID), 'woocommerce-duplicate-product_' . $post->ID);
        die;
    }

    public function mvx_create_duplicate_product() {
        global $MVX;
        if (!current_user_can('edit_products') ) {
            wp_die(-1);
        }
        check_ajax_referer('mvx-types', 'security');
        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        $parent_post = get_post($product_id);
        $redirect_url = isset($_POST['redirect_url']) ? esc_url($_POST['redirect_url']) : esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product')));
        $product = wc_get_product($product_id);
        if (!function_exists('duplicate_post_plugin_activation')) {
            include_once( WC_ABSPATH . 'includes/admin/class-wc-admin-duplicate-product.php' );
        }
        $duplicate_product_class = new WC_Admin_Duplicate_Product();
        $duplicate_product = $duplicate_product_class->product_duplicate($product);
        $response = array('status' => false);
        if ($duplicate_product && is_user_mvx_vendor(get_current_user_id())) {
            // if Product title have Copy string
            $title = str_replace(" (Copy)", "", $parent_post->post_title);
            wp_update_post(array('ID' => $duplicate_product->get_id(), 'post_author' => get_current_vendor_id(), 'post_title' => $title));
            wp_set_object_terms($duplicate_product->get_id(), absint(get_current_vendor()->term_id), $MVX->taxonomy->taxonomy_name);

            // Add GTIN, if exists
            $gtin_data = wp_get_post_terms($product->get_id(), $MVX->taxonomy->mvx_gtin_taxonomy);
            if ($gtin_data) {
                $gtin_type = isset($gtin_data[0]->term_id) ? $gtin_data[0]->term_id : '';
                wp_set_object_terms($duplicate_product->get_id(), $gtin_type, $MVX->taxonomy->mvx_gtin_taxonomy, true);
            }
            $gtin_code = get_post_meta($product->get_id(), '_mvx_gtin_code', true);
            if ($gtin_code)
                update_post_meta($duplicate_product->get_id(), '_mvx_gtin_code', wc_clean($gtin_code));

            $has_mvx_spmv_map_id = get_post_meta($product->get_id(), '_mvx_spmv_map_id', true);
            if ($has_mvx_spmv_map_id) {
                $data = array('product_id' => $duplicate_product->get_id(), 'product_map_id' => $has_mvx_spmv_map_id);
                update_post_meta($duplicate_product->get_id(), '_mvx_spmv_map_id', $has_mvx_spmv_map_id);
                mvx_spmv_products_map($data, 'insert');
            } else {
                $data = array('product_id' => $duplicate_product->get_id());
                $map_id = mvx_spmv_products_map($data, 'insert');

                if ($map_id) {
                    update_post_meta($duplicate_product->get_id(), '_mvx_spmv_map_id', $map_id);
                    // Enroll in SPMV parent product too 
                    $data = array('product_id' => $product->get_id(), 'product_map_id' => $map_id);
                    mvx_spmv_products_map($data, 'insert');
                    update_post_meta($product->get_id(), '_mvx_spmv_map_id', $map_id);
                }
                update_post_meta($product->get_id(), '_mvx_spmv_product', true);
            }
            update_post_meta($duplicate_product->get_id(), '_mvx_spmv_product', true);
            $duplicate_product->save();
            do_action('mvx_create_duplicate_product', $duplicate_product);
            $permalink_structure = get_option('permalink_structure');
            if (!empty($permalink_structure)) {
                $redirect_url .= $duplicate_product->get_id();
            } else {
                $redirect_url .= '=' . $duplicate_product->get_id();
            }
            $response['status'] = true;
            $response['redirect_url'] = htmlspecialchars_decode($redirect_url);
        }
        wp_send_json($response);
    }

    function mvx_auto_suggesion_product() {
        global $MVX, $wpdb;
        check_ajax_referer('search-products', 'security');
        $user = wp_get_current_user();
        $term = wc_clean(empty($term) ? stripslashes(wc_clean($_REQUEST['protitle'])) : $term);
        $is_admin = isset($_REQUEST['is_admin']) ? wc_clean($_REQUEST['is_admin']) : '';

        if (empty($term)) {
            wp_die();
        }

        $data_store = WC_Data_Store::load('product');
        $ids = $data_store->search_products($term, '', false);

        $include = array();
        foreach ($ids as $id) {
            $product_map_id = get_post_meta($id, '_mvx_spmv_map_id', true);
            if ($product_map_id) {
                $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mvx_products_map WHERE product_map_id=%d", $product_map_id));
                $product_ids = wp_list_pluck($results, 'product_id');
                if($product_ids){
                    $include[] = min($product_ids);
                }
            } else {
                $include[] = $id;
            }
        }

        if ($include) {
            $ids = array_slice(array_intersect($ids, $include), 0, 10);
        } else {
            $ids = array();
        }
        $product_objects = apply_filters('mvx_auto_suggesion_product_objects', array_map('wc_get_product', $ids), $user);
        $html = '';
        if (count($product_objects) > 0) {
            $html .= "<ul>";
            foreach ($product_objects as $product_object) {
                if ($product_object) {
                    if (is_user_mvx_vendor($user) && mvx_is_product_type_avaliable($product_object->get_type())) {
                        if ($is_admin == 'false') {
                            $html .= "<li><a data-product_id='{$product_object->get_id()}' href='javascript:void(0)'>" . rawurldecode($product_object->get_formatted_name()) . "</a></li>";
                        } else {
                            $html .= "<li data-element='{$product_object->get_id()}'><a href='" . wp_nonce_url(admin_url('edit.php?post_type=product&action=duplicate_product&singleproductmultiseller=1&post=' . $product_object->get_id()), 'woocommerce-duplicate-product_' . $product_object->get_id()) . "'>" . rawurldecode($product_object->get_formatted_name()) . "</a></li>";
                        }
                    } elseif (!is_user_mvx_vendor($user) && current_user_can('edit_products')) {
                        $html .= "<li data-element='{$product_object->get_id()}'><a href='" . wp_nonce_url(admin_url('edit.php?post_type=product&action=duplicate_product&singleproductmultiseller=1&post=' . $product_object->get_id()), 'woocommerce-duplicate-product_' . $product_object->get_id()) . "'>" . rawurldecode($product_object->get_formatted_name()) . "</a></li>";
                    }
                }
            }
            $html .= "</ul>";
        } else {
            $html .= "<ul><li class='mvx_no-suggesion'>" . __('No Suggestion found', 'multivendorx') . "</li></ul>";
        }

        wp_send_json(array('html' => $html, 'results_count' => count($product_objects)));
    }

    public function mvx_dismiss_dashboard_message() {
        global $wpdb, $MVX;
        check_ajax_referer('mvx-dashboard', 'security');
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $data_msg_deleted = get_user_meta($current_user_id, '_mvx_vendor_message_deleted', true);
        if (!empty($data_msg_deleted)) {
            $data_arr = explode(',', $data_msg_deleted);
            $data_arr[] = $post_id;
            $data_str = implode(',', $data_arr);
        } else {
            $data_arr[] = $post_id;
            $data_str = implode(',', $data_arr);
        }
        $is_updated = update_user_meta($current_user_id, '_mvx_vendor_message_deleted', $data_str);
        if ($is_updated) {
            $dismiss_notices_ids_array = array();
            $dismiss_notices_ids = get_user_meta($current_user_id, '_mvx_vendor_message_deleted', true);
            if (!empty($dismiss_notices_ids)) {
                $dismiss_notices_ids_array = explode(',', $dismiss_notices_ids);
            } else {
                $dismiss_notices_ids_array = array();
            }
            $args_msg = array(
                'posts_per_page' => 1,
                'offset' => 0,
                'post__not_in' => $dismiss_notices_ids_array,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_type' => 'mvx_vendor_notice',
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $msgs_array = get_posts($args_msg);
            if (is_array($msgs_array) && !empty($msgs_array) && count($msgs_array) > 0) {
                $msg = $msgs_array[0];
                ?>
                <h2><?php echo __('Admin Message:', 'multivendorx'); ?> </h2>
                <span> <?php echo $msg->post_title; ?> </span><br/>
                <span class="mormaltext" style="font-weight:normal;"> <?php
                echo $short_content = substr(stripslashes(strip_tags($msg->post_content)), 0, 155);
                if (strlen(stripslashes(strip_tags($msg->post_content))) > 155) {
                    echo '...';
                }
                ?> </span><br/>
                <a href="<?php echo get_permalink(mvx_get_option('mvx_product_vendor_messages_page_id')); ?>"><button><?php echo __('DETAILS', 'multivendorx'); ?></button></a>
                <div class="clear"></div>
                <a href="#" id="cross-admin" data-element = "<?php echo $msg->ID; ?>"  class="mvx_cross mvx_delate_message_dashboard"><i class="fa fa-times-circle"></i></a>
                    <?php
                } else {
                    ?>
                <h2><?php echo __('No Messages Found:', 'multivendorx'); ?> </h2>
                <?php
            }
        } else {
            ?>
            <h2><?php echo __('Error in process:', 'multivendorx'); ?> </h2>
            <?php
        }
        die;
    }

    public function mvx_msg_refresh_tab_data() {
        global $wpdb, $MVX;
        $tab = isset($_POST['tabname']) ? wc_clean($_POST['tabname']) : '';
        $MVX->template->get_template('vendor-dashboard/vendor-announcements/vendor-announcements' . str_replace("_", "-", $tab) . '.php');
        die;
    }

    public function mvx_vendor_messages_operation() {
        global $wpdb, $MVX;
        check_ajax_referer('grant-access', 'security');
        $current_user = wp_get_current_user();
        $current_user_id = $current_user->ID;
        $post_id = isset($_POST['msg_id']) ? wc_clean($_POST['msg_id']) : 0;
        $actionmode = isset($_POST['actionmode']) ? wc_clean($_POST['actionmode']) : '';
        if ($actionmode == "mark_delete") {
            $data_msg_deleted = get_user_meta($current_user_id, '_mvx_vendor_message_deleted', true);
            if (!empty($data_msg_deleted)) {
                $data_arr = explode(',', $data_msg_deleted);
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            } else {
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_mvx_vendor_message_deleted', $data_str)) {
                echo 1;
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_read") {
            $data_msg_readed = get_user_meta($current_user_id, '_mvx_vendor_message_readed', true);
            if (!empty($data_msg_readed)) {
                $data_arr = explode(',', $data_msg_readed);
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            } else {
                $data_arr[] = $post_id;
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_mvx_vendor_message_readed', $data_str)) {
                echo __('Mark Unread', 'multivendorx');
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_unread") {
            $data_msg_readed = get_user_meta($current_user_id, '_mvx_vendor_message_readed', true);
            if (!empty($data_msg_readed)) {
                $data_arr = explode(',', $data_msg_readed);
                if (is_array($data_arr)) {
                    if (($key = array_search($post_id, $data_arr)) !== false) {
                        unset($data_arr[$key]);
                    }
                }
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_mvx_vendor_message_readed', $data_str)) {
                echo __('Mark Read', 'multivendorx');
            } else {
                echo 0;
            }
        } elseif ($actionmode == "mark_restore") {
            $data_msg_deleted = get_user_meta($current_user_id, '_mvx_vendor_message_deleted', true);
            if (!empty($data_msg_deleted)) {
                $data_arr = explode(',', $data_msg_deleted);
                if (is_array($data_arr)) {
                    if (($key = array_search($post_id, $data_arr)) !== false) {
                        unset($data_arr[$key]);
                    }
                }
                $data_str = implode(',', $data_arr);
            }
            if (update_user_meta($current_user_id, '_mvx_vendor_message_deleted', $data_str)) {
                echo __('Mark Restore', 'multivendorx');
            } else {
                echo 0;
            }
        }
        die;
    }

    public function mvx_frontend_sale_get_row_callback() {
        global $wpdb, $MVX;
        $user = wp_get_current_user();
        $vendor = get_mvx_vendor($user->ID);
        $today_or_weekly = isset($_POST['today_or_weekly']) ? wc_clean($_POST['today_or_weekly']) : '';
        $current_page = isset($_POST['current_page']) ? wc_clean($_POST['current_page']) : '';
        $next_page = isset($_POST['next_page']) ? wc_clean($_POST['next_page']) : '';
        $total_page = isset($_POST['total_page']) ? wc_clean($_POST['total_page']): '';
        $perpagedata = isset($_POST['perpagedata']) ? wc_clean($_POST['perpagedata']) : '';
        if ($next_page <= $total_page) {
            if ($next_page > 1) {
                $start = ($next_page - 1) * $perpagedata;
                $MVX->template->get_template('vendor-dashboard/dashboard/vendor-dashboard-sales-item.php', array('vendor' => $vendor, 'today_or_weekly' => $today_or_weekly, 'start' => $start, 'to' => $perpagedata));
            }
        } else {
            echo "<tr><td colspan='5'>" . __('no more data found', 'multivendorx') . "</td></tr>";
        }
        die;
    }

    public function mvx_frontend_pending_shipping_get_row_callback() {
        global $wpdb, $MVX;
        $user = wp_get_current_user();
        $vendor = get_mvx_vendor($user->ID);
        $today_or_weekly = isset($_POST['today_or_weekly']) ? wc_clean($_POST['today_or_weekly']) : '';
        $current_page = isset($_POST['current_page']) ? wc_clean($_POST['current_page']) : '';
        $next_page = isset($_POST['next_page']) ? wc_clean($_POST['next_page']) : '';
        $total_page = isset($_POST['total_page']) ? wc_clean($_POST['total_page']): '';
        $perpagedata = isset($_POST['perpagedata']) ? wc_clean($_POST['perpagedata']) : '';
        if ($next_page <= $total_page) {
            if ($next_page > 1) {
                $start = ($next_page - 1) * $perpagedata;
                $MVX->template->get_template('vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items.php', array('vendor' => $vendor, 'today_or_weekly' => $today_or_weekly, 'start' => $start, 'to' => $perpagedata));
            }
        } else {
            echo "<tr><td colspan='5'>" . __('no more data found', 'multivendorx') . "</td></tr>";
        }
        die;
    }

    function mvx_vendor_csv_download_per_order() {
        global $MVX, $wpdb;

        if (isset($_GET['action']) && isset($_GET['order_id']) && isset($_GET['nonce'])) {
            $action = isset($_GET['action']) ? wc_clean($_GET['action']) : '';
            $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
            $nonce = isset($_REQUEST["nonce"]) ? wc_clean($_REQUEST["nonce"]) : '';

            if (!wp_verify_nonce($nonce, $action))
                die('Invalid request');

            $vendor = get_mvx_vendor(get_current_vendor_id());
            $vendor = apply_filters('mvx_csv_download_per_order_vendor', $vendor);
            if (!$vendor)
                die('Invalid request');
            $order_data = array();
            $commission_id = get_post_meta( $order_id, '_commission_id', true );
            if (!empty($commission_id)) {
                //$commission_id = $customer_orders[0]['commission_id'];
                $order_data[$commission_id] = $order_id;
                $MVX->vendor_dashboard->generate_csv($order_data, $vendor);
            }
            die;
        }
    }

    /**
     * Unassign vendor from a product
     */
    function unassign_vendor() {
        global $MVX;
        if ( ! current_user_can( 'edit_users' ) ) {
            wp_die(__('Sorry, you cannot update vendors', 'multivendorx'));
        }
        check_ajax_referer('search-products', 'security');
        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        $vendor = get_mvx_product_vendors($product_id);
        $admin_id = get_current_user_id();
        if (current_user_can('administrator')) {
            $_product = wc_get_product($product_id);
            $orders = array();
            if ($_product->is_type('variable')) {
                $get_children = $_product->get_children();
                if (!empty($get_children)) {
                    foreach ($get_children as $child) {
                        $orders = array_merge($orders, $vendor->get_vendor_orders_by_product($vendor->term_id, $child));
                    }
                    $orders = array_unique($orders);
                }
            } else {
                $orders = array_unique($vendor->get_vendor_orders_by_product($vendor->term_id, $product_id));
            }

            foreach ($orders as $order_id) {
                $order = new WC_Order($order_id);
                $items = $order->get_items('line_item');
                foreach ($items as $item_id => $item) {
                    wc_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
                }
            }

            wp_delete_object_term_relationships($product_id, $MVX->taxonomy->taxonomy_name);
            wp_delete_object_term_relationships($product_id, 'product_shipping_class');
            wp_update_post(array('ID' => $product_id, 'post_author' => $admin_id));
            delete_post_meta($product_id, '_commission_per_product');
            delete_post_meta($product_id, '_commission_percentage_per_product');
            delete_post_meta($product_id, '_commission_fixed_with_percentage_qty');
            delete_post_meta($product_id, '_commission_fixed_with_percentage');

            $product_obj = wc_get_product($product_id);
            if ($product_obj->is_type('variable')) {
                $child_ids = $product_obj->get_children();
                if (isset($child_ids) && !empty($child_ids)) {
                    foreach ($child_ids as $child_id) {
                        delete_post_meta($child_id, '_commission_fixed_with_percentage');
                        delete_post_meta($child_id, '_product_vendors_commission_percentage');
                        delete_post_meta($child_id, '_product_vendors_commission_fixed_per_trans');
                        delete_post_meta($child_id, '_product_vendors_commission_fixed_per_qty');
                    }
                }
            }
        }

        die;
    }

    function send_enquiry_to_vendor($send_to, $product_id) {
        global $MVX;
        $vendor = get_mvx_product_vendors($product_id);
        if ($vendor) {
            $send_to = $vendor->user_data->data->user_email;
        }
        return $send_to;
    }

    /**
     * MVX current user attachment
     */
    function show_current_user_attachments($query = array()) {
        $user_id = get_current_vendor_id();
        if (is_user_mvx_vendor($user_id)) {
            $query['author'] = $user_id;
        }
        return $query;
    }

    /**
     * Report Abuse Vendor via AJAX
     *
     * @return void
     */
    function send_report_abuse() {
        global $MVX;
        $check = false;
        $name = isset($_POST['name']) ? wc_clean($_POST['name']) : '';
        $from_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $user_message = isset($_POST['msg']) ? wc_clean($_POST['msg']) : '';
        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

        $check = !empty($name) && !empty($from_email) && !empty($user_message);

        if ($check) {
            $vendor = get_mvx_product_vendors($product_id);
            $previous_report_abuse_data = get_user_meta($vendor->id, 'report_abuse_data', true) ? get_user_meta($vendor->id, 'report_abuse_data', true) : array();
            $previous_report_abuse_data[] = array(
                'name'          =>  $name,
                'msg'           =>  $user_message,
                'product_id'    =>  $product_id,
                'email'         =>  $from_email,
            );
            update_user_meta($vendor->id, 'report_abuse_data', $previous_report_abuse_data);
            $mail = WC()->mailer()->emails['WC_Email_Send_Report_Abuse'];
            $result = $mail->trigger( $vendor, wc_clean($_POST) );
        }
        die();
    }

    function vendor_list_by_search_keyword() {
        global $MVX;
        // check vendor_search_nonce
        if (!isset($_POST['vendor_search_nonce']) || !wp_verify_nonce($_POST['vendor_search_nonce'], 'mvx_widget_vendor_search_form')) {
            die();
        }
        $html = '';
        if (isset($_POST['s']) && sanitize_text_field($_POST['s'])) {
            $args1 = array(
                'search' => '*' . wc_clean($_POST['s']) . '*',
                'search_columns' => array('display_name', 'user_login', 'user_nicename'),
            );
            $args2 = array(
                'meta_key' => '_vendor_page_title',
                'meta_value' => wc_clean($_POST['s']),
                'meta_compare' => 'LIKE',
            );
            $vendors1 = get_mvx_vendors($args1);
            $vendors2 = get_mvx_vendors($args2);
            $vendors = array_unique(array_merge($vendors1, $vendors2), SORT_REGULAR);

            if ($vendors) {
                foreach ($vendors as $vendors_key => $vendor) {
                    $vendor_term = get_term($vendor->term_id);
                    $vendor->image = $vendor->get_image() ? $vendor->get_image() : $MVX->plugin_url . 'assets/images/WP-stdavatar.png';
                    $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style=" width: 25%;  display: inline;">        
                    <img width="50" height="50" class="vendor_img" style="display: inline;" src="' . $vendor->image . '" id="vendor_image_display">
                    </div>
                    <div style=" width: 75%;  display: inline;  padding: 10px;">
                            <a href="' . esc_attr($vendor->permalink) . '">
                                ' . $vendor_term->name . '
                            </a>
                    </div>
                </div>';
                }
            } else {
                $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style="display: inline;  padding: 10px;">
                        ' . __('No Vendor Matched!', 'multivendorx') . '
                    </div>
                </div>';
            }
        } else {
            $vendors = get_mvx_vendors();
            if ($vendors) {
                foreach ($vendors as $vendors_key => $vendor) {
                    $vendor_term = get_term($vendor->term_id);
                    $vendor->image = $vendor->get_image() ? $vendor->get_image() : $MVX->plugin_url . 'assets/images/WP-stdavatar.png';
                    $html .= '<div style=" width: 100%; margin-bottom: 5px; clear: both; display: block;">
                    <div style=" width: 25%;  display: inline;">        
                    <img width="50" height="50" class="vendor_img" style="display: inline;" src="' . $vendor->image . '" id="vendor_image_display">
                    </div>
                    <div style=" width: 75%;  display: inline;  padding: 10px;">
                            <a href="' . esc_attr($vendor->permalink) . '">
                                ' . $vendor_term->name . '
                            </a>
                    </div>
                </div>';
                }
            }
        }
        echo $html;
        die();
    }

    public function delete_fpm_product() {
        check_ajax_referer('mvx-frontend', 'security');
        $proid = isset($_POST['proid']) ? wc_clean($_POST['proid']) : 0;
        if ($proid) {
            if (wp_delete_post($proid)) {
                //echo 'success';
                echo '{"status": "success", "shop_url": "' . get_permalink(wc_get_page_id('shop')) . '"}';
                die;
            }
            die;
        }
    }

    function fpm_get_image_id($attachment_url) {
        global $wpdb;
        $upload_dir_paths = wp_upload_dir();

        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        if (false !== strpos($attachment_url, $upload_dir_paths['baseurl'])) {
            $attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);

            // Remove the upload path base directory from the attachment URL
            $attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);

            // Finally, run a custom database query to get the attachment ID from the modified attachment URL
            $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url));
        }
        return $attachment_id;
    }

    public function mvx_vendor_product_list() {
        global $MVX;
        check_ajax_referer('mvx-product', 'security');
        if (is_user_logged_in() && is_user_mvx_vendor(get_current_user_id())) {
            $vendor = get_current_vendor();
            $enable_ordering = apply_filters('mvx_vendor_dashboard_product_list_table_orderable_columns', array('name', 'date'));
            $products_table_headers = array(
                'select_product' => '',
                'image' => '',
                'name' => __('Product', 'multivendorx'),
                'price' => __('Price', 'multivendorx'),
                'stock' => __('Stock', 'multivendorx'),
                'categories' => __('Categories', 'multivendorx'),
                'date' => __('Date', 'multivendorx'),
                'status' => __('Status', 'multivendorx'),
                'actions' => __('Actions', 'multivendorx'),
            );
            $products_table_headers = apply_filters('mvx_vendor_dashboard_product_list_table_headers', $products_table_headers);
            // storing columns keys for ordering
            $columns = array();
            foreach ($products_table_headers as $key => $value) {
                $columns[] = $key;
            }

            $requestData = ( $_REQUEST ) ? wp_unslash( $_REQUEST ) : array();
            $filterActionData = array();
            parse_str($requestData['products_filter_action'], $filterActionData);
            do_action('mvx_before_products_list_query_bind', $filterActionData, $requestData);
            $notices = array();
            // Do bulk handle
            if (isset($requestData['bulk_action']) && $requestData['bulk_action'] != '' && isset($filterActionData['selected_products']) && is_array($filterActionData['selected_products'])) {
                if ($requestData['bulk_action'] === 'trash') {
                    // Trash products
                    foreach ($filterActionData['selected_products'] as $id) {
                        wp_trash_post($id);
                    }
                    $notices[] = array(
                        'message' => ((count($filterActionData['selected_products']) > 1) ? sprintf(__('%s products', 'multivendorx'), count($filterActionData['selected_products'])) : sprintf(__('%s product', 'multivendorx'), count($filterActionData['selected_products']))) . ' ' . __('moved to the Trash.', 'multivendorx'),
                        'type' => 'success'
                    );
                } elseif ($requestData['bulk_action'] === 'untrash') {
                    // Untrash products
                    foreach ($filterActionData['selected_products'] as $id) {
                        wp_untrash_post($id);
                    }
                    $notices[] = array(
                        'message' => ((count($filterActionData['selected_products']) > 1) ? sprintf(__('%s products', 'multivendorx'), count($filterActionData['selected_products'])) : sprintf(__('%s product', 'multivendorx'), count($filterActionData['selected_products']))) . ' ' . __('restored from the Trash.', 'multivendorx'),
                        'type' => 'success'
                    );
                } elseif ($requestData['bulk_action'] === 'delete') {
                    if (current_user_can('delete_published_products')) {
                        // delete products
                        foreach ($filterActionData['selected_products'] as $id) {
                            wp_delete_post($id);
                        }
                        $notices[] = array(
                            'message' => ((count($filterActionData['selected_products']) > 1) ? sprintf(__('%s products', 'multivendorx'), count($filterActionData['selected_products'])) : sprintf(__('%s product', 'multivendorx'), count($filterActionData['selected_products']))) . ' ' . __('deleted from the Trash.', 'multivendorx'),
                            'type' => 'success'
                        );
                    } else {
                        $notices[] = array(
                            'message' => __('Sorry! You do not have this permission.', 'multivendorx'),
                            'type' => 'error'
                        );
                    }
                } else {
                    do_action('mvx_products_list_do_handle_bulk_actions', $vendor->get_products_ids(), $filterActionData['bulk_action'], $filterActionData['selected_products'], $filterActionData, $requestData);
                    //notice
                    $notices = apply_filters('mvx_products_list_action_notices', $notices, $vendor->get_products_ids(), $filterActionData, $requestData);
                }
            }
            $df_post_status = apply_filters('mvx_vendor_dashboard_default_product_list_statues', array('publish', 'pending', 'draft'), $requestData, $vendor);
            if (isset($requestData['post_status']) && $requestData['post_status'] != 'all') {
                $df_post_status = $requestData['post_status'];
            }
            $args = apply_filters( 'mvx_get_vendor_product_list_query_args', array(
                'posts_per_page' => -1,
                'offset' => 0,
                'category' => '',
                'category_name' => '',
                'orderby' => 'date',
                'order' => 'DESC',
                'include' => '',
                'exclude' => '',
                'meta_key' => '',
                'meta_value' => '',
                'post_type' => 'product',
                'post_mime_type' => '',
                'post_parent' => '',
                'author' => get_current_vendor_id(),
                'post_status' => $df_post_status,
                'suppress_filters' => true
            ), $vendor, $requestData );
            $tax_query = array();
            if (isset($filterActionData['product_cat']) && $filterActionData['product_cat'] != '') {
                $tax_query[] = array('taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $filterActionData['product_cat']);
            }
            if (isset($filterActionData['product_type']) && $filterActionData['product_type'] != '') {
                if ('downloadable' === $filterActionData['product_type']) {
                    $args['meta_value'] = 'yes';
                    $query_vars['meta_key'] = '_downloadable';
                } elseif ('virtual' === $filterActionData['product_types']) {
                    $query_vars['meta_value'] = 'yes';
                    $query_vars['meta_key'] = '_virtual';
                } else {
                    $tax_query[] = array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => $filterActionData['product_type']);
                }
            }
            if ($tax_query):
                $args['tax_query'] = $tax_query;
            endif;

            $total_products_array = $vendor->get_products(apply_filters('mvx_products_list_total_products_query_args', $args, $filterActionData, $requestData));
            // filter/ordering data
            if (!empty($requestData['search_keyword'])) {
                $args['s'] = $requestData['search_keyword'];
            }
            if (isset($columns[$requestData['order'][0]['column']]) && in_array($columns[$requestData['order'][0]['column']], $enable_ordering)) {
                $args['orderby'] = $columns[$requestData['order'][0]['column']];
                $args['order'] = $requestData['order'][0]['dir'];
            }
            if (isset($requestData['post_status']) && $requestData['post_status'] != 'all') {
                $args['post_status'] = $requestData['post_status'];
            }
            $args['offset'] = $requestData['start'];
            $args['posts_per_page'] = $requestData['length'];

            $args = apply_filters('mvx_datatable_product_list_query_args', $args, $filterActionData, $requestData);

            $data = array();
            $products_array = $vendor->get_products($args);
            if (!empty($products_array)) {
                foreach ($products_array as $product_single) {
                    $row = array();
                    $product = wc_get_product($product_single->ID);
                    $edit_product_link = '';

                    /* check if the product ID is the one of the current language in WPML */ 
                    if ( function_exists('icl_object_id') ) { // WPML activated
                        $correct_product_id = apply_filters( 'wpml_object_id',$product_single->ID , 'product', false, ICL_LANGUAGE_CODE );
                        if( $correct_product_id != $product_single->ID ){
                            continue; // skip the current loop and go to the next product
                        }
                    }
                    
                    if ((current_vendor_can('edit_published_products') && get_mvx_vendor_settings('is_edit_delete_published_product', 'products_capability')) || in_array($product->get_status(), apply_filters('mvx_enable_edit_product_options_for_statuses', array('draft', 'pending')))) {
                        $edit_product_link = esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product'), $product->get_id()));
                    }
                    if(!current_vendor_can('edit_product') && in_array($product->get_status(), apply_filters('mvx_enable_edit_product_options_for_statuses', array('draft', 'pending')))) $edit_product_link = '';
                    $edit_product_link = apply_filters('mvx_vendor_product_list_product_edit_link', $edit_product_link, $product);
                    // Get actions
                    $onclick = "return confirm('" . __('Are you sure want to delete this product?', 'multivendorx') . "')";
                    $view_title = __('View', 'multivendorx');
                    if (in_array($product->get_status(), array('draft', 'pending'))) {
                        $view_title = __('Preview', 'multivendorx');
                    }
                    $actions = array(
                        'id' => sprintf(__('ID: %d', 'multivendorx'), $product->get_id()),
                    );
                    // Add GTIN if have
                    if (get_mvx_vendor_settings('is_gtin_enable', 'general') == 'Enable') {
                        $gtin_terms = wp_get_post_terms($product->get_id(), $MVX->taxonomy->mvx_gtin_taxonomy);
                        $gtin_label = '';
                        if ($gtin_terms && isset($gtin_terms[0])) {
                            $gtin_label = $gtin_terms[0]->name;
                        }
                        $gtin_code = get_post_meta($product->get_id(), '_mvx_gtin_code', true);

                        if ($gtin_code) {
                            $actions['gtin'] = ( $gtin_label ) ? $gtin_label . ': ' . $gtin_code : __('GTIN', 'multivendorx') . ': ' . $gtin_code;
                        }
                    }

                    $dismiss_comment_id = get_post_meta($product->get_id(), '_comment_dismiss', true);
                    $dismiss_comment = get_comment($dismiss_comment_id);

                    $dismiss_reason_modal = '';
                    if ($dismiss_comment) {
                        $dismiss_reason_modal = '<div class="modal fade" id="mvx-product-dismiss-reason-modal-'.$product->get_id().'" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">'.__('Rejection Note', 'multivendorx').'</h4>
                                    </div>
                                    <div class="mvx-product-dismiss-modal modal-body order-notes">     
                                        <p class="order-note"><span>'.wptexturize( wp_kses_post( $dismiss_comment->comment_content ) ).'</span></p>
                                        <p>'. $dismiss_comment->comment_author .' - '. date_i18n(wc_date_format() . ' ' . wc_time_format(), strtotime($dismiss_comment->comment_date) ) .'</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    }

                    $actions_col = array(
                        'view' => '<a href="' . esc_url($product->get_permalink()) . '" target="_blank" title="' . $view_title . '"><i class="mvx-font ico-eye-icon"></i></a>',
                        'edit' => '<a href="' . esc_url($edit_product_link) . '" title="' . __('Edit', 'multivendorx') . '"><i class="mvx-font ico-edit-pencil-icon"></i></a>',
                        'restore' => '<a href="' . esc_url(wp_nonce_url(add_query_arg(array('product_id' => $product->get_id()), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_products_endpoint', 'seller_dashbaord', 'products'))), 'mvx_untrash_product')) . '" title="' . __('Restore from the Trash', 'multivendorx') . '"><i class="mvx-font ico-reply-icon"></i></a>',
                        'trash' => '<a class="productDelete" href="' . esc_url(wp_nonce_url(add_query_arg(array('product_id' => $product->get_id()), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_products_endpoint', 'seller_dashbaord', 'products'))), 'mvx_trash_product')) . '" title="' . __('Move to the Trash', 'multivendorx') . '"><i class="mvx-font ico-delete-icon"></i></a>',
                        'delete' => '<a class="productDelete" href="' . esc_url(wp_nonce_url(add_query_arg(array('product_id' => $product->get_id()), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_products_endpoint', 'seller_dashbaord', 'products'))), 'mvx_delete_product')) . '" onclick="' . $onclick . '" title="' . __('Delete Permanently', 'multivendorx') . '"><i class="mvx-font ico-delete-icon"></i></a>',
                        'dismiss' => $dismiss_reason_modal.'<a data-toggle="modal" data-target="#mvx-product-dismiss-reason-modal-'.$product->get_id().'" title="' . __('Click to view reason for dismiss', 'multivendorx') . '"><i class="mvx-font ico-reject-icon"></i></a>',
                    );
                    if ($product->get_status() == 'trash') {
                        $edit_product_link = '';
                        unset($actions_col['edit']);
                        unset($actions_col['trash']);
                        unset($actions_col['view']);
                    } else {
                        unset($actions_col['restore']);
                        unset($actions_col['delete']);
                    }

                    if(!get_post_meta($product->get_id(), '_dismiss_to_do_list', true))
                        unset($actions_col['dismiss']);

                    if (!current_vendor_can('edit_published_products') && !get_mvx_global_settings('is_edit_delete_published_product') && !in_array($product->get_status(), apply_filters('mvx_enable_edit_product_options_for_statuses', array('draft', 'pending')))) { 
                        unset($actions_col['edit']);
                        if ($product->get_status() != 'trash')
                            unset($actions_col['delete']);
                    }elseif(!current_vendor_can('edit_product') && in_array($product->get_status(), apply_filters('mvx_enable_edit_product_options_for_statuses', array('draft', 'pending')))){ unset($actions_col['edit']);}

                    $actions = apply_filters('mvx_vendor_product_list_row_actions', $actions, $product);
                    $actions_col = apply_filters('mvx_vendor_product_list_row_actions_column', $actions_col, $product);
                    $row_actions = array();
                    foreach ($actions as $action => $link) {
                        $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                    }
                    $row_actions_col = array();
                    foreach ($actions_col as $action => $link) {
                        $row_actions_col[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                    }
                    $action_html = '<div class="row-actions">' . implode(' <span class="divider">|</span> ', $row_actions) . '</div>';
                    $actions_col_html = '<div class="col-actions">' . implode(' <span class="divider">|</span> ', $row_actions_col) . '</div>';
                    // is in stock
                    if ($product->is_in_stock()) {
                        $stock_html = '<span class="text-success">' . __('In stock', 'multivendorx');
                        if ($product->managing_stock()) {
                            $stock_html .= ' (' . wc_stock_amount($product->get_stock_quantity()) . ')';
                        }
                        $stock_html .= '</span>';
                    } else {
                        $stock_html = '<span class="text-danger">' . __('Out of stock', 'multivendorx') . '</span>';
                    }
                    // product cat
                    $product_cats = '';
                    $termlist = array();
                    $terms = get_the_terms($product->get_id(), 'product_cat');
                    if (!$terms) {
                        $product_cats = '<span class="na">&ndash;</span>';
                    } else {
                        $terms = apply_filters('mvx_vendor_product_list_row_product_categories', $terms, $product);
                        foreach ($terms as $term) {
                            $termlist[] = $term->name;
                        }
                    }
                    if ($termlist) {
                        $product_cats = implode(' | ', $termlist);
                    }
                    $date = '&ndash;';
                    if ($product->get_status() == 'publish') {
                        $status = __('Published', 'multivendorx');
                        $date = mvx_date($product->get_date_created('edit'));
                    } elseif ($product->get_status() == 'pending') {
                        $status = __('Pending', 'multivendorx');
                    } elseif ($product->get_status() == 'draft') {
                        $status = __('Draft', 'multivendorx');
                    } elseif ($product->get_status() == 'private') {
                        $status = __('Private', 'multivendorx');
                    } elseif ($product->get_status() == 'trash') {
                        $status = __('Trash', 'multivendorx');
                    } else {
                        $status = ucfirst($product->get_status());
                    }
                    $row ['select_product'] = '<input type="checkbox" class="select_' . $product->get_status() . '" name="selected_products[' . $product->get_id() . ']" value="' . $product->get_id() . '" data-title="' . $product->get_title() . '" data-sku="' . $product->get_sku() . '"/>';
                    $row ['image'] = '<td>' . $product->get_image(apply_filters('mvx_vendor_product_list_image_size', array(40, 40))) . '</td>';
                    $row ['name'] = '<td><a href="' . esc_url($edit_product_link) . '">' . $product->get_title() . '</a>' . $action_html . '</td>';
                    $row ['price'] = '<td>' . $product->get_price_html() . '</td>';
                    $row ['stock'] = '<td>' . $stock_html . '</td>';
                    $row ['categories'] = '<td>' . $product_cats . '</td>';
                    $row ['date'] = '<td>' . $date . '</td>';
                    $row ['status'] = '<td>' . $status . '</td>';
                    $row ['actions'] = '<td>' . $actions_col_html . '</td>';
                    $data[] = apply_filters('mvx_vendor_dashboard_product_list_table_rows', $row, $product, $filterActionData, $requestData);
                }
            }

            $json_data = apply_filters('mvx_datatable_product_list_results', array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($total_products_array)), // total number of records
                "recordsFiltered" => intval(count($total_products_array)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data, // total data array
                "notices" => $notices   // set messages or motices
                    ), $filterActionData, $requestData);
            wp_send_json($json_data);
            die;
        }
    }

    public function mvx_vendor_unpaid_order_vendor_withdrawal_list() {
        global $MVX;
        check_ajax_referer('mvx-withdrawal', 'security');
        if (is_user_logged_in() && is_user_mvx_vendor(get_current_vendor_id())) {
            $vendor = get_mvx_vendor(get_current_vendor_id());
            $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
            $meta_query['meta_query'] = array(
                array(
                    'key' => '_paid_status',
                    'value' => array('unpaid', 'partial_refunded'),
                    'compare' => 'IN'
                ),
                array(
                    'key' => '_commission_vendor',
                    'value' => absint($vendor->term_id),
                    'compare' => '='
                )
            );
            $vendor_unpaid_total_orders = $vendor->get_unpaid_orders(false, false, $meta_query);
            $vendor_unpaid_total_orders = apply_filters( 'mvx_before_unpaid_order_vendor_withdrawal_lists', $vendor_unpaid_total_orders, $vendor, $requestData );
            $data = array();
            $commission_threshold_time = isset($MVX->vendor_caps->payment_cap['commission_threshold_time']) && !empty($MVX->vendor_caps->payment_cap['commission_threshold_time']) ? $MVX->vendor_caps->payment_cap['commission_threshold_time'] : 0;
            if ($vendor_unpaid_total_orders) {
                foreach ($vendor_unpaid_total_orders as $commission_id => $order_id) {
                    $order = wc_get_order($order_id);
                    if( $order ) {
                        $vendor_order = mvx_get_order($order_id);
                        $commission_create_date = get_the_date('U', $commission_id);
                        $current_date = date('U');
                        $diff = intval(($current_date - $commission_create_date) / (3600 * 24));
                        if ($diff < $commission_threshold_time) {
                            continue;
                        }
                        $payment_settings = mvx_get_option('mvx_payment_settings_name', true);
                        if (is_array($payment_settings) && !empty($payment_settings)) {
                            if (array_key_exists('order_withdrawl_status'. $order->get_status('edit'), $payment_settings)) {
                                continue;
                            }
                        }
                        
                        $allowd_withdrawals_settings = get_mvx_global_settings('order_withdrawl_status') ? wp_list_pluck(array_filter(get_mvx_global_settings('order_withdrawl_status')), 'value') : array();
                        if (is_commission_requested_for_withdrawals($commission_id) || empty($allowd_withdrawals_settings)) {
                            $disabled_reqested_withdrawals = 'disabled';
                        } elseif (!in_array($order->get_status('edit'), $allowd_withdrawals_settings)) {
                            $disabled_reqested_withdrawals = 'disabled';
                        } else {
                            $disabled_reqested_withdrawals = '';
                        }

                        //skip withdrawal for COD order and vendor end shipping
                        if ($order->get_payment_method() == 'cod' && $vendor->is_shipping_enable())
                            continue;
                        
                        $commission_amount = get_post_meta( $commission_id, '_commission_amount', true );
                        $shipping_amount = get_post_meta( $commission_id, '_shipping', true );
                        $tax_amount = get_post_meta( $commission_id, '_tax', true );
                        
                        $row = array();
                        $row ['select_withdrawal'] = '<input name="commissions[]" value="' . $commission_id . '" class="select_withdrawal" type="checkbox" ' . $disabled_reqested_withdrawals . '>';
                        $row ['order_id'] = $order->get_id();
                        $row ['commission_amount'] = wc_price($commission_amount, array('currency' => $order->get_currency()));
                        $row ['shipping_amount'] = wc_price($shipping_amount, array('currency' => $order->get_currency()));
                        $row ['tax_amount'] = wc_price($tax_amount, array('currency' => $order->get_currency()));
                        $row ['total'] = ( $vendor_order ) ? $vendor_order->get_commission_total() : wc_price(0);
                        $data[] = apply_filters('mvx_vendor_withdrawal_list_rows', $row, $commission_id);
                    }
                }
            }
            $total_array = $data;
            $data = array_slice( $data, $requestData['start'], $requestData['length'] );

            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($total_array)), // total number of records
                "recordsFiltered" => intval(count($total_array)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    public function mvx_vendor_coupon_list() {
        check_ajax_referer('mvx-coupon', 'security');
        if (is_user_logged_in() && is_user_mvx_vendor(get_current_vendor_id())) {
            $vendor = get_mvx_vendor(get_current_vendor_id());
            $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
            $args = apply_filters( 'mvx_get_vendor_coupon_list_query_args', array(
                'posts_per_page' => -1,
                'offset' => 0,
                'category' => '',
                'category_name' => '',
                'orderby' => 'date',
                'order' => 'DESC',
                'include' => '',
                'exclude' => '',
                'meta_key' => '',
                'meta_value' => '',
                'post_type' => 'shop_coupon',
                'post_mime_type' => '',
                'post_parent' => '',
                'author' => get_current_vendor_id(),
                'post_status' => array('publish', 'pending', 'draft', 'trash'),
                'suppress_filters' => true
            ), $vendor, $requestData );
            $vendor_total_coupons = get_posts($args);
            $args['offset'] = $requestData['start'];
            $args['posts_per_page'] = $requestData['length'];
            $vendor_coupons = get_posts($args);
            $data = array();
            if ($vendor_coupons) {
                foreach ($vendor_coupons as $coupon_single) {
                    $edit_coupon_link = '';
                    if (current_user_can('edit_published_shop_coupons') && get_mvx_vendor_settings('is_edit_delete_published_coupon', 'products_capability')) {
                        $edit_coupon_link = esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_add_coupon_endpoint', 'seller_dashbaord', 'add-coupon'), $coupon_single->ID));
                    }
                    // Get actions
                    $onclick = "return confirm('" . __('Are you sure want to delete this coupon?', 'multivendorx') . "')";
                    $actions = array(
                        'id' => sprintf(__('ID: %d', 'multivendorx'), $coupon_single->ID),
                    );
                    $actions_col = array(
                        'edit' => '<a href="' . esc_url($edit_coupon_link) . '" title="' . __('Edit', 'multivendorx') . '"><i class="mvx-font ico-edit-pencil-icon"></i></a>',
                        'restore' => '<a href="' . esc_url(wp_nonce_url(add_query_arg(array('coupon_id' => $coupon_single->ID), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_coupons_endpoint', 'seller_dashbaord', 'coupons'))), 'mvx_untrash_coupon')) . '" title="' . __('Restore from the Trash', 'multivendorx') . '"><i class="mvx-font ico-reply-icon"></i></a>',
                        'trash' => '<a class="couponDelete" href="' . esc_url(wp_nonce_url(add_query_arg(array('coupon_id' => $coupon_single->ID), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_coupons_endpoint', 'seller_dashbaord', 'coupons'))), 'mvx_trash_coupon')) . '" title="' . __('Move to the Trash', 'multivendorx') . '"><i class="mvx-font ico-delete-icon"></i></a>',
                        'delete' => '<a class="couponDelete" href="' . esc_url(wp_nonce_url(add_query_arg(array('coupon_id' => $coupon_single->ID), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_coupons_endpoint', 'products_capability'))), 'mvx_delete_coupon')) . '" onclick="' . $onclick . '" title="' . __('Delete Permanently', 'multivendorx') . '"><i class="mvx-font ico-delete-icon"></i></a>',
                    );
                    if ($coupon_single->post_status == 'trash') {
                        unset($actions_col['edit']);
                        unset($actions_col['trash']);
                    } else {
                        unset($actions_col['restore']);
                        unset($actions_col['delete']);
                    }
                    if (!current_user_can('edit_published_shop_coupons') || get_mvx_vendor_settings('is_edit_delete_published_coupon', 'products_capability')) {
                        unset($actions['edit']);
                        unset($actions['delete']);
                    }
                    $actions = apply_filters('mvx_vendor_coupon_list_row_actions', $actions, $coupon_single);
                    $actions_col = apply_filters('mvx_vendor_coupon_list_row_actions_col', $actions_col, $coupon_single);
                    $row_actions = array();
                    foreach ($actions as $action => $link) {
                        $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                    }
                    $action_html = '<div class="row-actions">' . implode(' | ', $row_actions) . '</div>';
                    $row_actions_cols = array();
                    foreach ($actions_col as $action => $link) {
                        $row_actions_cols[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                    }
                    $actions_col_html = '<div class="col-actions">' . implode(' | ', $row_actions_cols) . '</div>';
                    $coupon = new WC_Coupon($coupon_single->ID);
                    $usage_count = $coupon->get_usage_count();
                    $usage_limit = $coupon->get_usage_limit();
                    $usage_limit = $usage_limit ? $usage_limit : '&infin;';

                    if ($coupon->get_date_expires()) {
                        $expiry_date = mvx_date($coupon->get_date_expires());
                    } else {
                        $expiry_date = '&ndash;';
                    }

                    $row = array();
                    $row ['coupons'] = '<a href="' . esc_url($edit_coupon_link) . '">' . get_the_title($coupon_single->ID) . '</a>' . $action_html;
                    $row ['type'] = esc_html(wc_get_coupon_type($coupon->get_discount_type()));
                    $row ['amount'] = $coupon->get_amount();
                    $row ['uses_limit'] = $usage_count . ' / ' . $usage_limit;
                    $row ['expiry_date'] = $expiry_date;
                    $row ['actions'] = $actions_col_html;
                    $data[] = apply_filters('mvx_vendor_coupon_list_rows', $row, $coupon);
                }
            }

            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($vendor_total_coupons)), // total number of records
                "recordsFiltered" => intval(count($vendor_total_coupons)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    public function mvx_vendor_transactions_list() {
        global $MVX;
        check_ajax_referer('mvx-transaction', 'security');
        if (is_user_logged_in() && is_user_mvx_vendor(get_current_vendor_id())) {
            $vendor = get_mvx_vendor(get_current_vendor_id());
            $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
            $vendor = apply_filters('mvx_transaction_vendor', $vendor);
            $start_date = isset($requestData['from_date']) ? $requestData['from_date'] : date('Y-m-01');
            $end_date = isset($requestData['to_date']) ? $requestData['to_date'] : date('Y-m-d');
            $transaction_details = $MVX->transaction->get_transactions($vendor->term_id, $start_date, $end_date, array('mvx_processing', 'mvx_completed', 'wcmp_completed', 'wcmp_processing'));

            $data = array();
            if (!empty($transaction_details)) {
                foreach ($transaction_details as $transaction_id => $detail) {
                    $trans_post = get_post($transaction_id);
                    $order_ids = $commssion_ids = '';
                    $commission_details = get_post_meta($transaction_id, 'commission_detail', true);
                    $order_id = array();
                    foreach ($commission_details as $commission_detail)
                        $order_id[] = get_post_meta($commission_detail, '_commission_order_id', true);
                    $transfer_charge = get_post_meta($transaction_id, 'transfer_charge', true);
                    $transaction_amt = get_post_meta($transaction_id, 'amount', true) - get_post_meta($transaction_id, 'transfer_charge', true) - get_post_meta($transaction_id, 'gateway_charge', true);
                    $row = array();
                    $row ['select_transaction'] = '<input name="transaction_ids[]" value="' . $transaction_id . '"  class="select_transaction" type="checkbox" >';
                    $row ['date'] = mvx_date($trans_post->post_date);
                    $row ['order_id'] = '#'. implode(', #', $order_id);
                    $row ['transaction_id'] = '<a href="' . esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_transaction_details_endpoint', 'seller_dashbaord', 'transaction-details'), $transaction_id)) . '">#' . $transaction_id . '</a>';
                    $row ['commission_ids'] = '#' . implode(', #', $commission_details);
                    $row ['fees'] = isset($transfer_charge) ? wc_price($transfer_charge) : wc_price(0);
                    $row ['net_earning'] = wc_price($transaction_amt);
                    $data[] = apply_filters('mvx_vendor_transaction_list_rows', $row, $transaction_id);
                }
            }
            $data = array_slice( $data, $requestData['start'], $requestData['length'] );
            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($transaction_details)), // total number of records
                "recordsFiltered" => intval(count($transaction_details)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    /**
     * Customer Questions and Answers data handler
     */
    public function mvx_customer_ask_qna_handler() {
        global $MVX, $wpdb;
        $handler = isset($_POST['handler']) ? wc_clean($_POST['handler']) : '';
        $msg = '';
        $no_data = '';
        $qna_data = '';
        $remain_data = '';
        $redirect = '';

        if ($handler == 'submit') {
            $qna_form_data = array();
            $customer_qna_data = isset($_POST['customer_qna_data']) ? wp_unslash($_POST['customer_qna_data']) : '';
            parse_str($customer_qna_data, $qna_form_data);
            $wpnonce = isset($qna_form_data['cust_qna_nonce']) ? $qna_form_data['cust_qna_nonce'] : '';
            $product_id = isset($qna_form_data['product_ID']) ? (int) $qna_form_data['product_ID'] : 0;
            $cust_id = isset($qna_form_data['cust_ID']) ? (int) $qna_form_data['cust_ID'] : 0;
            $cust_question = isset($qna_form_data['cust_question']) ? sanitize_text_field($qna_form_data['cust_question']) : '';
            $vendor = get_mvx_product_vendors($product_id);
            $redirect = get_permalink($product_id);
            $customer = wp_get_current_user();
            $cust_qna = array();
            if ($wpnonce && wp_verify_nonce($wpnonce, 'mvx_customer_qna_form_submit') && $product_id && $cust_question) {
                $result = $MVX->product_qna->createQuestion(array(
                    'product_ID' => $product_id,
                    'ques_details' => sanitize_text_field($cust_question),
                    'ques_by' => $cust_id,
                    'ques_created' => date('Y-m-d H:i:s', current_time('timestamp')),
                    'ques_vote' => '',
                    'status' => 'pending'
                ));
                if ($result) {
                    //delete transient
                    if (get_transient('mvx_customer_qna_for_vendor_' . $vendor->id)) {
                        delete_transient('mvx_customer_qna_for_vendor_' . $vendor->id);
                    }
                    $no_data = 0;
                    $msg = __("Your question submitted successfully!", 'multivendorx');
                    $email_vendor = WC()->mailer()->emails['WC_Email_Vendor_New_Question'];
                    $email_vendor->trigger( $vendor, $product_id, $cust_question, $cust_id );
                    $email_admin = WC()->mailer()->emails['WC_Email_Admin_New_Question'];
                    $email_admin->trigger( $vendor, $product_id, $cust_question, $cust_id );
                    wc_add_notice($msg, 'success');
                    do_action('mvx_product_qna_after_question_submitted', $product_id, $cust_id, $cust_question);
                }
            }
        } elseif ($handler == 'search') {
            $keyword = isset($_POST['keyword']) ? wc_clean( $_POST['keyword'] ) : '';
            $product_id = isset($_POST['product_ID']) ? absint( $_POST['product_ID'] ) : 0;
            $product = wc_get_product($product_id);
            if ($product) {
                //$vendor = get_mvx_product_vendors( $product->get_id() );
                $qnas_data = $MVX->product_qna->get_Product_QNA($product->get_id(), array('sortby' => 'vote'));
                if ($keyword) {
                    $qnas_data = array_filter($qnas_data, function($data) use ($keyword) {
                        return ( strpos(strtolower($data->ques_details), $keyword) !== false );
                    });
                }
                if ($qnas_data) {
                    foreach ($qnas_data as $qna) {
                        $vendor = get_mvx_vendor($qna->ans_by);
                        if ($vendor) {
                            $vendor_term = get_term($vendor->term_id);
                            $ans_by = $vendor_term->name;
                        } else {
                            $ans_by = get_userdata($qna->ans_by)->display_name;
                        }
                        $qna_data .= '<div class="qna-item-wrap item-' . $qna->ques_ID . '">
                        <div class="qna-block">
                            <div class="qna-vote">';
                        $count = 0;
                        $ans_vote = maybe_unserialize($qna->ans_vote);
                        if (is_array($ans_vote)) {
                            $count = array_sum($ans_vote);
                        }
                        $qna_data .= '<div class="vote">';
                        if (is_user_logged_in()) {
                            if ($ans_vote && array_key_exists(get_current_user_id(), $ans_vote)) {
                                if ($ans_vote[get_current_user_id()] > 0) {
                                    $qna_data .= '<a href="javascript:void(0)" title="' . __('You already gave a thumbs up.', 'multivendorx') . '" class="give-up-vote" data-vote="up" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-like"></i></a>
                                    <span class="vote-count">' . $count . '</span>
                                    <a href="" title="' . __('Give a thumbs down', 'multivendorx') . '" class="give-vote-btn give-down-vote" data-vote="down" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-dislike"></i></a>';
                                } else {
                                    $qna_data .= '<a href="" title="' . __('Give a thumbs up', 'multivendorx') . '" class="give-vote-btn give-up-vote" data-vote="up" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-like"></i></a>
                                    <span class="vote-count">' . $count . '</span>
                                    <a href="javascript:void(0)" title="' . __('You already gave a thumbs down.', 'multivendorx') . '" class="give-vote-btn give-down-vote" data-vote="down" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-dislike"></i></a>';
                                }
                            } else {
                                $qna_data .= '<a href="" title="' . __('Give a thumbs up', 'multivendorx') . '" class="give-vote-btn give-up-vote" data-vote="up" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-like"></i></a>
                                    <span class="vote-count">' . $count . '</span>
                                    <a href="" title="' . __('Give a thumbs down', 'multivendorx') . '" class="give-vote-btn give-down-vote" data-vote="down" data-ans="' . $qna->ans_ID . '"><i class="vote-sprite vote-sprite-dislike"></i></a>';
                            }
                        } else {
                            $qna_data .= '<a href="javascript:void(0)" class="non_loggedin"><i class="vote-sprite vote-sprite-like"></i></a><span class="vote-count">' . $count . '</span><a href="javascript:void(0)" class="non_loggedin"><i class="vote-sprite vote-sprite-dislike"></i></a>';
                        }
                        $qna_data .= '</div></div>'
                                . '<div class="qtn-content">'
                                . '<div class="qtn-row">'
                                . '<p class="qna-question">'
                                . '<span>' . __('Q: ', 'multivendorx') . ' </span>' . $qna->ques_details . '</p>'
                                . '</div>'
                                . '<div class="qtn-row">'
                                . '<p class="qna-answer">'
                                . '<span>' . __('A: ', 'multivendorx') . ' </span>' . $qna->ans_details . '</p>'
                                . '</div>'
                                . '<div class="bottom-qna">'
                                . '<ul class="qna-info">';

                        $qna_data .= '<li class="qna-user">' . $ans_by . '</li>'
                                . '<li class="qna-date">' . date_i18n(wc_date_format(), strtotime($qna->ans_created)) . '</li>'
                                . '</ul>'
                                . '</div>'
                                . '</div></div></div>';
                    }
                    if (count($qnas_data) > 4) {
                        $qna_data .= '<div class="qna-item-wrap load-more-qna"><a href="" class="load-more-btn button" style="width:100%;text-align:center;">' . __('Load More', 'multivendorx') . '</a></div>';
                    }
                }
            }
            if (empty($qna_data)) {
                if (!is_user_logged_in()) {
                    $msg = __("You are not logged in.", 'multivendorx');
                }
                $no_data = 1;
            }
        } elseif ($handler == 'answer') {
            $ques_ID = isset($_POST['key']) ? wc_clean( $_POST['key'] ) : '';
            $reply = isset($_POST['reply']) ? sanitize_textarea_field($_POST['reply']) : '';
            $vendor = get_mvx_vendor(get_current_user_id());
            $question_info = $MVX->product_qna->get_Question($ques_ID);
            $product_id = $question_info->product_ID;
            $customer = get_userdata($question_info->ques_by);
            if ($vendor && $reply && $ques_ID) {
                $_is_answer_given = $MVX->product_qna->get_Answers($ques_ID);
                if (isset($_is_answer_given[0]) && count($_is_answer_given[0]) > 0) {
                    $result = $MVX->product_qna->updateAnswer($_is_answer_given[0]->ans_ID, array('ans_details' => $reply));
                } else {
                    $result = $MVX->product_qna->createAnswer(array(
                        'ques_ID' => $ques_ID,
                        'ans_details' => $reply,
                        'ans_by' => $vendor->id,
                        'ans_created' => date('Y-m-d H:i:s', current_time('timestamp')),
                        'ans_vote' => ''
                    ));
                }
                if ($result) {
                    //delete transient
                    if (get_transient('mvx_customer_qna_for_vendor_' . $vendor->id)) {
                        delete_transient('mvx_customer_qna_for_vendor_' . $vendor->id);
                    }
                    $remain_data = count($MVX->product_qna->get_Vendor_Questions($vendor));
                    if ($remain_data == 0) {
                        $msg = __('No more customer query found.', 'multivendorx');
                    } else {
                        $msg = '';
                    }
                    $email_customer = WC()->mailer()->emails['WC_Email_Customer_Answer'];
                    $email_customer->trigger( $customer, $reply, $product_id );
                    do_action('mvx_product_qna_after_answer_submitted', $ques_ID, $vendor, $reply);
                    $qna_data = '';
                    $no_data = 0;
                } else {
                    $no_data = 1;
                }
            }
        } elseif ($handler == 'vote_answer') {
            $ans_ID = isset($_POST['ans_ID']) ? absint($_POST['ans_ID']) : 0;
            $vote_type = isset($_POST['vote']) ? wc_clean($_POST['vote']) : '';
            $ans_row = $MVX->product_qna->get_Answer($ans_ID);
            $ques_row = $MVX->product_qna->get_Question($ans_row->ques_ID);
            $vote = maybe_unserialize($ans_row->ans_vote);
            $redirect = get_permalink($ques_row->product_ID);
            if (!$vote) {
                $vote = array();
            }
            if ($ans_ID && $vote_type && is_user_logged_in()) {
                if ($vote_type == 'up') {
                    $vote[get_current_user_id()] = +1;
                } else {
                    $vote[get_current_user_id()] = -1;
                }
                $result = $MVX->product_qna->updateAnswer($ans_ID, array('ans_vote' => maybe_serialize($vote)));
                if ($result) {
                    $qna_data = '';
                    $msg = __("Thanks for your vote!", 'multivendorx');
                    $no_data = 0;
                    wc_add_notice($msg, 'success');
                    do_action('mvx_product_qna_after_vote_submitted', $ans_ID, $vote_type);
                } else {
                    $no_data = 1;
                }
            }
        } elseif ($handler == 'update_answer') {
            $result = false;
            $ans_ID = isset($_POST['key']) ? absint($_POST['key']) : 0;
            $answer = isset($_POST['answer']) ? wc_clean($_POST['answer']) : '';
            if ($ans_ID) {
                $result = $MVX->product_qna->updateAnswer($ans_ID, array('ans_details' => sanitize_textarea_field($answer)));
            }
            if ($result) {
                $qna_data = '';
                $msg = __("Answer updated successfully!", 'multivendorx');
                $no_data = 0;
                wc_add_notice($msg, 'success');
                do_action('mvx_product_qna_after_update_answer_submitted', $ans_ID, $answer);
            } else {
                $no_data = 1;
            }
        }
        wp_send_json(array('no_data' => $no_data, 'message' => $msg, 'data' => $qna_data, 'remain_data' => $remain_data, 'redirect' => $redirect, 'is_user' => is_user_logged_in()));
        die();
    }

    public function mvx_vendor_dashboard_reviews_data() {
        $vendor = get_current_vendor();
        $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
        $data = array();
        $vendor_reviews_total = array();
        if (get_transient('mvx_dashboard_reviews_for_vendor_' . $vendor->id)) {
            $vendor_reviews_total = get_transient('mvx_dashboard_reviews_for_vendor_' . $vendor->id);
        } else {
            $query = array('meta_query' => array(
                    array(
                        'key' => '_mark_as_replied',
                        'value' => 1,
                        'compare' => 'NOT EXISTS',
                    )
            ));
            $vendor_reviews_total = $vendor->get_reviews_and_rating(0, '', $query, $query);
            set_transient('mvx_dashboard_reviews_for_vendor_' . $vendor->id, $vendor_reviews_total);
        }
        //$vendor_reviews_total = $vendor->get_reviews_and_rating(0, -1, $query);
        //$vendor_reviews = $vendor->get_reviews_and_rating($requestData['start'], $requestData['length'], $query);
        if ($vendor_reviews_total) {
            $vendor_reviews = array_slice($vendor_reviews_total, $requestData['start'], $requestData['length']);
            foreach ($vendor_reviews as $comment) :
                $vendor = get_mvx_vendor($comment->user_id);
                if ($vendor) {
                    $vendor_term = get_term($vendor->term_id);
                    $comment_by = $vendor_term->name;
                } else {
                    $comment_by = get_userdata($comment->user_id)->display_name;
                }
                $row = '';
                $row = '<div class="media-left pull-left">   
                        <a href="#">' . get_avatar($comment->user_id, 50, '', '') . '</a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">' . $comment_by . ' -- <small>' . human_time_diff(strtotime($comment->comment_date)) . __(' ago', 'multivendorx') . '</small></h4>
                        <p>' . wp_trim_words($comment->comment_content, 250, '...') . '</p>
                        <a data-toggle="modal" data-target="#commient-modal-' . $comment->comment_ID . '">' . __('Reply', 'multivendorx') . '</a>
                        <!-- Modal -->
                        <div class="modal fade" id="commient-modal-' . $comment->comment_ID . '" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">' . __('Reply to ', 'multivendorx') . $comment_by . '</h4>
                                    </div>
                                    <div class="mvx-widget-modal modal-body">
                                            <textarea class="form-control" rows="5" id="comment-content-' . $comment->comment_ID . '" placeholder="' . __('Enter reply...', 'multivendorx') . '"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-comment_id="' . $comment->comment_ID . '" data-vendor_id="' . get_current_vendor_id() . '" class="btn btn-default mvx-comment-reply">' . __('Comment', 'multivendorx') . '</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>';

                $data[] = array($row);
            endforeach;
        }
        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($vendor_reviews_total)), // total number of records
            "recordsFiltered" => intval(count($vendor_reviews_total)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data   // total data array
        );
        wp_send_json($json_data);
        die;
    }

    public function mvx_vendor_dashboard_customer_questions_data() {
        global $MVX;
        $vendor = get_current_vendor();
        $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
        $data_html = array();
        $active_qna_total = array();
        if (get_transient('mvx_customer_qna_for_vendor_' . $vendor->id)) {
            $active_qna_total = get_transient('mvx_customer_qna_for_vendor_' . $vendor->id);
        } else {
            $active_qna_total = $MVX->product_qna->get_Vendor_Questions($vendor);
            set_transient('mvx_customer_qna_for_vendor_' . $vendor->id, $active_qna_total);
        }
        if ($active_qna_total) {
            $active_qna = array_slice($active_qna_total, $requestData['start'], $requestData['length']);
            if ($active_qna) {
                foreach ($active_qna as $key => $data) :
                    $product = wc_get_product($data->product_ID);
                    if ($product) {
                        $row = '';
                        $row .= '<article id="reply-item-' . $data->ques_ID . '" class="reply-item">
                        <div class="media">
                            <!-- <div class="media-left">' . $product->get_image() . '</div> -->
                            <div class="media-body">
                                <h4 class="media-heading qna-question">' . wp_trim_words($data->ques_details, 160, '...') . '</h4>
                                <time class="qna-date">
                                    <span>' . mvx_date($data->ques_created) . '</span>
                                </time>';
                        if($data->status == 'pending') {
                            $row .= '<div class="mvx_vendor_question"><a class="accept_verification do_verify" id="question_response" data-verification="question_verification" data-action="verified" data-question_id="'.$data->ques_ID.'" data-product="'.$data->product_ID.'"><i class="mvx-font ico-approve-icon action-icon"></i></a>
                             <a class="reject_verification do_verify" id="question_response" data-verification="question_verification" data-action="rejected" data-question_id="'.$data->ques_ID.'" data-product="'.$data->product_ID.'"><i class="mvx-font ico-delete-icon action-icon"></i></a></div>';
                         } else {
                            $row .='<a data-toggle="modal" data-target="#qna-reply-modal-' . $data->ques_ID . '" >' . __('Reply', 'multivendorx') . '</a>';
                        }
                                /* Modal*/
                        $row .='<div class="modal fade" id="qna-reply-modal-' . $data->ques_ID . '" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">' . __('Product - ', 'multivendorx') . ' ' . $product->get_formatted_name() . '</h4>
                                            </div>
                                            <div class="mvx-widget-modal modal-body">
                                                    <label class="qna-question">' . stripslashes($data->ques_details) . '</label>
                                                    <textarea class="form-control" rows="5" id="qna-reply-' . $data->ques_ID . '" placeholder="' . __('Post your answer...', 'multivendorx') . '"></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-key="' . $data->ques_ID . '" class="btn btn-default mvx-add-qna-reply">' . __('Add', 'multivendorx') . '</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>';

                        $data_html[] = array($row);
                    }
                endforeach;
            }
        }

        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($active_qna_total)), // total number of records
            "recordsFiltered" => intval(count($active_qna_total)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data_html   // total data array
        );
        wp_send_json($json_data);
        die;
    }

    public function mvx_vendor_products_qna_list() {
        global $MVX;
        $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
        $vendor = get_current_vendor();
        // filter by status
        if (isset($requestData['qna_status']) && $requestData['qna_status'] == 'all' && $requestData['qna_status'] != '') {
            $vendor_questions_n_answers = $MVX->product_qna->get_Vendor_Questions($vendor, false);
        } else {
            $vendor_questions_n_answers = $MVX->product_qna->get_Vendor_Questions($vendor, true);
        }
        // filter by products
        if (isset($requestData['qna_products']) && is_array($requestData['qna_products'])) {
            if ($vendor_questions_n_answers) {
                foreach ($vendor_questions_n_answers as $key => $qna_ques) {
                    if (!in_array($qna_ques->product_ID, $requestData['qna_products'])) {
                        unset($vendor_questions_n_answers[$key]);
                    }
                }
            }
        }
        $vendor_qnas = array_slice($vendor_questions_n_answers, $requestData['start'], $requestData['length']);
        $data = array();

        if ($vendor_qnas) {
            // filter by vote
            if ($requestData['order'][0]['dir'] != 'asc') {
                $votes = array();
                foreach ($vendor_qnas as $key => $qna_ques) {
                    $count = 0;
                    $have_answer = $MVX->product_qna->get_Answers($qna_ques->ques_ID);
                    if (isset($have_answer[0]) && count($have_answer[0]) > 0) {
                        $ans_vote = maybe_unserialize($have_answer[0]->ans_vote);
                        if (is_array($ans_vote)) {
                            $count = array_sum($ans_vote);
                        }
                        $vendor_qnas[$key]->vote_count = $count;
                        $votes[$key] = $count;
                    } else {
                        $vendor_qnas[$key]->vote_count = $count;
                        $votes[$key] = $count;
                    }
                }
                array_multisort($votes, SORT_DESC, $vendor_qnas);
            }

            foreach ($vendor_qnas as $question) {
                $product = wc_get_product($question->product_ID);
                if ($product) {
                    $have_answer = $MVX->product_qna->get_Answers($question->ques_ID);
                    $details = '';
                    $status = '';
                    $vote = '&ndash;';
                    if (!isset($have_answer[0])) {
                        $status = '<span class="unanswered label label-default">' . __('Unanswered', 'multivendorx') . '</span>';
                        $details .= '<div class="mvx-question-details-modal modal-body">
                                        <textarea class="form-control" rows="5" id="qna-reply-' . $question->ques_ID . '" placeholder="' . __('Post your answer...', 'multivendorx') . '"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-key="' . $question->ques_ID . '" class="btn btn-default mvx-add-qna-reply">' . __('Add', 'multivendorx') . '</button>
                                    </div>';
                        $reply = '<a data-toggle="modal" data-target="#question-details-modal-' . $question->ques_ID . '" data-ques="' . $question->ques_ID . '" class="question-details"><i class="mvx-font ico-reply-icon action-icon"></i></a>';
                    } else {
                        $status = '<span class="answered label label-success">' . __('Answered', 'multivendorx') . '</span>';
                        $reply = '<a data-toggle="modal" data-target="#question-details-modal-' . $question->ques_ID . '" data-ques="' . $question->ques_ID . '" class="question-details"><i class="mvx-font ico-edit-pencil-icon action-icon"></i></a>';
                        $ans_vote = maybe_unserialize($have_answer[0]->ans_vote);
                        if (is_array($ans_vote)) {
                            $vote = array_sum($ans_vote);
                            if ($vote > 0) {
                                $vote = '<span class="label label-success">' . $vote . '</span>';
                            } else {
                                $vote = '<span class="label label-danger">' . $vote . '</span>';
                            }
                        }
                        if (apply_filters('mvx_vendor_can_modify_qna_answer', false)) {
                            $details .= '<div class="mvx-question-details-modal modal-body">
                                        <textarea class="form-control" rows="5" id="qna-answer-' . $have_answer[0]->ans_ID . '">' . stripslashes($have_answer[0]->ans_details) . '</textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-key="' . $have_answer[0]->ans_ID . '" class="btn btn-default mvx-update-qna-answer">' . __('Edit', 'multivendorx') . '</button>
                                    </div>';
                        } else {
                            $details .= '<div class="mvx-question-details-modal modal-body">
                                        <textarea class="form-control" rows="5" id="qna-answer-' . $have_answer[0]->ans_ID . '" disabled>' . stripslashes($have_answer[0]->ans_details) . '</textarea>
                                    </div>';
                        }
                    }
                    if($question->status == 'pending') {
                        $qnas  = wp_trim_words(stripslashes($question->ques_details), 160, '...');
                        $action_button = '<div class="mvx_vendor_question"><a class="accept_verification do_verify" id="question_response" data-verification="question_verification" data-action="verified" data-question_id="'.$question->ques_ID.'" data-product="'.$pending_question->product_ID.'"><i class="mvx-font ico-approve-icon action-icon"></i></a>
                                 <a class="reject_verification do_verify" id="question_response" data-verification="question_verification" data-action="rejected" data-question_id="'.$question->ques_ID.'" data-product="'.$pending_question->product_ID.'"><i class="mvx-font ico-delete-icon action-icon"></i></a></div>';
                
                    } else {
                        $qnas  = '<a data-toggle="modal" data-target="#question-details-modal-' . $question->ques_ID . '" data-ques="' . $question->ques_ID . '" class="question-details">' . wp_trim_words(stripslashes($question->ques_details), 160, '...') . '</a>';
                        $action_button  = '<a data-toggle="modal" data-target="#question-details-modal-' . $question->ques_ID . '" data-ques="' . $question->ques_ID . '" class="question-details">' . $reply . '</a>';
                    }
                    $data[] = array(
                        'qnas' => $qnas
                        . '<!-- Modal -->
                                <div class="modal fade" id="question-details-modal-' . $question->ques_ID . '" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">' . stripslashes($question->ques_details) . '</h4>
                                            </div>
                                            ' . $details . '
                                        </div>
                                    </div>
                                </div>',
                        'product' => '<a href="' . esc_html($product->get_permalink()) . '" target="_blank">' . $product->get_title() . '</a>',
                        'date' => mvx_date($question->ques_created),
                        'vote' => $vote,
                        'status' => $status,
                        'action' => $action_button
                    );
                }
            }
        }
        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($vendor_questions_n_answers)), // total number of records
            "recordsFiltered" => intval(count($vendor_questions_n_answers)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data   // total data array
        );
        wp_send_json($json_data);
    }

    public function mvx_question_verification_approval() {
        global $MVX;
        check_ajax_referer('mvx-vendors', 'security');
        $data = array();
        if(!empty($_POST['question_id'])){
            $question_id = isset($_POST['question_id']) ? absint($_POST['question_id']) : 0;
            if(!empty($_POST['question_type']) && !empty($_POST['data_action'])){
                $q_type = isset($_POST['question_type']) ? wc_clean($_POST['question_type']) : '';
                $action = isset($_POST['data_action']) ? wc_clean($_POST['data_action']) : '';
                $vendor = get_mvx_product_vendors(absint($_POST['product']));
                if($action == 'rejected'){
                    $MVX->product_qna->deleteQuestion( $question_id );
                    delete_transient('mvx_customer_qna_for_vendor_' . $vendor->id);
                }else{
                    $data['status'] = $action;
                    $MVX->product_qna->updateQuestion( $question_id, $data );
                    $questions = $MVX->product_qna->get_Vendor_Questions($vendor);
                    set_transient('mvx_customer_qna_for_vendor_' . $vendor->id, $questions);
                }
            }
        }
        die;
    }

    /**
     * Ajax handler for tag add.
     *
     * @since 3.0.6
     */
    function mvx_product_tag_add() {
        check_ajax_referer('add-attribute', 'security');
        $taxonomy = apply_filters('mvx_product_tag_add_taxonomy', 'product_tag');
        $tax = get_taxonomy($taxonomy);
        $tag_name = '';
        $message = '';
        $status = false;
        if (!apply_filters('mvx_vendor_can_add_product_tag', true, get_current_user_id())) {
            $message = __("You don't have permission to add product tags", 'multivendorx');
            wp_send_json(array('status' => $status, 'tag_name' => $tag_name, 'message' => $message));
            die;
        }
        $new_tag = isset($_POST['new_tag']) ? wc_clean($_POST['new_tag']) : '';
        $tag = wp_insert_term($_POST['new_tag'], $taxonomy, array());

        if (!$tag || is_wp_error($tag) || (!$tag = get_term($tag['term_id'], $taxonomy))) {
            $message = __('An error has occurred. Please reload the page and try again.', 'multivendorx');
            if (is_wp_error($tag) && $tag->get_error_message())
                $message = $tag->get_error_message();
        }else {
            $tag_name = $tag->name;
            $status = true;
        }
        wp_send_json(array('status' => $status, 'tag' => $tag, 'tag_name' => $tag_name, 'message' => $message));
        die;
    }

    function mvx_widget_vendor_pending_shipping() {
        check_ajax_referer('mvx-pending-shipping', 'security');
        if (is_user_logged_in() && is_user_mvx_vendor(get_current_vendor_id())) {
            $vendor = get_mvx_vendor(get_current_vendor_id());
            $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
            $today = @date('Y-m-d 00:00:00', strtotime("+1 days"));
            $days_range = apply_filters('mvx_widget_vendor_pending_shipping_days_range', 7, $requestData, $vendor);
            $last_seven_day_date = date('Y-m-d H:i:s', strtotime("-$days_range days"));

            $args = apply_filters('mvx_vendor_pending_shipping_args', array(
                'start_date' => $last_seven_day_date,
                'end_date' => $today
            ));
            $pending_shippings_orders = $vendor->get_vendor_orders_reports_of('pending_shipping', $args);
            $data = array();
            if ($pending_shippings_orders) {
                foreach ($pending_shippings_orders as $pending_order) {
                    try {
                        $line_items = $pending_order->get_items('line_item');
                        $product_name = array();
                        foreach ($line_items as $item_id => $item) {
                            $product = $item->get_product();
                            if ($product && $product->needs_shipping()) {
                                $product_name[] = $item->get_name();
                            }
                        }
                        if (empty($product_name))
                            continue;

                        $action_html = '';
                        if ($vendor->is_shipping_enable()) {
                            $is_shipped = (array) get_post_meta($pending_order->get_id(), 'dc_pv_shipped', true);
                            $vendor_order_shipped = get_post_meta($pending_order->get_id(), 'mvx_vendor_order_shipped');
                            if (!in_array($vendor->id, $is_shipped) && !$vendor_order_shipped ) {
                                $action_html .= '<a href="javascript:void(0)" title="' . __('Mark as shipped', 'multivendorx') . '" onclick="mvxMarkeAsShip(this,' . $pending_order->get_id() . ')"><i class="mvx-font ico-shippingnew-icon action-icon"></i></a> ';
                            } else {
                                $action_html .= '<i title="' . __('Shipped', 'multivendorx') . '" class="mvx-font ico-shipping-icon"></i> ';
                            }
                        }
                        // shipping amount
                        $refunded = $pending_order->get_total_shipping_refunded();
                        if ( $refunded > 0 ) {
                            $shipping_amount = '<del>' . strip_tags( wc_price( $pending_order->get_shipping_total(), array( 'currency' => $pending_order->get_currency() ) ) ) . '</del> <ins>' . wc_price( $pending_order->get_shipping_total() - $refunded, array( 'currency' => $pending_order->get_currency() ) ) . '</ins>'; // WPCS: XSS ok.
                        } else {
                            $shipping_amount = wc_price( $pending_order->get_shipping_total(), array( 'currency' => $pending_order->get_currency() ) ); // WPCS: XSS ok.
                        }
                        $action_html = apply_filters('mvx_dashboard_pending_shipping_widget_data_actions', $action_html, $pending_order->get_id());
                        $row = array();
                        $row ['order_id'] = '<a href="' . esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders'), $pending_order->get_id())) . '">#' . $pending_order->get_id() . '</a>';
                        $row ['products_name'] = implode(' , ', $product_name);
                        $row ['order_date'] = mvx_date($pending_order->get_date_created());
                        $row ['shipping_address'] = $pending_order->get_formatted_shipping_address();
                        $row ['shipping_amount'] = $shipping_amount;
                        $row ['action'] = $action_html;
                        $data[] = apply_filters('mvx_widget_vendor_pending_shipping_rows', $row, $pending_order);
                    } catch (Exception $ex) {
                        
                    }
                }
            }

            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($data)), // total number of records
                "recordsFiltered" => intval(count($data)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    function mvx_widget_vendor_product_sales_report() {
        global $wpdb;
        check_ajax_referer('mvx-sales', 'security');
        if (is_user_logged_in() && is_user_mvx_vendor(get_current_vendor_id())) {

            $vendor = get_mvx_vendor(get_current_vendor_id());
            $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
            $today = @date('Y-m-d 00:00:00', strtotime("+1 days"));
            $days_range = apply_filters('mvx_widget_vendor_product_sales_report_days_range', 7, $requestData, $vendor);
            $last_seven_day_date = date('Y-m-d H:i:s', strtotime("-$days_range days"));
            
            $query = array(
                'author' => $vendor->id,
                'date_query' => array(
                    array(
                        'after'     => $last_seven_day_date,
                        'before'    => $today,
                        'inclusive' => true,
                    ),
                ),
                'meta_query'    => array(
                    'relation' => 'AND',
                    array(
                        'key'       => '_commission_id',
                        'value'   => 0,
            'compare' => '!=',
                    )
                )
            );
            $vendor_orders = apply_filters('mvx_widget_vendor_product_sales_report_orders', mvx_get_orders( $query, 'object' ), $query);
            
            $sold_product_list = array();
            if ( $vendor_orders ) :
                foreach ( $vendor_orders as $order ) {
                    $line_items = apply_filters('mvx_widget_vendor_product_sales_report_line_items', $order->get_items('line_item') );
                    foreach ( $line_items as $item_id => $item ) {
                        if (array_key_exists($item->get_product_id(), $sold_product_list)) {
                            $sold_product_list[$item->get_product_id()]['qty'] += $item->get_quantity();
                            $sold_product_list[$item->get_product_id()]['item_total'] += $item->get_total();
                        } else {
                            $sold_product_list[$item->get_product_id()]['qty'] = $item->get_quantity();
                            $sold_product_list[$item->get_product_id()]['item'] = $item;
                            $sold_product_list[$item->get_product_id()]['item_total'] = $item->get_total();
                            $sold_product_list[$item->get_product_id()]['order_id'] = $order->get_id();
                        }
                    }
                }
            endif;
            arsort($sold_product_list);
            $data = array();
            foreach ($sold_product_list as $product_id => $sold_item_data) {
                $item = $sold_item_data['item'];
                $product = $item->get_product();
                $row = array();
                if ($product) {
                    $row ['product'] = '<a href="' . mvx_get_product_link( $product_id ) . '">' . $product->get_image(array(40, 40)) . ' ' . wp_trim_words($item->get_name(), 60, '...') . '</a>';
                    $row ['revenue'] = wc_price( $sold_item_data['item_total'] );
                    $row ['unique_purchase'] = $sold_item_data['qty'];
                } else {
                    $row ['product'] = __('This product does not exists', 'multivendorx');
                    $row ['revenue'] = '-';
                    $row ['unique_purchase'] = $sold_item_data['qty'];
                }
                $data[] = apply_filters( 'mvx_widget_vendor_product_sales_report_rows', $row, $product_id, $sold_item_data );
            }

            $json_data = array(
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($sold_product_list)), // total number of records
                "recordsFiltered" => intval(count($sold_product_list)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    public function mvx_get_shipping_methods_by_zone() {
        global $MVX;

        $zones = array();
        
        if (isset($_POST['zoneID'])) {
            if( !class_exists( 'MVX_Shipping_Zone' ) ) {
                $MVX->load_vendor_shipping();
            }
            $zones = MVX_Shipping_Zone::get_zone(wc_clean($_POST['zoneID']));
        }

        $show_post_code_list = $show_state_list = $show_post_code_list = false;

        $zone_id = $zones['data']['id'];
        $zone_locations = $zones['data']['zone_locations'];

        $zone_location_types = array_column(array_map('mvx_convert_normal_string_to_array', $zone_locations), 'type', 'code');

        $selected_continent_codes = array_keys($zone_location_types, 'continent');

        if (!$selected_continent_codes) {
            $selected_continent_codes = array();
        }

        $selected_country_codes = array_keys($zone_location_types, 'country');
        $all_states = WC()->countries->get_states();

        $state_key_by_country = array();
        $state_key_by_country = array_intersect_key($all_states, array_flip($selected_country_codes));

        array_walk($state_key_by_country, 'mvx_state_key_alter');

        if ($selected_country_codes && is_array($selected_country_codes) && !empty($selected_country_codes) && isset($selected_country_codes[0])) {
            $state_key_by_country = $state_key_by_country[$selected_country_codes[0]];
        }

        $show_limit_location_link = apply_filters('show_limit_location_link', (!in_array('postcode', $zone_location_types)));
        $vendor_shipping_methods = $zones['shipping_methods'];

        if ($show_limit_location_link) {
            if (in_array('state', $zone_location_types)) {
                $show_post_code_list = true;
            } elseif (in_array('country', $zone_location_types)) {
                $show_state_list = true;
                $show_post_code_list = true;
            }
        }

        $want_to_limit_location = !empty($zones['locations']);
        $countries = $states = $cities = $postcodes = array();
        
        if ($want_to_limit_location) {
            foreach ($zones['locations'] as $each_location) {
                switch ($each_location['type']) {
                    case 'state':
                        $states[] = $each_location['code'];
                        break;
                    case 'postcode':
                        $postcodes[] = $each_location['code'];
                        break;
                    default:
                        break;
                }
            }
            $postcodes = implode(',', $postcodes);
        }

        ob_start();
        $template_data = array(
            'zones' => $zones,
            'zone_id' => $zone_id,
            'want_to_limit_location' => $want_to_limit_location,
            'show_limit_location_link' => $show_limit_location_link,
            'show_state_list' => $show_state_list,
            'countries' => $countries,
            'states' => $states,
            'state_key_by_country' => $state_key_by_country,
            'show_post_code_list' => $show_post_code_list,
            'postcodes' => $postcodes ? $postcodes : '',
            'vendor_shipping_methods' => $vendor_shipping_methods,
        );
        $MVX->template->get_template('vendor-dashboard/vendor-shipping/vendor-shipping-zone-settings.php', $template_data);
        $zone_html['html'] = ob_get_clean();
        $zone_html['states'] = json_encode($states);

        wp_send_json_success($zone_html);
    }

    public function admin_get_vendor_shipping_methods_by_zone(){
        ?>
        <div class="wrap">
        <div id="icon-woocommerce" class="icon32 icon32-woocommerce-reports"><br/></div>
        <h2><?php _e('Shipping', 'multivendorx'); ?></h2> <?php
        $vendor_id = isset($_POST['vendor_id']) ? absint($_POST['vendor_id']) : 0;
        if( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        $zone_ids = isset($_POST['zoneID']) ? absint($_POST['zoneID']) : 0;
        $zones = MVX_Shipping_Zone::get_zone($zone_ids);
        if ($zones)
        $zone = WC_Shipping_Zones::get_zone(absint($zone_ids));

        $show_post_code_list = $show_state_list = false;
        $zone_id = $zones['data']['id'];
        $zone_locations = $zones['data']['zone_locations'];

        $zone_location_types = array_column(array_map('mvx_convert_normal_string_to_array', $zone_locations), 'type', 'code');

        $selected_continent_codes = array_keys($zone_location_types, 'continent');

        if (!$selected_continent_codes) {
            $selected_continent_codes = array();
        }

        $selected_country_codes = array_keys($zone_location_types, 'country');
        $all_states = WC()->countries->get_states();

        $state_key_by_country = array();
        $state_key_by_country = array_intersect_key($all_states, array_flip($selected_country_codes));

        array_walk($state_key_by_country, 'mvx_state_key_alter');

        if ($selected_country_codes && is_array($selected_country_codes) && !empty($selected_country_codes) && isset($selected_country_codes[0])) {
            $state_key_by_country = $state_key_by_country[$selected_country_codes[0]];
        }

        $show_limit_location_link = apply_filters('show_limit_location_link', (!in_array('postcode', $zone_location_types)));
        $vendor_shipping_methods = $zones['shipping_methods'];
        if ($show_limit_location_link) {
            if (in_array('state', $zone_location_types)) {
                $show_post_code_list = true;
            } elseif (in_array('country', $zone_location_types)) {
                $show_state_list = true;
                $show_post_code_list = true;
            }
        }

        $want_to_limit_location = !empty($zones['locations']);
        $countries = $states = $cities = array();
        $postcodes = '';
        if ($want_to_limit_location) {
            $postcodes = array();
            foreach ($zones['locations'] as $each_location) {
                switch ($each_location['type']) {
                    case 'state':
                        $states[] = $each_location['code'];
                        break;
                    case 'postcode':
                        $postcodes[] = $each_location['code'];
                        break;
                    default:
                        break;
                }
            }
            
            $postcodes = implode(',', $postcodes);
        }
        
        ?>
        <input id="zone_id" class="form-control" type="hidden" name="<?php echo 'mvx_shipping_zone[' . $zone_id . '][_zone_id]'; ?>" value="<?php echo $zone_id; ?>">
        <table class="form-table mvx-shipping-zone-settings wc-shipping-zone-settings">
            <tbody>
                <tr valign="top" class="">
                    <th scope="row" class="titledesc">
                        <label for="">
                            <?php _e('Zone Name', 'multivendorx'); ?>
                        </label>
                    </th>
                    <td class="forminp"><?php _e($zones['data']['zone_name'], 'multivendorx'); ?></td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" class="titledesc">
                        <label for="">
                            <?php _e('Zone region', 'multivendorx'); ?>
                        </label>
                    </th>
                    <td class="forminp"><?php _e($zones['formatted_zone_location'], 'multivendorx'); ?></td>
                </tr>
                <?php if ($show_limit_location_link && $zone_id !== 0) { ?>
                    <tr valign="top" class="">
                        <th scope="row" class="titledesc">
                            <label for="">
                                <?php _e('Limit Zone Location', 'multivendorx'); ?>
                            </label>
                        </th>
                        <td class="forminp"><input id="limit_zone_location" class="" type="checkbox" name="<?php echo 'mvx_shipping_zone[' . $zone_id . '][_limit_zone_location]'; ?>" value="1" <?php checked($want_to_limit_location, 1); ?>></td>
                    </tr>
                <?php } ?>
                <?php if ($show_state_list) { ?>
                    <tr valign="top" class="hide_if_zone_not_limited">
                        <th scope="row" class="titledesc">
                            <label for="">
                                <?php _e('Select specific states', 'multivendorx'); ?>
                            </label>
                        </th>
                        <td class="forminp">
                            <select id="select_zone_states" class="form-control" name="<?php echo 'mvx_shipping_zone[' . $zone_id . '][_select_zone_states][]'; ?>" multiple>
                                <?php foreach ($state_key_by_country as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>" <?php selected(in_array($key, $states), true); ?>><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
                <?php if ($show_post_code_list) { ?>
                    <tr valign="top" class="hide_if_zone_not_limited">
                        <th scope="row" class="titledesc">
                            <label for="">
                                <?php _e('Set your postcode', 'multivendorx'); ?>
                            </label>
                        </th>
                        <td class="forminp">
                            <input id="select_zone_postcodes" class="form-control" type="text" name="<?php echo 'mvx_shipping_zone[' . $zone_id . '][_select_zone_postcodes]'; ?>" value="<?php echo $postcodes; ?>" placeholder="<?php _e('Postcodes need to be comma separated', 'multivendorx'); ?>">
                        </td>
                    </tr>
                <?php } ?>
                <tr valign="top" class="">
                    <th scope="row" class="titledesc">
                        <label>
                            <?php _e('Shipping methods', 'multivendorx'); ?>
                            <?php echo wc_help_tip(__('Add your shipping method for appropiate zone', 'multivendorx')); // @codingStandardsIgnoreLine  ?>
                        </label>
                    </th>
                    <td class="">
                        <table class="mvx-shipping-zone-methods wc-shipping-zone-methods widefat">
                            <thead>
                                <tr>   
                                    <th class="mvx-title wc-shipping-zone-method-title"><?php _e('Title', 'multivendorx'); ?></th>
                                    <th class="mvx-enabled wc-shipping-zone-method-enabled"><?php _e('Enabled', 'multivendorx'); ?></th> 
                                    <th class="mvx-description wc-shipping-zone-method-description"><?php _e('Description', 'multivendorx'); ?></th>
                                    <th class="mvx-action"><?php _e('Action', 'multivendorx'); ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <button type="submit" class="button mvx-shipping-zone-show-method wc-shipping-zone-add-method" value="<?php esc_attr_e('Add shipping method', 'multivendorx'); ?>"><?php esc_html_e('Add shipping method', 'multivendorx'); ?></button>
                                    </td>
                                </tr>
                            </tfoot>
                            <tbody>
                                <?php if (empty($vendor_shipping_methods)) { ?> 
                                    <tr>
                                        <td colspan="4"><?php _e('You can add multiple shipping methods within this zone. Only customers within the zone will see them.', 'multivendorx'); ?></td>
                                    </tr>
                                    <?php
                                } else { 
                                    foreach ($vendor_shipping_methods as $vendor_shipping_method) {
                                        ?>
                                        <tr class="mvx-shipping-zone-method">
                                            <td><?php esc_html_e($vendor_shipping_method['title'], 'multivendorx'); ?>
                                                <div data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-method-settings='<?php echo json_encode($vendor_shipping_method); ?>' class="row-actions edit_del_actions">
                                                </div>
                                            </td>
                                            <td class="mvx-shipping-zone-method-enabled wc-shipping-zone-method-enabled"> 
                                                <span class="">
                                                    <input id="method_status <?php echo $vendor_shipping_method['instance_id']; ?>" data-vendor_id="<?php echo $vendor_id; ?>" class="input-checkbox method-status" type="checkbox" name="method_status" value="<?php echo $vendor_shipping_method['instance_id']; ?>" <?php checked(( $vendor_shipping_method['enabled'] == "yes"), true); ?>>
                                                </span>
                                            </td>
                                            <td><?php _e($vendor_shipping_method['settings']['description'], 'multivendorx'); ?></td>
                                            <td>
                                                <div class="col-actions edit_del_actions" data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-method-settings='<?php echo json_encode($vendor_shipping_method); ?>'>
                                                    <span class="edit"><a href="javascript:void(0);" data-vendor_id="<?php echo $vendor_id; ?>" class="edit-shipping-method" data-zone_id="<?php echo $zone_id; ?>" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" title="<?php _e('Edit', 'multivendorx') ?>"><?php _e('Edit', 'multivendorx') ?></a>
                                                    </span>|
                                                    <span class="delete"><a class="delete-shipping-method" data-vendor_id="<?php echo $vendor_id; ?>" href="javascript:void(0);" data-method_id="<?php echo $vendor_shipping_method['id']; ?>" data-instance_id="<?php echo $vendor_shipping_method['instance_id']; ?>" title="<?php _e('Delete', 'multivendorx') ?>"><?php _e('Delete', 'multivendorx') ?></a></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
            
            <script type="text/template" id="tmpl-mvx-modal-add-shipping-method">
                <div class="wc-backbone-modal mvx-modal-add-shipping-method-modal">
                <div class="wc-backbone-modal-content">
                <section class="wc-backbone-modal-main" role="main">
                <header class="wc-backbone-modal-header">
                <h1><?php esc_html_e('Add shipping method', 'multivendorx'); ?></h1>
                <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                <span class="screen-reader-text"><?php esc_html_e('Close modal panel', 'multivendorx'); ?></span>
                </button>
                </header>
                <article>
                <form action="" method="post">
                <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>"/>
                <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>"/>
                
                <div class="wc-shipping-zone-method-selector">
                <p><?php esc_html_e('Choose the shipping method you wish to add. Only shipping methods which support zones are listed.', 'multivendorx'); ?></p>
                <?php $shipping_methods = mvx_get_shipping_methods(); ?>
                <select id="shipping_method" class="form-control mt-15" name="mvx_shipping_method">
                <?php foreach ($shipping_methods as $key => $method) { ?>
                    <option data-description="<?php echo esc_attr( wp_kses_post( wpautop( $method->get_method_description() ) ) ); ?>" value="<?php echo esc_attr( $method->id ); ?>"><?php echo esc_attr( $method->get_method_title() ); ?></option>
                <?php } ?>
                </select>
                <div class="wc-shipping-zone-method-description"></div>
                </div>
                </form>
                </article>
                <footer>
                <div class="inner">
                <button id="btn-ok" data-vendor_id="<?php echo $vendor_id; ?>" class="button button-primary button-large mvx-shipping-zone-add-method" data-zone_id="<?php echo $zone_id; ?>"><?php esc_html_e('Add shipping method', 'multivendorx'); ?></button>
                </div>
                </footer>
                </section>
                </div>
                </div>
                <div class="wc-backbone-modal-backdrop modal-close"></div>
            </script>
            <script type="text/template" id="tmpl-mvx-modal-update-shipping-method">
                <?php
                global $MVX;

                $is_method_taxable_array = array(
                    'none' => __('None', 'multivendorx'),
                    'taxable' => __('Taxable', 'multivendorx')
                );

                $calculation_type = array(
                    'class' => __('Per class: Charge shipping for each shipping class individually', 'multivendorx'),
                    'order' => __('Per order: Charge shipping for the most expensive shipping class', 'multivendorx'),
                );
                ?>
                <div class="wc-backbone-modal mvx-modal-add-shipping-method-modal">
                <div class="wc-backbone-modal-content">
                <section class="wc-backbone-modal-main" role="main">
                <header class="wc-backbone-modal-header">
                <h1><?php _e( 'Edit Shipping Methods', 'multivendorx' ); ?></h1>
                <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                <span class="screen-reader-text"><?php esc_html_e('Close modal panel', 'multivendorx'); ?></span>
                </button>
                </header>
                <article class="mvx-shipping-methods">
                <form action="" method="post">
                <input id="instance_id_selected_zone" class="form-control" type="hidden" name="zone_id" value="<?php echo $zone_id; ?>"> 
                <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>"/>
                <input id="method_id_selected" class="form-control" type="hidden" name="method_id" value="{{{ data.methodId }}}"> 
                <input id="instance_id_selected" class="form-control" type="hidden" name="instance_id" value="{{{ data.instanceId }}}"> 
                {{{ data.config_settings }}}
     
                </form>
                </article>
                <footer>
                <div class="inner">
                <button id="btn-ok" class="button button-primary button-large mvx-shipping-zone-add-method" data-vendor_id="<?php echo $vendor_id; ?>" data-zone_id="<?php echo $zone_id; ?>"><?php esc_html_e('Save changes', 'multivendorx'); ?></button>
                </div>
                </footer>
                </section>
                </div>
                </div>
                <div class="wc-backbone-modal-backdrop modal-close"></div>
            </script>
        </table>
        <br class="clear"/>
        </div>
        <?php
        die;
    }

    public function mvx_add_shipping_method() {
        global $MVX;
        check_ajax_referer('mvx-shipping', 'security');
        $data = array(
            'zone_id' => wc_clean($_POST['zoneID']),
            'method_id' => wc_clean($_POST['method'])
        );
        if( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        $result = MVX_Shipping_Zone::add_shipping_methods($data);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'mvx');
        }

        wp_send_json_success(__('Shipping method added successfully', 'multivendorx'));
    }

    public function mvx_update_shipping_method() {
        global $MVX;
        check_ajax_referer('mvx-shipping', 'security');
        $args = isset($_POST['args']) ? wc_clean($_POST['args']) : '';
        $posted_data = isset($_POST['posted_data']) ? array_filter(wc_clean($_POST['posted_data'])) : array();
        $form_fields = array();
        if(isset($args['settings'])){
            foreach ($args['settings'] as $field) {
                $form_fields[$field['name']] = $field['value'];
            }
        }
        $args['settings'] = apply_filters('mvx_before_update_shipping_method_settings', array_merge($form_fields + $posted_data), $_POST);

        if (empty($args['settings']['title'])) {
            wp_send_json_error(__('Shipping title must be required', 'multivendorx'));
        }
        
        if( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        do_action( 'mvx_before_update_shipping_method', $args );
        $result = MVX_Shipping_Zone::update_shipping_method($args);

        $MVX->load_class('shipping-gateway');
        MVX_Shipping_Gateway::load_class('shipping-method');
        $vendor_shipping = new MVX_Vendor_Shipping_Method();
        $vendor_shipping->set_post_data($args['settings']);
        $vendor_shipping->process_admin_options();

        // clear shipping transient
        WC_Cache_Helper::get_transient_version('shipping', true);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'mvx');
        }

        wp_send_json_success(__('Shipping method updated', 'multivendorx'));
    }

    public function mvx_delete_shipping_method() {
        global $MVX;
        check_ajax_referer('mvx-shipping', 'security');
        $data = array(
            'zone_id' => wc_clean($_POST['zoneID']),
            'instance_id' => wc_clean($_POST['instance_id'])
        );
        
        if( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        
        $result = MVX_Shipping_Zone::delete_shipping_methods($data);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'mvx');
        }

        wp_send_json_success(__('Shipping method deleted', 'multivendorx'));
    }

    public function mvx_toggle_shipping_method() {
        global $MVX;
        check_ajax_referer('mvx-shipping', 'security');
        $data = array(
            'instance_id' => wc_clean($_POST['instance_id']),
            'zone_id' => absint($_POST['zoneID']),
            'checked' => ( $_POST['checked'] == 'true' ) ? 1 : 0
        );
        if( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        $result = MVX_Shipping_Zone::toggle_shipping_method($data);
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        $message = $data['checked'] ? __('Shipping method enabled successfully', 'multivendorx') : __('Shipping method disabled successfully', 'multivendorx');
        wp_send_json_success($message);
    }
    
    public function mvx_configure_shipping_method(){
        check_ajax_referer('mvx-shipping', 'security');
        global $MVX;
        $zone_id = isset($_POST['zoneId']) ? absint($_POST['zoneId']) : 0;
        $method_id = isset($_POST['methodId']) ? wc_clean($_POST['methodId']) : '';
        $instance_id = isset($_POST['instanceId']) ? wc_clean($_POST['instanceId']) : '';
        $vendor_id = isset($_POST['vendor_id']) ? absint($_POST['vendor_id']) : get_current_user_id();
        
            if( !class_exists( 'MVX_Shipping_Zone' ) ) {
                $MVX->load_vendor_shipping();
            }
            $zones = MVX_Shipping_Zone::get_zone($zone_id);
            $vendor_shipping_methods = $zones['shipping_methods'];
            $config_settings = array();
            $is_method_taxable_array = array(
                'none' => __('None', 'multivendorx'),
                'taxable' => __('Taxable', 'multivendorx')
            );
            $vendor_shipping_method = $vendor_shipping_methods[$method_id.':'.$instance_id];
            $calculation_type = array(
                'class' => __('Per class: Charge shipping for each shipping class individually', 'multivendorx'),
                'order' => __('Per order: Charge shipping for the most expensive shipping class', 'multivendorx'),
            );
            $settings_html = '';
                if ($vendor_shipping_method['id'] == 'free_shipping') {
                    $settings_html = '<!-- Free shipping -->'
                            . '<div class="shipping_form" id="' . $vendor_shipping_method['id'] . '">'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Method Title', 'multivendorx') . '</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_title_fs" class="form-control" type="text" name="title" value="'.$vendor_shipping_method['title'].'" placeholder="'.__( 'Enter method title', 'multivendorx' ).'">'
                            . '</div></div>'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Minimum order amount for free shipping', 'multivendorx') . '</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="minimum_order_amount_fs" class="form-control" type="text" name="min_amount" value="'.$vendor_shipping_method['settings']['min_amount'].'" placeholder="'.__( '0.00', 'multivendorx' ).'">'
                            . '</div></div>'
                            . '<input type="hidden" id="method_description_fs" name="description" value="'.$vendor_shipping_method['settings']['description'].'" />'
                            . '<input type="hidden" id="method_cost_fs" name="cost" value="0" />'
                            . '<input type="hidden" id="method_tax_status_fs" name="tax_status" value="none" />'
                            . '<!--div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Description', 'multivendorx') . '</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<textarea id="method_description_fs" class="form-control" name="method_description">' . $vendor_shipping_method['settings']['description'] . '</textarea>'
                            . '</div></div--></div>';
                } elseif ($vendor_shipping_method['id'] == 'local_pickup') {
                    $settings_html = '<!-- Local Pickup -->'
                            . '<div class="shipping_form " id="' . $vendor_shipping_method['id'] . '">'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Method Title', 'multivendorx') . '</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_title_fs" class="form-control" type="text" name="title" value="'.$vendor_shipping_method['title'].'" placeholder="'.__( 'Enter method title', 'multivendorx' ).'">'
                            . '</div></div>'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Cost', 'multivendorx') . '</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_cost_lp" class="form-control" type="text" name="cost" value="'.$vendor_shipping_method['settings']['cost'].'" placeholder="'.__( '0.00', 'multivendorx' ).'">'
                            . '</div></div>';
                            if( apply_filters( 'mvx_show_shipping_zone_tax', true ) ) {
                            $settings_html .= '<div class="form-group">'
                                    . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Tax Status', 'multivendorx' ).'</label>'
                                    . '<div class="col-md-9 col-sm-9">'
                                    . '<select id="method_tax_status_lp" class="form-control" name="tax_status">';
                                foreach( $is_method_taxable_array as $key => $value ) { 
                                    $settings_html .= '<option value="'.$key.'">'.$value.'</option>';
                                 } 
                            $settings_html .= '</select></div></div>';
                            }
                    $settings_html .= '<input type="hidden" id="method_description_lp" name="description" value="'.$vendor_shipping_method['settings']['description'].'" />'
                            . '<!--div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Description', 'multivendorx') . '</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<textarea id="method_description_lp" class="form-control" name="method_description">' . $vendor_shipping_method['settings']['description'] . '</textarea>'
                            . '</div></div--></div>';
                } elseif ($vendor_shipping_method['id'] == 'flat_rate') {
                    $settings_html = '<!-- Flat rate -->'
                            . '<div class="shipping_form" id="' . $vendor_shipping_method['id'] . '">'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Method Title', 'multivendorx') . '</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_title_fs" class="form-control" type="text" name="title" value="'.$vendor_shipping_method['title'].'" placeholder="'.__( 'Enter method title', 'multivendorx' ).'">'
                            . '</div></div>'
                            . '<div class="form-group">'
                            . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Cost', 'multivendorx') . '</label>'
                            . '<div class="col-md-9 col-sm-9">'
                            . '<input id="method_cost_fr" class="form-control" type="text" name="cost" value="'.$vendor_shipping_method['settings']['cost'].'" placeholder="'.__( '0.00', 'multivendorx' ).'">'
                            . '</div></div>';
                            if( apply_filters( 'mvx_show_shipping_zone_tax', true ) ) { 
                            $settings_html .= '<div class="form-group">'
                                    . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Tax Status', 'multivendorx' ).'</label>'
                                    . '<div class="col-md-9 col-sm-9">'
                                    . '<select id="method_tax_status_fr" class="form-control" name="tax_status">';
                                foreach( $is_method_taxable_array as $key => $value ) { 
                                    $settings_html .= '<option value="'.$key.'">'.$value.'</option>';
                                } 
                            $settings_html .= '</select></div></div>';
                            }
                            $settings_html .= '<input type="hidden" id="method_description_fr" name="description" value="'.$vendor_shipping_method['settings']['description'].'" />'
                                    . '<!--div class="form-group">'
                                    . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Description', 'multivendorx' ).'</label>'
                                    . '<div class="col-md-9 col-sm-9">'
                                    . '<textarea id="method_description_fr" class="form-control" name="method_description">'.$vendor_shipping_method['settings']['description'].'</textarea>'
                                    . '</div></div-->';
                            if (!apply_filters( 'mvx_hide_vendor_shipping_classes', false )) { 
                            $settings_html .= '<div class="mvx_shipping_classes"><hr>'
                                    . '<h2>'.__('Shipping Class Cost', 'multivendorx').'</h2>'
                                    . '<div class="description mb-15">'.__('These costs can be optionally entered based on the shipping class set per product (This cost will be added with the shipping cost above).', 'multivendorx').'</div>';
      
                            $shipping_classes = get_vendor_shipping_classes();

                            if(empty($shipping_classes)) {
                            $settings_html .= '<div class="no_shipping_classes">' . __("No Shipping Classes set by Admin", 'multivendorx') . '</div>';
                            } else {
                                foreach ($shipping_classes as $shipping_class ) {
                                    $settings_html .= '<div class="form-group">'
                                            . '<label for="" class="control-label col-sm-3 col-md-3">'.__( 'Cost of Shipping Class:', 'multivendorx' ) .' '. $shipping_class->name .'</label>'
                                            . '<div class="col-md-9 col-sm-9">'
                                            . '<input type="hidden" name="shipping_class_id" value="'.$shipping_class->term_id.'" />'
                                            . '<input id="'.$shipping_class->slug.'" class="form-control sc_vals" type="text" name="class_cost_'.$shipping_class->term_id.'" value="'.$vendor_shipping_method['settings']['class_cost_'.$shipping_class->term_id].'" placeholder="'.__( 'N/A', 'multivendorx' ).'" data-shipping_class_id="'. $shipping_class->term_id.'">'
                                            . '<div class="description">'.__( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'multivendorx' ) . '<br/><br/>' . __( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'multivendorx' ).'</div>'
                                            . '</div></div>';
                                }
                            $settings_html .= '<div class="form-group">'
                                    . '<label for="" class="control-label col-sm-3 col-md-3">' . __('Calculation type', 'multivendorx') . '</label>'
                                    . '<div class="col-md-9 col-sm-9">'
                                    . '<select id="calculation_type" class="form-control" name="calculation_type">';
                            foreach ($calculation_type as $key => $value) {
                                $settings_html .= '<option value="' . $key . '">' . $value . '</option>';
                            }
                            $settings_html .= '</select></div></div>';
                        }
                        $settings_html .= '</div>';
                    }
                    $settings_html .= '</div>';
                } else {
                    $settings_html = apply_filters('mvx_vendor_backend_shipping_methods_edit_form_fields', $settings_html, $vendor_id, $zone_id, $vendor_shipping_method);
                }
                $config_settings[$vendor_shipping_method['id']] = $settings_html;
            $html_settings = isset($config_settings[$method_id]) ? $config_settings[$method_id] : '';
            wp_send_json($html_settings);

    }
    
    public function mvx_vendor_configure_shipping_method(){
        global $MVX;
        check_ajax_referer('mvx-shipping', 'security');
        $zone_id = isset($_POST['zoneId']) ? absint($_POST['zoneId']) : 0;
        $method_id = isset($_POST['methodId']) ? wc_clean($_POST['methodId']) : '';
        $instance_id = isset($_POST['instanceId']) ? wc_clean($_POST['instanceId']) : '';
            if( !class_exists( 'MVX_Shipping_Zone' ) ) {
                $MVX->load_vendor_shipping();
            }
            $zones = MVX_Shipping_Zone::get_zone($zone_id);
            $vendor_shipping_methods = $zones['shipping_methods'];
            $config_settings = array();
            $is_method_taxable_array = array(
                'none' => __('None', 'multivendorx'),
                'taxable' => __('Taxable', 'multivendorx')
            );

            $calculation_type = array(
                'class' => __('Per class: Charge shipping for each shipping class individually', 'multivendorx'),
                'order' => __('Per order: Charge shipping for the most expensive shipping class', 'multivendorx'),
            );

            $settings_html = '';
            if(isset($vendor_shipping_methods[$method_id.':'.$instance_id])){
                $shipping_method = $vendor_shipping_methods[$method_id.':'.$instance_id];
                ob_start();
                do_action( 'mvx_vendor_shipping_'.$method_id.'_configure_form_fields', $shipping_method, $_POST );
                $settings_html = ob_get_clean();
            }
            wp_send_json(array('settings_html' => $settings_html));
            die;
    }
    
    
    public function mvx_product_classify_next_level_list_categories() {
        $term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
        $taxonomy = isset($_POST['taxonomy']) ? wc_clean($_POST['taxonomy']) : '';
        $cat_level = isset($_POST['cat_level']) ? wc_clean($_POST['cat_level']) : 0;
        $term = get_term($term_id, $taxonomy);
        $child_terms = get_term_children($term_id, $taxonomy);
        $html_level = '';
        $level = $cat_level + 1;
        $final = false;
        $hierarchy = get_ancestors($term_id, $taxonomy);
        $crumb = array();
        foreach (array_reverse($hierarchy) as $id) {
            $h_term = get_term($id, $taxonomy);
            $crumb[] = $h_term->name;
        }
        $crumb[] = $term->name;
        $html_hierarchy = implode(' <i class="mvx-font ico-right-arrow-icon"></i> ', $crumb);
        if ($child_terms) {
            $html_level .= '<ul class="mvx-product-categories ' . $level . '-level" data-cat-level="' . $level . '">';
            $html_level .= mvx_list_categories(apply_filters("mvx_vendor_product_classify_{$level}_level_categories", array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
                'html_list' => true,
                'parent' => $term_id,
                'cat_link' => '#',
                    )));
            $html_level .= '</ul>';
        } else {
            $final = true;
            //$level = 'final';
            $html_level .= '<div class="final-cat-button">'
                    . '<p>' . $term->name . '<p>'
                    . '<button class="classified-pro-cat-btn btn btn-default" data-term-id="' . $term->term_id . '" data-taxonomy="' . $taxonomy . '">' . strtoupper(__('Select', 'multivendorx')) . '</button>'
                    . '</div>';
        }
        wp_send_json(array('html_level' => $html_level, 'level' => $level, 'is_final' => $final, 'hierarchy' => $html_hierarchy));
        die;
    }

    public function show_product_classify_next_level_from_searched_term() {
        $term_id = isset($_POST['term_id']) ? absint($_POST['term_id']) : 0;
        $taxonomy = isset($_POST['taxonomy']) ? wc_clean($_POST['taxonomy']) : '';
        $hierarchy = get_ancestors($term_id, $taxonomy);
        $html_level = $html_hierarchy = '';
        //print_r($hierarchy);die;
        $level = 1;
        $parent = 0;
        if ($hierarchy) {
            foreach (array_reverse($hierarchy) as $id) {
                $html_level .= '<div class="mvx-product-cat-level ' . $level . '-level-cat cat-column" data-level="' . $level . '">'
                        . '<ul class="mvx-product-categories ' . $level . '-level" data-cat-level="' . $level . '">';
                $html_level .= mvx_list_categories(apply_filters('mvx_vendor_product_classify_' . $level . '_level_categories', array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                    'html_list' => true,
                    'parent' => $parent,
                    'cat_link' => '#',
                    'selected' => $id,
                        )));
                $html_level .= '</ul></div>';
                $level++;
                $parent = $id;
            }
        }
        $html_level .= '<div class="mvx-product-cat-level ' . $level . '-level-cat cat-column" data-level="' . $level . '">'
                . '<ul class="mvx-product-categories ' . $level . '-level" data-cat-level="' . $level . '">';
        $html_level .= mvx_list_categories(apply_filters('mvx_vendor_product_classify_first_level_categories', array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'html_list' => true,
            'parent' => $parent,
            'cat_link' => '#',
            'selected' => $term_id,
                )));
        $html_level .= '</ul></div>';
        // add final level step
        $level = $level + 1;
        $h_term = get_term($term_id, $taxonomy);
        $html_level .= '<div class="mvx-product-cat-level ' . $level . '-level-cat cat-column select-cat-button-holder" data-level="' . $level . '">'
                . '<div class="final-cat-button">'
                . '<p>' . $h_term->name . '<p>'
                . '<button class="classified-pro-cat-btn btn btn-default" data-term-id="' . $h_term->term_id . '" data-taxonomy="' . $taxonomy . '">' . strtoupper(__('Select', 'multivendorx')) . '</button>'
                . '</div></div>';

        wp_send_json(array('html_level' => $html_level));
        die;
    }

    public function mvx_product_classify_search_category_level() {
        global $MVX, $wpdb;
        $keyword = isset($_POST['keyword']) ? wc_clean( wp_unslash( $_POST['keyword'] ) ) : '';
        if (!empty($keyword)) {
            $query = apply_filters("mvx_product_classify_search_category_level_args", array(
                'taxonomy' => 'product_cat',
                'search' => $keyword,
                'hide_empty' => false,
                'parent' => '',
                'fields' => 'ids',
                    ));
            $search_terms = mvx_list_categories($query);
            $html_search_result = '';
            if ($search_terms) {
                foreach ($search_terms as $term_id) {
                    $term = get_term($term_id, $query['taxonomy']);
                    $hierarchy = get_ancestors($term_id, $query['taxonomy']);
                    $hierarchy = array_reverse($hierarchy);
                    $hierarchy[] = $term_id;
                    $html_search_result .= '<li class="list-group-item" data-term-id="' . $term->term_id . '" data-taxonomy="' . $query['taxonomy'] . '">'
                            . '<p><strong>' . $term->name . '</strong></p>'
                            . '<ul class="breadcrumb">';
                    foreach ($hierarchy as $id) {
                        $h_term = get_term($id, $query['taxonomy']);
                        $html_search_result .= '<li>' . $h_term->name . '</li>';
                    }
                    $html_search_result .= '</ul></li>';
                }
            } else {
                $html_search_result .= '<li class="list-group-item"><p>' . __('No results found', 'multivendorx') . '</p></li>';
            }
            wp_send_json(array('results' => $html_search_result));
            die;
        }
    }

    public function mvx_list_a_product_by_name_or_gtin() {
        global $MVX, $wpdb;
        $keyword = isset($_POST['keyword']) ? wc_clean( wp_unslash( $_POST['keyword'] ) ) : '';
        $html = '';
        if (!empty($keyword)) {
            $ids = array();
            $posts = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_mvx_gtin_code' AND meta_value LIKE %s;", esc_sql('%' . $keyword . '%')));
            if (!$posts) {
                $data_store = WC_Data_Store::load('product');
                $ids = $data_store->search_products($keyword, '', false);
                $include = array();
                foreach ($ids as $id) {
                    $product = wc_get_product($id);
                    $product_map_id = get_post_meta($id, '_mvx_spmv_map_id', true);
                    if ($product && $product_map_id) {
                        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mvx_products_map WHERE product_map_id=%d", $product_map_id));
                        $product_ids = wp_list_pluck($results, 'product_id');
                        if($product_ids){
                            $include[] = min($product_ids);
                        }
                    } elseif ($product) {
                        $include[] = $id;
                    }
                }

                if ($include) {
                    $ids = array_slice(array_intersect($ids, $include), 0, apply_filters('mvx_spmv_list_product_search_number', 10));
                } else {
                    $ids = array();
                }
            } else {
                $unique_gtin_arr = array();
                foreach ($posts as $post_id) {
                    $unique_gtin_arr[$post_id] = get_post_meta($post_id, '_mvx_gtin_code', true);
                }
                $ids = array_keys(array_unique($unique_gtin_arr));
            }

            $product_objects = apply_filters('mvx_list_a_products_objects', array_map('wc_get_product', $ids));
            $user_id = get_current_user_id();

            if (count($product_objects) > 0) {
                foreach ($product_objects as $product_object) {
                    if ($product_object) {
                        $gtin_code = get_post_meta($product_object->get_id(), '_mvx_gtin_code', true);
                        if (is_user_mvx_vendor($user_id) && mvx_is_product_type_avaliable($product_object->get_type())) {
                            // product cat
                            $product_cats = '';
                            $termlist = array();
                            //$terms = wp_get_post_terms( $product_object->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
                            $terms = get_the_terms($product_object->get_id(), 'product_cat');
                            if (!$terms) {
                                $product_cats = '<span class="na">&ndash;</span>';
                            } else {
                                $terms_arr = array();
                                $terms = apply_filters('mvx_vendor_product_list_row_product_categories', $terms, $product_object);
                                foreach ($terms as $term) {
                                    //$h_term = get_term_by('term_id', $term_id, 'product_cat');
                                    $terms_arr[] = $term->name;
                                }
                                $product_cats = implode(' | ', $terms_arr);
                            }

                            $html .= '<div class="search-result-clm">'
                                    . $product_object->get_image(apply_filters('mvx_searched_name_gtin_product_list_image_size', array(98, 98)))
                                    . '<div class="result-content">'
                                    . '<p><strong><a href="' . esc_url( $product_object->get_permalink() ) . '" target="_blank">' . wp_kses_post( rawurldecode( $product_object->get_formatted_name() ) ) .'</a></strong></p>'
                                    . '<p>' . $product_object->get_price_html() . '</p>'
                                    . '<p>' . $product_cats . '</p>'
                                    . '</div>'
                                    . '<a href="javascript:void(0)" data-product_id="' . $product_object->get_id() . '" class="mvx-create-pro-duplicate-btn btn btn-default item-sell">' . __('Sell yours', 'multivendorx') . '</a>'
                                    . '</div>';
                        } else {
                            
                        }
                    }
                }
            } else {
                $html .= '<div class="search-result-clm"><div class="result-content">' . __('No Suggestions found', 'multivendorx') . "</div></div>";
            }
        } else {
            $html .= '<div class="search-result-clm"><div class="result-content">' . __('Empty search field! Enter a text to search.', 'multivendorx') . "</div></div>";
        }
        wp_send_json(array('results' => $html));
        die;
    }

    public function mvx_set_classified_product_terms() {
        $term_id = isset($_POST['term_id']) ? absint($_POST['term_id']) : 0;
        $taxonomy = isset($_POST['taxonomy']) ? wc_clean($_POST['taxonomy']) : '';
        $user_id = get_current_user_id();
        $url = '';
        if (is_user_mvx_vendor($user_id)) {
            $data = array(
                'term_id' => $term_id,
                'taxonomy' => $taxonomy,
            );
            set_transient('classified_product_terms_vendor' . $user_id, $data, HOUR_IN_SECONDS);
            $url = esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product')));
        }
        wp_send_json(array('url' => $url));
        die;
    }

    /**
     * Add an attribute row.
     */
    public function edit_product_attribute_callback() {
        global $MVX;
        ob_start();

        check_ajax_referer('add-attribute', 'security');

        if (!current_user_can('edit_products') || (!apply_filters('mvx_vendor_can_add_custom_attribute', true) && empty(sanitize_text_field($_POST['taxonomy'])) )) {
            wp_die(-1);
        }

        $i = isset($_POST['i']) ? absint($_POST['i']) : 0;
        $metabox_class = array();
        $attribute = new WC_Product_Attribute();

        $attribute->set_id(wc_attribute_taxonomy_id_by_name(sanitize_text_field($_POST['taxonomy'])));
        $attribute->set_name(sanitize_text_field($_POST['taxonomy']));
        $attribute->set_visible(apply_filters('woocommerce_attribute_default_visibility', 1));
        $attribute->set_variation(apply_filters('woocommerce_attribute_default_is_variation', 0));

        if ($attribute->is_taxonomy()) {
            $metabox_class[] = 'taxonomy';
            $metabox_class[] = $attribute->get_name();
        }

        include( $MVX->plugin_path . 'templates/vendor-dashboard/product-manager/views/html-product-attribute.php' );
        wp_die();
    }

    /**
     * Save attributes
     */
    public function save_product_attributes_callback() {
        check_ajax_referer('save-attributes', 'security');

        if (!current_user_can('edit_products')) {
            wp_die(-1);
        }

        parse_str($_POST['data'], $data);

        $attr_data = isset($data['wc_attributes']) ? $data['wc_attributes'] : array();

        $attributes = mvx_woo()->prepare_attributes($attr_data);
        $product_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $product_type = !empty($_POST['product_type']) ? wc_clean($_POST['product_type']) : 'simple';
        $classname = WC_Product_Factory::get_product_classname($product_id, $product_type);
        $product = new $classname($product_id);

        $product->set_attributes($attributes);
        $product->save();
        wp_die();
    }

    /**
     * Handle a refund via the edit order screen.
     *
     * @throws Exception To return errors.
     */
    public function mvx_do_refund() {
        ob_start();
        global $MVX;

        check_ajax_referer('mvx-order-item', 'security');
        
        $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
        $refund_amount = wc_format_decimal(sanitize_text_field(wp_unslash($_POST['refund_amount'])), wc_get_price_decimals());
        $refunded_amount = wc_format_decimal(sanitize_text_field(wp_unslash($_POST['refunded_amount'])), wc_get_price_decimals());
        $refund_reason = sanitize_text_field($_POST['refund_reason']);
        $line_item_qtys = json_decode(sanitize_text_field(wp_unslash($_POST['line_item_qtys'])), true);
        $line_item_totals = json_decode(sanitize_text_field(wp_unslash($_POST['line_item_totals'])), true);
        $line_item_tax_totals = json_decode(sanitize_text_field(wp_unslash($_POST['line_item_tax_totals'])), true);
        $api_refund = 'true' === wc_clean($_POST['api_refund']);
        $restock_refunded_items = 'true' === wc_clean($_POST['restock_refunded_items']);
        $refund = false;
        $response_data = array();

        try {
            $order = wc_get_order($order_id);

            $parent_order_id = wp_get_post_parent_id($order_id);
            $parent_order = wc_get_order( $parent_order_id );
            $parent_items_ids = array_keys($parent_order->get_items( array( 'line_item', 'fee', 'shipping' ) ));

            $order_items = $order->get_items();
            $max_refund = wc_format_decimal($order->get_total() - $order->get_total_refunded(), wc_get_price_decimals());

            if (!$refund_amount || $max_refund < $refund_amount || 0 > $refund_amount) {
                throw new exception(__('Invalid refund amount', 'multivendorx'));
            }

            if ($refunded_amount !== wc_format_decimal($order->get_total_refunded(), wc_get_price_decimals())) {
                throw new exception(__('Error processing refund. Please try again.', 'multivendorx'));
            }

            // Prepare line items which we are refunding.
            $line_items = array();
            $parent_line_items = array();

            $item_ids = array_unique(array_merge(array_keys($line_item_qtys, $line_item_totals)));

            foreach ($item_ids as $item_id) {
                $line_items[$item_id] = array(
                    'qty' => 0,
                    'refund_total' => 0,
                    'refund_tax' => array(),
                );
                $parent_item_id = $MVX->order->get_vendor_parent_order_item_id($item_id);
                if( $parent_item_id && in_array($parent_item_id, $parent_items_ids) ){
                    $parent_line_items[$parent_item_id] = array(
                        'qty' => 0,
                        'refund_total' => 0,
                        'refund_tax' => array(),
                    );
                }
            }
            foreach ($line_item_qtys as $item_id => $qty) {
                $line_items[$item_id]['qty'] = max($qty, 0);
                
                $parent_item_id = $MVX->order->get_vendor_parent_order_item_id($item_id);
                if( $parent_item_id && in_array($parent_item_id, $parent_items_ids) ){
                    $parent_line_items[$parent_item_id]['qty'] = max($qty, 0);
                }
            }
            foreach ($line_item_totals as $item_id => $total) {
                $line_items[$item_id]['refund_total'] = wc_format_decimal($total);
                
                $parent_item_id = $MVX->order->get_vendor_parent_order_item_id($item_id);
                if( $parent_item_id && in_array($parent_item_id, $parent_items_ids) ){
                    $parent_line_items[$parent_item_id]['refund_total'] = wc_format_decimal($total);
                }
            }   
            foreach ($line_item_tax_totals as $item_id => $tax_totals) {
                $line_items[$item_id]['refund_tax'] = array_filter(array_map('wc_format_decimal', $tax_totals));
                
                $parent_item_id = $MVX->order->get_vendor_parent_order_item_id($item_id);
                if( $parent_item_id && in_array($parent_item_id, $parent_items_ids) ){
                    $parent_line_items[$parent_item_id]['refund_tax'] = array_filter(array_map('wc_format_decimal', $tax_totals));
                }
            }

            // Create the refund object.
            $refund = wc_create_refund(
                    array(
                        'amount' => $refund_amount,
                        'reason' => $refund_reason,
                        'order_id' => $order_id,
                        'line_items' => $line_items,
                        'refund_payment' => $api_refund,
                        'restock_items' => $restock_refunded_items,
                    )
            );
            
            if( $parent_line_items ){
                if (apply_filters('mvx_allow_refund_parent_order', true)) {
                    $parent_refund = wc_create_refund(
                            array(
                                'amount' => $refund_amount,
                                'reason' => $refund_reason,
                                'order_id' => $parent_order_id,
                                'line_items' => $parent_line_items,
                                'refund_payment' => $api_refund,
                                'restock_items' => $restock_refunded_items,
                            )
                    );
                }
            }

            if (is_wp_error($refund)) {
                throw new Exception($refund->get_error_message());
            }
            if (is_wp_error($parent_refund)) {
                throw new Exception($parent_refund->get_error_message());
            }
            
            do_action( 'mvx_order_refunded', $order_id, $refund->get_id() );

            if (did_action('woocommerce_order_fully_refunded')) {
                $response_data['status'] = 'fully_refunded';
            }

            wp_send_json_success($response_data);
            die;
        } catch (Exception $e) {
            wp_send_json_error(array('error' => $e->getMessage()));
            die;
        }
    }
    
    /**
     * Search for downloadable product variations and return json.
     *
     * @see MVX_AJAX::json_search_products()
     */
    public function mvx_json_search_downloadable_products_and_variations() {
        check_ajax_referer( 'search-products', 'security' );

            $term       = (string) wc_clean( wp_unslash( $_GET['term'] ) );
            $data_store = WC_Data_Store::load( 'product' );
            $ids        = $data_store->search_products( $term, 'downloadable', true );

            if ( ! empty( $_GET['exclude'] ) ) {
                    $ids = array_diff( $ids, array_filter(wc_clean($_GET['exclude'] ) ) );
            }

            if ( ! empty( $_GET['include'] ) ) {
                    $ids = array_intersect( $ids, array_filter(wc_clean($_GET['include'] ) ) );
            }

            if ( ! empty( $_GET['limit'] ) ) {
                    $ids = array_slice( $ids, 0, absint( $_GET['limit'] ) );
            }
            
            if (is_user_mvx_vendor(get_current_user_id() ) ) {
                $vendor = get_mvx_vendor(get_current_user_id() );
                $vendor_product_ids = wp_list_pluck( $vendor->get_products_ids(), 'ID' );
                $ids = array_intersect( $ids, (array) $vendor_product_ids );
            }

            $product_objects = array_filter( array_map( 'wc_get_product', $ids ), 'wc_products_array_filter_readable' );
            $products        = array();

            foreach ( $product_objects as $product_object ) {
                    $products[ $product_object->get_id() ] = rawurldecode( $product_object->get_formatted_name() );
            }

            wp_send_json( $products );
    }
    
    /**
     * Search for products and echo json.
     *
     * @param string $term (default: '')
     * @param bool   $include_variations in search or not
     */
    public static function mvx_json_search_products_and_variations() {
        check_ajax_referer('search-products', 'security');

        $term = wc_clean(empty($term) ? wp_unslash($_GET['term']) : $term);

        if (empty($term)) {
            wp_die();
        }

        if (!empty($_GET['limit'])) {
            $limit = absint($_GET['limit']);
        } else {
            $limit = absint(apply_filters('woocommerce_json_search_limit', 30));
        }

        $data_store = WC_Data_Store::load('product');
        $ids = $data_store->search_products($term, '', (bool) false, false, $limit);

        if (!empty($_GET['exclude'])) {
            $ids = array_diff($ids, array_filter(wc_clean($_GET['exclude'])));
        }

        if (!empty($_GET['include'])) {
            $ids = array_intersect($ids, array_filter(wc_clean($_GET['include'])));
        }
        
        if (is_user_mvx_vendor(get_current_user_id() ) ) {
            $vendor = get_mvx_vendor(get_current_user_id() );
            $vendor_product_ids = wp_list_pluck( $vendor->get_products_ids(), 'ID' );
            $ids = array_intersect( $ids, (array) $vendor_product_ids );
        }

        $product_objects = array_filter(array_map('wc_get_product', $ids), 'wc_products_array_filter_readable');
        $products = array();

        foreach ($product_objects as $product_object) {
            $formatted_name = $product_object->get_formatted_name();
            $managing_stock = $product_object->managing_stock();

            if ($managing_stock && !empty($_GET['display_stock'])) {
                $formatted_name .= ' &ndash; ' . wc_format_stock_for_display($product_object);
            }

            $products[$product_object->get_id()] = rawurldecode($formatted_name);
        }

        wp_send_json(apply_filters('mvx_json_search_found_products', $products));
    }

    /**
     * Grant download permissions via ajax function.
     */
    public function mvx_grant_access_to_download(){
        global $MVX;
        check_ajax_referer( 'grant-access', 'security' );

        if ( ! current_user_can( 'edit_shop_orders' ) ) {
                wp_die( -1 );
        }

        global $wpdb;

        $wpdb->hide_errors();

        $order_id     = isset( $_POST['order_id'] ) ? absint($_POST['order_id']) : 0;
        $product_ids  = isset( $_POST['product_ids'] ) ? array_filter( array_map( 'intval', (array) $_POST['product_ids'] ) ) : array();
        $loop         = isset( $_POST['loop'] ) ? absint($_POST['loop']) : 0;
        $file_counter = 0;
        $order        = wc_get_order( $order_id );

        if ( ! is_array( $product_ids ) ) {
                $product_ids = array( $product_ids );
        }

        foreach ( $product_ids as $product_id ) {
                $product = wc_get_product( $product_id );
                $files   = $product->get_downloads();

                if ( ! $order->get_billing_email() ) {
                        wp_die();
                }

                if ( ! empty( $files ) ) {
                        foreach ( $files as $download_id => $file ) {
                                if ( $inserted_id = wc_downloadable_file_permission( $download_id, $product_id, $order ) ) {
                                        $download = new WC_Customer_Download( $inserted_id );
                                        $loop ++;
                                        $file_counter ++;

                                        if ( $file->get_name() ) {
                                                $file_count = $file->get_name();
                                        } else {
                                                $file_count = sprintf( __( 'File %d', 'multivendorx' ), $file_counter );
                                        }
                                        include $MVX->plugin_path . 'templates/vendor-dashboard/vendor-orders/views/html-order-download-permission.php';
                                }
                        }
                }
        }
        wp_die();
    }
    
    public function mvx_order_status_changed() {
        check_ajax_referer('grant-access', 'security');
        if (!current_user_can('edit_shop_orders')) {
            wp_die(-1);
        }
        $order_id = isset( $_POST['order_id'] ) ? absint($_POST['order_id']) : 0;
        $selected_status = isset( $_POST['selected_status'] ) ? wc_clean($_POST['selected_status']) : '';
        $order = wc_get_order( $order_id );
        if( $order ) {
            // fetch actual status
            $status = str_replace( 'wc-', '', $selected_status );
            $order->update_status( $status );
            wp_send_json( array( 'status_name' => esc_html( wc_get_order_status_name( $order->get_status() ) ), 'status_key' => $selected_status ) );
        }
        die;
    }
    
    public function mvx_vendor_banking_ledger_list(){
        global $MVX;
        check_ajax_referer('mvx-ledger', 'security');
        if (is_user_logged_in() && is_user_mvx_vendor(get_current_vendor_id())) {
            $vendor = get_mvx_vendor(get_current_vendor_id());
            $requestData = ( $_REQUEST ) ? wc_clean( $_REQUEST ) : array();
            $data_store = $MVX->ledger->load_ledger_data_store();
            $vendor_all_ledgers = $data_store->get_ledger( array( 'vendor_id' => $vendor->id ), '', $requestData );
            $initial_balance = $ending_balance = $total_credit = $total_debit = 0;
            $vendor_ledgers = apply_filters( 'mvx_vendor_banking_ledger_lists', array_slice( $vendor_all_ledgers, $requestData['start'], $requestData['length'] ), $vendor, $requestData );
            // get initial balance
            $inital_data = end( $vendor_ledgers );
            $initial_balance = ( $inital_data->balance && $inital_data->balance != '' ) ? $inital_data->balance : 0;
            //get ending balance
            $ending_data = reset( $vendor_ledgers );
            $ending_balance = ( $ending_data->balance && $ending_data->balance != '' ) ? $ending_data->balance : 0;
            $data = array();
            if ( $vendor_ledgers ) {
                foreach ($vendor_ledgers as $ledger ) {
                    // total credited balance
                    $total_credit += floatval( $ledger->credit );
                    // total debited balance
                    $total_debit += floatval( $ledger->debit );
                    
                    $order = wc_get_order( $ledger->order_id );
                    $currency = '';
                    if( $order ){
                        $currency = $order->get_currency();
                    }
                    
                    $ref_types = get_mvx_ledger_types();
                    $ref_type = isset($ref_types[$ledger->ref_type]) ? $ref_types[$ledger->ref_type] : ucfirst( $ledger->ref_type );
                    $type = '<mark class="type ' . $ledger->ref_type . '"><span>' . $ref_type . '</span></mark>';
                    $status = $ledger->ref_status;
                    if( $ledger->ref_status == 'unpaid' ){
                        $status = '<i class="'. $ledger->ref_status .' mvx-font ico-processing-status-icon" title="'. ucfirst($ledger->ref_status).'"></i>';
                    }elseif( $ledger->ref_status == 'completed' ){
                        $status = '<i class="'. $ledger->ref_status.' mvx-font ico-completed-status-icon" title="'. ucfirst($ledger->ref_status).'"></i>';
                    }elseif( $ledger->ref_status == 'cancelled' ){
                        $status = '<i class="'. $ledger->ref_status .' mvx-font ico-processing-status-icon" title="'. ucfirst($ledger->ref_status).'"></i>';
                    }
                    // Update commission status
                    if($ledger->ref_type == 'commission' && get_post_meta($ledger->ref_id, '_paid_status', true) == 'paid') 
                        $status = '<i class="'. get_post_meta($ledger->ref_id, '_paid_status', true).' mvx-font ico-completed-status-icon" title="'. ucfirst(get_post_meta($ledger->ref_id, '_paid_status', true)).'"></i>';
                    $row = array();
                    $row ['status'] = $status;
                    $row ['date'] = mvx_date($ledger->created);
                    $row ['ref_type'] = $type;
                    $row ['ref_info'] = $ledger->ref_info;
                    $row ['credit'] = ( $ledger->credit ) ? wc_price($ledger->credit, array('currency' => $currency)) : '';
                    $row ['debit'] = ( $ledger->debit ) ? wc_price($ledger->debit, array('currency' => $currency)) : '';
                    $row ['balance'] = wc_price($ledger->balance, array('currency' => $currency));
                    $data[] = apply_filters( 'mvx_vendor_banking_ledger_list_rows', $row, $ledger );
                }
            }

            $json_data = array(
                "initial_bal" => wc_price( $initial_balance ),
                "ending_bal" => wc_price( $ending_balance ),
                "total_credit" => wc_price( $total_credit ),
                "total_debit" => wc_price( $total_debit ),
                "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
                "recordsTotal" => intval(count($vendor_all_ledgers)), // total number of records
                "recordsFiltered" => intval(count($vendor_all_ledgers)), // total number of records after searching, if there is no searching then totalFiltered = totalData
                "data" => $data   // total data array
            );
            wp_send_json($json_data);
            die;
        }
    }

    /**
     * Get Translations table content for Product manager
     *
     * @return string
     */
    function wpml_mvx_product_translations() {
        global $sitepress, $wpml_post_translations, $_POST, $MVX;
        check_ajax_referer('mvx-dashboard', 'security');
        $translation_html = '';
        if( isset( $_POST['proid'] ) && !empty( $_POST['proid'] ) ) {
            $product_id = $_POST['proid'];
            if( $product_id ) {
                $active_languages = $MVX->frontend->get_filtered_active_lanugages();
                if ( count( $active_languages ) <= 1 ) {
                    return;
                }
                $current_language = $sitepress->get_current_language();
                unset( $active_languages[ $current_language ] );
        
                if ( count( $active_languages ) > 0 ) {
                    foreach ( $active_languages as $language_data ) {
                        $translated_id = $wpml_post_translations->element_id_in( $product_id, $language_data['code'] );
                        $trid = $wpml_post_translations->get_element_trid ( $product_id );
                        $translation_edit_url = '';
                        if( $translated_id ) {
                            $translate_text = sprintf( __( 'Edit the %s translation', 'multivendorx' ), $language_data['display_name'] );
                            $product_url = mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product'), $translated_id, '', $language_data['code']);
                            $translation_edit_url = '<a href="' . $product_url . '" title="' . $translate_text . '"><img style="padding:1px;margin:2px;" border="0" src="' . ICL_PLUGIN_URL . '/res/img/edit_translation.png" alt="' . $translate_text . '" width="16" height="16" /></a>';
                        } else {
                            $translate_text = sprintf( __( 'Add translation to %s', 'multivendorx' ), $language_data['display_name'] );
                            $translation_edit_url = '<a href="#" class="mvx_product_new_translation" data-trid="' . $trid . '" data-source_lang="' . $current_language . '" data-proid="' . $product_id . '" data-lang="' . $language_data['code'] . '" title="' . $translate_text . '"><img style="padding:1px;margin:2px;" border="0" src="' . ICL_PLUGIN_URL . '/res/img/add_translation.png" alt="' . $translate_text . '" width="16" height="16" /></a>';
                        }
                        
                        $translation_html .= '<tr><td><img src="' . $sitepress->get_flag_url( $language_data['code'] ). '" width="18" height="12" alt="' . $language_data['display_name'] . '" title="' . $language_data['display_name'] . '" style="margin:2px" /></td>';
                        $translation_html .= '<td>' . $translation_edit_url . '</td></tr>';
                    }
                }
            }
        }
        
        echo $translation_html;
        die;
    }

    function wpml_mvx_product_new_translation() {
        global $sitepress, $_POST, $wpdb;
        check_ajax_referer('mvx-dashboard', 'security');
        if( isset( $_POST['proid'] ) && !empty( $_POST['proid'] ) ) {
            $product_id = absint($_POST['proid']);
            if( $product_id ) {
                if( isset( $_POST['lang'] ) && !empty( $_POST['lang'] ) ) {
                    $lang_code = $_POST['lang'];
                    if( $lang_code ) {
                        $product = wc_get_product( $product_id );
                        if ( false === $product ) {
                            /* translators: %s: product id */
                            echo '{"status": false, "message": "' . sprintf( __( 'Product creation failed, could not find original product: %s', 'multivendorx' ), $product_id ) . '" }';
                        }

                        if( !class_exists( 'WC_Admin_Duplicate_Product' ) ) {
                            include( WC_ABSPATH . 'includes/admin/class-wc-admin-duplicate-product.php' );
                        }
                        $WC_Admin_Duplicate_Product = new WC_Admin_Duplicate_Product();
                        $duplicate = $WC_Admin_Duplicate_Product->product_duplicate( $product );

                        $vendor_id = get_mvx_product_vendors( $product_id ); 
                        if( !$vendor_id ) {
                            $vendor_id = apply_filters( 'mvx_current_vendor_id', get_current_user_id() );
                        }

                        // Update translated post to sete title/content empty
                        $my_post = apply_filters( 'mvx_translated_product_content_before_save', array(
                            'ID'           => $duplicate->get_id(),
                            'post_title'   => get_the_title( $product_id ) . ' (' . $lang_code . ' copy)',
                            'post_author'  => $vendor_id->id,
                            'post_content' => '',
                            'post_excerpt' => '',
                        ), $product_id );
                        wp_update_post( $my_post );

                        $source_lang = $_POST['source_lang'];
                        $dest_lang   = $_POST['lang'];
                        $trid        = $_POST['trid'];

                        // Connect Translations
                        $original_element_language = $sitepress->get_default_language();
                        $trid_elements             = $sitepress->get_element_translations( $trid, 'post_product' );
                        if($trid_elements) {
                            foreach ( $trid_elements as $trid_element ) {
                                if ( $trid_element->original ) {
                                    $original_element_language = $trid_element->language_code;
                                    break;
                                }
                            }
                        }
                        $wpdb->update(
                            $wpdb->prefix . 'icl_translations',
                            array( 'source_language_code' => $original_element_language, 'trid' => $trid ),
                            array( 'element_id' => $duplicate->get_id(), 'element_type' => 'post_product' ),
                            array( '%s', '%d', '%s' ),
                            array( '%d', '%s' )
                        );

                        do_action(
                            'wpml_translation_update',
                            array(
                                'type' => 'update',
                                'trid' => $trid,
                                'element_id' => $duplicate->get_id(),
                                'element_type' => 'post_product',
                                'context' => 'post'
                            )
                        );

                        // Product Custom Taxonomies - 6.0.3
                        $product_taxonomies = get_object_taxonomies( 'product', 'objects' );
                        if( !empty( $product_taxonomies ) ) {
                            foreach( $product_taxonomies as $product_taxonomy ) {
                                if( !in_array( $product_taxonomy->name, array( 'product_cat', 'product_tag', 'wcpv_product_vendors' ) ) ) {
                                    if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
                                        $taxonomy_values = get_the_terms( $product->get_id(), $product_taxonomy->name );
                                        $is_translated = $sitepress->is_translated_taxonomy( $product_taxonomy );
                                        $is_first = true;
                                        if( !empty($taxonomy_values) ) {
                                            foreach($taxonomy_values as $pkey => $ptaxonomy) {
                                                if( $is_translated ) {
                                                    $term_id = apply_filters( 'translate_object_id', (int)$ptaxonomy->term_id, $product_taxonomy->name, false, $dest_lang );
                                                } else {
                                                    $term_id = (int)$ptaxonomy->term_id;
                                                }
                                                if($is_first) {
                                                    $is_first = false;
                                                    wp_set_object_terms( $duplicate->get_id(), $term_id, $product_taxonomy->name );
                                                } else {
                                                    wp_set_object_terms( $duplicate->get_id(), $term_id, $product_taxonomy->name, true );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        do_action( 'mvx_after_translated_new_product', $duplicate->get_id() );

                        $product_url = esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product'), $duplicate->get_id()));

                        // Redirect to the edit screen for the new draft page
                        echo '{"status": true, "redirect": "' . $product_url . '", "id": "' . $duplicate->get_id() . '"}';
                    }
                }
            }
        }
        die;
    }

    public function mvx_follow_store_toggle_status() {
        check_ajax_referer('mvx-frontend', 'security');
        $store_vendor_id = isset( $_POST['vendor_id'] ) ? absint($_POST['vendor_id']) : 0;
        $follow_status = isset( $_POST['status'] ) ? wc_clean($_POST['status']) : '';
        $current_user_id = get_current_user_id() ? absint(get_current_user_id()) : 0;

        if (!$current_user_id || !$store_vendor_id)
            wp_send_json_error( new WP_Error( 'invalid_vendor', __( 'Invalid vendor or customer', 'multivendorx' ) ), 422 );

        // get followed vendor by customer
        $mvx_customer_follow_vendor = get_user_meta( $current_user_id, 'mvx_customer_follow_vendor', true ) ? get_user_meta( $current_user_id, 'mvx_customer_follow_vendor', true ) : array();
        
        // get folloed customer from vendor
        $mvx_vendor_followed_by_customer = get_user_meta( $store_vendor_id, 'mvx_vendor_followed_by_customer', true ) ? get_user_meta( $store_vendor_id, 'mvx_vendor_followed_by_customer', true ) : array();

        if ($follow_status && $follow_status == 'Follow') {

            $follow_vendors_details[] = array(
                'user_id'   =>   !empty($mvx_customer_follow_vendor['user_id']) && in_array($store_vendor_id, $mvx_customer_follow_vendor['user_id']) ? '' : $store_vendor_id,
                'timestamp' => current_time( 'mysql' ),
            );
            $followed_by_customer[] = array(
                'user_id'   =>   !empty($mvx_vendor_followed_by_customer['user_id']) && in_array($current_user_id, $mvx_vendor_followed_by_customer['user_id']) ? '' : $current_user_id,
                'timestamp' => current_time( 'mysql' ),
            );

            $follow_vendors_details = array_merge( $follow_vendors_details, $mvx_customer_follow_vendor );
            $followed_by_customer = array_merge( $followed_by_customer, $mvx_vendor_followed_by_customer );
        }

        if ($follow_status && $follow_status == 'Unfollow') {
            foreach ($mvx_customer_follow_vendor as $key => $value) {
                if (absint($value['user_id']) == absint($store_vendor_id)) {
                    $follow_vendors_details = $key;
                }
            }

            foreach ($mvx_vendor_followed_by_customer as $key_by_customer => $value_by_customer) {
                if (absint($value_by_customer['user_id']) == absint($current_user_id)) {
                    $unset_customer_key = $key_by_customer;
                }
            }

            unset($mvx_customer_follow_vendor[$follow_vendors_details]);
            $follow_vendors_details = $mvx_customer_follow_vendor;

            unset($mvx_vendor_followed_by_customer[$unset_customer_key]);
            $followed_by_customer = $mvx_vendor_followed_by_customer;
        }

        update_user_meta( $current_user_id, 'mvx_customer_follow_vendor', array_filter( array_map( 'wc_clean', (array) $follow_vendors_details ) ) );
        update_user_meta( $store_vendor_id, 'mvx_vendor_followed_by_customer', array_filter( array_map( 'wc_clean', (array) $followed_by_customer ) ) );

        if ( is_wp_error( $follow_status ) ) {
            wp_send_json_error( $follow_status, 422 );
        }
        wp_send_json_success( array( 'status' => $follow_status ), 200 );
    }

    // Update vendor shipping zone order
    public function mvx_vendor_zone_shipping_order() {
        check_ajax_referer('mvx-shipping-zone', 'security');
        $array_items = array();
        foreach (explode("&", $_POST['data_detail']) as $value) {
            $array_items[] = (int) str_replace("item[]=","",$value);
        }
        if (!empty($array_items)) {
            update_user_meta(get_current_vendor_id(), 'mvx_vendor_shipping_zone_order', array_filter(wc_clean($array_items)));
        }
    }


    public function mvx_datatable_get_vendor_refund() {
        check_ajax_referer('mvx-dashboard', 'security');
        $requestData = ( $_REQUEST ) ? wp_unslash( $_REQUEST ) : array();
        $notices = $data = array();
        $vendor = get_current_vendor() ? get_current_vendor() : '';
        if ($vendor) {
            $args = array(
            'author' => $vendor->id,
            'post_status' => 'any',
            'meta_query' => array(
                    array(
                        'key' => '_customer_refund_order',
                        'value' => array('refund_request', 'refund_accept', 'refund_reject'),
                        'compare' => '='
                    )
                )
            );
            $vendor_all_orders = apply_filters('mvx_datatable_refund_vendor_all_orders', mvx_get_orders($args, 'object'), $requestData, $_POST);
            $vendor_orders = array_slice($vendor_all_orders, $requestData['start'], $requestData['length']);
            if ($vendor_orders) {
                foreach ($vendor_orders as $order) {
                    $row_actions_col = array();
                    $actions_col = array(
                        'pending' => '<a href="' . esc_url(wp_nonce_url(add_query_arg(array('order_id' => $order->get_id()), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_refund_req_endpoint', 'seller_dashbaord', 'refund-request'))), 'mvx_pending_refund'))   . '" title="' . __('Pending Refund', 'multivendorx') . '"><i class="mvx-font ico-expire-icon"></i></a>',                    
                        'accept' => '<a href="' . esc_url(wp_nonce_url(add_query_arg(array('order_id' => $order->get_id()), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_refund_req_endpoint', 'seller_dashbaord', 'refund-request'))), 'mvx_accept_refund'))   . '" title="' . __('Accept Refund', 'multivendorx') . '"><i class="mvx-font ico-approve-icon"></i></a>',
                        'reject' => '<a href="' . esc_url(wp_nonce_url(add_query_arg(array('order_id' => $order->get_id()), mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_refund_req_endpoint', 'seller_dashbaord', 'refund-request'))), 'mvx_reject_refund'))  . '" title="' . __('Reject Refund', 'multivendorx') . '"><i class="mvx-font ico-reject-icon"></i></a>',
                    );

                    $refund_status = '';
                    $refund_status_raw = $order->get_meta('_customer_refund_order') ? $order->get_meta('_customer_refund_order') : '';
                    switch ($refund_status_raw) {
                        case 'refund_request':
                            $refund_status = __('Refund Pending','multivendorx');
                            unset($actions_col['pending']);
                            break;
                        case 'refund_accept':
                            $refund_status = __('Refund Accepted','multivendorx');
                            unset($actions_col['accept']);
                            break;
                        case 'refund_reject':
                            $refund_status = __('Refund Rejected','multivendorx');
                            unset($actions_col['reject']);
                            break;
                        default:
                            $refund_status = __('-','multivendorx');
                            break;
                    }

                    
                    if ($actions_col) {
                        foreach ($actions_col as $action => $link) {
                            $row_actions_col[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
                        }
                    }
                    
                    $actions_col_html = '<div class="col-actions">' . implode(' <span class="divider">|</span> ', $row_actions_col) . '</div>';

                    $data[] = apply_filters('mvx_datatable_refund_list_row', array(
                        'order_id'       => '<a href="' . esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders'), $order->get_id())) . '">#' . $order->get_id() . '</a>' ,
                        'order_status'   => esc_html(wc_get_order_status_name($order->get_status())),
                        'refund_status'  => $refund_status,
                        'refund_reason'  => $order->get_meta('_customer_refund_reason') ? esc_html($order->get_meta('_customer_refund_reason')) : '-',
                        'payment_gateway'=> $order->get_payment_method_title(),
                        'action'         => $actions_col_html
                        ), $order); 
                }
            }
        }

        $json_data = array(
            "draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
            "recordsTotal" => intval(count($vendor_all_orders)), // total number of records
            "recordsFiltered" => intval(count($vendor_all_orders)), // total number of records after searching, if there is no searching then totalFiltered = totalData
            "data" => $data,   // total data array
            "notices" => $notices,  // set messages or notices
        );
        wp_send_json($json_data);
    }

    public function mvx_show_all_products() {
        global $MVX;
        $vendor_id = get_current_vendor_id() ? absint(get_current_vendor_id()) : 0;
        $default = array(
            'posts_per_page'   => -1,
            'post_type'        => 'product',
            'post_status' => 'publish',
            'author__not_in' => $vendor_id,
        );
        $query = new WP_Query( $default ); 
        $MVX->template->get_template( 'vendor-dashboard/product-manager/show_products.php', array('query' => $query) );	
        die;
    }

    function mvx_sent_deactivation_request() {
        $vendor_id = isset( $_REQUEST['vendor_id'] ) ? absint( wp_unslash( $_REQUEST['vendor_id'] ) ) : 0;
        $reason = isset( $_REQUEST['reason'] ) ? wc_clean( $_REQUEST['reason'] ) : '';
        if ($vendor_id && !empty($reason)) {
            update_user_meta($vendor_id, '_deactivate_reason', $reason);
            $email = isset(WC()->mailer()->emails['WC_Email_Admin_Vendor_Account_Deactivation_Request_Mail']) ? WC()->mailer()->emails['WC_Email_Admin_Vendor_Account_Deactivation_Request_Mail'] : '';
            if (!empty($email) && $email->is_enabled()) {
                $email->trigger($vendor_id);
            }
            wp_send_json_success( "We have submitted a request to the admin for approval. Please await confirmation from the admin's end.", 'multivendorx' );
        } else {
            wp_send_json_error( 'Enter your reason.', 'multivendorx' );
        }
        die;
    }
}