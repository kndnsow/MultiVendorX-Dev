<?php

/**
 * MVX Cron Job Class
 *
 * @version		2.2.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class MVX_Cron_Job {

    public function __construct() {
        add_action('masspay_cron_start', array(&$this, 'do_mass_payment'));
        // vendor weekly order stats reports
        add_action('vendor_weekly_order_stats', array(&$this, 'vendor_weekly_order_stats_report'));
        // vendor monthly order stats reports
        add_action('vendor_monthly_order_stats', array(&$this, 'vendor_monthly_order_stats_report'));
        // migrate all products having parent-child concept
        add_action('migrate_spmv_multivendor_table', array(&$this, 'migrate_spmv_multivendor_table'));
        // vendor Custom date order stats reports
        add_action('vendor_custom_date_order_stats', array(&$this, 'vendor_custom_date_order_stats_report'));
        // bind spmv excluded products mapping 
        add_action('mvx_spmv_excluded_products_map', array(&$this, 'mvx_spmv_excluded_products_map'));
        // bind spmv excluded products mapping 
        add_action('mvx_spmv_product_meta_update', array(&$this, 'mvx_spmv_product_meta_update'));
        // Reset product mapping
        add_action('mvx_reset_product_mapping_data', array(&$this, 'mvx_reset_product_mapping_data'), 10, 1);
        add_action('migrate_multivendor_table', array(&$this, 'migrate_multivendor_table'));
        // MVX order migration
        add_action('mvx_orders_migration', array(&$this, 'mvx_orders_migration'));
        //update users collected data using cron
        add_action('update_users_collection_using_cron', array(&$this, 'update_users_collection_using_cron'));
        // older wcmp settings migrated to mvx
        if (!mvx_get_option('_is_dismiss_mvx40_notice', false)) {
            add_action('mvx_older_settings_migrated_migration', array(&$this, 'mvx_older_settings_migrated_migration'));
        }

        $this->mvx_clear_scheduled_event();
    }

    /**
     * Clear scheduled event
     */
    function mvx_clear_scheduled_event() {
        $cron_hook_identifier = apply_filters('mvx_cron_hook_identifier', array(
            'masspay_cron_start',
            'vendor_weekly_order_stats',
            'vendor_monthly_order_stats',
            'vendor_custom_date_order_stats',
            'migrate_spmv_multivendor_table',
            'mvx_spmv_excluded_products_map',
            'mvx_spmv_product_meta_update',
            'mvx_older_settings_migrated_migration'
        ));
        if ($cron_hook_identifier) {
            foreach ($cron_hook_identifier as $cron_hook) {
                $timestamp = wp_next_scheduled($cron_hook);
                if ($timestamp && apply_filters('mvx_unschedule_'. $cron_hook . '_cron_event', false)) {
                    wp_unschedule_event($timestamp, $cron_hook);
                }
            }
        }
    }

    /**
     * Calculate the amount and selete payment method.
     *
     *
     */
    function do_mass_payment() {
        global $MVX;
        if (!get_mvx_global_settings('choose_payment_mode_automatic_disbursal')) {
            return;
        }
        $commission_to_pay = array();
        $commissions = $this->get_query_commission();
        if ($commissions && is_array($commissions)) {
            foreach ($commissions as $commission) {
                $commission_id = $commission->ID;
                $vendor_term_id = get_post_meta($commission_id, '_commission_vendor', true);
                $order_id = get_post_meta( $commission_id ,'_commission_order_id', true );
                $order = wc_get_order( $order_id );
                if ( is_a( $order, 'WC_Order' ) && !in_array( $order->get_status(), apply_filters( 'mvx_cron_mass_payment_exclude_order_statuses',array( 'failed', 'cancelled' ) ) ) ) {
                    $commission_to_pay[$vendor_term_id][] = $commission_id;
                }
            }
        }
        foreach ($commission_to_pay as $vendor_term_id => $commissions) {
            $vendor = get_mvx_vendor_by_term($vendor_term_id);
            if ($vendor) {
                $payment_method = get_user_meta($vendor->id, '_vendor_payment_mode', true);
                if ($payment_method && $payment_method != 'direct_bank') {
                    if (array_key_exists($payment_method, $MVX->payment_gateway->payment_gateways)) {
                        $MVX->payment_gateway->payment_gateways[$payment_method]->process_payment($vendor, $commissions);
                    }
                }
            }
        }
    }

    /**
     * Get Commissions
     *
     * @return object $commissions
     */
    public function get_query_commission() {
        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'meta_key' => '_paid_status',
            'meta_value' => 'unpaid',
            'posts_per_page' => 5
        );
        $commissions = get_posts($args);
        return $commissions;
    }

    /**
     * Weekly order stats report
     *
     * 
     */
    public function vendor_weekly_order_stats_report() {
        global $MVX;
        $vendors = get_mvx_vendors();
        if ($vendors && apply_filters('mvx_enabled_vendor_weekly_report_mail', true)) {
            foreach ($vendors as $key => $vendor_obj) {
                if ($vendor_obj->user_data->user_email) {
                    $order_data = array();
                    $vendor = get_mvx_vendor($vendor_obj->id);
                    $is_block = get_user_meta($vendor->id, '_vendor_turn_off', true);
                    if ($is_block) continue;
                    $email = WC()->mailer()->emails['WC_Email_Vendor_Orders_Stats_Report'];
                    $vendor_weekly_stats = $vendor->get_vendor_orders_reports_of('vendor_stats', array('vendor_id' => $vendor->id));
                    $transaction_details = $MVX->transaction->get_transactions($vendor->term_id, date('Y-m-d', strtotime('-7 days')), date('Y-m-d'));
                    if (is_array($vendor_weekly_stats)) {
                        $vendor_weekly_stats['total_transaction'] = array_sum(wp_list_pluck($transaction_details, 'total_amount'));
                    }
                    $report_data = array(
                        'period' => __('weekly', 'multivendorx'),
                        'start_date' => date('Y-m-d', strtotime('-7 days')),
                        'end_date' => @date('Y-m-d'),
                        'stats' => $vendor_weekly_stats,
                    );
                    $attachments = array();
                    $vendor_weekly_orders = $vendor->get_vendor_orders_reports_of('', array('vendor_id' => $vendor->id));
                    if ($vendor_weekly_orders && count($vendor_weekly_orders) > 0) {
                        foreach ($vendor_weekly_orders as $key => $data) {
                            if ($data->commission_id != 0 && $data->commission_id != '') {
                                $order_data[$data->commission_id] = $key;
                            }
                        }
                        if (count($order_data) > 0) {
                            $report_data['order_data'] = $order_data;
                            $args = array(
                                'filename' => 'OrderReports-' . $report_data['start_date'] . '-To-' . $report_data['end_date'] . '.csv',
                                'action' => 'temp',
                            );
                            $report_csv = $MVX->vendor_dashboard->generate_csv($order_data, $vendor, $args);
                            if ($report_csv)
                                $attachments[] = $report_csv;
                            if ($email->trigger($vendor, $report_data, $attachments)) {
                                $email->find[] = $vendor->page_title;
                                $email->replace[] = '{STORE_NAME}';
                                if (file_exists($report_csv)) {
                                    @unlink($report_csv);
                                }
                            } else {
                                if (file_exists($report_csv)) {
                                    @unlink($report_csv);
                                }
                            }
                        } else {
                            if (apply_filters('mvx_send_vendor_weekly_zero_order_stats_report', true, $vendor)) {
                                $report_data['order_data'] = $order_data;
                                if ($email->trigger($vendor, $report_data, $attachments)) {
                                    $email->find[] = $vendor->page_title;
                                    $email->replace[] = '{STORE_NAME}';
                                }
                            }
                        }
                    } else {
                        if (apply_filters('mvx_send_vendor_weekly_zero_order_stats_report', true, $vendor)) {
                            $report_data['order_data'] = $order_data;
                            if ($email->trigger($vendor, $report_data, $attachments)) {
                                $email->find[] = $vendor->page_title;
                                $email->replace[] = '{STORE_NAME}';
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Monthly order stats report
     *
     * 
     */
    public function vendor_monthly_order_stats_report() {
        global $MVX;
        $vendors = get_mvx_vendors();
        if ($vendors && apply_filters('mvx_enabled_vendor_monthly_report_mail', true)) {
            foreach ($vendors as $key => $vendor_obj) {
                if ($vendor_obj->user_data->user_email) {
                    $order_data = array();
                    $vendor = get_mvx_vendor($vendor_obj->id);
                    $is_block = get_user_meta($vendor->id, '_vendor_turn_off', true);
                    if ($is_block) continue;
                    $email = WC()->mailer()->emails['WC_Email_Vendor_Orders_Stats_Report'];
                    $vendor_monthly_stats = $vendor->get_vendor_orders_reports_of('vendor_stats', array('vendor_id' => $vendor->id, 'start_date' => date('Y-m-d H:i:s', strtotime('-30 days'))));
                    $transaction_details = $MVX->transaction->get_transactions($vendor->term_id, date('Y-m-d', strtotime('-30 days')), date('Y-m-d'));
                    if (is_array($vendor_monthly_stats)) {
                        $vendor_monthly_stats['total_transaction'] = array_sum(wp_list_pluck($transaction_details, 'total_amount'));
                    }
                    $report_data = array(
                        'period' => __('monthly', 'multivendorx'),
                        'start_date' => date('Y-m-d', strtotime('-30 days')),
                        'end_date' => @date('Y-m-d'),
                        'stats' => $vendor_monthly_stats,
                    );
                    $attachments = array();
                    $vendor_monthly_orders = $vendor->get_vendor_orders_reports_of('', array('vendor_id' => $vendor->id, 'start_date' => date('Y-m-d H:i:s', strtotime('-30 days'))));
                    if ($vendor_monthly_orders && count($vendor_monthly_orders) > 0) {
                        foreach ($vendor_monthly_orders as $key => $data) {
                            if ($data->commission_id != 0 && $data->commission_id != '') {
                                $order_data[$data->commission_id] = $key;
                            }
                        }
                        if (count($order_data) > 0) {
                            $report_data['order_data'] = $order_data;
                            $args = array(
                                'filename' => 'OrderReports-' . $report_data['start_date'] . '-To-' . $report_data['end_date'] . '.csv',
                                'action' => 'temp',
                            );
                            $report_csv = $MVX->vendor_dashboard->generate_csv($order_data, $vendor, $args);
                            if ($report_csv)
                                $attachments[] = $report_csv;
                            if ($email->trigger($vendor, $report_data, $attachments)) {
                                $email->find[] = $vendor->page_title;
                                $email->replace[] = '{STORE_NAME}';
                                if (file_exists($report_csv)) {
                                    @unlink($report_csv);
                                }
                            } else {
                                if (file_exists($report_csv)) {
                                    @unlink($report_csv);
                                }
                            }
                        } else {
                            if (apply_filters('mvx_send_vendor_monthly_zero_order_stats_report', true, $vendor)) {
                                $report_data['order_data'] = $order_data;
                                if ($email->trigger($vendor, $report_data, $attachments)) {
                                    $email->find[] = $vendor->page_title;
                                    $email->replace[] = '{STORE_NAME}';
                                }
                            }
                        }
                    } else {
                        if (apply_filters('mvx_send_vendor_monthly_zero_order_stats_report', true, $vendor)) {
                            $report_data['order_data'] = $order_data;
                            if ($email->trigger($vendor, $report_data, $attachments)) {
                                $email->find[] = $vendor->page_title;
                                $email->replace[] = '{STORE_NAME}';
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Order stats report by custom date
     *
     * 
     */
    public function vendor_custom_date_order_stats_report() {
        global $MVX;
        $custom_date_order_stat_report_mail = get_mvx_global_settings( 'custom_date_order_stat_report_mail' ) ? get_mvx_global_settings( 'custom_date_order_stat_report_mail' ) : 0;
        $vendors = get_mvx_vendors();
        if ($vendors && $custom_date_order_stat_report_mail && apply_filters('mvx_enabled_vendor_custom_date_report_mail', true)) {
            $strtotime = strtotime('-'. $custom_date_order_stat_report_mail .' days');
            foreach ($vendors as $key => $vendor_obj) {
                if ($vendor_obj->user_data->user_email) {
                    $order_data = array();
                    $vendor = get_mvx_vendor($vendor_obj->id);
                    $is_block = get_user_meta($vendor->id, '_vendor_turn_off', true);
                    if ($is_block) continue;
                    $email = WC()->mailer()->emails['WC_Email_Vendor_Orders_Stats_Report'];
                    $vendor_custom_date_stats = $vendor->get_vendor_orders_reports_of('vendor_stats', array('vendor_id' => $vendor->id, 'start_date' => date('Y-m-d H:i:s', $strtotime)));
                    $transaction_details = $MVX->transaction->get_transactions($vendor->term_id, date('Y-m-d', $strtotime), date('Y-m-d'));
                    if (is_array($vendor_custom_date_stats)) {
                        $vendor_custom_date_stats['total_transaction'] = array_sum(wp_list_pluck($transaction_details, 'total_amount'));
                    }
                    $report_data = array(
                        'period' => __('monthly', 'multivendorx'),
                        'start_date' => date('Y-m-d', $strtotime),
                        'end_date' => @date('Y-m-d'),
                        'stats' => $vendor_custom_date_stats,
                    );

                    $attachments = array();
                    $vendor_monthly_orders = $vendor->get_vendor_orders_reports_of('', array('vendor_id' => $vendor->id, 'start_date' => date('Y-m-d H:i:s', $strtotime)));
                    if ($vendor_monthly_orders && count($vendor_monthly_orders) > 0) {
                        foreach ($vendor_monthly_orders as $key => $data) {
                            if ($data->commission_id != 0 && $data->commission_id != '') {
                                $order_data[$data->commission_id] = $key;
                            }
                        }
                        if (count($order_data) > 0) {
                            $report_data['order_data'] = $order_data;
                            $args = array(
                                'filename' => 'OrderReports-' . $report_data['start_date'] . '-To-' . $report_data['end_date'] . '.csv',
                                'action' => 'temp',
                            );
                            $report_csv = $MVX->vendor_dashboard->generate_csv($order_data, $vendor, $args);
                            if ($report_csv)
                                $attachments[] = $report_csv;
                            if ($email->trigger($vendor, $report_data, $attachments)) {
                                $email->find[] = $vendor->page_title;
                                $email->replace[] = '{STORE_NAME}';
                                if (file_exists($report_csv)) {
                                    @unlink($report_csv);
                                }
                            } else {
                                if (file_exists($report_csv)) {
                                    @unlink($report_csv);
                                }
                            }
                        } else {
                            if (apply_filters('mvx_send_vendor_monthly_zero_order_stats_report', true, $vendor)) {
                                $report_data['order_data'] = $order_data;
                                if ($email->trigger($vendor, $report_data, $attachments)) {
                                    $email->find[] = $vendor->page_title;
                                    $email->replace[] = '{STORE_NAME}';
                                }
                            }
                        }
                    } else {
                        if (apply_filters('mvx_send_vendor_monthly_zero_order_stats_report', true, $vendor)) {
                            $report_data['order_data'] = $order_data;
                            if ($email->trigger($vendor, $report_data, $attachments)) {
                                $email->find[] = $vendor->page_title;
                                $email->replace[] = '{STORE_NAME}';
                            }
                        }
                    }
                }
            }
        }
    }

    public function migrate_spmv_multivendor_table() {
        global $MVX, $wpdb;
        $length = apply_filters('mvx_migrate_spmv_multivendor_table_length', 50);
        $args = apply_filters('mvx_migrate_spmv_table_products_query_args', array(
            'numberposts' => $length,
            'post_type' => 'product',
            'meta_key' => '_mvx_child_product',
            'meta_value' => '1',
            'fields' => 'id=>parent',
        ));
        $products = get_posts($args);

        if ($products){
            foreach ($products as $product_id => $parent_id) {
                if ($parent_id){
                    delete_post_meta($product_id, '_mvx_child_product');
                    wp_update_post(array('ID' => $product_id, 'post_parent' => 0), true);
                    $data = array('product_id' => $product_id);
                    if (get_post_meta($product_id, '_mvx_spmv_map_id', true) || get_post_meta($parent_id, '_mvx_spmv_map_id', true)){
                        $product_map_id = (get_post_meta($product_id, '_mvx_spmv_map_id', true)) ? get_post_meta($product_id, '_mvx_spmv_map_id', true) : 0;
                        $product_map_id = (get_post_meta($parent_id, '_mvx_spmv_map_id', true)) ? get_post_meta($parent_id, '_mvx_spmv_map_id', true) : $product_map_id;
                        $data['product_map_id'] = $product_map_id;
                    }
                    
                    $map_id = mvx_spmv_products_map($data, 'insert');
                    if ($map_id){
                        $data['product_map_id'] = $map_id;
                        $data['product_id'] = $parent_id;
                        mvx_spmv_products_map($data, 'insert');
                        //update meta
                        update_post_meta($product_id, '_mvx_spmv_product', true);
                        update_post_meta($parent_id, '_mvx_spmv_product', true);
                        update_post_meta($product_id, '_mvx_spmv_map_id', $map_id);
                        update_post_meta($parent_id, '_mvx_spmv_map_id', $map_id);
                    }
                }else{
                    delete_post_meta($product_id, '_mvx_child_product');
                }
            }
            // SPMV terms object update
            do_mvx_spmv_set_object_terms();
            $exclude_spmv_products = get_mvx_spmv_excluded_products_map_data();
            set_transient('mvx_spmv_exclude_products_data', $exclude_spmv_products, YEAR_IN_SECONDS);

        }else{
            mvx_update_option('spmv_multivendor_table_migrated', true);
            wp_clear_scheduled_hook('migrate_spmv_multivendor_table');
        }
    }

    public function mvx_spmv_excluded_products_map() {
        do_mvx_spmv_set_object_terms();
        $exclude_spmv_products = get_mvx_spmv_excluded_products_map_data();
        set_transient('mvx_spmv_exclude_products_data', $exclude_spmv_products, YEAR_IN_SECONDS);
    }
    
    public function mvx_spmv_product_meta_update() {
        $products_map_data = get_mvx_spmv_products_map_data();
        if ($products_map_data){
            foreach ($products_map_data as $product_map_id => $product_ids) {
                if ($product_ids){
                    foreach ($product_ids as $product_id) {
                        $is_mvx_spmv_product = get_post_meta($product_id, '_mvx_spmv_product', true);
                        $has_mvx_spmv_map_id = get_post_meta($product_id, '_mvx_spmv_map_id', true);
                        if (!$is_mvx_spmv_product || !$has_mvx_spmv_map_id){
                            update_post_meta($product_id, '_mvx_spmv_product', true);
                            update_post_meta($product_id, '_mvx_spmv_map_id', $product_map_id);
                        }
                    }
                }
            }
            do_mvx_spmv_set_object_terms();
            $exclude_spmv_products = get_mvx_spmv_excluded_products_map_data();
            set_transient('mvx_spmv_exclude_products_data', $exclude_spmv_products, YEAR_IN_SECONDS);
            mvx_update_option('mvx_spmv_product_meta_migrated', true);
        }
    }
    
    public function mvx_reset_product_mapping_data($map_id){
        do_mvx_spmv_set_object_terms($map_id);
        $exclude_spmv_products = get_mvx_spmv_excluded_products_map_data();
        set_transient('mvx_spmv_exclude_products_data', $exclude_spmv_products, YEAR_IN_SECONDS);
    }
    
    public function mvx_orders_migration() {
        global $MVX, $wpdb;

        $vendors = get_mvx_vendors();
        if ($vendors) {
            foreach ($vendors as $vendor) {
                $vendor_orders = get_mvx_vendor_orders(array('vendor_id' => $vendor->id));
                if ($vendor_orders){
                    $vendor_done_commissions_ids = array();
                    foreach ($vendor_orders as $vorder) {
                        if (!in_array($vorder->commission_id, $vendor_done_commissions_ids)){
                            $vendor_done_commissions_ids[] = $vorder->commission_id;
                            $commission_specific_orders = get_mvx_vendor_orders(array('vendor_id' => $vendor->id, 'commission_id' => $vorder->commission_id));
                            $items = array();
                            $commission_specific_orders_ids_done = array();
                            foreach ($commission_specific_orders as $corder) {
                                $order = wc_get_order($corder->order_id);
                                if (!$order){
                                    continue;
                                }
                                $vendor_specific_order_migrated = ($order->get_meta('_mvx_vendor_specific_order_migrated', true)) ? $order->get_meta('_mvx_vendor_specific_order_migrated', true) : array();
                                if (in_array($vendor->id, $vendor_specific_order_migrated) || in_array($corder->order_id, $commission_specific_orders_ids_done)){
                                    continue;
                                }
                                $vendor_specific_order_migrated[] = $vendor->id;
                                $commission_specific_orders_ids_done[] = $corder->order_id;

                                $items = $order->get_items();
                                $vendor_items = array();
                                foreach ($items as $item_id => $item) {
                                    if (isset($item['product_id']) && $item['product_id'] !== 0) {
                                        // check vendor product
                                        $has_vendor = get_mvx_product_vendors($item['product_id']);
                                        if ($has_vendor && $has_vendor->id == $vendor->id) {
                                            $vendor_items['commission'] = $corder->commission_amount;
                                            $vendor_items['commission_rate'] = array();
                                            $vendor_items[$item_id] = $item;
                                        }
                                    }
                                }
                                try {
                                    // migrate old orders
                                    require_once ( $MVX->plugin_path . '/classes/class-mvx-order.php' );
                                    $vendor_order_id = MVX_Order::create_vendor_order(array(
                                            'order_id' => $vorder->order_id,
                                            'vendor_id' => $vendor->id,
                                            'posted_data' => array(),
                                            'line_items' => $vendor_items
                                    ), true);
                                    $vendor_order = wc_get_order($vendor_order_id);
                                    // mark as shipped
                                    $shippers = $vendor_order->get_meta('dc_pv_shipped', true) ? $vendor_order->get_meta('dc_pv_shipped', true) : array();
                                    if (in_array($vendor->id, $shippers)) {
                                        update_post_meta($vendor_order_id, 'dc_pv_shipped', $shippers);
                                        // set new meta shipped
                                        update_post_meta($vendor_order_id, 'mvx_vendor_order_shipped', 1);
                                    }
                                    // add commission id in order meta
                                    $vendor_order->update_meta_data('_commission_id', $vorder->commission_id);
                                    // add order id with commission meta
                                    $vendor_order->update_meta_data('_commission_order_id', $vendor_order_id);
                                    // for track BW vendor order-commission
                                    $vendor_order->update_meta_data('_old_order_id', $vorder->order_id);
                                    // prevent duplication
                                    update_post_meta($corder->order_id, '_mvx_vendor_specific_order_migrated', $vendor_specific_order_migrated);
                                    do_action( 'mvx_orders_migration_order_created', $vendor_order_id, $vorder );  
                                } catch (Exception $exc) {
                                    doProductVendorLOG("Error in order migration create order :".$exc->getMessage());
                                }
                            }
                        }
                    }
                }
            }
        }
        mvx_update_option('mvx_orders_table_migrated', true);
        wp_clear_scheduled_hook('mvx_orders_migration');
    }

    public function mvx_older_settings_migrated_migration() {

        if (!mvx_get_option('_is_dismiss_mvx40_notice', false)) {
            //change shortcode content
            $list_of_all_pages = get_pages(array('post_status'   =>  'publish'));
            if ($list_of_all_pages) {
                foreach ($list_of_all_pages as $key_list => $value_list) {
                    if (stripos($value_list->post_content, 'wcmp') !== false) {
                        wp_update_post(array('ID' => $value_list->ID, 'post_content' => str_replace('wcmp', 'mvx', $value_list->post_content)));
                    }
                }
            }

            $get_managements_data = $seller_dashboard = $store_data = $products_data = $products_capabily_data = $spmv_data = $commission_data = $disbursement_data = $policy_data = $refund_data = $review_data = $social_data = $payemnts_masspay_data = $payemnts_payout_data = $payemnts_stripe_data = $pages_dashboard_array = $pages_array = [];

            $get_managements_data = mvx_get_option('mvx_settings_general_tab_settings', array());
            if (get_mvx_older_global_settings('approve_vendor_manually') && get_mvx_older_global_settings('approve_vendor_manually') == 'Enable') {
                $get_managements_data['approve_vendor'] = 'manually';
                mvx_update_option('mvx_settings_general_tab_settings', $get_managements_data);
            } else {
                $get_managements_data['approve_vendor'] = 'automatically';
                mvx_update_option('mvx_settings_general_tab_settings', $get_managements_data);
            }

            if (get_mvx_older_global_settings('is_disable_marketplace_plisting') && get_mvx_older_global_settings('is_disable_marketplace_plisting') == 'Enable') {
                $get_managements_data['category_pyramid_guide'] = array('category_pyramid_guide');
                mvx_update_option('mvx_settings_general_tab_settings', $get_managements_data);
            }
            
            $pages = get_pages();

            if (get_mvx_older_global_settings('vendor_registration')) {
                if ($pages) {
                    foreach ($pages as $page) {
                        if ($page->ID == get_mvx_older_global_settings('vendor_registration')) {
                            $pages_array = array(
                                'value'=> $page->ID,
                                'label'=> $page->post_title,
                                'key'=> $page->ID,
                            );
                        }
                    }
                }
                $get_managements_data['registration_page'] = $pages_array;
                mvx_update_option('mvx_settings_general_tab_settings', $get_managements_data);
            }

            if (get_mvx_older_global_settings('wcmp_vendor')) {
                if ($pages) {
                    foreach ($pages as $page) {
                        if ($page->ID == get_mvx_older_global_settings('wcmp_vendor')) {
                            $pages_dashboard_array = array(
                                'value'=> $page->ID,
                                'label'=> $page->post_title,
                                'key'=> $page->ID,
                            );
                        }
                    }
                }
                $get_managements_data['vendor_dashboard_page'] = $pages_dashboard_array;
                mvx_update_option('mvx_settings_general_tab_settings', $get_managements_data);
            }

            // seller dashboard
            if (get_mvx_older_global_settings('wcmp_dashboard_site_logo')) {
                $seller_dashboard['mvx_new_dashboard_site_logo'] = wp_get_attachment_image_src(get_mvx_older_global_settings('wcmp_dashboard_site_logo'))[0];
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('vendor_color_scheme_picker')) {
                $seller_dashboard['vendor_color_scheme_picker'] = get_mvx_older_global_settings('vendor_color_scheme_picker');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('setup_wizard_introduction')) {
                $seller_dashboard['setup_wizard_introduction'] = get_mvx_older_global_settings('setup_wizard_introduction');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_announcements_endpoint')) {
                $seller_dashboard['mvx_vendor_announcements_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_announcements_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_store_settings_endpoint')) {
                $seller_dashboard['mvx_store_settings_endpoint'] = get_mvx_older_global_settings('wcmp_store_settings_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_profile_endpoint')) {
                $seller_dashboard['mvx_profile_endpoint'] = get_mvx_older_global_settings('wcmp_profile_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_policies_endpoint')) {
                $seller_dashboard['mvx_vendor_policies_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_policies_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_billing_endpoint')) {
                $seller_dashboard['mvx_vendor_billing_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_billing_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_shipping_endpoint')) {
                $seller_dashboard['mvx_vendor_shipping_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_shipping_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_report_endpoint')) {
                $seller_dashboard['mvx_vendor_report_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_report_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_banking_overview_endpoint')) {
                $seller_dashboard['mvx_vendor_banking_overview_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_banking_overview_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_add_product_endpoint')) {
                $seller_dashboard['mvx_add_product_endpoint'] = get_mvx_older_global_settings('wcmp_add_product_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_edit_product_endpoint')) {
                $seller_dashboard['mvx_edit_product_endpoint'] = get_mvx_older_global_settings('wcmp_edit_product_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_products_endpoint')) {
                $seller_dashboard['mvx_products_endpoint'] = get_mvx_older_global_settings('wcmp_products_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_add_coupon_endpoint')) {
                $seller_dashboard['mvx_add_coupon_endpoint'] = get_mvx_older_global_settings('wcmp_add_coupon_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_coupons_endpoint')) {
                $seller_dashboard['mvx_coupons_endpoint'] = get_mvx_older_global_settings('wcmp_coupons_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_orders_endpoint')) {
                $seller_dashboard['mvx_vendor_orders_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_orders_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_withdrawal_endpoint')) {
                $seller_dashboard['mvx_vendor_withdrawal_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_withdrawal_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_transaction_details_endpoint')) {
                $seller_dashboard['mvx_transaction_details_endpoint'] = get_mvx_older_global_settings('wcmp_transaction_details_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_knowledgebase_endpoint')) {
                $seller_dashboard['mvx_vendor_knowledgebase_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_knowledgebase_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_tools_endpoint')) {
                $seller_dashboard['mvx_vendor_tools_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_tools_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_products_qnas_endpoint')) {
                $seller_dashboard['mvx_products_qna_endpoint'] = get_mvx_older_global_settings('wcmp_vendor_products_qnas_endpoint');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            if (get_mvx_older_global_settings('wcmp_vendor_dashboard_custom_css')) {
                $seller_dashboard['mvx_vendor_dashboard_custom_css'] = get_mvx_older_global_settings('wcmp_vendor_dashboard_custom_css');
                mvx_update_option('mvx_seller_dashbaord_tab_settings', $seller_dashboard);
            }
            do_action('mvx_seller_dashboard_settings_option', $seller_dashboard);

            // store settings
            if (get_mvx_older_global_settings('wcmp_vendor_shop_template')) {
                $store_data['mvx_vendor_shop_template'] = get_mvx_older_global_settings('wcmp_vendor_shop_template');
                mvx_update_option('mvx_store_tab_settings', $store_data);
            }

            if (get_mvx_older_global_settings('choose_map_api')) {
                $set_map_api = [];
                $choose_map_api = array(
                    array(
                        'key'=> "google_map_set",
                        'label'=> __('Google map', 'multivendorx'),
                        'value'=> 'google_map_set',
                    ),
                    array(
                        'key'=> "mapbox_api_set",
                        'label'=> __('Mapbox map', 'multivendorx'),
                        'value'=> 'mapbox_api_set',
                    )
                );
                foreach ($choose_map_api as $key_api => $value_api) {
                    if (get_mvx_older_global_settings('choose_map_api') == $value_api['value']) {
                        $set_map_api = array(
                            'value' => $value_api['value'],
                            'label' => $value_api['label'],
                            'index' => $key_api
                        );
                    }
                }

                $store_data['choose_map_api'] = $set_map_api;

                $store_data['enable_store_map_for_vendor'] = array('enable_store_map_for_vendor');

                mvx_update_option('mvx_store_tab_settings', $store_data);
            }

            if (get_mvx_older_global_settings('google_api_key')) {
                $store_data['google_api_key'] = get_mvx_older_global_settings('google_api_key');
                mvx_update_option('mvx_store_tab_settings', $store_data);
            }

            if (get_mvx_older_global_settings('mapbox_api_key')) {
                $store_data['mapbox_api_key'] = get_mvx_older_global_settings('mapbox_api_key');
                mvx_update_option('mvx_store_tab_settings', $store_data);
            }

            if (get_mvx_older_global_settings('is_enable_store_sidebar')) {
                $store_data['is_enable_store_sidebar_position'] = array('is_enable_store_sidebar_position');
                mvx_update_option('mvx_store_tab_settings', $store_data);
            }

            if (get_mvx_older_global_settings('store_sidebar_position')) {
                $store_data['mvx_store_sidebar_position'] = get_mvx_older_global_settings('store_sidebar_position');
                mvx_update_option('mvx_store_tab_settings', $store_data);
            }
            do_action('mvx_seller_store_settings_option', $store_data);

            // products data
            if (get_mvx_older_global_settings('virtual') && get_mvx_older_global_settings('virtual') == 'Enable') {
                $products_data['type_options'] = array('virtual');
                mvx_update_option('mvx_products_tab_settings', $products_data);
            }

            if (get_mvx_older_global_settings('downloadable') && get_mvx_older_global_settings('downloadable') == 'Enable') {
                $products_data['type_options'] = array('downloadable');
                mvx_update_option('mvx_products_tab_settings', $products_data);
            }

            if (get_mvx_older_global_settings('downloadable') && get_mvx_older_global_settings('downloadable') == 'Enable' && get_mvx_older_global_settings('virtual') && get_mvx_older_global_settings('virtual') == 'Enable') {
                $products_data['type_options'] = array('virtual', 'downloadable');
                mvx_update_option('mvx_products_tab_settings', $products_data);
            }

            // products compatibiluty
            if (get_mvx_older_global_settings('is_submit_product') && get_mvx_older_global_settings('is_submit_product') == 'Enable') {
                $products_capabily_data['is_submit_product'] = array('is_submit_product');
                mvx_update_option('mvx_products_capability_tab_settings', $products_capabily_data);
            }
            if (get_mvx_older_global_settings('is_published_product') && get_mvx_older_global_settings('is_published_product') == 'Enable') {
                $products_capabily_data['is_published_product'] = array('is_published_product');
                mvx_update_option('mvx_products_capability_tab_settings', $products_capabily_data);
            }
            if (get_mvx_older_global_settings('is_edit_delete_published_product') && get_mvx_older_global_settings('is_edit_delete_published_product') == 'Enable') {
                $products_capabily_data['is_edit_delete_published_product'] = array('is_edit_delete_published_product');
                mvx_update_option('mvx_products_capability_tab_settings', $products_capabily_data);
            }
            if (get_mvx_older_global_settings('publish_and_submit_products') && get_mvx_older_global_settings('publish_and_submit_products') == 'Enable') {
                $products_capabily_data['publish_and_submit_products'] = array('publish_and_submit_products');
                mvx_update_option('mvx_products_capability_tab_settings', $products_capabily_data);
            }
            if (get_mvx_older_global_settings('is_submit_coupon') && get_mvx_older_global_settings('is_submit_coupon') == 'Enable') {
                $products_capabily_data['is_submit_coupon'] = array('is_submit_coupon');
                mvx_update_option('mvx_products_capability_tab_settings', $products_capabily_data);
            }
            if (get_mvx_older_global_settings('is_published_coupon') && get_mvx_older_global_settings('is_published_coupon') == 'Enable') {
                $products_capabily_data['is_published_coupon'] = array('is_published_coupon');
                mvx_update_option('mvx_products_capability_tab_settings', $products_capabily_data);
            }
            if (get_mvx_older_global_settings('is_edit_delete_published_coupon') && get_mvx_older_global_settings('is_edit_delete_published_coupon') == 'Enable') {
                $products_capabily_data['is_edit_delete_published_coupon'] = array('is_edit_delete_published_coupon');
                mvx_update_option('mvx_products_capability_tab_settings', $products_capabily_data);
            }
            if (get_mvx_older_global_settings('is_upload_files') && get_mvx_older_global_settings('is_upload_files') == 'Enable') {
                $products_capabily_data['is_upload_files'] = array('is_upload_files');
                mvx_update_option('mvx_products_capability_tab_settings', $products_capabily_data);
            }

            // spmv
            if (get_mvx_older_global_settings('is_singleproductmultiseller') && get_mvx_older_global_settings('is_singleproductmultiseller') == 'Enable') {
                $spmv_data['is_singleproductmultiseller'] = array('is_singleproductmultiseller');
                mvx_update_option('mvx_spmv_pages_tab_settings', $spmv_data);
            }
            if (get_mvx_older_global_settings('singleproductmultiseller_show_order')) {
                $svmv_selection = [];
                $options_spmv = array(
                    array(
                        'key'=> "min-price",
                        'label'=> __('Min Price', 'multivendorx'),
                        'value'=> 'min-price',
                    ),
                    array(
                        'key'=> "max-price",
                        'label'=> __('Max Price', 'multivendorx'),
                        'value'=> 'max-price',
                    ),
                    array(
                        'key'=> "top-rated-vendor",
                        'label'=> __('Top rated vendor', 'multivendorx'),
                        'value'=> 'top-rated-vendor',
                    )
                );
                foreach ($options_spmv as $key_spmv => $value_spmv) {
                    if (get_mvx_older_global_settings('singleproductmultiseller_show_order') == $value_spmv['value']) {
                        $svmv_selection = array(
                            'label' => $value_spmv['label'],
                            'value' => $value_spmv['value'],
                            'index' => $key_spmv
                        );
                    }
                }
                $spmv_data['singleproductmultiseller_show_order'] = $svmv_selection;
                mvx_update_option('mvx_spmv_pages_tab_settings', $spmv_data);
            }

            // commission
            if (get_mvx_older_global_settings('default_commission')) {
                $commission_data['default_commission'] = array( array('key' =>   'fixed_ammount', 'value'    =>  get_mvx_older_global_settings('default_commission')), array('key' =>   'percent_amount', 'value'    =>  get_mvx_older_global_settings('default_commission')) );
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }

            if (get_mvx_older_global_settings('revenue_sharing_mode')) {
                $commission_data['revenue_sharing_mode'] = get_mvx_older_global_settings('revenue_sharing_mode');
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }
            if (get_mvx_older_global_settings('commission_type')) {

                $commission_type_selection = [];
                $options_type = array(
                    array(
                        'key'=> "fixed",
                        'label'=> __('Fixed Amount', 'multivendorx'),
                        'value'=> __('fixed', 'multivendorx'),
                    ),
                    array(
                        'key'=> "percent",
                        'label'=> __('Percentage', 'multivendorx'),
                        'value'=> __('percent', 'multivendorx'),
                    ),
                    array(
                        'key'=> "fixed_with_percentage",
                        'label'=> __('%age + Fixed (per transaction)', 'multivendorx'),
                        'value'=> __('fixed_with_percentage', 'multivendorx'),
                    ),
                    array(
                        'key'=> "fixed_with_percentage_qty",
                        'label'=> __('%age + Fixed (per unit)', 'multivendorx'),
                        'value'=> __('fixed_with_percentage_qty', 'multivendorx'),
                    ),
                    array(
                        'key'=> "commission_by_product_price",
                        'label'=> __('Commission By Product Price', 'multivendorx'),
                        'value'=> __('commission_by_product_price', 'multivendorx'),
                    ),
                    array(
                        'key'=> "commission_by_purchase_quantity",
                        'label'=> __('Commission By Purchase Quantity', 'multivendorx'),
                        'value'=> __('commission_by_purchase_quantity', 'multivendorx'),
                    ),
                    array(
                        'key'=> "fixed_with_percentage_per_vendor",
                        'label'=> __('%age + Fixed (per vendor)', 'multivendorx'),
                        'value'=> __('fixed_with_percentage_per_vendor', 'multivendorx'),
                    )
                );
                foreach ($options_type as $key_type => $value_type) {
                    if (get_mvx_older_global_settings('commission_type') == $value_type['value']) {
                        $commission_type_selection = array(
                            
                            'label' => $value_type['label'],
                            'value' => $value_type['key'],
                            'index' => $key_type
                        );
                    }
                }

                $commission_data['commission_type'] = $commission_type_selection;
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }

            if (get_mvx_older_global_settings('payment_method_paypal_masspay')) {
                $commission_data['payment_method_disbursement'] = array('payment_method_paypal_masspay');
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }
            if (get_mvx_older_global_settings('payment_method_stripe_masspay')) {
                $commission_data['payment_method_disbursement'] = array('payment_method_stripe_masspay');
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }
            if (get_mvx_older_global_settings('payment_method_direct_bank')) {
                $commission_data['payment_method_disbursement'] = array('payment_method_direct_bank');
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }
            if (get_mvx_older_global_settings('payment_method_paypal_payout')) {
                $commission_data['payment_method_disbursement'] = array('payment_method_paypal_payout');
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }

            // bear the gateway charges
            if (get_mvx_older_global_settings('gateway_charges_cost_carrier') && get_mvx_older_global_settings('gateway_charges_cost_carrier') == 'vendor') {
                $commission_data['gateway_charges_cost_carrier'] = array('label'=> __('Vendor', 'multivendorx'), 'value'=> 'vendor', 'index' => 0 );
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }
            if (get_mvx_older_global_settings('gateway_charges_cost_carrier') && get_mvx_older_global_settings('gateway_charges_cost_carrier') == 'admin') {
                $commission_data['gateway_charges_cost_carrier'] = array('label'=> __('Admin', 'multivendorx'), 'value'=> 'admin', 'index' => 1 );
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }
            if (get_mvx_older_global_settings('gateway_charges_cost_carrier') && get_mvx_older_global_settings('gateway_charges_cost_carrier') == 'separate') {
                $commission_data['gateway_charges_cost_carrier'] = array('label'=> __('Separately', 'multivendorx'), 'value'=> 'separate', 'index' => 2 );
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }

            // gateway charge type
            if (get_mvx_older_global_settings('payment_gateway_charge_type') && get_mvx_older_global_settings('payment_gateway_charge_type') == 'percent') {
                $commission_data['payment_gateway_charge_type'] = array('label'=> __('Percentage', 'multivendorx'), 'value'=> 'percent', 'index' => 0 );
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }
            if (get_mvx_older_global_settings('payment_gateway_charge_type') && get_mvx_older_global_settings('payment_gateway_charge_type') == 'fixed') {
                $commission_data['payment_gateway_charge_type'] = array('label'=> __('Fixed Amount', 'multivendorx'), 'value'=> 'fixed', 'index' => 1 );
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }
            if (get_mvx_older_global_settings('payment_gateway_charge_type') && get_mvx_older_global_settings('payment_gateway_charge_type') == 'fixed_with_percentage') {
                $commission_data['payment_gateway_charge_type'] = array('label'=> __('%age + Fixed', 'multivendorx'), 'value'=> 'fixed_with_percentage', 'index' => 2 );
                mvx_update_option('mvx_commissions_tab_settings', $commission_data);
            }


            // review
            if (get_mvx_older_global_settings('is_sellerreview')) {
                $review_data['is_sellerreview'] = array('is_sellerreview');
                mvx_update_option('mvx_review_management_tab_settings', $review_data);
            }
            if (get_mvx_older_global_settings('is_sellerreview_varified')) {
                $review_data['is_sellerreview_varified'] = array('is_sellerreview_varified');
                mvx_update_option('mvx_review_management_tab_settings', $review_data);
            }
            if (get_mvx_older_global_settings('product_review_sync')) {
                $review_data['product_review_sync'] = array('product_review_sync');
                mvx_update_option('mvx_review_management_tab_settings', $review_data);
            }

            $wcmp_review_options  = mvx_get_option( 'wcmp_review_settings_option', array() );
            $wcmp_review_categories = isset( $wcmp_review_options['review_categories'] ) ? $wcmp_review_options['review_categories'] : array();

            $review_options_data = mvx_get_option('mvx_review_management_tab_settings');
            if ($wcmp_review_categories) {
                $review_options_data['mvx_review_categories'] = $wcmp_review_categories;
                update_option('wcmp_review_settings_option', $review_options_data);
            }

            // refund
            if (get_mvx_older_global_settings('refund_days')) {
                $refund_data['refund_days'] = get_mvx_older_global_settings('refund_days');
                mvx_update_option('mvx_refund_management_tab_settings', $refund_data);
            }

            if (get_mvx_older_global_settings('refund_order_msg')) {
                $refund_data['refund_order_msg'] = get_mvx_older_global_settings('refund_order_msg');
                mvx_update_option('mvx_refund_management_tab_settings', $refund_data);
            }

            if (get_mvx_older_global_settings('refund_method_pending')) {
                $refund_data['customer_refund_status'] = array('pending');
                mvx_update_option('mvx_refund_management_tab_settings', $refund_data);
            }
            if (get_mvx_older_global_settings('refund_method_on-hold')) {
                $refund_data['customer_refund_status'] = array('on-hold');
                mvx_update_option('mvx_refund_management_tab_settings', $refund_data);
            }
            if (get_mvx_older_global_settings('refund_method_processing')) {
                $refund_data['customer_refund_status'] = array('processing');
                mvx_update_option('mvx_refund_management_tab_settings', $refund_data);
            }
            if (get_mvx_older_global_settings('refund_method_completed')) {
                $refund_data['customer_refund_status'] = array('completed');
                mvx_update_option('mvx_refund_management_tab_settings', $refund_data);
            }

            // policy
            if (get_mvx_older_global_settings('shipping_policy')) {
                $policy_data['shipping_policy'] = get_mvx_older_global_settings('shipping_policy');
                mvx_update_option('mvx_refund_management_tab_settings', $policy_data);
            }
            if (get_mvx_older_global_settings('refund_policy')) {
                $policy_data['refund_policy'] = get_mvx_older_global_settings('refund_policy');
                mvx_update_option('mvx_refund_management_tab_settings', $policy_data);
            }
            if (get_mvx_older_global_settings('cancellation_policy')) {
                $policy_data['cancellation_policy'] = get_mvx_older_global_settings('cancellation_policy');
                mvx_update_option('mvx_refund_management_tab_settings', $policy_data);
            }

            // disbursement
            if (get_mvx_older_global_settings('commission_include_coupon')) {
                $disbursement_data['commission_include_coupon'] = array('commission_include_coupon');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('admin_coupon_excluded')) {
                $disbursement_data['admin_coupon_excluded'] = array('admin_coupon_excluded');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('give_tax')) {
                $disbursement_data['give_tax'] = array('give_tax');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('give_shipping')) {
                $disbursement_data['give_shipping'] = array('give_shipping');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('choose_payment_mode_automatic_disbursal')) {
                $disbursement_data['choose_payment_mode_automatic_disbursal'] = array('choose_payment_mode_automatic_disbursal');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('commission_threshold')) {
                $disbursement_data['commission_threshold'] = get_mvx_older_global_settings('commission_threshold');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('withdrawal_request')) {
                $disbursement_data['withdrawal_request'] = array('withdrawal_request');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('commission_threshold_time')) {
                $disbursement_data['commission_threshold_time'] = get_mvx_older_global_settings('commission_threshold_time');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('commission_transfer')) {
                $disbursement_data['commission_transfer'] = get_mvx_older_global_settings('commission_transfer');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('no_of_orders')) {
                $disbursement_data['no_of_orders'] = get_mvx_older_global_settings('no_of_orders');
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            // order withdrawl status
            if (get_mvx_older_global_settings('order_withdrawl_statuson-hold')) {
                $disbursement_data['order_withdrawl_status'] = array('value' => 'on-hold', 'label' => __('On hold', 'multivendorx'), 'index' => 0);
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('order_withdrawl_statusprocessing')) {
                $disbursement_data['order_withdrawl_status'] = array('value' => 'processing', 'label' => __('Processing', 'multivendorx'), 'index' => 1);
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }
            if (get_mvx_older_global_settings('order_withdrawl_statuscompleted')) {
                $disbursement_data['order_withdrawl_status'] = array('value' => 'completed', 'label' => __('Completed', 'multivendorx'), 'index' => 2);
                mvx_update_option('mvx_disbursement_tab_settings', $disbursement_data);
            }

            // payments paypal masspay
            if (get_mvx_older_global_settings('api_username')) {
                $payemnts_masspay_data['api_username'] = get_mvx_older_global_settings('api_username');
                mvx_update_option('mvx_payment_masspay_tab_settings', $payemnts_masspay_data);
            }
            if (get_mvx_older_global_settings('api_pass')) {
                $payemnts_masspay_data['api_pass'] = get_mvx_older_global_settings('api_pass');
                mvx_update_option('mvx_payment_masspay_tab_settings', $payemnts_masspay_data);
            }
            if (get_mvx_older_global_settings('api_signature')) {
                $payemnts_masspay_data['api_signature'] = get_mvx_older_global_settings('api_signature');
                mvx_update_option('mvx_payment_masspay_tab_settings', $payemnts_masspay_data);
            }
            if (get_mvx_older_global_settings('is_testmode')) {
                $payemnts_masspay_data['is_testmode'] = array('is_testmode');
                mvx_update_option('mvx_payment_masspay_tab_settings', $payemnts_masspay_data);
            }

            // payments paypal payout
            if (get_mvx_older_global_settings('client_id')) {
                $payemnts_payout_data['client_id'] = get_mvx_older_global_settings('client_id');
                mvx_update_option('mvx_payment_payout_tab_settings', $payemnts_payout_data);
            }
            if (get_mvx_older_global_settings('client_secret')) {
                $payemnts_payout_data['client_secret'] = get_mvx_older_global_settings('client_secret');
                mvx_update_option('mvx_payment_payout_tab_settings', $payemnts_payout_data);
            }
            if (get_mvx_older_global_settings('is_asynchronousmode')) {
                $payemnts_payout_data['is_asynchronousmode'] = array('is_asynchronousmode');
                mvx_update_option('mvx_payment_payout_tab_settings', $payemnts_payout_data);
            }
            if (get_mvx_older_global_settings('is_testmode')) {
                $payemnts_payout_data['is_testmode'] = array('is_testmode');
                mvx_update_option('mvx_payment_payout_tab_settings', $payemnts_payout_data);
            }

            // payments stripe
            if (get_mvx_older_global_settings('testmode')) {
                $payemnts_stripe_data['testmode'] = array('testmode');
                mvx_update_option('mvx_payment_stripe_connect_tab_settings', $payemnts_stripe_data);
            }
            if (get_mvx_older_global_settings('test_client_id')) {
                $payemnts_stripe_data['test_client_id'] = get_mvx_older_global_settings('test_client_id');
                mvx_update_option('mvx_payment_stripe_connect_tab_settings', $payemnts_stripe_data);
            }
            if (get_mvx_older_global_settings('live_client_id')) {
                $payemnts_stripe_data['live_client_id'] = get_mvx_older_global_settings('live_client_id');
                mvx_update_option('mvx_payment_stripe_connect_tab_settings', $payemnts_stripe_data);
            }
            if (get_mvx_older_global_settings('test_publishable_key')) {
                $payemnts_stripe_data['test_publishable_key'] = get_mvx_older_global_settings('test_publishable_key');
                mvx_update_option('mvx_payment_stripe_connect_tab_settings', $payemnts_stripe_data);
            }
            if (get_mvx_older_global_settings('live_publishable_key')) {
                $payemnts_stripe_data['live_publishable_key'] = get_mvx_older_global_settings('live_publishable_key');
                mvx_update_option('mvx_payment_stripe_connect_tab_settings', $payemnts_stripe_data);
            }
            if (get_mvx_older_global_settings('test_secret_key')) {
                $payemnts_stripe_data['test_secret_key'] = get_mvx_older_global_settings('test_secret_key');
                mvx_update_option('mvx_payment_stripe_connect_tab_settings', $payemnts_stripe_data);
            }
            if (get_mvx_older_global_settings('live_secret_key')) {
                $payemnts_stripe_data['live_secret_key'] = get_mvx_older_global_settings('live_secret_key');
                mvx_update_option('mvx_payment_stripe_connect_tab_settings', $payemnts_stripe_data);
            }

            // social
            if (get_mvx_older_global_settings('profile_sync')) {
                $social_data['profile_sync'] = array('profile_sync');
                mvx_update_option('mvx_social_tab_settings', $social_data);
            }
            // updare data on modules
            $active_module_list = mvx_get_option('mvx_all_active_module_list') ? mvx_get_option('mvx_all_active_module_list') : array();
            if (get_mvx_older_global_settings('payment_method_paypal_masspay')) {
                array_push($active_module_list, 'paypal-masspay');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('payment_method_stripe_masspay')) {
                array_push($active_module_list, 'stripe-connect');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('payment_method_direct_bank')) {
                array_push($active_module_list, 'bank-payment');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('payment_method_paypal_payout')) {
                array_push($active_module_list, 'paypal-payout');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('is_vendor_shipping_on')) {
                array_push($active_module_list, 'vendor-shipping');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('is_vendor_shipping_on')) {
                array_push($active_module_list, 'zone-shipping');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('enabled_distance_by_shipping_for_vendor')) {
                array_push($active_module_list, 'distance-shipping');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('enabled_shipping_by_country_for_vendor')) {
                array_push($active_module_list, 'country-shipping');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('store_follow_enabled')) {
                array_push($active_module_list, 'follow-store');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('is_sellerreview')) {
                array_push($active_module_list, 'store-review');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            if (get_mvx_older_global_settings('profile_sync')) {
                array_push($active_module_list, 'buddypress');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            
            // refund module enabled
            array_push($active_module_list, 'marketplace-refund');
            mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            // Elementor enabled
            if (WC_Dependencies_Product_Vendor::elementor_pro_active_check()) {
                array_push($active_module_list, 'elementor');
                mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
            }
            // product types add
            array_push($active_module_list, 'simple', 'external', 'grouped');
            mvx_update_option( 'mvx_all_active_module_list', $active_module_list );

            // registration form migrate
            $mvx_vendor_registration_form_data = mvx_get_option( 'wcmp_vendor_registration_form_data' );
            if ($mvx_vendor_registration_form_data) {
                mvx_update_option('mvx_new_vendor_registration_form_data', $mvx_vendor_registration_form_data);
            }

            update_option('_is_dismiss_mvx40_notice', true);
        }
        wp_clear_scheduled_hook('mvx_older_settings_migrated_migration');
    }

    function update_users_collection_using_cron() {
        global $MVX;
        $allow_tracking = get_option( 'mvx_plugin_action_block_notice' );
        if ( $allow_tracking && $allow_tracking == 'yes' ) {
            $body = $MVX->mvx_usage_tracker->get_data();
            $MVX->mvx_usage_tracker->send_data( $body );
        }
    }
}
