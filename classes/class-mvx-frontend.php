<?php

/**
 * MVX Frontend Class
 *
 * @version		2.2.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class MVX_Frontend {
    public $custom_store_url = '';

    public function __construct() {

        $permalinks = mvx_get_option('dc_vendors_permalinks');
        $this->custom_store_url = empty($permalinks['vendor_shop_base']) ? _x('vendor', 'slug', 'multivendorx') : $permalinks['vendor_shop_base'];
        //enqueue scripts
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
        //enqueue styles
        add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'), 999);
        if ( apply_filters( 'mvx_load_default_vendor_store', false ) ) {
            add_action('woocommerce_archive_description', array(&$this, 'product_archive_vendor_info'), 10);
        } else {
            add_action('mvx_archive_description', array(&$this, 'product_archive_vendor_info'), 10);
        }
        add_filter('body_class', array(&$this, 'set_product_archive_class'));
        add_action('template_redirect', array(&$this, 'template_redirect'));

        add_filter('template_include', array(&$this, 'mvx_vendor_dashboard_template'), 99);
        //add_filter('page_template', array(&$this, 'mvx_vendor_dashboard_template'));

        add_action('woocommerce_order_details_after_order_table', array($this, 'display_vendor_msg_in_thank_you_page'), 100);
        add_action('mvx_vendor_register_form', array(&$this, 'mvx_vendor_register_form_callback'));
        add_action('woocommerce_register_post', array(&$this, 'mvx_validate_extra_register_fields'), 10, 3);
        add_action('woocommerce_created_customer', array(&$this, 'mvx_save_extra_register_fields'), 10, 3);
        // split woocommerce shipping packages
        add_filter('woocommerce_cart_shipping_packages', array(&$this, 'mvx_split_shipping_packages'), 0);
        // Rename woocommerce shipping packages
        add_filter('woocommerce_shipping_package_name', array(&$this, 'woocommerce_shipping_package_name'), 10, 3);
        if (is_mvx_version_less_3_4_0()) {
            // Add extra vendor_id to shipping packages
            add_action('woocommerce_checkout_create_order_shipping_item', array(&$this, 'add_meta_date_in_shipping_package'), 10, 4);
            // processed woocomerce checkout order data
            add_action('woocommerce_checkout_order_processed', array(&$this, 'mvx_checkout_order_processed'), 30, 3);
        }
        // store visitors stats
        if(!apply_filters('mvx_is_disable_store_visitors_stats', false))
            add_action('template_redirect', array(&$this, 'mvx_store_visitors_stats'), 99);

        add_filter('woocommerce_get_zone_criteria', array(&$this, 'mvx_shipping_zone_same_region_criteria'), 10, 3);
        // WPML work
        if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) && mvx_is_module_active('wpml') ) {
            add_filter( 'icl_ls_languages', array( &$this, 'mvx_store_page_wpml_language_switcher' ), 999 );
            add_action( 'mvx_product_manager_right_panel_after', array( &$this, 'wpml_mvx_product_manager_translations' ), 200 );
            add_action( 'mvx_dashboard_header_right_vendor_dropdown', array( &$this, 'wpml_language_switcher_option_on_dropdown' ) );
        }
        // MVX store page callback
        add_action( 'mvx_store_tab_widget_contents', array(&$this, 'mvx_store_tab_widget_contents' ));
        add_action( 'mvx_store_widget_contents', array(&$this, 'mvx_store_widget_contents' ));
        add_action( 'mvx_after_main_content', array(&$this, 'mvx_after_main_content' ));
        add_action( 'mvx_sidebar', array(&$this, 'mvx_sidebar') );

        add_action( 'init', array(&$this, 'mvx_sidebar_init'), 99 );
        add_filter( 'query_vars', array( $this, 'prefix_register_query_var' ), 1);
        add_action( 'pre_get_posts', [ $this, 'store_query_filter' ], 99 );

        // Review tab section
        add_action( 'mvx_vendor_shop_page_reviews_endpoint', array(&$this, 'mvx_vendor_shop_page_reviews_endpoint' ), 10, 2 );
        // Pllicies tab section
        if (mvx_is_module_active('store-policy')) {
            add_action( 'mvx_vendor_shop_page_policies_endpoint', array(&$this, 'mvx_vendor_shop_page_policies_endpoint' ), 10, 2 );
        }
        flush_rewrite_rules();

        // Customer follows vendor list on my account page
        if ( mvx_is_module_active('store-follow') ) {
            add_filter( 'woocommerce_account_menu_items',array($this, 'mvx_customer_followers_vendor'), 99 );
            add_action( 'woocommerce_account_followers_endpoint', array($this, 'mvx_customer_followers_vendor_callback' ));
        }
        //is checkout delivery location on
        if ( mvx_is_module_active('store-location') ) {
            add_filter( 'woocommerce_checkout_fields', array( &$this, 'mvx_checkout_user_location_fields' ), 50 );
            add_action( 'woocommerce_after_checkout_billing_form', array( &$this, 'mvx_checkout_user_location_map' ), 50 );
            add_action( 'woocommerce_checkout_update_order_review', array( &$this, 'mvx_checkout_user_location_session_set' ), 50 );
            add_action( 'woocommerce_checkout_update_order_meta', array( &$this, 'mvx_checkout_user_location_save' ), 50 );
        }

        if ( get_mvx_vendor_settings('display_product_seller', 'settings_general') && apply_filters('mvx_display_multiple_vendor_notice_at_cart_chekout_page', true) ){
            add_action( 'woocommerce_before_cart', array( &$this, 'message_multiple_vendors_cart' ), 10 );
            add_filter( 'woocommerce_locate_template', array( &$this, 'mvx_template_cart' ), 10, 1 );
            add_filter( 'wc_get_template', array( &$this, 'mvx_template_orderdetails' ), 10, 5 );
        }
        //display suborder in mail
        if ( get_mvx_vendor_settings('display_suborder_in_mail', 'order') ) {
            add_filter( 'wc_get_template', array( &$this, 'mvx_template_email_order_details' ), 10, 5 );
        }
        add_action('mvx_init', array(&$this, 'update_data_record'));
    }

    /**
     * Save the extra register fields.
     *
     * @param  int  $customer_id Current customer ID.
     *
     * @return void
     */
    function mvx_save_extra_register_fields($customer_id) {
        global $MVX;
        if (isset($_POST['mvx_vendor_fields']) && isset($_POST['pending_vendor'])) {

            if (isset($_FILES['mvx_vendor_fields'])) {
                $attacment_files = array_filter($_FILES['mvx_vendor_fields']);
                $files = array();
                $count = 0;
                if (!empty($attacment_files) && is_array($attacment_files)) {
                    foreach ($attacment_files['name'] as $key => $attacment) {
                        foreach ($attacment as $key_attacment => $value_attacment) {
                            $files[$count]['name'] = $value_attacment;
                            $files[$count]['type'] = $attacment_files['type'][$key][$key_attacment];
                            $files[$count]['tmp_name'] = $attacment_files['tmp_name'][$key][$key_attacment];
                            $files[$count]['error'] = $attacment_files['error'][$key][$key_attacment];
                            $files[$count]['size'] = $attacment_files['size'][$key][$key_attacment];
                            $files[$count]['field_key'] = $key;
                            $count++;
                        }
                    }
                }
                $upload_dir = wp_upload_dir();
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                if (!function_exists('wp_handle_upload')) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }
                foreach ($files as $file) {
                    $uploadedfile = $file;
                    $upload_overrides = array('test_form' => false);
                    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
                    if ($movefile && !isset($movefile['error'])) {
                        $filename = $movefile['file'];
                        $filetype = wp_check_filetype($filename, null);
                        $attachment = array(
                            'post_mime_type' => $filetype['type'],
                            'post_title' => $file['name'],
                            'post_content' => '',
                            'post_status' => 'inherit',
                            'guid' => $movefile['url']
                        );
                        $attach_id = wp_insert_attachment($attachment, $movefile['file']);
                        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                        wp_update_attachment_metadata($attach_id, $attach_data);
                        $_POST['mvx_vendor_fields'][$file['field_key']]['value'][] = $attach_id;
                    }
                }
            }
            $mvx_vendor_fields = isset( $_POST['mvx_vendor_fields'] ) ? array_filter( array_map( 'wc_clean', (array) $_POST['mvx_vendor_fields'] ) ) : '';

            $mvx_vendor_fields = apply_filters('mvx_save_registration_fields', $mvx_vendor_fields, $customer_id);
            update_user_meta($customer_id, 'mvx_vendor_fields', $mvx_vendor_fields);

            // MVX Vendor Registration form data mapping
            $data_type_map = apply_filters('mvx_vendor_formdata_mapped_types', array('vendor_address_1', 'vendor_address_2', 'vendor_phone', 'vendor_country', 'vendor_state', 'vendor_city', 'vendor_postcode', 'vendor_paypal_email', 'vendor_description'));
            if ($mvx_vendor_fields && is_array($mvx_vendor_fields)) {
                $data = array();
                foreach ($mvx_vendor_fields as $key => $value) {
                    if (in_array($value['type'], $data_type_map)) {
                        $has_value = get_user_meta($customer_id, '_' . $value['type'], true);
                        if (!$has_value && wc_clean($value['value'])) {
                            $data[$value['type']] = wc_clean($value['value']);
                        }
                    }
                }
                $MVX->vendor_dashboard->save_store_settings($customer_id, $data);
            }
            $MVX->user->mvx_woocommerce_created_customer_notification();
        }
    }

    /**
     * Validate the extra register fields.
     *
     * @param  string $username          Current username.
     * @param  string $email             Current email.
     * @param  object $validation_errors WP_Error object.
     *
     * @return void
     */
    function mvx_validate_extra_register_fields($username, $email, $validation_errors) {
        $mvx_vendor_registration_form_data = mvx_get_option('mvx_new_vendor_registration_form_data');
        if(isset($_POST['g-recaptchatype']) && $_POST['g-recaptchatype'] == 'v2'){
            if (isset($_POST['g-recaptcha-response']) && empty($_POST['g-recaptcha-response'])) {
                $validation_errors->add('recaptcha is not validate', __('Please Verify  Recaptcha', 'multivendorx'));
            }
        }elseif(isset($_POST['g-recaptchatype']) && $_POST['g-recaptchatype'] == 'v3') {
            $recaptcha_secret = isset($_POST['recaptchav3_secretkey']) ? wc_clean( $_POST['recaptchav3_secretkey'] ) : '';
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_response = isset($_POST['recaptchav3Response']) ? wc_clean( $_POST['recaptchav3Response'] ) : '';

            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);

            if ( !$recaptcha->success || $recaptcha->score < 0.5 ) {
                $validation_errors->add('recaptcha is not validate', __('Please Verify  Recaptcha', 'multivendorx'));
            }
        }
        
        if (isset($_FILES['mvx_vendor_fields'])) {
            $attacment_files = array_filter($_FILES['mvx_vendor_fields']);
            if (!empty($attacment_files) && is_array($attacment_files)) {
                foreach ($attacment_files['name'] as $key => $value) {
                    $file_type = array();
                    foreach ($mvx_vendor_registration_form_data[$key]['fileType'] as $key1 => $value1) {
                        if ($value1['selected']) {
                            array_push($file_type, $value1['value']);
                        }
                    }
                    foreach ($attacment_files['type'][$key] as $file_key => $file_value) {
                        if (!empty($attacment_files['name'][$key][$file_key])) {
                            if ($mvx_vendor_registration_form_data[$key]['required'] && !in_array($file_value, $file_type)) {
                                $validation_errors->add('file type error', __('Please Upload valid file', 'multivendorx'));
                            }
                        }
                    }
                    foreach ($attacment_files['size'][$key] as $file_size_key => $file_size_value) {
                        if (!empty($mvx_vendor_registration_form_data[$key]['fileSize'])) {
                            if ($mvx_vendor_registration_form_data[$key]['required'] && $file_size_value > $mvx_vendor_registration_form_data[$key]['fileSize']) {
                                $validation_errors->add('file size error', __('File upload limit exceeded', 'multivendorx'));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Populate vendor registration form
     * @global object $MVX
     */
    function mvx_vendor_register_form_callback() {
        global $MVX;
        $mvx_vendor_registration_form_data = mvx_get_option('mvx_new_vendor_registration_form_data');
        $MVX->template->get_template('vendor-registration-form.php', array('mvx_vendor_registration_form_data' => $mvx_vendor_registration_form_data));
    }

    /**
     * Display custom message in woocommerce thank you page
     * @global object $wpdb
     * @global object $MVX
     * @param int $order_id
     */
    public function display_vendor_msg_in_thank_you_page($order_id) {
        global $MVX;
        $order = wc_get_order($order_id);
        $items = $order->get_items('line_item');
        $vendor_array = array();
        $author_id = '';
        $customer_support_details_settings = mvx_get_option('mvx_general_customer_support_details_settings_name');
        $is_csd_by_admin = '';
        foreach ($items as $item_id => $item) {
            $product_id = wc_get_order_item_meta($item_id, '_product_id', true);
            if ($product_id) {
                $author_id = wc_get_order_item_meta($item_id, '_vendor_id', true);
                if (empty($author_id)) {
                    $product_vendors = get_mvx_product_vendors($product_id);
                    if (isset($product_vendors) && (!empty($product_vendors))) {
                        $author_id = $product_vendors->id;
                    } else {
                        $author_id = get_post_field('post_author', $product_id);
                    }
                }
                if (isset($vendor_array[$author_id])) {
                    $vendor_array[$author_id] = $vendor_array[$author_id] . ',' . $item['name'];
                } else {
                    $vendor_array[$author_id] = $item['name'];
                }
            }
        }
        if (!empty($vendor_array)) {
            echo '<div style="clear:both">';
            if (apply_filters('can_vendor_add_message_on_email_and_thankyou_page', true)) {
                $MVX->template->get_template('vendor-message-to-buyer.php', array('vendor_array' => $vendor_array, 'capability_settings' => $customer_support_details_settings, 'customer_support_details_settings' => $customer_support_details_settings));
            } elseif (get_mvx_vendor_settings('is_customer_support_details', 'settings_general')) {
                $MVX->template->get_template('customer-support-details-to-buyer.php', array('vendor_array' => $vendor_array, 'capability_settings' => $customer_support_details_settings, 'customer_support_details_settings' => $customer_support_details_settings));
            }
            echo "</div>";
        }
    }

    /**
     * split woocommerce shipping packages 
     * @since 2.6.6
     * @param array $packages
     * @return array
     */
    public function mvx_split_shipping_packages($packages) {
        // Reset all packages
        $packages = array();
        $split_packages = array();
        foreach (WC()->cart->get_cart() as $item) {
            if ($item['data']->needs_shipping()) {
                $product_id = $item['product_id'];
                $vendor = get_mvx_product_vendors($product_id);
                if ($vendor && $vendor->is_shipping_enable()) {
                    $split_packages[$vendor->id][] = $item;
                } else {
                    $split_packages[0][] = $item;
                }
            }
        }

        foreach ($split_packages as $vendor_id => $split_package) {
            $packages[$vendor_id] = array(
                'contents' => $split_package,
                'contents_cost' => array_sum(wp_list_pluck($split_package, 'line_total')),
                'applied_coupons' => WC()->cart->get_applied_coupons(),
                'user' => array(
                    'ID' => $vendor_id,
                ),
                'vendor_id' => $vendor_id,
                'destination' => array(
                    'country' => WC()->customer->get_shipping_country(),
                    'state' => WC()->customer->get_shipping_state(),
                    'postcode' => WC()->customer->get_shipping_postcode(),
                    'city' => WC()->customer->get_shipping_city(),
                    'address' => WC()->customer->get_shipping_address(),
                    'address_2' => WC()->customer->get_shipping_address_2()
                )
            );

            if( apply_filters( 'mvx_is_allow_checkout_user_location', true ) && mvx_is_module_active('distance-shipping') ) {
                $mvx_user_location     = WC()->session->get( '_mvx_user_location' );
                $mvx_user_location_lat = WC()->session->get( '_mvx_user_location_lat' );
                $mvx_user_location_lng = WC()->session->get( '_mvx_user_location_lng' );
                if( $mvx_user_location ) {
                    $packages[$vendor_id]['mvx_user_location']     = $mvx_user_location;
                    $packages[$vendor_id]['mvx_user_location_lat'] = $mvx_user_location_lat;
                    $packages[$vendor_id]['mvx_user_location_lng'] = $mvx_user_location_lng;
                }
            }
        }
        return apply_filters('mvx_split_shipping_packages', $packages);
    }

    /**
     * 
     * @param object $item
     * @param sting $package_key as $vendor_id
     */
    public function add_meta_date_in_shipping_package($item, $package_key, $package, $order) {
        $vendor_id = ( isset( $package['vendor_id'] ) && $package['vendor_id'] ) ? $package['vendor_id'] : $package_key;
        $item->add_meta_data('vendor_id', $vendor_id, true);
        $package_qty = array_sum(wp_list_pluck($package['contents'], 'quantity'));
        $item->add_meta_data('package_qty', $package_qty, true);
        do_action('mvx_add_shipping_package_meta');
    }

    /**
     * Rename shipping packages 
     * @since 2.6.6
     * @param string $package_name
     * @param string $vendor_id
     * @param array $package
     * @return string
     */
    public function woocommerce_shipping_package_name($package_name, $vendor_id, $package) {
        if ($vendor_id && $vendor_id != 0) {
            $vendor = get_mvx_vendor($vendor_id);
            
            if ($vendor) {
                return $vendor->page_title . __(' Shipping', 'multivendorx');
            }
            return $package_name;
        }
        return $package_name;
    }

    /**
     * Process order after checkout for shipping, Tax calculation.
     *
     * @param int $order_id
     * @param array $order_posted
     * @param WC_Order $order WooCommerce order object
     * @return void
     */
    public function mvx_checkout_order_processed($order_id, $order_posted, $order) {
        if (!get_post_meta($order_id, '_mvx_order_processed', true)) {
            mvx_process_order($order_id, $order);
        }
    }

    /**
     * Add frontend scripts
     * @return void
     */
    public function frontend_scripts() {
        global $MVX, $wp_scripts;
        $frontend_script_path = $MVX->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $suffix = defined('MVX_SCRIPT_DEBUG') && MVX_SCRIPT_DEBUG ? '' : '.min';

        wp_register_script('mvx_frontend_vdashboard_js', $frontend_script_path . 'mvx_vendor_dashboard' . $suffix . '.js', array('jquery'), $MVX->version, true);
        wp_register_script('frontend_js', $frontend_script_path . 'frontend' . $suffix . '.js', array('jquery'), $MVX->version, true);
        wp_register_script('vendor_order_by_product_js', $frontend_script_path . 'vendor_order_by_product' . $suffix . '.js', array('jquery'), $MVX->version, true);
        //wp_register_script('simplepopup_js', $frontend_script_path . 'simplepopup' . $suffix . '.js', array('jquery'), $MVX->version, true);
        wp_register_script('mvx_new_vandor_dashboard_js', $frontend_script_path . 'vendor_dashboard' . $suffix . '.js', array('jquery'), $MVX->version, true);
        wp_register_script('mvx_seller_review_rating_js', $frontend_script_path . 'vendor_review_rating' . $suffix . '.js', array('jquery'), $MVX->version, true);
        wp_register_script('mvx_single_product_multiple_vendors', $frontend_script_path . 'single-product-multiple-vendors' . $suffix . '.js', array('jquery'), $MVX->version, true);
        wp_register_script('mvx_customer_qna_js', $frontend_script_path . 'mvx-customer-qna' . $suffix . '.js', array('jquery'), $MVX->version, true);
        wp_register_script('mvx_custom_scroller_js', $frontend_script_path . 'jquery.mCustomScrollbar.concat.min.js', array('jquery'), $MVX->version, true);
        wp_register_script('mvx_country_state_js', $frontend_script_path . 'mvx-country-state.js', array('jquery'), $MVX->version, true);
        wp_register_script('mvx-vendor-shipping', $frontend_script_path . 'vendor-shipping.js', array( 'jquery' ), $MVX->version, true );
        wp_register_script('mvx-meta-boxes', $frontend_script_path . 'meta-boxes.js', array( 'jquery' ), $MVX->version, true );
     
        /** localize script data * */
        $MVX->localize_script('frontend_js');
        $MVX->localize_script('mvx_frontend_vdashboard_js');
        $MVX->localize_script('mvx_single_product_multiple_vendors');
        $MVX->localize_script('mvx_seller_review_rating_js');
        $MVX->localize_script('mvx_customer_qna_js');
        $MVX->localize_script('mvx-vendor-shipping');
        
        if (is_vendor_dashboard()) {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-blockui');
            wp_enqueue_script( 'wc-country-select' );
            wp_enqueue_script('jquery-ui-sortable');

            $MVX->library->load_bootstrap_script_lib();
            $MVX->library->load_qtip_lib();
            wp_enqueue_script('mvx_frontend_vdashboard_js');
            wp_enqueue_script('mvx_new_vandor_dashboard_js');
            wp_enqueue_script('vendor_order_by_product_js');
            wp_enqueue_script('mvx_seller_review_rating_js');
            wp_enqueue_script('mvx_customer_qna_js');
            wp_enqueue_script('mvx_custom_scroller_js');
            wp_enqueue_script('mvx_country_state_js');
        }
        if (is_woocommerce()) {
            wp_enqueue_script('frontend_js');
            //wp_enqueue_script('simplepopup_js');
            wp_enqueue_script('mvx_single_product_multiple_vendors');
            wp_enqueue_script('mvx_customer_qna_js');
        }
        if (mvx_is_store_page()) {
            $MVX->library->load_bootstrap_style_lib();
            $MVX->library->load_bootstrap_script_lib();
            $store_id = mvx_find_shop_page_vendor();
            $vendor = get_mvx_vendor($store_id);
            if ( $vendor && $vendor->banner_type == 'video' ) {
                $MVX->library->load_vlite_lib();
                wp_enqueue_script( 'mvx_vlite_js', $frontend_script_path . 'vlite_script' . $suffix . '.js', array('jquery' ), $MVX->version, true );
            }
            wp_enqueue_script('mvx_seller_review_rating_js');
            wp_enqueue_script('frontend_js');
        }
        if( is_checkout() && apply_filters( 'mvx_is_allow_checkout_user_location', true ) && mvx_is_module_active('distance-shipping') ) {
            if (mvx_mapbox_api_enabled()) {
                $MVX->library->load_mapbox_api();
            } else {
                $MVX->library->load_gmap_api();                
            }
            wp_enqueue_script( 'mvx_checkout_location_js', $frontend_script_path . 'checkout/mvx-script-checkout-location' . $suffix . '.js', array('jquery' ), $MVX->version, true );
            wp_localize_script( 'mvx_checkout_location_js', 'mvx_checkout_map_options', array( 'search_location' => __( 'Insert your address ..', 'multivendorx' ), 'mapbox_emable' => mvx_mapbox_api_enabled(), 'default_lat' => -79.4512, 'default_lng' => 43.6568, 'default_zoom' => 2, 'store_icon' => $MVX->plugin_url . 'assets/images/store-marker.png', 'icon_width' => apply_filters( 'mvx_map_icon_width', 40 ), 'icon_height' => apply_filters( 'mvx_map_icon_height', 57 ) ) );
        }
    }

    /**
     * Add frontend styles
     * @return void
     */
    public function frontend_styles() {
        global $MVX;
        $frontend_style_path = $MVX->plugin_url . 'assets/frontend/css/';
        $frontend_style_path = str_replace(array('http:', 'https:'), '', $frontend_style_path);
        $suffix = defined('MVX_SCRIPT_DEBUG') && MVX_SCRIPT_DEBUG ? '' : '.min';
        $is_vendor_dashboard = is_vendor_dashboard() && is_user_logged_in() && (is_user_mvx_vendor(get_current_user_id()) || is_user_mvx_pending_vendor(get_current_user_id()) || is_user_mvx_rejected_vendor(get_current_user_id())) && apply_filters('mvx_vendor_dashboard_dequeue_global_styles', true);
        if ($is_vendor_dashboard) {
            $this->mvx_dequeue_global_style();
        }

        wp_register_style('frontend_css', $frontend_style_path . 'frontend' . $suffix . '.css', array(), $MVX->version);
        wp_register_style('product_css', $frontend_style_path . 'product' . $suffix . '.css', array(), $MVX->version);
        wp_register_style('vandor-dashboard-style', $frontend_style_path . 'vendor_dashboard' . $suffix . '.css', array(), $MVX->version);
        wp_register_style('multiple_vendor', $frontend_style_path . 'multiple-vendor' . $suffix . '.css', array(), $MVX->version);
        wp_register_style('mvx_custom_scroller', $frontend_style_path . 'lib/jquery.mCustomScrollbar.css', array(), $MVX->version);
        wp_register_style( 'advance-product-manager', $frontend_style_path . 'advance-product-manager.css', array(), $MVX->version );
        // register vendor shoppage css
        wp_register_style( 'mvx_seller_shop_page_css', $frontend_style_path . 'mvx-shop-page' . $suffix . '.css', array(), $MVX->version );
        // Add RTL support
        wp_style_add_data('vandor-dashboard-style', 'rtl', 'replace');
        wp_style_add_data('frontend_css', 'rtl', 'replace');
        wp_style_add_data('product_css', 'rtl', 'replace');
        wp_style_add_data('advance-product-manager', 'rtl', 'replace');

        if (is_vendor_dashboard() && is_user_logged_in() && (is_user_mvx_vendor(get_current_user_id()) || is_user_mvx_pending_vendor(get_current_user_id()) || is_user_mvx_rejected_vendor(get_current_user_id()))) {
            wp_enqueue_style('dashicons');
            wp_enqueue_style('jquery-ui-style');
            $MVX->library->load_bootstrap_style_lib();
            wp_enqueue_style('vandor-dashboard-style');
            wp_add_inline_style('vandor-dashboard-style', get_mvx_vendor_settings('mvx_vendor_dashboard_custom_css', 'seller_dashbaord'));
            wp_enqueue_style('mvx_custom_scroller');
        }
        if (is_woocommerce()) {
            wp_enqueue_style('product_css');
            wp_enqueue_style('multiple_vendor');
            // add styly to product page
            $pstyle = '.mvx-product-policies .description { margin: 0 0 1.41575em;}';
            wp_add_inline_style('woocommerce-inline', $pstyle);
        }
        if (mvx_is_store_page()) {
            wp_enqueue_style('frontend_css');
            // Enqueue shop page css
            wp_enqueue_style('mvx_seller_shop_page_css');
            wp_enqueue_style('dashicons');
        }
        do_action('mvx_frontend_enqueue_scripts', $is_vendor_dashboard);
    }

    public function mvx_dequeue_global_style() {
        global $wp_styles;
        $styles_to_keep = apply_filters('mvx_styles_to_keep', array('admin-bar', 'select2', 'dashicons', 'qtip_css'));
        // loop over all of the registered scripts
        foreach ($wp_styles->registered as $handle => $data) {
            // remove all style
            if (!in_array($handle, $styles_to_keep)) {
                wp_dequeue_style($handle);
            }
        }
    }

    /**
     * Add html for vendor taxnomy page
     * @return void
     */
    public function product_archive_vendor_info() {
        global $MVX;
        if (mvx_is_store_page()) {
            $store_id = mvx_find_shop_page_vendor();
            $vendor = get_mvx_vendor($store_id);
            if( $vendor ){
                $image = $vendor->get_image() ? $vendor->get_image() : $MVX->plugin_url . 'assets/images/WP-stdavatar.png';
                $description = $vendor->description;

                $address = $vendor->get_formatted_address();

                $MVX->template->get_template('archive-vendor-info.php', array('vendor_id' => $vendor->id, 'banner' => $vendor->get_image('banner'), 'profile' => $image, 'description' => apply_filters('the_content', $description), 'mobile' => $vendor->phone, 'location' => $address, 'email' => (isset($vendor->user_data->_vendor_display_email) && !empty($vendor->user_data->_vendor_display_email)) ? $vendor->user_data->_vendor_display_email : $vendor->user_data->user_email));
            }
        }
    }

    /**
     * Add 'woocommerce' class to body tag for vendor pages
     *
     * @param  arr $classes Existing classes
     * @return arr          Modified classes
     */
    public function set_product_archive_class($classes) {
        global $MVX;
        if (mvx_is_store_page()) {

            // Add generic classes
            $classes[] = 'woocommerce';
            $classes[] = 'product-vendor';

            // Get vendor ID
            $vendor_id = mvx_find_shop_page_vendor();

            // Get vendor info
            $vendor = get_mvx_vendor($vendor_id);

            // Add vendor slug as class
            if ('' != $vendor->slug) {
                $classes[] = $vendor->slug;
            }
        }
        return $classes;
    }

    /**
     * template redirect function
     * @return void
     */
    public function template_redirect() {
        //redirect to my account or vendor dashbord page if user loggedin
        if (is_user_logged_in() && is_page_vendor_registration()) {
            if (current_user_can('administrator') || current_user_can('shop_manager')) {
                wp_safe_redirect(get_permalink(wc_get_page_id('myaccount')));
            } else {
                wp_safe_redirect(get_permalink(mvx_vendor_dashboard_page_id()));
            }
            exit();
        }elseif(is_product()){
            // restrict block/suspended vendor product view
            global $post;
            if($post){
                $vendor = get_mvx_product_vendors($post->ID);
                if($vendor){
                    $is_block = get_user_meta($vendor->id, '_vendor_turn_off', true);
                    if($is_block) wp_safe_redirect(apply_filters('mvx_suspended_vendor_product_view_return_url', wc_get_page_permalink( 'shop' )));
                }
            }
        }
    }

    /**
     * Return vendor dashboard template
     * @global object $MVX
     * @param string $page_template
     * @return string
     * @since 3.0.0
     */
    public function mvx_vendor_dashboard_template($page_template) {
        global $MVX;
        if (is_vendor_dashboard() && is_user_logged_in() && (is_user_mvx_vendor(get_current_user_id()) || is_user_mvx_pending_vendor(get_current_user_id()) || is_user_mvx_rejected_vendor(get_current_user_id())) && apply_filters('mvx_vendor_dashboard_exclude_header_footer', true)) {
            return $MVX->template->locate_template('template-vendor-dashboard.php');
        }
        return $page_template;
    }

    /**
     * Store visitors Stats
     * @global object $MVX
     * @return void
     * @since 3.0.0
     */
    public function mvx_store_visitors_stats() {
        global $MVX;
        $product_vendor = false;
        if (is_product()) {
            global $post;
            $product_vendor = get_mvx_product_vendors($post->ID);
        } elseif (mvx_is_store_page()) {
            $vendor_id = mvx_find_shop_page_vendor();
            $product_vendor = get_mvx_vendor($vendor_id);;
        }
        if ($product_vendor && isset($_COOKIE["_mvx_user_cookie_" . get_current_user_id()])) {
            $ip_data = get_visitor_ip_data();
            if( !empty($ip_data) && $ip_data->status == 'success' ) {
                $ip_data->user_id = get_current_user_id();
                $ip_data->user_cookie = $_COOKIE["_mvx_user_cookie_" . get_current_user_id()];
                $ip_data->session_id = session_id();
                mvx_save_visitor_stats($product_vendor->id, $ip_data);
            }
        }
    }

    public function mvx_shipping_zone_same_region_criteria( $criteria, $package, $postcode_locations ) {
        global $wpdb;
        $postcode  = wc_normalize_postcode( wc_clean( $package['destination']['postcode'] ) );
        if( !$postcode ) return $criteria;
        $search_results = $wpdb->get_results($wpdb->prepare(
            "SELECT vendor_id,zone_id
            FROM {$wpdb->prefix}mvx_shipping_zone_locations where location_code = %s", $postcode
            ));
        $match_rates = array();
        if ( !empty( $search_results ) ) {
            foreach ($search_results as $key => $value) {
                if( $value->vendor_id == $package['vendor_id'] ) {
                    $match_rates[] = $value->zone_id;
                }
            }
            if( !empty( $match_rates ) ) {
                $criteria[] = 'AND zones.zone_id IN (' . implode( ',', $match_rates ) . ')';
            }
        }
        return $criteria;
    }

    function wpml_mvx_product_manager_translations($product_id) {
        global $sitepress;
        if( !$product_id ) return;
        $active_languages = $this->get_filtered_active_lanugages();
        if ( count( $active_languages ) <= 1 ) {
            return;
        }
        $current_language = $sitepress->get_current_language();
        unset( $active_languages[ $current_language ] );

        if ( count( $active_languages ) > 0 ) {
            $translation_html = '';
            ?>
            <div id="product_images_container" class="custom-panel">
                <div style="max-width: 214px; margin: 0 auto;">
                    <h3><p class="product_translations"><strong><?php esc_html_e( 'Translations', 'multivendorx' ); ?></strong></p></h3>
                    <label class="screen-reader-text" for="product_translations"><?php esc_html_e( 'Translations', 'multivendorx' ); ?></label>
                    
                    <table style="margin-top:0px;">
                        <tbody id="mvx_product_translations" data-product_id="<?php echo esc_attr($product_id); ?>">
                            <?php echo $translation_html; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Get list of active languages.
     *
     * @return array
     */
    public function get_filtered_active_lanugages() {
        global $sitepress; 
        $active_languages = $sitepress->get_active_languages();
        return apply_filters( 'wpml_active_languages_access', $active_languages, array( 'action' => 'edit' ) );
    }

    /**
     * Store Page WPML Language Switcher Compatibility
     */
    function mvx_store_page_wpml_language_switcher( $languages ) {
        global $MVX, $sitepress;
        $default_lang = $sitepress->get_default_language();
        if ( mvx_is_store_page() ) {
            if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE && class_exists( 'SitePress' ) ) {
                $vendor_id = mvx_find_shop_page_vendor();
                $vendor = get_mvx_vendor($vendor_id);
                $formated_languages = array();
                $default_lang = $sitepress->get_default_language();
                $permalinks = mvx_get_option('dc_vendors_permalinks');
                $vendor_permalink = is_array($permalinks) && isset($permalinks['vendor_shop_base']) && !empty($permalinks['vendor_shop_base']) ? $permalinks['vendor_shop_base'] : 'vendor';
                if( !empty( $languages ) ) {
                    $is_wpml_configured = apply_filters( 'wpml_setting', 0, 'language_negotiation_type' );
                    foreach( $languages as $lang => $language ) {
                        if( $default_lang  && ( $default_lang  == $language['language_code'] ) ) {
                            $language['url'] = site_url() .'/'. $vendor_permalink .'/'. $vendor->page_title;
                        } else {
                            if ($is_wpml_configured == 3) {
                                $language['url'] = site_url() .'/'. $vendor_permalink .'/'. $vendor->page_title . '/' . '?lang='. $language['language_code'];
                            } else {
                                $language['url'] = site_url() .'/'. $language['language_code'] .'/'. $vendor_permalink .'/'. $vendor->page_title;
                            }
                        }
                        $formated_languages[$lang] = $language;
                    }
                    $languages = $formated_languages;
                }
            }
        }
        return $languages;
    }

    public function wpml_language_switcher_option_on_dropdown() {
        global $MVX;
        if ( $MVX->endpoints->get_current_endpoint() != 'edit-product' ) {
            ?><div style="height: 100px; overflow: scroll;">
                <?php do_action( 'wpml_footer_language_selector'); ?>
            </div><?php 
        }
    }

    public function mvx_vendor_shop_page_reviews_endpoint( $store_id, $query_vars_name ) {
        global $MVX;
        $MVX->review_rating->mvx_seller_review_rating_form();
    }

    public function mvx_vendor_shop_page_policies_endpoint( $store_id, $query_vars_name ) {
        $_vendor_shipping_policy = get_user_meta( $store_id, '_vendor_shipping_policy', true ) ? get_user_meta( $store_id, '_vendor_shipping_policy', true ) : get_mvx_global_settings('shipping_policy');
        $_vendor_refund_policy = get_user_meta( $store_id, '_vendor_refund_policy', true ) ? get_user_meta( $store_id, '_vendor_refund_policy', true ) : get_mvx_global_settings('refund_policy');
        $_vendor_cancellation_policy = get_user_meta( $store_id, '_vendor_cancellation_policy', true ) ? get_user_meta( $store_id, '_vendor_cancellation_policy', true ) : get_mvx_global_settings('cancellation_policy');
        ?>
        <div class="mvx-policie-sec">
            <div class="mvx-policies-header mvx-tabcontent-header">
                <div class='mvx-heading'><?php esc_html_e( 'Shop policies', 'multivendorx' ); ?></div>
            </div>
            <!-- Shipping policy -->
            <div>
                <div class="mvx-sub-heading">
                    <span class="dashicons dashicons-cart"></span>
                    <p><?php esc_html_e( 'Shipping Policy', 'multivendorx' ); ?></p>
                </div>
                <div class='mvx-policie-sub-area'>
                    <p><?php echo !empty($_vendor_shipping_policy) ? wp_kses_post( $_vendor_shipping_policy ) : __( 'No policy found', 'multivendorx' ); ?></p>
                </div>
            </div>
            <!-- Refund policy -->
            <div>
                <div class="mvx-sub-heading">
                    <span class="dashicons dashicons-cart"></span>
                    <p><?php esc_html_e( 'Refund Policy', 'multivendorx' ); ?></p>
                </div>
                <div class='mvx-policie-sub-area'>
                    <p><?php echo !empty($_vendor_refund_policy) ? wp_kses_post( $_vendor_refund_policy ) : __( 'No policy found', 'multivendorx' ); ?></p>
                </div>
            </div>
            <!-- Cancellation policy -->
            <div>
                <div class="mvx-sub-heading">
                    <span class="dashicons dashicons-cart"></span>
                    <p><?php esc_html_e( 'Cancellation/Return/Exchange Policy', 'multivendorx' ); ?></p>
                </div>
                <div class='mvx-policie-sub-area'>
                    <p><?php echo !empty($_vendor_cancellation_policy) ? wp_kses_post( $_vendor_cancellation_policy ) : __( 'No policy found', 'multivendorx' ); ?></p>
                </div>
            </div>
            <?php do_action( 'mvx_vendor_shop_page_policies', $store_id, $query_vars_name  ); ?>
        </div>
        <?php
    }

    public function store_query_filter( $query ) {
        global $wp_query, $MVX;
        $author = get_query_var( $this->custom_store_url );
        if ( ! is_admin() && $query->is_main_query() && ! empty( $author ) ) {
            $seller_info = '';
            $term_details = get_term_by('slug', $author, $MVX->taxonomy->taxonomy_name);
            $term_id = $term_details && $term_details->term_id ? $term_details->term_id : 0;
            if ($term_id) {
               $seller_info = get_mvx_vendor_by_term($term_id);
            }
            if ( ! $seller_info ) {
                return get_404_template();
            }

            $query->set( 'post_type', 'product' );
            $query->set( 'author_name', $seller_info->user_data->data->user_nicename );
        }
    }

    function prefix_register_query_var( $vars ) {
        $vars[] = $this->custom_store_url;
        return $vars;
    }

    function mvx_vendor_page_query_vars() {
        $query_vars = apply_filters(
            'mvx_query_var_filter', [
                'reviews',
                'policies'
            ]
        );
        return $query_vars;
    }

    function mvx_sidebar_init() {
        $query_vars = $this->mvx_vendor_page_query_vars();
        foreach ( $query_vars as $var ) {
            add_rewrite_endpoint( $var, EP_PAGES );
        }
        add_rewrite_rule( $this->custom_store_url.'/([^/]+)/reviews?$', 'index.php?'.$this->custom_store_url.'=$matches[1]&reviews=true', 'top' );
        add_rewrite_rule( $this->custom_store_url.'/([^/]+)/policies?$', 'index.php?'.$this->custom_store_url.'=$matches[1]&policies=true', 'top' );
    }

    public function mvx_sidebar() {
        wc_get_template( 'global/sidebar.php' );
    }

    public function mvx_after_main_content() {
        wc_get_template( 'global/wrapper-start.php' );
    }

    public function mvx_store_tab_widget_contents() {
        global $MVX;
        $store_id = mvx_find_shop_page_vendor();
        $shop_query_exist = false;
        $query_vars_name = 'products';
        $mvx_shop_page_query_vars = $this->mvx_vendor_page_query_vars();
        foreach ($mvx_shop_page_query_vars as $key_query) {
            if (get_query_var($key_query)) {
                $shop_query_exist = true;
                $query_vars_name = $key_query;
            }
        }
        $store_tabs = $this->mvx_get_store_tabs( $store_id );
        echo '<main id="main" class="site-main">';
        if ( ! empty( $store_tabs ) ) : ?>

        <div class='mvx-main-section'>
            <?php if (get_mvx_vendor_settings('mvx_store_sidebar_position', 'store') == 'At Left') do_action( 'mvx_store_widget_contents' ); ?>
            <div class="column-class mvx-middle-sec ">
                <div class="mvx-tab-header">
                    <?php foreach( $store_tabs as $key => $tab ) { 
                        ?>
                        <?php if ( $tab['url'] ): ?>
                            <a href="<?php echo esc_url( $tab['url'] ); ?>">
                                <div class="mvx-tablink <?php if( $tab['id'] == $query_vars_name ) echo 'active'; ?>">
                                    <?php echo esc_html( $tab['title'] ); ?>
                                </div>
                            </a>                         
                        <?php endif; ?>
                    <?php } ?>
                </div>
                <div>
                <?php

                if ($shop_query_exist) {
                    do_action( 'mvx_vendor_shop_page_'. $query_vars_name .'_endpoint', $store_id, $query_vars_name );
                } else {
                    $this->mvx_shop_product_callback(); 
                }

                ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (get_mvx_vendor_settings('mvx_store_sidebar_position', 'store') == 'At Right') do_action( 'mvx_store_widget_contents' ); ?>
        </div>
        <?php
    }

    public function mvx_get_store_tabs( $store_id ) {
        $store_id = mvx_find_shop_page_vendor();
        $vendor = get_mvx_vendor($store_id);
        $userstore = $vendor->permalink;
        $tabs = array(
            'products' => array(
                'id' => 'products',
                'title' => __( 'Products', 'multivendorx' ),
                'url'   => $userstore,
                'priority' => 1
            )
        );
        if (mvx_is_module_active('store-policy')) {
            $tabs['policies'] = array(
                'id' => 'policies',
                'title' => __( 'Policies', 'multivendorx' ),
                'url'   => $this->mvx_get_policies_url( $store_id ),
                'priority' => 3
            );
        }

        if (mvx_is_module_active('store-review') && get_mvx_vendor_settings('is_sellerreview', 'review_management')) {
            $tabs['reviews'] = array(
                'id' => 'reviews',
                'title' => __( 'Reviews', 'multivendorx' ),
                'url'   => $this->mvx_get_review_url( $store_id ),
                'priority' => 2
            );
        }
        // reorder as per pririty
        usort($tabs, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
        return apply_filters( 'mvx_store_tabs', $tabs, $store_id );
    }

    function mvx_get_policies_url( $user_id ) {
        if ( !$user_id ) {
            return '';
        }
        $vendor = get_mvx_vendor($user_id);
        $userstore = $vendor->permalink;
        return apply_filters( 'mvx_get_seller_policies_url', $userstore . 'policies' );
    }

    function mvx_get_review_url( $user_id ) {
        if ( !$user_id ) {
            return '';
        }
        $vendor = get_mvx_vendor($user_id);
        $userstore = $vendor->permalink;
        return apply_filters( 'mvx_get_seller_review_url', $userstore . 'reviews' );
    }

    // Product loop callback
    public function mvx_shop_product_callback() {

        if ( woocommerce_product_loop() ) {

            /**
            * Hook: woocommerce_before_shop_loop.
            *
            * @hooked woocommerce_output_all_notices - 10
            * @hooked woocommerce_result_count - 20
            * @hooked woocommerce_catalog_ordering - 30
            */
            do_action( 'woocommerce_before_shop_loop' );

            woocommerce_product_loop_start();

            if ( wc_get_loop_prop( 'total' ) ) {
                while ( have_posts() ) {
                    the_post();

                    /**
                    * Hook: woocommerce_shop_loop.
                    */
                    do_action( 'woocommerce_shop_loop' );

                    wc_get_template_part( 'content', 'product' );
                }
            }   

            woocommerce_product_loop_end();

            /**
            * Hook: woocommerce_after_shop_loop.
            *
            * @hooked woocommerce_pagination - 10
            */
            do_action( 'woocommerce_after_shop_loop' );
            } else {

            /**
            * Hook: woocommerce_no_products_found.
            *
            * @hooked wc_no_products_found - 10
            */
            do_action( 'woocommerce_no_products_found' );
        }
    }

    // Sideber as per admin choice
    public function mvx_store_widget_contents() {
        if (get_mvx_vendor_settings('mvx_store_sidebar_position', 'store') == 'At Left') { 
            $widget_class = 'mvx-leftwidget-sec';
        } elseif (get_mvx_vendor_settings('mvx_store_sidebar_position', 'store') == 'At Right') {
            $widget_class = 'mvx-rightwidget-sec';
        } else {
            $widget_class = '';
        }
        if ($widget_class != '' && is_active_sidebar('sidebar-mvx-store') && get_mvx_vendor_settings('is_enable_store_sidebar_position', 'store')) {
            ?>
            <div class="column-class" >
                <?php dynamic_sidebar( 'sidebar-mvx-store' ); ?>
            </div> 
            <?php
        }
    }

    public function mvx_customer_followers_vendor($items) {
        if (is_user_mvx_vendor(get_current_user_id())) {
            return $items;
        }
        unset( $items['customer-logout'] );
        $items[ 'followers' ] = __( 'Following', 'multivendorx' );
        $items[ 'customer-logout' ] = __( 'Log out', 'multivendorx' );
        return $items;
    }
    public function mvx_customer_followers_vendor_callback() {
        $mvx_customer_follow_vendor = get_user_meta( get_current_user_id(), 'mvx_customer_follow_vendor', true ) ? get_user_meta( get_current_user_id(), 'mvx_customer_follow_vendor', true ) : array();
        ?>
        <table>
            <tbody>
                <?php 
                if ($mvx_customer_follow_vendor) {
                    foreach ($mvx_customer_follow_vendor as $key_follow_vendor => $value_follow_vendor) {
                        $vendor = get_mvx_vendor($value_follow_vendor['user_id']);
                        if (!$vendor) continue;
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url($vendor->permalink); ?>"> <?php echo esc_html($vendor->page_title); ?> </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    esc_html_e('You do not follow any customer till now', 'multivendorx');
                }
                ?>
            </tbody>
        </table>
        <?php
    }

        /**
     * Checkout User Location Field Save
     */
    public function mvx_checkout_user_location_save( $order_id ) {
        if( apply_filters( 'mvx_is_allow_checkout_user_location', true ) && mvx_is_module_active('distance-shipping') ) {
            if ( ! empty( $_POST['mvx_user_location'] ) ) {
                update_post_meta( $order_id, '_mvx_user_location', sanitize_text_field( $_POST['mvx_user_location'] ) );
            }
            if ( ! empty( $_POST['mvx_user_location_lat'] ) ) {
                update_post_meta( $order_id, '_mvx_user_location_lat', sanitize_text_field( $_POST['mvx_user_location_lat'] ) );
            }
            if ( ! empty( $_POST['mvx_user_location_lng'] ) ) {
                update_post_meta( $order_id, '_mvx_user_location_lng', sanitize_text_field( $_POST['mvx_user_location_lng'] ) );
            }
        }
    }

    /**
     * Checkout User Location Field Save in Session
     */
    public function mvx_checkout_user_location_session_set( $post_data_raw ) {
        if( apply_filters( 'mvx_is_allow_checkout_user_location', true ) && mvx_is_module_active('distance-shipping') ) {
            parse_str( $post_data_raw, $post_data );
            if ( ! empty( $post_data['mvx_user_location'] ) ) {
                WC()->customer->set_props( array( 'mvx_user_location' => sanitize_text_field( $post_data['mvx_user_location'] ) ) );
                WC()->session->set( '_mvx_user_location', sanitize_text_field( $post_data['mvx_user_location'] ) );
            }
            if ( ! empty( $post_data['mvx_user_location_lat'] ) ) {
                WC()->session->set( '_mvx_user_location_lat', sanitize_text_field( $post_data['mvx_user_location_lat'] ) );
            }
            if ( ! empty( $post_data['mvx_user_location_lng'] ) ) {
                WC()->session->set( '_mvx_user_location_lng', sanitize_text_field( $post_data['mvx_user_location_lng'] ) );
            }
        }
    }

    /**
     * Checkout User Location Field
     */
    public function mvx_checkout_user_location_fields( $fields ) {
        if( ! WC()->is_rest_api_request() ) {
            if( ( true === WC()->cart->needs_shipping() ) && apply_filters( 'mvx_is_allow_checkout_user_location', true ) && mvx_is_module_active('distance-shipping') ) {
                $user_location_filed = mvx_mapbox_api_enabled() ? array('input-hidden') : array('form-row-wide');
                $fields['billing']['mvx_user_location'] = array(
                        'label'     => __( 'Delivery Location', 'multivendorx' ),
                        'placeholder'   => _x( 'Insert your address ..', 'placeholder', 'multivendorx' ),
                        'required'  => true,
                        'class'     => $user_location_filed,
                        'clear'     => true,
                        'priority'  => 999,
                        'value'     => WC()->session->get( '_mvx_user_location' )
                 );
                $fields['billing']['mvx_user_location_lat'] = array(
                        'required'  => false,
                        'class'     => array('input-hidden'),
                        'value'     => WC()->session->get( '_mvx_user_location_lat' )
                 );
                $fields['billing']['mvx_user_location_lng'] = array(
                        'required'  => false,
                        'class'     => array('input-hidden'),
                        'value'     => WC()->session->get( '_mvx_user_location_lng' )
                 );
            }
        }

     return $fields;
    }

    /**
     * Checkout User Location Map
     */
    public function mvx_checkout_user_location_map( $checkout ) {
        if( ( true === WC()->cart->needs_shipping() ) && apply_filters( 'mvx_is_allow_checkout_user_location', true ) && mvx_is_module_active('distance-shipping') ) {
            ?>
            <div class="woocommerce-billing-fields__field-wrapper">
                <div class="mvx-user-locaton-map" id="mvx-user-locaton-map" style="width: 100%; height: 300px;"></div>
            </div>
            <?php
        }
    }

    function message_multiple_vendors_cart() {
        $vendorsincart = $this->get_vendors_in_cart();
        if ( count($vendorsincart) > 1 ) {
            wc_print_notice(esc_html__('The products in your cart are sold by multiple different vendor partners. The order will be placed simultaneously with all vendors and you will receive a package from each of them.', 'multivendorx'), 'notice' );
        }else if ( count($vendorsincart) === 1 ) {
            $vendorid = reset($vendorsincart);
            echo '<input type="hidden" value="' . esc_attr($vendorid) . '">';
        }
        echo '<input type="hidden" value="' . esc_attr(count($vendorsincart)) . '">';
    }

    function get_vendors_in_cart() {
        $cart = WC()->cart;
        $vendors = array();
        if ( is_object($cart) ) {
            foreach ( $cart->get_cart() as $cart_item ) {
                $vendor = get_mvx_product_vendors( $cart_item['product_id'] );
                if ( $vendor ) {
                    $vendor_id = $vendor->id;
                    if ( !empty($vendor_id) ) {
                        array_push($vendors, $vendor_id);
                    }
                }
            }
        }
        return array_unique(array_filter($vendors));
    }

    function get_vendors_of_order( $order_id ) {
        if ( is_object($order_id) ) {
            $order = $order_id;
        } else {
            $order = wc_get_order($order_id);
        }
        $vendors = array();
        $items = $order->get_items();
        foreach ( $items as $product ) {
            $vendor_id = get_post_field( 'post_author', $product->get_product_id() );
            array_push($vendors, $vendor_id);
        }
        return array_unique(array_filter($vendors));
    }

    function mvx_template_cart( $template ) {
        global $MVX;
        $vendorsincart = $this->get_vendors_in_cart();
        if ( count($vendorsincart) > 1 ) {
            if ( 'cart.php' === basename( $template ) ) {
                $template = $MVX->plugin_path . 'templates/woocommerce/cart/cart.php';
            }
        }
        return $template;
    }

    function mvx_template_orderdetails( $template, $template_name, $args, $template_path, $default_path ) {
        global $MVX;
        if ( 'order-details.php' === basename( $template ) ) {
            if ( isset($args['order_id']) ) {
                $order_id = $args['order_id'];
                $vendors = $this->get_vendors_of_order($order_id);
                if ( count($vendors) > 1 ) {
                    $template = $MVX->plugin_path . 'templates/woocommerce/order/order-details.php';
                }
            }	
        }   
        return $template;
    }

    function mvx_template_email_order_details( $template, $template_name, $args, $template_path, $default_path ) {
        global $MVX;
        if ( 'email-order-details.php' === basename( $template ) ) {
            if ( $args['plain_text'] === false && $args['sent_to_admin'] === false ) {
                $order = $args['order'];
                $order_id = $order->get_id();
                $vendors = $this->get_vendors_of_order($order_id);
                if ( count($vendors) > 1 ) {
                    $template = $MVX->plugin_path . 'templates/woocommerce/emails/email-order-details.php';
                }  
            }	
        }
        return $template;
    }

    function update_data_record() {
        $users = get_users(array( 'role__in' => array('administrator', 'dc_vendor')));
        if (!empty($users)) {
            foreach ($users as $user) {
                $vendor_shipping = get_user_meta( $user->ID, '_vendor_shipping_policy', true );
                if (empty($vendor_shipping)) {
                    $prev_entry_shipping = get_user_meta( $user->ID, 'vendor_shipping_policy', true );
                    if ($prev_entry_shipping) {
                        update_user_meta($user->ID, '_vendor_shipping_policy', $prev_entry_shipping);
                    }
                    if (metadata_exists('user', $user->ID, 'vendor_shipping_policy')) {
                        delete_user_meta($user->ID, 'vendor_shipping_policy');
                    }
                }
                $vendor_refund = get_user_meta( $user->ID, '_vendor_refund_policy', true );
                if (empty($vendor_refund)) {
                    $prev_entry_refund = get_user_meta( $user->ID, 'vendor_refund_policy', true );
                    if ($prev_entry_refund) {
                        update_user_meta($user->ID, '_vendor_refund_policy', $prev_entry_refund);
                    }
                    if (metadata_exists('user', $user->ID, 'vendor_refund_policy')) {
                        delete_user_meta($user->ID, 'vendor_refund_policy');
                    }
                }
                $vendor_cancel = get_user_meta( $user->ID, '_vendor_cancellation_policy', true );
                if (empty($vendor_cancel)) {
                    $prev_entry_cancel = get_user_meta( $user->ID, 'vendor_cancellation_policy', true );
                    if ($prev_entry_cancel) {
                        update_user_meta($user->ID, '_vendor_cancellation_policy', $prev_entry_cancel);
                    }
                    if (metadata_exists('user', $user->ID, 'vendor_cancellation_policy')) {
                        delete_user_meta($user->ID, 'vendor_cancellation_policy');
                    }
                }
            }   
        }
    }
}
