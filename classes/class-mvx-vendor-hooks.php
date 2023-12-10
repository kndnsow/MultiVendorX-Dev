<?php

/**
 * Description of MVX_Vendor_Hooks
 *
 * @author 		MultiVendorX
 */
class MVX_Vendor_Hooks {

    function __construct() {
        add_action( 'mvx_vendor_dashboard_navigation', array( &$this, 'mvx_create_vendor_dashboard_navigation' ) );
        add_action( 'mvx_vendor_dashboard_content', array( &$this, 'mvx_create_vendor_dashboard_content' ) );
        add_action( 'mvx_before_vendor_dashboard', array( &$this, 'save_vendor_dashboard_data' ) );

        add_action( 'mvx_vendor_dashboard_vendor-announcements_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_announcements_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_vendor-orders_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_orders_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_storefront_endpoint', array( &$this, 'mvx_vendor_dashboard_storefront_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_profile_endpoint', array( &$this, 'mvx_vendor_dashboard_profile_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_vendor-policies_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_policies_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_vendor-billing_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_billing_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_vendor-shipping_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_shipping_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_vendor-report_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_report_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_banking-overview_endpoint', array( &$this, 'mvx_vendor_dashboard_banking_overview_endpoint' ) );

        add_action( 'mvx_vendor_dashboard_add-product_endpoint', array( &$this, 'mvx_vendor_dashboard_add_product_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_edit-product_endpoint', array( &$this, 'mvx_vendor_dashboard_edit_product_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_products_endpoint', array( &$this, 'mvx_vendor_dashboard_products_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_add-coupon_endpoint', array( &$this, 'mvx_vendor_dashboard_add_coupon_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_coupons_endpoint', array( &$this, 'mvx_vendor_dashboard_coupons_endpoint' ) );

        add_action( 'mvx_vendor_dashboard_vendor-withdrawal_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_withdrawal_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_transaction-details_endpoint', array( &$this, 'mvx_vendor_dashboard_transaction_details_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_vendor-followers_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_followers_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_vendor-knowledgebase_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_knowledgebase_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_vendor-tools_endpoint', array( &$this, 'mvx_vendor_dashboard_vendor_tools_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_products-qna_endpoint', array( &$this, 'mvx_vendor_dashboard_products_qna_endpoint' ) );
        add_action( 'mvx_vendor_dashboard_refund-request_endpoint', array( &$this, 'mvx_vendor_dashboard_refund_request_endpoint' ) );

        add_filter( 'the_title', array( &$this, 'mvx_vendor_dashboard_endpoint_title' ) );
        add_filter( 'mvx_vendor_dashboard_menu_vendor_policies_capability', array( &$this, 'mvx_vendor_dashboard_menu_vendor_policies_capability' ) );
        add_filter( 'mvx_vendor_dashboard_menu_vendor_withdrawal_capability', array( &$this, 'mvx_vendor_dashboard_menu_vendor_withdrawal_capability' ) );
        add_filter( 'mvx_vendor_dashboard_menu_vendor_shipping_capability', array( &$this, 'mvx_vendor_dashboard_menu_vendor_shipping_capability' ) );
        add_filter('mvx_vendor_dashboard_menu_vendor_knowledgebase_capability', array( &$this, 'mvx_vendor_dashboard_menu_vendor_knowledgebase_capability' ) );
        add_filter('mvx_vendor_dashboard_menu_refund_request_capability', array( &$this, 'mvx_vendor_dashboard_menu_refund_request_capability' ) );
        add_action( 'mvx_before_vendor_dashboard_content', array( &$this, 'mvx_before_vendor_dashboard_content' ) );
        add_action( 'wp', array( &$this, 'mvx_add_theme_support' ), 15 );
        
        // Rejected vendor dashboard content
        add_action( 'mvx_rejected_vendor_dashboard_content', array( &$this, 'rejected_vendor_dashboard_content' ) );
        add_action( 'mvx_before_rejected_vendor_dashboard', array( &$this, 'save_rejected_vendor_reapply_data' ) );
    }

    /**
     * Create vendor dashboard menu
     * array $args
     */
    public function mvx_create_vendor_dashboard_navigation( $args = array() ) {
        global $MVX;
        $MVX->template->get_template( 'vendor-dashboard/navigation.php', array( 'nav_items' => $this->mvx_get_vendor_dashboard_navigation(), 'args' => $args ) );
    }

    public function mvx_get_vendor_dashboard_navigation() {
        $vendor_nav = array(
            'dashboard'            => array(
                'label'       => __( 'Dashboard', 'multivendorx' )
                , 'url'         => mvx_get_vendor_dashboard_endpoint_url( 'dashboard' )
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_dashboard_capability', true )
                , 'position'    => 0
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-dashboard-icon'
            ),
            'store-settings'       => array(
                'label'       => __( 'Store Settings', 'multivendorx' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_store_settings_capability', true )
                , 'position'    => 10
                , 'submenu'     => array(
                    'storefront'      => array(
                        'label'       => __( 'Storefront', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_store_settings_endpoint', 'seller_dashbaord', 'storefront' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_shop_front_capability', true )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-storefront-icon'
                    ),
                    'vendor-policies' => array(
                        'label'       => __( 'Policies', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_vendor_policies_endpoint', 'seller_dashbaord', 'vendor-policies' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_policies_capability', false )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-policies-icon'
                    ),
                    'vendor-billing'  => array(
                        'label'       => __( 'Billing', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_vendor_billing_endpoint', 'seller_dashbaord', 'vendor-billing' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_billing_capability', true )
                        , 'position'    => 30
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-billing-icon'
                    ),
                    'vendor-shipping' => array(
                        'label'       => __( 'Shipping', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_vendor_shipping_endpoint', 'seller_dashbaord', 'vendor-shipping' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_shipping_capability', wc_shipping_enabled() )
                        , 'position'    => 40
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-shippingnew-icon'
                    ),
                    'products-qna' => array(
                        'label'       => __( 'Customer Questions', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_products_qna_endpoint', 'seller_dashbaord', 'products-qna' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_customer_questions_capability', true )
                        , 'position'    => 40
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-shippingnew-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-store-settings-icon'
            ),
            'vendor-products'      => array(
                'label'       => __( 'Product Manager', 'multivendorx' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_products_capability', 'edit_products' )
                , 'position'    => 20
                , 'submenu'     => array(
                    'products'    => array(
                        'label'       => __( 'All Products', 'multivendorx' )
                        , 'url'         => apply_filters( 'mvx_vendor_products', mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_products_endpoint', 'seller_dashbaord', 'products' ) ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_products_capability', 'edit_products' )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-product-manager-icon'
                    ),
                    'add-product' => array(
                        'label'       => __( 'Add Product', 'multivendorx' )
                        , 'url'         => apply_filters( 'mvx_vendor_dashboard_add_product_url', mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_add_product_endpoint', 'seller_dashbaord', 'add-product' ) ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_add_product_capability', 'edit_products' )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-add-product-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-product-manager-icon'
            ),
            'vendor-promte'        => array(
                'label'       => __( 'Coupons', 'multivendorx' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_promte_capability', 'edit_shop_coupons' )
                , 'position'    => 30
                , 'submenu'     => array(
                    'coupons'    => array(
                        'label'       => __( 'All Coupons', 'multivendorx' )
                        , 'url'         => apply_filters( 'mvx_vendor_coupons', mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_coupons_endpoint', 'seller_dashbaord', 'coupons' ) ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_coupons_capability', 'edit_shop_coupons' )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-coupons-icon'
                    ),
                    'add-coupon' => array(
                        'label'       => __( 'Add Coupon', 'multivendorx' )
                        , 'url'         => apply_filters( 'mvx_vendor_submit_coupon', mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_add_coupon_endpoint', 'seller_dashbaord', 'add-coupon' ) ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_add_coupon_capability', 'edit_shop_coupons' )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-addcoupon-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-coupons-icon'
            ),
            'vendor-report'        => array(
                'label'       => __( 'Stats / Reports', 'multivendorx' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_report_capability', true )
                , 'position'    => 40
                , 'submenu'     => array(
                    'vendor-report' => array(
                        'label'       => __( 'Overview', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_vendor_report_endpoint', 'seller_dashbaord', 'vendor-report' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_report_capability', true )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-reports-icon'
                    ),
                    'banking-overview' => array(
                        'label'       => __( 'Banking Overview', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_banking_overview_endpoint', 'seller_dashbaord', 'banking-overview' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_banking_report_capability', true )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-reports-icon'
                    )
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-reports-icon'
            ),
            'vendor-orders'        => array(
                'label'       => __( 'Orders', 'multivendorx' )
                , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders' ) )
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_orders_capability', true )
                , 'position'    => 50
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-orders-icon'
            ),
            'vendor-payments'      => array(
                'label'       => __( 'Payments', 'multivendorx' )
                , 'url'         => '#'
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_payments_capability', true )
                , 'position'    => 60
                , 'submenu'     => array(
                    'vendor-withdrawal'   => array(
                        'label'       => __( 'Withdrawal', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_vendor_withdrawal_endpoint', 'seller_dashbaord', 'vendor-withdrawal' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_withdrawal_capability', false )
                        , 'position'    => 10
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-revenue-icon'
                    ),
                    'transaction-details' => array(
                        'label'       => __( 'History', 'multivendorx' )
                        , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_transaction_details_endpoint', 'seller_dashbaord', 'transaction-details' ) )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_transaction_details_capability', true )
                        , 'position'    => 20
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-history-icon'
                    ),
                    'refund-request' => array(
                        'label'       => __( 'Refund', 'multivendorx' )
                        , 'url'         =>  mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings('mvx_refund_req_endpoint', 'seller_dashbaord', 'refund-request') )
                        , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_refund_request_capability', true )
                        , 'position'    => 30
                        , 'link_target' => '_self'
                        , 'nav_icon'    => 'mvx-font ico-reply-icon'
                    ),
                )
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-payments-icon'
            ),
            'vendor-followers' => array(
                'label'       => __('Followers', 'multivendorx')
                , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings('mvx_vendor_followers_endpoint', 'seller_dashbaord', 'vendor-followers') )
                , 'capability'  => mvx_is_module_active('follow-store') ? true : false
                , 'position'    => 70
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-followers-icon', 
            ),
            'vendor-knowledgebase' => array(
                'label'       => __( 'Knowledgebase', 'multivendorx' )
                , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_vendor_knowledgebase_endpoint', 'seller_dashbaord', 'vendor-knowledgebase' ) )
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_knowledgebase_capability', true )
                , 'position'    => 80
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-knowledgebase-icon'
            ),
            'vendor-tools'         => array(
                'label'       => __( 'Tools', 'multivendorx' )
                , 'url'         => mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_vendor_tools_endpoint', 'seller_dashbaord', 'vendor-tools' ) )
                , 'capability'  => apply_filters( 'mvx_vendor_dashboard_menu_vendor_tools_capability', true )
                , 'position'    => 90
                , 'submenu'     => array()
                , 'link_target' => '_self'
                , 'nav_icon'    => 'mvx-font ico-tools-icon'
            )
        );
        return apply_filters( 'mvx_vendor_dashboard_nav', $vendor_nav );
    }

    /**
     * Display Vendor dashboard Content
     * @global object $wp
     * @global object $MVX
     * @return null
     */
    public function mvx_create_vendor_dashboard_content() {
        global $wp, $MVX;
        foreach ( $wp->query_vars as $key => $value ) {
            // Ignore pagename and page param.
            if ( in_array( $key, array( 'page', 'pagename' ) ) ) {
                continue;
            }
            do_action( 'mvx_before_vendor_dashboard_content', $key );
            if ( has_action( 'mvx_vendor_dashboard_' . $key . '_endpoint' ) ) {
                if ( $this->current_vendor_can_view( $MVX->endpoints->get_current_endpoint() ) ) {
                    do_action( 'mvx_vendor_dashboard_' . $key . '_endpoint', $value );
                }
                return;
            }
            do_action( 'mvx_after_vendor_dashboard_content' );
        }
        $MVX->library->load_dataTable_lib();
        $MVX->template->get_template( 'vendor-dashboard/dashboard.php' );
    }

    public function mvx_create_vendor_dashboard_breadcrumbs( $current_endpoint, $nav = array(), $firstLevel = true ) {
        global $MVX;
        $nav = ! empty( $nav ) ? $nav : $this->mvx_get_vendor_dashboard_navigation();
        $resultArray = array();
        $current_endpoint = $current_endpoint ? $current_endpoint : 'dashboard';
        $breadcrumb = false;
        $curent_menu = array();
        if ( array_key_exists( $current_endpoint, $nav ) ) {
            $menu = $nav[$current_endpoint];
            $icon = isset($menu['nav_icon']) ? '<i class="' . $menu['nav_icon'] . '"></i>' : '';
            $breadcrumb = $icon . '<span> ' . $menu['label'] . '</span>';
            $curent_menu = $menu;
        } else {
            $submenus = wp_list_pluck( $nav, 'submenu' );
            foreach ( $submenus as $key => $submenu ) {
                if ( $submenu && array_key_exists( $current_endpoint, $submenu ) ) {
                    if ( ! $firstLevel ) {
                        $menu = $nav[$key];
                        $icon = isset($menu['nav_icon']) ? '<i class="' . $menu['nav_icon'] . '"></i>' : '';
                        $breadcrumb = $icon . '<span> ' . $menu['label'] . '</span>';
                        $subm = $submenu[$current_endpoint];
                        $subicon = isset($subm['nav_icon']) ? '<i class="' . $subm['nav_icon'] . '"></i>' : '';
                        $breadcrumb .= '&nbsp;<span class="bread-sepa"> ' . apply_filters( 'mvx_vendor_dashboard_breadcrumbs_separator', '>' ) . ' </span>&nbsp;';
                        $breadcrumb .= $subicon . '<span> ' . $subm['label'] . '</span>';
                        $curent_menu = $subm;
                    } else {
                        $menu = $submenu[$current_endpoint];
                        $icon = isset($menu['nav_icon']) ? '<i class="' . $menu['nav_icon'] . '"></i>' : '';
                        $breadcrumb = $icon . '<span> ' . $menu['label'] . '</span>';
                        $curent_menu = $menu;
                    }
                    break;
                } else {
                    $current_endpoint_arr = isset($MVX->endpoints->mvx_query_vars[$current_endpoint]) ? $MVX->endpoints->mvx_query_vars[$current_endpoint] : array();
                    $icon = isset($current_endpoint_arr['icon']) ? '<i class="' . $current_endpoint_arr['icon'] . '"></i>' : '';
                    $breadcrumb = $icon . '<span> ' . $current_endpoint_arr['label'] . '</span>';
                    $curent_menu = $current_endpoint_arr;
                }
            }
        }
        return apply_filters( 'mvx_create_vendor_dashboard_breadcrumbs', $breadcrumb, $curent_menu );
    }

    public function current_vendor_can_view( $current_endpoint = 'dashboard' ) {
        $nav = $this->mvx_get_vendor_dashboard_navigation();
        foreach ( $nav as $endpoint => $menu ) {
            if ( $endpoint == $current_endpoint ) {
                return current_user_can( $menu['capability'] ) || true === $menu['capability'];
            } else if ( ! empty( $menu['submenu'] ) && array_key_exists( $current_endpoint, $menu['submenu'] ) && isset( $menu['submenu'][$current_endpoint]['capability'] ) ) {
                return current_user_can( $menu['submenu'][$current_endpoint]['capability'] ) || true === $menu['submenu'][$current_endpoint]['capability'];
            }
        }
        return true;
    }

    /**
     * Display Vendor Announcements content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_announcements_endpoint() {
        global $MVX;
        if (mvx_is_module_active('announcement')) {
            $frontend_style_path = $MVX->plugin_url . 'assets/frontend/css/';
            $frontend_style_path = str_replace( array( 'http:', 'https:' ), '', $frontend_style_path );
            $frontend_script_path = $MVX->plugin_url . 'assets/frontend/js/';
            $frontend_script_path = str_replace( array( 'http:', 'https:' ), '', $frontend_script_path );
            $suffix = defined( 'MVX_SCRIPT_DEBUG' ) && MVX_SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script( 'jquery-ui-accordion' );
            wp_enqueue_script( 'mvx_new_vandor_announcements_js', $frontend_script_path . 'mvx_vendor_announcements' . $suffix . '.js', array( 'jquery' ), $MVX->version, true );
            $MVX->localize_script( 'mvx_new_vandor_announcements_js', array('grant_access_nonce' => wp_create_nonce( 'grant-access' ) ) );
            $vendor = get_mvx_vendor( get_current_vendor_id() );
            $MVX->template->get_template( 'vendor-dashboard/vendor-announcements.php', array( 'vendor_announcements' => $vendor->get_announcements() ) );
        }
    }

    /**
     * Display vendor dashboard shop front content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_storefront_endpoint() {
        global $MVX;
        $suffix = defined('MVX_SCRIPT_DEBUG') && MVX_SCRIPT_DEBUG ? '' : '.min';
        $vendor = get_mvx_vendor( get_current_vendor_id() );
        $user_array = $MVX->user->get_vendor_fields( $vendor->id );
        $MVX->library->load_dashboard_upload_lib();
        $MVX->library->load_gmap_api();
        wp_register_script( 'mvx-frontend-media', $MVX->plugin_url . 'assets/frontend/js/storefront-media' . $suffix . '.js', array( 'jquery'), $MVX->version );
        wp_enqueue_script( 'mvx-frontend-media' );
        $MVX->template->get_template( 'vendor-dashboard/shop-front.php', $user_array );
    }
    
    /**
     * Display vendor profile management content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_profile_endpoint() {
        global $MVX;
        $user = wp_get_current_user();
        $MVX->library->load_dashboard_upload_lib();
        $MVX->template->get_template( 'vendor-dashboard/profile.php', array( 'user' => $user ) );
    }

    /**
     * display vendor policies content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_policies_endpoint() {
        global $MVX;
        $vendor = get_mvx_vendor( get_current_vendor_id() );
        $user_array = $MVX->user->get_vendor_fields( $vendor->id );
        if ( ! wp_script_is( 'tiny_mce', 'enqueued' ) ) {
            wp_enqueue_editor();
        }
        $MVX->template->get_template( 'vendor-dashboard/vendor-policy.php', $user_array );
    }

    /**
     * Display Vendor billing settings content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_billing_endpoint() {
        global $MVX;
        $vendor = get_mvx_vendor( get_current_vendor_id() );
        $user_array = $MVX->user->get_vendor_fields( $vendor->id );
        $MVX->template->get_template( 'vendor-dashboard/vendor-billing.php', $user_array );
    }

    /**
     * Display vendor shipping content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_shipping_endpoint() {
        global $MVX;
        $MVX->library->load_select2_lib();
        $mvx_payment_settings_name = mvx_get_option( 'mvx_payment_settings_name' );
        $_vendor_give_shipping = get_user_meta( get_current_vendor_id(), '_vendor_give_shipping', true );
        if ( is_mvx_shipping_module_active() && empty( $_vendor_give_shipping ) ) {
            if (wp_script_is('mvx-vendor-shipping', 'registered') &&
                !wp_script_is('mvx-vendor-shipping', 'enqueued')) {
                wp_enqueue_script('mvx-vendor-shipping');
            }

            $MVX->template->get_template('vendor-dashboard/vendor-shipping.php');
        } else {
            echo '<p class="mvx_headding3">' . __( 'Sorry you are not authorized for this pages. Please contact with admin.', 'multivendorx' ) . '</p>';
        }
    }

    /**
     * Display vendor report content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_report_endpoint() {
        global $MVX;
        if ( isset( $_POST['mvx_stat_start_dt'] ) ) {
            $start_date = wc_clean( wp_unslash( $_POST['mvx_stat_start_dt'] ) );
        } else {
            // hard-coded '01' for first day     
            $start_date = date( 'Y-m-01' );
        }

        if ( isset( $_POST['mvx_stat_end_dt'] ) ) {
            $end_date = wc_clean( wp_unslash( $_POST['mvx_stat_end_dt'] ) );
        } else {
            // hard-coded '01' for first day
            $end_date = date( 'Y-m-d' );
        }
        $vendor = get_mvx_vendor( get_current_vendor_id() );
        $MVX_Plugin_Post_Reports = new MVX_Report();
        $array_report = $MVX_Plugin_Post_Reports->vendor_sales_stat_overview( $vendor, $start_date, $end_date );
        $MVX->template->get_template( 'vendor-dashboard/vendor-report.php', $array_report );
    }
    
    public function mvx_vendor_dashboard_banking_overview_endpoint() {
        global $MVX;
        $table_headers = apply_filters('mvx_vendor_dashboard_banking_overview_table_headers', array(
            'status'        => array('label' => __( 'Status', 'multivendorx' ), 'class' => 'text-center'),
            'date'          => array('label' => __( 'Date', 'multivendorx' )),
            'ref_type'      => array('label' => __( 'Type', 'multivendorx' )),
            'ref_info'      => array('label' => __( 'Reference', 'multivendorx' )),
            'credit'        => array('label' => __( 'Credit', 'multivendorx' )),
            'debit'         => array('label' => __( 'Debit', 'multivendorx' )),
            'balance'       => array('label' => __( 'Balance', 'multivendorx' )),
        ), get_current_user_id());
        $MVX->library->load_dataTable_lib();
        $MVX->template->get_template( 'vendor-dashboard/vendor-reports/vendor-ledger.php', array( 'table_headers' => $table_headers ) );
    }

    public function mvx_vendor_dashboard_add_product_endpoint() {
        global $MVX, $wp;
        $MVX->library->load_colorpicker_lib();
        $MVX->library->load_frontend_upload_lib();
        $MVX->library->load_accordian_lib();
        $MVX->library->load_select2_lib();

        $suffix = defined( 'MVX_SCRIPT_DEBUG' ) && MVX_SCRIPT_DEBUG ? '' : '.min';

        if ( mvx_is_module_active('spmv') && get_mvx_vendor_settings('is_singleproductmultiseller', 'spmv_pages') ) {
            wp_enqueue_script( 'mvx_admin_product_auto_search_js', $MVX->plugin_url . 'assets/admin/js/admin-product-auto-search' . $suffix . '.js', array( 'jquery' ), $MVX->version, true );
            wp_localize_script( 'mvx_admin_product_auto_search_js', 'mvx_admin_product_auto_search_js_params', array(
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'search_products_nonce' => wp_create_nonce( 'search-products' ),
            ) );
        }

        if ( ! wp_script_is( 'tiny_mce', 'enqueued' ) ) {
            wp_enqueue_editor();
        }
        // Enqueue jQuery UI and autocomplete
        wp_enqueue_script( 'jquery-ui-autocomplete' );
        wp_enqueue_script( 'wp-a11y' );
        wp_enqueue_script( 'suggest' );
        
        wp_register_script( 'mvx_product_classify', $MVX->plugin_url . 'assets/frontend/js/product-classify.js', array( 'jquery', 'jquery-blockui' ), $MVX->version, true );
        $script_param = array(
            'ajax_url' => $MVX->ajax_url(),
            'initial_graphic_url' => $MVX->plugin_url.'assets/images/select-category-graphic.png',
            'i18n' => array(
                'select_cat_list' => __( 'Select a category from the list', 'multivendorx' )
            )
        );
        wp_enqueue_script( 'mvx_product_classify' );
        $MVX->localize_script( 'mvx_product_classify', apply_filters( 'mvx_product_classify_script_data_params', $script_param ) );

        $MVX->template->get_template( 'vendor-dashboard/product-manager/add-product.php' );
    }
    
    public function mvx_vendor_dashboard_edit_product_endpoint(){
        global $MVX;
        require_once($MVX->plugin_path . 'classes/products/class-mvx-products-edit-product.php');
        // load scripts & styles
        $suffix = defined( 'MVX_SCRIPT_DEBUG' ) && MVX_SCRIPT_DEBUG ? '' : '.min';
        $MVX->library->load_select2_lib();
        $MVX->library->load_jquery_serializejson_library();
        $MVX->library->load_tabs_library();
        wp_enqueue_media();
        wp_enqueue_script( 'selectWoo' );
        wp_enqueue_style('advance-product-manager');
        // play video on wp editor
        wp_enqueue_script( 'mce-view' );
        wp_register_script( 'mvx-advance-product', $MVX->plugin_url . 'assets/frontend/js/product.js', array( 'jquery', 'jquery-ui-sortable', 'select2_js', 'jquery-ui-datepicker', 'selectWoo', 'mvx-serializejson', 'mvx-tabs' ), $MVX->version );
        wp_enqueue_script( 'mvx-meta-boxes' );
        $MVX->localize_script( 'mvx-meta-boxes');
        // load classes
        $MVX->load_class( 'edit-product', 'products' );
        $edit_product = new MVX_Products_Edit_Product();
        $edit_product->output();
    }

    public function mvx_vendor_dashboard_products_endpoint() {
        global $MVX;
        if ( is_user_logged_in() && is_user_mvx_vendor( get_current_vendor_id() ) ) {
            $MVX->library->load_dataTable_lib();
            $products_table_headers = array(
                'select_product' => '',
                'image'      => '<i class="mvx-font ico-image-icon"></i>',
                'name'       => __( 'Product', 'multivendorx' ),
                'price'      => __( 'Price', 'multivendorx' ),
                'stock'      => __( 'Stock', 'multivendorx' ),
                'categories' => __( 'Categories', 'multivendorx' ),
                'date'       => __( 'Date', 'multivendorx' ),
                'status'     => __( 'Status', 'multivendorx' ),
                'actions'     => __( 'Actions', 'multivendorx' ),
            );
            $products_table_headers = apply_filters( 'mvx_vendor_dashboard_product_list_table_headers', $products_table_headers );
            $table_init = apply_filters( 'mvx_vendor_dashboard_product_list_table_init', array(
                'ordering'    => 'true',
                'searching'   => 'false',
                'emptyTable'  => __( 'No products found!', 'multivendorx' ),
                'processing'  => __( 'Processing...', 'multivendorx' ),
                'info'        => __( 'Showing _START_ to _END_ of _TOTAL_ products', 'multivendorx' ),
                'infoEmpty'   => __( 'Showing 0 to 0 of 0 products', 'multivendorx' ),
                'lengthMenu'  => __( 'Number of rows _MENU_', 'multivendorx' ),
                'zeroRecords' => __( 'No matching products found', 'multivendorx' ),
                'search'      => __( 'Search:', 'multivendorx' ),
                'next'        => __( 'Next', 'multivendorx' ),
                'previous'    => __( 'Previous', 'multivendorx' ),
            ) );

            $MVX->template->get_template( 'vendor-dashboard/product-manager/products.php', array( 'products_table_headers' => $products_table_headers, 'table_init' => $table_init ) );
        }
    }

    public function mvx_vendor_dashboard_add_coupon_endpoint() {
        global $MVX, $wp;
              
        $MVX->library->load_select2_lib();
        wp_enqueue_script( 'selectWoo' );
        wp_register_script( 'mvx-advance-coupon', $MVX->plugin_url . 'assets/frontend/js/coupon.js', array( 'jquery', 'select2_js', 'jquery-ui-datepicker', 'selectWoo' ), $MVX->version );
        wp_enqueue_script( 'mvx-meta-boxes' );
        $MVX->localize_script( 'mvx-meta-boxes');
        // load classes
        $MVX->load_class( 'add-coupon', 'coupons' );
        $add_coupon = new MVX_Coupons_Add_Coupon();
        $add_coupon->output();
        
    }

    public function mvx_vendor_dashboard_coupons_endpoint() {
        global $MVX;
        if ( is_user_logged_in() && is_user_mvx_vendor( get_current_vendor_id() ) ) {
            $MVX->library->load_dataTable_lib();
            $MVX->template->get_template( 'vendor-dashboard/coupon-manager/coupons.php' );
        }
    }

    /**
     * Dashboard order endpoint contect
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_orders_endpoint() {
        global $MVX, $wp;
        $vendor = get_current_vendor();
        $suffix       = defined( 'MVX_SCRIPT_DEBUG' ) && MVX_SCRIPT_DEBUG ? '' : '.min';
        if ( isset( $_POST['mvx-submit-mark-as-ship'] ) ) {
            $order_id = isset( $_POST['order_id'] ) ? ( $_POST['order_id'] ) : 0;
            $filterActionData = array();
            parse_str($order_id, $filterActionData);
            $selected_orders = isset($filterActionData['selected_orders']) ? $filterActionData['selected_orders'] : array();

            $tracking_id = isset( $_POST['tracking_id'] ) ? wc_clean( wp_unslash( $_POST['tracking_id'] ) ) : 0;
            $tracking_url = isset( $_POST['tracking_url'] ) ? esc_url( $_POST['tracking_url'] ) : '';

            if (!empty($selected_orders)) {
                foreach ($selected_orders as $order_number) {
                    $vendor->set_order_shipped( $order_number, $tracking_id, $tracking_url );
                }
            } else {
                $vendor->set_order_shipped( absint($order_id), $tracking_id, $tracking_url );
            }
        }
        $vendor_order = $wp->query_vars[get_mvx_vendor_settings( 'mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders' )];
        if ( ! empty( $vendor_order ) ) {
            $order = wc_get_order( $vendor_order );
            $MVX->library->load_select2_lib();
            wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            wp_register_script( 'wc-clipboard', WC()->plugin_url() . '/assets/js/admin/wc-clipboard' . $suffix . '.js', array( 'jquery' ), WC_VERSION );
            wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.4' );
            wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), WC_VERSION );
            wp_localize_script(
                    'wc-enhanced-select',
                    'wc_enhanced_select_params',
                    array(
                            'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'multivendorx' ),
                            'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'multivendorx' ),
                            'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'multivendorx' ),
                            'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'multivendorx' ),
                            'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'multivendorx' ),
                            'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'multivendorx' ),
                            'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'multivendorx' ),
                            'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'multivendorx' ),
                            'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'multivendorx' ),
                            'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'multivendorx' ),
                            'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                            'search_products_nonce'     => wp_create_nonce( 'search-products' ),
                            'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
                            'search_categories_nonce'   => wp_create_nonce( 'search-categories' ),
                    )
            );
            wp_enqueue_script('selectWoo');
            wp_enqueue_script('wc-clipboard');
            wp_enqueue_script('jquery-tiptip');
            wp_enqueue_script('wc-enhanced-select');
            wp_register_script('mvx_order_details_js', $MVX->plugin_url . 'assets/frontend/js/mvx-order-details.js', array('jquery', 'accounting', 'jquery-tiptip', 'wc-enhanced-select', 'wc-clipboard'), $MVX->version, true);
            wp_enqueue_script('mvx_order_details_js');
            wp_localize_script(
                    'mvx_order_details_js',
                    'mvx_order_details_js_script_data',
                    array(
                        'i18n_do_refund'                => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'multivendorx' ),
                        'post_id'                       => isset( $vendor_order ) ? $vendor_order : '',
                        'order_item_nonce'              => wp_create_nonce( 'mvx-order-item' ),
                        'grant_access_nonce'            => wp_create_nonce( 'grant-access' ),
                        'revoke_access_nonce'           => wp_create_nonce( 'revoke-access' ),
                        'mon_decimal_point'             => wc_get_price_decimal_separator(),
                        'ajax_url'                      => admin_url( 'admin-ajax.php' ),
                        'currency_format_num_decimals'  => wc_get_price_decimals(),
                        'currency_format_symbol'        => get_woocommerce_currency_symbol( $order->get_currency() ),
                        'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
                        'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
                        'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS.
                        'rounding_precision'            => wc_get_rounding_precision(),
                        'i18n_download_permission_fail' => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'multivendorx' ),
                        'i18n_permission_revoke'        => __( 'Are you sure you want to revoke access to this download?', 'multivendorx' ),
                        'i18n_do_cancel'                => __( 'Are you sure you want to cancel this order? This action cannot be undone.', 'multivendorx' ),
                    )
            );
            $MVX->template->get_template( 'vendor-dashboard/vendor-orders/vendor-order-details.php', array( 'order_id' => $vendor_order ) );
        } else {
            $MVX->library->load_dataTable_lib();

            if ( ! empty( $_POST['mvx_start_date_order'] ) ) {
                $start_date = wc_clean( wp_unslash( $_POST['mvx_start_date_order'] ) );
            } else {
                $start_date = date( 'Y-m-01' );
            }

            if ( ! empty( $_POST['mvx_end_date_order'] ) ) {
                $end_date = wc_clean( wp_unslash( $_POST['mvx_end_date_order'] ) );
            } else {
                $end_date = date( 'Y-m-d' );
            }
            
            /**
             * Action hook befor order list.
             *
             * @since 3.4.7
             */
            do_action('mvx_befor_vendor_dashboard_order_list_actions', $_POST );
            
            // bulk actions
            $bulk_actions = apply_filters( 'mvx_bulk_actions_vendor_order_list', array(
                'mark_processing'   => __( 'Change status to processing', 'multivendorx' ),
                'mark_on-hold'      => __( 'Change status to on-hold', 'multivendorx' ),
                'mark_completed'   => __( 'Change status to completed', 'multivendorx' ),
            ), $vendor );
                
            $MVX->template->get_template( 'vendor-dashboard/vendor-orders.php', array( 
                'vendor' => $vendor, 
                'start_date'    => strtotime( $start_date ), 
                'end_date'      => strtotime( $end_date . ' +1 day' ),
                'bulk_actions'  => $bulk_actions,
            ) );
        }
    }

    /**
     * Display Vendor Withdrawal Content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_withdrawal_endpoint() {
        global $MVX;
        $vendor = get_mvx_vendor( get_current_vendor_id() );
        if ( $vendor ) {
            $MVX->library->load_dataTable_lib();
            $meta_query['meta_query'] = array(
                array(
                    'key' => '_paid_status',
                    'value' => array('unpaid', 'partial_refunded'),
                    'compare' => 'IN'
                ),
                array(
                    'key'     => '_commission_vendor',
                    'value'   => absint( $vendor->term_id ),
                    'compare' => '='
                )
            );
            $vendor_unpaid_orders = $vendor->get_unpaid_orders( false, false, $meta_query );
            
            // withdrawal table init
            $table_init = apply_filters( 'mvx_vendor_dashboard_payment_withdrawal_table_init', array(
                'ordering'    => 'false',
                'searching'   => 'false',
                'emptyTable'  => __( 'No orders found!', 'multivendorx' ),
                'processing'  => __( 'Processing...', 'multivendorx' ),
                'info'        => __( 'Showing _START_ to _END_ of _TOTAL_ orders', 'multivendorx' ),
                'infoEmpty'   => __( 'Showing 0 to 0 of 0 orders', 'multivendorx' ),
                'lengthMenu'  => __( 'Number of rows _MENU_', 'multivendorx' ),
                'zeroRecords' => __( 'No matching orders found', 'multivendorx' ),
                'search'      => __( 'Search:', 'multivendorx' ),
                'next'        => __( 'Next', 'multivendorx' ),
                'previous'    => __( 'Previous', 'multivendorx' ),
            ) );

            $MVX->template->get_template( 'vendor-dashboard/vendor-withdrawal.php', array( 'vendor' => $vendor, 'vendor_unpaid_orders' => $vendor_unpaid_orders, 'table_init' => $table_init ) );
        }
    }

    /**
     * Display transaction details content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_transaction_details_endpoint() {
        global $MVX, $wp;
        $user_id = get_current_vendor_id();
        if ( is_user_mvx_vendor( $user_id ) ) {
            $transaction_id = $wp->query_vars[get_mvx_vendor_settings( 'mvx_transaction_details_endpoint', 'seller_dashbaord', 'transaction-details' )];
            if ( ! empty( $transaction_id ) ) {
                $MVX->template->get_template( 'vendor-dashboard/vendor-withdrawal/vendor-withdrawal-request.php', array( 'transaction_id' => $transaction_id ) );
            } else {
                $MVX->library->load_dataTable_lib();
                $MVX->template->get_template( 'vendor-dashboard/vendor-transactions.php' );
            }
        }
    }

    /**
     * Display Vendor followers content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_followers_endpoint() {
        global $MVX;
        if (mvx_is_module_active('follow-store')) {
            $MVX->template->get_template( 'vendor-dashboard/vendor_followers.php' );
        }
    }

    /**
     * Display Vendor university content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_knowledgebase_endpoint() {
        global $MVX;
        if (mvx_is_module_active('knowladgebase')) {
            wp_enqueue_style( 'jquery-ui-style' );
            wp_enqueue_script( 'jquery-ui-accordion' );
            $MVX->template->get_template( 'vendor-dashboard/vendor-university.php' );
        }   
    }

    /**
     * Display Vendor Tools purging content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_vendor_tools_endpoint() {
        global $MVX;
        $MVX->template->get_template( 'vendor-dashboard/vendor-tools.php' );
    }

    /**
     * Display Vendor Products Q&As content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_products_qna_endpoint() {
        global $MVX;
        if ( is_user_logged_in() && is_user_mvx_vendor( get_current_vendor_id() ) ) {
            $MVX->library->load_dataTable_lib();
            $MVX->library->load_select2_lib();
            $MVX->template->get_template( 'vendor-dashboard/vendor-products-qna.php' );
        }
    }

    /**
     * Display Vendor refund content
     * @global object $MVX
     */
    public function mvx_vendor_dashboard_refund_request_endpoint() {
        global $MVX;
        if (mvx_is_module_active('marketplace-refund')) {
            if ( is_user_logged_in() && is_user_mvx_vendor(get_current_user_id() ) ) {
                $MVX->library->load_dataTable_lib();
                $MVX->template->get_template( 'vendor-dashboard/vendor-refund-list.php' );
            }
        }
    }

    public function save_vendor_dashboard_data() {
        global $MVX;
        $vendor = get_mvx_vendor( get_current_vendor_id() );
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $post_data = isset($_POST) ? wp_unslash($_POST) : false;
            switch ( $MVX->endpoints->get_current_endpoint() ) {
                case 'storefront':
                case 'vendor-policies':
                case 'vendor-billing':
                    $error = $MVX->vendor_dashboard->save_store_settings( $vendor->id, $post_data );
                    if ( empty( $error ) ) {
                        wc_add_notice( __( 'All Options Saved', 'multivendorx' ), 'success' );
                    } else {
                        wc_add_notice( $error, 'error' );
                    }
                    break;
                case 'vendor-shipping':
                    $MVX->vendor_dashboard->save_vendor_shipping( $vendor->id, $post_data );
                    break;
                case 'profile':
                    $MVX->vendor_dashboard->save_vendor_profile( $vendor->id, $post_data );
                    break;
                case 'vendor-orders':
                    $MVX->vendor_dashboard->save_handler_vendor_orders( $post_data );
                    break;
                default :
                    break;
            }
        }
        // FPM add product messages
        if ( get_transient( 'mvx_fpm_product_added_msg' ) ) {
            wc_add_notice( get_transient( 'mvx_fpm_product_added_msg' ), 'success' );
            delete_transient( 'mvx_fpm_product_added_msg' );
        }
    }

    /**
     * Change endpoint page title
     * @global object $wp_query
     * @global object $MVX
     * @param string $title
     * @return string
     */
    public function mvx_vendor_dashboard_endpoint_title( $title ) {
        global $wp_query, $MVX;
        if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && is_mvx_endpoint_url() ) {
            $endpoint = $MVX->endpoints->get_current_endpoint();

            if ( isset( $MVX->endpoints->mvx_query_vars[$endpoint]['label'] ) && $endpoint_title = $MVX->endpoints->mvx_query_vars[$endpoint]['label'] ) {
                $title = $endpoint_title;
            }

            remove_filter( 'the_title', array( &$this, 'mvx_vendor_dashboard_endpoint_title' ) );
        }

        return $title;
    }

    /**
     * set policies tab cap
     * @param Boolean $cap
     * @return Boolean
     */
    public function mvx_vendor_dashboard_menu_vendor_policies_capability( $cap ) {
        if ( (mvx_is_module_active('store-policy') && apply_filters( 'mvx_vendor_can_overwrite_policies', true )) || (get_mvx_vendor_settings('is_customer_support_details', 'settings_general') ) ) {
            $cap = true;
        }
        return $cap;
    }

    public function mvx_vendor_dashboard_menu_vendor_withdrawal_capability( $cap ) {
        if ( get_mvx_vendor_settings( 'withdrawal_request', 'disbursement' ) ) {
            $cap = true;
        }
        return $cap;
    }

    public function mvx_vendor_dashboard_menu_vendor_shipping_capability( $cap ) {
        $vendor = get_mvx_vendor( get_current_vendor_id() );
        if ( $vendor ) {
            return $vendor->is_shipping_enable();
        } else {
            return false;
        }
    }

    public function mvx_vendor_dashboard_menu_vendor_knowledgebase_capability( $cap ) {
        return mvx_is_module_active('knowladgebase');
    }

    public function mvx_vendor_dashboard_menu_refund_request_capability( $cap ) {
        return mvx_is_module_active('marketplace-refund');
    }

    /**
     * Generate Vendor Progress
     * @global object $MVX
     */
    public function mvx_before_vendor_dashboard_content( $key ) {
        global $MVX;
        if ( $key !== $MVX->endpoints->get_current_endpoint() ) {
            return;
        }
        $vendor = get_mvx_vendor( get_current_vendor_id() );
        if ( $vendor && apply_filters( 'mvx_vendor_dashboard_show_progress_bar', true, $vendor ) ) {
            $vendor_progress = mvx_get_vendor_profile_completion( $vendor->id );
            if ( $vendor_progress['progress'] < 100 ) {
                echo '<div class="col-md-12">';
                echo '<div class="panel">';
                if ( $vendor_progress['todo'] && is_array( $vendor_progress['todo'] ) ) {
                    $todo_link = isset( $vendor_progress['todo']['link'] ) ? esc_url( $vendor_progress['todo']['link'] ) : '';
                    $todo_label = isset( $vendor_progress['todo']['label'] ) ? $vendor_progress['todo']['label'] : '';
                    echo '<div style="margin:17px 20px 12px 20px;">' . __( 'To boost up your profile progress add', 'multivendorx' ) . ' <a href="' . $todo_link . '">' . $todo_label . '</a></div>';
                }
                echo '<div class="progress" style="margin:0 20px 20px;">';
                echo '<div class="progress-bar" role="progressbar" style="width: ' . $vendor_progress['progress'] . '%;" aria-valuenow="' . $vendor_progress['progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $vendor_progress['progress'] . '%</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
    }

    /**
     * MVX theme supported function
     */
    public function mvx_add_theme_support() {
        if ( is_vendor_dashboard() && is_user_logged_in() && is_user_mvx_vendor( get_current_user_id() ) ) {
            global $wp_filter;
            //Flatsome mobile menu support
            remove_action( 'wp_footer', 'flatsome_mobile_menu', 7 );
            // Remove demo store notice
            remove_action( 'wp_footer', 'woocommerce_demo_store' );
            // Remove custom css
            $wp_head_hooks = $wp_filter['wp_head']->callbacks;
            foreach ( $wp_head_hooks as $priority => $wp_head_hook ) {
                foreach ( array_keys( $wp_head_hook ) as $hook ) {
                    if ( strpos( $hook, 'custom_css' ) ) {
                        remove_action( 'wp_head', $hook, $priority );
                    }
                }
            }
        }
    }

    /**
     * MVX rejected vendor dashboard function
     */
    public function rejected_vendor_dashboard_content() {
        global $MVX, $wp;
        
        if(isset($wp->query_vars['rejected-vendor-reapply'])) {
            $MVX->template->get_template('non-vendor/rejected-vendor-reapply.php');
        } else {
            $MVX->template->get_template('non-vendor/rejected-vendor-dashboard.php');
        }
    }
    
    /**
     *  Update rejected vendor data and make the status pending
     */
    public function save_rejected_vendor_reapply_data() {
        global $MVX;
        $user = wp_get_current_user();
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && is_user_mvx_rejected_vendor($user->ID) && $MVX->endpoints->get_current_endpoint() == 'rejected-vendor-reapply') {
            if(isset($_POST['reapply_vendor_application']) && isset($_POST['mvx_vendor_fields'])) {
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
                /**
                 * Action hook to modify vendor re submit application before save.
                 *
                 * @since 3.4.5
                 */
                do_action( 'mvx_before_reapply_vendor_application_save', $_POST, get_current_vendor( $user->ID ) );
                update_user_meta( $user->ID, 'mvx_vendor_fields', array_filter( array_map( 'wc_clean', (array) $_POST['mvx_vendor_fields'] ) ) );
                $user->remove_cap( 'dc_rejected_vendor' );
                $user->add_cap( 'dc_pending_vendor' );
                /**
                 * Action hook to modify vendor re submit application after save.
                 *
                 * @since 3.4.5
                 */
                do_action( 'mvx_after_reapply_vendor_application_save', $_POST, get_current_vendor( $user->ID ) );
                $mvx_vendor_rejection_notes = unserialize( get_user_meta( $user->ID, 'mvx_vendor_rejection_notes', true ) );
                $mvx_vendor_rejection_notes[time()] = array(
                        'note_by' => $user->ID,
                        'note' => __( 'Re applied to become a vendor', 'multivendorx' ));
                update_user_meta( $user->ID, 'mvx_vendor_rejection_notes', serialize( $mvx_vendor_rejection_notes ) );
                // send mail to admin when rejected vendor reapply
                if (apply_filters( 'mvx_send_mail_to_admin_when_vendor_reapply', true )) {
                    $email_admin = WC()->mailer()->emails['WC_Email_Admin_New_Vendor_Account'];
                    $email_admin->trigger($user->ID);
                }
                /**
                * Action hook to modify vendor re submit application after note save.
                *
                * @since 3.4.5
                */
                do_action( 'mvx_after_reapply_vendor_application_saved_notes', $_POST, get_current_vendor( $user->ID ) );
            }
        }
    }
}
