<?php
if (!function_exists('get_mvx_vendor_settings')) {

    /**
     * get plugin settings
     * @return array
     */
    function get_mvx_vendor_settings($key = '', $tab = '', $default = false) {
        if (empty($key) && empty($tab)) {
            return $default;
        }
        if (empty($tab)) {
            return get_mvx_global_settings($key, $default);
        }
        if (empty($key)) {
            return mvx_get_option("mvx_{$tab}_tab_settings", $default);
        }
        if (!empty($key) && !empty($tab)) {
            $settings = mvx_get_option("mvx_{$tab}_tab_settings", $default);
        }
        if (!isset($settings[$key]) || empty($settings[$key])) {
            return $default;
        }
        return $settings[$key];
    }

}

if (!function_exists('get_mvx_global_settings')) {

    function get_mvx_global_settings($key = '', $default = false) {
        $options = array();
        $all_options = apply_filters('mvx_all_admin_options', array(
            'mvx_settings_general_tab_settings',
            'mvx_social_tab_settings',
            'mvx_vendor_registration_form_data',
            'mvx_seller_dashbaord_tab_settings',
            'mvx_store_tab_settings',
            'mvx_products_tab_settings',
            'mvx_products_capability_tab_settings',
            'mvx_spmv_pages_tab_settings',
            'mvx_commissions_tab_settings',
            'mvx_disbursement_tab_settings',
            'mvx_policy_tab_settings',
            'mvx_refund_management_tab_settings',
            'mvx_review_management_tab_settings',
            'mvx_payment_masspay_tab_settings',
            'mvx_payment_payout_tab_settings',
            'mvx_payment_stripe_connect_tab_settings',
                )
        );
        foreach ($all_options as $option_name) {
            if (is_array(mvx_get_option($option_name, array()))) {
                $options = array_merge($options, mvx_get_option($option_name, array()));
            }
        }
        if (empty($key)) {
            return $default;
        }
        if (!isset($options[$key]) || empty($options[$key])) {
            return $default;
        }
        return $options[$key];
    }

}

if (!function_exists('get_mvx_older_global_settings')) {

    function get_mvx_older_global_settings($name = '', $default = false) {
        $options = array();
        $all_options = apply_filters('wcmp_all_admin_options', array(
            'wcmp_general_settings_name',
            'wcmp_product_settings_name',
            'wcmp_capabilities_settings_name',
            'wcmp_payment_settings_name',
            'wcmp_general_policies_settings_name',
            'wcmp_general_customer_support_details_settings_name',
            'wcmp_vendor_general_settings_name',
            'wcmp_capabilities_product_settings_name',
            'wcmp_capabilities_order_settings_name',
            'wcmp_capabilities_miscellaneous_settings_name',
            'wcmp_payment_paypal_payout_settings_name',
            'wcmp_payment_paypal_masspay_settings_name',
            'wcmp_vendor_dashboard_settings_name'
                )
        );
        foreach ($all_options as $option_name) {
            $options = array_merge($options, mvx_get_option($option_name, array()));
        }
        if (empty($name)) {
            return $options;
        }
        if (!isset($options[$name]) || empty($options[$name])) {
            return $default;
        }
        return $options[$name];
    }

}

if (!function_exists('update_mvx_vendor_settings')) {

    function update_mvx_vendor_settings($key = '', $value = '', $tab = '') {
        if (empty($key) || empty($value) || empty($tab)) {
            return;
        }
        $settings = [];
        if (!empty($tab)) {
            $option_name = "mvx_{$tab}_tab_settings";
            $settings = mvx_get_option("mvx_{$tab}_tab_settings");
        }
        if (is_array($settings)) {
            $settings[$key] = $value;
            mvx_update_option($option_name, $settings);
        }
    }

}

if (!function_exists('delete_mvx_vendor_settings')) {

    function delete_mvx_vendor_settings($name = '', $tab = '', $subtab = '') {
        if (empty($name)) {
            return;
        }
        if (!empty($subtab)) {
            $option_name = "mvx_{$tab}_{$subtab}_settings_name";
            $settings = mvx_get_option("mvx_{$tab}_{$subtab}_settings_name");
        } else {
            $option_name = "mvx_{$tab}_settings_name";
            $settings = mvx_get_option("mvx_{$tab}_settings_name");
        }
        if ($settings && isset($settings[$name])) {
            unset($settings[$name]);
            mvx_update_option($option_name, $settings);
        }
    }

}

if (!function_exists('mvx_get_settings_value')) {

    /**
     * get settings value by key
     * @return string
     */
    function mvx_get_settings_value($key = array(), $default = 'false') {
        if (empty($key)) {
            return $default;
        }
        if (is_array($key) && isset($key['value'])) {
            return $key['value'];
        }
        return $default;
    }

}

if (!function_exists('is_user_mvx_pending_vendor')) {

    /**
     * Check if user is pending vendor
     * @param userid or WP_User object
     * @return boolean
     */
    function is_user_mvx_pending_vendor($user) {
        if ($user && !empty($user)) {
            if (!is_object($user)) {
                $user = new WP_User(absint($user));
            }
            return ( is_array($user->roles) && in_array('dc_pending_vendor', $user->roles) );
        } else {
            return false;
        }
    }

}

if (!function_exists('is_user_mvx_rejected_vendor')) {

    /**
     * Check if user is vendor
     * @param userid or WP_User object
     * @return boolean
     */
    function is_user_mvx_rejected_vendor($user) {
        if ($user && !empty($user)) {
            if (!is_object($user)) {
                $user = new WP_User(absint($user));
            }
            return ( is_array($user->roles) && in_array('dc_rejected_vendor', $user->roles) );
        } else {
            return false;
        }
    }

}

if (!function_exists('is_user_mvx_vendor')) {

    /**
     * Check if user is vendor
     * @param userid or WP_User object
     * @return boolean
     */
    function is_user_mvx_vendor($user) {
        if ($user && !empty($user)) {
            if (!is_object($user)) {
                $user = new WP_User(absint($user));
            }
            return apply_filters('is_user_mvx_vendor', ( is_array($user->roles) && in_array('dc_vendor', $user->roles)), $user);
        } else {
            return false;
        }
    }

}

if (!function_exists('get_mvx_vendors')) {

    /**
     * Get all vendors
     * @param args Array of args
     * @param return type `object`/`id'
     * @return arr Array of ids/vendors
     */
    function get_mvx_vendors($args = array(), $return = 'object') {
        $vendors_array = array();
        $args = wp_parse_args($args, array('role' => 'dc_vendor', 'fields' => 'ids', 'orderby' => 'registered', 'order' => 'ASC'));
        $user_query = new WP_User_Query($args);
        if( $return === 'object' ){
            if (!empty($user_query->results)) {
                foreach ($user_query->results as $vendor_id) {
                    $vendors_array[] = get_mvx_vendor($vendor_id);
                }
            }
        }else{
            $vendors_array = $user_query->results;
        }
        
        return apply_filters('get_mvx_vendors', $vendors_array, $return);
    }

}

if (!function_exists('mvx_get_default_commission_amount')) {
    function mvx_get_default_commission_amount() {
        global $MVX;
        $commission_amount = array();
        $commission_type = mvx_get_settings_value($MVX->vendor_caps->payment_cap['commission_type']);
        $default_commission_settings = get_mvx_global_settings('default_commission');
        if (is_array($default_commission_settings)) {
            switch ($commission_type) {
                case "fixed":
                case "percent":
                    $commission_amount = array('default_commission' => $default_commission_settings[0]['value'] );
                break;
                case "fixed_with_percentage":
                case "fixed_with_percentage_qty":
                    foreach ($default_commission_settings as $value) {
                        if (isset($value['key'] ) && isset($value['value'] )) {
                            $commission_amount[$value['key']] = $value['value'];
                        }
                    }
                break;
                default:
                $commission_amount = array();
            }
        }
        return $commission_amount;
    }
}

if (!function_exists('mvx_is_product_type_avaliable')) {

    /**
     * Check product type is avaliable
     * @return bool
     */
    function mvx_is_product_type_avaliable($type = '') {
        if ($type && !empty($type)) {
            $product_types = is_array(mvx_active_product_types()) ? mvx_active_product_types() : array();
            $type_option = get_mvx_global_settings('type_options', array());
            $mvx_product_types = array_merge($product_types, $type_option);
            if (is_array($mvx_product_types) && in_array($type, $mvx_product_types)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}

if (!function_exists('get_mvx_vendor')) {

    /**
     * Get individual vendor info by ID
     * @param  int $vendor_id ID of vendor
     * @return obj            Vendor object
     */
    function get_mvx_vendor($vendor_id = 0) {
        $vendor = false;
        $vendor_id = $vendor_id ? $vendor_id : get_current_vendor_id();
        if (is_user_mvx_vendor($vendor_id)) {
            if( !class_exists( 'MVX_Vendor' ) ) {
                global $MVX;
                include_once ( $MVX->plugin_path . "/classes/class-mvx-vendor-details.php" );
            }
            $vendor = new MVX_Vendor(absint($vendor_id));
        }
        return $vendor;
    }

}

if (!function_exists('get_mvx_vendor_by_term')) {

    /**
     * Get individual vendor info by term id
     * @param $term_id ID of term
     */
    function get_mvx_vendor_by_term($term_id) {
        $vendor = false;
        if (!empty($term_id)) {
            $user_id = get_term_meta($term_id, '_vendor_user_id', true);
            if (is_user_mvx_vendor($user_id)) {
                $vendor = get_mvx_vendor($user_id);
            }
        }
        return $vendor;
    }

}

if (!function_exists('get_mvx_product_vendors')) {

    /**
     * Get vendors for product
     * @param  int $product_id Product ID
     * @return arr             Array of product vendors
     */
    function get_mvx_product_vendors($product_id = 0) {
        global $MVX;
        $vendor_data = false;
        if ($product_id > 0) {
            $vendors_data = wp_get_post_terms($product_id, $MVX->taxonomy->taxonomy_name);
            foreach ($vendors_data as $vendor) {
                $vendor_obj = get_mvx_vendor_by_term($vendor->term_id);
                if ($vendor_obj) {
                    $vendor_data = $vendor_obj;
                }
            }
            if (!$vendor_data) {
                $product_obj = get_post($product_id);
                if (is_object($product_obj)) {
                    $author_id = $product_obj->post_author;
                    if ($author_id) {
                        $vendor_data = get_mvx_vendor($author_id);
                    }
                }
            }
        }
        return $vendor_data;
    }

}

if (!function_exists('doProductVendorLOG')) {

    /**
     * Write to log file
     */
    function doProductVendorLOG($str) {
        global $MVX;
        $file = $MVX->plugin_path . 'log/product_vendor.log';
        if (file_exists($file)) {
            // Open the file to get existing content
            $current = file_get_contents($file);
            if ($current) {
                // Append a new content to the file
                $current .= "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            } else {
                $current = "$str" . "\r\n";
                $current .= "-------------------------------------\r\n";
            }
            // Write the contents back to the file
            file_put_contents($file, $current);
        }
    }

}

if (!function_exists('is_vendor_dashboard')) {

    /**
     * check if vendor dashboard page
     * @return boolean
     */
    function is_vendor_dashboard() {
        $is_vendor_dashboard = false;
        if ( function_exists('icl_object_id') ) {
            return is_page( icl_object_id( mvx_vendor_dashboard_page_id(), 'page', true ) );
        } else {
            return is_page(mvx_vendor_dashboard_page_id());
        }
        return apply_filters('mvx_is_vendor_dashboard', $is_vendor_dashboard);
    }

}

if (!function_exists('mvx_vendor_dashboard_page_id')) {

    /**
     * Get vendor dashboard page id
     * @return int
     */
    function mvx_vendor_dashboard_page_id($language_code = '', $url = false) {
        if (get_mvx_vendor_settings('vendor_dashboard_page', 'settings_general')) {
            $mvx_dashboard_data = get_mvx_vendor_settings('vendor_dashboard_page', 'settings_general');
            if (isset($mvx_dashboard_data['value']) && !empty($mvx_dashboard_data['value'])) {
                if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
                    if( !$language_code ) {
                        global $sitepress;
                        $language_code = $sitepress->get_current_language();
                    }
                    if( $language_code ) {
                        if( defined('DOING_AJAX') ) {
                            do_action( 'wpml_switch_language', $language_code );
                        }
                        if ($url) {
                            $mvx_page =  get_permalink(icl_object_id( $mvx_dashboard_data['value'], 'page', true, $language_code ));
                            $mvx_page = apply_filters( 'wpml_permalink', $mvx_page, $language_code );
                        } else {
                            $mvx_page =  icl_object_id( $mvx_dashboard_data['value'], 'page', true, $language_code );
                            $mvx_page = apply_filters( 'wpml_permalink', $mvx_page, $language_code );
                        }
                        return $mvx_page;
                    } else {
                        if ($url) {
                            return  get_permalink(icl_object_id( $mvx_dashboard_data['value'], 'page', true ));
                        } else {
                            return  icl_object_id( $mvx_dashboard_data['value'], 'page', true );
                        }
                    }
                } else {
                    if ($url) {
                        return get_permalink( (int) $mvx_dashboard_data['value'] );
                    } else {
                        return (int) $mvx_dashboard_data['value'];
                    }
                }
            }
        }
        return false;
    }
}

if (!function_exists('is_page_vendor_registration')) {

    /**
     * check if vendor registration page
     * @return boolean
     */
    function is_page_vendor_registration() {
        $is_vendor_registration = false;
        if (mvx_vendor_registration_page_id()) {
            $is_vendor_registration = is_page(mvx_vendor_registration_page_id()) ? true : false;
        }
        return apply_filters('mvx_is_vendor_registration', $is_vendor_registration);
    }

}

if (!function_exists('mvx_vendor_registration_page_id')) {

    /**
     * Get vendor Registration page id
     * @return type
     */
    function mvx_vendor_registration_page_id() {
        if (get_mvx_vendor_settings('registration_page', 'settings_general')) {
            $mvx_registration_page_data = get_mvx_vendor_settings('registration_page', 'settings_general');
            if (isset($mvx_registration_page_data['value']) && !empty($mvx_registration_page_data['value'])) {
                if (function_exists('icl_object_id')) {
                    return icl_object_id((int) $mvx_registration_page_data['value'], 'page', false, ICL_LANGUAGE_CODE);
                }
                return (int) $mvx_registration_page_data['value'];
            }
        }
        return false;
    }

}

if (!function_exists('get_vendor_from_an_order')) {

    /**
     * Get vendor from a order
     * @param WC_Order $order or order id
     * @return type
     */
    function get_vendor_from_an_order($order) {
        $vendors = array();
        if (!is_object($order)) {
            $order = new WC_Order($order);
        }
        $items = $order->get_items('line_item');
        foreach ($items as $item_id => $item) {
            $vendor_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
            if ($vendor_id) {
                $term_id = get_user_meta($vendor_id, '_vendor_term_id', true);
                if (!in_array($term_id, $vendors)) {
                    $vendors[] = $term_id;
                }
            } else {
                $product_id = wc_get_order_item_meta($item_id, '_product_id', true);
                if ($product_id) {
                    $product_vendors = get_mvx_product_vendors($product_id);
                    if ($product_vendors && !in_array($product_vendors->term_id, $vendors)) {
                        $vendors[] = $product_vendors->term_id;
                    }
                }
            }
        }
        return $vendors;
    }

}

if (!function_exists('mvx_action_links')) {

    /**
     * Product Vendor Action Links Function
     * @param plugin links
     * @return plugin links
     */
    function mvx_action_links($links) {
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=mvx#&submenu=dashboard') . '">' . __('Settings', 'multivendorx') . '</a>');
        return array_merge($plugin_links, $links);
    }

}


if (!function_exists('mvx_get_all_blocked_vendors')) {

    /**
     * mvx_get_all_blocked_vendors Function
     *
     * @access public
     * @return plugin array
     */
    function mvx_get_all_blocked_vendors() {
        $vendors = get_mvx_vendors();
        $blocked_vendor = array();
        if (!empty($vendors) && is_array($vendors)) {
            foreach ($vendors as $vendor_key => $vendor) {
                if (is_a($vendor, 'MVX_Vendor')) {
                    $is_block = get_user_meta($vendor->id, '_vendor_turn_off', true);
                    if ($is_block) {
                        $blocked_vendor[] = $vendor;
                    }
                }
            }
        }
        return $blocked_vendor;
    }

}

if (!function_exists('get_mvx_vendor_orders')) {

    function get_mvx_vendor_orders($args = array()) {
        global $wpdb;
        $query = '';
        if (isset($args['order_id'])) {
            if (is_object($args['order_id'])) {
                $args['order_id'] = $args['order_id']->get_id();
            }
        }
        if (isset($args['product_id'])) {
            if (is_object($args['product_id'])) {
                $args['product_id'] = $args['product_id']->get_id();
            }
        }
        if (isset($args['commission_id'])) {
            if (is_object($args['commission_id'])) {
                $args['commission_id'] = $args['commission_id']->ID;
            }
        }
        if (isset($args['vendor_id'])) {
            if (is_object($args['vendor_id'])) {
                $args['vendor_id'] = $args['vendor_id']->id;
            }
        }
        if (!empty($args)) {
            foreach ($args as $key => $arg) {
                if (!$wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `{$wpdb->prefix}mvx_vendor_orders` LIKE %s", $key  ))) {
                    unset($args[$key]);
                }
            }
            $query .= ' WHERE ';
            $query .= implode(' AND ', array_map(
                            function ($v, $k) {
                        return sprintf("%s = '%s'", $k, $v);
                    }, $args, array_keys($args)
            ));
            $query = apply_filters('get_mvx_vendor_orders_query_where', $query);
        }
        return $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}mvx_vendor_orders %s", $query));
    }

}

if (!function_exists('get_mvx_vendor_order_amount')) {

    /**
     * @since 2.6.6
     * @global object $MVX
     * @param int $vendor_id
     * @param array $args
     * @param bool $check_caps
     * @return array
     */
    function get_mvx_vendor_order_amount($args = array(), $vendor_id = false, $check_caps = true) {
        global $MVX;
        if ($vendor_id) {
            $args['vendor_id'] = $vendor_id;
        }
        if (isset($args['vendor_id'])) {
            $vendor_id = $args['vendor_id'];
        }
        if (!isset($args['is_trashed'])) {
            $args['is_trashed'] = '';
        }
        $vendor_orders_in_order = get_mvx_vendor_orders($args);

        if (!empty($vendor_orders_in_order)) {
            $shipping_amount = array_sum(wp_list_pluck($vendor_orders_in_order, 'shipping'));
            $tax_amount = array_sum(wp_list_pluck($vendor_orders_in_order, 'tax'));
            $shipping_tax_amount = array_sum(wp_list_pluck($vendor_orders_in_order, 'shipping_tax_amount'));
            $commission_amount = array_sum(wp_list_pluck($vendor_orders_in_order, 'commission_amount'));
            $total = $commission_amount + $shipping_amount + $tax_amount + $shipping_tax_amount;
        } else {
            $shipping_amount = 0;
            $tax_amount = 0;
            $shipping_tax_amount = 0;
            $commission_amount = 0;
            $total = 0;
        }
        if ($check_caps && $MVX && $vendor_id) {
            $amount = array(
                'commission_amount' => $commission_amount,
            );
            if ($MVX->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($vendor_id, '_vendor_give_shipping', true)) {
                $amount['shipping_amount'] = $shipping_amount;
            } else {
                $amount['shipping_amount'] = 0;
            }
            if ($MVX->vendor_caps->vendor_payment_settings('give_tax') && $MVX->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($vendor_id, '_vendor_give_shipping', true) && !get_user_meta($vendor_id, '_vendor_give_tax', true)) {
                $amount['tax_amount'] = $tax_amount;
                $amount['shipping_tax_amount'] = $shipping_tax_amount;
            } else if ($MVX->vendor_caps->vendor_payment_settings('give_tax') && !get_user_meta($vendor_id, '_vendor_give_tax', true)) {
                $amount['tax_amount'] = $tax_amount;
                $amount['shipping_tax_amount'] = 0;
            } else {
                $amount['tax_amount'] = 0;
                $amount['shipping_tax_amount'] = 0;
            }
            $amount['total'] = $amount['commission_amount'] + $amount['shipping_amount'] + $amount['tax_amount'] + $amount['shipping_tax_amount'];
            return $amount;
        } else {
            return array(
                'commission_amount' => $commission_amount,
                'shipping_amount' => $shipping_amount,
                'tax_amount' => $tax_amount,
                'shipping_tax_amount' => $shipping_tax_amount,
                'total' => $total
            );
        }
    }

}

if (!function_exists('activate_mvx_plugin')) {

    /**
     * On activation, include the installer and run it.
     *
     * @access public
     * @return void
     */
    function activate_mvx_plugin() {
        //if (!mvx_get_option('dc_product_vendor_plugin_installed')) {
        require_once( 'class-mvx-install.php' );
        new MVX_Install();
        mvx_update_option('dc_product_vendor_plugin_installed', 1);
        //}
    }

}

if (!function_exists('deactivate_mvx_plugin')) {

    /**
     * On deactivation delete page install option
     */
    function deactivate_mvx_plugin() {
        delete_option('dc_product_vendor_plugin_page_install');
        delete_option('mvx_flushed_rewrite_rules');
    }

}

if (!function_exists('mvx_check_if_another_vendor_plugin_exits')) {

    /**
     * On activation, check if another vendor plugin installed.
     *
     * @access public
     * @return void
     */
    function mvx_check_if_another_vendor_plugin_exits() {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        // deactivate marketplace stripe gateway
        if (version_compare(mvx_get_option('dc_product_vendor_plugin_db_version'), '3.1.0', '<')) {
            
        } else {
            if (is_plugin_active('marketplace-stripe-gateway/marketplace-stripe-gateway.php')) {
                deactivate_plugins('marketplace-stripe-gateway/marketplace-stripe-gateway.php');
            }
        }
        $vendor_arr = array();
        $vendor_arr[] = 'dokan-lite/dokan.php';
        $vendor_arr[] = 'wc-vendors/class-wc-vendors.php';
        $vendor_arr[] = 'yith-woocommerce-product-vendors/init.php';
        foreach ($vendor_arr as $plugin) {
            if (is_plugin_active($plugin)) {
                // deactivate_plugins('dc-woocommerce-multi-vendor/dc_product_vendor.php');
                // exit(__('Another Multivendor Plugin is allready Activated Please deactivate first to install this plugin', 'multivendorx'));
            }
        }
    }

}

if (!function_exists('mvx_paid_commission_status')) {

    function mvx_paid_commission_status($commission_id) {
        global $wpdb;
        update_post_meta($commission_id, '_paid_status', 'paid', 'unpaid');
        update_post_meta($commission_id, '_paid_date', time());
        $wpdb->query( $wpdb->prepare( "UPDATE `{$wpdb->prefix}mvx_vendor_orders` SET commission_status = 'paid', commission_paid_date = now() WHERE commission_id = %d", $commission_id ) );
    }

}

if (!function_exists('mvx_rangeWeek')) {

    /**
     * Calculate start date and end date of a week
     * @param date $datestr
     * @return array
     */
    function mvx_rangeWeek($datestr) {
        date_default_timezone_set(date_default_timezone_get());
        $dt = strtotime($datestr);
        $res['start'] = date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
        $res['end'] = date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));
        return $res;
    }

}

if (!function_exists('mvx_seller_review_enable')) {

    /**
     * Check if vendor review enable or not
     * @param type $vendor_term_id
     * @return type
     */
    function mvx_seller_review_enable($vendor_term_id) {
        $is_enable = false;
        $current_user = wp_get_current_user();
        if ($current_user->ID > 0) {
            if (mvx_is_module_active('store-review') && get_mvx_vendor_settings('is_sellerreview', 'review_management')) {
                if (get_mvx_vendor_settings('is_sellerreview_varified', 'review_management')) {
                    $is_enable = mvx_find_user_purchased_with_vendor($current_user->ID, $vendor_term_id);
                } else {
                    $is_enable = true;
                }
            }
        }
        return apply_filters('mvx_seller_review_enable', $is_enable);
    }

}

if (!function_exists('mvx_find_user_purchased_with_vendor')) {

    /**
     * Check if a user purchase product from given vendor or not
     * @param type $user_id
     * @param type $vendor_term_id
     * @return boolean
     */
    function mvx_find_user_purchased_with_vendor($user_id, $vendor_term_id) {
        $is_purchased_with_vendor = false;
        $order_lits = mvx_get_all_order_of_user($user_id);
        foreach ($order_lits as $order) {
            $vendors = get_vendor_from_an_order($order->ID);
            if (!empty($vendors) && is_array($vendors)) {
                if (in_array($vendor_term_id, $vendors)) {
                    $is_purchased_with_vendor = true;
                    break;
                }
            }
        }
        return $is_purchased_with_vendor;
    }

}

if (!function_exists('mvx_get_vendor_dashboard_nav_item_css_class')) {

    function mvx_get_vendor_dashboard_nav_item_css_class($endpoint, $force_active = false) {
        global $wp;
        $cssClass = array(
            'nav-link',
            'mvx-venrod-dashboard-nav-link',
            'mvx-venrod-dashboard-nav-link--' . $endpoint
        );
        $current = isset($wp->query_vars[$endpoint]);
        if ('dashboard' === $endpoint && ( isset($wp->query_vars['page']) || empty($wp->query_vars) )) {
            $current = true; // Dashboard is not an endpoint, so needs a custom check.
        }
        if ($current || $force_active) {
            $cssClass[] = 'active';
        }
        $cssClass = apply_filters('mvx_vendor_dashboard_nav_item_css_class', $cssClass, $endpoint);
        return $cssClass;
    }

}

if (!function_exists('mvx_get_vendor_dashboard_endpoint_url')) {
    function mvx_get_vendor_dashboard_endpoint_url($endpoint, $value = '', $withvalue = false, $lang_code = '') {
        global $wp;
        $permalink =  mvx_vendor_dashboard_page_id($lang_code, true);
        if (empty($value)) {
            $value = isset($wp->query_vars[$endpoint]) && !empty($wp->query_vars[$endpoint]) && $withvalue ? $wp->query_vars[$endpoint] : '';
        }
        if (get_option('permalink_structure')) {
            if (strstr($permalink, '?')) {
                $query_string = '?' . parse_url($permalink, PHP_URL_QUERY);
                $permalink = current(explode('?', $permalink));
            } else {
                $query_string = '';
            }
            if ($endpoint == 'dashboard') {
                $url = trailingslashit($permalink) . $query_string;
            } else {
                $endpoint = defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ? apply_filters( 'wpml_translate_single_string', $endpoint, 'MVX', $endpoint, $lang_code ) : $endpoint;
                $url = trailingslashit($permalink) . $endpoint . '/' . $value . $query_string;
            }
        } else {
            if ($endpoint == 'dashboard') {
                $url = $permalink;
            } else {
                $endpoint = defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ? apply_filters( 'wpml_translate_single_string', $endpoint, 'MVX', $endpoint, $lang_code ) : $endpoint;
                $url = add_query_arg($endpoint, $value, $permalink);
            }
        }

        return apply_filters('mvx_get_vendor_dashboard_endpoint_url', $url, $endpoint, $value, $permalink);
    }
}

if (!function_exists('is_mvx_endpoint_url')) {

    /**
     * is_wc_endpoint_url - Check if an endpoint is showing.
     * @param  string $endpoint
     * @return bool
     */
    function is_mvx_endpoint_url($endpoint = false) {
        global $wp, $MVX;
        $mvx_endpoints = $MVX->endpoints->mvx_query_vars;

        if ($endpoint !== false) {
            if (!isset($mvx_endpoints[$endpoint])) {
                return false;
            } else {
                $endpoint_var = $mvx_endpoints[$endpoint];
            }

            return isset($wp->query_vars[$endpoint_var['endpoint']]);
        } else {
            foreach ($mvx_endpoints as $key => $value) {
                if (isset($wp->query_vars[$key])) {
                    return true;
                }
            }

            return false;
        }
    }

}

if (!function_exists('mvx_get_all_order_of_user')) {

    /**
     * Get all order of a customer
     * @param int $user_id
     * @return array
     */
    function mvx_get_all_order_of_user($user_id) {
        $order_lits = array();
        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'meta_value' => $user_id,
            'post_type' => wc_get_order_types(),
            'post_status' => array_keys(wc_get_order_statuses()),
        ));
        if (is_array($customer_orders) && count($customer_orders) > 0) {
            $order_lits = $customer_orders;
        }
        return $order_lits;
    }

}

if (!function_exists('mvx_review_is_from_verified_owner')) {

    /**
     * Check if given comment from verified customer or not
     * @param object $comment
     * @param int $vendor_term_id
     * @return boolean
     */
    function mvx_review_is_from_verified_owner($comment, $vendor_term_id) {
        $user_id = $comment->user_id;
        return mvx_find_user_purchased_with_vendor($user_id, $vendor_term_id);
    }

}

if (!function_exists('mvx_get_vendor_review_info')) {

    /**
     * Get vendor review information
     * @global type $wpdb
     * @param type $vendor_term_id
     * @param type $type values vendor-rating/product-rating
     * @return type
     */
    function mvx_get_vendor_review_info($vendor_term_id, $type = 'vendor-rating' ) {
        global $wpdb;
        $default_rating = apply_filters( 'mvx_vendor_review_rating_info_default_type', $type, $vendor_term_id );
        $rating_result_array = array(
            'total_rating' => 0,
            'avg_rating' => 0,
            'rating_type' => $default_rating,
        );

        if ( $default_rating === 'product-rating' ) {
            $vendor = get_mvx_vendor_by_term( $vendor_term_id );
            $vendor_products = wp_list_pluck( $vendor->get_products_ids(), 'ID' );
            $rating = $rating_pro_count = 0;
            if( $vendor_products ) {
                foreach( $vendor_products as $product_id ) {
                    if( get_post_meta( $product_id, '_wc_average_rating', true ) ) {
                        $rating += get_post_meta( $product_id, '_wc_average_rating', true );
                        $rating_pro_count++;
                    };
                }
            }
            if( $rating_pro_count ) {
                $rating_result_array['total_rating'] = $rating_pro_count;
                $rating_result_array['avg_rating'] = $rating / $rating_pro_count;
            }
        } else {
            $args_default = array(
                'status' => 'approve',
                'type' => 'mvx_vendor_rating',
                'meta_key' => 'vendor_rating_id',
                'meta_value' => get_mvx_vendor_by_term($vendor_term_id)->id,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'vendor_rating_id',
                        'value' => get_mvx_vendor_by_term($vendor_term_id)->id
                    ),
                    array(
                        'key' => 'vendor_rating',
                        'value' => '',
                        'compare' => '!='
                    )
                )
            );
            $args = apply_filters('mvx_vendor_review_rating_args_to_fetch', $args_default);
            $rating = $product_rating = 0;
            $comments = get_comments($args);
            // If product review sync enabled
            if (get_mvx_vendor_settings('product_review_sync', 'review_management')) {
                $vendor = get_mvx_vendor_by_term( $vendor_term_id );
                $args_default_for_product = apply_filters('mvx_vendors_product_review_info_args', array(
                    'status' => 'approve',
                    'type' => 'review',
                    'post__in' => wp_list_pluck($vendor->get_products_ids(), 'ID' ),
                    'author__not_in' => array($vendor->id)
                ) );
        $product_review_count = !empty($vendor->get_products_ids()) ? get_comments($args_default_for_product) : array();
                if (!empty($product_review_count)) {
                    $comments = array_merge(get_comments($args), $product_review_count);
                }
            }
            if ($comments && count($comments) > 0) {
                foreach ($comments as $comment) {
                    $rating += floatval(get_comment_meta($comment->comment_ID, 'vendor_rating', true));
            if (get_mvx_vendor_settings('product_review_sync', 'review_management')) {
                        $product_rating += floatval(get_comment_meta($comment->comment_ID, 'rating', true));
                    }
                }
        $rating = $rating + $product_rating;
                $rating_result_array['total_rating'] = count($comments);
                $rating_result_array['avg_rating'] = $rating / count($comments);
            }
        }

        return $rating_result_array;
    }

}

if (!function_exists('mvx_sort_by_rating_multiple_product')) {

    /**
     * Sort product by products ratings
     * @param type $more_product_array
     * @return type
     */
    function mvx_sort_by_rating_multiple_product($more_product_array) {
        $more_product_array2 = array();
        $j = 0;
        foreach ($more_product_array as $more_product) {

            if ($j == 0) {
                $more_product_array2[] = $more_product;
            } elseif ($more_product['is_vendor'] == 0) {
                $more_product_array2[] = $more_product;
            } elseif ($more_product['rating_data']['avg_rating'] == 0) {
                $more_product_array2[] = $more_product;
            } elseif ($more_product['rating_data']['avg_rating'] > 0) {
                if (isset($more_product_array2[0]['rating_data']['avg_rating'])) {
                    $i = 0;
                    while ($more_product_array2[$i]['rating_data']['avg_rating'] >= $more_product['rating_data']['avg_rating']) {
                        $i++;
                    }
                    if ($i == 0) {
                        array_unshift($more_product_array2, $more_product);
                    } elseif ($i == (count($more_product_array2) - 1)) {
                        if (isset($more_product_array2[$i]['rating_data']['avg_rating']) && $more_product_array2[$i]['rating_data']['avg_rating'] <= $more_product['rating_data']['avg_rating']) {
                            $temp = $more_product_array2[$i];
                            $more_product_array2[$i] = $more_product;
                            array_push($more_product_array2, $temp);
                        } else {
                            array_push($more_product_array2, $more_product);
                        }
                    } else {
                        $array_1 = array_slice($more_product_array2, 0, $i);
                        $array_2 = array_slice($more_product_array2, $i);
                        array_push($array_1, $more_product);
                        $more_product_array2 = array_merge($array_1, $array_2);
                    }
                } else {
                    array_unshift($more_product_array2, $more_product);
                }
            }
            $j++;
        }
        return $more_product_array2;
    }

}

if (!function_exists('mvx_remove_comments_section_from_vendor_dashboard')) {

    /**
     * Remove comments from vendor dashbord
     */
    function mvx_remove_comments_section_from_vendor_dashboard() {

        if (is_vendor_dashboard()) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('div#comments,section#comments').remove();
                });
            </script>
            <?php

        }
    }

}

if (!function_exists('do_mvx_data_migrate')) {

    /**
     * Migrate Old MVX data
     * @param string $previous_plugin_version
     */
    function do_mvx_data_migrate($previous_plugin_version = '', $new_plugin_version = '') {
        global $MVX, $wpdb, $wp_roles;
        if ($previous_plugin_version) {
            if ($previous_plugin_version <= '2.6.0' && !mvx_get_option('mvx_database_upgrade')) {
                $old_pages = mvx_get_option('mvx_pages_settings_name');
                if (isset($old_pages['vendor_dashboard'])) {
                    wp_update_post(array('ID' => $old_pages['vendor_dashboard'], 'post_content' => '[mvx_vendor]'));
                    mvx_update_option('mvx_product_vendor_vendor_page_id', mvx_get_option('mvx_product_vendor_vendor_dashboard_page_id'));
                    $mvx_product_vendor_vendor_page_id = mvx_get_option('mvx_product_vendor_vendor_page_id');
                    update_mvx_vendor_settings('mvx_vendor', $mvx_product_vendor_vendor_page_id, 'vendor', 'general');
                }
                /* remove unwanted vendor caps */
                $args = array('role' => 'dc_vendor', 'fields' => 'ids', 'orderby' => 'registered', 'order' => 'ASC');
                $user_query = new WP_User_Query($args);
                if (!empty($user_query->results)) {
                    foreach ($user_query->results as $vendor_id) {
                        $user = new WP_User($vendor_id);
                        if ($user) {
                            if ($user->has_cap('edit_others_products')) {
                                $user->remove_cap('edit_others_products');
                            }
                            if ($user->has_cap('delete_others_products')) {
                                $user->remove_cap('delete_others_products');
                            }
                            if ($user->has_cap('edit_others_shop_coupons')) {
                                $user->remove_cap('edit_others_shop_coupons');
                            }
                            if ($user->has_cap('delete_others_shop_coupons')) {
                                $user->remove_cap('delete_others_shop_coupons');
                            }
                        }
                    }
                }
                #region settings tab general data migrate
                if (get_mvx_vendor_settings('is_singleproductmultiseller', 'general', 'singleproductmultiseller') && get_mvx_vendor_settings('is_singleproductmultiseller', 'general', 'singleproductmultiseller') == 'Enable') {
                    update_mvx_vendor_settings('is_singleproductmultiseller', 'Enable', 'general');
                }
                delete_mvx_vendor_settings('is_singleproductmultiseller', 'general', 'singleproductmultiseller');
                if (mvx_is_module_active('store-review') && get_mvx_vendor_settings('is_sellerreview', 'review_management')) {
                    update_mvx_vendor_settings('is_sellerreview', 'Enable', 'general');
                }
                delete_mvx_vendor_settings('is_sellerreview', 'general', 'sellerreview');
                if (get_mvx_vendor_settings('is_sellerreview_varified', 'general', 'sellerreview') == 'Enable' && get_mvx_vendor_settings('is_sellerreview_varified', 'general', 'sellerreview')) {
                    update_mvx_vendor_settings('is_sellerreview_varified', 'Enable', 'general');
                }
                delete_mvx_vendor_settings('is_sellerreview_varified', 'general', 'sellerreview');
                if (mvx_is_module_active('store-policy')) {
                    update_mvx_vendor_settings('is_policy_on', 'Enable', 'general');
                }
                delete_mvx_vendor_settings('is_policy_on', 'general', 'policies');
                if (get_mvx_vendor_settings('is_customer_support_details', 'general', 'customer_support_details') && get_mvx_vendor_settings('is_customer_support_details', 'general', 'customer_support_details') == 'Enable') {
                    update_mvx_vendor_settings('is_customer_support_details', 'Enable', 'general');
                }
                delete_mvx_vendor_settings('is_customer_support_details', 'general', 'customer_support_details');
                #endregion
                #region migrate other data
                if (get_mvx_vendor_settings('can_vendor_edit_policy_tab_label', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('can_vendor_edit_policy_tab_label', 'Enable', 'general', 'policies');
                }
                delete_mvx_vendor_settings('can_vendor_edit_policy_tab_label', 'capabilities');
                if (get_mvx_vendor_settings('can_vendor_edit_cancellation_policy', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('can_vendor_edit_cancellation_policy', 'Enable', 'general', 'policies');
                }
                delete_mvx_vendor_settings('can_vendor_edit_cancellation_policy', 'capabilities');
                if (get_mvx_vendor_settings('can_vendor_edit_refund_policy', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('can_vendor_edit_refund_policy', 'Enable', 'general', 'policies');
                }
                delete_mvx_vendor_settings('can_vendor_edit_refund_policy', 'capabilities');
                if (get_mvx_vendor_settings('can_vendor_edit_shipping_policy', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('can_vendor_edit_shipping_policy', 'Enable', 'general', 'policies');
                }
                delete_mvx_vendor_settings('can_vendor_edit_refund_policy', 'capabilities');

                if (get_mvx_vendor_settings('simple', 'product') == 'Enable') {
                    update_mvx_vendor_settings('simple', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('simple', 'product');
                if (get_mvx_vendor_settings('variable', 'product') == 'Enable') {
                    update_mvx_vendor_settings('variable', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('variable', 'product');
                if (get_mvx_vendor_settings('grouped', 'product') == 'Enable') {
                    update_mvx_vendor_settings('grouped', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('grouped', 'product');
                if (get_mvx_vendor_settings('external', 'product') == 'Enable') {
                    update_mvx_vendor_settings('external', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('external', 'product');
                if (get_mvx_vendor_settings('virtual', 'product') == 'Enable') {
                    update_mvx_vendor_settings('virtual', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('virtual', 'product');
                if (get_mvx_vendor_settings('downloadable', 'product') == 'Enable') {
                    update_mvx_vendor_settings('downloadable', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('downloadable', 'product');

                /* Capability tab */
                if (get_mvx_vendor_settings('is_submit_product', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('is_submit_product', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('is_submit_product', 'capabilities');
                if (get_mvx_vendor_settings('is_published_product', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('is_published_product', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('is_published_product', 'capabilities');
                if (get_mvx_vendor_settings('is_upload_files', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('is_upload_files', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('is_upload_files', 'capabilities');
                if (get_mvx_vendor_settings('is_submit_coupon', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('is_submit_coupon', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('is_submit_coupon', 'capabilities');
                if (get_mvx_vendor_settings('is_published_coupon', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('is_published_coupon', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('is_published_coupon', 'capabilities');
                if (get_mvx_vendor_settings('is_edit_published_product', 'capabilities') == 'Enable') {
                    update_mvx_vendor_settings('is_edit_published_product', 'Enable', 'capabilities', 'product');
                }
                delete_mvx_vendor_settings('is_edit_published_product', 'capabilities');

                if (!get_mvx_vendor_settings('is_edit_delete_published_product', 'capabilities', 'product')) {
                    update_mvx_vendor_settings('is_edit_delete_published_product', 'Enable', 'capabilities', 'product');
                }
                if (!get_mvx_vendor_settings('is_edit_delete_published_coupon', 'capabilities', 'product')) {
                    update_mvx_vendor_settings('is_edit_delete_published_coupon', 'Enable', 'capabilities', 'product');
                }

                $mvx_pages = mvx_get_option('mvx_pages_settings_name');
                $mvx_old_pages = array(
//                'vendor_dashboard' => 'mvx_product_vendor_vendor_dashboard_page_id'
                    'shop_settings' => 'mvx_product_vendor_shop_settings_page_id'
                    , 'view_order' => 'mvx_product_vendor_vendor_orders_page_id'
                    , 'vendor_order_detail' => 'mvx_product_vendor_vendor_order_detail_page_id'
                    , 'vendor_transaction_thankyou' => 'mvx_product_vendor_transaction_widthdrawal_page_id'
                    , 'vendor_transaction_detail' => 'mvx_product_vendor_transaction_details_page_id'
                    , 'vendor_policies' => 'mvx_product_vendor_policies_page_id'
                    , 'vendor_billing' => 'mvx_product_vendor_billing_page_id'
                    , 'vendor_shipping' => 'mvx_product_vendor_shipping_page_id'
                    , 'vendor_report' => 'mvx_product_vendor_report_page_id'
                    , 'vendor_widthdrawals' => 'mvx_product_vendor_widthdrawals_page_id'
                    , 'vendor_university' => 'mvx_product_vendor_university_page_id'
                    , 'vendor_announcements' => 'mvx_product_vendor_announcements_page_id'
                );
                foreach ($mvx_old_pages as $page_slug => $page_option) {
                    $trash_status = wp_trash_post(get_option($page_option));
                    if ($trash_status) {
                        delete_option($page_option);
                        unset($mvx_pages[$page_slug]);
                    }
                }
                mvx_update_option('mvx_pages_settings_name', $mvx_pages);

                #region update page option
                if (get_mvx_vendor_settings('mvx_vendor', 'pages')) {
                    update_mvx_vendor_settings('mvx_vendor', get_mvx_vendor_settings('mvx_vendor', 'pages'), 'vendor', 'general');
                }
                if (get_mvx_vendor_settings('vendor_registration', 'pages')) {
                    update_mvx_vendor_settings('vendor_registration', get_mvx_vendor_settings('vendor_registration', 'pages'), 'vendor', 'general');
                }
                $MVX->load_class('endpoints');
                $endpoints = new MVX_Endpoints();
                $endpoints->add_mvx_endpoints();
                flush_rewrite_rules();
                delete_option('mvx_pages_settings_name');
                mvx_update_option('mvx_database_upgrade', 'done');
                #endregion
            }
            if ($previous_plugin_version <= '2.6.5') {
                if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}mvx_vendor_orders';")) {
                    if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_vendor_orders` LIKE 'commission_status';")) {
                        $wpdb->query("ALTER TABLE {$wpdb->prefix}mvx_vendor_orders ADD `commission_status` varchar(100) NOT NULL DEFAULT 'unpaid';");
                    }
                    if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_vendor_orders` LIKE 'quantity';")) {
                        $wpdb->query("ALTER TABLE {$wpdb->prefix}mvx_vendor_orders ADD `quantity` bigint(20) NOT NULL DEFAULT 1;");
                    }
                    if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_vendor_orders` LIKE 'variation_id';")) {
                        $wpdb->query("ALTER TABLE {$wpdb->prefix}mvx_vendor_orders ADD `variation_id` bigint(20) NOT NULL DEFAULT 0;");
                    }
                    if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_vendor_orders` LIKE 'shipping_tax_amount';")) {
                        $wpdb->query("ALTER TABLE {$wpdb->prefix}mvx_vendor_orders ADD `shipping_tax_amount` varchar(255) NOT NULL DEFAULT 0;");
                    }
                    if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_vendor_orders` LIKE 'line_item_type';")) {
                        $wpdb->query("ALTER TABLE {$wpdb->prefix}mvx_vendor_orders ADD `line_item_type` longtext NULL;");
                    }
                    if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_vendor_orders` LIKE 'commission_paid_date';")) {
                        $wpdb->query("ALTER TABLE {$wpdb->prefix}mvx_vendor_orders ADD `commission_paid_date` timestamp NULL;");
                    }
                }
            }
            if ($previous_plugin_version <= '2.7.3') {
                if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}mvx_vendor_orders';")) {
                    $wpdb->query("ALTER TABLE `{$wpdb->prefix}mvx_vendor_orders` DROP INDEX `vendor_orders`;");
                    $wpdb->query("ALTER TABLE `{$wpdb->prefix}mvx_vendor_orders` ADD CONSTRAINT `vendor_orders` UNIQUE (order_id, vendor_id, commission_id, order_item_id);");
                }
            }
            if ($previous_plugin_version <= '2.7.5') {
                if (!class_exists('WP_Roles')) {
                    return;
                }

                if (!isset($wp_roles)) {
                    $wp_roles = new WP_Roles();
                }
                $wp_roles->add_cap('dc_vendor', 'assign_product_terms', true);
                $wp_roles->add_cap('dc_vendor', 'read_product', true);
                $wp_roles->add_cap('dc_vendor', 'read_shop_coupon', true);
                /** remove user wise capability * */
                $args = array('role' => 'dc_vendor', 'fields' => 'ids', 'orderby' => 'registered', 'order' => 'ASC');
                $user_query = new WP_User_Query($args);
                if (!empty($user_query->results)) {
                    foreach ($user_query->results as $vendor_id) {
                        $user = new WP_User($vendor_id);
                        if ($user && !get_user_meta($vendor_id, 'vendor_group_id', true)) {
                            $user->remove_cap('publish_products');
                            $user->remove_cap('assign_product_terms');
                            $user->remove_cap('read_product');
                            $user->remove_cap('read_shop_coupon');
                            $user->remove_cap('read_shop_coupons');
                            $user->remove_cap('edit_posts');
                            $user->remove_cap('edit_shop_coupon');
                            $user->remove_cap('delete_shop_coupon');
                            $user->remove_cap('upload_files');
                            $user->remove_cap('edit_published_products');
                            $user->remove_cap('delete_published_products');
                            $user->remove_cap('edit_product');
                            $user->remove_cap('delete_product');
                            $user->remove_cap('edit_products');
                            $user->remove_cap('delete_products');
                            $user->remove_cap('publish_shop_coupons');
                            $user->remove_cap('edit_shop_coupons');
                            $user->remove_cap('delete_shop_coupons');
                            $user->remove_cap('edit_published_shop_coupons');
                            $user->remove_cap('delete_published_shop_coupons');
                        }
                    }
                }
            }
            if ($previous_plugin_version <= '2.7.7') {
                $wpdb->delete($wpdb->prefix . 'mvx_products_map', array('product_title' => 'AUTO-DRAFT'));
            }
            if (version_compare($previous_plugin_version, '2.7.8', '<=')) {
                mvx_update_option('users_can_register', 1);
                delete_option('_is_dismiss_service_notice');
                if (apply_filters('mvx_do_schedule_cron_vendor_weekly_order_stats', true) && !wp_next_scheduled('vendor_weekly_order_stats')) {
                    wp_schedule_event(time(), 'weekly', 'vendor_weekly_order_stats');
                }
                if (apply_filters('mvx_do_schedule_cron_vendor_weekly_order_stats', true) && !wp_next_scheduled('vendor_monthly_order_stats')) {
                    wp_schedule_event(time(), 'monthly', 'vendor_monthly_order_stats');
                }
                $collate = '';
                if ($wpdb->has_cap('collation')) {
                    $collate = $wpdb->get_charset_collate();
                }
                $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}mvx_vistors_stats`;");
                $create_tables_query = array();
                // mvx_visitors_stats table 
                $create_tables_query[$wpdb->prefix . 'mvx_visitors_stats'] = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "mvx_visitors_stats` (
                    `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                    `vendor_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                    `user_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
                    `user_cookie` varchar(255) NOT NULL,
                    `session_id` varchar(191) NOT NULL,
                    `ip` varchar(60) NOT NULL,
                    `lat` varchar(60) NOT NULL,
                    `lon` varchar(60) NOT NULL,
                    `city` text NOT NULL,
                    `zip` varchar(20) NOT NULL,
                    `regionCode` text NOT NULL,
                    `region` text NOT NULL,
                    `countryCode` text NOT NULL,
                    `country` text NOT NULL,
                    `isp` text NOT NULL,
                    `timezone` varchar(255) NOT NULL,
                    `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,             
                    PRIMARY KEY (`ID`),
                    CONSTRAINT visitor UNIQUE (vendor_id, session_id),
                    KEY vendor_id (vendor_id),
                    KEY user_id (user_id),
                    KEY user_cookie (user_cookie),
                    KEY session_id (session_id),
                    KEY ip (ip)
                    ) $collate;";
                // mvx_cust_questions table 
                $create_tables_query[$wpdb->prefix . 'mvx_cust_questions'] = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "mvx_cust_questions` (
                    `ques_ID` bigint(20) NOT NULL AUTO_INCREMENT,
                    `product_ID` BIGINT UNSIGNED NOT NULL DEFAULT '0',
                    `ques_details` text NOT NULL,
                    `ques_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
                    `ques_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `ques_vote` longtext NULL,
                    PRIMARY KEY (`ques_ID`)
                    ) $collate;";
                // mvx_cust_answers table 
                $create_tables_query[$wpdb->prefix . 'mvx_cust_answers'] = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "mvx_cust_answers` (
                    `ans_ID` bigint(20) NOT NULL AUTO_INCREMENT,
                    `ques_ID` BIGINT UNSIGNED NOT NULL DEFAULT '0',
                    `ans_details` text NOT NULL,
                    `ans_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
                    `ans_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `ans_vote` longtext NULL,
                    PRIMARY KEY (`ans_ID`),
                    CONSTRAINT ques_id UNIQUE (ques_ID)
                    ) $collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                foreach ($create_tables_query as $table => $create_table_query) {
                    if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                        $wpdb->query(str_replace( 'rn', '', wc_clean(wp_unslash(esc_sql($create_table_query)))));
                    }
                }
                if (get_mvx_vendor_settings('sold_by_catalog', 'frontend') && get_mvx_vendor_settings('sold_by_catalog', 'frontend') == 'Enable') {
                    update_mvx_vendor_settings('sold_by_catalog', 'Enable', 'general');
                }
            }
            if (version_compare($previous_plugin_version, '3.0.1', '<=')) {
                $vendors = get_mvx_vendors();
                if ($vendors) {
                    foreach ($vendors as $vendor) {
                        delete_user_meta($vendor->id, 'timezone_string');
                    }
                }
            }
            if (version_compare($previous_plugin_version, '3.0.3', '<=')) {
                $collate = '';
                if ($wpdb->has_cap('collation')) {
                    $collate = $wpdb->get_charset_collate();
                }
                $create_table_query = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mvx_cust_answers` (
        `ans_ID` bigint(20) NOT NULL AUTO_INCREMENT,
        `ques_ID` BIGINT UNSIGNED NOT NULL DEFAULT '0',
                `ans_details` text NOT NULL,
        `ans_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
        `ans_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `ans_vote` longtext NULL,
        PRIMARY KEY (`ans_ID`),
                CONSTRAINT ques_id UNIQUE (ques_ID)
        ) $collate;";
                $wpdb->query($wpdb->prepare("%s", $create_table_query));
            }
            if (version_compare($previous_plugin_version, '3.0.5', '<=')) {
                $max_index_length = 191;
                $wpdb->query("ALTER TABLE `{$wpdb->prefix}mvx_visitors_stats` DROP INDEX `user_cookie`, ADD INDEX `user_cookie`(user_cookie({$max_index_length}))");
            }
            if (version_compare($previous_plugin_version, '3.1.0', '<=')) {
                /* Migrate vendor data application */
                $args = array('post_type' => 'mvx_vendorrequest', 'numberposts' => -1, 'post_status' => 'publish');
                $vendor_applications = get_posts($args);
                if ($vendor_applications) :
                    foreach ($vendor_applications as $application) {
                        $user_id = get_post_meta($application->ID, 'user_id', true);
                        $application_data = get_post_meta($application->ID, 'mvx_vendor_fields', true);
                        if (update_user_meta($user_id, 'mvx_vendor_fields', $application_data)) {
                            wp_delete_post($application->ID, true);
                        }
                    }
                endif;
                if (post_type_exists('mvx_vendorrequest')) {
                    unregister_post_type('mvx_vendorrequest');
                }
            }
            if (version_compare($previous_plugin_version, '3.1.5', '<')) {
                // new vendor shipping setting value based on payment shipping settings
                if (!get_mvx_vendor_settings('is_vendor_shipping_on', 'general') && get_mvx_vendor_settings('give_shipping', 'payment') && 'Enable' === get_mvx_vendor_settings('give_shipping', 'payment')) {
                    update_mvx_vendor_settings('is_vendor_shipping_on', 'Enable', 'general');
                } else {
                    $settings = mvx_get_option("mvx_general_settings_name");
                    if (isset($settings['is_vendor_shipping_on']))
                        unset($settings['is_vendor_shipping_on']);
                    mvx_update_option('mvx_general_settings_name', $settings);
                }
            }
            if (version_compare($previous_plugin_version, '3.2.0', '<=')) {
                if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}mvx_products_map';")) {
                    if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_products_map` LIKE 'product_map_id';") && !$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_products_map` LIKE 'product_id';")) {
                        $wpdb->query("DELETE FROM `{$wpdb->prefix}mvx_products_map`;");
                        $wpdb->query("ALTER TABLE `{$wpdb->prefix}mvx_products_map` AUTO_INCREMENT = 1;");
                        if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_products_map` LIKE 'product_map_id';") && $wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_products_map` LIKE 'product_title';")) {
                            $wpdb->query("ALTER TABLE `{$wpdb->prefix}mvx_products_map` CHANGE `product_title` `product_map_id` BIGINT UNSIGNED NOT NULL DEFAULT 0;");
                        }
                        if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_products_map` LIKE 'product_id';") && $wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_products_map` LIKE 'product_ids';")) {
                            $wpdb->query("ALTER TABLE `{$wpdb->prefix}mvx_products_map` CHANGE `product_ids` `product_id` BIGINT UNSIGNED NOT NULL DEFAULT 0;");
                        }
                    }
                }
                if (apply_filters('mvx_do_schedule_cron_mvx_spmv_excluded_products_map', true) && !wp_next_scheduled('mvx_spmv_excluded_products_map')) {
                    wp_schedule_event(time(), 'every_5minute', 'mvx_spmv_excluded_products_map');
                }
                // Add delete caps for vendor, specially for media
                $dc_role = get_role('dc_vendor');
                if (!$dc_role->has_cap('delete_posts'))
                    $dc_role->add_cap('delete_posts');
            }
            if (version_compare($previous_plugin_version, '3.2.1', '<=')) {
                if (!wp_next_scheduled('mvx_spmv_product_meta_update') && !mvx_get_option('mvx_spmv_product_meta_migrated', false)) {
                    wp_schedule_event(time(), 'hourly', 'mvx_spmv_product_meta_update');
                }
            }
            if (version_compare($previous_plugin_version, '3.2.2', '<=')) {
                // shipping migration for beta
                if(!mvx_get_option('mvx_322_vendor_shipping_data_migrated')){
                    $vendors = get_mvx_vendors();
                    $MVX->shipping_gateway->load_shipping_methods();
                    if ($vendors) {
                        foreach ($vendors as $vendor) {
                            $vendor_shipping_data = get_user_meta($vendor->id, 'vendor_shipping_data', true);
                            if(!$vendor_shipping_data) continue;
                            $shipping_class_id = get_user_meta($vendor->id, 'shipping_class_id', true);
                            
                            $raw_zones = WC_Shipping_Zones::get_zones();
                            $raw_zones[] = array('id' => 0);
                            foreach ($raw_zones as $raw_zone) {
                                $zone = new WC_Shipping_Zone($raw_zone['id']);
                                $raw_methods = $zone->get_shipping_methods();
                                foreach ($raw_methods as $raw_method) {
                                    if ($raw_method->id == 'flat_rate' && isset($raw_method->instance_form_fields["class_cost_" . $shipping_class_id])) {
                                        $instance_field = $raw_method->instance_form_fields["class_cost_" . $shipping_class_id];
                                        $instance_settings = $raw_method->instance_settings["class_cost_" . $shipping_class_id];
                                        if($instance_settings){
                                            $instance_id = $zone->add_shipping_method( wc_clean( 'mvx_vendor_shipping' ) );
           
                                            $data = array(
                                                'zone_id'   => $raw_zone['id'],
                                                'method_id' => $raw_method->id,
                                                'vendor_id' => $vendor->id
                                            );
                                            $added_shipping_instance_id = MVX_Shipping_Zone::add_shipping_methods($data);
                                            if($added_shipping_instance_id){
                                                $args = array(
                                                    'instance_id'   => $added_shipping_instance_id,
                                                    'method_id'     => $raw_method->id,
                                                    'zone_id'       => $raw_zone['id'],
                                                    'vendor_id'     => $vendor->id,
                                                    'settings'      => array(
                                                        'title'         => $raw_method->instance_settings['title'],
                                                        'description'   => '',
                                                        'cost'          => '',
                                                        'tax_status'    => 'none',
                                                        'class_cost_'.$shipping_class_id => $instance_settings,
                                                        'calculation_type' => ''
                                                    )
                                                );
                                                $result = MVX_Shipping_Zone::update_shipping_method($args);
                                            }
                                            // remove vendor shipping cost data
                                            $option_name = "woocommerce_" . $raw_method->id . "_" . $raw_method->instance_id . "_settings";
                                            $shipping_details = get_option($option_name);
                                            $class = "class_cost_" . $shipping_class_id;
                                            $shipping_details[$class] = '';
                                            mvx_update_option($option_name, $shipping_details);
                                        }
                                    }
                                }
                            }
                        }
                        WC_Cache_Helper::get_transient_version('shipping', true);
                        mvx_update_option('mvx_322_vendor_shipping_data_migrated', true);
                    }
                }
            }
            if (version_compare($previous_plugin_version, '3.4.0', '<=')) {
                $args = array('role' => 'dc_vendor', 'fields' => 'ids', 'orderby' => 'registered', 'order' => 'ASC');
                $user_query = new WP_User_Query($args);
                if ( !mvx_get_option( 'user_mvx_vendor_role_updated' ) && !empty( $user_query->results ) ) {
                    foreach ( $user_query->results as $vendor_id ) {
                        $user = new WP_User( $vendor_id );
                        $user->add_cap( 'edit_shop_orders' );
                        $user->add_cap( 'read_shop_orders' );
                        $user->add_cap( 'delete_shop_orders' );
                        $user->add_cap( 'publish_shop_orders' );
                        $user->add_cap( 'edit_published_shop_orders' );
                        $user->add_cap( 'delete_published_shop_orders' );
                        $user->remove_cap( 'add_shop_orders' );
                    }
                    mvx_update_option( 'user_mvx_vendor_role_updated', true );
                }
                
                if( !mvx_get_option('mvx_orders_table_migrated') && !wp_next_scheduled('mvx_orders_migration') ){
                    wp_schedule_event( time(), 'hourly', 'mvx_orders_migration' );
                }
            }
            if (version_compare($previous_plugin_version, '3.5.0', '<=')) {
                if (!$wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}mvx_cust_questions` LIKE 'status';")) {
                    $wpdb->query("ALTER TABLE {$wpdb->prefix}mvx_cust_questions ADD `status` text NOT NULL;");
                }
            }
            /* Migrate commission data into table */
            do_mvx_commission_data_migrate();
        }
        mvx_update_option('dc_product_vendor_plugin_db_version', $new_plugin_version);
    }

}

if (!function_exists('sksort')) {

    /**
     * Multilevel sort by subarry key
     * @param array $array
     * @param string $subkey
     * @param Boolean $sort_ascending
     */
    function sksort(&$array, $subkey = "id", $sort_ascending = false) {
        if (count($array)) {
            $temp_array[key($array)] = array_shift($array);
        }
        foreach ($array as $key => $val) {
            $offset = 0;
            $found = false;
            foreach ($temp_array as $tmp_key => $tmp_val) {
                if (!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) {
                    $temp_array = array_merge((array) array_slice($temp_array, 0, $offset), array($key => $val), array_slice($temp_array, $offset)
                    );
                    $found = true;
                }
                $offset++;
            }
            if (!$found) {
                $temp_array = array_merge($temp_array, array($key => $val));
            }
        }
        if ($sort_ascending) {
            $array = array_reverse($temp_array);
        } else {
            $array = $temp_array;
        }
    }

}

if (!function_exists('do_mvx_commission_data_migrate')) {

    function do_mvx_commission_data_migrate() {
        global $wpdb;
        /* Update Commission Order Table */
        if (mvx_get_option('commission_data_migrated')) {
            return;
        }
        $offset = mvx_get_option('dc_commission_offset_to_migrate') ? mvx_get_option('dc_commission_offset_to_migrate') : 0;
        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('private'),
            'posts_per_page' => 50,
            'order' => 'asc',
            'offset' => $offset
        );

        if (isset(wp_count_posts('dc_commission')->private) && wp_count_posts('dc_commission')->private >= $offset * 50) {
            $commissions = get_posts($args);
            $commissions_to_migrate = array();
            foreach ($commissions as $commission) {
                $commissions_to_migrate[$commission->ID] = array(
                    'order_id' => get_post_meta($commission->ID, '_commission_order_id', true),
                    'products' => get_post_meta($commission->ID, '_commission_product', true),
                    'vendor_id' => get_post_meta($commission->ID, '_commission_vendor', true),
                    'commission_amount' => get_post_meta($commission->ID, '_commission_amount', true),
                    'shipping_amount' => get_post_meta($commission->ID, '_shipping', true),
                    'tax_amount' => get_post_meta($commission->ID, '_tax', true),
                    'paid_status' => get_post_meta($commission->ID, '_paid_status', true)
                );
            }
            $update_data = array();
            foreach ($commissions_to_migrate as $commission_id => $data) {
                $product_count = count($data['products']);
                foreach ($data['products'] as $product_id) {
                    if ($data['vendor_id']) {
                        $order = wc_get_order($data['order_id']);
                        $vendor = get_mvx_vendor_by_term($data['vendor_id']);
                        $update_data[] = array(
                            'order_id' => $data['order_id'],
                            'commission_id' => $commission_id,
                            'vendor_id' => $vendor->id,
                            'shipping_status' => in_array($vendor->id, (array) $order->get_meta( 'dc_pv_shipped', true)) ? 1 : 0,
                            'product_id' => $product_id,
                            'commission_amount' => round(($data['commission_amount'] / $product_count), 2),
                            'shipping' => round(($data['shipping_amount'] / $product_count), 2),
                            'tax' => round(($data['tax_amount'] / $product_count), 2),
                            'commission_status' => $data['paid_status'],
                            'quantity' => 1,
                            'shipping_tax_amount' => 0,
                            'variation_id' => 0,
                            'line_item_type' => 'product'
                        );
                    }
                }
            }
            foreach ($update_data as $update) {
                if ($wpdb->get_var( $wpdb->prepare("SELECT ID FROM `{$wpdb->prefix}mvx_vendor_orders` WHERE order_id = %d AND commission_id = %d AND vendor_id = %d AND product_id = %d", $update['order_id'], $update['commission_id'], $update['vendor_id'], $update['product_id'] ))) {
                    $wpdb->query($wpdb->prepare("UPDATE `{$wpdb->prefix}mvx_vendor_orders` SET shipping_status = %d, commission_amount = %d, shipping = %d, tax = %d, commission_status = %d, quantity = %d, variation_id = %d, shipping_tax_amount = %d, line_item_type = %d WHERE order_id = %d AND commission_id = %d AND vendor_id = %d AND product_id = %d", $update['shipping_status'], $update['commission_amount'], $update['shipping'], $update['tax'], $update['commission_status'], $update['quantity'], $update['variation_id'], $update['shipping_tax_amount'], $update['line_item_type'], $update['order_id'], $update['commission_id'], $update['vendor_id'], $update['product_id'] ));
                } else {
                    $wpdb->query(
                            $wpdb->prepare(
                                    "INSERT INTO `{$wpdb->prefix}mvx_vendor_orders` 
                                        ( order_id
                                        , commission_id
                                        , commission_amount
                                        , vendor_id
                                        , shipping_status
                                        , order_item_id
                                        , product_id
                                        , variation_id
                                        , tax
                                        , line_item_type
                                        , quantity
                                        , commission_status
                                        , shipping
                                        , shipping_tax_amount
                                        ) VALUES ( %d
                                        , %d
                                        , %s
                                        , %d
                                        , %s
                                        , %d
                                        , %d 
                                        , %d
                                        , %s
                                        , %s
                                        , %d
                                        , %s
                                        , %s
                                        , %s
                                        ) ON DUPLICATE KEY UPDATE `created` = now()"
                                    , $update['order_id']
                                    , $update['commission_id']
                                    , $update['commission_amount']
                                    , $update['vendor_id']
                                    , $update['shipping_status']
                                    , 0
                                    , $update['product_id']
                                    , 0
                                    , $update['tax']
                                    , 'product'
                                    , 1
                                    , $update['commission_status']
                                    , $update['shipping']
                                    , $update['shipping_tax_amount']
                            )
                    );
                }
            }
            $offset++;
            mvx_update_option('dc_commission_offset_to_migrate', $offset);
        } else {
            mvx_update_option('commission_data_migrated', '1');
        }
    }

}

if (!function_exists('mvx_process_order')) {

    /**
     * Calculate shipping, tax and insert into mvx_vendor_orders table whenever an order is created.
     * 
     * @since 2.7.6
     * @global onject $wpdb
     * @param int $order_id
     * @param WC_Order object $order
     */
    function mvx_process_order($order_id, $order = null) {
        global $wpdb;
        if (!$order)
            $order = wc_get_order($order_id);
        if ($order->get_meta( '_mvx_order_processed', true) && !$order) {
            return;
        }
        $vendor_shipping_array = $order->get_meta( 'dc_pv_shipped', true);
        $mark_ship = 0;
        $items = $order->get_items('line_item');
        $shipping_items = $order->get_items('shipping');
        $vendor_shipping = array();
        foreach ($shipping_items as $shipping_item_id => $shipping_item) {
            $order_item_shipping = new WC_Order_Item_Shipping($shipping_item_id);
            $shipping_vendor_id = $order_item_shipping->get_meta('vendor_id', true);
            $vendor_shipping[$shipping_vendor_id] = array(
                'shipping' => $order_item_shipping->get_total()
                , 'shipping_tax' => $order_item_shipping->get_total_tax()
                , 'package_qty' => $order_item_shipping->get_meta('package_qty', true)
            );
        }
        foreach ($items as $order_item_id => $item) {
            $line_item = new WC_Order_Item_Product($item);
            $product_id = $item['product_id'];
            $variation_id = isset($item['variation_id']) ? $item['variation_id'] : 0;
            if ($product_id) {
                $product_vendors = get_mvx_product_vendors($product_id);
                $product = wc_get_product($product_id);
                if ($product_vendors) {
                    if (isset($product_vendors->id) && is_array($vendor_shipping_array)) {
                        if (in_array($product_vendors->id, $vendor_shipping_array)) {
                            $mark_ship = 1;
                        }
                    }
                    $shipping_amount = $shipping_tax_amount = $tax_amount = 0;
                    if (!empty($vendor_shipping) && isset($vendor_shipping[$product_vendors->id]) && $product_vendors->is_transfer_shipping_enable() && $product->needs_shipping()) {
                        $shipping_amount = (float) round(($vendor_shipping[$product_vendors->id]['shipping'] / $vendor_shipping[$product_vendors->id]['package_qty']) * $line_item->get_quantity(), 2);
                        $shipping_tax_amount = (float) round(($vendor_shipping[$product_vendors->id]['shipping_tax'] / $vendor_shipping[$product_vendors->id]['package_qty']) * $line_item->get_quantity(), 2);
                    }
                    if ($product_vendors->is_transfer_tax_enable() && $line_item->get_total_tax()) {
                        $tax_amount = $line_item->get_total_tax();
                    }
                    $wpdb->query(
                            $wpdb->prepare(
                                    "INSERT INTO `{$wpdb->prefix}mvx_vendor_orders` 
                                        ( order_id
                                        , commission_id
                                        , vendor_id
                                        , shipping_status
                                        , order_item_id
                                        , product_id
                                        , variation_id
                                        , tax
                                        , line_item_type
                                        , quantity
                                        , commission_status
                                        , shipping
                                        , shipping_tax_amount
                                        ) VALUES ( %d
                                        , %d
                                        , %d
                                        , %s
                                        , %d
                                        , %d 
                                        , %d
                                        , %s
                                        , %s
                                        , %d
                                        , %s
                                        , %s
                                        , %s
                                        ) ON DUPLICATE KEY UPDATE `created` = now()"
                                    , $order_id
                                    , 0
                                    , $product_vendors->id
                                    , $mark_ship
                                    , $order_item_id
                                    , $product_id
                                    , $variation_id
                                    , $tax_amount
                                    , 'product'
                                    , $line_item->get_quantity()
                                    , 'unpaid'
                                    , $shipping_amount
                                    , $shipping_tax_amount
                            )
                    );
                }
            }
        }
        $order->update_meta_data('_mvx_order_processed', true);
        $order->save();
        do_action('mvx_order_processed', $order);
    }

}

if (!function_exists('get_current_vendor_id')) {

    /**
     * get current logged in vendor id
     * @return int
     */
    function get_current_vendor_id() {
        return apply_filters('mvx_current_loggedin_vendor_id', get_current_user_id());
    }

}

if (!function_exists('get_current_vendor')) {

    /**
     * get current logged in vendor
     * @return MVX_Vendor object
     */
    function get_current_vendor() {
        return get_mvx_vendor() ? get_mvx_vendor() : false;
    }

}

if (!function_exists('MVXGenerateTaxonomyHTML')) {

    function MVXGenerateTaxonomyHTML($taxonomy, $product_categories = array(), $categories = array(), $nbsp = '') {

        foreach ($product_categories as $cat) {
            if (apply_filters('mvx_is_visible_frontend_product_categories', true, $cat->term_id, $taxonomy)) {
                echo '<option value="' . esc_attr($cat->term_id) . '"' . selected(in_array($cat->term_id, $categories), true, false) . '>' . $nbsp . esc_html($cat->name) . '</option>';
            }
            $product_child_categories = get_terms($taxonomy, 'orderby=name&hide_empty=0&parent=' . absint($cat->term_id));
            if ($product_child_categories) {
                MVXGenerateTaxonomyHTML($taxonomy, $product_child_categories, $categories, $nbsp . '<span class="sub-cat-pre">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
            }
        }
    }

}

if (!function_exists('mvx_get_vendor_profile_completion')) {

    /**
     * Get vendor profile completion
     * @param int $vendor_id
     * @return array profile_completion
     */
    function mvx_get_vendor_profile_completion($vendor_id) {
        $profile_completion = array('todo' => '', 'progress' => 0);
        $vendor = get_mvx_vendor($vendor_id);
        if ($vendor) {
            $progress_fields = array(
                '_vendor_page_title' => array(
                    'label' => __('Store Name', 'multivendorx'),
                    'link' => mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_store_settings_endpoint', 'seller_dashbaord', 'storefront'))
                ),
                '_vendor_image' => array(
                    'label' => __('Store Image', 'multivendorx'),
                    'link' => mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_store_settings_endpoint', 'seller_dashbaord', 'storefront'))
                ),
                '_vendor_banner' => array(
                    'label' => __('Store Cover Image', 'multivendorx'),
                    'link' => mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_store_settings_endpoint', 'seller_dashbaord', 'storefront'))
                ),
                '_vendor_payment_mode' => array(
                    'label' => __('Payment Method', 'multivendorx'),
                    'link' => mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_billing_endpoint', 'seller_dashbaord', 'vendor-billing'))
                ),
                '_vendor_added_product' => array(
                    'label' => __('Product', 'multivendorx'),
                    'link' => mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_add_product_endpoint', 'seller_dashbaord', 'add-product'))
                ),
            );
            if (wc_shipping_enabled() && $vendor->is_shipping_enable()) {
                $progress_fields['vendor_shipping_data'] = array(
                    'label' => __('Shipping Data', 'multivendorx'),
                    'link' => mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_shipping_endpoint', 'seller_dashbaord', 'vendor-shipping'))
                );
            }
            $progress_fields = apply_filters('mvx_vendor_profile_completion_progress_fields', $progress_fields, $vendor->id);
            // initial vendor progress
            if (is_user_mvx_vendor($vendor_id)) {
                $progress = 1;
                $no_of_fields = count($progress_fields) + 1;
            } else {
                $progress = 0;
                $no_of_fields = count($progress_fields);
            }

            $todo = array();
            foreach ($progress_fields as $key => $value) {
                $has_value = get_user_meta($vendor->id, $key, true);
                if ($key == '_vendor_added_product') {
                    if ($has_value || count($vendor->get_products_ids()) > 0) {
                        $progress++;
                    } else {
                        $todo[] = $value;
                    }
                } elseif( $key == 'vendor_shipping_data' ) {
                    if( has_vendor_config_shipping_methods() ) {
                        $progress++;
                    } else {
                        $todo[] = $value;
                    }
                } else {
                    if ($has_value) {
                        $progress++;
                    } else {
                        $todo[] = $value;
                    }
                }
            }
            if ($todo && count($todo) > 0) {
                $random_todo = array_rand($todo);
                $profile_completion['todo'] = $todo[$random_todo];
            } else {
                $profile_completion['todo'] = '';
            }
            $profile_completion['progress'] = number_format((float) (($progress / $no_of_fields) * 100), 0);
        }
        return apply_filters('mvx_vendor_profile_completion_progress_array', $profile_completion, $vendor->id);
    }

}

if (!function_exists('get_attachment_id_by_url')) {

    /**
     * Get an attachment ID by URL.
     * 
     * @param string $url
     * @return int Attachment ID on success, 0 on failure
     */
    function get_attachment_id_by_url($url) {
        $attachment_id = 0;
        $upload_dir = wp_get_upload_dir();
        if (false !== strpos($url, $upload_dir['baseurl'] . '/')) {
            $file = basename($url);
            $args = array(
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'fields' => 'ids',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'value' => $file,
                        'compare' => 'LIKE',
                        'key' => '_wp_attached_file',
                    ),
                    array(
                        'value' => $file,
                        'compare' => 'LIKE',
                        'key' => '_wp_attachment_metadata',
                    )
                )
            );
            $attachment_query = new WP_Query($args);
            if ($attachment_query->have_posts()) {
                foreach ($attachment_query->posts as $attachment_id) {
                    $meta = wp_get_attachment_metadata($attachment_id);
                    $original_file = basename($meta['file']);
                    $cropped_image_files = wp_list_pluck($meta['sizes'], 'file');
                    if ($original_file === $file || in_array($file, $cropped_image_files)) {
                        $attachment_id = $attachment_id;
                        break;
                    }
                }
            }
        }
        return $attachment_id;
    }

}

if (!function_exists('get_visitor_ip_data')) {

    /**
     * Get visitor IP information.
     *
     */
    function get_visitor_ip_data() {
        if (!class_exists('WC_Geolocation', false)) {
            include_once( WC_ABSPATH . 'includes/class-wc-geolocation.php' );
        }
        $e = new WC_Geolocation();
        $ip_address = $e->get_ip_address();
        if ($ip_address) {
            if (get_transient('mvx_' . $ip_address)) {
                $data = get_transient('mvx_' . $ip_address);
                if ($data->status != 'error')
                    return $data;
            }
            $service_endpoint = 'http://ip-api.com/json/%s';
            $response = wp_safe_remote_get(sprintf($service_endpoint, $ip_address), array('timeout' => 2));
            if (!is_wp_error($response) && $response['body']) {
                set_transient('mvx_' . $ip_address, json_decode($response['body']), 2 * MONTH_IN_SECONDS);
                return json_decode($response['body']);
            } else {
                $data = new stdClass();
                $data->status = 'error';
                set_transient('mvx_' . $ip_address, $data, 2 * MONTH_IN_SECONDS);
                return $data;
            }
        }
    }

}

if (!function_exists('mvx_save_visitor_stats')) {

    /**
     * Save vistor stats for vendor.
     * 
     * @since 3.0.0
     * @param int $vendor_id
     * @param array $data
     */
    function mvx_save_visitor_stats($vendor_id, $data) {
        global $wpdb;
        $wpdb->query(
                $wpdb->prepare(
                        "INSERT INTO `{$wpdb->prefix}mvx_visitors_stats` 
                        ( vendor_id
                        , user_id
                        , user_cookie
                        , session_id
                        , ip
                        , lat
                        , lon
                        , city
                        , zip
                        , regionCode
                        , region
                        , countryCode
                        , country
                        , isp
                        , timezone
                        ) VALUES ( %d
                        , %d
                        , %s
                        , %s
                        , %s
                        , %s
                        , %s
                        , %s
                        , %s 
                        , %s
                        , %s
                        , %s
                        , %s
                        , %s
                        , %s
                        ) ON DUPLICATE KEY UPDATE `created` = now()"
                        , $vendor_id
                        , $data->user_id
                        , $data->user_cookie
                        , $data->session_id
                        , $data->query
                        , $data->lat
                        , $data->lon
                        , $data->city
                        , $data->zip
                        , $data->region
                        , $data->regionName
                        , $data->countryCode
                        , $data->country
                        , $data->isp
                        , $data->timezone
                )
        );
    }

}

if (!function_exists('mvx_get_visitor_stats')) {

    /**
     * Get vistors stats for vendor.
     * 
     * @since 3.0.0
     * @param int $vendor_id
     * @param string $query_where
     * @param string $query_filter
     * @return array $data
     */
    function mvx_get_visitor_stats($vendor_id, $query_where = '', $query_filter = '') {
        global $wpdb;
        if ($vendor_id) {
            $results = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$wpdb->prefix}mvx_visitors_stats WHERE " . wp_unslash(esc_sql($query_where)) . " vendor_id=%d ". wp_unslash(esc_sql($query_filter)) . " ", $vendor_id ) 
            );
            return $results;
        } else {
            return false;
        }
    }

}

if (!function_exists('get_color_shade')) {

    /**
     * Get color shade hexcode.
     * 
     * @since 3.0.0
     * @param string $hexcolor
     * @param int $percent
     * @return string $hexcolor
     */
    function get_color_shade($hex, $percent) {
        // validate hex string
        $hex = preg_replace('/[^0-9a-f]/i', '', $hex);
        $new_hex = '#';
        if (strlen($hex) < 6) {
            $hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
        }
        // convert to decimal and change luminosity
        for ($i = 0; $i < 3; $i++) {
            $dec = hexdec(substr($hex, $i * 2, 2));
            $dec = min(max(0, $dec + $dec * $percent), 255);
            $new_hex .= str_pad(dechex($dec), 2, 0, STR_PAD_LEFT);
        }
        return $new_hex;
    }

}

if (!function_exists('get_mvx_product_policies')) {

    /**
     * Get product policies.
     * 
     * @since 3.0.0
     * @param int $product_id
     * @return array $policies
     */
    function get_mvx_product_policies($product_id = 0) {
        $product = wc_get_product($product_id);
        $policies = array();
        if ($product) {
            $shipping_policy = get_mvx_vendor_settings('shipping_policy');
            $refund_policy = get_mvx_vendor_settings('refund_policy');
            $cancellation_policy = get_mvx_vendor_settings('cancellation_policy');
            if (apply_filters('mvx_vendor_can_overwrite_policies', true) && $vendor = get_mvx_product_vendors($product->get_id())) {
                $shipping_policy = get_user_meta($vendor->id, '_vendor_shipping_policy', true) ? get_user_meta($vendor->id, '_vendor_shipping_policy', true) : $shipping_policy;
                $refund_policy = get_user_meta($vendor->id, '_vendor_refund_policy', true) ? get_user_meta($vendor->id, '_vendor_refund_policy', true) : $refund_policy;
                $cancellation_policy = get_user_meta($vendor->id, '_vendor_cancellation_policy', true) ? get_user_meta($vendor->id, '_vendor_cancellation_policy', true) : $cancellation_policy;
            }
            if (get_post_meta($product->get_id(), '_mvx_shipping_policy', true)) {
                $shipping_policy = get_post_meta($product->get_id(), '_mvx_shipping_policy', true);
            }
            if (get_post_meta($product->get_id(), '_mvx_refund_policy', true)) {
                $refund_policy = get_post_meta($product->get_id(), '_mvx_refund_policy', true);
            }
            if (get_post_meta($product->get_id(), '_mvx_cancallation_policy', true)) {
                $cancellation_policy = get_post_meta($product->get_id(), '_mvx_cancallation_policy', true);
            }
            if (!empty($shipping_policy)) {
                $policies['shipping_policy'] = $shipping_policy;
            }
            if (!empty($refund_policy)) {
                $policies['refund_policy'] = $refund_policy;
            }
            if (!empty($cancellation_policy)) {
                $policies['cancellation_policy'] = $cancellation_policy;
            }
        }
        return $policies;
    }

}

if (!function_exists('get_mvx_vendor_dashboard_visitor_stats_data')) {

    /**
     * Get vendor visitor stats data.
     * 
     * @since 3.0.0
     * @param int $vendor
     * @return array $stats_data_visitors
     */
    function get_mvx_vendor_dashboard_visitor_stats_data($vendor_id = '') {
        if ($vendor_id) {
            if (get_transient('mvx_visitor_stats_data_' . $vendor_id)) {
                $data = get_transient('mvx_visitor_stats_data_' . $vendor_id);
                if ((isset($data[7]['map_stats']) && !empty($data[7]['map_stats'])) || (isset($data[30]['map_stats']) && !empty($data[30]['map_stats'])))
                    return $data;
            }

            $visitor_map_filter_attr = apply_filters('mvx_vendor_visitors_map_filter_attr', array(
                '7' => __('Last 7 days', 'multivendorx'),
                '30' => __('Last 30 days', 'multivendorx'),
            ));
            $stats_data_visitors = array(
                'lang' => array('visitors' => __(' visitors', 'multivendorx'))
            );
            $color_palet = get_mvx_vendor_settings('vendor_color_scheme_picker', 'seller_dashbaord', 'outer_space_blue');
            $color_palet_array = array('outer_space_blue' => '#316fa8', 'green_lagoon' => '#00796a', 'old_west' => '#ad8162', 'wild_watermelon' => '#fb3f4e');
            $color_shade = apply_filters('mvx_visitor_map_widget_primary_color_n_shade', array($color_palet_array[$color_palet], '0.2'));
            $primary_color = isset($color_shade[0]) ? $color_shade[0] : $color_palet_array[$color_palet];
            $shade = isset($color_shade[1]) ? $color_shade[1] : '0.2';
            if ($visitor_map_filter_attr) {
                foreach ($visitor_map_filter_attr as $period => $value) {
                    $st_dt = date('Y-m-d H:i:s', strtotime("-{$period} days"));
                    $en_dt = date('Y-m-d 23:59:59');
                    $where = "created BETWEEN '{$st_dt}' AND '{$en_dt}' AND ";
                    $filter = "GROUP BY user_cookie";
                    $visitor_data = mvx_get_visitor_stats($vendor_id, $where, $filter);
                    $data_visitors = array('map_stats' => array(), 'data_stats' => '<tr><td class="no_data" colspan="2">' . __('No Data', 'multivendorx') . '</td></tr>');
                    if ($visitor_data) {
                        $users = array();
                        $unique_data_visitors = array();
                        foreach ($visitor_data as $key => $data) {
                            if ($data->user_id == 0) {
                                $unique_data_visitors[] = $data;
                            } else {
                                if (!in_array($data->user_id, $users)) {
                                    $users[] = $data->user_id;
                                    $unique_data_visitors[] = $data;
                                }
                            }
                        }
                        $map_stats = array();
                        foreach ($unique_data_visitors as $key => $data) {
                            $country_code = strtolower($data->countryCode);
                            if (isset($map_stats[$country_code])) {
                                $map_stats[$country_code]['hits_count'] = $map_stats[$country_code]['hits_count'] + 1;
                            } else {
                                $map_stats[$country_code]['hits_count'] = 1;
                            }
                            $map_stats[$country_code]['hits_percent'] = round(($map_stats[$country_code]['hits_count'] / count($unique_data_visitors)) * 100, 2);
                        }
                        $hits = array();
                        foreach ($map_stats as $key => $data) {
                            $hits[$key] = $data['hits_count'];
                        }
                        array_multisort($hits, SORT_DESC, $map_stats);

                        $i = 1;
                        $new_color = '';
                        $data_stats = '';
                        if ($map_stats) {
                            $data_stats .= '<thead><tr><th>' . __('Country', 'multivendorx') . '</th><th>' . __('% Users', 'multivendorx') . '</td></tr></thead><tbody>';
                            foreach (array_slice($map_stats, 0, 5) as $key => $value) {
                                if ($i == 1) {
                                    $map_stats[$key]['color'] = $primary_color;
                                    $new_color = $primary_color;
                                    $data_stats .= '<tr><td class="region" bgcolor="' . $new_color . '">' . strtoupper($key) . '</td><td>' . $value['hits_percent'] . '%</td></tr>';
                                } else {
                                    $new_color = get_color_shade($new_color, $shade);
                                    $map_stats[$key]['color'] = $new_color;
                                    $data_stats .= '<tr><td class="region" bgcolor="' . $new_color . '">' . strtoupper($key) . '</td><td>' . $value['hits_percent'] . '%</td></tr>';
                                }
                                $i++;
                            }
                            $data_stats .= '</tbody>';
                        }
                        $data_visitors['map_stats'] = $map_stats;
                        $data_visitors['data_stats'] = $data_stats;
                    }
                    $stats_data_visitors[$period] = $data_visitors;
                }
            }
            set_transient('mvx_visitor_stats_data_' . $vendor_id, $stats_data_visitors, 12 * HOUR_IN_SECONDS);
        }
        return $stats_data_visitors;
    }

}

if (!function_exists('get_mvx_vendor_dashboard_stats_reports_data')) {

    /**
     * Get vendor dashboard stats reports data.
     * 
     * @since 3.0.0
     * @param object $vendor
     * @return array $stats_reports_data
     */
    function get_mvx_vendor_dashboard_stats_reports_data($vendor = '') {
        if (empty($vendor))
            $vendor = get_current_vendor();
        if ($vendor) {
            if (get_transient('mvx_stats_report_data_' . $vendor->id)) {
                return get_transient('mvx_stats_report_data_' . $vendor->id);
            }

            $stats_report_data = array();
            $mvx_stats_table = array();
            $raw_stats_data = array();
            $stats_difference = array();
            $today = @date('Y-m-d 00:00:00', strtotime("+1 days"));
            $stats_reports_periods = apply_filters('mvx_vendor_stats_reports_periods', array(
                '7' => __('Last 7 days', 'multivendorx'),
                '30' => __('Last 30 days', 'multivendorx'),
            ));
            if ($stats_reports_periods) {
                foreach ($stats_reports_periods as $key => $value) {
                    $stats_report_data[$key]['_mvx_stats_period'] = $stats_reports_periods[$key];
                    $args = array(
                        'start_date' => date('Y-m-d 00:00:00', strtotime("-$key days")),
                        'end_date' => $today,
                        'is_trashed' => ''
                    );
                    $vendor_current_stats = $vendor->get_vendor_orders_reports_of('vendor_stats', $args);
                    $raw_stats_data['current'] = $vendor_current_stats;
                    $mvx_stats_table['current_traffic_no'] = $vendor_current_stats['traffic_no'];
                    $mvx_stats_table['current_coupon_total'] = wc_price($vendor_current_stats['coupon_total'], array('decimals' => 0));
                    $mvx_stats_table['current_earning'] = wc_price($vendor_current_stats['earning'], array('decimals' => 0));
                    $mvx_stats_table['current_sales_total'] = wc_price($vendor_current_stats['sales_total'], array('decimals' => 0));
                    $mvx_stats_table['current_withdrawal'] = wc_price($vendor_current_stats['withdrawal'], array('decimals' => 0));
                    $mvx_stats_table['current_orders_no'] = $vendor_current_stats['orders_no'];
                    // previous data
                    $previous_days_range = $key * 2;
                    $args = array(
                        'start_date' => date('Y-m-d 00:00:00', strtotime("-$previous_days_range days")),
                        'end_date' => date('Y-m-d 00:00:00', strtotime("-$key days")),
                        'is_trashed' => ''
                    );
                    $vendor_previous_stats = $vendor->get_vendor_orders_reports_of('vendor_stats', $args);
                    $raw_stats_data['previous'] = $vendor_previous_stats;
                    $mvx_stats_table['previous_traffic_no'] = $vendor_previous_stats['traffic_no'];
                    $mvx_stats_table['previous_coupon_total'] = wc_price($vendor_previous_stats['coupon_total'], array('decimals' => 0));
                    $mvx_stats_table['previous_earning'] = wc_price($vendor_previous_stats['earning'], array('decimals' => 0));
                    $mvx_stats_table['previous_sales_total'] = wc_price($vendor_previous_stats['sales_total'], array('decimals' => 0));
                    $mvx_stats_table['previous_withdrawal'] = wc_price($vendor_previous_stats['withdrawal'], array('decimals' => 0));
                    $mvx_stats_table['previous_orders_no'] = $vendor_previous_stats['orders_no'];

                    $stats_report_data[$key]['_mvx_stats_table'] = $mvx_stats_table;
                    $stats_report_data[$key]['_raw_stats_data'] = $raw_stats_data;
                    foreach ($vendor_previous_stats as $prev_key => $prev_value) {
                        if ($prev_value != 0) {
                            if ($prev_key == 'orders_no') {
                                $stats_difference['_mvx_diff_' . $prev_key] = ($vendor_current_stats[$prev_key] - $vendor_previous_stats[$prev_key]);
                            } else {
                                $stats_difference['_mvx_diff_' . $prev_key] = round((($vendor_current_stats[$prev_key] - $vendor_previous_stats[$prev_key]) / $vendor_previous_stats[$prev_key]) * 100);
                            }
                        } else {
                            $stats_difference['_mvx_diff_' . $prev_key] = 'no_data';
                        }
                    }
                    $aov = 0;
                    if ($vendor_current_stats['orders_no'] != 0) {
                        $aov = $vendor_current_stats['sales_total'] / $vendor_current_stats['orders_no'];
                    }
                    $stats_report_data[$key]['stats_difference'] = $stats_difference;
                    $stats_report_data[$key]['_mvx_stats_aov'] = wc_price($aov, array('decimals' => 0));
                    $stats_report_data[$key]['_mvx_stats_lang_up'] = __('is up by', 'multivendorx');
                    $stats_report_data[$key]['_mvx_stats_lang_down'] = __('is down by', 'multivendorx');
                    $stats_report_data[$key]['_mvx_stats_lang_are_up'] = __('are up by', 'multivendorx');
                    $stats_report_data[$key]['_mvx_stats_lang_are_down'] = __('are down by', 'multivendorx');
                    $stats_report_data[$key]['_mvx_stats_lang_same'] = __('remains same', 'multivendorx');
                    $stats_report_data[$key]['_mvx_stats_lang_no_amount'] = __('no amount', 'multivendorx');
                    $stats_report_data[$key]['_mvx_stats_lang_no_prev'] = __('no prior data', 'multivendorx');
                }
            } else {
                $days_range = apply_filters('mvx_vendor_stats_default_days_range', 7);
                $stats_report_data[$key]['_mvx_stats_period'] = printf(__('Last %d days', 'multivendorx'), $days_range);
                $args = array(
                    'start_date' => date('Y-m-d 00:00:00', strtotime("-$days_range days")),
                    'end_date' => $today,
                    'is_trashed' => ''
                );
                $vendor_current_stats = $vendor->get_vendor_orders_reports_of('vendor_stats', $args);
                $raw_stats_data['current'] = $vendor_current_stats;
                $mvx_stats_table['current_traffic_no'] = $vendor_current_stats['traffic_no'];
                $mvx_stats_table['current_coupon_total'] = wc_price($vendor_current_stats['coupon_total'], array('decimals' => 0));
                $mvx_stats_table['current_earning'] = wc_price($vendor_current_stats['earning'], array('decimals' => 0));
                $mvx_stats_table['current_sales_total'] = wc_price($vendor_current_stats['sales_total'], array('decimals' => 0));
                $mvx_stats_table['current_withdrawal'] = wc_price($vendor_current_stats['withdrawal'], array('decimals' => 0));
                $mvx_stats_table['current_orders_no'] = $vendor_current_stats['orders_no'];
                // previous data
                $previous_days_range = $days_range * 2;
                $args = array(
                    'start_date' => date('Y-m-d 00:00:00', strtotime("-$previous_days_range days")),
                    'end_date' => date('Y-m-d 00:00:00', strtotime("-$days_range days")),
                    'is_trashed' => ''
                );
                $vendor_previous_stats = $vendor->get_vendor_orders_reports_of('vendor_stats', $args);
                $raw_stats_data['previous'] = $vendor_previous_stats;
                $mvx_stats_table['previous_traffic_no'] = $vendor_previous_stats['traffic_no'];
                $mvx_stats_table['previous_coupon_total'] = wc_price($vendor_previous_stats['coupon_total'], array('decimals' => 0));
                $mvx_stats_table['previous_earning'] = wc_price($vendor_previous_stats['earning'], array('decimals' => 0));
                $mvx_stats_table['previous_sales_total'] = wc_price($vendor_previous_stats['sales_total'], array('decimals' => 0));
                $mvx_stats_table['previous_withdrawal'] = wc_price($vendor_previous_stats['withdrawal'], array('decimals' => 0));
                $mvx_stats_table['previous_orders_no'] = $vendor_previous_stats['orders_no'];

                $stats_report_data[$days_range]['_mvx_stats_table'] = $mvx_stats_table;
                $stats_report_data[$days_range]['_raw_stats_data'] = $raw_stats_data;
                foreach ($vendor_previous_stats as $prev_key => $prev_value) {
                    if ($prev_value != 0) {
                        if ($prev_key == 'orders_no') {
                            $stats_difference['_mvx_diff_' . $prev_key] = ($vendor_current_stats[$prev_key] - $vendor_previous_stats[$prev_key]);
                        } else {
                            $stats_difference['_mvx_diff_' . $prev_key] = round((($vendor_current_stats[$prev_key] - $vendor_previous_stats[$prev_key]) / $vendor_previous_stats[$prev_key]) * 100);
                        }
                    } else {
                        $stats_difference['_mvx_diff_' . $prev_key] = 'no_data';
                    }
                }
                $stats_report_data[$key]['stats_difference'] = $stats_difference;
                $aov = 0;
                if ($vendor_current_stats['orders_no'] != 0) {
                    $aov = $vendor_current_stats['sales_total'] / $vendor_current_stats['orders_no'];
                }
                $stats_report_data[$days_range]['_mvx_stats_aov'] = wc_price($aov, array('decimals' => 0));
                $stats_report_data[$days_range]['_mvx_stats_lang_up'] = __('is up by ', 'multivendorx');
                $stats_report_data[$days_range]['_mvx_stats_lang_down'] = __('is down by ', 'multivendorx');
                $stats_report_data[$days_range]['_mvx_stats_lang_are_up'] = __('are up by', 'multivendorx');
                $stats_report_data[$days_range]['_mvx_stats_lang_are_down'] = __('are down by', 'multivendorx');
                $stats_report_data[$days_range]['_mvx_stats_lang_same'] = __('remains same', 'multivendorx');
                $stats_report_data[$days_range]['_mvx_stats_lang_no_amount'] = __('no amount', 'multivendorx');
                $stats_report_data[$days_range]['_mvx_stats_lang_no_prev'] = __('no prior data', 'multivendorx');
            }
            set_transient('mvx_stats_report_data_' . $vendor->id, $stats_report_data, DAY_IN_SECONDS);
            return $stats_report_data;
        }
    }

}

if (!function_exists('mvx_date')) {

    /**
     * MVX date formatter function
     * @param DateTime $date
     * @return date string
     */
    function mvx_date($date) {
        $date = wc_string_to_datetime($date)->setTimezone(new DateTimeZone('UTC'));
        $date = wc_string_to_datetime($date)->setTimezone(new DateTimeZone(get_vendor_timezone_string()));
        return $date->date_i18n( get_option('date_format'), $date );
    }

}


if (!function_exists('get_vendor_timezone_string')) {

    /**
     * MVX timezone string
     * @param $vendor_id int Vendor ID
     * @return Timezone string
     */
    function get_vendor_timezone_string($vendor_id = '') {
        if (!$vendor_id)
            $vendor_id = get_current_user_id();
        // If site timezone string exists, return it.
        if ($timezone = get_user_meta($vendor_id, 'timezone_string', true) ? get_user_meta($vendor_id, 'timezone_string', true) : get_option('timezone_string')) {
            return $timezone;
        }
        // Get UTC offset, if it isn't set then return UTC.
        if (0 === ( $utc_offset = intval(get_user_meta($vendor_id, 'gmt_offset', true) ? get_user_meta($vendor_id, 'gmt_offset', true) : get_option('gmt_offset', 0)) )) {
            return 'UTC';
        }

        // Adjust UTC offset from hours to seconds.
        $utc_offset *= 3600;


        // Attempt to guess the timezone string from the UTC offset.
        if ($timezone = timezone_name_from_abbr('', $utc_offset)) {
            return $timezone;
        }

        // Last try, guess timezone string manually.
        foreach (timezone_abbreviations_list() as $abbr) {
            foreach ($abbr as $city) {
                if ((bool) date('I') === (bool) $city['dst'] && $city['timezone_id'] && intval($city['offset']) === $utc_offset) {
                    return $city['timezone_id'];
                }
            }
        }

        // Fallback to wc_timezone_string.
        return 'UTC';
    }

}

if (!function_exists('is_commission_requested_for_withdrawals')) {

    /**
     * MVX commission requested for withdrawals
     * @return True/false bool
     */
    function is_commission_requested_for_withdrawals($commission_id) {
        if (!$commission_id)
            return false;
        $args = array(
            'post_type' => 'mvx_transaction',
            'posts_per_page' => -1,
            'post_status' => 'mvx_processing',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'commission_detail',
                    'value' => sprintf(':"%s";', $commission_id),
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'commission_detail',
                    'value' => sprintf(';i:%d;', $commission_id),
                    'compare' => 'LIKE'
                )
            )
        );
        $transactions = new WP_Query($args);
        $have_transactions = $transactions->get_posts();
        if ($have_transactions) {
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('get_mvx_vendor_order_shipping_method')) {

    /**
     * MVX vendor order shipping method
     * @param Order ID $order_id
     * @param Vendor ID $vendor_id
     * @return shipping method object on success or else false
     */
    function get_mvx_vendor_order_shipping_method($order_id, $vendor_id = '') {
        if (!$order_id)
            return false;
        $order = wc_get_order($order_id);
        if (!$order)
            return false;
        $vendor_id = !empty($vendor_id) ? $vendor_id : get_current_user_id();
        foreach ($order->get_shipping_methods() as $shipping_method) {
            $meta_data = $shipping_method->get_formatted_meta_data('');
            foreach ($meta_data as $meta_id => $meta) :
                if (!in_array($meta->key, array('vendor_id'), true)) {
                    continue;
                }

                if ($meta->value && $meta->value == $vendor_id)
                    return $shipping_method;

            endforeach;
        }
        return false;
    }

}

if (!function_exists('get_url_from_upload_field_value')) {

    /**
     * Returns image url from dc-wp-field upload value.
     *
     * @param string $type (default: 'image')
     * @param string/array $size (default: 'full')
     * @return string
     */
    function get_url_from_upload_field_value($value, $size = 'full', $protocol = false) {
        global $wp_version;
    
        if (!$value)
            return false;
        $attach_id = $image = '';
        if (!is_numeric($value)) {
            if (version_compare($wp_version, '4.0.0', '>=')) {
                $attach_id = attachment_url_to_postid($value);
            }else{
                $attach_id = get_attachment_id_by_url($value);
            }
            if ($attach_id == 0) { /* if no attachment id found from attachment url */
                $image = $value;
            }
        } else {
            $attach_id = $value;
        }
        $image_attributes = wp_get_attachment_image_src(absint($attach_id), $size, true);
        if (is_array($image_attributes) && count($image_attributes)) {
            $image = $image_attributes[0];
        }

        $image = apply_filters('mvx_image_url_from_upload_field_value_src', $image);
        if (!$protocol)
            return str_replace(array('https://', 'http://'), '//', $image);
        else
            return $image;
    }

}


if (!function_exists('mvx_get_latlng_distance')) {
    /*
     * calculates the distance between two points (given the latitude/longitude of those points).
     * lat1, lon1 = Latitude and Longitude of point 1
     * lat2, lon2 = Latitude and Longitude of point 2
     * unit = the unit you desire for results            
      where: 'M' is statute miles (default)
      'K' is kilometers
      'N' is nautical miles
     */

    function mvx_get_latlng_distance($lat1, $lon1, $lat2, $lon2, $unit = 'M') {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            do_action('mvx_get_latlng_distance', $lat1, $lon1, $lat2, $lon2, $unit, $dist);
            return $miles;
        }
    }

}


if (!function_exists('mvx_get_vendor_list_map_store')) {

    function mvx_get_vendor_list_map_store($vendors, $request) {
        global $MVX;
        $location = isset($request['locationText']) ? $request['locationText'] : '';
        $distance_type = isset($request['distanceSelect']) ? $request['distanceSelect'] : 'M';
        $radius = isset($request['radiusSelect']) ? $request['radiusSelect'] : '5';
        $map_stores = array('vendors' => array(), 'stores' => array());

        foreach ($vendors as $vendor_id) {
            $vendor = get_mvx_vendor($vendor_id);
            $store_lat = get_user_meta($vendor_id, '_store_lat', true);
            $store_lng = get_user_meta($vendor_id, '_store_lng', true);
            $image = $vendor->get_image() ? $vendor->get_image('image', array(125, 125)) : $MVX->plugin_url . 'assets/images/WP-stdavatar.png';
            $rating_info = mvx_get_vendor_review_info($vendor->term_id);
            $rating = round($rating_info['avg_rating'], 2);
            $count = intval($rating_info['total_rating']);
            if ($count > 0) {
                $rating_html = '<div itemprop="reviewRating" class="star-rating" style="float:none;">
        <span style="width:' . (( $rating / 5 ) * 100) . '%"><strong itemprop="ratingValue">' . $rating . '</strong> </span>
                </div>';
            } else {
                $rating_html = __('No Rating Yet', 'multivendorx');
            }
            $info_html = '<div class="info-store-wrapper"> 
                        <div class="store-img-wrap">
                            <img src="' . $image . '" class="info-store-img" /> 
                            <a href="' . $vendor->get_permalink() . '">' . __('Visit', 'multivendorx') . '</a>
                        </div>
                        <div class="info-store-header">
                            <p class="store-name">' . $vendor->page_title . '</p>
                            <ul>
                                <li>' . $rating_html . '</li>
                                <li>' . $vendor->user_data->data->user_email . '</li>
                                <li>' . $vendor->phone . '</li>
                            </ul>
                        </div> 
                    </div> ';

            if ((isset($request['mvx_vlist_center_lat']) && !empty($request['mvx_vlist_center_lat'])) && (isset($request['mvx_vlist_center_lng']) && !empty($request['mvx_vlist_center_lng']))) {
                if (!empty($radius) && ((!empty($store_lat) && !empty($store_lng)) || (!empty($location) && !empty($store_lat) && !empty($store_lng)))) {
                    $distance = mvx_get_latlng_distance($request['mvx_vlist_center_lat'], $request['mvx_vlist_center_lng'], $store_lat, $store_lng, $distance_type);
                    if ($distance < $radius) {
                        $map_stores['stores'][] = array(
                            'store_name' => $vendor->page_title,
                            'store_url' => $vendor->get_permalink(),
                            'location' => array('lat' => $store_lat, 'lng' => $store_lng),
                            'info_html' => apply_filters('mvx_vendor_list_map_vendor_info_html', $info_html, $vendor),
                        );
                        $map_stores['vendors'][] = $vendor_id;
                    }
                } elseif (empty($location) && empty($radius)) {
                    if (!empty($store_lat) && !empty($store_lng)) {
                        $map_stores['stores'][] = array(
                            'store_name' => $vendor->page_title,
                            'store_url' => $vendor->get_permalink(),
                            'location' => array('lat' => $store_lat, 'lng' => $store_lng),
                            'info_html' => apply_filters('mvx_vendor_list_map_vendor_info_html', $info_html, $vendor),
                        );
                    }
                    $map_stores['vendors'][] = $vendor_id;
                }
            } else {
                if (!empty($store_lat) && !empty($store_lng)) {
                    $map_stores['stores'][] = array(
                        'store_name' => $vendor->page_title,
                        'store_url' => $vendor->get_permalink(),
                        'location' => array('lat' => $store_lat, 'lng' => $store_lng),
                        'info_html' => apply_filters('mvx_vendor_list_map_vendor_info_html', $info_html, $vendor),
                    );
                }
                $map_stores['vendors'][] = $vendor_id;
            }
        }
        return apply_filters('mvx_get_vendor_list_map_store', $map_stores, $vendors, $request);
    }

}

if (!function_exists('mvx_get_vendor_specific_order_charge')) {

    function mvx_get_vendor_specific_order_charge($order) {
        $vendor_specific_admin_commision = array();
        if (!$order) {
            return $vendor_specific_admin_commision;
        }
        if (!is_object($order))
            $order = wc_get_order($order);
        if ($order) :
            $vendor_specific_admin_commision['order_total'] = $order->get_total();
            $items = $order->get_items('line_item');
            $marchants = array();
            foreach ($items as $order_item_id => $item) {
                $line_item = new WC_Order_Item_Product($item);
                $product_id = $item['product_id'];
                if ($product_id) {
                    $product_vendors = get_mvx_product_vendors($product_id);
                    if (!empty($product_vendors) && isset($product_vendors->term_id)) {
                        $marchants[] = $product_vendors->id;
                        $line_item_total = $line_item->get_total() + $line_item->get_total_tax(); // Item Total + Item Tax

                        if (isset($vendor_specific_admin_commision[$product_vendors->id]))
                            $vendor_specific_admin_commision[$product_vendors->id] = $vendor_specific_admin_commision[$product_vendors->id] + $line_item_total;
                        else
                            $vendor_specific_admin_commision[$product_vendors->id] = $line_item_total;
                    }else {
                        $post = get_post($product_id);
                        $marchants[] = $post->post_author;
                    }
                }
            }
            $vendor_specific_admin_commision['order_marchants'] = array_unique($marchants);

            $shipping_items = $order->get_items('shipping');
            foreach ($shipping_items as $shipping_item_id => $shipping_item) {
                $order_item_shipping = new WC_Order_Item_Shipping($shipping_item_id);
                $shipping_vendor_id = $order_item_shipping->get_meta('vendor_id', true);
                if ($shipping_vendor_id > 0) {
                    $shipping_item_total = $order_item_shipping->get_total() + $order_item_shipping->get_total_tax(); // Shipping Total + Shipping Tax

                    if (isset($vendor_specific_admin_commision[$shipping_vendor_id]))
                        $vendor_specific_admin_commision[$shipping_vendor_id] = $vendor_specific_admin_commision[$shipping_vendor_id] + $shipping_item_total;
                    else
                        $vendor_specific_admin_commision[$shipping_vendor_id] = $shipping_item_total;
                }
            }
        endif;

        return $vendor_specific_admin_commision;
    }

}

if (!function_exists('mvx_get_geocoder_components')) {

    function mvx_get_geocoder_components($components = array()) {
        $address_components = array(
            'street_number' => '',
            'street_name' => '',
            'street' => '',
            'premise' => '',
            'neighborhood' => '',
            'city' => '',
            'county' => '',
            'region_name' => '',
            'region_code' => '',
            'country_name' => '',
            'country_code' => '',
            'postcode' => '',
            'address' => '',
            'formatted_address' => '',
            'latitude' => '',
            'longitude' => '',
        );

        foreach ($components as $key => $component) {
            $component = (object) $component;
            $type = implode(",", $component->types);
            if ($type == 'street_number' && !empty($component->long_name)) {
                $address_components['street_number'] = $component->long_name;
            } elseif ($type == 'route' && !empty($component->long_name)) {
                $address_components['street_name'] = $component->long_name;
                $address_components['street'] = !empty($address_components['street_number']) ? $address_components['street_number'] . ' ' . $component->long_name : $component->long_name;
            } elseif ($type == 'subpremise' && !empty($component->long_name)) {
                $address_components['premise'] = $component->long_name;
            } elseif ($type == 'neighborhood,political' && !empty($component->long_name)) {
                $address_components['neighborhood'] = $component->long_name;
            } elseif ($type == 'locality,political' && !empty($component->long_name)) {
                $address_components['city'] = $component->long_name;
            } elseif ($type == 'administrative_area_level_2,political' && !empty($component->long_name)) {
                $address_components['city'] = $component->long_name;
            } elseif ($type == 'administrative_area_level_1,political') {
                $address_components['region_name'] = $component->long_name;
                $address_components['region_code'] = $component->short_name;
            } elseif ($type == 'country,political') {
                $address_components['country_name'] = $component->long_name;
                $address_components['country_code'] = $component->short_name;
            } elseif ($type == 'postal_code' && !empty($component->long_name)) {
                $address_components['postcode'] = $component->long_name;
            }
        }
        return $address_components;
    }

}


if (!function_exists('mvx_get_available_product_types')) {

    function mvx_get_available_product_types() {
        global $MVX;
        $available_product_types = array();
        $terms = get_terms('product_type');
        foreach ($terms as $term) {
            if ($term->name == 'simple' && mvx_is_product_type_avaliable('simple')) {
                $available_product_types['simple'] = __('Simple product', 'multivendorx');
                if (mvx_is_product_type_avaliable('virtual')) {
                    $available_product_types['virtual'] = __('Virtual product', 'multivendorx');
                }
                if (mvx_is_product_type_avaliable('downloadable')) {
                    $available_product_types['downloadable'] = __('Downloadable product', 'multivendorx');
                }
            } elseif ($term->name == 'variable' && mvx_is_product_type_avaliable('variable')) {
                $available_product_types['variable'] = __('Variable product', 'multivendorx');
            } elseif ($term->name == 'grouped' && mvx_is_product_type_avaliable('grouped')) {
                $available_product_types['grouped'] = __('Grouped product', 'multivendorx');
            } elseif ($term->name == 'external' && mvx_is_product_type_avaliable('external')) {
                $available_product_types['external'] = __('External/Affiliate product', 'multivendorx');
            } else {
                
            }
        }
        return apply_filters('mvx_get_available_product_types', $available_product_types, $terms);
    }

}

if (!function_exists('mvx_spmv_products_map')) {

    function mvx_spmv_products_map($data = array(), $action = 'insert') {
        global $wpdb;
        if ($data) {
            $table = $wpdb->prefix . 'mvx_products_map';
            if ($action == 'insert') {
                $wpdb->insert($table, $data);
                if (!isset($data['product_map_id'])) {
                    $inserted_id = $wpdb->insert_id;
                    $wpdb->update(esc_sql($table), array('product_map_id' => $inserted_id), array('product_id' => $data['product_id']));
                    return $inserted_id;
                } else {
                    return $data['product_map_id'];
                }
            } else {
                do_action('mvx_spmv_products_map_do_action', $action, $data);
                return false;
            }
        }
        return false;
    }

}

if (!function_exists('get_mvx_spmv_products_map_data')) {

    function get_mvx_spmv_products_map_data($map_id = '') {
        global $wpdb;
        $products_map_data = array();
        $results = $wpdb->get_results("SELECT product_map_id FROM {$wpdb->prefix}mvx_products_map");
        if ($results) {
            $product_map_ids = array_unique(wp_list_pluck($results, 'product_map_id'));
            foreach ($product_map_ids as $product_map_id) {
                $product_ids = $wpdb->get_results($wpdb->prepare("SELECT product_id FROM {$wpdb->prefix}mvx_products_map WHERE product_map_id=%d", $product_map_id));
                $products_map_data[$product_map_id] = wp_list_pluck($product_ids, 'product_id');
            }
        }
        if ($map_id) {
            return isset($products_map_data[$map_id]) ? $products_map_data[$map_id] : array();
        }
       
        return $products_map_data;
    }

}

if (!function_exists('do_mvx_spmv_set_object_terms')) {

    function do_mvx_spmv_set_object_terms($map_id = '') {
        global $MVX;
        if ($map_id) {
            $products_map_data_ids = get_mvx_spmv_products_map_data($map_id);
            $product_array_price = $top_rated_vendors = array();
            foreach ($products_map_data_ids as $product_id) {
                $product = wc_get_product($product_id);
                if ($product) {
                    $product_visibility_terms = get_the_terms($product->get_id(), 'product_visibility');
                    if ($product_visibility_terms) {
                        $term_taxonomy_ids = wp_list_pluck($product_visibility_terms, 'term_taxonomy_id');
                        $product_visibility_terms = wc_get_product_visibility_term_ids();
                        // Hide product_visibility_not_in products
                        if (in_array($product_visibility_terms['exclude-from-catalog'], $term_taxonomy_ids) || ('yes' === get_option('woocommerce_hide_out_of_stock_items') && in_array($product_visibility_terms['outofstock'], $term_taxonomy_ids)))
                            continue;

                        $product_array_price[$product->get_id()] = $product->get_price();
                        // top rated vendor
                        $product_vendor = get_mvx_product_vendors($product->get_id());
                        if ($product_vendor) {
                            $rating_val_array = mvx_get_vendor_review_info($product_vendor->term_id);
                            $rating = round($rating_val_array['avg_rating'], 1);
                            $top_rated_vendors[$product->get_id()] = $rating;
                        }
                    } else {
                        $product_array_price[$product->get_id()] = $product->get_price();
                        // top rated vendor
                        $product_vendor = get_mvx_product_vendors($product->get_id());
                        if ($product_vendor) {
                            $rating_val_array = mvx_get_vendor_review_info($product_vendor->term_id);
                            $rating = round($rating_val_array['avg_rating'], 1);
                            $top_rated_vendors[$product->get_id()] = $rating;
                        }
                    }
                }
            }
            $min_price_product = ($product_array_price) ? array_search(min($product_array_price), $product_array_price) : 0;
            $min_price_product = apply_filters('mvx_spmv_filtered_min_price_product', $min_price_product, $map_id);
            $max_price_product = ($product_array_price) ? array_search(max($product_array_price), $product_array_price) : 0;
            $max_price_product = apply_filters('mvx_spmv_filtered_max_price_product', $max_price_product, $map_id);
            $top_rated_vendor_product = ($top_rated_vendors) ? array_search(max($top_rated_vendors), $top_rated_vendors) : 0;
            $top_rated_vendor_product = apply_filters('mvx_spmv_filtered_top_rated_vendor_product', $top_rated_vendor_product, $map_id);
            $spmv_terms = $MVX->taxonomy->get_mvx_spmv_terms();
            if ($spmv_terms) {
                foreach ($spmv_terms as $term) {
                    if ($term->slug == 'min-price') {
                        $min_product_map_id = get_post_meta($min_price_product, '_mvx_spmv_map_id', true);
                        $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                        if($min_product_map_id == $map_id){
                            foreach ($products_map_data_ids as $product_id) {
                                if ($min_price_product != $product_id && in_array($product_id, $object_ids)) {
                                    wp_remove_object_terms($product_id, $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                                }
                            }
                        }
                        wp_set_object_terms($min_price_product, (int) $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy, true);
                    } elseif ($term->slug == 'max-price') {
                        $max_product_map_id = get_post_meta($max_price_product, '_mvx_spmv_map_id', true);
                        $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                        if($max_product_map_id == $map_id){
                            foreach ($products_map_data_ids as $product_id) {
                                if ($max_price_product != $product_id && in_array($product_id, $object_ids)) {
                                    wp_remove_object_terms($product_id, $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                                }
                            }
                        }
                        wp_set_object_terms($max_price_product, (int) $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy, true);
                    } elseif ($term->slug == 'top-rated-vendor') {
                        $top_rated_vendor_map_id = get_post_meta($top_rated_vendor_product, '_mvx_spmv_map_id', true);
                        $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                        if($top_rated_vendor_map_id == $map_id){
                            foreach ($products_map_data_ids as $product_id) {
                                if ($top_rated_vendor_product != $product_id && in_array($product_id, $object_ids)) {
                                    wp_remove_object_terms($product_id, $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                                }
                            }
                        }
                        wp_set_object_terms($top_rated_vendor_product, (int) $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy, true);
                    } else {
                        do_action('mvx_spmv_set_object_terms_handler', $term, $map_id, $products_map_data_ids);
                    }
                }
            }
        } else {
            $products_map_data = get_mvx_spmv_products_map_data();
            if ($products_map_data) {
                foreach ($products_map_data as $product_map_id => $product_ids) {
                    $product_array_price = $top_rated_vendors = array();
                    foreach ($product_ids as $product_id) {
                        $product = wc_get_product($product_id);
                        if ($product) {
                            $product_visibility_terms = get_the_terms($product->get_id(), 'product_visibility');
                            if ($product_visibility_terms) {
                                $term_taxonomy_ids = wp_list_pluck($product_visibility_terms, 'term_taxonomy_id');
                                $product_visibility_terms = wc_get_product_visibility_term_ids();
                                // Hide product_visibility_not_in products
                                if (in_array($product_visibility_terms['exclude-from-catalog'], $term_taxonomy_ids) || ('yes' === get_option('woocommerce_hide_out_of_stock_items') && in_array($product_visibility_terms['outofstock'], $term_taxonomy_ids)))
                                    continue;

                                $product_array_price[$product->get_id()] = $product->get_price();
                                // top rated vendor
                                $product_vendor = get_mvx_product_vendors($product->get_id());
                                if ($product_vendor) {
                                    $rating_val_array = mvx_get_vendor_review_info($product_vendor->term_id);
                                    $rating = round($rating_val_array['avg_rating'], 1);
                                    $top_rated_vendors[$product->get_id()] = $rating;
                                }
                            } else {
                                $product_array_price[$product->get_id()] = $product->get_price();
                                // top rated vendor
                                $product_vendor = get_mvx_product_vendors($product->get_id());
                                if ($product_vendor) {
                                    $rating_val_array = mvx_get_vendor_review_info($product_vendor->term_id);
                                    $rating = round($rating_val_array['avg_rating'], 1);
                                    $top_rated_vendors[$product->get_id()] = $rating;
                                }
                            }
                        }
                    }
                    $min_price_product = ($product_array_price) ? array_search(min($product_array_price), $product_array_price) : 0;
                    $min_price_product = apply_filters('mvx_spmv_filtered_min_price_product', $min_price_product, $product_map_id);
                    $max_price_product = ($product_array_price) ? array_search(max($product_array_price), $product_array_price) : 0;
                    $max_price_product = apply_filters('mvx_spmv_filtered_max_price_product', $max_price_product, $product_map_id);
                    $top_rated_vendor_product = ($top_rated_vendors) ? array_search(max($top_rated_vendors), $top_rated_vendors) : 0;
                    $top_rated_vendor_product = apply_filters('mvx_spmv_filtered_top_rated_vendor_product', $top_rated_vendor_product, $product_map_id);
                    $spmv_terms = $MVX->taxonomy->get_mvx_spmv_terms(array('orderby' => 'id'));
                    if ($spmv_terms) {
                        foreach ($spmv_terms as $term) {
                            if ($term->slug == 'min-price') {
                                $min_product_map_id = get_post_meta($min_price_product, '_mvx_spmv_map_id', true);
                                $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                                if($min_product_map_id == $product_map_id){
                                    foreach ($product_ids as $product_id) {
                                        if ($min_price_product != $product_id && in_array($product_id, $object_ids)) {
                                            wp_remove_object_terms($product_id, $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                                        }
                                    }
                                }
                                wp_set_object_terms($min_price_product, (int) $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy, true);
                            } elseif ($term->slug == 'max-price') {
                                $max_product_map_id = get_post_meta($max_price_product, '_mvx_spmv_map_id', true);
                                $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                                if($max_product_map_id == $product_map_id){
                                    foreach ($product_ids as $product_id) {
                                        if ($max_price_product != $product_id && in_array($product_id, $object_ids)) {
                                            wp_remove_object_terms($product_id, $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                                        }
                                    }
                                }
                                wp_set_object_terms($max_price_product, (int) $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy, true);
                            } elseif ($term->slug == 'top-rated-vendor') {
                                $top_rated_vendor_map_id = get_post_meta($top_rated_vendor_product, '_mvx_spmv_map_id', true);
                                $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->taxonomy_name);
                                if($top_rated_vendor_map_id == $product_map_id){
                                    foreach ($product_ids as $product_id) {
                                        if ($top_rated_vendor_product != $product_id && in_array($product_id, $object_ids)) {
                                            wp_remove_object_terms($product_id, $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                                        }
                                    }
                                }
                                wp_set_object_terms($top_rated_vendor_product, (int) $term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy, true);
                            } else {
                                do_action('mvx_spmv_set_object_terms_handler', $term, $map_id, $products_map_data);
                            }
                        }
                    }
                }
            }
        }
    }

}

if (!function_exists('get_mvx_spmv_excluded_products_map_data')) {

    function get_mvx_spmv_excluded_products_map_data() {
        global $MVX, $wpdb;
        $spmv_terms = $MVX->taxonomy->get_mvx_spmv_terms(array('orderby' => 'id'));
        if ($spmv_terms) {
            $exclude_spmv_products = array();
            foreach ($spmv_terms as $term) {
                if ($term->slug == 'min-price') {
                    $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                    if ($object_ids) {
                        foreach ($object_ids as $product_id) {
                            if ($product_id && get_post_status($product_id) == 'publish') {
                                $product_map_id = get_post_meta($product_id, '_mvx_spmv_map_id', true);
                                if ($product_map_id) {
                                    $products_map_data_ids = get_mvx_spmv_products_map_data($product_map_id);
                                    $excludes = array_diff($products_map_data_ids, array($product_id));
                                    if ($excludes) {
                                        foreach ($excludes as $id) {
                                            $exclude_spmv_products[$term->slug][] = $id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } elseif ($term->slug == 'max-price') {
                    $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                    if ($object_ids) {
                        foreach ($object_ids as $product_id) {
                            if ($product_id && get_post_status($product_id) == 'publish') {
                                $product_map_id = get_post_meta($product_id, '_mvx_spmv_map_id', true);
                                if ($product_map_id) {
                                    $products_map_data_ids = get_mvx_spmv_products_map_data($product_map_id);
                                    $excludes = array_diff($products_map_data_ids, array($product_id));

                                    if ($excludes) {
                                        foreach ($excludes as $id) {
                                            $exclude_spmv_products[$term->slug][] = $id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } elseif ($term->slug == 'top-rated-vendor') {
                    $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                    if ($object_ids) {
                        foreach ($object_ids as $product_id) {
                            if ($product_id && get_post_status($product_id) == 'publish') {
                                $product_map_id = get_post_meta($product_id, '_mvx_spmv_map_id', true);
                                if ($product_map_id) {
                                    $products_map_data_ids = get_mvx_spmv_products_map_data($product_map_id);
                                    $excludes = array_diff($products_map_data_ids, array($product_id));

                                    if ($excludes) {
                                        foreach ($excludes as $id) {
                                            $exclude_spmv_products[$term->slug][] = $id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $object_ids = get_objects_in_term($term->term_id, $MVX->taxonomy->mvx_spmv_taxonomy);
                    do_action('mvx_spmv_term_exclude_products_handler', $exclude_spmv_products, $object_ids, $term);
                }
            }

            return apply_filters('mvx_spmv_term_exclude_products', $exclude_spmv_products, $spmv_terms);
        }
        return false;
    }

}

if (!function_exists('mvx_get_available_commission_types')) {

    function mvx_get_available_commission_types($default = array()) {
        global $MVX;
        $available_commission_types = array();
        if ($default)
            $available_commission_types = $default;
        $available_commission_types['fixed'] = __('Fixed Amount', 'multivendorx');
        $available_commission_types['percent'] = __('Percentage', 'multivendorx');
        $available_commission_types['fixed_with_percentage'] = __('%age + Fixed (per transaction)', 'multivendorx');
        $available_commission_types['fixed_with_percentage_qty'] = __('%age + Fixed (per unit)', 'multivendorx');
        $available_commission_types['commission_by_product_price'] = __('Commission By Product Price', 'multivendorx');
        $available_commission_types['commission_by_purchase_quantity'] = __('Commission By Purchase Quantity', 'multivendorx');
        $available_commission_types['fixed_with_percentage_per_vendor'] = __('%age + Fixed (per vendor)', 'multivendorx');
        
        return apply_filters('mvx_get_available_commission_types', $available_commission_types);
    }

}

if (!function_exists('mvx_list_categories')) {

    function mvx_list_categories($args = array()) {
        global $wp_version;
        $defaults = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => true,
            'exclude' => array(),
            'exclude_tree' => array(),
            'include' => array(),
            'number' => '',
            'fields' => 'all',
            'slug' => '',
            'parent' => 0,
            'hierarchical' => true,
            'child_of' => 0,
            'childless' => false,
            'get' => '',
            'name__like' => '',
            'description__like' => '',
            'pad_counts' => false,
            'offset' => '',
            'search' => '',
            'show_count' => false,
            'taxonomy' => 'product_cat',
            'show_option_none' => __('No categories', 'multivendorx'),
            'style' => 'list',
            'selected' => '',
            'list_class' => '',
            'cat_link' => false,
            'cache_domain' => 'core',
            'html_list' => false,
            'echo' => false
        );

        $r = apply_filters( 'mvx_before_list_categories_query_args', wp_parse_args($args, $defaults), $args );

        $taxonomy = $r['taxonomy'];

        if (!taxonomy_exists($taxonomy)) {
            return false;
        }

        if (version_compare($wp_version, '4.5.0', '>=')) {
            // Since 4.5.0, taxonomies should be passed via the ‘taxonomy’ argument in the $args array
            $categories = get_terms($r);
        } else {
            // Prior to 4.5.0, the first parameter of get_terms() was a taxonomy or list of taxonomies
            $categories = get_terms($taxonomy, $r);
        }

        if (is_wp_error($categories)) {
            $categories = array();
        } else {
            $categories = (array) $categories;
            foreach (array_keys($categories) as $k) {
                _make_cat_compat($categories[$k]);
            }
        }
        // for No html output
        if (!$r['html_list'])
            return $categories;

        $output = '';
        $list_class = apply_filters('mvx_list_categories_list_style_classes', $r['list_class']);
        if (empty($categories)) {
            if (!empty($r['show_option_none'])) {
                if ('list' == $r['style']) {
                    $output .= '<li class="' . $list_class . ' cat-item-none">' . $r['show_option_none'] . '</li>';
                } else {
                    $output .= $r['show_option_none'];
                }
            }
        } else {
            foreach ($categories as $key => $cat) {
                $list_class = empty($r['list_class']) ? 'cat-item cat-item-' . $cat->term_id : $r['list_class'] . ' cat-item cat-item-' . $cat->term_id;
                $child_terms = get_term_children($cat->term_id, $taxonomy);
                // show count
                $inner_html = '';
                if ($r['show_count'] || $child_terms) {
                    $inner_html .= ' <span class="pull-right">';
                    if ($r['show_count']) {
                        $inner_html .= '<span class="count ' . apply_filters('mvx_list_categories_show_count_style_classess', 'badge badge-primary badge-pill ', $cat) . '">' . $cat->count . '</span>';
                    }

                    if ($child_terms) {
                        $list_class .= ' has-children';
                        //$inner_html .= ' <i class="mvx-font ico-right-arrow-icon"></i>';
                    }
                    $inner_html .= '</span>';
                }
                // has selected term
                if(!empty($r['selected']) && $cat->term_id == $r['selected'] ) $list_class .= ' active';
                $list_class = apply_filters('mvx_list_categories_list_style_classes', $list_class, $cat);
                $link = apply_filters('mvx_list_categories_get_term_link', ($r['cat_link']) ? $r['cat_link'] : get_term_link($cat->term_id, $taxonomy), $cat, $r);
                if ('list' == $r['style']) {
                    //<li><a href="#"><span>Grocery & Gourmet Foods</span></a></li>
                    $output .= "<li class='$list_class' data-term-id='$cat->term_id' data-taxonomy='$taxonomy'><a href='$link'><span>" . apply_filters('mvx_list_categories_term_name', $cat->name, $cat) . "</span></a>$inner_html</li>";
                } else {
                    $output .= "<a class='$list_class' href='$link' data-term-id='$cat->term_id' data-taxonomy='$taxonomy'>" . apply_filters('mvx_list_categories_term_name', $cat->name, $cat) . "$inner_html</a>";
                }
            }
        }

        /**
         * Filters the HTML output of a taxonomy list.
         *
         * @since 3.2.0
         *
         * @param string $output HTML output.
         * @param array  $r   An array of taxonomy-listing arguments.
         */
        $html = apply_filters('mvx_list_categories', $output, $r);

        if ($r['echo']) {
            echo $html;
        } else {
            return $html;
        }
    }

}

if (!function_exists('mvx_get_shipping_zone')) {

    function mvx_get_shipping_zone($zoneID = '', $vendor_id = '') {
        global $MVX;
        $zones = array();
        if( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        if ( isset($zoneID) && $zoneID != '' ) {
                $zones = MVX_Shipping_Zone::get_zone($zoneID);
        } else {
                $zones = MVX_Shipping_Zone::get_zones($vendor_id);
        }
        return $zones;
    }

}

if (!function_exists('mvx_get_shipping_methods')) {

    function mvx_get_shipping_methods() {
        $vendor_shippings = array();
        foreach ( WC()->shipping->load_shipping_methods() as $method ) {
            if ( ! array_key_exists( $method->id, apply_filters( 'mvx_vendor_shipping_methods', array(
                'flat_rate' => __('Flat Rate', 'multivendorx'),
                'local_pickup' => __('Local Pickup', 'multivendorx'),
                'free_shipping' => __('Free Shipping', 'multivendorx')
            ) ) ) ) {
                    continue;
            }
            $vendor_shippings[$method->id] = $method;
        }
        return $vendor_shippings;
    }

}

if (!function_exists('mvx_convert_normal_string_to_array')) {

    function mvx_convert_normal_string_to_array($a) {
        return (array) $a;
    }

}

if (!function_exists('mvx_state_key_alter')) {

    function mvx_state_key_alter(&$value, $key) {
        $value = array_combine(
                array_map(function($k) use ($key) {
                    return $key . ':' . $k;
                }, array_keys($value)), $value
        );
    }

}

if (!function_exists('get_vendor_shipping_classes')) {

    function get_vendor_shipping_classes() {
        $vendor_user_id = apply_filters('mvx_dashboard_shipping_vendor', get_current_vendor_id());

        $shipping_classes = array();

        if ($vendor_user_id) {
            $shipping_class_id = get_user_meta($vendor_user_id, 'shipping_class_id', true);
            if ($shipping_class_id) {
                $shipping_classes[] = get_term($shipping_class_id);
            }
        }

        return $shipping_classes;
    }

}

//------------------ Afm core module--------------------//

if ( ! function_exists( 'current_vendor_can' ) ) {

    /**
     * Check if vendor has a certain capability 
     * @param string | ARRAY_N $capability 
     * @return boolean TRUE only if all passed capabilities are true for current vendor
     */
    function current_vendor_can( $capability ) {
        $current_vendor_id = '';
        if(is_user_mvx_vendor(get_current_user_id())) $current_vendor_id = get_current_user_id();
        
        if ( ! $current_vendor_id || empty( $capability ) ) {
            return false;
        }
        $vendor_role = get_role( 'dc_vendor' );
        $capabilities = isset( $vendor_role->capabilities ) ? $vendor_role->capabilities : array();

        if ( is_array( $capability ) ) {
            foreach ( $capability as $cap ) {
                if ( ! array_key_exists( $cap, $capabilities ) || ! $capabilities[$cap] ) {
                    return false;
                }
            }
            return true;
        }
        return array_key_exists( $capability, $capabilities ) && $capabilities[$capability];
    }

}

if ( ! function_exists( 'mvx_is_allowed_vendor_shipping' ) ) {

    function mvx_is_allowed_vendor_shipping() {
        global $MVX;
        return is_mvx_shipping_module_active() && $MVX->vendor_caps->vendor_payment_settings('give_shipping');
    }

}

if ( ! function_exists( 'mvx_get_post_permalink_html' ) ) {

    function mvx_get_post_permalink_html( $id ) {
        if ( ! $id )
            return '';
        $post = get_post( $id );
        if ( ! $post )
            return '';

        list($permalink, $post_name) = mvx_get_post_permalink( $post->ID );

        $view_link = false;
        $preview_target = '';

        if ( current_user_can( 'read_post', $post->ID ) ) {
            if ( 'draft' === $post->post_status || empty( $post->post_name ) ) {
                $view_link = get_preview_post_link( $post );
                $preview_target = " target='wp-preview-{$post->ID}'";
            } else {
                if ( 'publish' === $post->post_status || 'attachment' === $post->post_type ) {
                    $view_link = get_permalink( $post );
                } else {
                    // Allow non-published (private, future) to be viewed at a pretty permalink, in case $post->post_name is set
                    $view_link = str_replace( array( '%pagename%', '%postname%' ), $post->post_name, $permalink );
                }
            }
        }

        if ( mb_strlen( $post_name ) > 34 ) {
            $post_name_abridged = mb_substr( $post_name, 0, 16 ) . '&hellip;' . mb_substr( $post_name, -16 );
        } else {
            $post_name_abridged = $post_name;
        }
        $post_type = get_post_type( $post );
        $post_name_html = '<span id="afm-' . $post_type . '-name">' . esc_html( $post_name_abridged ) . '</span>';
        $display_link = str_replace( array( '%pagename%', '%postname%' ), $post_name_html, esc_html( urldecode( $permalink ) ) );

        $return = '';
        if ( $post_type === 'shop_coupon' ) {
            $type = __('coupon', 'multivendorx');
        } else {
            $type = __('product', 'multivendorx');
        }
        if ( false === strpos( $view_link, 'preview=true' ) ) {
            $return .= '<label>' . __( sprintf( __('View %s', 'multivendorx'), $type ) ) . ":</label>\n";
        } else {
            $return .= '<label>' . __( sprintf( __('View %s', 'multivendorx'), $type ) ) . ":</label>\n";
        }
        $return .= '<span id="afm-' . $post_type . '-permalink"><a href="' . esc_url( $view_link ) . '"' . $preview_target . '>' . $display_link . "</a></span>";

        return $return;
    }

}

if ( ! function_exists( 'mvx_get_post_permalink' ) ) {

    function mvx_get_post_permalink( $id ) {
        $post = get_post( $id );
        if ( ! $post )
            return array( '', '' );

        $original_status = $post->post_status;
        $original_date = $post->post_date;
        $original_name = $post->post_name;

        // Hack: get_permalink() would return ugly permalink for drafts, so we will fake that our post is published.
        if ( in_array( $post->post_status, array( 'draft', 'pending', 'future' ) ) ) {
            $post->post_status = 'publish';
            $post->post_name = sanitize_title( $post->post_name ? $post->post_name : $post->post_title, $post->ID );
        }

        $post->post_name = wp_unique_post_slug( $post->post_name, $post->ID, $post->post_status, $post->post_type, $post->post_parent );

        $post->filter = 'sample';

        $permalink = get_permalink( $post, true );

        // Replace custom post_type Token with generic pagename token for ease of use.
        $permalink = str_replace( "%$post->post_type%", '%pagename%', $permalink );
        $permalink = array( $permalink, $post->post_name );

        $post->post_status = $original_status;
        $post->post_date = $original_date;
        $post->post_name = $original_name;
        unset( $post->filter );
        return $permalink;
    }

}

if ( ! function_exists( 'mvx_default_product_types' ) ) {

    function mvx_default_product_types() {
        return mvx_is_module_active('simple') ? array(
            'simple'   => __( 'Simple product', 'multivendorx' ),
        ) : array();
    }

}

if ( ! function_exists( 'mvx_get_product_types' ) ) {

    function mvx_get_product_types() {
        return apply_filters( 'mvx_product_type_selector', mvx_default_product_types() );
    }
}

if ( ! function_exists( 'mvx_is_allowed_product_type' ) ) {
    /*
     * @params MIXED
     * string or array 
     * 
     */

    function mvx_is_allowed_product_type() {
        $product_types = mvx_get_product_types();
        foreach ( func_get_args() as $arg ) {
            //typecast normal string params to array
            $a_arg = (array) $arg;
            foreach ( $a_arg as $key ) {
                if ( apply_filters( 'mvx_is_allowed_product_type_check', array_key_exists( $key, $product_types ), $key, $product_types ) ) {
                    return true;
                }
            }
        }
        return false;
    }

}

if ( ! function_exists( 'get_current_vendor_shipping_classes' ) ) {

    function get_current_vendor_shipping_classes() {
        $current_vendor_id = get_current_user_id();
        $shipping_options = array();
        if ( $current_vendor_id ) {
            $shipping_classes = get_terms( 'product_shipping_class', array( 'hide_empty' => 0 ) );
            foreach ( $shipping_classes as $shipping_class ) {
                if ( apply_filters( 'mvx_allowed_only_vendor_shipping_class', true ) ) {
                    $vendor_id = absint( get_term_meta( $shipping_class->term_id, 'vendor_id', true ) );
                    if ( $vendor_id === $current_vendor_id ) {
                        $shipping_options[$shipping_class->term_id] = $shipping_class->name;
                    }
                } else {
                    $shipping_options[$shipping_class->term_id] = $shipping_class->name;
                }
            }
        }
        return apply_filters( 'current_vendor_shipping_classes', $shipping_options );
    }

}

if ( ! function_exists( 'mvx_get_product_terms_HTML' ) ) {

    function mvx_get_product_terms_HTML( $taxonomy, $id = null, $add_cap = false, $hierarchical = true ) {
        $terms = array();
        $product_terms = get_terms( apply_filters( "mvx_get_product_terms_{$taxonomy}_query_args", array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'orderby'    => 'name',
            'parent'     => 0,
            'fields'     => 'id=>name',
            ) ) );
        if ( ( empty( $product_terms ) || is_wp_error( $product_terms ) ) && ! $add_cap ) {
            return false;
        }
        $term_id_list = wp_get_post_terms( $id, $taxonomy, array( 'fields' => 'ids' ) );
        if ( ! empty( $term_id_list ) && ! is_wp_error( $term_id_list ) ) {
            $terms = $term_id_list;
        } else {
            $terms = array();
        }
        $terms = isset( $_POST['tax_input'][$taxonomy] ) ? wp_parse_id_list( $_POST['tax_input'][$taxonomy] ) : $terms;
        $terms = apply_filters( 'mvx_get_product_terms_html_selected_terms', $terms, $taxonomy, $id );
        if ( $hierarchical ) {
            return generate_hierarchical_taxonomy_html( $taxonomy, $product_terms, $terms, $add_cap );
        } else {
            return generate_non_hierarchical_taxonomy_html( $taxonomy, $product_terms, $terms, $add_cap );
        }
    }

}
if ( ! function_exists( 'generate_non_hierarchical_taxonomy_html' ) ) {

    function generate_non_hierarchical_taxonomy_html( $taxonomy, $product_terms, $seleted_terms, $add_cap ) {
        $html = '';
        if ( ! empty( $product_terms ) || $add_cap ) {
            ob_start();
            ?>
            <select multiple = "multiple" data-placeholder = "<?php esc_attr_e( 'Select', 'multivendorx' ); ?>" class = "multiselect form-control <?php echo $taxonomy; ?>" name = "tax_input[<?php echo $taxonomy; ?>][]">
                <?php
                foreach ( $product_terms as $term_id => $term_name ) {
                    echo '<option value="' . $term_id . '" ' . selected( in_array( $term_id, $seleted_terms ), true, false ) . '>' . $term_name . '</option>';
                }
                ?>
            </select>
            <?php
            $html = ob_get_clean();
        }
        return $html;
    }

}

if ( ! function_exists( 'generate_hierarchical_taxonomy_html' ) ) {

    function generate_hierarchical_taxonomy_html( $taxonomy, $terms, $post_terms, $add_cap, $level = 0, $max_depth = 2 ) {
        $max_depth = apply_filters( 'mvx_generate_hierarchical_taxonomy_html_max_depth', 5, $taxonomy, $terms, $post_terms, $level );
        $tax_html_class = ($level == 0) ? 'taxonomy-widget ' . $taxonomy . ' level-' . $level : '';
        $tax_html = '<ul class="'.$tax_html_class.'">';
        foreach ( $terms as $term_id => $term_name ) {
            $child_html = '';
            if ( $max_depth > $level ) {
                $child_terms = get_terms( array(
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'parent'     => absint( $term_id ),
                    'fields'     => 'id=>name',
                    ) );
                if ( ! empty( $child_terms ) && ! is_wp_error( $child_terms ) ) {
                    $child_html = generate_hierarchical_taxonomy_html( $taxonomy, $child_terms, $post_terms, $add_cap, $level + 1 );
                }
            }

            $tax_html .= '<li><label><input type="checkbox" name="tax_input[' . $taxonomy . '][]" value="' . $term_id . '" ' . checked( in_array( $term_id, $post_terms ), true, false ) . '> ' . $term_name . $child_html . '</label></li>';
        }
        $tax_html .= '</ul>';
        if ( $add_cap ) {
            $label = '';
            switch ( $taxonomy ) {
                case 'product_cat':
                    $label = __( 'Add new product category', 'multivendorx' );
                    break;
                default:
                    $label = __( 'Add new item', 'multivendorx' );
            }
            $tax_html .= '<a href="#">' . $label . '</a>';
        }
        return $tax_html;
    }

}


if ( ! function_exists( 'mvx_generate_term_breadcrumb_html' ) ) {

    function mvx_generate_term_breadcrumb_html( $args = array() ) {
        $args = wp_parse_args( $args, apply_filters( 'mvx_term_breadcrumb_defaults', array(
                'term_id'               => 0,
                'term_list'             => array(),
                'taxonomy'              => 'product_cat',
                'delimiter'             => '&nbsp;&#47;&nbsp;',
                'wrap_before'           => '<ul class="mvx-breadcrumb breadcrumb">',
                'wrap_after'            => '</ul>',
                'wrap_child_before'     => '<li>',
                'wrap_child_after'      => '</li>',
                'link'                  => false,
                'before'                => '',
                'after'                 => '',
                'echo'                  => false,
        ) ) );
        if(!$args['term_list']){
            $hierarchy = get_ancestors( $args['term_id'], $args['taxonomy'] );
            $hierarchy = array_reverse($hierarchy);
            $hierarchy[] = $args['term_id'];
        }else{
            $hierarchy = $args['term_list'];
        }
        $breadcrumbs = array();
        foreach ( $hierarchy as $id ) {
            $term = get_term( $id, $args['taxonomy'] );
            $breadcrumbs[]= array( $term->name, apply_filters( 'mvx_generate_term_breadcrumb_crumb_link', $args['link'], $term, $args) );
        }
        
        $html = '';
        if ( ! empty( $breadcrumbs ) ) {
            $html .= $args['wrap_before'];
            foreach ( $breadcrumbs as $key => $crumb ) {
                $html .= $args['wrap_child_before'];
                $html .= $args['before'];
                if ( ! empty( $crumb[1] ) && sizeof( $breadcrumbs ) !== $key + 1 ) {
                    $html .= '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
                } else {
                    $html .= esc_html( $crumb[0] );
                }
                $html .= $args['after'];
                if ( sizeof( $breadcrumbs ) !== $key + 1 ) {
                    $html .= $args['delimiter'];
                }
                $html .= $args['wrap_child_after'];
            }
            $html .= $args['wrap_after'];
        }
        
        $html = apply_filters( 'mvx_generate_term_breadcrumb_html', $html, $args );
        
        if($args['echo']){
            echo $html;
        }else{
            return $html;
        }
    }
}

if ( ! function_exists( 'is_product_mvx_spmv' ) ) {

    function is_product_mvx_spmv( $product_id = '' ) {
        $is_mvx_spmv_product = false;
        if($product_id){
            $is_mvx_spmv_product = (get_post_meta(absint($product_id), '_mvx_spmv_product', true)) ? true : false;
        }
        return apply_filters( 'mvx_is_product_in_spmv', $is_mvx_spmv_product, $product_id );
    }
}

if ( ! function_exists( 'get_mvx_different_terms_hierarchy' ) ) {

    function get_mvx_different_terms_hierarchy( $term_list = array(), $taxonomy = 'product_cat' ) {
        $terms_hierarchy_arr = array();
        if($term_list) {
            $flag = 0;
            foreach ($term_list as $term_id) {
                $hierarchy = get_ancestors( $term_id, $taxonomy );
                if($hierarchy){
                    if(!array_key_exists(end($hierarchy), $terms_hierarchy_arr)){
                        $terms_hierarchy_arr[end($hierarchy)] = $term_id;
                    }elseif(array_key_exists(end($hierarchy), $terms_hierarchy_arr) && $terms_hierarchy_arr[end($hierarchy)] < $term_id){ // if terms has same parent
                        $terms_hierarchy_arr[end($hierarchy)] = $term_id;
                    }
                }elseif(!array_key_exists($term_id, $terms_hierarchy_arr)){
                    $terms_hierarchy_arr[$flag] = $term_id;
                }
                $flag++;
            }
            // check same hierarchy duplication
            foreach ($terms_hierarchy_arr as $term_id) {
                if(array_key_exists($term_id, $terms_hierarchy_arr)){
                    $key = array_search($term_id, $terms_hierarchy_arr);
                    unset($terms_hierarchy_arr[$key]);
                }
            }
            return $terms_hierarchy_arr;
        }
        return false;
    }
}

if ( ! function_exists( 'is_current_vendor_coupon' ) ) {

    function is_current_vendor_coupon( $coupon_id = 0, $vendor_id = 0 ) {
        global $MVX;
        if ( ! $vendor_id ) {
            $vendor_id = get_current_user_id();
        }

        if ( ! ( $coupon_id && $vendor_id && is_user_mvx_vendor( $vendor_id ) ) ) {
            return false;
        }

        $coupon = new WC_Coupon( $coupon_id );
        $coupon_post = get_post( $coupon_id );
        $coupon_author_id = absint( $coupon_post->post_author );

        if ( ! $coupon || $vendor_id !== $coupon_author_id ) {
            return false;
        }
        //the coupon is valid and belongs to the current vendor
        return true;
    }

}

if ( ! function_exists( 'has_vendor_config_shipping_methods' ) ) {

    function has_vendor_config_shipping_methods() {
        $vendor_shipping_zones = mvx_get_shipping_zone();
        $flag = array();
        if( $vendor_shipping_zones ) :
            foreach ( $vendor_shipping_zones as $zone ) {
                $vendor_shipping_methods = $zone['shipping_methods'];
                if( !empty( $vendor_shipping_methods ) ) {
                    $flag[] = 'true';
                }
            }
        endif;
        return (in_array( 'true', $flag) ) ? true : false;
    }

}

if ( ! function_exists( 'mvx_get_price_to_display' ) ) {
    /**
     * Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting.
     *
     * @since  3.3.1
     * @param  WC_Product $product WC_Product object.
     * @param  array      $args Optional arguments to pass product quantity and price.
     * @return float
     */
    function mvx_get_price_to_display( $product, $args = array() ) {
        $args = wp_parse_args(
                $args, array(
                        'qty'   => 1,
                        'price' => $product->get_price(),
                )
        );

        $price = $args['price'];
        $qty   = $args['qty'];
        $price_html = '';
        if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) && $product->is_taxable() && wc_prices_include_tax() ) {
            $price_html = wc_price( $price * $qty );
        }else{
            $price_html = $product->get_price_html();
        }
        return apply_filters( 'mvx_get_price_to_display', $price_html, $product, $args );
    }
}

if (!function_exists('is_mvx_version_less_3_4_0')) {

    /**
     * Check Multivendor X current version is less than 3.4.0
     *
     * @return boolean true/false
     */
    function is_mvx_version_less_3_4_0() {
        $current_mvx = mvx_get_option('dc_product_vendor_plugin_db_version');
        return version_compare( $current_mvx, '3.4.0', '<' );
    }
}

if (!function_exists('mvx_get_commission_statuses')) {
    /**
     * Get all commission statuses.
     *
     * @since 3.4.0
     * @return array
     */
    function mvx_get_commission_statuses() {
        $commission_statuses = array(
            'paid'              => __( 'Paid', 'multivendorx' ),
            'unpaid'            => __( 'Unpaid', 'multivendorx' ),
            'refunded'          => __( 'Refunded', 'multivendorx' ),
            'partial_refunded'  => __( 'Partial refunded', 'multivendorx' ),
            'reverse'           => __( 'Reverse', 'multivendorx' ),
        );
        return apply_filters( 'mvx_get_commission_statuses', $commission_statuses );
    }
}

if (!function_exists('mvx_get_product_link')) {
    /**
     * Get product link.
     *
     * @since 3.4.0
     * @param integer $product_id
     * @return string url
     */
    function mvx_get_product_link( $product_id ) {
        $link = '';
        if ( current_user_can('edit_published_products') && get_mvx_global_settings('is_edit_delete_published_product') ) {
            $link = esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product'), $product_id));
        }
        return apply_filters('mvx_get_product_link', $link, $product_id);
    }
}

if (!function_exists('get_mvx_default_payment_gateways')) {
    /**
     * Get default payment gateways.
     *
     * @since 3.4.0
     * @return array payment gateways
     */
    function get_mvx_default_payment_gateways() {
        $default_gateways = apply_filters('automatic_payment_method', 
        array(
            'paypal_masspay' => __('PayPal Masspay', 'multivendorx'), 
            'paypal_payout' => __('Paypal Payout', 'multivendorx'), 
            'stripe_masspay' => __('Stripe Connect', 'multivendorx'), 
            'direct_bank' => __('Direct Bank Transfer', 'multivendorx'),
            )
        );
        return apply_filters( 'mvx_default_payment_gateways', $default_gateways );
    }
}

if (!function_exists('get_mvx_available_payment_gateways')) {
    /**
     * Get available payment gateways.
     *
     * @since 3.4.0
     * @return array payment gateways
     */
    function get_mvx_available_payment_gateways() {
        $available_gateways = array();
        if (mvx_is_module_active('paypal-masspay')) {
            $available_gateways['paypal_masspay'] = __('PayPal Masspay', 'multivendorx');
        }
        
        if (mvx_is_module_active('paypal-payout')) {
            $available_gateways['paypal_payout'] = __('PayPal Payout', 'multivendorx');
        }
       
        if (mvx_is_module_active('stripe-connect')) {
            $available_gateways['stripe_masspay'] = __('Stripe Connect', 'multivendorx');
        }
        
        if (mvx_is_module_active('bank-payment')) {
            $available_gateways['direct_bank'] = __('Direct Bank', 'multivendorx');
        }
        return apply_filters( 'mvx_available_payment_gateways', $available_gateways );
    }
}

if (!function_exists('is_mvx_vendor_completed_store_setup')) {
    /**
     * Check vendor store setup.
     *
     * @since 3.4.0
     * @param object $user 
     * @return boolean 
     */
    function is_mvx_vendor_completed_store_setup( $user ) {
        if( $user ){
            $is_completed = get_user_meta( $user->ID, '_vendor_is_completed_setup_wizard', true );
            $is_skipped = get_user_meta( $user->ID, '_vendor_skipped_setup_wizard', true );
            $store_name = get_user_meta( $user->ID, '_vendor_page_title', true );
            $country = get_user_meta( $user->ID, '_vendor_country', true );
            $payment_mode = get_user_meta( $user->ID, '_vendor_payment_mode', true );
            if( $is_skipped ) return true;
            if( $store_name && $country && $payment_mode ) return true;
            if( $is_completed || !apply_filters('mvx_vendor_store_setup_wizard_enabled', true) ) return true;
        }
        return false;
    }
}

if (!function_exists('get_mvx_ledger_types')) {
    /**
     * Get available ledger types.
     *
     * @return array types 
     */
    function get_mvx_ledger_types() {
        return apply_filters( 'mvx_ledger_types', array(
            'commission'    => __( 'Commission', 'multivendorx' ),
            'refund'        => __( 'Refund', 'multivendorx' ),
            'withdrawal'    => __( 'Withdrawal', 'multivendorx' ),
        ) );
    }
}

if (!function_exists('get_mvx_more_spmv_products')) {
    /**
     * Get available SPMV products.
     *
     * @param int $product_id 
     * @return array $products 
     */
    function get_mvx_more_spmv_products( $product_id = 0 ) {
        if( !$product_id ) return array();
        $more_products = array();
        $has_product_map_id = get_post_meta( $product_id, '_mvx_spmv_map_id', true );
        if( $has_product_map_id ){
            $products_map_data_ids = get_mvx_spmv_products_map_data( $has_product_map_id );
            $mapped_products = array_diff( $products_map_data_ids, array( $product_id ) );
            if( $mapped_products && count( $mapped_products ) >= 1 ){
                $i = 0;
                foreach ( $mapped_products as $p_id ) {
                    $p_author = absint( get_post_field( 'post_author', $p_id ) );
                    $p_obj = wc_get_product( $p_id );
                    if( $p_obj ){
                        if ( !$p_obj->is_visible() || get_post_status ( $p_id ) != 'publish' ) continue;
                        if ( is_user_mvx_pending_vendor( $p_author ) || is_user_mvx_rejected_vendor( $p_author ) && absint( get_post_field( 'post_author', $product_id ) ) == $p_author ) continue;
                        $product_vendor = get_mvx_product_vendors( $p_id );
                        if ( $product_vendor ){
                            $more_products[$i]['seller_name'] = $product_vendor->page_title;
                            $more_products[$i]['is_vendor'] = 1;
                            $more_products[$i]['shop_link'] = $product_vendor->permalink;
                            $more_products[$i]['rating_data'] = mvx_get_vendor_review_info( $product_vendor->term_id );
                        } else {
                            $user_info = get_userdata($p_author);
                            $more_products[$i]['seller_name'] = isset( $user_info->data->display_name ) ? $user_info->data->display_name : '';
                            $more_products[$i]['is_vendor'] = 0;
                            $more_products[$i]['shop_link'] = get_permalink(wc_get_page_id('shop'));
                            $more_products[$i]['rating_data'] = 'admin';
                        }
                        $currency_symbol = get_woocommerce_currency_symbol();
                        $regular_price_val = $p_obj->get_regular_price();
                        $sale_price_val = $p_obj->get_sale_price();
                        $price_val = $p_obj->get_price();
                        $more_products[$i]['product_name'] = $p_obj->get_title();
                        $more_products[$i]['regular_price_val'] = $regular_price_val;
                        $more_products[$i]['sale_price_val'] = $sale_price_val;
                        $more_products[$i]['price_val'] = $price_val;
                        $more_products[$i]['product_id'] = $p_obj->get_id();
                        $more_products[$i]['product_type'] = $p_obj->get_type();
                        if ($p_obj->get_type() == 'variable') {
                            $more_products[$i]['_min_variation_price'] = get_post_meta( $p_obj->get_id(), '_min_variation_price', true );
                            $more_products[$i]['_max_variation_price'] = get_post_meta( $p_obj->get_id(), '_max_variation_price', true );
                            $variable_min_sale_price = get_post_meta( $p_obj->get_id(), '_min_variation_sale_price', true );
                            $variable_max_sale_price = get_post_meta( $p_obj->get_id(), '_max_variation_sale_price', true );
                            $more_products[$i]['_min_variation_sale_price'] = $variable_min_sale_price ? $variable_min_sale_price : $more_products[$i]['_min_variation_price'];
                            $more_products[$i]['_max_variation_sale_price'] = $variable_max_sale_price ? $variable_max_sale_price : $more_products[$i]['_max_variation_price'];
                            $more_products[$i]['_min_variation_regular_price'] = get_post_meta( $p_obj->get_id(), '_min_variation_regular_price', true );
                            $more_products[$i]['_max_variation_regular_price'] = get_post_meta( $p_obj->get_id(), '_max_variation_regular_price', true );
                        }
                    }
                    $i++;
                }
            }
        }
        return apply_filters( 'mvx_more_spmv_products', $more_products, $product_id );
    }
}

function mvx_get_option( $key, $default_val = '', $lang_code = '' ) {
    $option_val = get_option( $key, $default_val );
    
    // WPML Support
    if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
        global $sitepress;
        if( !$lang_code ) {
            $current_language = $sitepress->get_current_language();
        } else {
            $current_language = $lang_code;
        }
        $option_val = get_option( $key . '_' . $current_language, $option_val );
    }
    
    return $option_val;
}

function mvx_update_option( $key, $option_val ) {
    // WPML Support
    if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
        global $sitepress;
        $current_language = $sitepress->get_current_language();
        update_option( $key . '_' . $current_language, $option_val );
    } else {
        update_option( $key, $option_val );
    }
}

function mvx_get_user_meta( $user_id, $key, $is_single = true ) {
    $meta_val = get_user_meta( $user_id, $key, $is_single );
    // WPML Support
    if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
        global $sitepress;
        $current_language = $sitepress->get_current_language();
        $option_val_wpml = get_user_meta( $user_id, $key . '_' . $current_language, $is_single );
        if( $option_val_wpml ) $meta_val = $option_val_wpml;
    }
    
    return $meta_val;
}

function mvx_update_user_meta( $user_id, $key, $meta_val ) {
    // WPML Support
    if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
        global $sitepress;
        $current_language = $sitepress->get_current_language();
        update_user_meta( $user_id, $key . '_' . $current_language, $meta_val );
    } else {
        update_user_meta( $user_id, $key, $meta_val );
    }
}

if (!function_exists('mvx_is_store_page')) {
    /**
     * Check if it's a store page
     *
     * @return bool
     */
    function mvx_is_store_page() {
        global $MVX;
        $vendor = false;
        if (get_queried_object()) {
            $vendor_id = is_tax($MVX->taxonomy->taxonomy_name) ? get_queried_object()->term_id : false;
            $vendor = $vendor_id ? get_mvx_vendor_by_term($vendor_id) : false;
        } else {
            $store_id = get_query_var('author');
            if ($store_id) {
                $vendor = get_mvx_vendor($store_id);
            }
        }
        if ($vendor) {
            return true;
        }
        return false;
    }
}

if (!function_exists('mvx_find_shop_page_vendor')) {
    /**
     * find vendor id from vendor shop page
     *
     * @return store_id
     */
    function mvx_find_shop_page_vendor() {
        $store_id = false;
        if (get_queried_object()) {
            $vendor_id = get_queried_object()->term_id;
            $store = get_mvx_vendor_by_term($vendor_id);
            $store_id = $store ? $store->id : false;;
        } else {
            $store_id = get_query_var('author');
        }
        return $store_id;
    }
}

if (!function_exists('mvx_get_attachment_url')) {
    /**
     * MVX get attachment URL by ID
     */
    function mvx_get_attachment_url( $attachment_id ) {
        $attachment_url = '';
        if( $attachment_id && is_numeric( $attachment_id ) ) {
            $attachment_url = wp_get_attachment_url( $attachment_id );
        } else {
            $attachment_url = $attachment_id;
        }
        return $attachment_url;
    }
    
}

if (!function_exists('mvx_mapbox_api_enabled')) {
    function mvx_mapbox_api_enabled() {
        if (mvx_is_module_active('store-location') && get_mvx_vendor_settings('enable_store_map_for_vendor', 'store')) {
            $get_choose_map = get_mvx_vendor_settings('choose_map_api') ? mvx_get_settings_value(get_mvx_vendor_settings('choose_map_api')) : '';
            $mapbox_api_key = get_mvx_vendor_settings('mapbox_api_key') ? get_mvx_vendor_settings('mapbox_api_key') : '';
            $mapbox_enabled = !empty($mapbox_api_key) && !empty($get_choose_map) && $get_choose_map == 'mapbox_api_set' ? $mapbox_api_key : false;
            return $mapbox_enabled;
        }
        return false;
    }
}

if (!function_exists('mvx_google_api_enabled')) {
    function mvx_google_api_enabled() {
        if (mvx_is_module_active('store-location') && get_mvx_vendor_settings('enable_store_map_for_vendor', 'store')) {
            $get_choose_map = get_mvx_vendor_settings('choose_map_api') ? mvx_get_settings_value(get_mvx_vendor_settings('choose_map_api')) : '';
            $gmap_api_key = get_mvx_vendor_settings('google_api_key') ? get_mvx_vendor_settings('google_api_key') : '';
            $gmap_enabled = !empty($gmap_api_key) && !empty($get_choose_map) && $get_choose_map == 'google_map_set' ? $gmap_api_key : false;
            return $gmap_enabled;
        }
        return false;
    }
}

if (!function_exists('mvx_mapbox_design_switcher')) {
    function mvx_mapbox_design_switcher() {
        if (mvx_mapbox_api_enabled()) {
            $map_styles_option = apply_filters('mvx_mapbox_map_style_switcher', 
                array(
                    'satellite' => array(
                        'id' => 'satellite-v9',
                        'name' => 'rtoggle',
                        'value' => __('Satellite', 'multivendorx'),
                        'checked' => 'yes'
                    ),
                    'light' => array(
                        'id' => 'light-v10',
                        'name' => 'rtoggle',
                        'value' => __('Light', 'multivendorx'),
                    ),
                    'dark' => array(
                        'id' => 'dark-v10',
                        'name' => 'rtoggle',
                        'value' => __('Dark', 'multivendorx'),
                    ),
                    'streets' => array(
                        'id' => 'streets-v11',
                        'name' => 'rtoggle',
                        'value' => __('Streets', 'multivendorx'),
                    ),
                    'outdoors' => array(
                        'id' => 'outdoors-v11',
                        'name' => 'rtoggle',
                        'value' => __('Outdoors', 'multivendorx'),
                    ),
                ) 
            );
            ?>
            <div id="menu">
                <?php foreach ($map_styles_option as $map_key => $map_value) { ?>
                    <input id="<?php echo $map_value['id'] ?>" type="radio" name="<?php echo $map_value['name'] ?>" value="<?php echo $map_key ?>" <?php if( isset($map_value['checked']) && $map_value['checked']){ echo 'checked="checked"'; } ?> >
                    <label for="<?php echo $map_value['id'] ?>"><?php echo esc_html($map_value['value']); ?></label>
                <?php } ?>
            </div>
            <?php
        }
    }
}

if (!function_exists('mvx_vendor_distance_by_shipping_settings')) {
    function mvx_vendor_distance_by_shipping_settings( $vendor_id = 0 ) {
        global $MVX;
        $mvx_shipping_by_distance = get_user_meta( $vendor_id, '_mvx_shipping_by_distance', true ) ? get_user_meta( $vendor_id, '_mvx_shipping_by_distance', true ) : array();
        $MVX->mvx_wp_fields->dc_generate_form_field( apply_filters( 'mvx_marketplace_settings_fields_shipping_distance', array(
            "mvx_byd_default_cost" => array('label' => __('Default Cost', 'multivendorx'), 'name' => 'mvx_shipping_by_distance[_default_cost]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'col-md-6 col-sm-9', 'label_class' => 'mvx_title mvx_ele mvx_store_shipping_distance_fields', 'value' => isset($mvx_shipping_by_distance['_default_cost']) ? $mvx_shipping_by_distance['_default_cost'] : '' ),

            "mvx_byd_max_distance" => array('label' => __('Max Distance (km)', 'multivendorx'), 'name' => 'mvx_shipping_by_distance[_max_distance]', 'placeholder' => __('No Limit', 'multivendorx'), 'type' => 'text', 'class' => 'col-md-6 col-sm-9', 'label_class' => 'mvx_title mvx_ele mvx_store_shipping_distance_fields', 'value' => isset($mvx_shipping_by_distance['_max_distance']) ? $mvx_shipping_by_distance['_max_distance'] : '' ),
            "mvx_byd_local_pickup_cost" => array('label' => __('Local Pickup Cost', 'multivendorx'), 'name' => 'mvx_shipping_by_distance[_local_pickup_cost]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'col-md-6 col-sm-9', 'label_class' => 'mvx_title mvx_ele mvx_store_shipping_distance_fields', 'value' => isset($mvx_shipping_by_distance['_local_pickup_cost']) ? $mvx_shipping_by_distance['_local_pickup_cost'] : '' ),
        ) ) );

        $mvx_shipping_by_distance_rates = get_user_meta( $vendor_id, '_mvx_shipping_by_distance_rates', true ) ? get_user_meta( $vendor_id, '_mvx_shipping_by_distance_rates', true ) : array();
        // display as per backend configuration
        foreach ($mvx_shipping_by_distance_rates as $key_distance => $value_distance) {
            if (isset($value_distance['mvx_distance_rule']['value'])) {
                $mvx_shipping_by_distance_rates[$key_distance]['mvx_distance_rule'] = $value_distance['mvx_distance_rule']['value'];
            }
        }

        $MVX->mvx_wp_fields->dc_generate_form_field(
            apply_filters( 'mvx_settings_fields_shipping_rates_by_distance', array( 
                    "mvx_shipping_by_distance_rates" => array(
                        'label'       => __('Distance-Cost Rules', 'multivendorx'), 
                        'type'        => 'multiinput',
                        'class'       => 'form-group',
                        'value'       => $mvx_shipping_by_distance_rates,
                        'options' => array(
                            "mvx_distance_rule" => array( 
                                'label' => __('Distance Rule', 'multivendorx'), 
                                'type' => 'select', 
                                'class' => 'col-md-6 col-sm-9', 
                                'label_class' => '', 
                                'options' => array(
                                    'up_to' => __('Distance up to', 'multivendorx'),
                                    'more_than' => __('Distance more than', 'multivendorx')
                                )
                            ),
                            "mvx_distance_unit" => array( 
                                'label' => __('Distance', 'multivendorx') . ' ( '. __('km', 'multivendorx') .' )', 
                                'type' => 'number', 
                                'class' => 'col-md-6 col-sm-9', 
                                'label_class' => ''
                            ),
                            "mvx_distance_price" => array( 
                                'label' => __('Cost', 'multivendorx') . ' ('.get_woocommerce_currency_symbol().')', 
                                'type' => 'number', 
                                'placeholder' => '0.00 (' . __('Free Shipping', 'multivendorx') . ')',
                                'class' => 'col-md-6 col-sm-9', 
                                'label_class' => '' 
                            ),
                        )
                    )
                ) 
            )
        );
    }
}

if (!function_exists('mvx_vendor_different_type_shipping_options')) {
    function mvx_vendor_different_type_shipping_options( $vendor_id = 0) {
        $vendor_shipping_options = get_user_meta($vendor_id, 'vendor_shipping_options', true) ? get_user_meta($vendor_id, 'vendor_shipping_options', true) : '';
        $shipping_options = apply_filters('mvx_vendor_shipping_option_to_vendor', array());
        if (mvx_is_module_active('zone-shipping')) {
            $shipping_options['distance_by_zone'] = __('Shipping by Zone', 'multivendorx');
        }
        if (mvx_is_module_active('distance-shipping')) {
            $shipping_options['distance_by_shipping'] = __('Shipping by Distance', 'multivendorx');
        }
        if (mvx_is_module_active('country-shipping')) {
            $shipping_options['shipping_by_country'] = __('Shipping by Country', 'multivendorx');
        }
        ?>
        <label for="shipping-options"><?php esc_html_e( 'Shipping Options', 'multivendorx' ); ?></label>
        <select class="form-control inline-select" id="shipping-options" name="shippping-options">
            <?php foreach ( $shipping_options as $value => $label ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php echo selected( $vendor_shipping_options, $value, false ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}

if (!function_exists('mvx_vendor_shipping_by_country_settings')) {
    function mvx_vendor_shipping_by_country_settings( $vendor_id = 0 ) {
        global $MVX;
        $mvx_shipping_by_country = mvx_get_user_meta( $vendor_id, '_mvx_shipping_by_country', array() ) ? mvx_get_user_meta( $vendor_id, '_mvx_shipping_by_country', array() )[0] : '';
        $mvx_country_rates       = mvx_get_user_meta( $vendor_id, '_mvx_country_rates', array() ) ? mvx_get_user_meta( $vendor_id, '_mvx_country_rates', array() ) : '';
        $mvx_state_rates         = mvx_get_user_meta( $vendor_id, '_mvx_state_rates', array() ) ? mvx_get_user_meta( $vendor_id, '_mvx_state_rates', array() ) : '';
        $MVX->mvx_wp_fields->dc_generate_form_field (
            apply_filters( 'mvx_settings_fields_shipping_by_country', array(
                "mvx_shipping_type_price" => array('label' => __('Default Shipping Price', 'multivendorx'), 'name' => 'mvx_shipping_by_country[_mvx_shipping_type_price]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'col-md-6 col-sm-9', 'label_class' => 'mvx_title mvx_ele', 'value' => isset($mvx_shipping_by_country['_mvx_shipping_type_price']) ? $mvx_shipping_by_country['_mvx_shipping_type_price'] : '', 'hints' => __('This is the base price and will be the starting shipping price for each product', 'multivendorx') ),
                "mvx_additional_product" => array('label' => __('Per Product Additional Price', 'multivendorx'), 'name' => 'mvx_shipping_by_country[_mvx_additional_product]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'col-md-6 col-sm-9', 'label_class' => 'mvx_title mvx_ele', 'value' => isset($mvx_shipping_by_country['_mvx_additional_product']) ? $mvx_shipping_by_country['_mvx_additional_product'] : '', 'hints' => __('If a customer buys more than one type product from your store, first product of the every second type will be charged with this price', 'multivendorx') ),
                "mvx_additional_qty" => array('label' => __('Per Qty Additional Price', 'multivendorx'), 'name' => 'mvx_shipping_by_country[_mvx_additional_qty]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'col-md-6 col-sm-9', 'label_class' => 'mvx_title mvx_ele', 'value' => isset($mvx_shipping_by_country['_mvx_additional_qty']) ? $mvx_shipping_by_country['_mvx_additional_qty'] : '', 'hints' => __('Every second product of same type will be charged with this price', 'multivendorx') ),
                "mvx_byc_free_shipping_amount" => array('label' => __('Free Shipping Minimum Order Amount', 'multivendorx'), 'name' => 'mvx_shipping_by_country[_free_shipping_amount]', 'placeholder' => __( 'NO Free Shipping', 'multivendorx'), 'type' => 'text', 'class' => 'col-md-6 col-sm-9', 'label_class' => 'mvx_title mvx_ele', 'value' => isset($mvx_shipping_by_country['_free_shipping_amount']) ? $mvx_shipping_by_country['_free_shipping_amount'] : '', 'hints' => __('Free shipping will be available if order amount more than this. Leave empty to disable Free Shipping.', 'multivendorx') ),
                "mvx_byc_local_pickup_cost" => array('label' => __('Local Pickup Cost', 'multivendorx'), 'name' => 'mvx_shipping_by_country[_local_pickup_cost]', 'placeholder' => '0.00', 'type' => 'text', 'class' => 'col-md-6 col-sm-9', 'label_class' => 'mvx_title mvx_ele', 'value' => isset($mvx_shipping_by_country['_local_pickup_cost']) ? $mvx_shipping_by_country['_local_pickup_cost'] : '' ),
            ) )
        );

        $mvx_shipping_rates = array();
        $state_options = array();
        if ( $mvx_country_rates ) {
            foreach ( $mvx_country_rates[0] as $country => $country_rate ) {
                $mvx_shipping_state_rates = array();
                $state_options = array();
                if ( !empty( $mvx_state_rates[0] ) && isset( $mvx_state_rates[0][$country] ) ) {
                    foreach ( $mvx_state_rates[0][$country] as $state => $state_rate ) {
                        $state_options[$state] = $state;
                        $mvx_shipping_state_rates[] = array( 
                            'mvx_state_to' => $state, 
                            'mvx_state_to_price' => $state_rate, 
                            'option_values' => $state_options 
                        );
                    }
                }
                $mvx_shipping_rates[] = array( 
                    'mvx_country_to' => $country, 
                    'mvx_country_to_price' => $country_rate, 
                    'mvx_shipping_state_rates' => $mvx_shipping_state_rates 
                );
            }   
        }
        $every_where = array('everywhere' => __('Everywhere Else', 'multivendorx'));
        $MVX->mvx_wp_fields->dc_generate_form_field( 
            apply_filters( 'mvx_settings_fields_shipping_rates_by_country', array( 
                "mvx_shipping_rates" => array(
                    'label' => __('Shipping Rates by Country', 'multivendorx') , 
                    'type' => 'multiinput', 
                    'value' => $mvx_shipping_rates, 
                    'desc' => __( 'Add the countries you deliver your products to. You can specify states as well. If the shipping price is same except some countries, there is an option Everywhere Else, you can use that.', 'multivendorx' ),
                    'desc_class' => 'instructions', 
                    'options' => array(
                        "mvx_country_to" => array(
                            'label' => __('Country', 'multivendorx'), 
                            'type' => 'select',
                            'class' => 'col-md-6 col-sm-9 mvx_country_to_select', 
                            'options' => array_merge($every_where, WC()->countries->get_shipping_countries())
                        ),
                        "mvx_country_to_price" => array(
                            'label' => __('Cost', 'multivendorx') . '('.get_woocommerce_currency_symbol().')', 
                            'type' => 'text',
                            'dfvalue' => 0,
                            'placeholder' => '0.00',
                            'class' => 'col-md-6 col-sm-9', 
                        ),
                        "mvx_shipping_state_rates" => array(
                            'label' => __('State Shipping Rates', 'multivendorx'), 
                            'type' => 'multiinput', 
                            'label_class' => 'mvx_title mvx_shipping_state_rates_label', 
                            'options' => array(
                                "mvx_state_to" => array( 
                                    'label' => __('State', 'multivendorx'), 
                                    'type' => 'select', 'class' => 'col-md-6 col-sm-9 mvx_state_to_select', 
                                    'options' => $state_options 
                                ),
                                "mvx_state_to_price" => array( 
                                    'label' => __('Cost', 'multivendorx') . '('.get_woocommerce_currency_symbol().')', 
                                    'type' => 'text', 
                                    'dfvalue' => 0,
                                    'placeholder' => '0.00 (' . __('Free Shipping', 'multivendorx') . ')', 
                                    'class' => 'col-md-6 col-sm-9', 
                                ),
                            ) 
                        )   
                    ) 
                )
            ) ) 
        );
    }
}

if (!function_exists('is_customer_not_given_review_to_vendor')) {
    function is_customer_not_given_review_to_vendor( $vendor_id = 0, $customer_id = 0) {
        $vendor = get_mvx_vendor($vendor_id);
        $reviews_lists = $vendor->get_reviews_and_rating(0);
        $vendor_review_user_id = wp_list_pluck($reviews_lists, 'user_id');
        if (in_array($customer_id, $vendor_review_user_id)) {
            return false;
        }
        return true;
    }
}


if (!function_exists('mvx_admin_backend_settings_fields_details')) {
    function mvx_admin_backend_settings_fields_details() {
        global $MVX;
        // Find country list
        $woo_countries = new WC_Countries();
        $countries = $woo_countries->get_allowed_countries();
        $country_list = $pages_array = $country_list = [];
        foreach ($countries as $countries_key => $countries_value) {
            $country_list[] = array(
                'lebel' => $countries_key,
                'value' => $countries_value
            );
        }

        // Find MVX created pages
        $pages = get_pages();
        $woocommerce_pages = array(wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
        if($pages){
            foreach ($pages as $page) {
                if (!in_array($page->ID, $woocommerce_pages)) {
                    $pages_array[] = array(
                        'value'=> $page->ID,
                        'label'=> $page->post_title,
                        'key'=> $page->ID,
                    );
                }
            }
        }

        // default nested fields
        $default_nested_data = array(
            array(
                'nested_datas'  => array(
                    (Object)[]
                )
            )
        );

        $review_options_data = mvx_get_option('mvx_review_management_tab_settings');
        $mvx_review_categories = $review_options_data && isset($review_options_data['mvx_review_categories']) ? $review_options_data['mvx_review_categories'] : $default_nested_data;

        $commission_options_data = mvx_get_option('mvx_commissions_tab_settings');
        $mvx_product_commission_variations = isset($commission_options_data['vendor_commission_by_products']) ? $commission_options_data['vendor_commission_by_products'] : $default_nested_data;
        $mvx_quantity_commission_variations = isset($commission_options_data['vendor_commission_by_quantity']) ? $commission_options_data['vendor_commission_by_quantity'] : $default_nested_data;

        $disbursement_settings_methods = $gateway_charge_fixed_value = $gateway_charge_percent_value = $gateway_charge_fixed_percent_value = [];
        if (mvx_is_module_active('paypal-masspay')) {
            $disbursement_settings_methods[] = array(
                'key'=> "paypal_masspay",
                'label'=> __('PayPal Masspay ', 'multivendorx'),//(Stop Waiting and Pay Vendors Immediately with PayPal Real-Time Split Payment using <a href="https://multivendorx.com/product/mvx-paypal-marketplace/">MVX PayPal Marketplace</a>. Please visit our site)
                'value'=> "paypal_masspay"
            );

            $gateway_charge_fixed_value[] = array(
                'name'  => 'default_gateway_charge_value',
                'key' => 'fixed_gayeway_amount_paypal_masspay',
                'type'      => 'number',
                'label' => __('Fixed paypal masspay amount', 'multivendorx'),
                'value' => 'fixed_gayeway_amount_paypal_masspay'
            );

            $gateway_charge_percent_value[] = array(
                'name'  => 'default_gateway_charge_value',
                'key' => 'percent_gayeway_amount_paypal_masspay',
                'type'      => 'number',
                'label' => __('percent paypal masspay amount', 'multivendorx'),
                'value' => 'percent_gayeway_amount_paypal_masspay'
            );

            $gateway_charge_fixed_percent_value[] = array_merge($gateway_charge_fixed_value, $gateway_charge_percent_value);
        }
        if (mvx_is_module_active('paypal-payout')) {
            $disbursement_settings_methods[] = array(
                'key'=> "paypal_payout",
                'label'=> __('Paypal Payout', 'multivendorx'),
                'value'=> "paypal_payout"
            );

            $gateway_charge_fixed_value[] = array(
                'name'  => 'default_gateway_charge_value',
                'key' => 'fixed_gayeway_amount_paypal_payout',
                'type'      => 'number',
                'label' => __('Fixed paypal payout amount', 'multivendorx'),
                'value' => 'fixed_gayeway_amount_paypal_payout'
            );

            $gateway_charge_percent_value[] = array(
                'name'  => 'default_gateway_charge_value',
                'key' => 'percent_gayeway_amount_paypal_payout',
                'type'      => 'number',
                'label' => __('Percent paypal payout amount', 'multivendorx'),
                'value' => 'percent_gayeway_amount_paypal_payout'
            );
            $gateway_charge_fixed_percent_value[] = array_merge($gateway_charge_fixed_value, $gateway_charge_percent_value);
        }
        if (mvx_is_module_active('stripe-connect')) {
            $disbursement_settings_methods[] = array(
                'key'=> "stripe_masspay",
                'label'=> __('Stripe Connect', 'multivendorx'),
                'value'=> "stripe_masspay"
            );

            $gateway_charge_fixed_value[] = array(
                'name'  => 'default_gateway_charge_value',
                'key' => 'fixed_gayeway_amount_stripe_masspay',
                'type'      => 'number',
                'label' => __('Fixed stripe amount', 'multivendorx'),
                'value' => 'fixed_gayeway_amount_stripe_masspay'
            );

            $gateway_charge_percent_value[] = array(
                'name'  => 'default_gateway_charge_value',
                'key' => 'percent_gayeway_amount_stripe_masspay',
                'type'      => 'number',
                'label' => __('Percent stripe amount', 'multivendorx'),
                'value' => 'percent_gayeway_amount_stripe_masspay'
            );
            $gateway_charge_fixed_percent_value[] = array_merge($gateway_charge_fixed_value, $gateway_charge_percent_value);
        }
        if (mvx_is_module_active('bank-payment')) {
            $disbursement_settings_methods[] = array(
                'key'=> "direct_bank",
                'label'=> __('Direct Bank Transfer', 'multivendorx'),
                'value'=> "direct_bank"
            );

            $gateway_charge_fixed_value[] = array(
                'name'  => 'default_gateway_charge_value',
                'key' => 'fixed_gayeway_amount_direct_bank',
                'type'      => 'number',
                'label' => __('Fixed bank amount', 'multivendorx'),
                'value' => 'fixed_gayeway_amount_direct_bank'
            );

            $gateway_charge_percent_value[] = array(
                'name'  => 'default_gateway_charge_value',
                'key' => 'percent_gayeway_amount_direct_bank',
                'type'      => 'number',
                'label' => __('Percent bank amount', 'multivendorx'),
                'value' => 'percent_gayeway_amount_direct_bank'
            );
            $gateway_charge_fixed_percent_value[] = array_merge($gateway_charge_fixed_value, $gateway_charge_percent_value);
        }

        $settings_fields = [
            'settings-general'  =>  [
                [
                    'key'       => 'approve_vendor',
                    'type'      => 'toggle_rectangle',
                    'class'     => 'mvx-toggle-radio-switcher',
                    'label'     => __( 'Approve Vendor', 'multivendorx' ),
                    'desc'      => __( 'Evaluate sellers before granting dashboard access or grant immediate dashboard access', 'multivendorx' ),
                    'options' => array(
                        array(
                            'name'  => 'approve_vendor',
                            'key' => 'manually',
                            'label' => __('Manually', 'multivendorx'),
                            'value' => 'manually'
                        ),
                        array(
                            'name'  => 'approve_vendor',
                            'key'   => 'automatically',
                            'label' => __('Automatically', 'multivendorx'),
                            'value' => 'automatically'
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'vendors_backend_access',
                    'label'   => __( "Vendor's Backend Access", 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'props'     => array(
                        'disabled'  => apply_filters('is_mvx_pro_plugin_inactive', true)
                    ),
                    'options' => array(
                        array(
                            'key'=> "vendors_backend_access",
                            'label'=> __('Upgrade to MultiVendorX Pro to offer an all-purpose dashboard while eliminating the requirenment for Wordpress backend access.', 'multivendorx'),
                            'value'=> "vendors_backend_access"
                        ),
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'display_product_seller',
                    'label'   => __( "Display Product Seller", 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'props'     => array(
                        //'disabled'  => true
                    ),
                    'options' => array(
                        array(
                            'key'=> "display_product_seller",
                            'label'=> __("Showcase the product vendor's name", 'multivendorx'),
                            'value'=> "display_product_seller"
                        ),
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'category_pyramid_guide',
                    'label'   => __( "Category Pyramid Guide (CPG)", 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'props'     => array(
                        //'disabled'  => true
                    ),
                    'options' => array(
                        array(
                            'key'=> "category_pyramid_guide",
                            'label'=> __("CPG option helps vendor's to identify the correct categories for their products", 'multivendorx'),
                            'value'=> "category_pyramid_guide"
                        ),
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_customer_support_details',
                    'label'   => __( "Customer Support", 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'props'     => array(
                    ),
                    'options' => array(
                        array(
                            'key'=> "is_customer_support_details",
                            'label'=> __("Show support channel details in \"Thank You\" page and new order email", 'multivendorx'),
                            'value'=> "is_customer_support_details"
                        ),
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'separator_content',
                    'type'      => 'section',
                    'label'     => "",
                ],
                [
                    'key'       =>  'vendor_list_page',
                    'type'      =>  'blocktext',
                    'label'     =>  __( 'no_label', 'multivendorx' ),
                    'blocktext'      =>  __( "Use the <code>[mvx_vendorlist]</code> shortcode to display vendor's list on your site <a href='https://www.w3schools.com'>Learn More</a>", 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'registration_page',
                    'type'      => 'select',
                    'label'     => __( 'Registration Page', 'multivendorx' ),
                    'desc'      => __( 'Select the page on which you have inserted <code>[vendor_registration]</code> shortcode .', 'multivendorx' ),
                    'options' => $pages_array,
                    'database_value' => '',
                ],
                [
                    'key'       => 'vendor_dashboard_page',
                    'type'      => 'select',
                    'label'     => __( 'Vendor Dashboard Page', 'multivendorx' ),
                    'desc'      => __( 'Select the page on which you have inserted <code>[mvx_vendor]</code> shortcode .', 'multivendorx' ),
                    'options' => $pages_array,
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_tinymce_api_section',
                    'type'      => 'text',
                    'label'     => __( 'TinyMCE Api', 'multivendorx' ),
                    'desc'      => __( 'Set TinyMCE Api key <a href="https://www.tiny.cloud/blog/how-to-get-tinymce-cloud-up-in-less-than-5-minutes/" target="_blank">Click here to generate key</a>', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'avialable_shortcodes',
                    'type'      => 'table',
                    'label'     => __( 'Avialable Shortcodes', 'multivendorx' ),
                    'label_options' =>  array(
                        __('Shortcodes', 'multivendorx'),
                        __('Description', 'multivendorx'),
                    ),
                    'options' => array(
                        array(
                            'variable'=> "<code>[mvx_vendor]</code>",
                            'description'=> __('Enables you to create a seller dashboard ', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[vendor_registration]</code>",
                            'description'=> __('Creates a page where the vendor registration form is available', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[vendor_coupons]</code>",
                            'description'=> __('Lets you view  a brief summary of the coupons created by the seller and number of times it has been used by the customers', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[mvx_recent_products]</code>",
                            'description'=> __('Allows you to glance at the recent products added by seller', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[mvx_products]</code>",
                            'description'=> __('Displays the products added by seller', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[mvx_featured_products]</code>",
                            'description'=> __('Exhibits featured products added by the seller', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[mvx_sale_products]</code>",
                            'description'=> __('Allows you to see the products put on sale by a seller', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[mvx_top_rated_products]</code>",
                            'description'=> __('Displays the top rated products of the seller', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[mvx_best_selling_products]</code>",
                            'description'=> __('Presents you the option of viewing the best selling products of the vendor', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[mvx_product_category]</code>",
                            'description'=> __('Lets you see the product categories used by the vendor', 'multivendorx'),
                        ),
                        array(
                            'variable'=> "<code>[mvx_vendorslist]</code>",
                            'description'=> __('Shows customers a list of available seller.', 'multivendorx'),
                        ),
                    ),
                    'database_value' => '',
                ],

            ],
            'social'    =>  [
                [
                    'key'    => 'buddypress_enabled',
                    'label'   => __( 'Buddypress', 'multivendorx' ),
                    'type'    => 'checkbox',
                    'class'     => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'=> "buddypress_enabled",
                            'label'=> __('Allows sellers to sell products on their BuddyPress profile while connecting with their customers', 'multivendorx'),//correct
                            'value'=> "buddypress_enabled"
                        )
                    ),
                    'database_value' => array(),
                ],
            ],
            'registration'  =>  [
            ],
            
            'seller-dashbaord'  =>  [
                [
                    'key'    => 'mvx_new_dashboard_site_logo',
                    'label'   => __( 'Branding Logo', 'multivendorx' ),
                    'type'    => 'file',
                    'width' =>  75,
                    'height'    => 75,
                    'desc' => __('Upload brand image as logo', 'multivendorx'),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'vendor_color_scheme_picker',
                    'type'      => 'radio_color',
                    'label'     => __( 'Color Scheme', 'multivendorx' ),
                    'desc'      => __( 'Select your prefered seller dashboard colour scheme', 'multivendorx' ),
                    'options' => array(
                        array(
                            'name'  => 'vendor_color_scheme_picker',
                            'key' => 'outer_space_blue',
                            'label' => __('Outer Space', 'multivendorx'),
                            'color' => array('#202528', '#333b3d','#3f85b9', '#316fa8'),
                            'value' => 'outer_space_blue'
                        ),
                        array(
                            'name'  => 'vendor_color_scheme_picker',
                            'key'   => 'green_lagoon',
                            'label' => __('Green Lagoon', 'multivendorx'),
                            'color' => array('#171717', '#212121', '#009788','#00796a'),
                            'value' => 'green_lagoon'
                        ),
                        array(
                            'name'  => 'vendor_color_scheme_picker',
                            'key'   => 'old_west',
                            'label' => __('Old West', 'multivendorx'),
                            'color' => array('#46403c', '#59524c', '#c7a589', '#ad8162'),
                            'value' => 'old_west'
                        ),
                        array(
                            'name'  => 'vendor_color_scheme_picker',
                            'key'   => 'wild_watermelon',
                            'label' => __('Wild Watermelon', 'multivendorx'),
                            'color' => array('#181617', '#353130', '#fd5668', '#fb3f4e'),
                            'value' => 'wild_watermelon'
                        ),
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'setup_wizard_introduction',
                    'type'      => 'textarea',
                    'class'     =>  'mvx-setting-wpeditor-class',
                    'label'     => __( 'Vendor Setup wizard Introduction Message', 'multivendorx' ),
                    'desc'      => __( "Welcome vendors with creative onboard messages", 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'     => 'vendor_deactivation_enabled',
                    'label'   => __( 'Enable Vendor Profile Deactivation', 'multivendorx' ),
                    'type'    => 'checkbox',
                    'class'   => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'   => "vendor_deactivation_enabled",
                            'label' => sprintf(__('Allows vendors to request the deactivation of their profiles. To know more, please <a href="%s">click here</a>.', 'multivendorx'), 'https://multivendorx.com/docs/knowledgebase/vendor-account-deactivation-flow/'),
                            'value' => "vendor_deactivation_enabled"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'separator_content',
                    'type'      => 'section',
                    'label'     => "",
                ],
                [
                    'key'       => 'mvx_vendor_announcements_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Vendor Announcements Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor announcements page', 'multivendorx' ),
                    'placeholder'   => __('vendor-announcements', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_store_settings_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Storefront Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Used as site logo on vendor dashboard pages', 'multivendorx' ),
                    'placeholder'   => __('storefront', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_profile_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Seller Profile Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor profile management page', 'multivendorx' ),
                    'placeholder'   => __('profile', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_policies_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Seller Policies Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor policies page', 'multivendorx' ),
                    'placeholder'   => __('vendor-policies', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_billing_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Seller Billing Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor billing page', 'multivendorx' ),
                    'placeholder'   => __('vendor-billing', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_shipping_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Vendor Shipping Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor shipping page', 'multivendorx' ),
                    'placeholder'   => __('vendor-shipping', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_report_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Seller Report Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor report page', 'multivendorx' ),
                    'placeholder'   => __('vendor-report', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_banking_overview_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Banking Overview Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor banking overview page', 'multivendorx' ),
                    'placeholder'   => __('banking-overview', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_add_product_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Add Product Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for add new product page', 'multivendorx' ),
                    'placeholder'   => __('add-product', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_edit_product_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Edit Product Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for edit product page', 'multivendorx' ),
                    'placeholder'   => __('edit-product', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_products_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Products List Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for products list page', 'multivendorx' ),
                    'placeholder'   => __('products', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_add_coupon_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Add Coupon Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for add new coupon page', 'multivendorx' ),
                    'placeholder'   => __('add-coupon', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_coupons_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Coupons List Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for coupons list page', 'multivendorx' ),
                    'placeholder'   => __('coupons', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_orders_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Vendor Orders Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor orders page', 'multivendorx' ),
                    'placeholder'   => __('vendor-orders', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_withdrawal_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Vendor Widthdrawals Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor widthdrawals page', 'multivendorx' ),
                    'placeholder'   => __('vendor-withdrawal', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_transaction_details_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Transaction Details Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for transaction details page', 'multivendorx' ),
                    'placeholder'   => __('transaction-details', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_knowledgebase_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Seller Knowledgebase Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor knowledgebase page', 'multivendorx' ),
                    'placeholder'   => __('vendor-knowledgebase', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_tools_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Seller Tools Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor tools page', 'multivendorx' ),
                    'placeholder'   => __('vendor-tools', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_products_qna_endpoint',
                    'type'      => 'text',
                    'label'     => __( 'Seller Products Q&As Endpoint', 'multivendorx' ),
                    'desc'      => __( 'Set endpoint for vendor products Q&As page', 'multivendorx' ),
                    'placeholder'   => __('products-qna', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_refund_req_endpoint',
                    'type'      => 'text',
                    'label'     => __('Refund Endpoint', 'multivendorx'),
                    'desc'      => __('Set endpoint for refund page', 'multivendorx'),
                    'placeholder'   => __('refund-request', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'separator_content',
                    'type'      => 'section',
                    'label'     => "",
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_vendor_dashboard_custom_css',
                    'type'      => 'textarea',
                    'label'     => __( 'Custom CSS', 'multivendorx' ),
                    'desc'      => __( 'Apply custom CSS to change dashboard design', 'multivendorx' ),
                    'database_value' => '',
                ],
            ],
            'store' =>  [
                [
                    'key'       => 'mvx_vendor_shop_template',
                    'type'      => 'radio_select',
                    'label'     => __( 'Store Header', 'multivendorx' ),
                    'desc'      => __( "Select store banner style", 'multivendorx' ),
                    'options' => array(
                        array(
                            'name'  => 'mvx_vendor_shop_template',
                            'key' => 'template1',
                            'label' => __('Outer Space', 'multivendorx'),
                            'color' => $MVX->plugin_url.'assets/images/template1.jpg',
                            'width' => 50,
                            'height'=> 60,
                            'value' => 'template1'
                        ),
                        array(
                            'name'  => 'mvx_vendor_shop_template',
                            'key'   => 'template2',
                            'label' => __('Green Lagoon', 'multivendorx'),
                            'color' => $MVX->plugin_url.'assets/images/template2.jpg',
                            'width' => 50,
                            'height'=> 60,
                            'value' => 'template2'
                        ),
                        array(
                            'name'  => 'mvx_vendor_shop_template',
                            'key'   => 'template3',
                            'label' => __('Old West', 'multivendorx'),
                            'color' => $MVX->plugin_url.'assets/images/template3.jpg',
                            'width' => 50,
                            'height'=> 60,
                            'value' => 'template3'
                        ),
                    ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'enable_store_map_for_vendor',
                    'label'   => __( 'Store Location', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "enable_store_map_for_vendor",
                            'label'=> __("Tap to display the location of sellers' shops.", 'multivendorx'),
                            'value'=> "enable_store_map_for_vendor"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'choose_map_api',
                    'type'      => 'select',
                    'depend_checkbox'    => 'enable_store_map_for_vendor',
                    'bydefault' =>  'google_map_set',
                    'label'     => __( 'Location Provider', 'multivendorx' ),
                    'desc'      => __( 'Select prefered location provider', 'multivendorx' ),
                    'options' => array(
                        array(
                            'key'=> "google_map_set",
                            'label'=> __('Google map', 'multivendorx'),
                            'value'=> __('google_map_set', 'multivendorx'),
                        ),
                        array(
                            'key'=> "mapbox_api_set",
                            'selected'  => true,
                            'label'=> __('Mapbox map', 'multivendorx'),
                            'value'=> __('mapbox_api_set', 'multivendorx'),
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'google_api_key',
                    'type'      => 'text',
                    'depend_checkbox'    => 'enable_store_map_for_vendor',
                    'depend'    => 'choose_map_api',
                    'dependvalue'       =>  'google_map_set',
                    'label'     => __( 'Google Map API key', 'multivendorx' ),
                    'desc'      => __('<a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key" target="_blank">Click here to generate key</a>','multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mapbox_api_key',
                    'type'      => 'text',
                    'depend_checkbox'    => 'enable_store_map_for_vendor',
                    'depend'    => 'choose_map_api',
                    'dependvalue'       =>  'mapbox_api_set',
                    'label'     => __( 'Mapbox access token', 'multivendorx' ),
                    'desc' => __('<a href="https://docs.mapbox.com/help/getting-started/access-tokens/" target="_blank">Click here to generate access token</a>','multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'show_related_products',
                    'type'      => 'select',
                    'label'     => __( 'Related Product', 'multivendorx' ),
                    'desc'      => __( 'Let customers view other products related to the product they are viewing..', 'multivendorx' ),
                    'options' => array(
                        array(
                            'key'=> "all_related",
                            'label'=> __('Related Products from Entire Store', 'multivendorx'),
                            'value'=> __('all_related', 'multivendorx'),
                        ),
                        array(
                            'key'=> "vendors_related",
                            'selected'  => true,
                            'label'=> __("Related Products from Seller's Store", 'multivendorx'),
                            'value'=> __('vendors_related', 'multivendorx'),
                        ),
                        array(
                            'key'=> "disable",
                            'selected'  => true,
                            'label'=> __("Disable", 'multivendorx'),
                            'value'=> __('disable', 'multivendorx'),
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'is_enable_store_sidebar_position',
                    'label'   => __( 'Store Sidebar', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_enable_store_sidebar_position",
                            'label'=> __('Display sidebar section for vendor shop page. Select here to add vendor shop widget.', 'multivendorx'),
                            'value'=> "is_enable_store_sidebar_position"
                        ),
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       =>  'vendor_widget_show_page',
                    'type'      =>  'blocktext',
                    'depend_checkbox'    => 'is_enable_store_sidebar_position',
                    'label'     =>  __( 'no_label', 'multivendorx' ),
                    'blocktext'      =>  __( "If you are not sure where to add widget, just go to admin <a href=".admin_url("widgets.php")." terget='_blank'>widget</a> section and add your preferred widgets to <b>vendor store sidebar</b>.", 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mvx_store_sidebar_position',
                    'type'      => 'toggle_rectangle',
                    'depend_checkbox'    => 'is_enable_store_sidebar_position',
                    'label'     => __( 'Store Sidebar Position', 'multivendorx' ),
                    'desc'      => __( 'Decide where your want your store sidebar to be displayed', 'multivendorx' ),
                    'options' => array(
                        array(
                            'name'  =>  'mvx_store_sidebar_position',
                            'key'=> "left",
                            'label'=> __('At Left', 'multivendorx'),
                            'value'=> __('At Left', 'multivendorx'),
                        ),
                        array(
                            'name'  =>  'mvx_store_sidebar_position',
                            'key'=> "right",
                            'label'=> __('At Right', 'multivendorx'),
                            'value'=> __('At Right', 'multivendorx'),
                        ),
                    ),
                    'database_value' => '',
                ],
            ],
            'products'  =>  [
                [
                    'key'    => 'type_options',
                    'label'   => __( 'Type options', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'parent_class'  => 'mvx-toggle-checkbox-header',
                    'select_deselect'   =>  true,
                    'type'    => 'checkbox_select',
                    'desc' => __('Select if the product is non-tangible or downloadable.', 'multivendorx'),
                    'options' => array(
                        array(
                            'key'=> "virtual",
                            'label'=> __('Virtual', 'multivendorx'),
                            'hints'=>   __('Virtual', 'multivendorx'),
                            'value'=> "virtual"
                        ),
                        array(
                            'key'=> "downloadable",
                            'label'=> __('Downloadable', 'multivendorx'),
                            'hints'=>   __('Downloadable', 'multivendorx'),
                            'value'=> "downloadable"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'products_fields',
                    'type'      => 'checkbox_select',
                    'class'     => 'mvx-toggle-checkbox',
                    'parent_class'  => 'mvx-toggle-checkbox-header',
                    'select_deselect'   =>  true,
                    'label'     => __( 'Product Fields ', 'multivendorx' ),
                    'options' => array(
                        array(
                            'name'  => 'products_fields',
                            'key' => 'general',
                            'label' => __('General', 'multivendorx'),
                            'value' => 'general'
                        ),
                        array(
                            'name'  => 'products_fields',
                            'key'   => 'inventory',
                            'label' => __('Inventory', 'multivendorx'),
                            'value' => 'inventory'
                        ),
                        array(
                            'name'  => 'products_fields',
                            'key'   => 'linked_product',
                            'label' => __('Linked Product', 'multivendorx'),
                            'value' => 'linked_product'
                        ),
                        array(
                            'name'  => 'products_fields',
                            'key'   => 'attribute',
                            'label' => __('Attribute', 'multivendorx'),
                            'value' => 'attribute'
                        ),
                        array(
                            'name'  => 'products_fields',
                            'key'   => 'advanced',
                            'label' => __('Advance', 'multivendorx'),
                            'value' => 'advanced'
                        ),
                        array(
                            'name'  => 'products_fields',
                            'key'   => 'policies',
                            'label' => __('Policies', 'multivendorx'),
                            'value' => 'policies'
                        ),
                        array(
                            'name'  => 'products_fields',
                            'key'   => 'product_tag',
                            'label' => __('Product Tag', 'multivendorx'),
                            'value' => 'product_tag'
                        ),
                        array(
                            'name'  => 'products_fields',
                            'key'   => 'GTIN',
                            'label' => __('GTIN', 'multivendorx'),
                            'value' => 'GTIN'
                        )
                    ),
                    'database_value' => '',
                ],
            ],
            'products-capability'   =>  [
                [
                    'key'    => 'is_submit_product',
                    'label'   => __( 'Submit Products', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_submit_product",
                            'label'=> __('Enables sellers to add new products and submit them for admin approval', 'multivendorx'),
                            'value'=> "is_submit_product"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_published_product',
                    'label'   => __( 'Publish Products', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_published_product",
                            'label'=> __('Lets sellers can publish products on the website without waiting for approval', 'multivendorx'),
                            'value'=> "is_published_product"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_edit_delete_published_product',
                    'label'   => __( 'Edit Published Products', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_edit_delete_published_product",
                            'label'=> __('Makes it possible for sellers to edit and delete a published product.', 'multivendorx'),
                            'value'=> "is_edit_delete_published_product"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'publish_and_submit_products',
                    'label'   => __( 'Publish and Submit Re-edited Products', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "publish_and_submit_products",
                            'label'=> __('Allows sellers to list their products while submitting them to your for revision', 'multivendorx'),
                            'value'=> "publish_and_submit_products"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_submit_coupon',
                    'label'   => __( 'Submit Coupons', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_submit_coupon",
                            'label'=> __('Equips sellers with the ability to create their own coupons', 'multivendorx'),
                            'value'=> "is_submit_coupon"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_published_coupon',
                    'label'   => __( 'Publish Coupons', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_published_coupon",
                            'label'=> __('Gives sellers the ability to publish coupons on your website', 'multivendorx'),
                            'value'=> "is_published_coupon"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_edit_delete_published_coupon',
                    'label'   => __( 'Edit Coupons', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_edit_delete_published_coupon",
                            'label'=> __('Sellers gain the option to edit, re-use or delete a published coupons', 'multivendorx'),
                            'value'=> "is_edit_delete_published_coupon"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_upload_files',
                    'label'   => __( 'Upload Media Files', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_upload_files",
                            'label'=> __('Let Vendors upload media like ebooks, music, video, images etc', 'multivendorx'),
                            'value'=> "is_upload_files"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'sku_generator_simple',
                    'type'      => 'select',
                    'label'     => __( 'Generate Simple / Parent SKUs:', 'multivendorx' ),
                    'desc'      => __( 'Determine how SKUs for simple, external, or parent products will be generated.', 'multivendorx' ),
                    'options' => array(
                        array(
                            'key'=> "choose_options",
                            'label'=> __('Choose options', 'multivendorx'),
                            'value'=> "choose_options",
                        ),
                        array(
                            'key'=> "never",
                            'label'=> __('Never (let me set them)', 'multivendorx'),
                            'value'=> "never",
                        ),
                        array(
                            'key'=> "slugs",
                            'label'=> __('Using the product slug (name)', 'multivendorx'),
                            'value'=> "slugs",
                        ),
                        array(
                            'key'=> "ids",
                            'label'=> __('Using the product ID)', 'multivendorx'),
                            'value'=> "ids",
                        ),
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'sku_generator_variation',
                    'type'      => 'select',
                    'label'     => __( 'Generate Variation SKUs:', 'multivendorx' ),
                    'desc'      => __( 'Determine how SKUs for product variations will be generated.', 'multivendorx' ),
                    'options' => array(
                        array(
                            'key'=> "choose_options",
                            'label'=> __('Choose options', 'multivendorx'),
                            'value'=> "choose_options",
                        ),
                        array(
                            'key'=> "never",
                            'label'=> __('Never (let me set them)', 'multivendorx'),
                            'value'=> "never",
                        ),
                        array(
                            'key'=> "slugs",
                            'label'=> __('Using the attribute slugs (names)', 'multivendorx'),
                            'value'=> "slugs",
                        ),
                        array(
                            'key'=> "ids",
                            'label'=> __('Using the variation ID)', 'multivendorx'),
                            'value'=> "ids",
                        ),
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'sku_generator_attribute_spaces',
                    'type'      => 'select',
                    'label'     => __( 'Replace spaces in attributes?', 'multivendorx' ),
                    'desc'      => __( 'Replace spaces in attribute names when used in a SKU.', 'multivendorx' ),
                    'options' => array(
                        array(
                            'key'=> "choose_options",
                            'label'=> __('Choose options', 'multivendorx'),
                            'value'=> "choose_options",
                        ),
                        array(
                            'key'=> "no",
                            'label'=> __('Do not replace spaces in attribute names.', 'multivendorx'),
                            'value'=> "no",
                        ),
                        array(
                            'key'=> "underscore",
                            'label'=> __('Replace spaces with underscores', 'multivendorx'),
                            'value'=> "underscore",
                        ),
                        array(
                            'key'=> "dash",
                            'label'=> __('Replace spaces with dashes / hyphens', 'multivendorx'),
                            'value'=> "dash",
                        ),
                        array(
                            'key'=> "none",
                            'label'=> __('Remove spaces from attribute names', 'multivendorx'),
                            'value'=> "none",
                        ),
                    ),
                    'database_value' => '',
                ],
            ],
            'commissions'   =>  [
                [
                    'key'       => 'revenue_sharing_mode',
                    'type'      => 'toggle_rectangle',
                    'class'     => 'mvx-toggle-radio-switcher',
                    'label'     => __( 'Revenue Sharing Mode', 'multivendorx' ),
                    'desc'      => __( 'Select how you want the commission to be split. If you are not sure about how to setup commissions and payment options in your marketplace, kindly read this <a href="https://multivendorx.com/doc/knowladgebase/payments/" terget="_blank">article</a> before proceeding.', 'multivendorx' ),
                    'options' => array(
                        array(
                            'name'  => 'revenue_sharing_mode',
                            'key' => 'revenue_sharing_mode_admin',
                            'label' => __('Admin fees', 'multivendorx'),
                            'value' => 'revenue_sharing_mode_admin'
                        ),
                        array(
                            'name'  => 'revenue_sharing_mode',
                            'key'   => 'revenue_sharing_mode_vendor',
                            'label' => __('Vendor commissions', 'multivendorx'),
                            'value' => 'revenue_sharing_mode_vendor'
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'commission_type',
                    'type'      => 'select',
                    'label'     => __( 'Commission Type', 'multivendorx' ),
                    'desc'      => __( 'Choose the Commission Option prefered by you. For better undrestanding read doc', 'multivendorx' ),
                    'options' => array(
                        array(
                            'key'=> "choose_commission_type",
                            'label'=> __('Choose Commission Type', 'multivendorx'),
                            'value'=> __('choose_commission_type', 'multivendorx'),
                        ),
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
                        ),
                        array(
                            'key'=> "commission_calculation_on_tax",
                            'label'=> __('Commission Calculation on Tax', 'multivendorx'),
                            'value'=> __('commission_calculation_on_tax', 'multivendorx'),
                        ),
                    ),
                    'database_value' => '',
                ],
                // purchase quantity

                [
                    'key'       => 'vendor_commission_by_quantity',
                    'type'      => 'nested',
                    'depend'    => 'commission_type',
                    'dependvalue'       =>  'commission_by_purchase_quantity',
                    'label'     => __( 'Commission By Purchase Quantity', 'multivendorx' ),
                    'desc'      => __( 'Commission rules depending upon purchased product quantity. e.g 80&#37; commission when purchase quantity 2, 80&#37; commission when purchase quantity > 2 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'multivendorx' ),
                    'parent_options' => array(
                        array(
                            'key'=>'quantity',
                            'type'=> "number",
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label'=> __('Purchase Quantity', 'multivendorx'),
                            'value'=> "quantity"
                        ),
                        array(
                            'key'   =>'rule',
                            'label' => __('Rule', 'multivendorx'), 
                            'type'  => 'select',
                            'options'     => array(
                                array(
                                    'name'  => 'rule',
                                    'key'   => 'upto',
                                    'type'  => 'number',
                                    'label' => __('Up to', 'multivendorx'),
                                    'value' => 'upto'
                                ),
                                array(
                                    'name'  => 'rule',
                                    'key'   => 'greater',
                                    'type'  => 'number',
                                    'label' => __('More than', 'multivendorx'),
                                    'value' => 'greater'
                                )
                            )
                        ),
                        array(
                            'key'   => 'type',
                            'label' => __('Commission Type', 'multivendorx'), 
                            'type'  => 'select2nd', 
                            'options' => array(
                                array(
                                    'name'  => 'type',
                                    'key'   => 'percent',
                                    'type'  => 'number',
                                    'label' => __('Percent', 'multivendorx'),
                                    'value' => 'percent'
                                ),
                                array(
                                    'name'  => 'type',
                                    'key'   => 'fixed',
                                    'type'  => 'number',
                                    'label' => __('Fixed', 'multivendorx'),
                                    'value' => 'fixed'
                                ),
                                array(
                                    'name'  => 'type',
                                    'key'   => 'percent_fixed',
                                    'type'  => 'number',
                                    'label' => __('Percent + Fixed', 'multivendorx'),
                                    'value' => 'percent_fixed'
                                )
                            ) 
                        ),
                        array(
                            'key'=>'commission',
                            'type'=> "number",
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label'=> __('Commission Percent(%)', 'multivendorx'),
                            'value'=> "commission"
                        ),
                        array(
                            'key'=>'commission_fixed',
                            'type'=> "number",
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label' => __('Commission Fixed', 'multivendorx') . '(' . get_woocommerce_currency_symbol() . ')',
                            'value'=> "commission_fixed"
                        )
                    ),
                    'child_options' => array(
                    ),
                    'database_value' => $mvx_quantity_commission_variations,
                ],

                [
                    'key'       => 'vendor_commission_by_products',
                    'type'      => 'nested',
                    'depend'    => 'commission_type',
                    'dependvalue'       =>  'commission_by_product_price',
                    'label'     => __( 'Commission By Product Price', 'multivendorx' ),
                    'desc'      => sprintf( __( 'Commission rules depending upon product price. e.g 80&#37; commission when product cost < %s1000, %s100 fixed commission when product cost > %s1000 and so on. You may define any number of such rules. Please be sure, <b> do not set conflicting rules.</b>', 'multivendorx' ), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol() ),
                    'parent_options' => array(
                        array(
                            'key'=>'cost',
                            'type'=> "number",
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label'=> __('Product cost', 'multivendorx'),
                            'value'=> "cost"
                        ),
                        array(
                            'key'   =>'rule',
                            'label' => __('Rule', 'multivendorx'), 
                            'type'  => 'select',
                            'options'     => array(
                                array(
                                    'name'  => 'rule',
                                    'key'   => 'upto',
                                    'type'  => 'number',
                                    'label' => __('Up to', 'multivendorx'),
                                    'value' => 'upto'
                                ),
                                array(
                                    'name'  => 'rule',
                                    'key'   => 'greater',
                                    'type'  => 'number',
                                    'label' => __('More than', 'multivendorx'),
                                    'value' => 'greater'
                                )
                            )
                        ),
                        array(
                            'key'   => 'type',
                            'label' => __('Commission Type', 'multivendorx'), 
                            'type'  => 'select2nd', 
                            'options' => array(
                                array(
                                    'name'  => 'type',
                                    'key'   => 'percent',
                                    'type'  => 'number',
                                    'label' => __('Percent', 'multivendorx'),
                                    'value' => 'percent'
                                ),
                                array(
                                    'name'  => 'type',
                                    'key'   => 'fixed',
                                    'type'  => 'number',
                                    'label' => __('Fixed', 'multivendorx'),
                                    'value' => 'fixed'
                                ),
                                array(
                                    'name'  => 'type',
                                    'key'   => 'percent_fixed',
                                    'type'  => 'number',
                                    'label' => __('Percent + Fixed', 'multivendorx'),
                                    'value' => 'percent_fixed'
                                )
                            ) 
                        ),
                        array(
                            'key'=>'commission',
                            'type'=> "number",
                            'depend'    => 'type',
                            'dependvalue'       =>  'percent',
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label'=> __('Commission Percent(%)', 'multivendorx'),
                            'value'=> "commission"
                        ),
                        array(
                            'key'=>'commission_fixed',
                            'type'=> "number",
                            'depend'    => 'type',
                            'dependvalue'       =>  'fixed',
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label' => __('Commission Fixed', 'multivendorx') . '(' . get_woocommerce_currency_symbol() . ')',
                            'value'=> "commission_fixed"
                        ),
                        array(
                            'key'=>'commission',
                            'type'=> "number",
                            'depend'    => 'type',
                            'dependvalue'       =>  'percent_fixed',
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label'=> __('Commission Percent(%)', 'multivendorx'),
                            'value'=> "commission"
                        ),
                        array(
                            'key'=>'commission_fixed',
                            'type'=> "number",
                            'depend'    => 'type',
                            'dependvalue'       =>  'percent_fixed',
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label' => __('Commission Fixed', 'multivendorx') . '(' . get_woocommerce_currency_symbol() . ')',
                            'value'=> "commission_fixed"
                        )
                    ),
                    'child_options' => array(
                    ),
                    'database_value' => $mvx_product_commission_variations,
                ],
                // default commissions
                [
                    'key'       => 'default_commission',
                    'type'      => 'multi_number',
                    'depend'    => 'commission_type',
                    'dependvalue'       =>  'fixed',
                    'label'     => __( 'Commission Value', 'multivendorx' ),
                    'desc' => __(' The is the default commission amount that will be applicable for all transactions.', 'multivendorx'),
                    'options' => array(
                        array(
                            'name'  => 'default_commission',
                            'key' => 'fixed_ammount',
                            'type'      => 'number',
                            'label' => __('Fixed', 'multivendorx'),
                            'value' => 'fixed_ammount'
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'default_commission',
                    'type'      => 'multi_number',
                    'depend'    => 'commission_type',
                    'dependvalue'       =>  'percent',
                    'label'     => __( 'Commission Value', 'multivendorx' ),
                    'desc' => __('The is the default commission amount that will be applicable for all transactions.', 'multivendorx'),
                    'options' => array(
                        array(
                            'name'  => 'default_commission',
                            'key'   => 'percent_amount',
                            'type'      => 'number',
                            'label' => __('Percentage', 'multivendorx'),
                            'value' => 'percent_amount'
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'default_commission',
                    'type'      => 'multi_number',
                    'depend'    => 'commission_type',
                    'dependvalue'       =>  'fixed_with_percentage',
                    'label'     => __( 'Commission Value', 'multivendorx' ),
                    'desc' => __('The is the default commission amount that will be applicable for all transactions.', 'multivendorx'),
                    'options' => array(
                        array(
                            'name'  => 'default_commission',
                            'key' => 'fixed_ammount',
                            'type'      => 'number',
                            'label' => __('Fixed', 'multivendorx'),
                            'value' => 'fixed_ammount'
                        ),
                        array(
                            'name'  => 'default_commission',
                            'key'   => 'percent_amount',
                            'type'      => 'number',
                            'label' => __('Percentage', 'multivendorx'),
                            'value' => 'percent_amount'
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'default_commission',
                    'type'      => 'multi_number',
                    'depend'    => 'commission_type',
                    'dependvalue'       =>  'fixed_with_percentage_qty',
                    'label'     => __( 'Commission Value', 'multivendorx' ),
                    'desc' => __('The is the default commission amount that will be applicable for all transactions.', 'multivendorx'),
                    'options' => array(
                        array(
                            'name'  => 'default_commission',
                            'key' => 'fixed_ammount',
                            'type'      => 'number',
                            'label' => __('Fixed', 'multivendorx'),
                            'value' => 'fixed_ammount'
                        ),
                        array(
                            'name'  => 'default_commission',
                            'key'   => 'percent_amount',
                            'type'      => 'number',
                            'label' => __('Percentage', 'multivendorx'),
                            'value' => 'percent_amount'
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'payment_method_disbursement',
                    'label'   => __( 'Commission Disbursement Method', 'multivendorx' ),
                    'desc'  =>  __( "Kindly activate your preferred payment method in the <a href='". admin_url( '?page=mvx#&submenu=modules' ) ."'>Module section</a>", 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'right_content' =>  true,
                    'options' => apply_filters('mvx_payment_method_disbursement_options', array()),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'separator_content',
                    'type'      => 'section',
                    'label'     => "",
                ],
                [
                    'key'       => 'payment_gateway_charge',
                    'label'     => __( 'Payment Gateway Charge', 'multivendorx' ),
                    'desc'  =>  __( "Add the payment gateway charges incurred while paying online.", 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'      => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "payment_gateway_charge",
                            'label'=> __('', 'multivendorx'),
                            'value'=> "payment_gateway_charge"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'gateway_charges_cost_carrier',
                    'depend_checkbox'   =>  'payment_gateway_charge',
                    'type'      => 'select',
                    'label'     => __( 'Who bear the gateway charges', 'multivendorx' ),
                    'desc'      => __('When it comes to automated payments, you can decide who will be responsible for the gateway charges.', 'multivendorx'),
                    'options' => array(
                        array(
                            'key'=> "vendor",
                            'label'=> __('Vendor', 'multivendorx'),
                            'value'=> __('vendor', 'multivendorx'),
                        ),
                        array(
                            'key'=> "admin",
                            'label'=> __('Site owner', 'multivendorx'),
                            'value'=> __('admin', 'multivendorx'),
                        ),
                        array(
                            'key'=> "separate",
                            'label'=> __('Separately', 'multivendorx'),
                            'value'=> __('separate', 'multivendorx'),
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'payment_gateway_charge_type',
                    'type'      => 'select',
                    'depend_checkbox'   =>  'payment_gateway_charge',
                    'label'     => __( 'Gateway Charge Type', 'multivendorx' ),
                    'desc'      => __('Choose your preferred gateway charge type.', 'multivendorx'),
                    'options' => array(
                        array(
                            'key'=> "percent",
                            'label'=> __('Percentage', 'multivendorx'),
                            'value'=> 'percent',
                        ),
                        array(
                            'key'=> "fixed",
                            'label'=> __('Fixed Amount', 'multivendorx'),
                            'value'=> 'fixed',
                        ),
                        array(
                            'key'=> "fixed_with_percentage",
                            'label'=> __('%age + Fixed', 'multivendorx'),
                            'value'=> 'fixed_with_percentage',
                        )
                    ),
                    'database_value' => '',
                ],

                // gayeway charge value
                [
                    'key'       => 'default_gateway_charge_value',
                    'type'      => 'multi_number',
                    'depend_checkbox'   =>  'payment_gateway_charge',
                    'depend'    => 'payment_gateway_charge_type',
                    'dependvalue'       =>  'fixed',
                    'label'     => __( 'Gateway Value', 'multivendorx' ),
                    'desc' => __('The commission amount added here will be applicable for all commissions. In case the your commission type is fixed the', 'multivendorx'),
                    'options' => $gateway_charge_fixed_value,
                    'database_value' => '',
                ],
                [
                    'key'       => 'default_gateway_charge_value',
                    'type'      => 'multi_number',
                    'depend_checkbox'   =>  'payment_gateway_charge',
                    'depend'    => 'payment_gateway_charge_type',
                    'dependvalue'       =>  'percent',
                    'label'     => __( 'Gateway Value', 'multivendorx' ),
                    'desc' => __('The commission amount added here will be applicable for all commissions. In case the your commission type is fixed the', 'multivendorx'),
                    'options' => $gateway_charge_percent_value,
                    'database_value' => '',
                ],
                [
                    'key'       => 'default_gateway_charge_value',
                    'type'      => 'multi_number',
                    'depend_checkbox'   =>  'payment_gateway_charge',
                    'depend'    => 'payment_gateway_charge_type',
                    'dependvalue'       =>  'fixed_with_percentage',
                    'label'     => __( 'Gateway Value', 'multivendorx' ),
                    'desc' => __('The commission amount added here will be applicable for all commissions. In case the your commission type is fixed the', 'multivendorx'),
                    'options' => !empty($gateway_charge_fixed_percent_value) ? end($gateway_charge_fixed_percent_value) : array(),
                    'database_value' => '',
                ],
            ],
            'dashbaord-pages'   => [
               
                [
                    'key'       => 'mvx_vendor',
                    'type'      => 'select',
                    'label'     => __( 'Vendor Dashboard', 'multivendorx' ),
                    'desc'      => __( 'Choose your preferred page for vendor dashboard', 'multivendorx' ),
                    'options' => $pages_array,
                    'database_value' => '',
                ],
                [
                    'key'       => 'vendor_registration',
                    'type'      => 'select',
                    'label'     => __( 'Vendor Dashboard', 'multivendorx' ),
                    'desc'      => __( 'Choose your preferred page for vendor registration', 'multivendorx' ),
                    'options' => $pages_array,
                    'database_value' => '',
                ],
            ],
            
            'spmv-pages'    => [
                [
                    'key'    => 'is_singleproductmultiseller',
                    'label'   => __( 'Allow Vendor to Copy Products', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_singleproductmultiseller",
                            'label'=> __('Let vendors search for products sold on your site and sell them from their store.', 'multivendorx'),
                            'value'=> "is_singleproductmultiseller"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'singleproductmultiseller_show_order',
                    'type'      => 'select',
                    'label'     => __( 'Display Shop Page Product', 'multivendorx' ),
                    'desc'      => stripslashes(__('Select the criteria on which the SPMV product is going to be based on.', 'multivendorx')),
                    'options' => array(
                        array(
                            'key'=> "min-price",
                            'label'=> __('Min Price', 'multivendorx'),
                            'value'=> __('min-price', 'multivendorx'),
                        ),
                        array(
                            'key'=> "max-price",
                            'label'=> __('Max Price', 'multivendorx'),
                            'value'=> __('max-price', 'multivendorx'),
                        ),
                        array(
                            'key'=> "top-rated-vendor",
                            'label'=> __('Top rated vendor', 'multivendorx'),
                            'value'=> __('top-rated-vendor', 'multivendorx'),
                        )
                    ),
                    'database_value' => '',
                ],
            ],
            
            'review-management'   => [
                [
                    'key'       =>  'vendor_rating_page',
                    'type'      =>  'blocktext',
                    'label'     =>  __( 'no_label', 'multivendorx' ),
                    'blocktext'      =>  __( "<b>Admin needs to enable product review from woocommerce settings</b>", 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'is_sellerreview',
                    'label'   => __( 'Vendor Review', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_sellerreview",
                            'label'=> __('Any customer can rate and review a vendor.', 'multivendorx'),
                            'value'=> "is_sellerreview"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_sellerreview_varified',
                    'label'   => __( 'Buyer only reviews', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_sellerreview_varified",
                            'label'=> __('Allows you to accept reviews only from buyers purchasing the product.', 'multivendorx'),
                            'value'=> "is_sellerreview_varified"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'product_review_sync',
                    'label'   => __( 'Product Rating Sync', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "product_review_sync",
                            'label'=> __('Store Rating will be calculated based on Store Rating + Product Rating', 'multivendorx'),
                            'value'=> "product_review_sync"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'mvx_review_categories',
                    'type'      => 'nested',
                    'label'     => __( 'Rating Parameters', 'multivendorx' ),
                    'desc'      => __( 'Specify parameters for which you want to have ratings, e.g. Packaging, Delivery, Behaviour, Policy etc', 'multivendorx' ),
                    'parent_options' => array(
                        array(
                            'key'=>'category',
                            'type'=> "text",
                            'class' => "nested-parent-class",
                            'name' => "nested-parent-name",
                            'label'=> __('category', 'multivendorx'),
                            'value'=> "category"
                        )
                    ),
                    'child_options' => array(
                    ),
                    'database_value' => $mvx_review_categories,
                ],
            ],
            'report-settings'   => [
                [
                    'key'       => 'custom_date_order_stat_report_mail',
                    'type'      => 'number',
                    'label'     => __( 'Set custom date for order stat report mail', 'multivendorx' ),
                    'hints'     => __( 'Email will send as per select dates ( put is blank for disabled it )', 'multivendorx' ),
                    'placeholder' => __('in days', 'multivendorx'),
                    'database_value' => '',
                ],
            ],
            'policy'  => [
                [
                    'key'       => 'store-policy',
                    'type'      => 'textarea',
                    'class'     =>  'mvx-setting-wpeditor-class',
                    'desc'      => __('Site will reflect admin created policy. However vendors can edit and override store policies', 'multivendorx'),
                    'label'     => __( 'Store Policy', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'shipping_policy',
                    'type'      => 'textarea',
                    'class'     =>  'mvx-setting-wpeditor-class',
                    'desc'      => __('Site will reflect admin created policy. However vendors can edit and override store policies', 'multivendorx'),
                    'label'     => __( 'Shipping Policy', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'refund_policy',
                    'type'      => 'textarea',
                    'class'     =>  'mvx-setting-wpeditor-class',
                    'desc'      => __('Site will reflect admin created policy. However vendors can edit and override store policies', 'multivendorx'),
                    'label'     => __( 'Refund Policy', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'cancellation_policy',
                    'type'      => 'textarea',
                    'class'     =>  'mvx-setting-wpeditor-class',
                    'desc'      => __('Site will reflect admin created policy. However vendors can edit and override store policies', 'multivendorx'),
                    'label'     => __( 'Cancellation / Return / Exchange Policy', 'multivendorx' ),
                    'database_value' => '',
                ],
            ],
            'disbursement'  => [
                [
                    'key'    => 'commission_include_coupon',
                    'label'   => __( 'Who will bear the Coupon Cost', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "commission_include_coupon",
                            'label'=> __('Tap to let the vendors bear the coupon discount charges of the coupons created by them', 'multivendorx'),
                            'value'=> "commission_include_coupon"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'admin_coupon_excluded',
                    'label'   => __( 'Exclude Admin Created Coupon', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "admin_coupon_excluded",
                            'label'=> __('Bear the coupon discount charges of the coupons created by you', 'multivendorx'),
                            'value'=> "admin_coupon_excluded"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'commission_calculation_on_tax',
                    'label'     => __( 'Commission Calculation On Tax', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'      => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "commission_calculation_on_tax",
                            'label'=> __('', 'multivendorx'),
                            'value'=> "commission_calculation_on_tax"
                        )
                    ),
                    'database_value' => array(),
                ],
                
                [
                    'key'    => 'give_tax',
                    'label'   => __( 'Tax', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "give_tax",
                            'label'=> __('Let vendor collect & manage tax amount', 'multivendorx'),
                            'value'=> "give_tax"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'give_shipping',
                    'label'   => __( 'Shipping', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "give_shipping",
                            'label'=> __('Allow sellers to collect & manage shipping charges', 'multivendorx'),
                            'value'=> "give_shipping"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'choose_payment_mode_automatic_disbursal',
                    'label'   => __( 'Disbursement Schedule', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "choose_payment_mode_automatic_disbursal",
                            'label'=> __('Schedule when vendors would receive their commission', 'multivendorx'),
                            'value'=> "choose_payment_mode_automatic_disbursal"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'payment_schedule',
                    'type'      => 'radio',
                    'depend_checkbox'    => 'choose_payment_mode_automatic_disbursal',
                    'label'     => __( 'Set Schedule', 'multivendorx' ),
                    'options' => array(
                        array(
                            'name'  => 'payment_schedule',
                            'key' => 'weekly',
                            'label' => __('Weekly', 'multivendorx'),
                            'value' => 'weekly'
                        ),
                        array(
                            'name'  => 'payment_schedule',
                            'key' => 'daily',
                            'label' => __('Daily', 'multivendorx'),
                            'value' => 'daily'
                        ),
                        array(
                            'name'  => 'payment_schedule',
                            'key' => 'monthly',
                            'label' => __('Monthly', 'multivendorx'),
                            'value' => 'monthly'
                        ),
                        array(
                            'name'  => 'payment_schedule',
                            'key' => 'fortnightly',
                            'label' => __('Fortnightly', 'multivendorx'),
                            'value' => 'fortnightly'
                        ),
                        array(
                            'name'  => 'payment_schedule',
                            'key' => 'hourly',
                            'label' => __('Hourly', 'multivendorx'),
                            'value' => 'hourly'
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'commission_threshold',
                    'label'   => __( 'Disbursement Threshold', 'multivendorx' ),
                    'type'    => 'number',
                    'desc'  =>  __('Add the minimum value required before payment is disbursed to the vendor', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'    => 'withdrawal_request',
                    'label'   => __( 'Allow Withdrawal Request', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "withdrawal_request",
                            'label'=> __('Let vendors withdraw payment prior to reaching the agreed disbursement value', 'multivendorx'),
                            'value'=> "withdrawal_request"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'commission_threshold_time',
                    'label'   => __( 'Withdrawal Locking Period', 'multivendorx' ),
                    'type'    => 'number',
                    'desc' => __('Refers to the minimum number of days required before a seller can send a withdrawal request', 'multivendorx'),
                    'placeholder'   => __('in days', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'    => 'commission_transfer',
                    'label'   => __( 'Withdrawal Charges', 'multivendorx' ),
                    'type'    => 'number',
                    'desc' => __('Vendors will be charged this amount per withdrawal after the quota of free withdrawals is over.', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'    => 'no_of_orders',
                    'label'   => __( 'Number of Free Withdrawals', 'multivendorx' ),
                    'type'    => 'number',
                    'desc' => __('Number of free withdrawal requests.', 'multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'order_withdrawl_status',
                    'type'      => 'multi-select',
                    'label'     => __( 'Available Order Status for Withdrawal', 'multivendorx' ),
                    'desc'        => __( 'Withdrawal request would be available in case of these order statuses', 'multivendorx' ),
                    'options' => array(
                        array(
                            'key'=> "on-hold",
                            'label'=> __('On hold', 'multivendorx'),
                            'value'=> "on-hold"
                        ),
                        array(
                            'key'=> "processing",
                            'label'=> __('Processing', 'multivendorx'),
                            'value'=> "processing"
                        ),
                        array(
                            'key'=> "completed",
                            'label'=> __('Completed', 'multivendorx'),
                            'value'=> "completed"
                        ),
                    ),
                    'database_value' => '',
                ]
            ],
            'order' =>  [
                [
                    'key'    => 'disallow_vendor_order_status',
                    'label'   => __( 'Order Status Control', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "disallow_vendor_order_status",
                            'label'=> __('Disallow sellers to change their order status', 'multivendorx'),
                            'value'=> "disallow_vendor_order_status"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'     => 'display_suborder_in_mail',
                    'label'   => __( 'Display Suborder in mail', 'multivendorx' ),
                    'class'   => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "display_suborder_in_mail",
                            'label'=> __('Display suborder number in mail.', 'multivendorx'),
                            'value'=> "display_suborder_in_mail"
                        )
                    ),
                    'database_value' => array(),
                ],
            ],
            'store-location' => [
                [
                    'key'    => 'enable_store_map_for_vendor',
                    'label'   => __( 'Enable store map for vendors', 'multivendorx' ),
                    'class'     => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "enable_store_map_for_vendor",
                            'label'=> __('', 'multivendorx'),
                            'value'=> "enable_store_map_for_vendor"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'choose_map_api',
                    'type'      => 'select',
                    'bydefault' =>  'google_map_set',
                    'label'     => __( 'Choose Your Map', 'multivendorx' ),
                    'desc'      => __( 'Choose your preferred map.', 'multivendorx' ),
                    'options' => array(
                        array(
                            'key'=> "google_map_set",
                            'label'=> __('Google map', 'multivendorx'),
                            'value'=> __('google_map_set', 'multivendorx'),
                        ),
                        array(
                            'key'=> "mapbox_api_set",
                            'selected'  => true,
                            'label'=> __('Mapbox map', 'multivendorx'),
                            'value'=> __('mapbox_api_set', 'multivendorx'),
                        )
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'google_api_key',
                    'type'      => 'text',
                    'depend'    => 'choose_map_api',
                    'dependvalue'       =>  'google_map_set',
                    'label'     => __( 'Google Map API key', 'multivendorx' ),
                    'desc'      => __('<a href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key" target="_blank">Click here to generate key</a>','multivendorx'),
                    'database_value' => '',
                ],
                [
                    'key'       => 'mapbox_api_key',
                    'type'      => 'text',
                    'depend'    => 'choose_map_api',
                    'dependvalue'       =>  'mapbox_api_set',
                    'label'     => __( 'Mapbox access token', 'multivendorx' ),
                    'desc' => __('<a href="https://docs.mapbox.com/help/getting-started/access-tokens/" target="_blank">Click here to generate access token</a>','multivendorx'),
                    'database_value' => '',
                ],
            ],
            'refund-management'   => [
                [
                    'key'    => 'customer_refund_status',
                    'label'   => __( 'Available Status for Refund', 'multivendorx' ),
                    'type'    => 'checkbox_select',
                    'select_deselect'   =>  true,
                    'desc'  =>  __("Customers would be able to avail a refund only if their order is at the following stage/s", 'multivendorx'),
                    'class'     => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'=> "pending",
                            'label'=> __('Pending', 'multivendorx'),
                            'value'=> "pending"
                        ),
                        array(
                            'key'=> "on-hold",
                            'label'=> __('On hold', 'multivendorx'),
                            'value'=> "on-hold"
                        ),
                        array(
                            'key'=> "processing",
                            'label'=> __('Processing', 'multivendorx'),
                            'value'=> "processing"
                        ),
                        array(
                            'key'=> "completed",
                            'label'=> __('Completed', 'multivendorx'),
                            'value'=> "completed"
                        ),
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'refund_days',
                    'type'      => 'number',
                    'label'     => __( 'Refund Claim Period (In Days)', 'multivendorx' ),
                    'props'     => array(
                        'max'  => 365
                    ),
                    'hints'     => __( 'The duration till which the refund request is available/valid', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'refund_order_msg',
                    'type'      => 'textarea',
                    'label'     => __( 'Reasons For Refund', 'multivendorx' ),
                    'desc'      => __( 'Add reasons for a refund. Use || to separate each reason. Options will appear as a radio button to customers', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'disable_refund_customer_end',
                    'label'   => __( 'Disable refund request for customer', 'multivendorx' ),
                    'type'    => 'checkbox',
                    'class'     => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'=> "disable_refund_customer_end",
                            'label'=> __('Remove capability to customer from refund request', 'multivendorx'),
                            'value'=> "disable_refund_customer_end"
                        )
                    ),
                    'database_value' => array(),
                ],
            ],
            'payment-stripe-connect' => [
                [
                    'key'    => 'testmode',
                    'label'   => __( 'Stripe Enable Test Mode', 'multivendorx' ),
                    'type'    => 'checkbox',
                    'class'     => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'=> "testmode",
                            'label'=> __('', 'multivendorx'),
                            'value'=> "testmode"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'       => 'test_client_id_block',
                    'type'      => 'blocktext',
                    'label'     => __( 'Config redirect URI', 'multivendorx' ),
                    'valuename' => '<code>' . admin_url('admin-ajax.php') . "?action=marketplace_stripe_authorize". '</code>',
                    'blocktext' => '<code>' . admin_url('admin-ajax.php') . "?action=marketplace_stripe_authorize". '</code><a href="https://dashboard.stripe.com/account/applications/settings" target="_blank">'.__('Copy the URI and configured stripe redirect URI with above.', 'multivendorx').'</a>',
                    'database_value' => '',
                ],
                [
                    'key'       => 'test_client_id',
                    'type'      => 'text',
                    'depend_checkbox'    => 'testmode',
                    'label'     => __( 'Stripe Test Client ID', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'live_client_id',
                    'type'      => 'text',
                    'not_depend_checkbox'    => 'testmode', // not_depend_checkbox parameter works when testmode this key checkbox is disabled
                    'label'     => __( 'Stripe Live Client ID', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'test_publishable_key',
                    'type'      => 'text',
                    'depend_checkbox'    => 'testmode', // depend_checkbox parameter works when testmode this key checkbox is enabled
                    'label'     => __( 'Stripe Test Publishable key', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'live_publishable_key',
                    'type'      => 'text',
                    'not_depend_checkbox'    => 'testmode',
                    'label'     => __( 'Stripe Live Publishable key', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'test_secret_key',
                    'type'      => 'text',
                    'depend_checkbox'    => 'testmode',
                    'label'     => __( 'Stripe Test Secret key', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'live_secret_key',
                    'type'      => 'text',
                    'not_depend_checkbox'    => 'testmode',
                    'label'     => __( 'Stripe Live Secret key', 'multivendorx' ),
                    'database_value' => '',
                ],

            ],
            'buddypress' => [
                [
                    'key'    => 'profile_sync',
                    'label'   => __( 'Vendor Capability Sync', 'multivendorx' ),
                    'type'    => 'checkbox',
                    'class'     => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'=> "profile_sync",
                            'label'=> __('Ignore if BuddyPress is not active', 'multivendorx'),
                            'value'=> "profile_sync"
                        )
                    ),
                    'database_value' => array(),
                ],
            ],
            'payment-payout' => [
                [
                    'key'       => 'client_id',
                    'type'      => 'text',
                    'label'     => __( 'Paypal Client ID', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'client_secret',
                    'type'      => 'text',
                    'label'     => __( 'Paypal Client Secret', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'is_asynchronousmode',
                    'label'   => __( 'Enable Asynchronous Mode', 'multivendorx' ),
                    'type'    => 'checkbox',
                    'class'     => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_asynchronousmode",
                            'label'=> __('', 'multivendorx'),
                            'value'=> "is_asynchronousmode"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'is_testmode',
                    'label'   => __( 'Enable Test Mode', 'multivendorx' ),
                    'type'    => 'checkbox',
                    'class'     => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_testmode",
                            'label'=> __('', 'multivendorx'),
                            'value'=> "is_testmode"
                        )
                    ),
                    'database_value' => array(),
                ],
            ],
            'payment-masspay' => [
                [
                    'key'       => 'api_username',
                    'type'      => 'text',
                    'label'     => __( 'Paypal API Username', 'multivendorx' ),
                    'hints'     => __( 'Number of Days for the refund period.', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'api_pass',
                    'type'      => 'text',
                    'label'     => __( 'Paypal API Password', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'api_signature',
                    'type'      => 'text',
                    'label'     => __( 'Paypal API Signature', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'is_testmode',
                    'label'   => __( 'Enable Test Mode', 'multivendorx' ),
                    'type'    => 'checkbox',
                    'class'     => 'mvx-toggle-checkbox',
                    'options' => array(
                        array(
                            'key'=> "is_testmode",
                            'label'=> __('', 'multivendorx'),
                            'value'=> "is_testmode"
                        )
                    ),
                    'database_value' => array(),
                ],
            ],
            'create_announcement'   =>  [
                [
                    'key'       => 'announcement_title',
                    'type'      => 'text',
                    'label'     => __( 'Title (required)', 'multivendorx' ),
                    'props'     => array(
                        'required'  => true
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'announcement_url',
                    'type'      => 'url',
                    'label'     => __( 'Enter Url', 'multivendorx' ),
                    'props'     => array(
                        //'required'  => true
                    ),
                    'database_value' => '',
                ],
                [
                    'label' => __('Enter Content', 'multivendorx'),
                    'type' => get_mvx_vendor_settings('mvx_tinymce_api_section', 'settings_general') ? 'wpeditor' : 'textarea', 
                    'key' => 'announcement_content', 
                    'database_value' => ''
                ],
                [
                    'key'       => 'announcement_vendors',
                    'type'      => 'multi-select',
                    'select_deselect'   =>  true,
                    'label'     => __( 'Vendors', 'multivendorx' ),
                    'options' => ($MVX->vendor_rest_api->mvx_show_vendor_name()->data),
                    'database_value' => '',
                ]
            ],
            'create_knowladgebase'   =>  [
                [
                    'key'       => 'knowladgebase_title',
                    'type'      => 'text',
                    'label'     => __( 'Title (required)', 'multivendorx' ),
                    'props'     => array(
                        'required'  => true
                    ),
                    'database_value' => '',
                ],
                [
                    'label' => __('Enter Content', 'multivendorx'),
                    'type' => get_mvx_vendor_settings('mvx_tinymce_api_section', 'settings_general') ? 'wpeditor' : 'textarea', 
                    'key' => 'knowladgebase_content', 
                    'database_value' => ''
                ],
            ],
            'add-new' => [
                [
                    'key'       => 'user_login',
                    'type'      => 'text',
                    'label'     => __( 'Username (required)', 'multivendorx' ),
                    'desc' => __('Usernames cannot be changed.', 'multivendorx'),
                    'props'     => array(
                        'required'  => true
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'password',
                    'type'      => 'password',
                    'label'     => __( 'Password', 'multivendorx' ),
                    'desc'     => __('Keep it blank for not to update.', 'multivendorx'),
                    'props'     => array(
                        'required'  => true
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'first_name',
                    'type'      => 'text',
                    'label'     => __( 'First Name', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'last_name',
                    'type'      => 'text',
                    'label'     => __( 'Last Name', 'multivendorx' ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'user_email',
                    'type'      => 'email',
                    'label'     => __( 'Email (required)', 'multivendorx' ),
                    'props'     => array(
                        'required'  => true
                    ),
                    'database_value' => '',
                ],
                [
                    'key'       => 'user_nicename',
                    'type'      => 'text',
                    'label'     => __( 'Nick Name (required)', 'multivendorx' ),
                    'props'     => array(
                        'required'  => true
                    ),
                    'database_value' => '',
                ],
                [
                    'key'    => 'vendor_profile_image',
                    'label'   => __( 'Profile Image', 'multivendorx' ),
                    'type'    => 'file',
                    'width' =>  75,
                    'height'    => 75,
                    'database_value' => array(),
                ],
                
            ],
            'settings-min-max' => [
                [
                    'key'    => 'enable_min_max_quantity',
                    'label'   => __( 'Enable Min/Max Quantities', 'multivendorx' ),
                    'desc'    => __( 'Activating this will set min and max quantities for selected products.', 'multivendorx' ),
                    'class'   => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "enable_min_max_quantity",
                            'value'=> "enable_min_max_quantity"
                        )
                    ),
                    'database_value' => array(),
                ],
                [
                    'key'    => 'enable_min_max_amount',
                    'label'   => __( 'Enable Min/Max Amount', 'multivendorx' ),
                    'desc'    => __( 'Activating this will set min and max amount for selected products.', 'multivendorx' ),
                    'class'   => 'mvx-toggle-checkbox',
                    'type'    => 'checkbox',
                    'options' => array(
                        array(
                            'key'=> "enable_min_max_amount",
                            'value'=> "enable_min_max_amount"
                        )
                    ),
                    'database_value' => array(),
                ],
            ],
        ];

        return apply_filters('mvx_settings_fields_details', $settings_fields);
    }
}


if (!function_exists('mvx_admin_backend_tab_settings')) {
    function mvx_admin_backend_tab_settings() {
        global $MVX;
        
        $general_settings_page_endpoint = array(
            array(
                'tablabel'       =>  __('General', 'multivendorx'),
                'apiurl'         =>  'mvx_module/v1/save_dashpages',
                'description'    =>  __('Configure the basic setting of the marketplace.', 'multivendorx'),
                'icon'           =>  'icon-tab-general',
                'submenu'       =>  'settings',
                'modulename'     =>  'settings-general'
            ),
            array(
                'tablabel'      =>  __('Registration Form', 'multivendorx'),
                'description'   =>  __('Customise personalised seller registration form for marketplace.', 'multivendorx'),
                'icon'          =>  'icon-tab-registration-form',
                'submenu'       =>  'settings',
                'modulename'    => 'registration'
            ),
            array(
                'tablabel'      =>  __('Seller Dashboard', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __("Manage the appearance of your seller's dashboard.", 'multivendorx'),
                'icon'          =>  'icon-tab-seller-dashbaord',
                'submenu'       =>  'settings',
                'modulename'    =>  'seller-dashbaord'
            ),
            array(
                'tablabel'      =>  __('Store', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __("Manage setting related to the sellers shop.", 'multivendorx'),
                'icon'          =>  'icon-tab-store',
                'submenu'       =>  'settings',
                'modulename'    =>  'store'
            ),
            array(
                'tablabel'      =>  __('Products', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __("Select the type of product that best suits your marketplace.", 'multivendorx'),
                'icon'          =>  'icon-tab-products',
                'submenu'       =>  'settings',
                'modulename'    =>  'products'
            ),
            array(
                'tablabel'      =>  __('Products Capability', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __("Manage product-related capabilities that you want sellers to have.", 'multivendorx'),
                'icon'          =>  'icon-tab-products-capability',
                'submenu'       =>  'settings',
                'modulename'    =>  'products-capability'
            ),
            array(
                'tablabel'      =>  __('SPMV(Single Product Multiple Vendor)', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __("Give sellers the option to add other seller's products into their store inventory.", 'multivendorx'),
                'icon'          =>  'icon-tab-SPMV',
                'submenu'       =>  'settings',
                'modulename'    =>  'spmv-pages'
            ),
            array(
                'tablabel'      =>  __('Commission', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __("Configure commission settings to customise your commission plan.", 'multivendorx'),
                'icon'          =>  'icon-tab-commissions',
                'submenu'       =>  'settings',
                'modulename'    =>  'commissions'
            ),
            array(
                'tablabel'      =>  __('Disbursement', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('Manage payment and disbursement setting of your site.', 'multivendorx'),
                'icon'          =>  'icon-tab-disbursement',
                'submenu'       =>  'settings',
                'modulename'    =>  'disbursement'
            ),
            array(
                'tablabel'      =>  __('Policy', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('Add policies that are applicable to your site.', 'multivendorx'),
                'icon'          =>  'icon-tab-policy',
                'submenu'       =>  'settings',
                'modulename'    =>  'policy'
            ),
            array(
                'tablabel'      =>  __('Orders', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __("Manage vendor's order releated capabilities", 'multivendorx'),
                'icon'          =>  'icon-tab-orders',
                'submenu'       =>  'settings',
                'modulename'    =>  'order'
            ),
            array(
                'tablabel'      =>  __('Refunds', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('Set conditions for refund requests.', 'multivendorx'),
                'icon'          =>  'icon-tab-refunds',
                'submenu'       =>  'settings',
                'modulename'    =>  'refund-management'
            ),
            array(
                'tablabel'      =>  __('Reviews & Rating', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('Manage settings for product and store review.', 'multivendorx'),
                'icon'          =>  'icon-tab-reviews-and-rating',
                'submenu'       =>  'settings',
                'modulename'    =>  'review-management'
            ),
            array(
                'tablabel'      =>  __('Social', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('Create a platform for seller-customer interaction.', 'multivendorx'),
                'icon'          =>  'icon-tab-social',
                'submenu'       =>  'settings',
                'modulename'    =>  'social'
            ),
            array(
                'tablabel'      =>  __('Min-Max Quantities', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('Manage Min-Max Quantities/Amount', 'multivendorx'),
                'icon'          =>  'icon-tab-min-max',
                'submenu'       =>  'settings',
                'modulename'    =>  'settings-min-max'
            ),
        );
        
        if (!mvx_is_module_active('spmv')) {
           unset($general_settings_page_endpoint[6]);
        }

        if (!mvx_is_module_active('store-review')) {
           unset($general_settings_page_endpoint[12]);
        }

        if (!mvx_is_module_active('store-policy')) {
           unset($general_settings_page_endpoint[9]);
        }

        if (!mvx_is_module_active('marketplace-refund')) {
           unset($general_settings_page_endpoint[11]);
        }

        if (!mvx_is_module_active('min-max')) {
            unset($general_settings_page_endpoint[14]);
        }
        
        $payment_page_endpoint = array(
            array(
                'tablabel'      =>  __('PayPal Masspay', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('PayPal MassPay lets you pay out a large number of affiliates very easily and quickly.', 'multivendorx'),
                'icon'          =>  'icon-tab-paypal-masspay',
                'submenu'       =>  'payment',
                'modulename'     =>  'payment-masspay'
            ),
            array(
                'tablabel'      =>  __('PayPal Payout', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('PayPal Payout makes it easy for you to pay multiple sellers at the sametime.', 'multivendorx'),
                'icon'          =>  'icon-tab-paypal-payout',
                'submenu'       =>  'payment',
                'modulename'     =>  'payment-payout'
            ),
            array(
                'tablabel'      =>  __('Stripe Connect', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('Connect to vendors stripe account and make hassle-free transfers as scheduled.', 'multivendorx'),
                'icon'          =>  'icon-tab-stripe-connect',
                'submenu'       =>  'payment',
                'modulename'     =>  'payment-stripe-connect'
            )
        );

        $advance_page_endpoint = array(
            array(
                'tablabel'      =>  __('Buddypress', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'description'   =>  __('Default description', 'multivendorx'),
                'icon'          =>  'ico-store-icon',
                'modulename'     =>  'buddypress'
            )
        );

        $analytics_page_endpoint = array(
            array(
                'tablabel'      =>  __('Overview', 'multivendorx'),
                'description'   =>  __('View the overall performance of the site', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'icon'          =>  'icon-tab-overview',
                'submenu'       =>  'analytics',
                'modulename'     =>  'admin-overview'
            ),
            array(
                'tablabel'      =>  __('Vendor', 'multivendorx'),
                'description'   =>  __('Get comprehensive reports on vendor sales and orders', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'icon'          =>  'icon-tab-vendor',
                'submenu'       =>  'analytics',
                'modulename'     =>  'vendor'
            ),
            array(
                'tablabel'      =>  __('Product', 'multivendorx'),
                'description'   =>  __('View reports product sales and order', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'icon'          =>  'icon-tab-products',
                'submenu'       =>  'analytics',
                'modulename'     =>  'product'
            ),
            array(
                'tablabel'      =>  __('Transaction History', 'multivendorx'),
                'description'   =>  __('Get detailed reports on vendor commission', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/save_dashpages',
                'icon'          =>  'icon-tab-transaction-history',
                'submenu'       =>  'analytics',
                'modulename'     =>  'transaction-history'
            )
        );

        $marketplace_vendors = array(
            array(
                'tablabel'      =>  __('Personal', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-personal',
                'submenu'       =>  'vendor',
                'modulename'     =>  'vendor-personal'
            ),
            array(
                'tablabel'      =>  __('Store', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-store',
                'submenu'       =>  'vendor',
                'modulename'     =>  'vendor-store'
            ),
            array(
                'tablabel'      =>  __('Social', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-social',
                'submenu'       =>  'vendor',
                'modulename'     =>  'vendor-social'
            ),
            array(
                'tablabel'      =>  __('Payment', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-payment',
                'submenu'       =>  'vendor',
                'modulename'     =>  'vendor-payments'
            ),
            array(
                'tablabel'      =>  __('Vendor Application', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-application',
                'submenu'       =>  'vendor',
                'modulename'     =>  'vendor-application'
            ),
        );

        $marketplace_new_vendor = array(
            array(
                'tablabel'      =>  __('Vendor', 'multivendorx'),
                'description'   =>  __('Create MultivendorX vendor', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-personal',
                'submenu'       =>  'vendor',
                'modulename'     =>  'add-new'
            )
        );

        if (is_mvx_shipping_module_active()) {
            $marketplace_vendors[] = array(
                'tablabel'      =>  __('Vendor Shipping', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-shipping',
                'submenu'       =>  'vendor',
                'modulename'     =>  'vendor-shipping'
            );
        }

        if (mvx_is_module_active('follow-store')) {
            $marketplace_vendors[] = array(
                'tablabel'      =>  __('Vendor Followers', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-follower',
                'submenu'       =>  'vendor',
                'modulename'     =>  'vendor-followers'
            );
        }

        if (mvx_is_module_active('store-policy')) {
            $marketplace_vendors[] = array(
                'tablabel'      =>  __('Vendor Policy', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-vendor-policy',
                'submenu'       =>  'vendor',
                'modulename'     =>  'vendor-policy'
            );
        }

        $marketplace_workboard = array(
            array(
                'tablabel'      =>  __('Taskboard', 'multivendorx'),
                'description'   =>  __('Keeps track of all important marketplace chores', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-tab-activity-reminder',
                'submenu'       =>  'work-board',
                'modulename'     =>  'activity-reminder'
            ),
            array(
                'tablabel'      =>  __('Announcement', 'multivendorx'),
                'description'   =>  __('Broadcast important messages, news and announcements to single or multiple sellers.Read more to learn about this feature.', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-tab-announcement',
                'submenu'       =>  'work-board',
                'modulename'     =>  'announcement'
            ),
            array(
                'tablabel'      =>  __('Knowledgebase', 'multivendorx'),
                'description'   =>  __('"Share tutorials, best practices, "how-to" guides or whatever you feel is appropriate with your vendors. Read More ', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-tab-knowladgebase',
                'submenu'       =>  'work-board',
                'modulename'     =>  'knowladgebase'
            ),
            array(
                'tablabel'      =>  __('Store Review', 'multivendorx'),
               'description'   =>  __('View feeckback received from cutomers. ', 'multivendorx'),
                 'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-tab-store-review',
                'submenu'       =>  'work-board',
                'modulename'     =>  'store-review'
            ),
            array(
                'tablabel'      =>  __('Report Abuse', 'multivendorx'),
               'description'   =>  __('Keep track of complaints and reports filed by customers.  ', 'multivendorx'),
                 'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-tab-report-abuse',
                'submenu'       =>  'work-board',
                'modulename'     =>  'report-abuse'
            ),
            array(
                'tablabel'      =>  __('Question & Answer', 'multivendorx'),
                'description'   =>  __('View and publish questions sent to sellers by their customers.', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-tab-question-and-answer',
                'submenu'       =>  'work-board',
                'modulename'     =>  'question-ans'
            ),
            array(
                'tablabel'      =>  __('Request refund', 'multivendorx'),
                'description'   =>  __('Records request for refund made by the customers', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-tab-question-and-answer',
                'submenu'       =>  'work-board',
                'modulename'     =>  'refund-request'
            ),
            array(
                'tablabel'      =>  __('Refunded orders', 'multivendorx'),
                'description'   =>  __('Records of all refunded orders', 'multivendorx'),
                'apiurl'        =>  'mvx_module/v1/update_vendor',
                'icon'          =>  'icon-tab-question-and-answer',
                'submenu'       =>  'work-board',
                'modulename'     =>  'refunded-order'
            )
        );

        if (!mvx_is_module_active('announcement')) {
            unset($marketplace_workboard[1]);
        }
        if (!mvx_is_module_active('report-abuse')) {
            unset($marketplace_workboard[4]);
        }
        if (!mvx_is_module_active('knowladgebase')) {
            unset($marketplace_workboard[2]);
        }
        if (!mvx_is_module_active('store-review')) {
            unset($marketplace_workboard[3]);
        }
        if (!mvx_is_module_active('marketplace-refund')) {
            unset($marketplace_workboard[6]);
            unset($marketplace_workboard[7]);
        }

        $status_tools = array(
            /*array(
                'tablabel'      =>  __('Version Control', 'multivendorx'),
                'description'   =>  __('View the overall performance of the site', 'multivendorx'),
                'icon'          =>  'icon-tab-version-control',
                'submenu'       =>  'status-tools',
                'modulename'     =>  'version-control'
            ),*/
            array(
                'tablabel'      =>  __('Database Tools', 'multivendorx'),
                'description'   =>  __('Get comprehensive reports on vendor sales and orders', 'multivendorx'),
                'icon'          =>  'icon-tab-database-tools',
                'submenu'       =>  'status-tools',
                'modulename'     =>  'database-tools'
            ),
            array(
                'tablabel'      =>  __('System Status', 'multivendorx'),
                'description'   =>  __('View reports product sales and order', 'multivendorx'),
                'icon'          =>  'icon-tab-system-status',
                'submenu'       =>  'status-tools',
                'modulename'     =>  'system-status'
            ),
            array(
                'tablabel'      =>  __('Migration', 'multivendorx'),
                'link'          =>  admin_url( 'index.php?page=mvx-migrator' ),
                'description'   =>  __('Get detailed reports on vendor commission', 'multivendorx'),
                'icon'          =>  'icon-tab-migration',
                'submenu'       =>  'status-tools',
                'modulename'     =>  'transaction-history'
            ),
            array(
                'icon'          =>  'icon-tab-setup-widget',
                'modulename'       =>  'setup-widget',
                'tablabel'      =>  __('Setup Widget', 'multivendorx'),
                'link'          =>  admin_url( 'index.php?page=mvx-setup' ),
                'description'   =>  __('Default description', 'multivendorx'),
            ),
        );

        if (!$MVX->multivendor_migration->mvx_is_marketplace()) {
            unset( $status_tools[2] );
        }

        $mvx_all_backend_tab_list = apply_filters('mvx_multi_tab_array_list', array(
            'marketplace-advance-settings'      => $advance_page_endpoint,
            'marketplace-analytics'             => $analytics_page_endpoint,
            'status-tools'                      => array_values($status_tools),
            'marketplace-payments'              => $payment_page_endpoint,
            'marketplace-general-settings'      => array_values($general_settings_page_endpoint),
            'marketplace-vendors'               => $marketplace_vendors,
            'marketplace-new-vendor'            => $marketplace_new_vendor,
            'marketplace-workboard'             => array_values($marketplace_workboard)
        ));
        return $mvx_all_backend_tab_list;
    }
}


if (!function_exists('mvx_is_module_active')) {

    /**
     * check is module active
     * @return array
     */
    function mvx_is_module_active($module_name = '') {
        global $MVX;
        if (empty($module_name)) {
            return false;
        }
        return is_current_module_active($module_name);   
    }
}

if (!function_exists('mvx_list_of_all_modules')) {
    function mvx_list_of_all_modules() {
        return mvx_list_all_modules();   
    }
}

if (!function_exists('mvx_string_wpml')) {
    function mvx_string_wpml($input) {
        do_action( 'wpml_register_single_string', 'MVX', $input, $input );
        if (function_exists('icl_t')) {
            return icl_register_string ('MVX', '' . $input . '', '' . $input . '');
        } else {
            return $input;
        }
    }
}

if (!function_exists('mvx_active_product_types')) {
    function mvx_active_product_types() {
        $active_product_types = array();
        if (mvx_is_module_active('simple')) {
            array_push($active_product_types, 'simple');
        }
        return apply_filters('mvx_active_product_types', $active_product_types);
    }
}

if (!function_exists('mvx_list_all_modules')) {
    function mvx_list_all_modules() {
        global $MVX;
        $thumbnail_dir = $MVX->plugin_url.'assets/images/modules';
        $thumbnail_path = $MVX->plugin_path.'assets/images/modules';
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        $mvx_pro_is_active = is_plugin_active('mvx-pro/mvx-pro.php') ? true : false;
                    
        $mvx_all_modules   =   [
            [
                'label' =>  __('Marketplace Types', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'simple',
                        'name'         => __( 'Simple (Downloadable & Virtual)', 'multivendorx' ),
                        'description'  => __( 'Covers the vast majority of any tangible products you may sell or ship i.e books', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/simple-product',
                        'parent_category' => __( 'Marketplace Types.', 'multivendorx' ),
                    ],
                    [
                        'id'           => 'variable',
                        'name'         => __( 'Variable', 'multivendorx' ),
                        'description'  => __( 'A product with variations, like different SKU, price, stock option, etc.', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/pricing',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/variable-product',
                    ],
                    [
                        'id'           => 'external',
                        'name'         => __( 'External', 'multivendorx' ),
                        'description'  => __( 'Grants vendor the option to  list and describe on admin website but sold elsewhere', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/pricing',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/external-product/',
                    ],
                    [
                        'id'           => 'grouped',
                        'name'         => __( 'Grouped', 'multivendorx' ),
                        'description'  => __( 'A cluster of simple related products that can be purchased individually', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/grouped-product',
                    ],
                    [
                        'id'           => 'booking',
                        'name'         => __( 'Booking', 'multivendorx' ),
                        'description'  => __( 'Allow customers to book appointments, make reservations or rent equipment etc', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('WooCommerce Booking', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/docs/knowladgebase/appointment-product/',
                                'is_active' => is_plugin_active('woocommerce-bookings/woocommerce-bookings.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/booking-product',
                    ],
                    [
                        'id'           => 'appointment',
                        'name'         => __( 'Appointments', 'multivendorx' ),
                        'description'  => __( 'Allow customers to book appointments', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('WooCommerce Appointment', 'multivendorx'),
                                'plugin_link'   => 'https://bookingwp.com/plugins/woocommerce-appointments/',
                                'is_active'     => is_plugin_active('woocommerce-appointments/woocommerce-appointments.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/appointment-product/',
                    ],
                    [
                        'id'           => 'subscription',
                        'name'         => __( 'Subscription', 'multivendorx' ),
                        'description'  => __( 'Let customers subscribe to your products or services and pay weekly, monthly or yearly ', 'multivendorx' ),  
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('WooCommerce Subscription', 'multivendorx'),
                                'plugin_link'   => 'https://woocommerce.com/products/woocommerce-subscriptions/',
                                'is_active' => is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php') ? true : false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/pricing',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/subscription-product',
                    ],
                    [
                        'id'           => 'accommodation',
                        'name'         => __( 'Accommodation', 'multivendorx' ),
                        'description'  => __( 'Grant your guests the ability to quickly book overnight stays in a few clicks', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('WooCommerce Accommodation & Booking', 'multivendorx'),
                                'plugin_link'   => 'https://woocommerce.com/products/woocommerce-accommodation-bookings/',
                                'is_active' => is_plugin_active('woocommerce-bookings/woocommerce-bookings.php') && is_plugin_active('woocommerce-accommodation-bookings/woocommerce-accommodation-bookings.php') ? true : false,
                            ),
                            array(
                                'plugin_name'   => __('WooCommerce Booking', 'multivendorx'),
                                'plugin_link'   => 'https://woocommerce.com/products/woocommerce-bookings/',
                                'is_active' => is_plugin_active('woocommerce-bookings/woocommerce-bookings.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/accommodation-product',
                    ],
                    [
                        'id'           => 'bundle',
                        'name'         => __( 'Bundle', 'multivendorx' ),
                        'description'  => __( 'Offer personalized product bundles, bulk discount packages, and assembled products.', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Product Bundle', 'multivendorx'),
                                'plugin_link'   => 'https://woocommerce.com/products/product-bundles/',
                                'is_active' => is_plugin_active('woocommerce-product-bundles/woocommerce-product-bundles.php') ? true : false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/bundle-product',
                    ],
                    [
                        'id'           => 'auction',
                        'name'         => __( 'Auction', 'multivendorx' ),
                        'description'  => __( 'Implement an auction system similar to eBay on your store', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Simple Auction', 'multivendorx'),
                                'plugin_link'   => '',
                                'is_active' => is_plugin_active('woocommerce-simple-auctions/woocommerce-simple-auctions.php') ? true : false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/auction-product',
                    ],
                    [
                        'id'           => 'rental-pro',
                        'name'         => __( 'Rental Pro', 'multivendorx' ),
                        'description'  => __( 'Perfect for those desiring to offer rental, booking, or real state agencies or services.', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Rental Pro', 'multivendorx'),
                                'plugin_link'   => 'https://codecanyon.net/item/rnb-woocommerce-rental-booking-system/14835145?ref=redqteam',
                                'is_active' => is_plugin_active('woocommerce-rental-and-booking/redq-rental-and-bookings.php') ? true : false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/rental-product',
                    ],
                    [
                        'id'           => 'gift-card',
                        'name'         => __( 'Gift Cards', 'multivendorx' ),
                        'description'  => __( "Activate this module to offer gift cards, boosting your store's earnings and attracting fresh clientele.", 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('YITH WooCommerce Gift Cards', 'multivendorx'),
                                'plugin_link'   => 'https://wordpress.org/plugins/yith-woocommerce-gift-cards/',
                                'is_active' => is_plugin_active('yith-woocommerce-gift-cards/init.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/gift-card/',
                    ],
                ]
            ],
            [
                'label' =>  __('Seller management ', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'identity-verification',
                        'name'         => __( 'Seller Identity Verification', 'multivendorx' ),
                        'description'  => __( 'Verify vendors on the basis of Id documents, Address  and Social Media Account  ', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/identity-verifictaion',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-identity-verification'),
                    ],
                ]
            ],
            [
                'label' =>  __('Product management ', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'spmv',
                        'name'         => __( 'Single Product Multiple Vendor', 'multivendorx' ),
                        'description'  => __( 'Lets multiple vendors sell the same products ', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/single-product-multiple-vendors-spmv',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=spmv-pages'),
                    ],
                    [
                        'id'           => 'import-export',
                        'name'         => __( 'Import Export  ', 'multivendorx' ),
                        'description'  => __( 'Helps vendors seamlessly import or export product data using CSV etc', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/import-export',
                    ],
                    [
                        'id'           => 'store-inventory',
                        'name'         => __( 'Store Inventory', 'multivendorx' ),
                        'description'  => __( 'Present vendors with the choice to handle normal product quantities, set low inventory and no inventory alarms and manage a subscriber list for the unavailable products.', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/store-inventory',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-store-inventory'),
                    ],
                    [
                        'id'           => 'min-max',
                        'name'         => __( 'Min Max Quantities', 'multivendorx' ),
                        'description'  => __( 'Set a minimum or maximum purchase quantity or amount for the products of your marketplace.', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/non-knowledgebase/min-max-quantities/',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-min-max'),
                    ],
                ]
            ],
            [
                'label' =>  __('Payment', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'bank-payment',
                        'name'         => __( 'Bank Transfer', 'multivendorx' ),
                        'description'  => __( "Manually transfer money directly to the vendor's bank account.", 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/direct-bank-transfer/',
                    ],
                    [
                        'id'           => 'paypal-masspay',
                        'name'         => __( 'PayPal Masspay', 'multivendorx' ),
                        'description'  => __( 'Schedule payment to multiple vendors at the same time.', 'multivendorx' ),
                        'plan'         => 'free',
                       
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/paypal-masspay/',
                    ],
                    [
                        'id'           => 'paypal-payout',
                        'name'         => __( 'PayPal Payout', 'multivendorx' ),
                        'description'  => __( 'Send payments automatically to multiple vendors as per scheduled', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/paypal-payout',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=payment&name=payment-payout'),
                    ],
                    [
                        'id'           => 'paypal-marketplace',
                        'name'         => __( 'PayPal Marketplace (Real time Split)', 'multivendorx' ),
                        'description'  => __( 'Using  split payment pay vendors instantly after a completed order ', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active' => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/paypal-marketplace-real-time-split/',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=payment&name=payment-payout'),
                    ],
                    [
                        'id'           => 'stripe-connect',
                        'name'         => __( 'Stripe Connect', 'multivendorx' ),
                        'description'  => __( 'Connect to vendors stripe account and make hassle-free transfers as scheduled.', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/stripe-connect',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=payment&name=payment-stripe-connect'),
                    ],
                    [
                        'id'           => 'stripe-marketplace',
                        'name'         => __( 'Stripe Marketplace (Real time Split)', 'multivendorx' ),
                        'description'  => __( 'Real-Time Split payments pays vendor directly after a completed order', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active' => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/stripe-marketplace',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=payment&name=payment-stripe-connect'),
                    ],
                    [
                        'id'           => 'mangopay',
                        'name'         => __( 'Mangopay', 'multivendorx' ),
                        'description'  => __( 'Gives the benefit of both realtime split transfer and scheduled distribution', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active' => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/mangopay',
                        'mod_link'     => admin_url('admin.php?page=mvx-setting-admin'),
                    ],
                    [
                        'id'           => 'razorpay',
                        'name'         => __( 'Razorpay', 'multivendorx' ),
                        'description'  => __( 'For clients looking to pay multiple Indian vendors instantly', 'multivendorx' ),
                        'plan'         => 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MVX Razorpay Split Payment', 'multivendorx'),
                                'plugin_link'   => 'https://wordpress.org/plugins/mvx-razorpay-split-payment/',
                                'is_active' => is_plugin_active('wcmp-razorpay-split-payment/mvx-razorpay-checkout-gateway.php') ? true :false,
                            )
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/razorpay',
                        'mod_link'     => admin_url('admin.php?page=mvx-setting-admin'),
                    ]
                ]
            ],
            [
                'label' =>  __('Shipping', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'zone-shipping',
                        'name'         => __( 'Zone-Wise Shipping', 'multivendorx' ),
                        'description'  => __( 'Limit vendors to sell in selected zones', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/knowledgebase/',
                        'mod_link'     => admin_url('admin.php?page=wc-settings&tab=shipping'),
                        'parent_category' => __( 'Shipping.', 'multivendorx' ),
                    ],
                    [
                        'id'           => 'distance-shipping',
                        'name'         => __( 'Distance Shipping', 'multivendorx' ),
                        'description'  => __( 'Calculate Rates based on distance between the vendor store and drop location', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/distance-shipping',
                        'mod_link'     => admin_url('admin.php?page=wc-settings&tab=shipping&section=mvx_product_shipping_by_distance'),
                    ],
                    [
                        'id'           => 'country-shipping',
                        'name'         => __( 'Country-Wise Shipping', 'multivendorx' ),
                        'description'  => __( 'Let vendors choose and manage shipping, to countries of their choice', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/country-shipping',
                        'mod_link'     => admin_url('admin.php?page=wc-settings&tab=shipping&section=mvx_product_shipping_by_country'),
                    ],
                    [
                        'id'           => 'weight-shipping',
                        'name'         => __( 'Weight Wise Shipping (using Table Rate Shipping)', 'multivendorx' ),
                        'description'  => __( 'Vendors can create shipping rates based on price, weight and quantity', 'multivendorx' ),
                        'plan'         => 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Table Rate Shipping', 'multivendorx'),
                                'plugin_link'   => 'https://woocommerce.com/products/table-rate-shipping/',
                                'is_active' => is_plugin_active('woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php') ?true : false,
                            )
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/weight-shipping',
                        'mod_link'     => admin_url('admin.php?page=wc-settings&tab=shipping'),
                    ],
                    [
                        'id'           => 'per-product-shipping',
                        'name'         => __( 'Per Product Shipping', 'multivendorx' ),
                        'description'  => __( 'let vendors add shipping cost to specific products', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Per Product Shipping', 'multivendorx'),
                                'plugin_link'   => 'https://woocommerce.com/products/per-product-shipping/',
                                'is_active' => is_plugin_active('woocommerce-shipping-per-product/woocommerce-shipping-per-product.php') ?true : false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/per-product-shipping',
                        'mod_link'     => admin_url('admin.php?page=wc-settings&tab=shipping'),
                    ],
                ]
            ],
            [
                'label' =>  __('Order Managemnet', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'invoice',
                        'name'         => __( 'Invoice & Packing slip', 'multivendorx' ),
                        'description'  => __( 'Send invoice and packaging slips to vendor', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/invoice-packing-slip',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-vendor-invoice'),
                    ],
                    [
                        'id'           => 'marketplace-refund',
                        'name'         => __( 'Marketplace Refund', 'multivendorx' ),
                        'description'  => __( 'Enable customer refund requests & Let vendors manage customer refund ', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/marketplace-refund',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=refund-management'),
                    ],
                ]
            ],
            [
                'label' =>  __('Store Managemnet', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'store-location',
                        'name'         => __( 'Store Location', 'multivendorx' ),
                        'description'  => __( "If enabled customers can view a vendor's store location", 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/store-location',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=store'),
                    ],
                    [
                        'id'           => 'store-policy',
                        'name'         => __( 'Store Policy', 'multivendorx' ),
                        'description'  => __( 'Offers vendors the option to set individual store specific policies', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/store-policy',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=policy'),
                    ],
                    [
                        'id'           => 'follow-store',
                        'name'         => __( 'Follow Store', 'multivendorx' ),
                        'description'  => __( 'Permit customers to follow store, receive updates & lets vendors keep track of customers', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/follow-store',
                    ],
                    [
                        'id'           => 'store-review',
                        'name'         => __( 'Store Review', 'multivendorx' ),
                        'description'  => __( 'Allows customers to rate and review stores and their purchased products', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/store-review',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=review-management'),
                    ],
                    [
                        'id'           => 'business-hours',
                        'name'         => __( 'Business Hours', 'multivendorx' ),
                        'description'  => __( 'Gives vendors the option to set and manage business timings', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/business-hours/',
                    ],
                    [
                        'id'           => 'mvx-blocks',
                        'name'         => __( 'Gutenberg Blocks', 'multivendorx' ),
                        'description'  => __( 'Lets you add widgets using Gutenberg block editor. Use the block to register our widget area on any page or post using the Gutenberg editor.', 'multivendorx' ),
                        'plan'         => 'free',
                    ],
                    [
                        'id'           => 'advertisement',
                        'name'         => __( 'Advertise Product', 'multivendorx' ),
                        'description'  => __( 'Enable the option of paid advertisiment by letting vendors advertise their products on your website.', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/advertise-product/',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-advertising'),
                    ],
                ]
            ],
            [
                'label' =>  __('Store Component', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'vacation',
                        'name'         => __( 'Vacation', 'multivendorx' ),
                        'description'  => __( 'On vacation mode, vendor can allow / disable sale & notify customer accordingly', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/vacation',
                    ],
                    [
                        'id'           => 'staff-manager',
                        'name'         => __( 'Staff Manager', 'multivendorx' ),
                        'description'  => __( 'Lets vendors hire and manage staff to support store', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/staff-manager',
                    ],
                    [
                        'id'           => 'wholesale',
                        'name'         => __( 'Wholesale', 'multivendorx' ),
                        'description'  => __( 'Set wholesale price and quantity for customers ', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/wholesale',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-wholesale'),
                    ],
                    [
                        'id'           => 'live-chat',
                        'name'         => __( 'Live Chat', 'multivendorx' ),
                        'description'  => __( 'Allows real-time messaging between vendors and customers', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/live-chat',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-live-chat'),
                    ],
                    [
                        'id'           => 'store-support',
                        'name'         => __('Store Support', 'multivendorx'),
                        'description'  => __('Streamline order support with vendor-customer ticketing system.', 'multivendorx'),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/store-support/',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-store-support'),
                    ],
                ]
            ],
            [
                'label' =>  __('Analytics', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'store-analytics',
                        'name'         => __( 'Store Analytics', 'multivendorx' ),
                        'description'  => __( 'Gives vendors detailed store report & connect to google analytics', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/store-analytics',
                        'mod_link'     => admin_url('admin.php?page=mvx-setting-admin'),
                    ],
                    [
                        'id'           => 'store-seo',
                        'name'         => __( 'Store SEO  ', 'multivendorx' ),
                        'description'  => __( 'Lets vendors manage their store SEOs using Rank Math and Yoast SEO', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/store-seo',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-seo'),
                    ],
                ]
            ],
            [
                'label' =>  __('Marketplace Membership', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'marketplace-membership',
                        'name'         => __( 'Makertplace Membership', 'multivendorx' ),
                        'description'  => __( 'Lets Admin create marketplace memberships levels and manage vendor-wise individual capablity  ', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/marketplace-memberhsip',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=settings-vendor-membership'),
                    ],
                ]
            ],
            [
                'label' =>  __('Notifictaion', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'announcement',
                        'name'         => __( 'Announcement', 'multivendorx' ),
                        'description'  => __( 'Lets admin broadcast important news to sellers', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/announcement/',                        
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=work-board&name=announcement'),
                    ],
                    [
                        'id'           => 'report-abuse',
                        'name'         => __( 'Report Abuse', 'multivendorx' ),
                        'description'  => __( 'Lets customers report false products', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/report-abuse',                        
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=work-board&name=report-abuse'),
                    ],
                    [
                        'id'           => 'knowladgebase',
                        'name'         => __( 'Knowledgebase', 'multivendorx' ),
                        'description'  => __( 'Admin can share tutorials and othe vendor-specific information with vendors', 'multivendorx' ),
                        'plan'         => 'free',
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/knowladgebase',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=work-board&name=knowladgebase'),
                    ],
                ]
            ],
            [
                'label' =>  __('Third Party Compartibility', 'multivendorx'),
                'options'       =>  [
                    [
                        'id'           => 'elementor',
                        'name'         => __( 'Elementor', 'multivendorx' ),
                        'description'  => __( 'Create Sellers Pages using Elementors drag and drop feature ', 'multivendorx' ),
                        'plan'         => 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Elementor Website Builder', 'multivendorx'),
                                'plugin_link'   => 'https://wordpress.org/plugins/elementor/',
                                'is_active' => is_plugin_active('elementor/elementor.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('Elementor Pro', 'multivendorx'),
                                'plugin_link'   => 'https://elementor.com/pricing/',
                                'is_active' => is_plugin_active('elementor-pro/elementor-pro.php') ? true : false,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/mvx-elementor',
                        'parent_category' => __( 'Third Party Compartibility', 'multivendorx' ),
                    ],
                    [
                        'id'           => 'buddypress',
                        'name'         => __( 'Buddypress', 'multivendorx' ),
                        'description'  => __( 'Allows stores to have a social networking feature', 'multivendorx' ),
                        'plan'         => 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Buddypress', 'multivendorx'),
                                'plugin_link'   => 'https://wordpress.org/plugins/buddypress/',
                                'is_active' => is_plugin_active('buddypress/bp-loader.php') ? true : false,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/mvx-buddypress',
                        'mod_link'     => admin_url('admin.php?page=mvx#&submenu=settings&name=social'),
                    ],
                    [
                        'id'           => 'wpml',
                        'name'         => __( 'WPML', 'multivendorx' ),
                        'description'  => __( 'Gives vendors the option of selling their product in different languages', 'multivendorx' ),
                        'plan'         => 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('The WordPress Multilingual Plugin', 'multivendorx'),
                                'plugin_link'   => 'https://wpml.org/',
                                'is_active' => class_exists( 'SitePress' ) ? true : false,
                            ),
                            array(
                                'plugin_name'   => __('WooCommerce Multilingual – run WooCommerce with WPML', 'multivendorx'),
                                'plugin_link'   => 'https://wordpress.org/plugins/woocommerce-multilingual/',
                                'is_active'     => is_plugin_active('woocommerce-multilingual/wpml-woocommerce.php') ? true : false,
                            )
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/mvx-wpml',
                    ],
                    [
                        'id'           => 'advance-custom-field',
                        'name'         => __( 'Advance Custom field', 'multivendorx' ),
                        'description'  => __( 'Allows for an on demand product field in Add Product section', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Advanced custom fields', 'multivendorx'),
                                'plugin_link'   => 'https://wordpress.org/plugins/advanced-custom-fields/',
                                'is_active' => class_exists('ACF') ? true : false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/mvx-acf',
                        'category'  => 'store boosters',
                    ],
                    [
                        'id'           => 'geo-my-wp',
                        'name'         => __( 'GEOmyWP', 'multivendorx' ),
                        'description'  => __( 'Offer vendor the option to attach location info along with their products', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Geo My wp', 'multivendorx'),
                                'plugin_link'   => 'https://wordpress.org/plugins/geo-my-wp/',
                                'is_active' => is_plugin_active('geo-my-wp/geo-my-wp.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/geo-my-wp',
                    ],
                    [
                        'id'           => 'toolset-types',
                        'name'         => __( 'Toolset Types', 'multivendorx' ),
                        'description'  => __( "Allows admin to create custom fields, and taxonomy for vendor's product field", 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Toolset', 'multivendorx'),
                                'plugin_link'   => 'https://toolset.com/',
                                'is_active' => is_plugin_active('types/wpcf.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                    ],
                    [
                        'id'           => 'wp-affiliate',
                        'name'         => __( 'WP Affiliate', 'multivendorx' ),
                        'description'  => __( 'Launch affiliate programme into your marketplace', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('AffiliateWP', 'multivendorx'),
                                'plugin_link'   => 'https://affiliatewp.com/',
                                'is_active' => is_plugin_active('affiliate-wp/affiliate-wp.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowledgebase/affiliate-product/',
                    ],
                    [
                        'id'           => 'product-addon',
                        'name'         => __( 'Product Addon', 'multivendorx' ),
                        'description'  => __( 'Offer add-ons like gift wrapping, special messages etc along with primary products', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Product Add-Ons', 'multivendorx'),
                                'plugin_link'   => 'https://woocommerce.com/products/product-add-ons/',
                                'is_active' => is_plugin_active('woocommerce-product-addons/woocommerce-product-addons.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/mvx-product-addon',
                    ],
                    [
                        'id'           => 'product-addon',
                        'name'         => __( 'Product Addon', 'multivendorx' ),
                        'description'  => __( 'Offer add-ons like gift wrapping, special messages etc along with primary products', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('Product Add-Ons', 'multivendorx'),
                                'plugin_link'   => 'https://woocommerce.com/products/product-add-ons/',
                                'is_active' => is_plugin_active('woocommerce-product-addons/woocommerce-product-addons.php') ? true :false,
                            ),
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                        'doc_link'     => 'https://multivendorx.com/docs/knowladgebase/mvx-product-addon',
                    ],
                    [
                        'id'           => 'shipstation-module',
                        'name'         => __( 'Shipstation', 'multivendorx' ),
                        'description'  => __( 'Shipstation', 'multivendorx' ),
                        'plan'         => apply_filters('is_mvx_pro_plugin_inactive', true) ? 'pro' : 'free',
                        'required_plugin_list' => array(
                            array(
                                'plugin_name'   => __('MultivendorX Pro', 'multivendorx'),
                                'plugin_link'   => 'https://multivendorx.com/',
                                'is_active'     => $mvx_pro_is_active,
                            ),
                        ),
                    ],
                ]
            ],
        ];
        $is_required_plugin_active10 = [];
        if ($mvx_all_modules) {
            foreach ($mvx_all_modules as $parent_module_key => $parent_module_value) {
                if (isset($parent_module_value['options']) && !empty($parent_module_value['options'])) {
                    foreach ($parent_module_value['options'] as $module_key => $module_value) {
                        $mvx_all_modules[$parent_module_key]['options'][$module_key]['is_active'] = is_current_module_active($module_value['id']);
                        $mvx_all_modules[$parent_module_key]['options'][$module_key]['thumbnail_dir'] = 'module-' . $module_value['id'];

                        $mvx_all_modules[$parent_module_key]['options'][$module_key]['active_status'] = true;
                        if (isset($mvx_all_modules[$parent_module_key]['options'][$module_key]['required_plugin_list']) && !empty($mvx_all_modules[$parent_module_key]['options'][$module_key]['required_plugin_list'])) {
                            foreach ($mvx_all_modules[$parent_module_key]['options'][$module_key]['required_plugin_list'] as $req_key => $req_value) {
                                if (empty($req_value['is_active'])) {
                                    $mvx_all_modules[$parent_module_key]['options'][$module_key]['active_status'] = false;
                                }
                            }
                        }
                    }
                }
            }
        }
        return apply_filters('mvx_list_modules', $mvx_all_modules);
    }
}

if (!function_exists('is_current_module_active')) {
    function is_current_module_active($module_name) {
        $is_module_active = mvx_get_option('mvx_all_active_module_list', true);
        $is_active = $is_module_active && is_array($is_module_active) && in_array($module_name, $is_module_active) ? true : false;
        return $is_active;
    }
}

if (!function_exists('is_mvx_shipping_module_active')) {
    function is_mvx_shipping_module_active() {
        if (mvx_is_module_active('zone-shipping') || mvx_is_module_active('distance-shipping') || mvx_is_module_active('country-shipping')) {
            return true;
        }
        return false;
    }
}

if (!function_exists('mvx_convert_select_structure')) {
    function mvx_convert_select_structure($data_fileds = array(), $csv = false, $object = false) {
        $is_csv = $csv ? 'key' : 'value';
        $datafileds_initialize_array = [];
        if ($data_fileds) {
            foreach($data_fileds as $fileds_key => $fileds_value) {
                if ($object) {
                    $datafileds_initialize_array[] = array(
                        'value' => $fileds_value->ID,
                        'label' => $fileds_value->post_title
                    );
                } else {
                    $datafileds_initialize_array[] = array(
                        $is_csv => $csv ? $fileds_value : $fileds_key,
                        'label' => $fileds_value
                    );
                }
            }
        }
        return $datafileds_initialize_array;
    }
}

if (!function_exists('mvx_count_commission')) {
    function mvx_count_commission() {
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'dc_commission',
            'post_status' => array('private', 'publish')
        );
        $commission_id = wp_list_pluck(get_posts($args), 'ID');
        $commission_count = new stdClass();
        $commission_count->paid = $commission_count->unpaid = $commission_count->reverse = 0;
        foreach ($commission_id as $id) {
            $commission_status = get_post_meta($id, '_paid_status', true);
            if ($commission_status) {
                switch ($commission_status) {
                    case 'paid':
                        $commission_count->paid += 1;
                        break;
                    case 'unpaid':
                        $commission_count->unpaid += 1;
                        break;
                    case 'reverse':
                        $commission_count->reverse += 1;
                        break;
                }
            }
        }
        return $commission_count;
    }
}

if (!function_exists('mvx_count_wordboard_list')) {
    function mvx_count_wordboard_list() {
        global $MVX;        
        return (int) ((int)count($MVX->vendor_rest_api->mvx_list_of_pending_vendor_product()->data) + (int) count($MVX->vendor_rest_api->mvx_list_of_pending_vendor()->data) + (int)count($MVX->vendor_rest_api->mvx_list_of_pending_vendor_coupon()->data) + (int)count($MVX->vendor_rest_api->mvx_list_of_pending_transaction()->data) + (int)count($MVX->vendor_rest_api->mvx_list_of_pending_question('', '')->data)
        );
    }
}

if (!function_exists('mvxArrayToObject')) {
    /**
     * Convert php array to object
     * @param array $d
     * @return object
     */
    function mvxArrayToObject($d) {
        if (is_array($d)) {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return (object) array_map(__FUNCTION__, $d);
        } else {
            // Return object
            return $d;
        }
    }
}

if(!function_exists('insert_mvx_vendor_order_data')){
    function insert_mvx_vendor_order_data($parent_order, $vendor_id) {
        //createing sub order on wc_orders table
        $order = new WC_Order();
        $meta = [
            'cart_hash',
            'customer_id',
            'currency',
            'prices_include_tax',
            'customer_ip_address',
            'customer_user_agent',
            'customer_note',
            'payment_method',
            'payment_method_title',
            'status',
            'billing_country',
            'billing_first_name',
            'billing_last_name',
            'billing_company',
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_state',
            'billing_postcode',
            'billing_email',
            'billing_phone',
            'shipping_country',
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_city',
            'shipping_state',
            'shipping_postcode',
        ];
        // save other details
        $order->set_created_via( 'mvx_vendor_order' );
        $order->calculate_totals();
        $order->set_parent_id( $parent_order->get_id() );
        $order->update_meta_data( '_vendor_id', $vendor_id );
        $order->save();
        foreach ( $meta as $key ) {
            if ( is_callable( [ $order, "set_{$key}" ] ) ) {
                $order->{"set_{$key}"}( $parent_order->{"get_{$key}"}() );
            }
        }
        $order->save();

        wp_update_post(array(
            'ID' => $order->get_id(),
            'post_author' => $vendor_id,
        ));

        return $order->get_id();
    }
}
