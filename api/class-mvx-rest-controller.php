<?php
/**
 * Multivendor X API
 *
 * Handles MVX-API endpoint requests.
 *
 * @author 		MultiVendorX
 * @category API
 * @package MultiVendorX/API
 * @since    3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * API class.
 */
class MVX_REST_API {
    /**
     * Setup class.
     *
     * @since 3.1
     */

    public $MVX_REST_API_Vendors_Controller;
    public $MVX_REST_API_Vendor_Reviews_Controller;

    public function __construct() {

        // Add query vars.
        add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

        // Register API endpoints.
        add_action( 'init', array( $this, 'add_endpoint' ), 0 );

        // Handle wc-api endpoint requests.
        add_action( 'parse_request', array( $this, 'handle_api_requests' ), 0 );
        
        // WP REST API.
        $this->rest_api_init();

        if (current_user_can('manage_options')) {
            add_action( 'rest_api_init', array( $this, 'mvx_rest_routes_react_module' ) );
        }
    }
    

    /**
     * Add new query vars.
     *
     * @since 3.1
     * @param array $vars Query vars.
     * @return string[]
     */
    public function add_query_vars( $vars ) {
        $vars[] = 'mvx-api';
        return $vars;
    }

    /**
     * MVX API for payment gateway IPNs, etc.
     *
     * @since 3.1
     */
    public static function add_endpoint() {
        add_rewrite_endpoint( 'mvx-api', EP_ALL );
    }

    /**
     * API request - Trigger any API requests.
     *
     * @since   3.1
     */
    public function handle_api_requests() {
        global $wp;

        if ( ! empty( $_GET['mvx-api'] ) ) { // WPCS: input var okay, CSRF ok.
            $wp->query_vars['mvx-api'] = sanitize_key( wp_unslash( $_GET['mvx-api'] ) ); // WPCS: input var okay, CSRF ok.
        }

        // mvx-api endpoint requests.
        if ( ! empty( $wp->query_vars['mvx-api'] ) ) {

            // Buffer, we won't want any output here.
            ob_start();

            // No cache headers.
            wc_nocache_headers();

            // Clean the API request.
            $api_request = strtolower( wc_clean( $wp->query_vars['mvx-api'] ) );

            // Trigger generic action before request hook.
            do_action( 'mvx_rest_api_request', $api_request );

            // Is there actually something hooked into this API request? If not trigger 400 - Bad request.
            status_header( has_action( 'mvx_rest_api_' . $api_request ) ? 200 : 400 );

            // Trigger an action which plugins can hook into to fulfill the request.
            do_action( 'mvx_rest_api_' . $api_request );

            // Done, clear buffer and exit.
            ob_end_clean();
            die( '-1' );
        }
    }

    /**
     * Init WP REST API.
     *
     * @since 3.1
     */
    private function rest_api_init() {
        // REST API was included starting WordPress 4.4.
        if ( ! class_exists( 'WP_REST_Server' ) ) {
            return;
        }

        // Init REST API routes.
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
    }

    /**
     * Include REST API classes.
     *
     * @since 3.1
     */
    private function rest_api_includes() {
        // REST API v1 controllers.
        $this->load_controller_class('vendors');
        $this->load_controller_class('products');
        $this->load_controller_class('coupons');
        $this->load_controller_class('orders');
        $this->load_controller_class('vendor-reviews');
    }

    /**
     * Register REST API routes.
     *
     * @since 3.1
     */
    public function register_rest_routes() {
        // Register settings to the REST API.
        $this->register_wp_admin_settings();

        $this->rest_api_includes();

        $controllers = array(
            // v1 controllers.
            'MVX_REST_API_Vendors_Controller',
            'MVX_REST_API_Vendor_Reviews_Controller'
        );

        foreach ( $controllers as $controller ) {
            $this->$controller = new $controller();
            $this->$controller->register_routes();
        }
    }

    /**
     * Register WC settings from WP-API to the REST API.
     *
     * @since  3.0.0
     */
    public function register_wp_admin_settings() {
        $pages = WC_Admin_Settings::get_settings_pages();
        foreach ( $pages as $page ) {
            new WC_Register_WP_Admin_Settings( $page, 'page' );
        }

        $emails = WC_Emails::instance();
        foreach ( $emails->get_emails() as $email ) {
            new WC_Register_WP_Admin_Settings( $email, 'email' );
        }
    }
    
    /**
     * Load class located under api folder
     *
     * @since  3.1
     */
    function load_controller_class($class_name = '') {
        global $MVX;
        if ('' != $class_name) {
            require_once($MVX->plugin_path . 'api/class-' . esc_attr($MVX->token) . '-rest-' . esc_attr($class_name) . '-controller.php');
        } // End If Statement
    }

    public function mvx_rest_routes_react_module() {
        // module page checkbox update/true/false
        register_rest_route( 'mvx_module/v1', '/checkbox_update', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'save_checkbox_module' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // list of modules block
        register_rest_route( 'mvx_module/v1', '/module_lists', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_get_module_lists' ),
            'permission_callback' => array( $this, 'save_settings_permission' ),
            'args'     => [
                'module_id' => [
                    'required' => false,
                    'type'     => 'string',
                ],
            ],
        ] );

        // save settings page data on database
        register_rest_route( 'mvx_module/v1', '/save_dashpages', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_save_dashpages' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // save registration fileds data from settings page
        register_rest_route( 'mvx_module/v1', '/save_registration', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_save_registration_forms' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // fetch registration fileds data from settings page
        register_rest_route( 'mvx_module/v1', '/get_registration', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_get_registration_forms_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // list of vendors on vendor tab section
        register_rest_route( 'mvx_module/v1', '/all_vendors', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_all_vendor_details' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // ceate vendor
        register_rest_route( 'mvx_module/v1', '/create_vendor', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_create_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // update vendor
        register_rest_route( 'mvx_module/v1', '/update_vendor', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // all vendor followers
        register_rest_route( 'mvx_module/v1', '/all_vendor_followers', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_all_vendor_followers' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // list of vendors
        register_rest_route( 'mvx_module/v1', '/vendor_list_search', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_vendor_list_search' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // search specific vendor
        register_rest_route( 'mvx_module/v1', '/specific_search_vendor', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_specific_search_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // fetch a specific vendor shipping
        register_rest_route( 'mvx_module/v1', '/specific_vendor_shipping', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_specific_vendor_shipping' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // fetch a specific vendor shipping zero
        register_rest_route( 'mvx_module/v1', '/specific_vendor_shipping_zone', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_specific_vendor_shipping_zone' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // add new shipping options
        register_rest_route( 'mvx_module/v1', '/add_shipping_option', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_add_shipping_option' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // add new shipping method
        register_rest_route( 'mvx_module/v1', '/add_vendor_shipping_method', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_add_vendor_shipping_method' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // Update vendor shipping method
        register_rest_route( 'mvx_module/v1', '/update_vendor_shipping_method', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_vendor_shipping_method' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // Delete vendor shipping method
        register_rest_route( 'mvx_module/v1', '/delete_shipping_method', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_delete_shipping_method' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // Toggle checkbox vendor shipping method
        register_rest_route( 'mvx_module/v1', '/toggle_shipping_method', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_toggle_shipping_method' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // update shipping post code
        register_rest_route( 'mvx_module/v1', '/update_post_code', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_post_code' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // list of commissions
        register_rest_route( 'mvx_module/v1', '/all_commission', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_all_commission_details' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // search specific commission by id
        register_rest_route( 'mvx_module/v1', '/search_specific_commission', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_search_specific_commission' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // display commission as choosen from status option
        register_rest_route( 'mvx_module/v1', '/show_commission_from_status_list', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_show_commission_from_status_list' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // list all vendor name
        register_rest_route( 'mvx_module/v1', '/show_vendor_name', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_show_vendor_name' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // Search commission as per vendor name 
        register_rest_route( 'mvx_module/v1', '/search_commissions_as_per_vendor_name', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_search_commissions_as_per_vendor_name' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // commission paid and download csv from bulk actions
        register_rest_route( 'mvx_module/v1', '/update_commission_bulk', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_commission_bulk' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        
        register_rest_route( 'mvx_module/v1', '/export_csv_for_report_product_chart', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_export_csv_for_report_product_chart' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/export_csv_for_report_vendor_chart', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_export_csv_for_report_vendor_chart' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/update_specific_vendor_shipping_option', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_specific_vendor_shipping_option' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/vendor_delete', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_vendor_delete' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/commission_delete', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_commission_delete' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/details_specific_commission', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_details_specific_commission' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
    
        register_rest_route( 'mvx_module/v1', '/get_commission_id_status', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_get_commission_id_status' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/update_commission_status', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_commission_status' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );


        register_rest_route( 'mvx_module/v1', '/get_report_overview_data', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_get_report_overview_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/fetch_report_overview_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_fetch_report_overview_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/create_announcement', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_create_announcement' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/display_announcement', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_display_announcement' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/update_announcement_display', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_update_announcement_display' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );


        register_rest_route( 'mvx_module/v1', '/update_custom_post_status', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_custom_post_status' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/update_announcement', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_announcement' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // knowledgebase
        register_rest_route( 'mvx_module/v1', '/create_knowladgebase', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_create_knowladgebase' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/display_list_knowladgebase', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_display_list_knowladgebase' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/update_knowladgebase_display', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_update_knowladgebase_display' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/update_knowladgebase', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_knowladgebase' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/list_of_all_tab_based_settings_field', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_all_tab_based_settings_field' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // pending product
        register_rest_route( 'mvx_module/v1', '/list_of_pending_vendor_product', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_pending_vendor_product' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // pending vendor
        register_rest_route( 'mvx_module/v1', '/list_of_pending_vendor', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_pending_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // pending coupon
        register_rest_route( 'mvx_module/v1', '/list_of_pending_vendor_coupon', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_pending_vendor_coupon' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // pending transaction
        register_rest_route( 'mvx_module/v1', '/list_of_pending_transaction', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_pending_transaction' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // pending Question
        register_rest_route( 'mvx_module/v1', '/list_of_pending_question', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_pending_question' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // pending Question
        register_rest_route( 'mvx_module/v1', '/list_of_bulk_change_status_question', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_list_of_bulk_change_status_question' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // approve pending Question
        register_rest_route( 'mvx_module/v1', '/approve_dismiss_pending_question', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_approve_dismiss_pending_question' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );        

        // store review
        register_rest_route( 'mvx_module/v1', '/list_of_store_review', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_store_review' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );


        // search announcement
        register_rest_route( 'mvx_module/v1', '/search_announcement', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_search_announcement' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // search knowledgebase
        register_rest_route( 'mvx_module/v1', '/search_knowledgebase', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_search_knowledgebase' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // delete post
        register_rest_route( 'mvx_module/v1', '/delete_post_details', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_delete_post_details' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // delete post
        register_rest_route( 'mvx_module/v1', '/dismiss_requested_vendors_query', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_dismiss_requested_vendors_query' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // delete post
        register_rest_route( 'mvx_module/v1', '/approve_product', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_approve_product' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // approve vendor
        register_rest_route( 'mvx_module/v1', '/approve_vendor', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_approve_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // reject vendor
        register_rest_route( 'mvx_module/v1', '/reject_vendor', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_reject_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // dismiss vendor
        register_rest_route( 'mvx_module/v1', '/dismiss_vendor', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_dismiss_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // dismiss and approve vendor coupon
        register_rest_route( 'mvx_module/v1', '/dismiss_and_approve_vendor_coupon', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_dismiss_and_approve_vendor_coupon' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // dismiss and approve questions
        register_rest_route( 'mvx_module/v1', '/dismiss_and_approve_vendor_product_questions', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_dismiss_and_approve_vendor_product_questions' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        //pending products bulk approve or dismiss
        register_rest_route( 'mvx_module/v1', '/bulk_todo_pending_product', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_bulk_todo_pending_product' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        //pending products bulk approve or dismiss
        register_rest_route( 'mvx_module/v1', '/fetch_all_settings_for_searching', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_fetch_all_settings_for_searching' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        //search question and aswear
        register_rest_route( 'mvx_module/v1', '/search_question_ans', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_search_question_ans' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        //search question and aswear
        register_rest_route( 'mvx_module/v1', '/delete_review', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_delete_review' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        //search question and aswear
        register_rest_route( 'mvx_module/v1', '/search_review', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_search_review' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
    
        //search question and aswear
        register_rest_route( 'mvx_module/v1', '/tools_funtion', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_tools_funtion' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // fetch system info
        register_rest_route( 'mvx_module/v1', '/fetch_system_info', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_fetch_system_info' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/system_info_copy_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_system_info_copy_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/report_abuse_details', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_report_abuse_details' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/report_abuse_delete', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_report_abuse_delete' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/fetch_all_modules_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_fetch_all_modules_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
    
        register_rest_route( 'mvx_module/v1', '/search_module_lists', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_search_module_lists' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/modules_count', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_modules_count' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/get_as_per_module_status', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_get_as_per_module_status' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/active_suspend_vendor', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_active_suspend_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/list_vendor_application_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_vendor_application_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/list_vendor_application_raw_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_vendor_application_raw_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/list_vendor_roles_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_vendor_roles_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/approve_dismiss_pending_transaction', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_approve_dismiss_pending_transaction' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // fetch all tabs
        register_rest_route( 'mvx_module/v1', '/list_of_all_tabs', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_all_tabs' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // update vendor store data
        register_rest_route( 'mvx_module/v1', '/update_vendor_store', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_update_vendor_store' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // taskboard datas display dynamicaly from array
        register_rest_route( 'mvx_module/v1', '/list_of_work_board_content', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_work_board_content' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // taskboard section every icons working callback
        register_rest_route( 'mvx_module/v1', '/task_board_icons_triggers', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_task_board_icons_triggers' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // fetch individual tab list
        register_rest_route( 'mvx_module/v1', '/find_individual_vendor_tabs', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_find_individual_vendor_tabs' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // fetch individual tab list
        register_rest_route( 'mvx_module/v1', '/fetch_pending_verification_data', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_fetch_pending_verification_data' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        register_rest_route( 'mvx_module/v1', '/vendor_pending_verification_action', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_vendor_pending_verification_action' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // pending shipping for all vendor
        register_rest_route( 'mvx_module/v1', '/vendor_pending_shipping', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_vendor_pending_shipping' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // pending shipping for all vendor
        register_rest_route( 'mvx_module/v1', '/vendor_short_pending_customer', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_vendor_short_pending_customer' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // pending shipping for all vendor
        register_rest_route( 'mvx_module/v1', '/seller_latest_ativity', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_seller_latest_ativity' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // all refund request
        register_rest_route( 'mvx_module/v1', '/list_of_refund_request', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_refund_request' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        
        // all refunded order
        register_rest_route( 'mvx_module/v1', '/list_of_refunded_orders', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_refunded_orders' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // Specific vendor products
        register_rest_route( 'mvx/v1', '/vendor_products', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_vendor_products' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // Specific vendor orders
        register_rest_route( 'mvx/v1', '/vendor_orders', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_vendor_orders' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // Specific vendor announcement
        register_rest_route( 'mvx/v1', '/vendor_announcement', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_vendor_announcement' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // Specific vendor reports
        register_rest_route( 'mvx/v1', '/vendor_reports', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_vendor_reports' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // Specific vendor order
        register_rest_route( 'mvx/v1', '/vendor_order', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_vendor_order' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
        // create products
        register_rest_route( 'mvx/v1', '/create_product', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_create_product' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        // request profile deletion vendor
        register_rest_route( 'mvx_module/v1', '/list_of_request_profile_deletion_vendor', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => array( $this, 'mvx_list_of_request_profile_deletion_vendor' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );

        //request profile deletion bulk approve
        register_rest_route( 'mvx_module/v1', '/bulk_todo_request_profile_deletion', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array( $this, 'mvx_bulk_todo_request_profile_deletion' ),
            'permission_callback' => array( $this, 'save_settings_permission' )
        ] );
    }

    public function mvx_create_product($request) {
        $name = $request->get_param('name') ? $request->get_param('name') : '';
        $regular_price = $request->get_param('regular_price') ? $request->get_param('regular_price') : '';
        $sale_price = $request->get_param('sale_price') ? $request->get_param('sale_price') : '';
        $sku = $request->get_param('sku') ? $request->get_param('sku') : '';
        $manage_stock = $request->get_param('manage_stock') ? $request->get_param('manage_stock') : '';
        $product_id = wp_insert_post( array( 'post_title' => $name, 'post_type' => 'product', 'post_status' => 'publish' ) );

        update_post_meta($product_id, '_regular_price', $regular_price);
        update_post_meta($product_id, '_sale_price', $sale_price);
        update_post_meta($product_id, '_sku', $sku);
    }

    public function mvx_list_of_vendor_order($request) {
        $order_id = $request && $request->get_param('order_id') ? $request->get_param('order_id') : '';
        $order = wc_get_order($order_id);
        $email_and_phone = $product_name = '';


        $line_items          = $order->get_items( 'line_item' );
        foreach ($line_items as $item_id => $item) {
            $product_name      .= $item->get_name();
        }


        $billing_fields = array(
            'email' => array( 'label' => __( 'Email address', 'multivendorx' ) ),
            'phone' => array( 'label' => __( 'Phone', 'multivendorx' ) )
        );
        foreach ( $billing_fields as $key => $field ) {
            if ( isset( $field['show'] ) && false === $field['show'] ) {
                    continue;
            }

            $field_name = 'billing_' . $key;

            if ( isset( $field['value'] ) ) {
                    $field_value = $field['value'];
            } elseif ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
                    $field_value = $order->{"get_$field_name"}( 'edit' );
            } else {
                    $field_value = $order->get_meta( '_' . $field_name );
            }

            if ( 'billing_phone' === $field_name ) {
                    $field_value = $field_value;
            } else {
                    $field_value = $field_value;
            }

            if ( $field_value ) {
                    $email_and_phone = '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . wp_kses_post( $field_value ) . '</p>';
            }
        }

        $order_id_details = array(
            'order_date'                =>  $order->get_date_created()->date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ),
            'payment_method'            =>  $order->get_payment_method(),
            'billing_address'           =>  $order->get_formatted_billing_address(),
            'shipping_address'          =>  $order->get_formatted_shipping_address(),
            'email_and_phone'           =>  $email_and_phone,
            'product_name'              =>  $product_name,
            "status"                    =>  $order->get_status()
        );

        return rest_ensure_response($order_id_details);
    }

    public function mvx_list_of_vendor_reports($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : '';
        $status = $request && $request->get_param('status') ? $request->get_param('status') : '';
        $vendor = '';
        $reports_list = '';
        if ($vendor_id) {
            $vendor = get_mvx_vendor($vendor_id);
            if ($vendor) {
                $vendor_report_data = get_mvx_vendor_dashboard_stats_reports_data($vendor);
                if ($vendor_report_data) {
                    $reports_list = array(
                        'traffic_no'    =>  $vendor_report_data[$status]['_raw_stats_data']['current']['traffic_no'],
                        'coupon_total'  =>  $vendor_report_data[$status]['_raw_stats_data']['current']['coupon_total'],
                        'withdrawal'    =>  $vendor_report_data[$status]['_raw_stats_data']['current']['withdrawal'],
                        'earning'       =>  $vendor_report_data[$status]['_raw_stats_data']['current']['earning'],
                        'sales_total'   =>  $vendor_report_data[$status]['_raw_stats_data']['current']['sales_total'],
                        'orders_no'     =>  $vendor_report_data[$status]['_raw_stats_data']['current']['orders_no'],
                    );
                }
            }
        }
        return rest_ensure_response($reports_list);
    }

    public function mvx_list_of_vendor_products($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : '';
        $vendor = '';
        $product_list = [];
        if ($vendor_id) {
            $vendor = get_mvx_vendor($vendor_id);
        }
        if ($vendor) {
            $products_array = $vendor->get_products(array());
            if ($products_array) {
                foreach ($products_array as $key => $value) {
                    $product_list[] = array(
                        'ID'            =>  $value->ID,
                        'name'          =>  $value->post_title,
                        'price'         =>  get_post_meta($value->ID, '_price', true) ? get_post_meta($value->ID, '_price', true) . ' ' . get_option('woocommerce_currency') : '',
                        'status'        =>  $value->post_status
                    );
                }
            }
        }
        return rest_ensure_response($product_list);
    }

    public function mvx_list_of_vendor_orders($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : '';
        $vendor = '';
        $order_list = [];
        if ($vendor_id) {
            $vendor = get_mvx_vendor($vendor_id);
        }
        if ($vendor) {
            $args = array(
                'author' => $vendor->id,
                'post_status' => 'any',
                
            );
            $vendor_all_orders = mvx_get_orders($args);
            if ($vendor_all_orders) {
                foreach ($vendor_all_orders as $order_id) {
                    $order = wc_get_order($order_id);
                    $vendor_order = mvx_get_order($order_id);
                    $order_list[] = array(
                        'order_id'              =>  $order_id,
                        'order_date'                  =>  mvx_date($order->get_date_created()),
                        'vendor_earning'                 =>  $vendor_order && $vendor_order->get_commission_total() ? $vendor_order->get_commission_total() : '-',
                        'order_status' => esc_html(wc_get_order_status_name($order->get_status()))
                    );
                }
            }
        }
        return rest_ensure_response($order_list);
    }

    public function mvx_list_of_vendor_announcement($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : '';
        $vendor = '';
        $announcement_list = [];
        if ($vendor_id) {
            $vendor = get_mvx_vendor($vendor_id);
            $get_announcements = $vendor ? $vendor->get_announcements() : array();
            if ($get_announcements && isset($get_announcements['all'])) {
                foreach ($get_announcements['all'] as $key => $value) {
                    $announcement_list[] = array(
                        'Id'                        =>  $value->ID,
                        'post_title'                =>  $value->post_title,
                        'post_content'              =>  $value->post_content,
                        'post_date'                 =>  $value->post_date
                    );
                }
            }
        }
        return rest_ensure_response($announcement_list);
    }

    public function mvx_list_of_refund_request() {
        $lists = [];
        $default = array(
            'posts_per_page'  => -1,
            'post_type'       => 'shop_order',
            'post_status'     => 'any',
            'fields'          =>  'ids',
            'meta_query'      => array(
                    array(
                        'key'     => '_customer_refund_order',
                        'value'   => array('refund_request', 'refund_accept', 'refund_reject'),
                        'compare' => '='
                    )
                )
            );
        $query = new WP_Query( $default ); 
        foreach ($query->get_posts() as $post_id) {
            $refund_status = '';
            $order = wc_get_order($post_id);
            $post = get_post($post_id);
            if (!is_user_mvx_vendor($post->post_author)) continue;
            if (!$order) continue;
            $refund_status_raw = $order->get_meta('_customer_refund_order') ? $order->get_meta('_customer_refund_order') : '';
            switch ($refund_status_raw) {
                case 'refund_request':
                    $refund_status = __('Refund Pending','multivendorx');
                    break;
                case 'refund_accept':
                    $refund_status = __('Refund Accepted','multivendorx');
                    break;
                case 'refund_reject':
                    $refund_status = __('Refund Rejected','multivendorx');
                    break;
                default:
                    $refund_status = __('-','multivendorx');
                    break;
            }

            $lists[] = array(
                'order_id'          =>  $order->get_id(),
                'vendor'            =>  get_user_by('ID', $post->post_author)->display_name,
                'refund_reason'     =>  $order->get_meta('_customer_refund_reason') ? esc_html($order->get_meta('_customer_refund_reason')) : '-',
                'refund_status'     =>  $refund_status,
                'payment_gateway'   =>  $order->get_payment_method_title()
            );
        }
        return rest_ensure_response($lists);
    }

    public function mvx_list_of_refunded_orders() {
        $lists = [];
        $default = array(
            'posts_per_page'   => -1,
            'post_type'        => 'shop_order',
            'post_status' => 'any',
            'fields'    =>  'ids'
            );
        $query = new WP_Query( $default ); 
        foreach ($query->get_posts() as $post_id) {
            $refund_amount = '';
            $order = wc_get_order($post_id);
            $post = get_post($post_id);
            if (!is_user_mvx_vendor($post->post_author)) continue;
            if (!$order) continue;
            if (!$order->get_refunds()) continue;

            foreach ( $order->get_refunds() as $id => $refund ) {
                $refund_amount .= $refund->get_amount() ? $refund->get_amount() : '';
            }

            $lists[] = array(
                'order_id'          =>  $post_id,
                'vendor'            =>  get_user_by('ID', $post->post_author)->display_name,
                'refund_reason'     =>  $order->get_meta('_customer_refund_reason') ? esc_html($order->get_meta('_customer_refund_reason')) : '-',
                'refunded_amount'   =>  $refund_amount,
                'payment_gateway'   =>  $order->get_payment_method_title()
            );
        }
        return rest_ensure_response($lists);
    }

    public function mvx_vendor_short_pending_customer() {
        $pending_questions = $this->mvx_list_of_pending_question('', '')->data && !empty($this->mvx_list_of_pending_question('', '')->data) ? $this->mvx_list_of_pending_question('', '')->data[0] : '';
        $row = '';
        if ($pending_questions) {
            $Q_A_link = admin_url('admin.php?page=mvx#&submenu=work-board&name=question-ans');
            $row = '<h2 class="mvx-text-with-right-side-line-wrapper">' . __("Q & A Part", 'multivendorx') . '<hr></h2><div class="media">
                        <div class="media-body">
                            <h4 class="media-heading qna-question">' . wp_trim_words($pending_questions['question_details'], 160, '...') . '</h4>
                            <time class="qna-date">
                                <span>' . mvx_date($pending_questions['question_date']) . '</span>
                            </time>
                        </div>
                        <a href="'.$Q_A_link.'" class="footer-link">
                            Show All Q&As
                            <i class="mvx-font icon-link-right-arrow"></i>
                        </a>
                </div>
            ';
        }
        return rest_ensure_response($row);
    }

    public function mvx_seller_latest_ativity() {

        $store_product_details = $store_order_details = $store_order_refund_details = [];
        $args_multi_vendor = array(
            'posts_per_page' => -1,
            'post_type' => 'product',
            'post_status' => 'publish',
        );
        $vendor_query = new WP_Query($args_multi_vendor);
        if (!empty($vendor_query->get_posts())) {
            foreach ($vendor_query->get_posts() as $key_post => $value_post) {
                if (is_user_mvx_vendor($value_post->post_author)) {
                    $store_product_details[] = array('id' => $value_post->ID, 'title'   =>  $value_post->post_title, 'date' =>  $value_post->post_date, 'vendor'    =>  get_mvx_product_vendors($value_post->ID) ? get_mvx_product_vendors($value_post->ID)->page_title : '');
                }
            }
        }
        $store_first_id = !empty($store_product_details) && isset($store_product_details[0]) ? $store_product_details[0] : '';
        $store_second_id = !empty($store_product_details) && isset($store_product_details[1]) ? $store_product_details[1] : '';

        // order setails
        $args_multi_vendor = array(
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'post_status' => array('wc-processing', 'wc-completed'),
            'meta_query' => array(
                array(
                    'key' => '_commissions_processed',
                    'value' => 'yes',
                    'compare' => '='
                )
            ),
        );
        $vendor_query = new WP_Query($args_multi_vendor);
        if (!empty($vendor_query->get_posts())) {
            foreach ($vendor_query->get_posts() as $key_post => $value_post) {
                $order = wc_get_order( $value_post->ID );
                if (!$order) continue;
                if (get_post_meta( $value_post->ID, '_customer_refund_order', true )) {
                    $store_order_refund_details[] = array('id' => $value_post->ID, 'date' =>  $value_post->post_date, 'customer'    =>  $order->get_billing_first_name());
                }
                $store_order_details[] = array('id' => $value_post->ID, 'date' =>  $value_post->post_date);
            }
        }
        $store_order_first_id = !empty($store_order_details) && isset($store_order_details[0]) ? $store_order_details[0] : '';
        $store_order_second_id = !empty($store_order_details) && isset($store_order_details[1]) ? $store_order_details[1] : '';

        $store_order_refund_first_id = !empty($store_order_refund_details) && isset($store_order_refund_details[0]) ? $store_order_refund_details[0] : '';
        $store_order_refund_second_id = !empty($store_order_refund_details) && isset($store_order_refund_details[1]) ? $store_order_refund_details[1] : '';

        // transaction details
        $transaction_details = $this->mvx_list_of_pending_transaction()->data ? $this->mvx_list_of_pending_transaction()->data : '';
        $transaction_details1 = $transaction_details && isset($transaction_details[0]) ? $transaction_details[0] : '';
        $transaction_details2 = $transaction_details && isset($transaction_details[1]) ? $transaction_details[1] : '';

        // commission details
        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'meta_key' => '_paid_status',
            'meta_value' => 'paid',
            'posts_per_page' => 5
        );
        $commissions = get_posts($args);
        //mvx-vendor-application-content
        $applcation_data_display = '';
        //$applcation_data_display .= '<h2 class="mvx-text-with-right-side-line-wrapper">' . __("Seller's Latest Activity", 'multivendorx') . '<hr></h2>';
        $applcation_data_display .= '<div class="note-clm-wrap">';

        if ($store_first_id) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> Vendor ' . $store_first_id['vendor'] . ' has added product ' . $store_first_id['title'] . '</p><p class="note_time note-meta">On ' . human_time_diff(strtotime($store_first_id['date'])) . ' </p><p class="note_owner note-meta"></p></div>';
        }
        if ($store_order_first_id) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> New Order placed #' . $store_order_first_id['id'] . '</p><p class="note_time note-meta">On ' . human_time_diff(strtotime($store_order_first_id['date'])) . '</p><p class="note_owner note-meta"></p></div>';
        }

        if ($store_order_refund_first_id) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> Customer '. $store_order_refund_first_id['customer'] .'has requested refund for #' . $store_order_refund_first_id['id'] . '</p><p class="note_time note-meta">On ' . human_time_diff(strtotime($store_order_refund_first_id['date'])) . '</p><p class="note_owner note-meta"></p></div>';
        }

        if ($transaction_details1) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> Vendor '.$transaction_details1['vendor'].' has requested commission withdrawl for order#' . $transaction_details1['order'] . '</p><p class="note_time note-meta">On ' . $transaction_details1['commission'] . '</p><p class="note_owner note-meta"></p></div>';
        }

        if (isset($commissions[0])) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> Commission id #'.$commissions[0]->ID.' is paid</p><p class="note_time note-meta">On ' . human_time_diff(strtotime($commissions[0]->post_date)) . '</p><p class="note_owner note-meta"></p></div>';
        }



        if ($store_second_id) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> Vendor ' . $store_second_id['vendor'] . ' has added product ' . $store_second_id['title'] . '</p><p class="note_time note-meta">On ' . human_time_diff(strtotime($store_second_id['date'])) . ' </p><p class="note_owner note-meta"></p></div>';
        }
        if ($store_order_second_id) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> New Order placed #' . $store_order_second_id['id'] . '</p><p class="note_time note-meta">On ' . human_time_diff(strtotime($store_order_second_id['date'])) . '</p><p class="note_owner note-meta"></p></div>';
        }

        if ($store_order_refund_second_id) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> Customer '. $store_order_refund_second_id['customer'] .'has requested refund for #' . $store_order_refund_second_id['id'] . '</p><p class="note_time note-meta">On ' . human_time_diff(strtotime($store_order_refund_second_id['date'])) . '</p><p class="note_owner note-meta"></p></div>';
        }

        if ($transaction_details2) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> Vendor '.$transaction_details2['vendor'].' has requested commission withdrawl for order#' . $transaction_details2['order'] . '</p><p class="note_time note-meta">On ' . $transaction_details2['commission'] . '</p><p class="note_owner note-meta"></p></div>';
        }

        if (isset($commissions[1])) {
            $applcation_data_display .= '<div class="note-clm"><p class="note-description"> Commission id #'.$commissions[1]->ID.' is paid</p><p class="note_time note-meta">On ' . human_time_diff(strtotime($commissions[1]->post_date)) . '</p><p class="note_owner note-meta"></p></div>';
        }

        $applcation_data_display .= '</div>';
        return $applcation_data_display;
    }

    public function mvx_vendor_pending_shipping() {
        $today = @date('Y-m-d 00:00:00', strtotime("+1 days"));
        $days_range = 7;
        $last_seven_day_date = date('Y-m-d H:i:s', strtotime("-$days_range days"));

        $args = apply_filters('mvx_vendor_pending_shipping_args', array(
            'start_date' => $last_seven_day_date,
            'end_date' => $today
        ));
        $user_list = [];
        $user_query = new WP_User_Query(array('role__in' => array('dc_vendor'), 'orderby' => 'registered', 'order' => 'ASC'));
        $users = $user_query->get_results();
        if ($users) {
            foreach($users as $user_details) {
                $user = get_mvx_vendor($user_details->ID);
                $pending_shippings_orders = $user->get_vendor_orders_reports_of('pending_shipping', $args);
                if ($pending_shippings_orders) {
                    foreach ($pending_shippings_orders as $pending_order) {
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
                        if ($user->is_shipping_enable()) {
                            $is_shipped = (array) get_post_meta($pending_order->get_id(), 'dc_pv_shipped', true);
                            $vendor_order_shipped = get_post_meta($pending_order->get_id(), 'mvx_vendor_order_shipped');
                            if (!in_array($user->id, $is_shipped) && !$vendor_order_shipped ) {
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

                        $user_list[] = array(
                            'vendor_name'   =>  $user->page_title,
                            'order_id'       => '<a href="' . esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_vendor_orders_endpoint', 'seller_dashbaord', 'vendor-orders'), $pending_order->get_id())) . '">#' . $pending_order->get_id() . '</a>',
                            'products_name'            => implode(' , ', $product_name),
                            'order_date'           => mvx_date($pending_order->get_date_created()),
                            'shipping_address'            => $pending_order->get_formatted_shipping_address(),
                            'shipping_amount'       => $shipping_amount,
                        );
                    }
                }
            }
        }
        return rest_ensure_response($user_list);
    }

    public function mvx_vendor_pending_verification_action($request) {
        $action = $request->get_param('action') ? $request->get_param('action') : '';
        $id = $request->get_param('id') ? $request->get_param('id') : '';
        $type = $request->get_param('type') ? $request->get_param('type') : '';
        if(!empty($id)){
            $user_id = (int)$id;
            $verification_settings = get_user_meta($user_id, 'mvx_vendor_verification_settings', true);
            if(!empty($type) && !empty($action)) {
                $v_type = $type;
                if($action == 'rejected'){
                    $verification_settings[$v_type]['is_verified'] = $action;
                    $verification_settings[$v_type]['data'] = '';
                    delete_user_meta($user_id, 'mvx_vendor_is_verified');
                }else{
                    $verification_settings[$v_type]['is_verified'] = $action;
                }
            }
            
            update_user_meta($user_id, 'mvx_vendor_verification_settings', $verification_settings);

            if(isset($verification_settings['address_verification']['is_verified']) && $verification_settings['address_verification']['is_verified'] == 'verified' && isset($verification_settings['id_verification']['is_verified']) && $verification_settings['id_verification']['is_verified'] == 'verified'){
                update_user_meta($user_id, 'mvx_vendor_is_verified', 'verified');

            }else{
                delete_user_meta($user_id, 'mvx_vendor_is_verified');
            }

            // Send email as per admin notified
            $vendor = get_mvx_vendor($user_id) ? get_mvx_vendor($user_id) : '';
            $verification_type = isset($type) ? wc_clean($type) : '';
            if ($verification_type && $verification_type == 'address_verification') {
                $verification_title = __('Address Verification', 'multivendorx');
            } elseif ($verification_type == 'id_verification') {
                $verification_title = __('Identity Verification', 'multivendorx');
            } else {
                $verification_title = __('Verification', 'multivendorx');
            }
            $verifiedemail = WC()->mailer()->emails['MVX_Email_Vendor_Approve_Alert'];
            $rejectemail = WC()->mailer()->emails['MVX_Email_Vendor_Reject_Alert'];

            if (!empty($action) && $action == 'verified') {
                if ($verifiedemail) {
                    $verifiedemail->trigger($vendor, $verification_title);
                }
            } else {
                if ($rejectemail) {
                    $rejectemail->trigger($vendor, $verification_title);
                }
            }
        }
    }

    public function mvx_fetch_pending_verification_data() {
        global $MVX;
        $get_pending_verification_list = $lists = [];
        $args = apply_filters('mvx_vendor_verification_to_do_list_args', array(
            'role' => 'dc_vendor',
            'meta_key' => 'mvx_vendor_verification_settings',
        ));
        
        $get_verification_vendors = new WP_User_Query($args);
        $get_verification_vendors = $get_verification_vendors->get_results();
        $have_pending_verification = array();
        foreach ($get_verification_vendors as $get_vendor) {
            $verification_settings = get_user_meta($get_vendor->ID, 'mvx_vendor_verification_settings', true);
            if (isset($verification_settings['address_verification']['is_verified']) && $verification_settings['address_verification']['is_verified'] == 'pending' || isset($verification_settings['id_verification']['is_verified']) && $verification_settings['id_verification']['is_verified'] == 'pending' ){
                $have_pending_verification[] = 'pending';
            }
        }
        if (in_array('pending', $have_pending_verification) && $get_verification_vendors) {
            foreach ($get_verification_vendors as $get_vendor) {
                $verification_settings = get_user_meta($get_vendor->ID, 'mvx_vendor_verification_settings', true);
                $addrs = array();
                if (isset($verification_settings['address_verification']['data']['address_1']) )
                    $addrs['address_1'] = $verification_settings['address_verification']['data']['address_1'];
                if (isset($verification_settings['address_verification']['data']['address_2']) )
                    $addrs['address_2'] = $verification_settings['address_verification']['data']['address_2'];
                if (isset($verification_settings['address_verification']['data']['country']) )
                    $addrs['country'] = $verification_settings['address_verification']['data']['country'];
                if (isset($verification_settings['address_verification']['data']['state']) )
                    $addrs['state'] = $verification_settings['address_verification']['data']['state'];
                if (isset($verification_settings['address_verification']['data']['city']) )
                    $addrs['city'] = $verification_settings['address_verification']['data']['city'];
                if (isset($verification_settings['address_verification']['data']['postcode']) )
                    $addrs['postcode'] = $verification_settings['address_verification']['data']['postcode'];

                $id_type = $file_display = $file_display_social = '';
                if (isset($verification_settings['id_verification']['data']['verification_type']) )
                    $id_type = $verification_settings['id_verification']['data']['verification_type'];


                if (isset($verification_settings['id_verification']['data']['verification_file']) && !empty($verification_settings['id_verification']['data']['verification_file'])){
                    $file_type = wp_check_filetype($verification_settings['id_verification']['data']['verification_file']);
                    $img_type = array('image/jpeg', 'image/png', 'image/gif');
                    if (isset($file_type['type']) && in_array($file_type['type'], $img_type)){
                        $file_display = '<br><img height="100px" src="'.esc_url($verification_settings['id_verification']['data']['verification_file']).'" />';
                    } else {
                        $file_display = '<br><img height="100px" src="'.$WCMP_Vendor_Verification->plugin_url . 'assets/images/document.png'.'" />';
                    }
                }

                if (isset($verification_settings['social_verification'])){
                    foreach($verification_settings['social_verification'] as $provider => $profile) {
                    $file_display_social = '<img height="35px" src="'. $WCMP_Vendor_Verification->plugin_url . 'assets/images/'.strtolower($provider).'.png'.'" style="margin-right:4px;"/>';
                    }
                }

                $lists[] = array(
                    'id'    =>  $get_vendor->ID,
                    'image' =>  '<img src='.$MVX->plugin_url . 'assets/images/wp-avatar-frau.jpg'.' alt="" class="avatar avatar-32 photo" height="32" width="32"></img>' . $get_vendor->data->display_name,
                    'address_verified'  => isset($verification_settings['address_verification']['is_verified']) && $verification_settings['address_verification']['is_verified'] == 'verified' ? true : false,
                    'id_verified'  => isset($verification_settings['id_verification']['is_verified']) && $verification_settings['id_verification']['is_verified'] == 'verified' ? true : false,
                    'address'   =>  WC()->countries->get_formatted_address($addrs),
                    'id_verification'   =>  __('ID Type : ', 'multivendorx').ucwords($id_type) . $file_display,
                    'social'    =>  $file_display_social ? $file_display_social : __('No social data found', 'multivendorx')
                );
            }
        }
        return rest_ensure_response($lists);
    }

    public function mvx_list_of_work_board_content() {
        $todo_list = $get_pending_product_list = $get_pending_vendor_list = $get_pending_coupon_list = $get_pending_transaction_list = $get_pending_question_list = $get_request_profile_deletion_vendor_list = [];
        /**
         * pending product list
         */
        if ($this->mvx_list_of_pending_vendor_product()->data) {
            foreach ($this->mvx_list_of_pending_vendor_product()->data as $key => $value) {
                $get_pending_product_list[] = array(
                    'list_datas'    =>  array(
                        array(
                            'label' =>  __('Vendor Name', 'multivendorx'),
                            'value' =>  $value['vendor']
                        ),
                        array(
                            'label' =>  __('Product Name', 'multivendorx'),
                            'value' =>  $value['product']
                        )
                    ),
                    'left_icons'    =>  array(
                        array(
                            'key'   =>  'edit',
                            'link'  =>  $value['product_url'],
                            'title' =>  __('Edit', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'approve_product',
                            'value'  =>  $value,
                            'icon'  =>  'icon-approve',
                            'title' =>  __('Approve', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'dismiss_product',
                            'value'  =>  $value,
                            'icon'  =>  'icon-dismiss',
                            'title' =>  __('Dismiss', 'multivendorx')
                        )
                    )
                );
            }
        }

        /**
         * pending vendor list
         */
        if ($this->mvx_list_of_pending_vendor()->data) {
            foreach ($this->mvx_list_of_pending_vendor()->data as $key => $value) {
                $get_pending_vendor_list[] = array(
                    'list_datas'    => apply_filters('mvx_pending_vendor_todo_content', array(
                        array(
                            'label' =>  __('Vendor Name', 'multivendorx'),
                            'value' =>  $value['vendor_name']
                        )
                    ), $value),
                    'left_icons'    =>  array(
                        array(
                            'key'   =>  'edit',
                            'link'  =>  $value['vendor_link'],
                            'title' =>  __('Edit', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'approve_vendor',
                            'value'  =>  $value,
                            'icon'  =>  'icon-approve',
                            'title' =>  __('Approve', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'dismiss_vendor',
                            'value'  =>  $value,
                            'icon'  =>  'icon-dismiss',
                            'title' =>  __('Dismiss', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'reject_vendor',
                            'value'  =>  $value,
                            'icon'  =>  'icon-no',
                            'title' =>  __('Reject', 'multivendorx')
                        )
                    )
                );
            }
        }

        /**
         * pending coupon list
         */
        if ($this->mvx_list_of_pending_vendor_coupon()->data) {
            foreach ($this->mvx_list_of_pending_vendor_coupon()->data as $key => $value) {
                $get_pending_coupon_list[] = array(
                    'list_datas'    =>  array(
                        array(
                            'label' =>  __('Vendor Name', 'multivendorx'),
                            'value' =>  $value['vendor']
                        ),
                        array(
                            'label' =>  __('Coupon Name', 'multivendorx'),
                            'value' =>  $value['coupon']
                        )
                    ),
                    'left_icons'    =>  array(
                        array(
                            'key'   =>  'edit',
                            'link'  =>  $value['coupon_url'],
                            'title' =>  __('Edit', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'approve_coupon',
                            'value'  =>  $value,
                            'icon'  =>  'icon-approve',
                            'title' =>  __('Approve', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'dismiss_coupon',
                            'value'  =>  $value,
                            'icon'  =>  'icon-dismiss',
                            'title' =>  __('Dismiss', 'multivendorx')
                        )
                    )
                );
            }
        }

        /**
         * pending transaction list
         */
        if ($this->mvx_list_of_pending_transaction()->data) {
            foreach ($this->mvx_list_of_pending_transaction()->data as $key => $value) {
                $get_pending_transaction_list[] = array(
                    'list_datas'    =>  array(
                        array(
                            'label' =>  __('Vendor Name', 'multivendorx'),
                            'value' =>  $value['vendor']
                        ),
                        array(
                            'label' =>  __('Amount', 'multivendorx'),
                            'value' =>  $value['amount']
                        ),
                        array(
                            'label' =>  __('Account Detail', 'multivendorx'),
                            'value' =>  $value['account_details']
                        )
                    ),
                    'left_icons'    =>  array(
                        array(
                            'key'   =>  'approve_transaction',
                            'value'  =>  $value,
                            'icon'  =>  'icon-approve',
                            'title' =>  __('Approve', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'dismiss_transaction',
                            'value'  =>  $value,
                            'icon'  =>  'icon-dismiss',
                            'title' =>  __('Dismiss', 'multivendorx')
                        )
                    )
                );
            }
        }

        /**
         * pending question list
         */
        if ($this->mvx_list_of_pending_question('', '')->data) {
            foreach ($this->mvx_list_of_pending_question('', '')->data as $key => $value) {
                $get_pending_question_list[] = array(
                    'list_datas'    =>  array(
                        array(
                            'label' =>  __('Question by', 'multivendorx'),
                            'value' =>  $value['question_by']
                        ),
                        array(
                            'label' =>  __('Product Name', 'multivendorx'),
                            'value' =>  $value['product_name']
                        ),
                        array(
                            'label' =>  __('Question details', 'multivendorx'),
                            'value' =>  $value['question_details']
                        )
                    ),
                    'left_icons'    =>  array(
                        array(
                            'key'   =>  'approve_question',
                            'value'  =>  $value,
                            'icon'  =>  'icon-approve',
                            'title' =>  __('Approve', 'multivendorx')
                        ),
                        array(
                            'key'   =>  'dismiss_question',
                            'value'  =>  $value,
                            'icon'  =>  'icon-dismiss',
                            'title' =>  __('Dismiss', 'multivendorx')
                        )
                    )
                );
            }
        }

        /**
         * pending vendor deactivation request list
         */
        if (method_exists($this, 'mvx_list_of_request_profile_deletion_vendor') && is_array($this->mvx_list_of_request_profile_deletion_vendor()->data) && !empty($this->mvx_list_of_request_profile_deletion_vendor()->data)) {
            foreach ($this->mvx_list_of_request_profile_deletion_vendor()->data as $key => $value) {
                if (!empty($value)) {
                    $get_request_profile_deletion_vendor_list[] = array(
                        'list_datas'    => array(
                            array(
                                'label' =>  __('Vendor Name', 'multivendorx'),
                                'value' =>  $value['vendor_name']
                            ),
                            array(
                                'label' =>  __('Reason', 'multivendorx'),
                                'value' =>  $value['deactivate_reason']
                            )
                        ), 
                        'left_icons'    =>  array(
                            array(
                                'key'   =>  'deactivate_vendor',
                                'value'  =>  $value,
                                'icon'  =>  'icon-approve',
                                'title' =>  __('Accept', 'multivendorx')
                            ),
                            array(
                                'key'   =>  'reject_request_profile_deletion_vendor',
                                'value'  =>  $value,
                                'icon'  =>  'icon-no',
                                'title' =>  __('Reject', 'multivendorx')
                            )
                        )
                    );
                }
            }
        }

        $todo_list = apply_filters('mvx_task_board_listing_modules', [
            array(
                'key'       =>  'pending_product',
                'header'    =>  __('Pending Product', 'multivendorx'),
                'content'   =>  $get_pending_product_list
            ),
            array(
                'key'       =>  'pending_vendor',
                'header'    =>  __('Pending Vendor', 'multivendorx'),
                'content'   =>  $get_pending_vendor_list
            ),
            array(
                'key'       =>  'pending_coupon',
                'header'    =>  __('Pending Coupon', 'multivendorx'),
                'content'   =>  $get_pending_coupon_list
            ),
            array(
                'key'       =>  'pending_transaction',
                'header'    =>  __('Pending Transaction', 'multivendorx'),
                'content'   =>  $get_pending_transaction_list
            ),
            array(
                'key'       =>  'pending_question',
                'header'    =>  __('Pending Question', 'multivendorx'),
                'content'   =>  $get_pending_question_list
            ),
        ]);
        
        if (get_mvx_vendor_settings('vendor_deactivation_enabled')) {
            $todo_list[] = array(
                'key'       =>  'requested_profile_deletion',
                'header'    =>  __('Requested Profile Deletion', 'multivendorx'),
                'content'   =>  $get_request_profile_deletion_vendor_list
            );
        }
        return rest_ensure_response($todo_list);
    }

    public function mvx_task_board_icons_triggers($request) {
        global $MVX;
        require_once(ABSPATH.'wp-admin/includes/user.php');
        $value = $request->get_param('value') ? $request->get_param('value') : '';
        $key = $request->get_param('key') ? $request->get_param('key') : '';
        $reject_word = $request->get_param('reject_word') ? $request->get_param('reject_word') : '';
        if ($key == 'dismiss_product') {
            $product_id = $value['id'];
            $vendor_id = $value['vendor_id'];
            $post = get_post($product_id);
            $reason = $reject_word;
            $vendor = get_mvx_vendor($vendor_id);
            $email_vendor = WC()->mailer()->emails['WC_Email_Vendor_Product_Rejected'];
            $email_vendor->trigger($product_id, $post, $vendor, $reason);
            update_post_meta($product_id, '_dismiss_to_do_list', 'true');
            $comment_id = MVX_Product::add_product_note($product_id, $reason, get_current_user_id());
            update_post_meta($product_id, '_comment_dismiss', absint($comment_id));
            add_comment_meta($comment_id, '_author_id', get_current_user_id());
        } elseif ($key == 'approve_product') {
            $product_id = $value['id'];
            $post_update = array(
                'ID'            => $product_id,
                'post_status'   => 'publish',
            );
            wp_update_post( $post_update );
        } elseif ($key == 'approve_vendor') {
            $vendor_id = $value['id'];
            if ($vendor_id) {
                $user = new WP_User(absint($vendor_id));
                $user->set_role('dc_vendor');
                $user_dtl = get_userdata(absint($vendor_id));
                $email = WC()->mailer()->emails['WC_Email_Approved_New_Vendor_Account'];
                $email->trigger($vendor_id, $user_dtl->user_pass);
            }
        } elseif ($key == 'dismiss_vendor') {
            $vendor_id = $value['id'];
            update_user_meta($vendor_id, '_dismiss_to_do_list', 'true');
        } elseif ($key == 'reject_vendor') {
            $user = new WP_User(absint($value['id']));
            $user->set_role('dc_rejected_vendor');
        } elseif ($key == 'approve_coupon') {
            $coupon_id = $value['id'];
            $post_update = array(
                'ID'            => $coupon_id,
                'post_status'   => 'publish',
            );
            wp_update_post( $post_update );
        } elseif ($key == 'dismiss_coupon') {
            $coupon_id = $value['id'];
            update_post_meta($coupon_id, '_dismiss_to_do_list', 'true');
        } elseif ($key == 'approve_transaction') {
            $transaction_id = $value['id'];
            $vendor_id = $value['vendor_id'];
            $vendor = get_mvx_vendor_by_term( $vendor_id );
            update_post_meta($transaction_id, 'paid_date', date("Y-m-d H:i:s"));
            $commission_detail = get_post_meta($transaction_id, 'commission_detail', true);
            if ($commission_detail && is_array($commission_detail)) {
                foreach ($commission_detail as $commission_id) {
                    mvx_paid_commission_status($commission_id);
                    $withdrawal_total = MVX_Commission::commission_totals($commission_id, 'edit');
                    $order_id = get_post_meta( $commission_id, '_commission_order_id', true );
                    $args = array(
                        'meta_query' => array(
                            array(
                                'key' => '_commission_vendor',
                                'value' => absint($vendor->term_id),
                                'compare' => '='
                            ),
                        ),
                    );
                    $unpaid_commission_total = MVX_Commission::get_commissions_total_data( $args, $vendor->id );
                    $data = array(
                        'vendor_id'     => $vendor->id,
                        'order_id'      => $order_id,
                        'ref_id'        => $transaction_id,
                        'ref_type'      => 'withdrawal',
                        'ref_info'      => sprintf(__('Withdrawal generated for Commission &ndash; #%s', 'multivendorx'), $commission_id),
                        'ref_status'    => 'completed',
                        'ref_updated'   => date('Y-m-d H:i:s', current_time('timestamp')),
                        'debit'         => $withdrawal_total,
                        'balance'       => $unpaid_commission_total['total'],
                    );
                    $data_store = $MVX->ledger->load_ledger_data_store();
                    $ledger_id = $data_store->create($data);
                }
                $email_admin = WC()->mailer()->emails['WC_Email_Vendor_Commission_Transactions'];
                $email_admin->trigger($transaction_id, $vendor_id);
                update_post_meta($transaction_id, '_dismiss_to_do_list', 'true');
                wp_update_post(array('ID' => $transaction_id, 'post_status' => 'mvx_completed'));
                do_action( 'mvx_todo_done_pending_transaction', $transaction_id, $vendor );
            }
        } elseif ($key == 'dismiss_transaction') {
            $transaction_id = $value['id'];
            $vendor_id = $value['vendor_id'];
            update_post_meta($transaction_id, '_dismiss_to_do_list', 'true');
            wp_update_post(array('ID' => $transaction_id, 'post_status' => 'mvx_canceled'));
        } elseif ($key == 'approve_question') {
            $product_id = $value['question_product_id'];
            $question_id = $value['id'];
            $data = array();
            if (!empty($product_id)) {
                $vendor = get_mvx_product_vendors(absint($product_id));
                $data['status'] = 'verified';
                $MVX->product_qna->updateQuestion( $question_id, $data );
                $questions = $MVX->product_qna->get_Vendor_Questions($vendor);
                set_transient('mvx_customer_qna_for_vendor_' . $vendor->id, $questions);
            }
        } elseif ($key == 'dismiss_question') {
            $product_id = $value['question_product_id'];
            $question_id = $value['id'];
            if (!empty($product_id)) {
                $vendor = get_mvx_product_vendors(absint($product_id));
                $MVX->product_qna->deleteQuestion( $question_id );
                delete_transient('mvx_customer_qna_for_vendor_' . $vendor->id);
            }
        } elseif (!empty($key) && $key == 'product_draft') {
            if (!empty($value) && is_array($value)) {
                foreach ($value as $vendor_id) {
                    if ($vendor_id) {
                        $args = array( 'posts_per_page' => -1, 'author__in' => $vendor_id, 'post_type' => 'product', 'post_status' => 'publish' );
                        $vendor_query = get_posts($args);
                        if ($vendor_query) {
                            foreach ($vendor_query as $product_id){
                                $update_post_status = array( 'ID' => $product_id->ID, 'post_status' => 'draft' );
                                wp_update_post($update_post_status);
                            }
                        }
                        $email = isset(WC()->mailer()->emails['WC_Email_Vendor_Account_Deletion_Mail']) ? WC()->mailer()->emails['WC_Email_Vendor_Account_Deletion_Mail'] : '';
                        if (!empty($email) && $email->is_enabled() && $vendor_id) {
                            $email->trigger($vendor_id);
                        }
                        wp_delete_user($vendor_id);
                    }
                }
            } elseif (!empty($value)) {
                $args = array( 'posts_per_page' => -1, 'author__in' => $value, 'post_type' => 'product', 'post_status' => 'publish' );
                $vendor_query = get_posts($args);
                if ($vendor_query) {
                    foreach ($vendor_query as $product_id){
                        if ($product_id) {
                            $update_post_status = array( 'ID' => $product_id->ID, 'post_status' => 'draft' );
                            wp_update_post($update_post_status);
                        }
                    }
                }
                $email = isset(WC()->mailer()->emails['WC_Email_Vendor_Account_Deletion_Mail']) ? WC()->mailer()->emails['WC_Email_Vendor_Account_Deletion_Mail'] : '';
                if (!empty($email) && $email->is_enabled() && $value) {
                    $email->trigger($value);
                }
                wp_delete_user($value);
            }
        } elseif (!empty($key) && $key == 'product_assigned_admin') {
            $users = get_users(array( 'role__in' => array('administrator')));
            if (!empty($value) && is_array($value)) {
                foreach ($value as $vendor_id) {
                    if ($vendor_id) {
                        $args = array( 'posts_per_page' => -1, 'author__in' => $vendor_id, 'post_type' => 'product', 'post_status' => 'publish' );
                        $vendor_query = get_posts($args);
                        if ($vendor_query) {
                            foreach ($vendor_query as $product_id){
                                if (!empty($users) && !empty($product_id->ID)) {
                                    $update_post_author = array( 'ID' => $product_id->ID, 'post_author' => $users[0]->ID );
                                    wp_update_post($update_post_author);
                                }
                            }
                        }
                        $email = isset(WC()->mailer()->emails['WC_Email_Vendor_Account_Deletion_Mail']) ? WC()->mailer()->emails['WC_Email_Vendor_Account_Deletion_Mail'] : '';
                        if (!empty($email) && $email->is_enabled() && $vendor_id) {
                            $email->trigger($vendor_id);
                        }
                        wp_delete_user($vendor_id);
                    }
                }
            } elseif (!empty($value)) {
                $args = array( 'posts_per_page' => -1, 'author__in' => $value, 'post_type' => 'product', 'post_status' => 'publish');
                $vendor_query = get_posts($args);
                if ($vendor_query) {
                    foreach ($vendor_query as $product_id){
                        if (!empty($users) && !empty($product_id)) {
                            $update_post_author = array( 'ID' => $product_id->ID, 'post_author' => $users[0]->ID );
                            wp_update_post($update_post_author);
                        }
                    }
                }
                $email = isset(WC()->mailer()->emails['WC_Email_Vendor_Account_Deletion_Mail']) ? WC()->mailer()->emails['WC_Email_Vendor_Account_Deletion_Mail'] : '';
                if (!empty($email) && $email->is_enabled() && $value) {
                    $email->trigger($value);
                }
                wp_delete_user($value);
            }
        } elseif (!empty($key) && $key == 'reject_request_profile_deletion_vendor') {
            $vendor_id = $value['id'] ? $value['id'] : ''; 
            if (!empty($vendor_id) && metadata_exists('user', $vendor_id, '_deactivate_reason')) {
                delete_user_meta($vendor_id, '_deactivate_reason');
                $email = isset(WC()->mailer()->emails['WC_Email_Vendor_Account_Deactive_Request_Reject_Mail']) ? WC()->mailer()->emails['WC_Email_Vendor_Account_Deactive_Request_Reject_Mail'] : '';
                if (!empty($email) && $email->is_enabled()) {
                    $email->trigger($vendor_id);
                }
            }           
        } 
        do_action('mvx_task_board_icons_triggers_action', $key, $value);
        return $this->mvx_list_of_work_board_content();
    }

    public function mvx_update_vendor_store($request) {
        $vendor_id = $request->get_param('vendor_id') ? absint( $request->get_param('vendor_id') ) : 0;
        $lat = $request->get_param('lat') ? $request->get_param('lat') : 0;
        $lng = $request->get_param('lng') ? $request->get_param('lng') : 0;
        $places = $request->get_param('places') ? $request->get_param('places') : '';
        mvx_update_user_meta($vendor_id, '_store_location', wc_clean($places));
        mvx_update_user_meta($vendor_id, '_store_lat', wc_clean($lat));
        mvx_update_user_meta($vendor_id, '_store_lng', wc_clean($lng));
    }

    public function mvx_list_of_all_tabs() {
        return rest_ensure_response(mvx_admin_backend_tab_settings());
    }

    public function mvx_list_of_bulk_change_status_question($request) {
        global $MVX;
        $value = $request && $request->get_param('value') ? $request->get_param('value') : '';
        $product_ids = $request && $request->get_param('product_ids') ? $request->get_param('product_ids') : array();
        $pending_list = [];
        if ($value == 'all') {
            $vendor_questions_n_answers = $MVX->product_qna->get_All_Vendor_Questions(false);
        } else {
            $vendor_questions_n_answers = $MVX->product_qna->get_All_Vendor_Questions(true);
        }
        
        // filter by products
        if ($product_ids && is_array($product_ids)) {
            $qna_products = wp_list_pluck($product_ids, 'value');
            if ($vendor_questions_n_answers) {
                foreach ($vendor_questions_n_answers as $key => $qna_ques) {
                    if (!in_array($qna_ques->product_ID, $qna_products)) {
                        unset($vendor_questions_n_answers[$key]);
                    }
                }
            }
        }

        if (!empty($vendor_questions_n_answers)) {
            foreach ($vendor_questions_n_answers as $pending_question) {
                $question_by_details = get_userdata($pending_question->ques_by);
                $question_by = "<img src=' " . $MVX->plugin_url . 'assets/images/wp-avatar-frau.jpg' ."' class='avatar avatar-32 photo' height='32' width='32'>" .$question_by_details->data->display_name . "";
                $pending_list[] = array(
                    'id'                    =>  $pending_question->ques_ID,
                    'question_by'           =>  $question_by,
                    'question_product_id'   =>  $pending_question->product_ID,
                    'question_by_name'      =>  $question_by_details->data->display_name,
                    'product_name'          =>  get_the_title($pending_question->product_ID),
                    'question_details'      =>  $pending_question->ques_details,
                    'question_date'         =>  human_time_diff(strtotime($pending_question->ques_created)),
                    'question_status'       =>  $pending_question->status,
                    'product_url'           =>  admin_url('post.php?post=' . $pending_question->product_ID . '&action=edit'),
                );
            }   
        }

        return rest_ensure_response($pending_list);
    }

    public function mvx_approve_dismiss_pending_transaction($request) {
        global $MVX;
        $transaction_id = $request && $request->get_param('transaction_id') ? absint($request->get_param('transaction_id')) : 0;
        $vendor_id = $request && $request->get_param('vendor_id') ? absint($request->get_param('vendor_id')) : 0;
        $status = $request && $request->get_param('status') ? $request->get_param('status') : '';
        if ($status == 'dismiss') {
            update_post_meta($transaction_id, '_dismiss_to_do_list', 'true');
            wp_update_post(array('ID' => $transaction_id, 'post_status' => 'mvx_canceled'));
        } else {
            $vendor = get_mvx_vendor_by_term( $vendor_id );
            update_post_meta($transaction_id, 'paid_date', date("Y-m-d H:i:s"));
            $commission_detail = get_post_meta($transaction_id, 'commission_detail', true);
            if ($commission_detail && is_array($commission_detail)) {
                foreach ($commission_detail as $commission_id) {
                    mvx_paid_commission_status($commission_id);
                    $withdrawal_total = MVX_Commission::commission_totals($commission_id, 'edit');
                    $order_id = get_post_meta( $commission_id, '_commission_order_id', true );
                    $args = array(
                        'meta_query' => array(
                            array(
                                'key' => '_commission_vendor',
                                'value' => absint($vendor->term_id),
                                'compare' => '='
                            ),
                        ),
                    );
                    $unpaid_commission_total = MVX_Commission::get_commissions_total_data( $args, $vendor->id );
                    $data = array(
                        'vendor_id'     => $vendor->id,
                        'order_id'      => $order_id,
                        'ref_id'        => $transaction_id,
                        'ref_type'      => 'withdrawal',
                        'ref_info'      => sprintf(__('Withdrawal generated for Commission &ndash; #%s', 'multivendorx'), $commission_id),
                        'ref_status'    => 'completed',
                        'ref_updated'   => date('Y-m-d H:i:s', current_time('timestamp')),
                        'debit'         => $withdrawal_total,
                        'balance'       => $unpaid_commission_total['total'],
                    );
                    $data_store = $MVX->ledger->load_ledger_data_store();
                    $ledger_id = $data_store->create($data);
                }
                $email_admin = WC()->mailer()->emails['WC_Email_Vendor_Commission_Transactions'];
                $email_admin->trigger($transaction_id, $vendor_id);
                update_post_meta($transaction_id, '_dismiss_to_do_list', 'true');
                wp_update_post(array('ID' => $transaction_id, 'post_status' => 'mvx_completed'));
                do_action( 'mvx_todo_done_pending_transaction', $transaction_id, $vendor );
            }
        }
        return $this->mvx_list_of_pending_transaction();    
    }

    public function mvx_list_vendor_roles_data($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? absint($request->get_param('vendor_id')) : 0;
        $user = get_user_by("ID", $vendor_id);
        $set_user_role = '';
        if (in_array('dc_rejected_vendor', $user->roles)) {
            $set_user_role = 'reject_vendor';
        } else if (in_array('dc_pending_vendor', $user->roles)) {
            $set_user_role = 'pending_vendor';
        }
        return $set_user_role;
    }

    public function mvx_list_vendor_application_data($request) {
        global $MVX;
        $vendor_id = $request && $request->get_param('vendor_id') ? absint($request->get_param('vendor_id')) : 0;
        $applcation_data_display = $rejection_data_display = '';

        $vendor_application_data = get_user_meta(absint($vendor_id), 'mvx_vendor_fields', true);
        
        $applcation_data_display .= '<h2 class="mvx-text-with-right-side-line-wrapper">' . __('Vendor Application Data', 'multivendorx') . '<hr></h2>';
        if (!empty($vendor_application_data) && is_array($vendor_application_data)) {
            foreach ($vendor_application_data as $key => $value) {
                if ($value['type'] == 'recaptcha') continue;
                $applcation_data_display .= '<div class="mvx-form-field">';
                $applcation_data_display .= '<label>' . html_entity_decode($value['label']) . ':</label>';
                if ($value['type'] == 'file') {
                    if (!empty($value['value']) && is_array($value['value'])) {
                        foreach ($value['value'] as $attacment_id) {
                            $applcation_data_display .= '<span> <a href="' . wp_get_attachment_url($attacment_id) . '" download>' . get_the_title($attacment_id) . '</a> </span>';
                        }
                    }
                } else if ($value['type'] == 'checkbox') {
                    if (!empty($value['value']) && $value['value'] == 'on') {
                            $applcation_data_display .= '<span> <input type="checkbox" name="" checked disabled /></span>';
                    } else {
                        $applcation_data_display .= '<span> <input type="checkbox" name="" disabled /></span>';
                    }
                } else if ($value['type'] == 'vendor_country') {
                                                            $country_code = $value['value'];
                                                            $country_data = WC()->countries->get_countries();
                                                            $country_name = ( isset( $country_data[ $country_code ] ) ) ? $country_data[ $country_code ] : $country_code; //To get country name by code
                    $applcation_data_display .= '<span> ' . $country_name . '</span>';
                } else if ($value['type'] == 'vendor_state') {
                                                            $country_field_key = array_search('vendor_country', array_column($vendor_application_data, 'type'));
                                                            $country_field = $vendor_application_data[$country_field_key];
                                                            $country_code = $country_field['value'];
                                                            $state_code = $value['value'];
                                                            $state_data = WC()->countries->get_states($country_code);
                                                            $state_name = ( isset( $state_data[$state_code] ) ) ? $state_data[$state_code] : $state_code; //to get State name by state code
                    $applcation_data_display .= '<span> ' . $state_name . '</span>';
                } else {
                    if (is_array($value['value'])) {
                        $applcation_data_display .= '<span> ' . implode(', ', $value['value']) . '</span>';
                    } else {
                        $applcation_data_display .= '<span> ' . $value['value'] . '</span>';
                    }
                }
                $applcation_data_display .= '</div>';
            }
        } else {
            $applcation_data_display .= '<div class="mvx-no-form-data">' . __('No Vendor Application archive data!!', 'multivendorx') . '</div>';
        }

        $mvx_vendor_rejection_notes = unserialize( get_user_meta( absint($vendor_id), 'mvx_vendor_rejection_notes', true ) );
        if(is_array($mvx_vendor_rejection_notes) && count($mvx_vendor_rejection_notes) > 0) {
            $applcation_data_display .= '<h2 class="mvx-text-with-right-side-line-wrapper">' . __('Notes', 'multivendorx') . '<hr></h2>';
            $applcation_data_display .= '<div class="note-clm-wrap">';
            foreach($mvx_vendor_rejection_notes as $time => $notes) {
                $author_info = get_userdata($notes['note_by']);
                $applcation_data_display .= '<div class="note-clm"><p class="note-description">' . $notes['note'] . '</p><p class="note_time note-meta">On ' . date( "Y-m-d", $time ) . '</p><p class="note_owner note-meta">By ' . $author_info->display_name . '</p></div>';
            }
            $applcation_data_display .= '</div>';
        }

        return $applcation_data_display;
    }

    public function mvx_list_vendor_application_raw_data($request) {
        global $MVX;
        $vendor_id = $request && $request->get_param('vendor_id') ? absint($request->get_param('vendor_id')) : 0;
        $applcation_data_display = $applcation_data_label = '';
        $applcation_data_array = [];

        $vendor_application_data = get_user_meta(absint($vendor_id), 'mvx_vendor_fields', true);
    
        if (!empty($vendor_application_data) && is_array($vendor_application_data)) {
            
            foreach ($vendor_application_data as $key => $value) {
                if ($value['type'] == 'recaptcha') continue;

                $applcation_data_label =  html_entity_decode($value['label']) . ' : ' ;
                if ($value['type'] == 'file') {
                    if (!empty($value['value']) && is_array($value['value'])) {
                        foreach ($value['value'] as $attacment_id) {
                            $applcation_data_display =  wp_get_attachment_url($attacment_id);
                        }
                    }
                }  else if ($value['type'] == 'vendor_country') {
                    $country_code = $value['value'];
                    $country_data = WC()->countries->get_countries();
                    $country_name = ( isset( $country_data[ $country_code ] ) ) ? $country_data[ $country_code ] : $country_code; //To get country name by code
                    $applcation_data_display = $country_name;
                } else if ($value['type'] == 'vendor_state') {
                    $country_field_key = array_search('vendor_country', array_column($vendor_application_data, 'type'));
                    $country_field = $vendor_application_data[$country_field_key];
                    $country_code = $country_field['value'];
                    $state_code = $value['value'];
                    $state_data = WC()->countries->get_states($country_code);
                    $state_name = ( isset( $state_data[$state_code] ) ) ? $state_data[$state_code] : $state_code; //to get State name by state code
                    $applcation_data_display = $state_name;
                } else {
                    if (is_array($value['value'])) {
                        $applcation_data_display = implode(', ', $value['value']);
                    } else {
                        $applcation_data_display = $value['value'];
                    }
                }
                $applcation_data_array[] = array(
                    'Fields' => $applcation_data_label,
                    'Data' => $applcation_data_display,
                );
            }
        }
        return $applcation_data_array;
    }

    public function mvx_active_suspend_vendor($request) {

        $status = $request && $request->get_param('status') ? $request->get_param('status') : '';
        $user_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : '';
        $section = $request && $request->get_param('section') ? $request->get_param('section') : '';
        $user = new WP_User(absint($user_id));
        if ($status == 'activate') {
            if (is_user_mvx_vendor($user)) {
                delete_user_meta($user_id, '_vendor_turn_off');
            }
        } else {
            if (is_user_mvx_vendor($user)) {
                update_user_meta($user_id, '_vendor_turn_off', 'Enable');
                if ( apply_filters( 'mvx_suspend_vendor_email_sent' , true ) ) {
                    $email_vendor_suspend = WC()->mailer()->emails['WC_Email_Suspend_Vendor_Account'];
                    $email_vendor_suspend->trigger($user_id);
                }
            }
        }

        if ($section) {
            return $this->mvx_list_all_vendor('');
        }
    }

    public function mvx_get_as_per_module_status($request) {
        $module_status = $request && $request->get_param('module_status') ? $request->get_param('module_status') : '';
        $module_count = $request && $request->get_param('count') ? $request->get_param('count') : '';
        $active_modules = mvx_get_option('mvx_all_active_module_list', true) ? mvx_get_option('mvx_all_active_module_list', true) : array();

        $all_module_lists = mvx_list_all_modules();
        if ($module_status == 'all') return rest_ensure_response(array_values($all_module_lists));

        $get_searchable_data = $set_2nd_search_item_data = $set_3rd_search_item_data = [];
        foreach ($all_module_lists as $key_parent => $value_parent) {
            foreach ($value_parent['options'] as $key_child => $value_child) {

                if ($module_status == 'active') {
                    if (in_array($value_child['id'], $active_modules)) {
                        $get_searchable_data[] = array('id' => $value_child['id'], 'label' => $value_parent['label'], 'parent_key' => $key_parent, 'child_option_key' => $key_child);
                    }
                } else {
                    if (!in_array($value_child['id'], $active_modules)) {
                        $get_searchable_data[] = array('id' => $value_child['id'], 'label' => $value_parent['label'], 'parent_key' => $key_parent, 'child_option_key' => $key_child);
                    }
                }
            }
        }
        if ($module_count) {
            return rest_ensure_response( count($get_searchable_data) );
        }
        foreach ($get_searchable_data as $key1 => $value1) {
            $list_include = wp_list_pluck($set_2nd_search_item_data, 'label');
            if (in_array($value1['label'], $list_include)) {
                $fdsgdsgsdgsd[] = $all_module_lists[$value1['parent_key']]['options'][$value1['child_option_key']];
            } else {
                $fdsgdsgsdgsd = array($all_module_lists[$value1['parent_key']]['options'][$value1['child_option_key']]);
            }
            $set_2nd_search_item_data[] = array(
                'label' => $value1['label'],
                'options' => $fdsgdsgsdgsd
            );
        }

        foreach ($set_2nd_search_item_data as $keyi => $valuei) {
            $cut_arrays = array_slice($set_2nd_search_item_data, $keyi + 1);
            $listing_search_valus = wp_list_pluck($cut_arrays, 'label');
            if (in_array($valuei['label'], $listing_search_valus)) {
                $set_3rd_search_item_data[] = $keyi;
            }
        }

        foreach ($set_3rd_search_item_data as $key => $value) {
            unset($set_2nd_search_item_data[$value]);
        }

        $response = array_values($set_2nd_search_item_data);
        
        return rest_ensure_response( $response );
    }

    public function mvx_modules_count() {
        $add_modules_details = mvx_list_all_modules();
        $total_number_of_modules = [];
        foreach ($add_modules_details as $key_parent => $value_parent) {
            $total_number_of_modules[] = count($value_parent['options']);
        }
        return rest_ensure_response(array_sum($total_number_of_modules));
    }

    public function mvx_search_module_lists($request) {
        $category = $request && $request->get_param('category') ? $request->get_param('category') : '';
        $new_array_data = [];
        $modules_details = mvx_list_all_modules();
        foreach ($modules_details as $key => $value) {
            if ($value['label'] == $category) {
                $new_array_data[] = $modules_details[$key];
            }
        }
        $response = array_values($new_array_data);
        return rest_ensure_response( $response );
    }

    public function mvx_fetch_all_modules_data() {
        // get all settings fileds
        $settings_fields = mvx_admin_backend_settings_fields_details();
        if (!empty($settings_fields)) {
            foreach ($settings_fields as $settings_key => $settings_value) {
                foreach ($settings_value as $inter_key => $inter_value) {
                    $change_settings_key    =   str_replace("-", "_", $settings_key);
                    $option_name = 'mvx_'.$change_settings_key.'_tab_settings';
                    $database_value = mvx_get_option($option_name) ? mvx_get_option($option_name) : array();
                    if (!empty($database_value)) {
                        if (isset($inter_value['key']) && array_key_exists($inter_value['key'], $database_value)) {
                            if (empty($settings_fields[$settings_key][$inter_key]['database_value'])) {
                               $settings_fields[$settings_key][$inter_key]['database_value'] = $database_value[$inter_value['key']];
                            }
                        }
                    }
                }
            }
        }
        return rest_ensure_response($settings_fields);
    }

    public function mvx_report_abuse_delete($request) {
        $reason = $request && $request->get_param('reason') ? $request->get_param('reason') : '';
        $product = $request && $request->get_param('product') ? $request->get_param('product') : '';
        $vendor_details = $request && $request->get_param('vendor') ? $request->get_param('vendor') : '';

        $user_query = new WP_User_Query(array('role' => 'dc_vendor', 'orderby' => 'registered', 'order' => 'ASC'));
        $users = $user_query->get_results();
        $user_list = [];
        if ($users) {
            foreach($users as $user) {
                $vendor = get_mvx_vendor($user->data->ID);
                $report_abuse_data = get_user_meta($vendor->id, 'report_abuse_data', true) ? get_user_meta($vendor->id, 'report_abuse_data', true) : array();
                if ($report_abuse_data) {
                    foreach ($report_abuse_data as $key => $value) {

                        if ($reason == $value['msg'] && $product == get_the_title($value['product_id']) && $vendor_details == $vendor->page_title) {
                            unset($report_abuse_data[$key]);
                            update_user_meta($vendor->id, 'report_abuse_data', $report_abuse_data);
                        }
                    }
                }
            }
        }
        return $this->mvx_report_abuse_details('');
    }

    public function mvx_report_abuse_details($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : '';
        $product_id = $request && $request->get_param('product_id') ? $request->get_param('product_id') : '';

        $user_query = new WP_User_Query(array('role' => 'dc_vendor', 'orderby' => 'registered', 'order' => 'ASC'));
        $users = $user_query->get_results();
        $user_list = [];
        if ($users) {
            foreach($users as $user) {
                $vendor = get_mvx_vendor($user->data->ID);
                if ($vendor_id && $vendor_id != $user->data->ID) {
                    continue;
                }
                $report_abuse_data = get_user_meta($vendor->id, 'report_abuse_data', true) ? get_user_meta($vendor->id, 'report_abuse_data', true) : array();
                if ($report_abuse_data) {
                    foreach ($report_abuse_data as $key => $value) {

                        if ($product_id && $product_id != $value['product_id']) {
                            continue;
                        }
                        $user_list[] = array(
                            'reason'            => $value['msg'],
                            'product'           => get_the_title($value['product_id']),
                            'vendor'            => $vendor->page_title,
                            'reported_by'       => $value['name'] .  '( ' . $value['email'] . ')',
                        );
                    }
                }
            }
        }

        return rest_ensure_response($user_list);
    }

    public function mvx_fetch_system_info() {
        global $MVX;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        // Core debug data.
        if ( ! class_exists( 'WP_Debug_Data' ) ) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
        }

        $is_module_active = mvx_get_option('mvx_all_active_module_list', true);
        $list_modules = [];
        if ($is_module_active) {
            foreach ($is_module_active as $key => $value) {
                $list_modules[] = $this->mvx_find_module_name_by_id($value);
            }
        }

        $mvx = [
            'label'  => esc_html__( 'MultiVendorX', 'multivendorx' ),
            'fields' => [
                'version'          => [
                    'label' => esc_html__( 'Version', 'multivendorx' ),
                    'value' => $MVX->version,
                ],
                'plugin_plan'      => [
                    'label' => esc_html__( 'Plugin subscription plan', 'multivendorx' ),
                    'value' => apply_filters('mvx_current_subscription_plan', __('Free', 'multivendorx') ),
                ],
                'active_modules'   => [
                    'label' => esc_html__( 'Active modules', 'multivendorx' ),
                    'value' => implode(", ", $list_modules),
                ]
            ],
        ];
        $mvx_data = apply_filters( 'mvx/status/mvx_info', $mvx );

        $core_data     = \WP_Debug_Data::debug_data();
        // Keep only relevant data.
        $core_data = array_intersect_key(
            $core_data,
            array_flip(
                [
                    'wp-core',
                    'wp-dropins',
                    'wp-active-theme',
                    'wp-parent-theme',
                    'wp-mu-plugins',
                    'wp-plugins-active',
                    'wp-plugins-inactive',
                    'wp-server',
                    'wp-database',
                    'wp-constants',
                    'wp-filesystem',
                ]
            )
        );

        $core_data = [ 'mvx' => $mvx_data ] + $core_data;
        
        return rest_ensure_response($core_data);
    }

    public function mvx_find_module_name_by_id($module_id = '') {
        $add_modules_details = mvx_list_all_modules();
        foreach ($add_modules_details as $key_parent => $value_parent) {
            foreach ($value_parent['options'] as $key_child => $value_child) {
                if ($value_child['id'] == $module_id) {
                    return $value_child['name'];
                }
            }
        }
    }

    public function mvx_system_info_copy_data() {
        $system_info = $this->mvx_fetch_system_info();
        $system_info = \WP_Debug_Data::format( $system_info->data, 'debug' );
        return $system_info;
    }

    public function mvx_tools_funtion($request) {
        global $wpdb, $MVX;
        $all_details = [];
        $type = $request && $request->get_param('type') ? $request->get_param('type') : '';
        
        if ($type == 'transients') {
            $vendors = get_mvx_vendors();
            foreach ( $vendors as $vendor ) {
                if ( $vendor ) $vendor->clear_all_transients($vendor->id);
            }
        } else if ($type == 'visitor') {
            $delete = $wpdb->query("TRUNCATE {$wpdb->prefix}mvx_visitors_stats");
            if ( $delete ) {
                $message = __( 'MVX visitors stats successfully deleted', 'multivendorx' );
            } else {
                $ran     = false;
                $message = __( 'There was an error calling this tool. There is no callback present.', 'multivendorx' );
            }
        } else if ($type == 'migrate_order') {
            delete_option('mvx_orders_table_migrated');
            wp_schedule_event( time(), 'hourly', 'mvx_orders_migration' );
        } else if ($type == 'migrate') {
            $all_details['redirect_link'] = admin_url('index.php?page=mvx-migrator');
            return $all_details;
        } else if ($type == 'default_pages') {
            require_once($MVX->plugin_path . 'includes/class-mvx-install.php');
            $install = new MVX_Install();
            if (!get_option("dc_product_vendor_plugin_page_install")) {
                $install->mvx_product_vendor_plugin_create_pages();
                mvx_update_option("dc_product_vendor_plugin_page_install", 1);
            }
        }
    }

    public function mvx_search_review($request) {
        $value = $request && $request->get_param('value') ? $request->get_param('value') : '';
        
        $review_list = array();
        $mvx_vendor_reviews = array();
        $args_default = array(
                'status' => 'approve',
                'type' => 'mvx_vendor_rating',
                'count' => false,
                'posts_per_page' => -1,
                'offset' => 0,
            );
        $mvx_vendor_reviews = get_comments($args_default);
        if ($mvx_vendor_reviews) {
            foreach ($mvx_vendor_reviews as $mvx_vendor_review) {
                $comment_vendor_id = get_comment_meta($mvx_vendor_review->comment_ID, 'vendor_rating_id', true);
                $vendor = get_mvx_vendor($comment_vendor_id);
                $vendor_term_id = get_user_meta($comment_vendor_id, '_vendor_term_id', true);
                $rating_val_array = mvx_get_vendor_review_info($vendor_term_id);
                $rating = round($rating_val_array['avg_rating'], 1);
                $review = '<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title='. sprintf(__('Rated %s out of 5', 'multivendorx'), $rating). '>
                        <span style="width: ' . ( round($rating_val_array['avg_rating']) / 5 ) * 100 .'%"><strong itemprop="ratingValue">'. ($rating). '</strong> '. ('out of 5').'</span>
                    </span>';
                if (stripos($mvx_vendor_review->comment_content, $value) !== false) {     
                    $review_list[] = apply_filters('mvx_list_table_reviews_columns_data', array(
                        'id'        =>  $mvx_vendor_review->comment_ID,
                        'author'    =>  $mvx_vendor_review->comment_author,
                        'user_id'   =>  $vendor->page_title,
                        'time'      =>  human_time_diff(strtotime($mvx_vendor_review->comment_date)),
                        'content'   =>  $mvx_vendor_review->comment_content,
                        'review'    =>  $review,
                        'link'      =>  admin_url('comment.php?action=editcomment&c='. $mvx_vendor_review->comment_ID .''),
                        'type'      =>  'review'
                    ), $mvx_vendor_review);
                }
            }
        }

        return rest_ensure_response($review_list);
    }

    public function mvx_delete_review($request) {
        $id = $request && $request->get_param('id') ? $request->get_param('id') : '';
        if (is_array($id)) {
            foreach ($id as $key => $value) {
                wp_delete_comment($value);
            }
        } else {
            wp_delete_comment($id);
        }
        return $this->mvx_list_of_store_review();
    }

    public function mvx_search_question_ans($request) {
        $value = $request && $request->get_param('value') ? $request->get_param('value') : '';
        global $MVX;
        $vendor_ids = get_mvx_vendors(array(), 'ids');
        $pending_list = [];
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'product',
            'post_status' => 'publish',
        );
        $get_vendor_products = new WP_Query($args);
        $get_vendor_products = $get_vendor_products->get_posts();
        if (!empty($get_vendor_products) && apply_filters('admin_can_approve_qna_answer', true)) {
            foreach ($get_vendor_products as $get_vendor_product) {
                $get_pending_questions = $MVX->product_qna->get_Questions($get_vendor_product->ID);
                if (!empty($get_pending_questions)) {
                    foreach ($get_pending_questions as $pending_question) {
                        $question_by_details = get_userdata($pending_question->ques_by);
                        $question_by = "<img src=' " . $MVX->plugin_url . 'assets/images/wp-avatar-frau.jpg' ."' class='avatar avatar-32 photo' height='32' width='32'>" .$question_by_details->data->display_name . "";
                        if (stripos($pending_question->ques_details, $value) !== false) {
                            $pending_list[] = array(
                                'id'                    =>  $pending_question->ques_ID,
                                'question_by'           =>  $question_by,
                                'question_product_id'   =>  $pending_question->product_ID,
                                'question_by_name'      =>  $question_by_details->data->display_name,
                                'product_name'          =>  get_the_title($pending_question->product_ID),
                                'question_details'      =>  $pending_question->ques_details,
                                'product_url'           =>  admin_url('post.php?post=' . $pending_question->product_ID . '&action=edit'),
                            );
                        }
                    }   
                }
            }
        }
        return rest_ensure_response($pending_list);
    }

    public function mvx_fetch_all_settings_for_searching($request) {
        $value = $request && $request->get_param('value') ? $request->get_param('value') : 0;
        $list_of_titles = $mvx_parent_tabs_datas = [];
        $all_fields_data = mvx_admin_backend_settings_fields_details();

        $all_tab_fileds = mvx_admin_backend_tab_settings();
        
        foreach ($all_tab_fileds as $key_fields_parent => $value_fields_parent) {
            foreach ($value_fields_parent as $key_list_parent => $value_list_parent) {
                $all_fields_data['others_fileds'][] = array(
                    'label' =>  $value_list_parent['tablabel'],
                    'desc'  =>  ($value_list_parent['description']) ? $value_list_parent['description'] : '',
                    'link'  =>  admin_url('admin.php?page=mvx#&submenu='. $value_list_parent['submenu'] .'&name='. $value_list_parent['modulename'] .'')
                );
            }
        }


        $all_fields_data['others_fileds_section1'] = array(
            array(
                'label' =>  __('Dashboard', 'multivendorx'),
                'desc'  =>  __('Dashboard Submenu page', 'multivendorx'),
                'link'  =>  admin_url('admin.php?page=mvx#&submenu=dashboard')
            ),
            array(
                'label' =>  __('Work Board', 'multivendorx'),
                'desc'  =>  __('Work Board Submenu page', 'multivendorx'),
                'link'  =>  admin_url('admin.php?page=mvx#&submenu=work-board&name=activity-reminder')
            ),
            array(
                'label' =>  __('Modules', 'multivendorx'),
                'desc'  =>  __('Modules Submenu page', 'multivendorx'),
                'link'  =>  admin_url('admin.php?page=mvx#&submenu=modules')
            ),
            array(
                'label' =>  __('Vendors', 'multivendorx'),
                'desc'  =>  __('Vendors Submenu page', 'multivendorx'),
                'link'  =>  admin_url('admin.php?page=mvx#&submenu=vendor')
            ),
            array(
                'label' =>  __('Payments', 'multivendorx'),
                'desc'  =>  __('Payments Submenu page', 'multivendorx'),
                'link'  =>  admin_url('admin.php?page=mvx#&submenu=payment&name=payment-masspay')
            ),
            array(
                'label' =>  __('Settings', 'multivendorx'),
                'desc'  =>  __('Settings Submenu page', 'multivendorx'),
                'link'  =>  admin_url('admin.php?page=mvx#&submenu=settings&name=settings-general')
            ),
            array(
                'label' =>  __('Analytics', 'multivendorx'),
                'desc'  =>  __('Analytics Submenu page', 'multivendorx'),
                'link'  =>  admin_url('admin.php?page=mvx#&submenu=analytics&name=admin-overview')
            ),
            array(
                'label' =>  __('Status and Tools', 'multivendorx'),
                'desc'  =>  __('Status and Tools Submenu page', 'multivendorx'),
                'link'  =>  admin_url('admin.php?page=mvx#&submenu=status-tools&name=version-control')
            )
        );

        $add_modules_details = mvx_list_all_modules();
        foreach ($add_modules_details as $key_parent => $value_parent) {
            foreach ($value_parent['options'] as $key_child => $value_child) {
                $all_fields_data['others_fileds_section5'][] =  array( 'id' => $value_child['id'], 'label' => $value_child['name'], 'desc' => $value_child['description'], 'link' => admin_url('admin.php?page=mvx#&submenu=modules&name='.$value_child['id'].''));
            }
        }

        if ($value) {
            foreach ($all_fields_data as $key_fields => $value_fields) {
                foreach ($value_fields as $key_list => $value_list) {
                    if (stripos($value_list['label'], $value) !== false && $value_list['label'] !== 'no_label') {
                        $link  =  $value_list['link'] ? $value_list['link'] : (stripos($key_fields, 'payment-') !== false ? admin_url('admin.php?page=mvx#&submenu=payment&name='. $key_fields .'') : admin_url('admin.php?page=mvx#&submenu=settings&name='. $key_fields .''));
                        $details_link = explode("=", $link);
                        $list_of_titles[] = array(
                            'label' =>  $value_list['label'],
                            'desc'  =>  ($value_list['desc']) ? $value_list['desc'] : '',
                            'link_redirect'  =>  ($value_list['link_redirect']) ? $value_list['link_redirect'] : '',
                            'link'  =>  $value_list['link'] ? $value_list['link'] : (stripos($key_fields, 'payment-') !== false ? admin_url('admin.php?page=mvx#&submenu=payment&name='. $key_fields .'') : admin_url('admin.php?page=mvx#&submenu=settings&name='. $key_fields .'')),
                            'details'   => isset($details_link[2]) ? '<h5>Under '. str_replace('-', ' ', str_replace("&name", "", $details_link[2])) . ' section</h5>' : ''
                        );
                    }
                }
            }
        }
        return rest_ensure_response($list_of_titles);
    }

    public function mvx_bulk_todo_pending_product($request) {
        $data_list = $request && $request->get_param('data_list') ? $request->get_param('data_list') : '';
        $select_option_value = $request && $request->get_param('value') ? $request->get_param('value') : '';
        $type = $request && $request->get_param('type') ? $request->get_param('type') : '';
        $get_product_list = $get_users_list = $get_coupon_list = $get_transaction_list = $get_pending_questions_list = $get_request_profile_deletion_vendor_list = [];
        if ($type == "pending_product") {
            
            if ($this->mvx_list_of_pending_vendor_product()->data) {
                foreach ($this->mvx_list_of_pending_vendor_product()->data as $key => $value) {
                    if ($data_list[$key]) {
                        $get_product_list[] = $value['id'];
                    }
                }
            }
            if ($get_product_list) {
                foreach ($get_product_list as $product_key => $product_id) {
                    if ($select_option_value == "approve") {
                        $post_update = array(
                            'ID'            => $product_id,
                            'post_status'   => 'publish',
                        );
                        wp_update_post( $post_update );
                    } else {
                        $post = get_post($product_id);
                        $reason = '';
                        $vendor = get_mvx_product_vendors($product_id);
                        $email_vendor = WC()->mailer()->emails['WC_Email_Vendor_Product_Rejected'];
                        $email_vendor->trigger($product_id, $post, $vendor, $reason);
                        update_post_meta($product_id, '_dismiss_to_do_list', 'true');
                        $comment_id = MVX_Product::add_product_note($product_id, $reason, get_current_user_id());
                        update_post_meta($product_id, '_comment_dismiss', absint($comment_id));
                        add_comment_meta($comment_id, '_author_id', get_current_user_id());
                    }
                }
            }

            
        } elseif ($type == "pending_vendor") {

            if ($this->mvx_list_of_pending_vendor()->data) {
                foreach ($this->mvx_list_of_pending_vendor()->data as $key => $value) {
                    if ($data_list[$key]) {
                        $get_users_list[] = $value['id'];
                    }
                }
            }

            if ($select_option_value == "approve") {
                if ($get_users_list) {
                    foreach ($get_users_list as $vendor_key => $vendor_id) {
                        $user = new WP_User(absint($vendor_id));
                        $user->set_role('dc_vendor');
                        $user_dtl = get_userdata(absint($vendor_id));
                        $email = WC()->mailer()->emails['WC_Email_Approved_New_Vendor_Account'];
                        $email->trigger($vendor_id, $user_dtl->user_pass);
                    }
                }
            } else {
                if ($get_users_list) {
                    foreach ($get_users_list as $vendor_key => $vendor_id) {
                        update_user_meta($vendor_id, '_dismiss_to_do_list', 'true');
                    }
                }
            }
            
        } elseif ($type == "pending_coupon") {

            if ($this->mvx_list_of_pending_vendor_coupon()->data) {
                foreach ($this->mvx_list_of_pending_vendor_coupon()->data as $key => $value) {
                    if ($data_list[$key]) {
                        $get_coupon_list[] = $value['id'];
                    }
                }
            }

            if ($select_option_value == "approve") {
                if ($get_coupon_list) {
                    foreach ($get_coupon_list as $coupon_key => $coupon_id) {
                        $post_update = array(
                            'ID'            => $coupon_id,
                            'post_status'   => 'publish',
                        );
                        wp_update_post( $post_update );  
                    }
                }
            } else {
                if ($get_coupon_list) {
                    foreach ($get_coupon_list as $coupon_key => $coupon_id) {
                        update_post_meta($coupon_id, '_dismiss_to_do_list', 'true');
                    }
                }
            }


        } elseif ($type == "pending_transaction") {

            if ($this->mvx_list_of_pending_transaction()->data) {
                foreach ($this->mvx_list_of_pending_transaction()->data as $key => $value) {
                    if ($data_list[$key]) {
                        $get_transaction_list[] = array('transaction_id'    =>  $value['id'], 'vendor_id'    =>    $value['vendor_id']);
                    }
                }
            }

            if ($select_option_value == "approve") {
                if ($get_transaction_list) {
                    foreach ($get_transaction_list as $transaction_key => $transaction_id) {
                        $vendor = get_mvx_vendor_by_term( $transaction_id['vendor_id'] );
                        update_post_meta($transaction_id['transaction_id'], 'paid_date', date("Y-m-d H:i:s"));
                        $commission_detail = get_post_meta($transaction_id['transaction_id'], 'commission_detail', true);
                        if ($commission_detail && is_array($commission_detail)) {
                            foreach ($commission_detail as $commission_id) {
                                mvx_paid_commission_status($commission_id);
                                $withdrawal_total = MVX_Commission::commission_totals($commission_id, 'edit');
                                $order_id = get_post_meta( $commission_id, '_commission_order_id', true );
                                $args = array(
                                    'meta_query' => array(
                                        array(
                                            'key' => '_commission_vendor',
                                            'value' => absint($vendor->term_id),
                                            'compare' => '='
                                        ),
                                    ),
                                );
                                $unpaid_commission_total = MVX_Commission::get_commissions_total_data( $args, $vendor->id );
                                $data = array(
                                    'vendor_id'     => $vendor->id,
                                    'order_id'      => $order_id,
                                    'ref_id'        => $transaction_id['transaction_id'],
                                    'ref_type'      => 'withdrawal',
                                    'ref_info'      => sprintf(__('Withdrawal generated for Commission &ndash; #%s', 'multivendorx'), $commission_id),
                                    'ref_status'    => 'completed',
                                    'ref_updated'   => date('Y-m-d H:i:s', current_time('timestamp')),
                                    'debit'         => $withdrawal_total,
                                    'balance'       => $unpaid_commission_total['total'],
                                );
                                $data_store = $MVX->ledger->load_ledger_data_store();
                                $ledger_id = $data_store->create($data);
                            }
                            $email_admin = WC()->mailer()->emails['WC_Email_Vendor_Commission_Transactions'];
                            $email_admin->trigger($transaction_id['transaction_id'], $vendor_id);
                            update_post_meta($transaction_id['transaction_id'], '_dismiss_to_do_list', 'true');
                            wp_update_post(array('ID' => $transaction_id['transaction_id'], 'post_status' => 'mvx_completed'));
                            do_action( 'mvx_todo_done_pending_transaction', $transaction_id['transaction_id'], $vendor );
                        }
                    }
                }
            } else {
                if ($get_transaction_list) {
                    foreach ($get_transaction_list as $transaction_key => $transaction_id) {
                        update_post_meta($transaction_id['transaction_id'], '_dismiss_to_do_list', 'true');
                        wp_update_post(array('ID' => $transaction_id['transaction_id'], 'post_status' => 'mvx_canceled'));
                    }
                }
            }

        } elseif ($type == "pending_question") {

            if ($this->mvx_list_of_pending_question('', '')->data) {
                foreach ($this->mvx_list_of_pending_question('', '')->data as $key => $value_p) {
                    if ($data_list[$key]) {
                        $get_pending_questions_list[] = array(
                            'question_id'   => $value_p['id'],
                            'product_id'    => $value_p['question_product_id']
                        );
                    }
                }
            }

            if ($get_pending_questions_list) {
                foreach ($get_pending_questions_list as $q_key => $q_value) {
                    $product_id     = $q_value['product_id'] ? $q_value['product_id'] : 0;
                    $question_id    = $q_value['question_id'] ? $q_value['question_id'] : 0;
                    $data = array();
                    if (!empty($product_id)) {
                        $vendor = get_mvx_product_vendors(absint($product_id));
                        if ($select_option_value == 'rejected') {
                            $MVX->product_qna->deleteQuestion( $question_id );
                            delete_transient('mvx_customer_qna_for_vendor_' . $vendor->id);
                        } else {
                            $data['status'] = $select_option_value;
                            $MVX->product_qna->updateQuestion( $question_id, $data );
                            $questions = $MVX->product_qna->get_Vendor_Questions($vendor);
                            set_transient('mvx_customer_qna_for_vendor_' . $vendor->id, $questions);
                        }
                    }
                }
            }
        } elseif (!empty($type) && $type == "requested_profile_deletion") {
            if (method_exists($this, 'mvx_list_of_request_profile_deletion_vendor') && is_array($this->mvx_list_of_request_profile_deletion_vendor()->data) && !empty($this->mvx_list_of_request_profile_deletion_vendor()->data)) {
                foreach ($this->mvx_list_of_request_profile_deletion_vendor()->data as $key => $value) {
                    if (is_array($data_list) && !empty($data_list) && isset($data_list[$key]) && isset($value['id'])) {
                        $get_request_profile_deletion_vendor_list[] = $value['id'];
                    }
                }
            }
            if (!empty($select_option_value) && $select_option_value == "dismiss") {
                if ($get_request_profile_deletion_vendor_list) {
                    foreach ($get_request_profile_deletion_vendor_list as $request_vendor_key => $request_vendor_id) { 
                        if (!empty($request_vendor_id) && metadata_exists('user', $request_vendor_id, '_deactivate_reason')) {
                            delete_user_meta($request_vendor_id, '_deactivate_reason');
                            $email = isset(WC()->mailer()->emails['WC_Email_Vendor_Account_Deactive_Request_Reject_Mail']) ? WC()->mailer()->emails['WC_Email_Vendor_Account_Deactive_Request_Reject_Mail'] : '';
                            if (!empty($email) && $email->is_enabled()) {
                                $email->trigger($request_vendor_id);  
                            }
                        }   
                    }
                }
            }
        } 
        do_action('mvx_task_board_bulk_action_triggers', $type, $data_list, $select_option_value);
        return $this->mvx_list_of_work_board_content();
    }

    function mvx_bulk_todo_request_profile_deletion($request) {
        $data_list = $request && $request->get_param('data_list') ? $request->get_param('data_list') : '';
        $get_request_profile_deletion_vendor_list = [];
        if (method_exists($this, 'mvx_list_of_request_profile_deletion_vendor') && is_array($this->mvx_list_of_request_profile_deletion_vendor()->data) && !empty($this->mvx_list_of_request_profile_deletion_vendor()->data)) {
            foreach ($this->mvx_list_of_request_profile_deletion_vendor()->data as $key => $value) {
                if (is_array($data_list) && !empty($data_list) && isset($data_list[$key]) && isset($value['id'])) {
                    $get_request_profile_deletion_vendor_list[] = $value['id'];
                }
            }
        }
        return rest_ensure_response($get_request_profile_deletion_vendor_list);
    }

    public function mvx_dismiss_and_approve_vendor_product_questions($request) {
        
    }

    public function mvx_dismiss_and_approve_vendor_coupon($request) {
        $coupon_id = $request && $request->get_param('coupon_id') ? $request->get_param('coupon_id') : 0;
        $type = $request && $request->get_param('type') ? $request->get_param('type') : 0;
        if ($type == "dismiss") {
            update_post_meta($coupon_id, '_dismiss_to_do_list', 'true');
        } else {
            $post_update = array(
                'ID'            => $coupon_id,
                'post_status'   => 'publish',
            );
            wp_update_post( $post_update );
        }
        return $this->mvx_list_of_pending_vendor_coupon();
    }

    // approve pending user
    public function mvx_approve_vendor($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : 0;
        $section = $request && $request->get_param('section') ? $request->get_param('section') : '';
        if ($vendor_id) {
            $user = new WP_User(absint($vendor_id));
            $user->set_role('dc_vendor');
            $user_dtl = get_userdata(absint($vendor_id));
            $email = WC()->mailer()->emails['WC_Email_Approved_New_Vendor_Account'];
            $email->trigger($vendor_id, $user_dtl->user_pass);
        }

        if ($section) {
            return $this->mvx_list_all_vendor('');
        }

        return $this->mvx_list_of_pending_vendor();
    }

    public function mvx_reject_vendor($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : 0;
        $custom_note = $request && $request->get_param('custom_note') ? $request->get_param('custom_note') : '';
        $note_by = get_current_user_id();
        if ($vendor_id) {
            $user = new WP_User(absint($vendor_id));
            $user->set_role('dc_rejected_vendor');

            if (isset($custom_note) && $custom_note != '') {
                $mvx_vendor_rejection_notes = unserialize(get_user_meta($vendor_id, 'mvx_vendor_rejection_notes', true));
                $mvx_vendor_rejection_notes[time()] = array(
                    'note_by' => $note_by,
                    'note' => $custom_note);
                update_user_meta($vendor_id, 'mvx_vendor_rejection_notes', serialize($mvx_vendor_rejection_notes));
            }
        }

        return $this->mvx_list_all_vendor('');
    }

    // dissmiss pending user
    public function mvx_dismiss_vendor($request) {
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : 0;
        update_user_meta($vendor_id, '_dismiss_to_do_list', 'true');
        return $this->mvx_list_of_pending_vendor();
    }

    public function mvx_dismiss_requested_vendors_query($request) {
        $product_id = $request && $request->get_param('product_id') ? $request->get_param('product_id') : 0;
        $vendor_id = $request && $request->get_param('vendor_id') ? $request->get_param('vendor_id') : 0;
        $post = get_post($product_id);
        $reason = '';
        $vendor = get_mvx_vendor($vendor_id);
        $email_vendor = WC()->mailer()->emails['WC_Email_Vendor_Product_Rejected'];
        $email_vendor->trigger($product_id, $post, $vendor, $reason);
        update_post_meta($product_id, '_dismiss_to_do_list', 'true');
        $comment_id = MVX_Product::add_product_note($product_id, $reason, get_current_user_id());
        update_post_meta($product_id, '_comment_dismiss', absint($comment_id));
        add_comment_meta($comment_id, '_author_id', get_current_user_id());

        return $this->mvx_list_of_pending_vendor_product();
    }

    public function mvx_approve_product($request) {
        $product_id = $request && $request->get_param('product_id') ? $request->get_param('product_id') : 0;
        $post_update = array(
            'ID'            => $product_id,
            'post_status'   => 'publish',
        );
        wp_update_post( $post_update );
        return $this->mvx_list_of_pending_vendor_product();
    }

    public function mvx_delete_post_details($request) {
        $ids = $request && $request->get_param('ids') ? $request->get_param('ids') : 0;
        $title = $request && $request->get_param('title') ? $request->get_param('title') : 0;
        if ($title == 'post_announcement') {
            wp_delete_post($ids);
            return $this->mvx_display_announcement('');
        } elseif ($title == 'post_knowladgebase') {
            wp_delete_post($ids);
            return $this->mvx_display_list_knowladgebase('');
        } elseif ($title == 'review') {
            // delete review code
            return $this->mvx_list_of_store_review();
        }
            
    }

    public function mvx_update_custom_post_status($request) {
        $ids = $request && $request->get_param('ids') ? wp_list_pluck($request->get_param('ids'), 'id') : 0;
        $value = $request && $request->get_param('value') ? ($request->get_param('value')) : 0;
        foreach ($ids as $key_id => $value_id) {
            $post_update = array(
                'ID'           => $value_id,
                'post_status' => $value,
            );
            wp_update_post( $post_update );
        }
    }

    // search announcement
    public function mvx_search_announcement($request) {
        $ids = $request && $request->get_param('ids') ? ($request->get_param('ids')) : 0;
        $value = $request && $request->get_param('value') ? ($request->get_param('value')) : 0;
        $all_announcement   =   $this->mvx_display_announcement('');
        $search_announcement_renew = [];
        if ($all_announcement->data && !empty($all_announcement->data) && !empty($value)) {
            foreach ($all_announcement->data as $announce_key => $anounce_value) {
                if (stripos($anounce_value['sample_title'], $value) !== false) {
                    $search_announcement_renew[]    =   $all_announcement->data[$announce_key];
                }
            }            
        } else {
            return rest_ensure_response($all_announcement->data);
        }
        return rest_ensure_response($search_announcement_renew);
    }
    
    // search knowledgebase
    public function mvx_search_knowledgebase($request) {
        $value = $request && $request->get_param('value') ? ($request->get_param('value')) : 0;

        $all_knowledgebase   =   $this->mvx_display_list_knowladgebase('');
        $search_knowledgebase_renew = [];
        if ($all_knowledgebase->data && !empty($all_knowledgebase->data) && !empty($value)) {
            foreach ($all_knowledgebase->data as $announce_key => $anounce_value) {
                if (stripos($anounce_value['sample_title'], $value) !== false) {
                    $search_knowledgebase_renew[]    =   $all_knowledgebase->data[$announce_key];
                }
            }
        } else {
            return rest_ensure_response($all_knowledgebase->data);
        }
        return rest_ensure_response($search_knowledgebase_renew);
    }


    public function mvx_list_of_store_review() {
        $review_list = array();
        $mvx_vendor_reviews = array();
        $args_default = array(
                'status' => 'approve',
                'type' => 'mvx_vendor_rating',
                'count' => false,
                'posts_per_page' => -1,
                'offset' => 0,
            );
        $mvx_vendor_reviews = get_comments($args_default);
        if ($mvx_vendor_reviews) {
            foreach ($mvx_vendor_reviews as $mvx_vendor_review) {
                $comment_vendor_id = get_comment_meta($mvx_vendor_review->comment_ID, 'vendor_rating_id', true);
                $vendor = get_mvx_vendor($comment_vendor_id);
                $vendor_term_id = get_user_meta($comment_vendor_id, '_vendor_term_id', true);
                $rating_val_array = mvx_get_vendor_review_info($vendor_term_id);
                $rating = round($rating_val_array['avg_rating'], 1);
                $review = '<span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title='. sprintf(__('Rated %s out of 5', 'multivendorx'), $rating). '>
                        <span style="width: ' . ( round($rating_val_array['avg_rating']) / 5 ) * 100 .'%"><strong itemprop="ratingValue">'. ($rating). '</strong> '. ('out of 5').'</span>
                    </span>';

                $review_list[] = apply_filters('mvx_list_table_reviews_columns_data', array(
                    'id'        =>  $mvx_vendor_review->comment_ID,
                    'author'    =>  $mvx_vendor_review->comment_author,
                    'user_id'   =>  $vendor->page_title,
                    'time'      =>  human_time_diff(strtotime($mvx_vendor_review->comment_date)),
                    'content'   =>  $mvx_vendor_review->comment_content,
                    'review'    =>  $review,
                    'link'      =>  admin_url('comment.php?action=editcomment&c='. $mvx_vendor_review->comment_ID .''),
                    'type'      =>  'review'
                ), $mvx_vendor_review);
            }
        }

        return rest_ensure_response($review_list);
    }

    public function mvx_list_of_pending_vendor_product() {
        global $MVX;
        $pending_list = [];
        $vendor_ids = get_mvx_vendors(array(), 'ids');
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'product',
            'post_status' => 'pending',
        );
        $get_pending_products = new WP_Query($args);
        $get_pending_products = $get_pending_products->get_posts();

        if (!empty($get_pending_products)) {
            foreach ($get_pending_products as $get_pending_product) {
                $dismiss = get_post_meta($get_pending_product->ID, '_dismiss_to_do_list', true);
                if ($dismiss) continue;

                $currentvendor = get_mvx_vendor($get_pending_product->post_author);
                $vendor_term = $currentvendor ? get_term($currentvendor->term_id) : '';
                $question_by = "<img src=' " . $MVX->plugin_url . 'assets/images/wp-avatar-frau.jpg' ."' class='avatar avatar-32 photo' height='32' width='32'>" .$get_pending_product->post_title . "";
                $pending_list[] = array(
                    'id'        =>  $get_pending_product->ID,
                    'vendor'    =>  $vendor_term ? $vendor_term->name : '',
                    'product_src'   =>  $question_by, //wp_get_attachment_image_src( get_post_thumbnail_id( $get_pending_product->ID ), 'single-post-thumbnail' ),
                    'vendor_id'    =>  $get_pending_product->post_author,
                    'vendor_link'   =>  sprintf('?page=%s&ID=%s&name=vendor-personal', 'mvx#&submenu=vendor', $currentvendor ? $currentvendor->id : 0),
                    'product'   =>  $get_pending_product->post_title,
                    'product_url'   =>  admin_url('post.php?post=' . $get_pending_product->ID . '&action=edit'),
                );
            }
        }
        return rest_ensure_response($pending_list);
    }
    public function mvx_list_of_pending_vendor() {
        global $MVX;
        $pending_list = [];
        $get_pending_vendors = get_users('role=dc_pending_vendor');
        if (!empty($get_pending_vendors)) {
            foreach ($get_pending_vendors as $pending_vendor) {
                $dismiss = get_user_meta($pending_vendor->ID, '_dismiss_to_do_list', true);
                if ($dismiss)   continue;
                $pending_list[] = array(
                    'id'        =>  $pending_vendor->ID,
                    'vendor_image_src'  =>  get_avatar($pending_vendor->ID, 50),
                    'vendor_link'   =>  sprintf('?page=%s&ID=%s&name=vendor-application', 'mvx#&submenu=vendor', $pending_vendor->ID),
                    'vendor'    =>  $question_by = "<img src=' " . $MVX->plugin_url . 'assets/images/wp-avatar-frau.jpg' ."' class='avatar avatar-32 photo' height='32' width='32'>" .$pending_vendor->user_login . "",
                    'vendor_name'    =>  $pending_vendor->user_login,
                );
            }
        }
        return rest_ensure_response($pending_list);
    }
    public function mvx_list_of_pending_vendor_coupon() {
        $pending_list = [];
        $vendor_ids = get_mvx_vendors(array(), 'ids');
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'shop_coupon',
            'post_status' => 'pending',
        );
        $get_pending_coupons = new WP_Query($args);
        $get_pending_coupons = $get_pending_coupons->get_posts();
        if (!empty($get_pending_coupons)) {
            foreach ($get_pending_coupons as $get_pending_coupon) {
                $dismiss = get_post_meta($get_pending_coupon->ID, '_dismiss_to_do_list', true);
                if ($dismiss)   continue;
                $currentvendor = get_mvx_vendor($get_pending_coupon->post_author);
                $vendor_term = get_term($currentvendor->term_id);
                $pending_list[] = array(
                    'id'            =>  $get_pending_coupon->ID,
                    'vendor'        =>  $vendor_term->name,
                    'vendor_id'    =>  $get_pending_coupon->post_author,
                    'vendor_link'   =>  sprintf('?page=%s&ID=%s&name=vendor-personal', 'mvx#&submenu=vendor', $currentvendor->id),
                    'coupon'        =>  $get_pending_coupon->post_title,
                    'coupon_url'   =>  admin_url('post.php?post=' . $get_pending_coupon->ID . '&action=edit'),
                );
            }
        }
        return rest_ensure_response($pending_list);
    }
    public function mvx_list_of_pending_transaction() {
        $pending_list = $order_id = [];
        $args = array(
            'post_type' => array('mvx_transaction', 'wcmp_transaction'),
            'post_status' => 'mvx_processing',
            'meta_key' => 'transaction_mode',
            'meta_value' => 'direct_bank',
            'posts_per_page' => -1
        );
        $transactions = get_posts($args);

        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                $dismiss = get_post_meta($transaction->ID, '_dismiss_to_do_list', true);
                $vendor_term_id = $transaction->post_author;
                $currentvendor = get_mvx_vendor_by_term($vendor_term_id);
                $vendor_term = get_term($vendor_term_id);
                if ($dismiss || !$currentvendor) {
                    continue;
                }
                $account_name = get_user_meta($currentvendor->id, '_vendor_account_holder_name', true);
                $account_no = get_user_meta($currentvendor->id, '_vendor_bank_account_number', true);
                $bank_name = get_user_meta($currentvendor->id, '_vendor_bank_name', true);
                $iban = get_user_meta($currentvendor->id, '_vendor_iban', true);
                $amount = get_post_meta($transaction->ID, 'amount', true) - get_post_meta($transaction->ID, 'transfer_charge', true) - get_post_meta($transaction->ID, 'gateway_charge', true);
                $address_array = apply_filters('mvx_wordboard_pending_bank_transfer_data', array(
                __('Account Name-', 'multivendorx') . ' ' . $account_name,
                __('Account No -', 'multivendorx') . ' ' . $account_no,
                __('Bank Name -', 'multivendorx') . ' ' . $bank_name,
                __('IBAN -', 'multivendorx') . ' ' . $iban,
                    ), $currentvendor, $transaction);

                $commission_details = get_post_meta($transaction->ID, 'commission_detail', true);
                if ($commission_details) {
                    foreach ($commission_details as $commission_detail)
                        $order_id[] = get_post_meta($commission_detail, '_commission_order_id', true);
                }

                $pending_list[] = array(
                    'id'        =>  $transaction->ID,
                    'vendor'    =>  $vendor_term->name,
                    'vendor_id'    =>  $vendor_term_id,
                    'commission'    =>  $transaction->post_title,
                    'amount'    =>  $amount,
                    'order'     =>  '#'. implode(', #', $order_id),
                    'account_details'   =>  implode('<br/>', $address_array)
                );
            }
        }
        return rest_ensure_response($pending_list);
    }

    public function mvx_list_of_pending_question($request, $extra_status = '') {
        global $MVX;
        $status = $request && $request->get_param('status') ? ($request->get_param('status')) : '';
        $vendor_ids = get_mvx_vendors(array(), 'ids');
        $pending_list = [];
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'product',
            'post_status' => 'publish',
        );
        $get_vendor_products = new WP_Query($args);
        $get_vendor_products = $get_vendor_products->get_posts();
        if (!empty($get_vendor_products) && apply_filters('admin_can_approve_qna_answer', true)) {
            foreach ($get_vendor_products as $get_vendor_product) {
                $get_pending_questions = $status && $status == 'publish' ? $MVX->product_qna->get_Questions($get_vendor_product->ID) : ( $extra_status == 'all' ? $MVX->product_qna->get_Questions($get_vendor_product->ID) : $MVX->product_qna->get_Pending_Questions($get_vendor_product->ID) );
                if (!empty($get_pending_questions)) {
                    foreach ($get_pending_questions as $pending_question) {
                        $question_by_details = get_userdata($pending_question->ques_by);
                        $question_by = "<img src=' " . $MVX->plugin_url . 'assets/images/wp-avatar-frau.jpg' ."' class='avatar avatar-32 photo' height='32' width='32'>" .$question_by_details->data->display_name . "";
                        $pending_list[] = array(
                            'id'                    =>  $pending_question->ques_ID,
                            'question_by'           =>  $question_by,
                            'question_product_id'   =>  $pending_question->product_ID,
                            'question_by_name'      =>  $question_by_details->data->display_name,
                            'product_name'          =>  get_the_title($pending_question->product_ID),
                            'question_details'      =>  $pending_question->ques_details,
                            'question_date'         =>  human_time_diff(strtotime($pending_question->ques_created)),
                            'question_status'       =>  $pending_question->status,
                            'product_url'           =>  admin_url('post.php?post=' . $pending_question->product_ID . '&action=edit'),
                        );
                    }   
                }
            }
        }
        return rest_ensure_response($pending_list);
    }

    public function mvx_list_of_request_profile_deletion_vendor() {
        global $MVX;
        $request_profile_deletion_list = [];
        $args = array(
            'role__in' => array('dc_vendor'),
            'fields' => 'all',
            'meta_query' => array(
                array(
                    'key'     => '_deactivate_reason',
                    'value'   => '',
                    'compare' => '!='
                ),
            )
        );
        $get_request_profile_deletion_vendors = get_users($args);
        if (!empty($get_request_profile_deletion_vendors)) {
            foreach ($get_request_profile_deletion_vendors as $request_profile_deletion_vendor) {
                if (!empty($request_profile_deletion_vendor)) {
                    $reason = get_user_meta($request_profile_deletion_vendor->ID, '_deactivate_reason', true) ? get_user_meta($request_profile_deletion_vendor->ID, '_deactivate_reason', true) : '';
                    $dismiss = get_user_meta($request_profile_deletion_vendor->ID, '_dismiss_to_do_list', true) ? get_user_meta($request_profile_deletion_vendor->ID, '_dismiss_to_do_list', true) : '';
                    if (!empty($dismiss)) continue;
                    $request_profile_deletion_list[] = array(
                        'id'        =>  $request_profile_deletion_vendor->ID,
                        'vendor_image_src'  =>  get_avatar($request_profile_deletion_vendor->ID, 50),
                        'vendor_link'   =>  sprintf('?page=%s&ID=%s&name=vendor-application', 'mvx#&submenu=vendor', $request_profile_deletion_vendor->ID),
                        'vendor'    => "<img src=' " . $MVX->plugin_url . 'assets/images/wp-avatar-frau.jpg' ."' class='avatar avatar-32 photo' height='32' width='32'>" .$request_profile_deletion_vendor->user_login . "",
                        'vendor_name'    =>  $request_profile_deletion_vendor->user_login,
                        'deactivate_reason' => $reason,
                    );
                }
            }
        }
        return rest_ensure_response($request_profile_deletion_list);
    }

    public function mvx_approve_dismiss_pending_question($request) {
        global $MVX;
        $productId = $request && $request->get_param('productId') ? $request->get_param('productId') : 0;
        $questionId = $request && $request->get_param('questionId') ? $request->get_param('questionId') : 0;
        $type = $request && $request->get_param('type') ? $request->get_param('type') : '';
        $data = array();
        if (!empty($productId)) {
            $vendor = get_mvx_product_vendors(absint($productId));
            if ($type == 'rejected') {
                $MVX->product_qna->deleteQuestion( $questionId );
                delete_transient('mvx_customer_qna_for_vendor_' . $vendor->id);
            } else {
                $data['status'] = $type;
                $MVX->product_qna->updateQuestion( $questionId, $data );
                $questions = $MVX->product_qna->get_Vendor_Questions($vendor);
                set_transient('mvx_customer_qna_for_vendor_' . $vendor->id, $questions);
            }
        }
        return $this->mvx_list_of_pending_question('', 'all');
    }

    public function mvx_create_knowladgebase($request) {
        $all_details = [];
        $fetch_data = $request->get_param('model');
        $knowladgebase_title = $fetch_data && isset($fetch_data['knowladgebase_title']) ? $fetch_data['knowladgebase_title'] : '';
        $knowladgebase_content = $fetch_data && isset($fetch_data['knowladgebase_content']) ? $fetch_data['knowladgebase_content'] : '';
        $post_id = wp_insert_post( array( 'post_title' => $knowladgebase_title, 'post_type' => 'mvx_university', 'post_status' => 'publish', 'post_content' => $knowladgebase_content ) );
        $all_details['redirect_link'] = admin_url('admin.php?page=mvx#&submenu=work-board&name=knowladgebase&knowladgebaseID='. $post_id .'');
        return $all_details;
    }
    public function mvx_display_list_knowladgebase($request) {
        $knowladgebase_list = [];
        $status = $request && $request->get_param('status') ? $request->get_param('status') : '';
        $status = $status == 'all' ? array('publish', 'auto-draft', 'pending') : $status;
        $announcement_list = array();
        $args = array(
            'post_type' => 'mvx_university',
            'post_status' => $status ? $status : array('publish', 'auto-draft', 'pending'),
            'posts_per_page' => -1,
        );
        $knowladgebase = get_posts($args);

        foreach ($knowladgebase as $knowladgebasekey => $knowladgebasevalue) {
            $knowladgebase_list[] = array(
                'id'            =>  $knowladgebasevalue->ID,
                'sample_title'  =>  $knowladgebasevalue->post_title,
                'title'         =>  '<a href="' . sprintf('?page=%s&name=%s&knowladgebaseID=%s', 'mvx#&submenu=work-board', 'knowladgebase', $knowladgebasevalue->ID) . '">#' . $knowladgebasevalue->post_title . '</a>',
                'date'          =>  $knowladgebasevalue->post_modified,
                'link'          =>  sprintf('?page=%s&name=%s&knowladgebaseID=%s', 'mvx#&submenu=work-board', 'knowladgebase', $knowladgebasevalue->ID),
                'type'          =>  'post_knowladgebase',
            );
        }
        return rest_ensure_response($knowladgebase_list);
    }
    public function mvx_update_knowladgebase_display($request) {
        global $MVX;
        $knowladgebase_id = $request && $request->get_param('knowladgebase_id') ? ($request->get_param('knowladgebase_id')) : 0;
        $knowladgebase_fields_data = mvx_admin_backend_settings_fields_details();
        if ($knowladgebase_id && absint($knowladgebase_id) > 0) {

            $post_details = get_post($knowladgebase_id);
            $knowladgebase_title =   $post_details->post_title;
            $knowladgebase_content   =   $post_details->post_content;
        }

        $knowladgebase_fields_data['update_knowladgebase_display'] = [
            [
                'key'       => 'knowladgebase_title',
                'type'      => 'text',
                'label'     => __( 'Title (required)', 'multivendorx' ),
                'props'     => array(
                    'required'  => true
                ),
                'database_value' => isset($knowladgebase_title) ? $knowladgebase_title : '',
            ],
            [
                'label' => __('Enter Content', 'multivendorx'),
                'type' => get_mvx_vendor_settings('mvx_tinymce_api_section', 'settings_general') ? 'wpeditor' : 'textarea', 
                'key' => 'knowladgebase_content', 
                'database_value' => $knowladgebase_content
            ]
        ];

        return rest_ensure_response($knowladgebase_fields_data);
    }
    public function mvx_update_knowladgebase($request) {
        $knowladgebase_id = $request && $request->get_param('knowladgebase_id') ? ($request->get_param('knowladgebase_id')) : 0;
        $fetch_data = $request->get_param('model');
        
        $knowladgebase_post = array(
            'ID'    =>  $knowladgebase_id,
        );

        if (isset($fetch_data['knowladgebase_title']) && !empty($fetch_data['knowladgebase_title'])) {
            $knowladgebase_post['post_title'] = $fetch_data['knowladgebase_title'];
        }
        if (isset($fetch_data['knowladgebase_content']) && !empty($fetch_data['knowladgebase_content'])) {
            $knowladgebase_post['post_content'] = $fetch_data['knowladgebase_content'];
        }
        // Update the post into the database
        wp_update_post( $knowladgebase_post );
    }


    // announcement

    public function mvx_update_announcement($request) {
        $announcement_id = $request && $request->get_param('announcement_id') ? ($request->get_param('announcement_id')) : 0;
        $fetch_data = $request->get_param('model');
        
        $announcement_post = array(
            'ID'    =>  $announcement_id,
        );

        if (isset($fetch_data['announcement_title']) && !empty($fetch_data['announcement_title'])) {
            $announcement_post['post_title'] = $fetch_data['announcement_title'];
        }
        if (isset($fetch_data['announcement_content']) && !empty($fetch_data['announcement_content'])) {
            $announcement_post['post_content'] = $fetch_data['announcement_content'];
        }
        // Update the post into the database
        wp_update_post( $announcement_post );

        if (isset($fetch_data['announcement_url']) && !empty($fetch_data['announcement_url'])) {
            update_post_meta($announcement_id, '_mvx_vendor_notices_url', $fetch_data['announcement_url']);
        }
        if (isset($fetch_data['announcement_vendors'])) {
            $notify_vendors = isset($fetch_data['announcement_vendors']) ? wp_list_pluck(array_filter($fetch_data['announcement_vendors']), 'value')  : get_mvx_vendors( array(), 'ids' );
            update_post_meta($announcement_id, '_mvx_vendor_notices_vendors', $notify_vendors);
        }
    }

    public function mvx_update_announcement_display($request) {
        global $MVX;
        $announcement_id = $request && $request->get_param('announcement_id') ? ($request->get_param('announcement_id')) : 0;
        $announcement_fields_data = mvx_admin_backend_settings_fields_details();
        $show_anouncemnt_vendor = [];
        if ($announcement_id && absint($announcement_id) > 0) {
            $post_details = get_post($announcement_id);
            $announcement_title =   $post_details->post_title;
            $announcement_url   =   get_post_meta($post_details->ID, '_mvx_vendor_notices_url', true) ? get_post_meta($post_details->ID, '_mvx_vendor_notices_url', true) : '';
            $announcement_content   =   $post_details->post_content;
            $announcement_vendors   =   get_post_meta($post_details->ID, '_mvx_vendor_notices_vendors', true) ? get_post_meta($post_details->ID, '_mvx_vendor_notices_vendors', true) : '';

            if ($this->mvx_show_vendor_name()->data && is_array($announcement_vendors)) {
                foreach ($this->mvx_show_vendor_name()->data as $announcement_key => $announcement_value) {
                    if ($announcement_value['value'] && in_array($announcement_value['value'], $announcement_vendors)) {
                        $show_anouncemnt_vendor[]  = $this->mvx_show_vendor_name()->data[$announcement_key];
                    }
                }
            }
        }

        $announcement_fields_data['update_announcement_display'] = [
            [
                'key'       => 'announcement_title',
                'type'      => 'text',
                'label'     => __( 'Title (required)', 'multivendorx' ),
                'props'     => array(
                    'required'  => true
                ),
                'database_value' => isset($announcement_title) ? $announcement_title : '',
            ],
            [
                'key'       => 'announcement_url',
                'type'      => 'url',
                'label'     => __( 'Enter Url', 'multivendorx' ),
                'props'     => array(
                ),
                'database_value' => isset($announcement_url) ? $announcement_url : '',
            ],
            [
                'label' => __('Enter Content', 'multivendorx'),
                'type' => get_mvx_vendor_settings('mvx_tinymce_api_section', 'settings_general') ? 'wpeditor' : 'textarea', 
                'key' => 'announcement_content', 
                'database_value' => $announcement_content
            ],
            [
                'key'       => 'announcement_vendors',
                'type'      => 'multi-select',
                'select_deselect'   =>  true,
                'label'     => __( 'Vendors', 'multivendorx' ),
                'options'   => ($MVX->vendor_rest_api->mvx_show_vendor_name()->data),
                'database_value' => isset($show_anouncemnt_vendor) ? $show_anouncemnt_vendor : '',
            ],
        ];

        return rest_ensure_response($announcement_fields_data);
    }

    public function mvx_list_of_all_tab_based_settings_field($request) {
        global $MVX;
        $vendor_id = $request && $request->get_param('vendor_id') ? ($request->get_param('vendor_id')) : 0;

        $woo_countries = new WC_Countries();
        $countries = $woo_countries->get_allowed_countries();
        $country_list = [];
        foreach ($countries as $countries_key => $countries_value) {
            $country_list[] = array(
                'label' => $countries_value,
                'value' => $countries_key
            );
        }
        // Find MVX created pages
        $pages = get_pages();
        $woocommerce_pages = array(wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
        $pages_array = array();
        if ($pages) {
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

        $default_nested_data = array(
            array(
                'nested_datas'  => array(
                    (Object)[]
                )
            )
        );

        $woo_countries = new WC_Countries();
        $countries = $woo_countries->get_allowed_countries();
        /*$country_list = [];
        foreach ($countries as $countries_key => $countries_value) {
            $country_list[] = array(
                'label' => $countries_value,
                'value' => $countries_key
            );
        }*/



        //vendor_country_code
        //vendor_state_code

        $user = null;
        $mvx_shipping_by_distance = $mvx_shipping_by_country = $vendor_default_shipping_options = '';
        $display_name_option = $shipping_options_list = $showdisplayname = $showpayment_method = array();

        if (isset($vendor_id) && absint($vendor_id) > 0) {
            $user = get_user_by("ID", $vendor_id);
                        
            // display name for vendor start

            if (isset($user->display_name)) {
                if ($user->user_login) {
                    $display_name_option[] = array(
                        'value'=> $user->user_login,
                        'label'=> $user->user_login,
                        'key'=> $user->user_login,
                    );
                }
                if ($user->first_name) {
                    $display_name_option[] = array(
                        'value'=> $user->first_name,
                        'label'=> $user->first_name,
                        'key'=> $user->first_name,
                    );
                }
                if ($user->last_name) {
                    $display_name_option[] = array(
                        'value'=> $user->last_name,
                        'label'=> $user->last_name,
                        'key'=> $user->last_name,
                    );
                }

                if ($user->first_name && $user->last_name) {
                    $display_name_option[] = array(
                        'value'=> $user->first_name . " " . $user->last_name,
                        'label'=> $user->first_name . " " . $user->last_name,
                        'key'=> $user->first_name . " " . $user->last_name,
                    );
                     $display_name_option[] = array(
                        'value'=> $user->last_name . " " . $user->first_name,
                        'label'=> $user->last_name . " " . $user->first_name,
                        'key'=> $user->last_name . " " . $user->first_name,
                    );
                }
            }

            foreach ($display_name_option as $display_key => $display_value) {
                if ($display_value['value'] && $display_value['value'] == $user->display_name) {
                    $showdisplayname[]  = $display_name_option[$display_key];
                }
            }

            // set option vendor payment method
            $payment_mode = array('payment_mode' => __('Payment Mode', 'multivendorx'));
            if (mvx_is_module_active('paypal-masspay')) {
                $payment_mode['paypal_masspay'] = __('PayPal Masspay', 'multivendorx');
            }
            if (mvx_is_module_active('paypal-payout')) {
                $payment_mode['paypal_payout'] = __('PayPal Payout', 'multivendorx');
            }
            if (mvx_is_module_active('stripe-connect')) {
                $payment_mode['stripe_masspay'] = __('Stripe Connect', 'multivendorx');
            }
            if (mvx_is_module_active('bank-payment')) {
                $payment_mode['direct_bank'] = __('Direct Bank', 'multivendorx');
            }
            $vendor_payment_mode_select = apply_filters('mvx_vendor_payment_mode', $payment_mode);
            $vendor_payment_method_display_section  =   array();
            foreach ($vendor_payment_mode_select as $selectkey => $selectvalue) {
                $vendor_payment_method_display_section[]    =   array(
                    'label' =>  $selectvalue,
                    'value' =>  $selectkey
                );
            }

            $payment_method = get_user_meta($vendor_id, '_vendor_payment_mode', true);
            foreach ($vendor_payment_method_display_section as $payment_key => $payment_value) {
                if ($payment_value['value'] && $payment_value['value'] == $payment_method) {
                    $showpayment_method  = $vendor_payment_method_display_section[$payment_key];
                }
            }

            $commission_value = get_user_meta($vendor_id, '_vendor_commission', true);
            $vendor_paypal_email = get_user_meta($vendor_id, '_vendor_paypal_email', true);

            $vendor_bank_name = get_user_meta($vendor_id, '_vendor_bank_name', true);
            $vendor_aba_routing_number = get_user_meta($vendor_id, '_vendor_aba_routing_number', true);
            $vendor_destination_currency = get_user_meta($vendor_id, '_vendor_destination_currency', true);
            $vendor_bank_address = get_user_meta($vendor_id, '_vendor_bank_address', true);
            $vendor_iban = get_user_meta($vendor_id, '_vendor_iban', true);
            $vendor_account_holder_name = get_user_meta($vendor_id, '_vendor_account_holder_name', true);
            $vendor_bank_account_number = get_user_meta($vendor_id, '_vendor_bank_account_number', true);


            $_vendor_shipping_policy = get_user_meta( $user->data->ID, '_vendor_shipping_policy', true ) ? get_user_meta( $user->data->ID, '_vendor_shipping_policy', true ) : __( 'No policy found', 'multivendorx' );
            $_vendor_refund_policy = get_user_meta( $user->data->ID, '_vendor_refund_policy', true ) ? get_user_meta( $user->data->ID, '_vendor_refund_policy', true ) : __( 'No policy found', 'multivendorx' );
            $_vendor_cancellation_policy = get_user_meta( $user->data->ID, '_vendor_cancellation_policy', true ) ? get_user_meta( $user->data->ID, '_vendor_cancellation_policy', true ) : __( 'No policy found', 'multivendorx' );


            $vendor_phone = get_user_meta( $user->data->ID, '_vendor_phone', true ) ? get_user_meta( $user->data->ID, '_vendor_phone', true ) : '';
            $vendor_description = get_user_meta( $user->data->ID, '_vendor_description', true ) ? get_user_meta( $user->data->ID, '_vendor_description', true ) : '';
            $vendor_address_1 = get_user_meta( $user->data->ID, '_vendor_address_1', true ) ? get_user_meta( $user->data->ID, '_vendor_address_1', true ) : '';
            $vendor_address_2 = get_user_meta( $user->data->ID, '_vendor_address_2', true ) ? get_user_meta( $user->data->ID, '_vendor_address_2', true ) : '';
            $vendor_city = get_user_meta( $user->data->ID, '_vendor_city', true ) ? get_user_meta( $user->data->ID, '_vendor_city', true ) : '';
            $vendor_postcode = get_user_meta( $user->data->ID, '_vendor_postcode', true ) ? get_user_meta( $user->data->ID, '_vendor_postcode', true ) : '';

            $vendor_profile_image = get_user_meta( $user->data->ID, '_vendor_profile_image', true ) ? get_user_meta( $user->data->ID, '_vendor_profile_image', true ) : '';


            $vendor_country_code = get_user_meta( $user->data->ID, '_vendor_country_code', true ) ? get_user_meta( $user->data->ID, '_vendor_country_code', true ) : '';
            $vendor_state_code = get_user_meta( $user->data->ID, '_vendor_state_code', true ) ? get_user_meta( $user->data->ID, '_vendor_state_code', true ) : '';

            // display country value from database
            $vendor_country_code_data = $vendor_state_code_data = array();
            foreach ($country_list as $display_country_key => $display_country_value) {
                if ($display_country_value['value'] && $display_country_value['value'] == $vendor_country_code) {
                    $vendor_country_code_data[]  = $country_list[$display_country_key];
                }
            }

            // display state value from database
            $state_list = wc_clean( wp_unslash( WC()->countries->get_states($vendor_country_code) ) );
            if ($state_list) {
                foreach ($state_list as $display_state_key => $display_state_value) {
                    if ($display_state_key && $display_state_key == $vendor_state_code) {
                        $vendor_state_code_data[]  = [
                            'label' => $display_state_value,
                            'value' => $display_state_key
                        ];
                    }
                }
            }
            

            $user_vendor = get_mvx_vendor($user->data->ID);

            $current_offset = get_user_meta($user->data->ID, 'gmt_offset', true);
            $tzstring = get_user_meta($user->data->ID, 'timezone_string', true);
            // Remove old Etc mappings. Fallback to gmt_offset.
            if (false !== strpos($tzstring, 'Etc/GMT')) {
                $tzstring = '';
            }


            $vendor_fb_profile = get_user_meta($user->data->ID, '_vendor_fb_profile', true) ? get_user_meta($user->data->ID, '_vendor_fb_profile', true) : '';
            $vendor_twitter_profile = get_user_meta($user->data->ID, '_vendor_twitter_profile', true) ? get_user_meta($user->data->ID, '_vendor_twitter_profile', true) : '';
            $vendor_linkdin_profile = get_user_meta($user->data->ID, '_vendor_linkdin_profile', true) ? get_user_meta($user->data->ID, '_vendor_linkdin_profile', true) : '';
            $vendor_youtube_profile = get_user_meta($user->data->ID, '_vendor_youtube', true) ? get_user_meta($user->data->ID, '_vendor_youtube', true) : '';
            $vendor_instagram_profile = get_user_meta($user->data->ID, '_vendor_instagram', true) ? get_user_meta($user->data->ID, '_vendor_instagram', true) : '';

            $store_lat = get_user_meta($user->data->ID, '_store_lat', true) ? get_user_meta($user->data->ID, '_store_lat', true) : '';
            $store_lng = get_user_meta($user->data->ID, '_store_lng', true) ? get_user_meta($user->data->ID, '_store_lng', true) : '';

            if (empty($tzstring)) { // Create a UTC+- zone if no timezone string exists
                $check_zone_info = false;
                if (0 == $current_offset) {
                    $tzstring = 'UTC+0';
                } elseif ($current_offset < 0) {
                    $tzstring = 'UTC' . $current_offset;
                } else {
                    $tzstring = 'UTC+' . $current_offset;
                }
            }

            // Shipping options
            
            $vendor_default_shipping_options_database_value = get_user_meta($vendor_id, 'vendor_shipping_options', true) ? get_user_meta($vendor_id, 'vendor_shipping_options', true) : '';
            $shipping_options = [];
            
            if (mvx_is_module_active('zone-shipping')) {
                $shipping_options['distance_by_zone'] = __('Shipping by Zone', 'multivendorx');
            }
            if (mvx_is_module_active('distance-shipping')) {
                $shipping_options['distance_by_shipping'] = __('Shipping by Distance', 'multivendorx');
            }
            if (mvx_is_module_active('country-shipping')) {
                $shipping_options['shipping_by_country'] = __('Shipping by Country', 'multivendorx');
            }
            foreach ($shipping_options as $shipping_key => $shipping_value) {
                $shipping_options_list[] = array(
                    'value' => sanitize_text_field($shipping_key),
                    'label' => sanitize_text_field($shipping_value)
                );
            }

            $vendor_default_shipping_options = array();
            foreach ($shipping_options_list as $key => $value) {
                if ($value['value'] == $vendor_default_shipping_options_database_value) {
                    $vendor_default_shipping_options = $shipping_options_list[$key];
                }
            }

            $shipping_distance_rate = mvx_get_user_meta( $vendor_id, '_mvx_shipping_by_distance_rates', true ) ? mvx_get_user_meta( $vendor_id, '_mvx_shipping_by_distance_rates', true ) : $default_nested_data;

            $mvx_shipping_by_distance = mvx_get_user_meta( $vendor_id, '_mvx_shipping_by_distance', true ) ? get_user_meta( $vendor_id, '_mvx_shipping_by_distance', true ) : array();

            $mvx_shipping_by_country = mvx_get_user_meta( $vendor_id, '_mvx_shipping_by_country', true ) ? mvx_get_user_meta( $vendor_id, '_mvx_shipping_by_country', true ) : '';

            $shipping_country_rate = mvx_get_user_meta( $vendor_id, '_mvx_country_shipping_rates', true ) ? mvx_get_user_meta( $vendor_id, '_mvx_country_shipping_rates', true ) : $default_nested_data;

        }

        $settings_fields_data = mvx_admin_backend_settings_fields_details();

        $settings_fields_data['user_ID']   =  [
            'userid'    =>  $vendor_id
        ];
        $settings_fields_data['vendor_default_shipping_options']   = $vendor_default_shipping_options;
        $settings_fields_data['shipping_options']  = $shipping_options_list;
        $is_block = get_user_meta($vendor_id, '_vendor_turn_off', true);
        $settings_fields_data['vendor-personal'] =   [
            [
                'key'       => 'user_login',
                'type'      => 'text',
                'label'     => __( 'Username (required)', 'multivendorx' ),
                'desc' => __('Usernames cannot be changed.', 'multivendorx'),
                'props'     => array(
                    'required'  => true,
                    'disabled'  => true
                ),
                'database_value' => isset($user->user_login) ? $user->user_login : '',
            ],
            [
                'key'       => 'password',
                'type'      => 'password',
                'label'     => __( 'Password', 'multivendorx' ),
                'desc'     => __('Keep it blank for not to update.', 'multivendorx'),
                'props'     => array(
                    'required'  => true
                ),
                'database_value' => '$P$BBy/qPu84qp8n2TQ.fFhyJNoru.orT1',
            ],
            [
                'key'       => 'first_name',
                'type'      => 'text',
                'label'     => __( 'First Name', 'multivendorx' ),
                'database_value' => isset($user->first_name) ? $user->first_name : '',
            ],
            [
                'key'       => 'last_name',
                'type'      => 'text',
                'label'     => __( 'Last Name', 'multivendorx' ),
                'database_value' => isset($user->last_name) ? $user->last_name : '',
            ],
            [
                'key'       => 'user_email',
                'type'      => 'email',
                'label'     => __( 'Email (required)', 'multivendorx' ),
                'props'     => array(
                    'required'  => true
                ),
                'database_value' => isset($user->user_email) ? $user->user_email : '',
            ],
            [
                'key'       => 'user_nicename',
                'type'      => 'text',
                'label'     => __( 'Nick Name (required)', 'multivendorx' ),
                'props'     => array(
                    'required'  => true
                ),
                'database_value' => isset($user->user_nicename) ? $user->user_nicename : '',
            ],
            [
                'key'       => 'display_name',
                'type'      => 'select',
                'label'     => __( 'Display name', 'multivendorx' ),
                'desc'      => __( '', 'multivendorx' ),
                'options' => $display_name_option,
                'restricted_page'   => '?page=vendors&name=add_new',
                'database_value' => isset($showdisplayname) ? $showdisplayname : '',
            ],
            [
                'key'    => 'vendor_profile_image',
                'label'   => __( 'Profile Image', 'multivendorx' ),
                'type'    => 'file',
                'width' =>  75,
                'height'    => 75,
                'database_value' => isset($vendor_profile_image) ? $vendor_profile_image : '',
            ],
            [
                'key'       => 'vendor_active_suspend_button',
                'label'     => 'no_label',
                'type'      => 'button',
                'api_link'  => 'mvx_module/v1/active_suspend_vendor',
                'vendor_status' => $is_block ? 'activate' : 'suspend',
                'vendor_status_label' => $is_block ? __('Activate', 'multivendorx') : __('Suspend', 'multivendorx'),
                'vendor_id' => $vendor_id,
                'database_value' => array(),
            ],
            
        ];
        
        $settings_fields_data['vendor-store'] =   [
            [
                'label' => __('Store Name *', 'multivendorx'),
                'type' => 'text',
                'key' => 'vendor_page_title',
                'database_value' => isset($user_vendor->page_title) ? $user_vendor->page_title : '' 
            ],
            [
                'label' => __('Store Slug *', 'multivendorx'),
                'type' => 'text',
                'key' => 'vendor_page_slug',
                'desc' => sprintf(__('Store URL will be something like - %s', 'multivendorx'), trailingslashit(get_home_url()) . 'vendor_slug'),
                'database_value' => isset($user_vendor->page_slug) ? $user_vendor->page_slug : '',
            ],
            [
                'key'    => 'vendor_image',
                'label'   => __( 'Store Logo', 'multivendorx' ),
                'type'    => 'file',
                'width' =>  75,
                'height'    => 75,
                'database_value' => $user_vendor->get_image() ? $user_vendor->get_image() : '',
            ],
            [
                'key'    => 'vendor_banner',
                'label'   => __( 'Store Banner', 'multivendorx' ),
                'type'    => 'file',
                'width' =>  520,
                'height'    => 200,
                'database_value' => $user_vendor->get_image('banner') ? $user_vendor->get_image('banner') : $MVX->plugin_url . 'assets/images/banner_placeholder.jpg',
            ],
            [
                'label' => __('Store Description', 'multivendorx'),
                'type' => 'textarea', 
                'key' => 'vendor_description', 
                'database_value' => isset($vendor_description) ? $vendor_description : ''
            ],
            [
                'label' => __('Phone', 'multivendorx'), 
                'type' => 'number', 
                'key' => 'vendor_phone', 
                'database_value' => isset($vendor_phone) ? $vendor_phone : ''
            ],
            [
                'label' => __('Address', 'multivendorx'), 
                'type' => 'text', 
                'key' => 'vendor_address_1', 
                'database_value' => isset($vendor_address_1) ? $vendor_address_1 : ''
            ],
            [
                'label' => '', 
                'type' => 'text', 
                'key' => 'vendor_address_2', 
                'database_value' => isset($vendor_address_2) ? $vendor_address_2 : ''
            ],
            [
                'label' => __('Country', 'multivendorx'), 
                'type' => 'country', 
                'key' => 'vendor_country', 
                'class' => 'country_to_state regular-text', 
                'options' => $country_list, 
                'database_value' => isset($vendor_country_code_data) ? $vendor_country_code_data : ''
            ],
            [
                'label' => __('State', 'multivendorx'), 
                'type' => 'state', 
                'key' => 'vendor_state', 
                'class' => 'regular-text', 
                'options' => array(), 
                'database_value' => isset($vendor_state_code_data) ? $vendor_state_code_data : ''
            ],
            [
                'label' => __('City', 'multivendorx'), 
                'type' => 'text', 
                'key' => 'vendor_city', 
                'database_value' => isset($vendor_city) ? $vendor_city : ''
            ],
            [
                'label' => __('ZIP code', 'multivendorx'), 
                'type' => 'text', 
                'key' => 'vendor_postcode', 
                'database_value' => isset($vendor_postcode) ? $vendor_postcode : ''
            ],
            [
                'label' => __('Timezone', 'multivendorx'),
                'type' => 'text', 
                'key' => 'timezone_string',
                'props'     => array(
                    'disabled'  => true
                ),
                'database_value' => isset($tzstring) ? $tzstring : '', 
            ],
        ];
        if (mvx_is_module_active('store-location') && get_mvx_vendor_settings('enable_store_map_for_vendor', 'store')) {
            $settings_fields_data['vendor-store'][] =   [
                'label' => __('Vendor Map', 'multivendorx'),
                'type' => 'google_map',
                'key' => 'vendor_store_map',
                'center'    =>  array('lat' => isset($store_lat) ? floatval($store_lat) : '', 'lng' => isset($store_lng) ? floatval($store_lng) : '' ),
                'store_lat' =>  isset($store_lat) ? floatval($store_lat) : '',
                'store_lng' =>  isset($store_lng) ? floatval($store_lng) : '',
                'database_value' => '', 
            ];
        }

        $settings_fields_data['vendor-social'] =   [
            [
                'label' => __('Facebook', 'multivendorx'), 
                'type' => 'url', 
                'key' => 'vendor_fb_profile', 
                'database_value' => isset($vendor_fb_profile) ? $vendor_fb_profile : ''
            ],
            [
                'label' => __('Twitter', 'multivendorx'), 
                'type' => 'url', 
                'key' => 'vendor_twitter_profile', 
                'database_value' => isset($vendor_twitter_profile) ? $vendor_twitter_profile : ''
            ],
            [
                'label' => __('LinkedIn', 'multivendorx'), 
                'type' => 'url', 
                'key' => 'vendor_linkdin_profile', 
                'database_value' => isset($vendor_linkdin_profile) ? $vendor_linkdin_profile : ''
            ],
            [
                'label' => __('YouTube', 'multivendorx'), 
                'type' => 'url', 
                'key' => 'vendor_youtube', 
                'database_value' => isset($vendor_youtube_profile) ? $vendor_youtube_profile : ''
            ],
            [
                'label' => __('Instagram', 'multivendorx'), 
                'type' => 'url', 
                'key' => 'vendor_instagram', 
                'database_value' => isset($vendor_instagram_profile) ? $vendor_instagram_profile : ''
            ],
        ];

        $settings_fields_data['vendor-payments'] =   [
                [
                    'key'       => 'vendor_payment_mode',
                    'type'      => 'select',
                    'label'     => __( 'Choose Payment Method', 'multivendorx' ),
                    'options'   => isset($vendor_payment_method_display_section) ? $vendor_payment_method_display_section : array(),
                    'database_value' => isset($showpayment_method) ? $showpayment_method : '',
                ],
                [
                    'label' => __('Commission Amount', 'multivendorx'), 
                    'type' => 'number', 
                    'key' => 'vendor_commission',
                    'placeholder' => '0.00',
                    'database_value' => isset($commission_value) ? $commission_value : '',
                    'desc'  =>  'To set vendor commission as 0 pass "0" in the Commission Amount filed.'
                ],
                [
                    'label' => __('Paypal Email', 'multivendorx'), 
                    'type' => 'text', 
                    'key' => 'vendor_paypal_email',
                    'placeholder' => '0.00',
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'paypal_masspay',
                    'database_value' => isset($vendor_paypal_email) ? $vendor_paypal_email : ''
                ],
                [
                    'label' => __('Paypal Email', 'multivendorx'), 
                    'type' => 'text', 
                    'key' => 'vendor_paypal_email',
                    'placeholder' => '0.00',
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'paypal_payout',
                    'database_value' => isset($vendor_paypal_email) ? $vendor_paypal_email : ''
                ],

               /* [
                    'label' => __('Account type', 'multivendorx'), 
                    'type' => 'select', 
                    'key' => 'vendor_bank_account_type', 
                    'label_for' => 'vendor_bank_account_type', 
                    'name' => 'vendor_bank_account_type', 
                    'options' => $vendor_bank_account_type_select, 
                    'database_value' => $vendor_obj->bank_account_type, 
                ],*/
                [
                    'label' => __('Bank Name', 'multivendorx'), 
                    'type' => 'text',
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'direct_bank',
                    'key' => 'vendor_bank_name', 
                    'database_value' => isset($vendor_bank_name) ? $vendor_bank_name : '' 
                ],

                [
                    'label' => __('ABA Routing Number', 'multivendorx'), 
                    'type' => 'text',
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'direct_bank',
                    'key' => 'vendor_aba_routing_number', 
                    'database_value' => isset($vendor_aba_routing_number) ? $vendor_aba_routing_number : ''
                ],

                [
                    'label' => __('Destination Currency', 'multivendorx'), 
                    'type' => 'text',
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'direct_bank',
                    'key' => 'vendor_destination_currency', 
                    'database_value' => isset($vendor_destination_currency) ? $vendor_destination_currency : ''
                ],
                [
                    'label' => __('Bank Address', 'multivendorx'), 
                    'type' => 'textarea', 
                    'key' => 'vendor_bank_address', 
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'direct_bank',
                    'rows'=>'6', 
                    'cols'=>'53', 
                    'database_value' => isset($vendor_bank_address) ? $vendor_bank_address : ''
                ],
                [
                    'label' => __('IBAN', 'multivendorx'), 
                    'type' => 'text',
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'direct_bank',
                    'key' => 'vendor_iban', 
                    'database_value' => isset($vendor_iban) ? $vendor_iban : ''
                ],
                [
                    'label' => __('Account Holder Name', 'multivendorx'), 
                    'type' => 'text',
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'direct_bank',
                    'key' => 'vendor_account_holder_name', 
                    'database_value' => isset($vendor_account_holder_name) ? $vendor_account_holder_name : ''
                ],
                [
                    'label' => __('Account Number', 'multivendorx'), 
                    'type' => 'text',
                    'depend'    => 'vendor_payment_mode',
                    'dependvalue'       =>  'direct_bank',
                    'key' => 'vendor_bank_account_number', 
                    'database_value' => isset($vendor_bank_account_number) ? $vendor_bank_account_number : ''
                ],
        ];

        $settings_fields_data['vendor-policy'] =   [
            [
                'label' => __('Shipping Policy', 'multivendorx'), 
                'type' => 'textarea', 
                'key' => 'vendor_shipping_policy', 
                'database_value' => isset($_vendor_shipping_policy) ? $_vendor_shipping_policy : ''
            ],
            [
                'label' => __('Refund Policy', 'multivendorx'), 
                'type' => 'textarea', 
                'key' => 'vendor_refund_policy', 
                'database_value' => isset($_vendor_refund_policy) ? $_vendor_refund_policy : ''
            ],
            [
                'label' => __('Cancellation/Return/Exchange Policy', 'multivendorx'),
                 'type' => 'textarea', 
                 'key' => 'vendor_cancellation_policy', 
                 'database_value' => isset($_vendor_cancellation_policy) ? $_vendor_cancellation_policy : ''
             ],
        ];

        $settings_fields_data['distance-shipping'] =   [
            [
                'label' => __('Default Cost', 'multivendorx'), 
                'type' => 'text', 
                'key' => 'mvx_byd_default_cost',
                'placeholder' => '0.00',
                'database_value' => isset($mvx_shipping_by_distance['_default_cost']) ? $mvx_shipping_by_distance['_default_cost'] : ''
            ],
            [
                'label' => __('Max Distance (km)', 'multivendorx'), 
                'type' => 'text',
                'placeholder' => __('No Limit', 'multivendorx'), 
                'key' => 'mvx_byd_max_distance', 
                'database_value' => isset($mvx_shipping_by_distance['_max_distance']) ? $mvx_shipping_by_distance['_max_distance'] : ''
            ],
            /*[
                'label' => __('Enable Local Pickup', 'multivendorx'), 
                'type' => 'checkbox', 
                'key' => 'mvx_byd_enable_local_pickup',
                'options' => array(
                    array(
                        'key'=> "mvx_byd_enable_local_pickup",
                        'label'=> __('', 'multivendorx'),
                        'value'=> "mvx_byd_enable_local_pickup"
                    ),
                ),
                'database_value' => isset($mvx_shipping_by_distance['_enable_local_pickup']) ? $mvx_shipping_by_distance['_enable_local_pickup'] : ''
            ],*/
            [
                'label' => __('Local Pickup Cost', 'multivendorx'), 
                'type' => 'text', 
                'key' => 'mvx_byd_local_pickup_cost', 
                'placeholder' => '0.00',
                'database_value' => isset($mvx_shipping_by_distance['_local_pickup_cost']) ? $mvx_shipping_by_distance['_local_pickup_cost'] : ''
            ],
            [
                'key'       => 'mvx_shipping_by_distance_rates',
                'type'      => 'nested',
                'label'     => __( 'Distance-Cost Rules:', 'multivendorx' ),
                'parent_options' => array(
                    array(
                        'key'=>'mvx_distance_rule',
                        'type'=> "select",
                        'class' => "nested-parent-class",
                        'name' => "nested-parent-name",
                        'label'=> __('Distance Rule', 'multivendorx'),
                        'options' => array(
                            array(
                                'key'=> "up_to",
                                'label'=> __('Distance up to', 'multivendorx'),
                                'value'=> __('up_to', 'multivendorx'),
                            ),
                            array(
                                'key'=> "more_than",
                                'label'=> __('Distance more than', 'multivendorx'),
                                'value'=> __('more_than', 'multivendorx'),
                            ),
                        ),
                    ),
                    array(
                        'key'   => 'mvx_distance_unit',
                        'type'  => "text",
                        'class' => "nested-parent-class",
                        'name'  => "nested-parent-name",
                        'label' => __('Distance', 'multivendorx') . ' ( '. __('km', 'multivendorx') .' )', 
                    ),
                    array(
                        'key'   => 'mvx_distance_price',
                        'type'  => "text",
                        'class' => "nested-parent-class",
                        'name'  => "nested-parent-name",
                        'label' => __('Cost', 'multivendorx') . ' ('.get_woocommerce_currency_symbol().')',
                    ),
                ),
                'child_options' => array(
                    
                ),
                'database_value' => isset($vendor_id) ? $shipping_distance_rate : $default_nested_data,
            ]
        ];

        $settings_fields_data['country-shipping'] =   [
                [
                    'label' => __('Default Shipping Price', 'multivendorx'), 
                    'placeholder' => '0.00', 
                    'type' => 'text', 
                    'class' => 'col-md-6 col-sm-9', 
                    'key' => 'mvx_shipping_type_price', 
                    'database_value' => isset($mvx_shipping_by_country['_mvx_shipping_type_price']) ? $mvx_shipping_by_country['_mvx_shipping_type_price'] : '', 
                ],

                [
                    'label' => __('Per Product Additional Price', 'multivendorx'), 
                    'placeholder' => '0.00', 
                    'type' => 'text', 
                    'class' => 'col-md-6 col-sm-9', 
                    'key' => 'mvx_additional_product', 
                    'database_value' => isset($mvx_shipping_by_country['_mvx_additional_product']) ? $mvx_shipping_by_country['_mvx_additional_product'] : '',
                    'desc' => __('If a customer buys more than one type product from your store, first product of the every second type will be charged with this price', 'multivendorx') 
                ],

                [
                    'label' => __('Per Qty Additional Price', 'multivendorx'), 
                    'placeholder' => '0.00', 
                    'type' => 'text', 
                    'class' => 'col-md-6 col-sm-9', 
                    'key' => 'mvx_additional_qty', 
                    'database_value' => isset($mvx_shipping_by_country['_mvx_additional_qty']) ? $mvx_shipping_by_country['_mvx_additional_qty'] : '', 
                    'hints' => __('Every second product of same type will be charged with this price', 'multivendorx'),
                ],

                [
                    'label' => __('Free Shipping Minimum Order Amount', 'multivendorx'), 
                    'placeholder' => __( 'NO Free Shipping', 'multivendorx'), 
                    'type' => 'text', 
                    'class' => 'col-md-6 col-sm-9', 
                    'key' => 'mvx_byc_free_shipping_amount', 
                    'database_value' => isset($mvx_shipping_by_country['_free_shipping_amount']) ? $mvx_shipping_by_country['_free_shipping_amount'] : '', 
                    'hints' => __('Free shipping will be available if order amount more than this. Leave empty to disable Free Shipping.', 'multivendorx') 
                ],

                /*[
                    'label' => __('Enable Local Pickup', 'multivendorx'), 
                    'type' => 'checkbox', 
                    'class' => 'mvx-checkbox mvx_ele', 
                    'key' => 'mvx_byc_enable_local_pickup', 
                    'options' => array(
                        array(
                            'key'=> "mvx_byc_enable_local_pickup",
                            'label'=> __('', 'multivendorx'),
                            'value'=> "mvx_byc_enable_local_pickup"
                        ),
                    ),
                    'database_value' => isset($mvx_shipping_by_country['_enable_local_pickup']) ? $mvx_shipping_by_country['_enable_local_pickup'] : '' 
                ],*/

                [
                    'label' => __('Local Pickup Cost', 'multivendorx'), 
                    'placeholder' => '0.00', 
                    'type' => 'text', 
                    'class' => 'col-md-6 col-sm-9', 
                    'key' => 'mvx_byc_local_pickup_cost', 
                    'database_value' => isset($mvx_shipping_by_country['_local_pickup_cost']) ? $mvx_shipping_by_country['_local_pickup_cost'] : '' 
                ],

                [
                    'key'       => 'mvx_country_shipping_rates',
                    'type'      => 'nested',
                    'label'     => __( 'Shipping Rates by Country', 'multivendorx' ),
                    'desc' => __( 'Add the countries you deliver your products to. You can specify states as well. If the shipping price is same except some countries, there is an option Everywhere Else, you can use that.', 'multivendorx' ),
                    'parent_options' => array(
                        array(
                            'key'       =>'mvx_country_to',
                            'type'      => "country",
                            'class'     => "nested-parent-class",
                            'name'      => "nested-parent-name",
                            'label'     => __('Country', 'multivendorx'),
                            'options'   => $country_list
                        ),
                        array(
                            'key'           => 'mvx_country_to_price',
                            'type'          => "text",
                            'class'         => "nested-parent-class",
                            'name'          => "nested-parent-name",
                            'placeholder'   => '0.00 (' . __('Free Shipping', 'multivendorx') . ')',
                            'label'         => __('Cost', 'multivendorx') . ' ('.get_woocommerce_currency_symbol().')',
                        ),
                    ),
                    'child_options' => array(
                        array(
                            'key'       =>'mvx_state_to',
                            'type'      => "state",
                            'class'     => "nested-parent-class",
                            'name'      => "nested-parent-name",
                            'label'     => __('State', 'multivendorx'),
                            'options'   => array()
                        ),
                        array(
                            'key'   => 'mvx_state_to_price',
                            'type'  => "text",
                            'class' => "nested-parent-class",
                            'name'  => "nested-parent-name",
                            'placeholder' => '0.00 (' . __('Free Shipping', 'multivendorx') . ')',
                            'label' => __('Cost', 'multivendorx') . ' ('.get_woocommerce_currency_symbol().')',
                        ),
                    ),
                    'database_value' => isset($vendor_id) ? $shipping_country_rate : $default_nested_data,
                ]
        ];

        $settings_fields_data['vendor-application'] =   [];

        $settings_fields_data['vendor-shipping'] =   [];

        $settings_fields_data['vendor-followers'] =   [];
        
        return rest_ensure_response(apply_filters('mvx_vendors_tab_routes', $settings_fields_data, $vendor_id));
    }

    public function mvx_create_announcement($request) {
        $all_details = [];
        $fetch_data = $request->get_param('model');

        $announcement_title = $fetch_data && isset($fetch_data['announcement_title']) ? $fetch_data['announcement_title'] : '';
        $announcement_url = $fetch_data && isset($fetch_data['announcement_url']) ? $fetch_data['announcement_url'] : '';
        $announcement_content = $fetch_data && isset($fetch_data['announcement_content']) ? $fetch_data['announcement_content'] : '';
        $announcement_vendors = $fetch_data && isset($fetch_data['announcement_vendors']) ? $fetch_data['announcement_vendors'] : '';
        $notice_id = wp_insert_post( array( 'post_title' => $announcement_title, 'post_type' => 'mvx_vendor_notice', 'post_status' => 'publish', 'post_content' => $announcement_content ) );
        $post = get_post($notice_id);
        update_post_meta( $notice_id, '_mvx_vendor_notices_url', wc_clean($announcement_url) );
        $email_vendor = WC()->mailer()->emails['WC_Email_Vendor_New_Announcement'];
        $notify_vendors = isset($fetch_data['announcement_vendors']) && !empty($fetch_data['announcement_vendors']) ? wp_list_pluck(array_filter($fetch_data['announcement_vendors']), 'value')  : get_mvx_vendors( array(), 'ids' );
        if (isset($fetch_data['announcement_vendors']) && !empty($fetch_data['announcement_vendors'])) {
            update_post_meta($notice_id, '_mvx_vendor_notices_vendors', $notify_vendors);
            // send mail
            $single = ( count($notify_vendors) == 1 ) ? __( 'Your', 'multivendorx' ) : __( 'All vendors and their', 'multivendorx' );
            foreach ($notify_vendors as $vendor_id) {
                $email_vendor->trigger( $post, $vendor_id, $single );
            }
        } else {
            update_post_meta($notice_id, '_mvx_vendor_notices_vendors', get_mvx_vendors( array(), 'ids' ));
            // send mail
            $single = ( count(get_mvx_vendors( array(), 'ids' )) == 1 ) ? __( 'Your', 'multivendorx' ) : __( 'All vendors and their', 'multivendorx' );
            foreach (get_mvx_vendors( array(), 'ids' ) as $vendor_id) {
                $email_vendor->trigger( $post, $vendor_id, $single );
            }
        }

        $all_details['redirect_link'] = admin_url('admin.php?page=mvx#&submenu=work-board&name=announcement&AnnouncementID='. $notice_id .'');
        return $all_details;
    }

    public function mvx_display_announcement($request) {
        $status = $request && $request->get_param('status') ? $request->get_param('status') : '';
        $status = $status == 'all' ? array('publish', 'auto-draft', 'pending') : $status;
        $announcement_list = $vedors_list_renew = array();
        $args = array(
            'post_type' => 'mvx_vendor_notice',
            'post_status' => $status ? $status : array('publish', 'auto-draft', 'pending'),
            'posts_per_page' => -1,
        );
        $announcement = get_posts($args);

        foreach ($announcement as $announcementkey => $announcementvalue) {
            $vedors_list_renew = [];
            $vedors_list = get_post_meta($announcementvalue->ID, '_mvx_vendor_notices_vendors', true);

            if ($vedors_list && is_array($vedors_list) && !empty($vedors_list)) {
                foreach ($vedors_list as $key => $value) {
                    $vendor = get_mvx_vendor($value);
                    if ($vendor) {
                        $vedors_list_renew[] = $vendor->page_title;
                    }
                }
            }

            $announcement_list[] = array(
                'id'            =>  $announcementvalue->ID,
                'sample_title'  =>  $announcementvalue->post_title,
                'title'         =>  '<a href="' . sprintf('?page=%s&name=%s&AnnouncementID=%s', 'mvx#&submenu=work-board', 'announcement', $announcementvalue->ID) . '">' . $announcementvalue->post_title . '</a>',
                'date'          =>  human_time_diff(strtotime($announcementvalue->post_modified)),
                'vendor'        =>  $vedors_list_renew ? implode(',', $vedors_list_renew) : '',
                'link'          =>  sprintf('?page=%s&name=%s&AnnouncementID=%s', 'mvx#&submenu=work-board', 'announcement', $announcementvalue->ID),
                'type'          =>  'post_announcement',
            );
        }
        return rest_ensure_response($announcement_list);
    }

    public function mvx_fetch_report_overview_data() {
        return $this->mvx_report_data('');
    }

    public function mvx_get_report_overview_data($request) {
        return $this->mvx_report_data($request);
    }

    public function mvx_report_data($request) {
        global $MVX;
        // get date value from datepicker
        $value = $request && $request->get_param('value') ? ($request->get_param('value')) : 0;
        $product = $request && $request->get_param('product') ? ($request->get_param('product')) : 0;
        $find_1st_vendor_from_rest = $this->mvx_vendor_list_search()->data ? $this->mvx_vendor_list_search()->data[0]['value'] : 0;
        $selectvendor = $request && $request->get_param('vendor') ? ($request->get_param('vendor')) : 0;
        $status_sales = $request && $request->get_param('status_sales') ? $request->get_param('status_sales') : '';

        // Bydefault last 7 days
        $start_date    = strtotime( '-7 days', strtotime( 'midnight', current_time( 'timestamp' ) ) );
        $end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );

        if ($value) {
            $initial_start = $value ? $value[0] : '';
            $initial_end = $value ? $value[1] : '';
            $start_date = max( strtotime( '-20 years' ), strtotime( sanitize_text_field( $initial_start ) ) );
            $end_date = strtotime( 'midnight', strtotime( sanitize_text_field( $initial_end ) ) );
        }

        $end_date = strtotime('+1 day', $end_date);


        /** *Report overview* **/
        $overview_sales = $gross_sales = $vendor_earning = $overview_admin_earning = $pending_vendors = $overview_vendors = $products = $transactions = $report_product_net_sales = $total_item_sold = $report_vendor_net_sales = 0;
        $report_html = '';
        $product_number_stack = $total_orders_product_chart = $product_sales_data_chart = $product_item_sold_chart = $total_number_order_data_chart = $net_sales_data_chart = $total_sales = $banking_datas = array();

        // transaction history
        if ( $selectvendor ) {
            $requestData = array('from_date'=> date("Y-m-d", $start_date) , 'to_date' => date("Y-m-d", $end_date) );

            $data_store = $MVX->ledger->load_ledger_data_store();
            $vendor_all_ledgers = apply_filters('mvx_admin_report_banking_data', $data_store->get_ledger( array( 'vendor_id' => $selectvendor ), '', $requestData )); 
            if ( !empty( $vendor_all_ledgers ) ) {
                foreach ($vendor_all_ledgers as $ledger ) {
                    // total credited balance
                    $total_credit += floatval( $ledger->credit );
                    // total debited balance
                    $total_debit += floatval( $ledger->debit );
                    $order = wc_get_order( $ledger->order_id );
                    $currency = ( $order ) ? $order->get_currency() : '';
                    $ref_types = get_mvx_ledger_types();
                    $ref_type = isset($ref_types[$ledger->ref_type]) ? $ref_types[$ledger->ref_type] : ucfirst( $ledger->ref_type );
                    $type = '<mark class="type ' . $ledger->ref_type . '"><span>' . $ref_type . '</span></mark>';
                    $status = $ledger->ref_status;
                    if ($ref_type == 'Commission') {
                        $link = admin_url('post.php?post=' . $ledger->order_id . '&action=edit');
                        $ref_link = '<a href="'.esc_url($link).'">#'.$ledger->order_id.'</a>';
                    } elseif ($ref_type == 'Refund' && $ref_type == 'Withdrawal') {
                        $com_id = get_post_meta( $ledger->order_id, '_commission_id', true );
                        $link = admin_url('post.php?post=' . $com_id . '&action=edit');
                        $ref_link = '<a href="'.esc_url($link).'">#'.$com_id.'</a>';
                    }
                    $credit = ( $ledger->credit ) ? wc_price($ledger->credit, array('currency' => $currency)) : '';
                    $debit = ( $ledger->debit ) ? wc_price($ledger->debit, array('currency' => $currency)) : '';
                    $banking_datas[] = apply_filters( 'mvx_admin_report_banking_details', array( 
                        'status' => ucfirst( $status ), 
                        'date' => mvx_date($ledger->created), 
                        'type' => $ref_type, 
                        'reference_id' => $ref_link, 
                        'Credit' => $credit, 
                        'Debit' => $debit, 
                        'balance' => wc_price($ledger->balance, array('currency' => $currency))
                    ), $ledger );
                }
            }
        }


        $args_overview = apply_filters('mvx_report_admin_overview_query_args', array(
                'post_type' => 'shop_order',
                'posts_per_page' => -1,
                'post_parent' => 0,
                'post_status' => array('wc-processing', 'wc-completed'),
                'date_query' => array(
                    'inclusive' => true,
                    'after' => array(
                        'year' => date('Y', $start_date),
                        'month' => date('n', $start_date),
                        'day' => date('j', $start_date), //date('1'),
                    ),
                    'before' => array(
                        'year' => date('Y', $end_date),
                        'month' => date('n', $end_date),
                        'day' => date('j', $end_date),
                    ),
                )
            ));


        $qry = new WP_Query($args_overview);
        $orders_overview = apply_filters('mvx_report_admin_overview_orders_overview', $qry->get_posts());
        $sales_data_chart = array();
         if ( !empty( $orders_overview ) ) {
            foreach ( $orders_overview as $order ) {
                // $order = wc_get_order($order_obj->ID);
                $date = date_create($order->order_date);
                $sales_data_chart[] = array(
                    'name'  =>  date_format($date,"d M"),
                    'Net Sales'  =>  $order->get_subtotal(),
                );
                
                $overview_sales += $order->get_subtotal();
                $mvx_suborders = get_mvx_suborders($order);
                if (!empty($mvx_suborders)) {
                    foreach ($mvx_suborders as $suborder) {
                        $vendor_order = mvx_get_order($suborder->get_id());
                        if ( $vendor_order ) {
                            $gross_sales += $suborder->get_total( 'edit' );
                            $vendor_earning += $vendor_order->get_commission_total('edit');
                        }
                    }
                }
            }
            $overview_admin_earning = $gross_sales - $vendor_earning;
        }

        $user_args = array(
            'role' => 'dc_vendor',
            'date_query' => array(
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day' => date('j', $start_date), //date('1'),
                ),
                'before' => array(
                    'year' => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day' => date('j', $end_date),
                ),
            )
        );
        $user_query = new WP_User_Query($user_args);
        if (!empty($user_query->results)) 
            $overview_vendors = count($user_query->results);
        
        $pending_user_args = array(
            'role' => 'dc_pending_vendor',
            'date_query' => array(
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day' => date('j', $start_date), //date('1'),
                ),
                'before' => array(
                    'year' => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day' => date('j', $end_date),
                ),
            )
        );
        $pending_user_query = new WP_User_Query($pending_user_args);
        if (!empty($pending_user_query->results)) 
            $pending_vendors = count($pending_user_query->results);

        $product_args = array(
            'posts_per_page' => -1,
            //'author__in' => $vendor_ids,
            'post_type' => 'product',
            'post_status' => 'pending',
            'date_query' => array(
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day' => date('j', $start_date), //date('1'),
                ),
                'before' => array(
                    'year' => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day' => date('j', $end_date),
                ),
            )
        );
        $get_pending_products = new WP_Query($product_args);
        if (!empty($get_pending_products->get_posts())) 
            $products = count($get_pending_products->get_posts());

        $transactions_args = array(
            'post_type' => array('mvx_transaction', 'wcmp_transaction'),
            'post_status' => 'mvx_processing',
            'meta_key' => 'transaction_mode',
            'meta_value' => 'direct_bank',
            'posts_per_page' => -1,
            'date_query' => array(
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day' => date('j', $start_date), //date('1'),
                ),
                'before' => array(
                    'year' => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day' => date('j', $end_date),
                ),
            )
        );
        $transactions = get_posts($transactions_args);
        if (!empty($transactions)) 
            $transactions = count($transactions);



        /** * ------------------------------------------- report product overview ------------------------------------------------------- * **/

        $args_report_product = apply_filters( 'mvx_report_data_product_query_args', array(
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'post_status' => array('wc-processing', 'wc-completed'),
            'meta_query' => array(
                array(
                    'key' => '_commissions_processed',
                    'value' => 'yes',
                    'compare' => '='
                )
            ),
            'date_query' => array(
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day' => date('j', $start_date),
                ),
                'before' => array(
                    'year' => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day' => date('j', $end_date),
                ),
            )
        ) );

        $qry_report_product = new WP_Query($args_report_product);
        $overview_orders_product = apply_filters('mvx_filter_orders_report_product', $qry_report_product->get_posts());

        if (!empty($overview_orders_product)) {
            $pro_total = $vendor_total = array();
            foreach ($overview_orders_product as $order) {
                try {
                    // $order = wc_get_order($order_obj->ID);
                    if ($order) :
                        
                        $date = date_create($order->order_date);
                        $vendor_order = mvx_get_order($order->get_id());
                        if ( $vendor_order ) {
                            $line_items = $order->get_items( 'line_item' );
                            
                            foreach ($line_items as $item_id => $item) {
                                
                                if ( $product && $product != $item->get_product_id() ) {
                                    continue;
                                }

                                $total_orders_product_chart[] = array(
                                    'name'  =>  date_format($date,"d M"),
                                    'Net Sales'  =>  absint(1),
                                );

                                $pro_total[$item->get_product_id()] = isset( $pro_total[$item->get_product_id()] ) ? $pro_total[$item->get_product_id()] + $item->get_subtotal() : $item->get_subtotal();
                                $total_sales[$item->get_product_id()]['product_id'] = $item->get_product_id();
                                $total_sales[$item->get_product_id()]['total_sales'] = $pro_total[$item->get_product_id()];
                                $total_sales[$item->get_product_id()]['quantities'] = $item->get_quantity();
                                $total_sales[$item->get_product_id()]['order_id'] = $order->get_id();

                                $total_sales[$item->get_product_id()]['details'][] = array(
                                    'date'  =>  date_format($date,"d M"),
                                    'value' =>  $item->get_subtotal()
                                );

                                $meta_data = $item->get_meta_data();
                                // get item commission
                                foreach ( $meta_data as $meta ) {
                                    if ($meta->key == '_vendor_item_commission') {
                                        $vendor_total[$item->get_product_id()] = isset( $vendor_total[$item->get_product_id()] ) ? $vendor_total[$item->get_product_id()] + floatval($meta->value) : floatval($meta->value);
                                        $total_sales[$item->get_product_id()]['vendor_earning'] = $vendor_total[$item->get_product_id()];
                                    }
                                }
                                // admin part
                                $total_sales[$item->get_product_id()]['admin_earning'] = $total_sales[$item->get_product_id()]['total_sales'] - $total_sales[$item->get_product_id()]['vendor_earning'];
                            }
                        }
                    endif;
                } catch (Exception $ex) {

                }
            }

            if (sizeof($total_sales) > 0) {
                foreach ($total_sales as $product_id => $sales_report) {
                    $report_product_net_sales += ( $sales_report['total_sales'] > 0 ) ? $sales_report['total_sales'] : 0;

                        foreach ($sales_report['details'] as $detailskey => $detailsvalue) {
                            $product_sales_data_chart[] = array(
                                'name'  =>  $detailsvalue['date'],
                                'Net Sales'  => $detailsvalue['value'],
                            );
                        }
                    $product = wc_get_product($product_id);
                    if ( $product ) {

                        $product_item_sold_chart[] = array(
                            'name'  =>  date_format($date,"d M"),
                            'Net Sales'  => 1,
                        );
                        // set product in an array
                        $product_number_stack[]   =   $product_id;
                        $product_url = admin_url('post.php?post=' . $product_id . '&action=edit');
                    }
                }
            } else {
                $report_html = '<tr><td colspan="3">' . __('No product was sold in the given period.', 'multivendorx') . '</td></tr>';
            }
        } else {
            $report_html = '<tr><td colspan="3">' . __('Your store has no products.', 'multivendorx') . '</td></tr>';
        }

        // total item sold **
        $total_item_sold = count($product_number_stack);

        // product report end

        $product_report_datatable = array();
        if ($total_sales) {
            foreach ($total_sales as $total_sales_key => $total_sales_value) {
                $product = wc_get_product( $total_sales_key );
                if (!$product) continue;
                $product_report_datatable[] = array(
                    'id'                    =>  $total_sales_key,
                    'title'                 =>  $product->get_name(),
                    'admin_earning'         =>  $total_sales_value['admin_earning'],
                    'vendor_earning'        =>  $total_sales_value['vendor_earning'],
                    'gross'                 =>  $total_sales_value['total_sales'],
                );
            }
        }

        /** * ---------------------------------------------------- vendor report start ------------------------------------------------------------- * **/

        $all_vendors = get_mvx_vendors();

        $total_sales = $admin_earning = $vendor_report = $report_bk = $total_number_orders = array();

        if (!empty($all_vendors) && is_array($all_vendors)) {
            foreach ($all_vendors as $vendor) {
                $gross_sales = $my_earning = $vendor_earning = 0;
                $chosen_product_ids = array();
                $vendor_id = $vendor->id;

                if ( $selectvendor && $selectvendor != $vendor_id ) {
                    continue;
                }
                

                $args = apply_filters('mvx_report_admin_vendor_tab_query_args', array(
                    'post_type' => 'shop_order',
                    'posts_per_page' => -1,
                    'author' => $vendor_id,
                    'post_status' => array('wc-processing', 'wc-completed'),
                    'meta_query' => array(
                        array(
                            'key' => '_commissions_processed',
                            'value' => 'yes',
                            'compare' => '='
                        ),
                        array(
                            'key' => '_vendor_id',
                            'value' => $vendor_id,
                            'compare' => '='
                        )
                    ),
                    'date_query' => array(
                        'inclusive' => true,
                        'after' => array(
                            'year' => date('Y', $start_date),
                            'month' => date('n', $start_date),
                            'day' => date('j', $start_date),
                        ),
                        'before' => array(
                            'year' => date('Y', $end_date),
                            'month' => date('n', $end_date),
                            'day' => date('j', $end_date),
                        ),
                    )
                ) );

                $qry = new WP_Query($args);

                $orders = apply_filters('mvx_filter_orders_report_vendor', $qry->get_posts());

                if ( !empty( $orders ) ) {
                    foreach ( $orders as $order ) {
                        try {
                            // $order = wc_get_order($order_obj->ID);
                            if ($order) :

                                $date = date_create($order->order_date);
                                $total_number_order_data_chart[] = array(
                                    'name'  =>  date_format($date,"d M"),
                                    'Net Sales'  => 1,
                                );


                                $net_sales_data_chart[] = array(
                                    'name'  =>  date_format($date,"d M"),
                                    'Net Sales'  => $order->get_total( 'edit' ),
                                );

                                $total_number_orders[] = $order->get_id();
                                $vendor_order = mvx_get_order($order->get_id());
                                $gross_sales += $order->get_total( 'edit' );
                                $vendor_earning += $vendor_order->get_commission_total('edit');
                            endif;
                        } catch (Exception $ex) {

                        }
                        
                    }
                }
                
                $total_sales[$vendor_id]['total_sales'] = $gross_sales;
                $total_sales[$vendor_id]['vendor_earning'] = $vendor_earning;
                $total_sales[$vendor_id]['admin_earning'] = $gross_sales - $vendor_earning;
                $total_sales[$vendor_id]['vendor_id'] = $vendor_id; // for report filter
            }

            $html_chart = '';
            
            foreach ($total_sales as $vendor_id => $report) {
                $report_vendor_net_sales += ( $report['total_sales'] > 0 ) ? $report['total_sales'] : 0;
                //$total_sales_width = ( $report['total_sales'] > 0 ) ? round($report['total_sales']) / round($report['total_sales']) * 100 : 0;
                //$admin_earning_width = ( $report['admin_earning'] > 0 ) ? ( $report['admin_earning'] / round($report['total_sales']) ) * 100 : 0;
                //$vendor_earning_width = ( $report['vendor_earning'] > 0 ) ? ( $report['vendor_earning'] / round($report['total_sales']) ) * 100 : 0;
            }

        } else {
            $html_chart = '<tr><td colspan="3">' . __('Your store has no vendors.', 'multivendorx') . '</td></tr>';
        }

        $order_number_count = !empty($total_number_orders) ? count($total_number_orders) : 0;
        // vendor report end


        
        $vendor_report_datatable = array();
        if ($total_sales) {
            foreach ($total_sales as $total_sales_key => $total_sales_value) {
                $vendor = get_mvx_vendor($total_sales_key);
                $name_display = "<b><a href='". sprintf('?page=%s&ID=%s&name=vendor-personal', 'vendors', $vendor->id) ."'>" . $vendor->page_title . "</a>";
                $vendor_report_datatable[] = array(
                    'id'                    =>  $vendor->id,
                    'title'                 =>  $name_display,
                    'defalt_title'          =>  $vendor->page_title,
                    'admin_earning'         =>  $total_sales_value['admin_earning'],
                    'vendor_earning'        =>  $total_sales_value['vendor_earning'],
                    'gross'                 =>  $total_sales_value['total_sales'],
                );
            }
        }

        // merge work product

        // order count ** 
        $total_orders_product = $total_orders_product_chart ? count($total_orders_product_chart) : 0;

        $order_count_pro_chart = array();
        foreach ($total_orders_product_chart as $chart_key => $chart_value) {
            $order_count_pro_chart[$chart_value['name']] += $chart_value['Net Sales'];
        }

        $net_sales_pro_chart = array();
        foreach ($product_sales_data_chart as $chart_key => $chart_value) {
            $net_sales_pro_chart[$chart_value['name']] += $chart_value['Net Sales'];
        }

        $item_count = array();
        foreach ($product_item_sold_chart as $chart_key => $chart_value) {
            $item_count[$chart_value['name']] += $chart_value['Net Sales'];
        }

        $merge_three_data = array_merge_recursive($order_count_pro_chart,$net_sales_pro_chart, $item_count);
        $final_array_product_chart = [];

        foreach ($merge_three_data as $merge_three_data_key => $merge_three_data_value) {
            $final_array_product_chart[] = array(
                'Date'          => $merge_three_data_key,
                'Order Count'   => isset($merge_three_data_value[0]) ? $merge_three_data_value[0] : 0,
                'Net Sales'     => isset($merge_three_data_value[1]) ? $merge_three_data_value[1] : 0,
                'Item Sold'     => isset($merge_three_data_value[2]) ? $merge_three_data_value[2] : 0
            );
        }

        // merge work vendor

        $order_count_ven_chart = array();
        foreach ($total_number_order_data_chart as $chart_key => $chart_value) {
            $order_count_ven_chart[$chart_value['name']] += $chart_value['Net Sales'];
        }

        $net_sales_ven_chart = array();
        foreach ($net_sales_data_chart as $chart_key => $chart_value) {
            $net_sales_ven_chart[$chart_value['name']] += $chart_value['Net Sales'];
        }

        $item_ven_count = array();
        foreach ($product_item_sold_chart as $chart_key => $chart_value) {
            $item_ven_count[$chart_value['name']] += $chart_value['Net Sales'];
        }

        $merge_three_data_ven = array_merge_recursive($order_count_ven_chart,$net_sales_ven_chart, $item_ven_count);
        $final_array_vendor_chart = [];

        foreach ($merge_three_data_ven as $merge_three_data_key => $merge_three_data_value) {
            $final_array_vendor_chart[] = array(
                'Date'          => $merge_three_data_key,
                'Order Count'   => isset($merge_three_data_value[0]) ? $merge_three_data_value[0] : 0,
                'Net Sales'     => isset($merge_three_data_value[1]) ? $merge_three_data_value[1] : 0,
                'Item Sold'     => isset($merge_three_data_value[2]) ? $merge_three_data_value[2] : 0
            );
        }





        $report_by_admin_overview = array(
            'admin_overview'    =>  array(
                'sales' =>  array( 
                    'value'  => wc_price($overview_sales), 
                    'label'    =>  __('Total Sales', 'multivendorx') 
                ),
                'admin_earning' =>  array( 
                    'value'  => wc_price($overview_admin_earning), 
                    'label'    =>  __('Admin Earnings', 'multivendorx') 
                ),
                'vendors'   =>  array( 
                    'value'  => ($overview_vendors), 
                    'label'    =>  __('Signup Vendors', 'multivendorx') 
                ),
                'pending_vendors'   =>  array( 
                    'value'  => ($pending_vendors), 
                    'label'    =>  __('Awaiting Vendors', 'multivendorx') 
                ),
                'products'  =>  array( 
                    'value'  => ($products), 
                    'label'    =>  __('Awaiting Products', 'multivendorx') 
                ),
                'transactions'  =>  array( 
                    'value'  => wc_price($transactions), 
                    'label'    =>  __('Awaiting Withdrawals', 'multivendorx') 
                ),
                'sales_data_chart'  =>  $sales_data_chart
            ),

            'vendor'    =>  array(
                'total_number_orders'   =>  array(
                    'value' =>  ($order_number_count),
                    'label' =>  __('Orders', 'multivendorx')
                ),
                'net_sales'   =>  array(
                    'value' =>  wc_price($report_vendor_net_sales),
                    'label' =>  __('Net Sales', 'multivendorx')
                ),
                'total_item_sold'   =>  array(
                    'value' =>  ($total_item_sold),
                    'label' =>  __('Items Sold', 'multivendorx')
                ),
                'sales_data_chart'  =>  $final_array_vendor_chart, /*array(
                    'total_orders_product_chart'    =>  $total_number_order_data_chart,
                    'product_sales_data_chart'      =>  $net_sales_data_chart,
                    'product_item_sold_chart'       =>  $product_item_sold_chart
                )*/
                'vendor_report_datatable'  =>  $vendor_report_datatable
            ),

            'product'   =>  array(
                'total_orders_product'  =>  array(
                    'value' =>  ($total_orders_product),
                    'label' =>  __('Orders', 'multivendorx')
                ),
                'net_sales'  =>  array(
                    'value' =>  wc_price($report_product_net_sales),
                    'label' =>  __('Net Sales', 'multivendorx')
                ),
                'total_item_sold'  =>  array(
                    'value' =>  ($total_item_sold),
                    'label' =>  __('Items Sold', 'multivendorx')
                ),
                'sales_data_chart'  =>  $final_array_product_chart,/*array(
                    'total_orders_product_chart'    =>  $total_orders_product_chart,
                    'product_sales_data_chart'      =>  $product_sales_data_chart,
                    'product_item_sold_chart'       =>  $product_item_sold_chart
                )*/
                'product_report_datatable'  =>  $product_report_datatable
            ),
            'banking_overview'  =>  $banking_datas
        );

        return rest_ensure_response(apply_filters('mvx_analytics_tabs_datas', $report_by_admin_overview, $status_sales, $selectvendor, $value));
    }

    public function mvx_update_commission_status($request) {
        global $MVX;
        $value = $request->get_param('value') ? ($request->get_param('value')) : 0;
        $commission_id = $request->get_param('commission_id') ? absint($request->get_param('commission_id')) : 0;
        if ( $value == 'paid' ) {
            $MVX->postcommission->mvx_mark_commission_paid( array( $commission_id ) ) ;
        } else {
            update_post_meta($commission_id, '_paid_status', wc_clean(wp_unslash($value)));
        }
    }

    public function mvx_get_commission_id_status($request) {
        $commission_id = $request->get_param('commission_id') ? absint($request->get_param('commission_id')) : 0;
        $status = MVX_Commission::get_status($commission_id, 'edit');

        $commission_status_list_action = array();
        $commission_status = mvx_get_commission_statuses();
        foreach ($commission_status as $status_key => $status_value) {
            if ($status_key == $status) {
                $commission_status_list_action[] = array(
                    'value' => $status_key,
                    'label' => $status_value
                );
            }
        }
        return rest_ensure_response($commission_status_list_action);
    }

    public function mvx_details_specific_commission($request) {
        global $MVX;
        $commission_id = $request->get_param('commission_id') ? ($request->get_param('commission_id')) : 0;
        $commission_order_id = get_post_meta($commission_id, '_commission_order_id', true);
        $order = wc_get_order($commission_order_id);
        if (!$order) return;
        $vendor_order = mvx_get_order($commission_order_id);
        $commission_order_version = get_post_meta($commission_order_id, '_order_version', true);
        $post = get_post($commission_id);
        $vendor = get_mvx_vendor($post->post_author);
        if (!$vendor) {
            $vendor_id = get_post_meta($commission_order_id, '_vendor_id', true);
            $vendor = get_mvx_vendor($vendor_id);
        }
        $commission_type_object = get_post_type_object( $post->post_type );
        $shipping_amount   =  get_post_meta( $commission_id, '_shipping', true );
        $tax_amount = get_post_meta( $commission_id, '_tax', true );

        $commission_amount =  get_post_meta( $commission_id, '_commission_amount', true );

        $meta_list = $meta_list_associate_vendor = $shipping_items_meta_details = $line_items_meta_details = array();

        if ( $vendor ) {
            /* translators: %s: associated vendor */
            $vendor_string = sprintf(
                __( '<div class="mvx-commission-label-class">Associated vendor</div> <div class="mvx-commission-value-class">%s</div>', 'multivendorx' ),
                '<a href="' . sprintf('?page=%s&ID=%s&name=vendor-personal', 'vendors', $vendor->id) . '" target="_blank">'.$vendor->page_title.'</a>'
            );

            $meta_list_associate_vendor[] = $vendor_string;
        }

        /* translators: %s: Commission status */
        $status = MVX_Commission::get_status($commission_id, 'edit');
        $shipping_items_details = $status_html = $tax_data = '';
        $line_items_details = [];
        if ($status == 'paid') {
            $status_html .= '<p class="commission-status-paid">'.MVX_Commission::get_status($commission_id).'</p>';
        } else {
            $status_html .= '<p class="commission-status-unpaid">'.MVX_Commission::get_status($commission_id).'</p>';
        }

        $meta_list[] = sprintf(
            __( '<p class="commission-status-text-check">Commission status: </p> %s', 'multivendorx' ),
            $status_html
        );

        $meta_list_associate_vendor = wp_kses_post( implode( '. ', $meta_list_associate_vendor ) );
        $order_meta_details = wp_kses_post( implode( '. ', $meta_list ) );


        $line_items = $order->get_items(apply_filters('mvx_admin_commission_order_item_types', 'line_item'));
        $hidden_order_itemmeta = apply_filters(
                'mvx_admin_commission_hidden_order_itemmeta', array(
                '_qty',
                '_tax_class',
                '_product_id',
                '_variation_id',
                '_line_subtotal',
                '_line_subtotal_tax',
                '_line_total',
                '_line_tax',
                'method_id',
                '_vendor_item_commission',
                'cost',
                '_vendor_order_item_id',
                '_vendor_id',
                'Sold By'
            )
        );
        if ($line_items) {
            foreach ($line_items as $item_id => $item) {
                $product = $item->get_product();
                $product_link = $product ? admin_url('post.php?post=' . $item->get_product_id() . '&action=edit') : '';
                $thumbnail = $product ? apply_filters('mvx_admin_commission_order_item_thumbnail', $product->get_image(array(50, 50), array('title' => '')), $item_id, $item) : '';
                $row_class = apply_filters('mvx_admin_commission_html_order_item_class', !empty($class) ? $class : '', $item, $order);
                $meta_data = $item->get_formatted_meta_data('');

                if ($meta_data) {
                    foreach ($meta_data as $meta_id => $meta) {
                        if (in_array($meta->key, $hidden_order_itemmeta, true)) {
                            continue;
                        }
                        $line_items_meta_details[]  =   array(
                            'display_key'   =>  wp_kses_post($meta->display_key),
                            'display_value' =>  wp_kses_post(force_balance_tags($meta->display_value)),
                        );
                    }
                }
                $refunded = $order->get_total_refunded_for_item($item_id);
                $refunded_qty = $order->get_qty_refunded_for_item($item_id);

                $line_items_details[]   =   array(
                    'item_id'   =>  $item_id,
                    'product'   =>  $item->get_product(),
                    'product_link'  =>  $product ? admin_url('post.php?post=' . $item->get_product_id() . '&action=edit') : '',
                    'thumbnail' =>  $product ? apply_filters('mvx_admin_commission_order_item_thumbnail', $product->get_image('thumbnail', array('title' => ''), false), $item_id, $item) : '',
                    'row_class' =>  apply_filters('mvx_admin_commission_html_order_item_class', !empty($class) ? $class : '', $item, $order),
                    'item_name' =>  $item->get_name(),
                    'product_link_display'  =>  $product_link ? '<a href="' . esc_url($product_link) . '" class="wc-order-item-name">' . esc_html($item->get_name()) . '</a>' : '<div class="wc-order-item-name">' . esc_html($item->get_name()) . '</div>',
                    'product_sku'   => $product && $product->get_sku() ? '<div class="wc-order-item-sku"><strong>' . esc_html__('SKU:', 'multivendorx') . '</strong> ' . esc_html($product->get_sku()) . '</div>'    :   '',
                    'variation_id_text' =>  '<div class="wc-order-item-variation"><strong>' . esc_html__('Variation ID:', 'multivendorx') . '</strong> ',
                    'get_variation_post_type'   =>  get_post_type($item->get_variation_id()),
                    'check_variation_id'    =>  $item->get_variation_id(),
                    'item_variation_display'    =>  esc_html($item->get_variation_id()),
                    'no_longer_exist'   =>  sprintf(esc_html__('%s (No longer exists)', 'multivendorx'), $item->get_variation_id()),
                    'close_div' =>  '</div>',
                    'meta_data' =>  $line_items_meta_details,
                    'meta_format_data'  =>  $meta_data,
                    'item_cost' =>  wc_price($order->get_item_total($item, false, true), array('currency' => $order->get_currency())),
                    'line_cost_html'    => $item->get_subtotal() !== $item->get_total() ? '<span class="wc-order-item-discount">-' . wc_price(wc_format_decimal($order->get_item_subtotal($item, false, false) - $order->get_item_total($item, false, false), ''), array('currency' => $order->get_currency())) . '</span>' :   '',
                    'quantity_1st'  =>  '<small class="times">&times;</small> ' . esc_html($item->get_quantity()),
                    'quantity_2nd'  =>  $order->get_qty_refunded_for_item($item_id) ? '<small class="refunded">-' . ( $refunded_qty * -1 ) . '</small>' :   '',

                    'line_cost' =>  wc_price($item->get_total(), array('currency' => $order->get_currency())),
                    'line_cost_1st' =>  $item->get_subtotal() !== $item->get_total()    ?   '<span class="wc-order-item-discount">-' . wc_price(wc_format_decimal($item->get_subtotal() - $item->get_total(), ''), array('currency' => $order->get_currency())) . '</span>' :   '',
                    'line_cost_2nd' =>  $refunded ?  '<small class="refunded">-' . wc_price($refunded, array('currency' => $order->get_currency())) . '</small>'    :   '',
                    'item_thunbail' =>  '<div class="wc-order-item-thumbnail">' . ($thumbnail) . '</div>',
                );
            }
        }

        $line_items_shipping = $order->get_items('shipping');
        $shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
        $refunded = $order->get_total_refunded_for_item($item_id, 'shipping');

        $get_total_shipping_refunded = $order->get_total_shipping_refunded() ? $order->get_total_shipping_refunded() : 0;

        if ($line_items_shipping) {

            foreach ($line_items_shipping as $item_id => $item) {
                $hidden_order_itemmeta = apply_filters(
                    'mvx_admin_commission_hidden_order_itemmeta', array(
                        '_qty',
                        '_tax_class',
                        '_product_id',
                        '_variation_id',
                        '_line_subtotal',
                        '_line_subtotal_tax',
                        '_line_total',
                        '_line_tax',
                        'method_id',
                        'cost',
                        '_vendor_order_shipping_item_id',
                        'vendor_id',
                        'Items'
                    )
                );
                $refunded = $order->get_total_refunded_for_item($item_id, 'shipping');


                // to add shipping method title
                $shipping_items_meta_details[]  =   array(
                    'display_key'   =>  __('Shipping method', 'multivendorx'),
                    'display_value' =>  esc_html($item->get_name() ? $item->get_name() : __('Shipping', 'multivendorx') ),
                );

                if ($meta_data = $item->get_formatted_meta_data('')) {
                    foreach ($meta_data as $meta_id => $meta) {
                        if (in_array($meta->key, $hidden_order_itemmeta, true)) {
                            continue;
                        }
                        $shipping_items_meta_details[]  =   array(
                            'display_key'   =>  $meta->display_key == 'package_qty' ? 'Qty' : wp_kses_post($meta->display_key),
                            'display_value' =>  wp_kses_post(force_balance_tags($meta->display_value)),
                        );
                    }
                }

                $shipping_items_meta_details[]  =   array(
                    'display_key'   =>  __('Total shipping', 'multivendorx'),
                    'display_value' =>  wc_price($item->get_total(), array('currency' => $order->get_currency())),
                );

                $shipping_items_details   =   array(
                    'shipping_text'   =>  esc_html($item->get_name() ? $item->get_name() : __('Shipping', 'multivendorx') ),
                    'meta_data' =>  $shipping_items_meta_details,
                    'refunded_amount'   =>  $refunded ? '<small class="refunded">-' . wc_price($refunded, array('currency' => $order->get_currency())) . '</small>' : '',
                    'shipping_price'    =>  wc_price($item->get_total(), array('currency' => $order->get_currency())),
                    'refunded_shipping' =>  $refunded ? '<small class="refunded">-' . wc_price($refunded, array('currency' => $order->get_currency())) . '</small>' : ''
                );
            }
        }

        if (wc_tax_enabled()) {
            foreach ($order->get_tax_totals() as $code => $tax) {
                $get_total_tax_refunded_by_rate_id = $order->get_total_tax_refunded_by_rate_id($tax->rate_id);
                if ($get_total_tax_refunded_by_rate_id > 0) {
                    $tax_data   =   array(
                        'tax_label' =>  esc_html($tax->label),
                        'get_total_tax_refunded_by_rate_id' =>  $get_total_tax_refunded_by_rate_id,
                        'greater_zero'  =>  '<del>' . strip_tags($tax->formatted_amount) . '</del> <ins>' . wc_price(WC_Tax::round($tax->amount, wc_get_price_decimals()) - WC_Tax::round($get_total_tax_refunded_by_rate_id, wc_get_price_decimals()), array('currency' => $order->get_currency())) . '</ins>',
                        'else_output'   =>  wp_kses_post($tax->formatted_amount)
                    );
                }
            }
        }

        $commission_total = get_post_meta( $commission_id, '_commission_total', true );
        $order_id = get_post_meta( $commission_id, '_commission_order_id', true );
        $is_migration_order = $order->get_meta('_order_migration', true); // backward compatibility
        $notes = $MVX->postcommission->get_commission_notes($commission_id);
        $notes_data = [];
        if ($notes) {
            foreach ($notes as $note) {
                $notes_data[] = array(
                    'comment_content'   => str_replace("wcmp", "mvx", $note->comment_content),
                    'comment_date'   =>  $note->comment_date,
                );
            }
        }

        $order_edit_link = sprintf('post.php?post=%s&action=edit', $commission_order_id);
        $payment_details = apply_filters('mvx_individual_commission_content_filter', array(
            'commission_id' => $commission_id,
            'commission_order_id'   => $commission_order_id,
            'commission_type_object'    =>  $commission_type_object,
            'vendor_edit_link'  => sprintf('?page=%s&ID=%s&name=vendor-personal', 'mvx#&submenu=vendor', $vendor->id),
            'vendor'    =>  $vendor,
            'status'    =>  MVX_Commission::get_status($commission_id, 'edit'),
            'order_edit_link'   =>  $order_edit_link,
            'order' => $order,
            'avater_image'  =>  get_avatar($vendor->id, 50),
            'payment_title' =>  isset($MVX->payment_gateway->payment_gateways[$vendor->payment_mode]) ? $MVX->payment_gateway->payment_gateways[$vendor->payment_mode]->gateway_title : '',
            'commission_total_calculate'    =>  MVX_Commission::commission_amount_totals($commission_id, 'edit'),
            'commission_totals' =>  '<del>' . wc_price($commission_amount, array('currency' => $order->get_currency())) . '</del> <ins>' . MVX_Commission::commission_amount_totals($commission_id).'</ins>',
            'commission_totals_else'    =>  MVX_Commission::commission_amount_totals($commission_id),
            'shipping_amount'   =>  get_post_meta( $commission_id, '_shipping', true ),
            'commission_shipping_totals'    =>  MVX_Commission::commission_shipping_totals($commission_id, 'edit'),
            'commission_shipping_totals_output' =>  '<del>' . wc_price($shipping_amount, array('currency' => $order->get_currency())) . '</del> <ins>' . MVX_Commission::commission_shipping_totals($commission_order_id, 'edit').'</ins>',
            'tax_amount'    =>  $tax_amount,
            'commission_tax_total'  =>  MVX_Commission::commission_tax_totals($commission_id, 'edit'),
            'commission_tax_total_output'   =>  '<del>' . wc_price($tax_amount, array('currency' => $order->get_currency())) . '</del> <ins>' . MVX_Commission::commission_tax_totals($commission_id).'</ins>',
            'order_meta_details'    =>  $order_meta_details,
            'meta_list_associate_vendor'    =>  $meta_list_associate_vendor,
            'order_status_display'  =>  ucfirst($order->get_status()),
            'commission_amount' =>  $commission_amount,
            'line_items'    =>  $line_items_details,

            'order_total_discount'  =>  $order->get_total_discount(),
            'commission_include_coupon' =>  get_post_meta($commission_id, '_commission_include_coupon', true),
            'is_shipping'   =>   get_post_meta($commission_id, '_shipping', true),
            'commission_total_include_shipping' =>  get_post_meta($commission_id, '_commission_total_include_shipping', true),
            'is_tax'    =>  get_post_meta($commission_id, '_tax', true) ? get_post_meta($commission_id, '_tax', true) : 0,
            'commission_total_include_tax'  =>  get_post_meta($commission_id, '_commission_total_include_tax', true),
            'formated_commission_total' =>  $vendor_order->get_formatted_commission_total(),
            'get_total_shipping_refunded'   =>  $get_total_shipping_refunded,
            'refund_shipping_display'    => $order->get_shipping_total() ? '<del>' . strip_tags(wc_price($order->get_shipping_total(), array('currency' => $order->get_currency()))) . '</del> <ins>' . wc_price($order->get_shipping_total() - $get_total_shipping_refunded, array('currency' => $order->get_currency())) . '</ins>' : '',
            'else_shipping' =>  wc_price($order->get_shipping_total(), array('currency' => $order->get_currency())),
            'tax_data'  =>  $tax_data,
            'commission_total'  =>  wc_price($commission_total, array('currency' => $order->get_currency())),
            'is_migration_order'    =>  $is_migration_order,
            'commission_total_edit' =>  MVX_Commission::commission_totals($commission_id, 'edit'),
            'commission_total_display'  => MVX_Commission::commission_totals($commission_id),
            'is_refuned'    =>  get_post_meta( $commission_id, '_commission_refunded', true ),
            'refunded_output'   =>  wc_price(get_post_meta( $commission_id, '_commission_refunded', true ), array('currency' => $order->get_currency())),
            'get_shipping_method'   =>  $order->get_shipping_methods(),
            'notes_data'    =>  $notes_data,
            'shipping_items_details'    =>  $shipping_items_details,
        ));

        return rest_ensure_response($payment_details); 
    }

    public function mvx_vendor_delete($request) {
        require_once(ABSPATH.'wp-admin/includes/user.php');
        $vendor_ids = $request->get_param('vendor_ids') ? $request->get_param('vendor_ids') : '';
        $select_input = $request->get_param('select_input') ? $request->get_param('select_input') : '';

        if ($select_input == 'delete_user') {
            if ($vendor_ids && is_array($vendor_ids)) {
                foreach (wp_list_pluck($vendor_ids, "ID") as $user_id) {
                    wp_delete_user($user_id);
                }
            } elseif ($vendor_ids) {
                wp_delete_user($vendor_ids);
            }
        }
        if ($select_input == 'delete_vendor') {
            if ($vendor_ids && is_array($vendor_ids)) {
                foreach (wp_list_pluck($vendor_ids, "ID") as $vendor_id) {
                    $user = new WP_User(absint($vendor_id));
                    $user->set_role('customer');
                }
            } elseif ($vendor_ids) {
                $user = new WP_User(absint($vendor_ids));
                $user->set_role('customer');
            }
        }

        if ($select_input == 'delete') {
            if ($vendor_ids && is_array($vendor_ids)) {
                foreach (wp_list_pluck($vendor_ids, "ID") as $key => $value) {
                    wp_delete_user($value);
                }
            } elseif ($vendor_ids) {
                wp_delete_user($vendor_ids);
            }
        } elseif ($select_input == 'approve') {
            if ($vendor_ids && is_array($vendor_ids)) {
                foreach (wp_list_pluck($vendor_ids, "ID") as $key => $value) {
                    delete_user_meta(absint($value), '_vendor_turn_off');
                    $user = new WP_User(absint($value));
                    $user->set_role('dc_vendor');
                }
            } elseif ($vendor_ids) {
                delete_user_meta(absint($vendor_ids), '_vendor_turn_off');
                $user = new WP_User(absint($vendor_ids));
                $user->set_role('dc_vendor');
            }
        } elseif ($select_input == 'reject') {
            if ($vendor_ids && is_array($vendor_ids)) {
                foreach (wp_list_pluck($vendor_ids, "ID") as $key => $value) {
                    delete_user_meta(absint($value), '_vendor_turn_off');
                    $user = new WP_User(absint($value));
                    $user->set_role('dc_rejected_vendor');
                }
            } elseif ($vendor_ids) {
                delete_user_meta(absint($vendor_ids), '_vendor_turn_off');
                $user = new WP_User(absint($vendor_ids));
                $user->set_role('dc_rejected_vendor');
            }
        } elseif ($select_input == 'pending') {
            if ($vendor_ids && is_array($vendor_ids)) {
                foreach (wp_list_pluck($vendor_ids, "ID") as $key => $value) {
                    delete_user_meta(absint($value), '_vendor_turn_off');
                    $user = new WP_User(absint($value));
                    $user->set_role('dc_pending_vendor');
                }
            } elseif ($vendor_ids) {
                delete_user_meta(absint($vendor_ids), '_vendor_turn_off');
                $user = new WP_User(absint($vendor_ids));
                $user->set_role('dc_pending_vendor');
            }
        } elseif ($select_input == 'suspend') {
            if ($vendor_ids && is_array($vendor_ids)) {
                foreach (wp_list_pluck($vendor_ids, "ID") as $key => $value) {
                    update_user_meta($value, '_vendor_turn_off', 'Enable');
                }
            } elseif ($vendor_ids) {
                update_user_meta($vendor_ids, '_vendor_turn_off', 'Enable');
            }
        }
        return $this->mvx_list_all_vendor('');
    }

    public function mvx_commission_delete($request) {
        $commission_ids = $request->get_param('commission_ids') ? $request->get_param('commission_ids') : '';
        if ($commission_ids && is_array($commission_ids)) {
            foreach (wp_list_pluck($commission_ids, "ID") as $key => $value) {
                wp_delete_post($value);
            }
        } elseif ($commission_ids) {
            wp_delete_post($commission_ids);
        }
        return $this->mvx_find_specific_commission();
    }

    public function mvx_update_specific_vendor_shipping_option($request) {
        $value = $request->get_param('value') ? ($request->get_param('value')) : 0;
        $vendor_id = $request->get_param('vendor_id') ? ($request->get_param('vendor_id')) : 0;
        mvx_update_user_meta($vendor_id, 'vendor_shipping_options', wc_clean($value));
    }

    public function mvx_export_csv_for_report_product_chart($request) {
        global $MVX;
        $product_list = $request->get_param('product_list') ? ($request->get_param('product_list')) : array();
        $csv_data_data = array();

        foreach ($product_list as $value_details) {
            $csv_data_data[] = array(
                'Product Name'      =>  $value_details['title'],
                'Net Sales'     =>  $value_details['gross'],
                'Admin Earning'      =>  $value_details['admin_earning'],
                'Vendor Earning'    =>  $value_details['vendor_earning'],
            );
        }
        return rest_ensure_response($csv_data_data);
    }

    public function mvx_export_csv_for_report_vendor_chart($request) {
        global $MVX;
        $vendor_list = $request->get_param('vendor_list') ? ($request->get_param('vendor_list')) : array();
        $csv_data_data = array();

        foreach ($vendor_list as $value_details) {
            $csv_data_data[] = array(
                'Vendor Name'       =>  $value_details['defalt_title'],
                'Net Sales'         =>  $value_details['gross'],
                'Admin Earning'     =>  $value_details['admin_earning'],
                'Vendor Earning'    =>  $value_details['vendor_earning'],
            );
        }
        return rest_ensure_response($csv_data_data);
    }

    public function mvx_update_commission_bulk($request) {
        global $MVX;
        $value = $request->get_param('value') ? ($request->get_param('value')) : 0;
        $commission_list = $request->get_param('commission_list') ? ($request->get_param('commission_list')) : array();
        if ($value == 'mark_paid') {
            $MVX->postcommission->mvx_mark_commission_paid($commission_list);
        } else if ($value == 'delete') {
            if ($commission_list) {
                foreach ($commission_list as $key => $value_id) {
                    if (get_post_status($value_id) != 'trash') {
                        wp_trash_post($value_id);
                    } else {
                        wp_delete_post($value_id);
                    }
                }
            }
        } else if ($value == 'restore') {
            if ($commission_list) {
                foreach ($commission_list as $key => $value_id_un) {
                    if (get_post_status($value_id_un) == 'trash') {
                        wp_untrash_post($value_id_un);
                    }
                }
            }
        } else if ($value == 'export') {
            $commissions_data = array();
            $currency = get_woocommerce_currency();
            foreach ($commission_list as $commission) {
                $commission_data = $MVX->postcommission->get_commission($commission);
                $commission_staus = get_post_meta($commission, '_paid_status', true);

                $product_list = '';
                $order_id = get_post_meta($commission, '_commission_order_id', true);
                $order = wc_get_order($order_id);
                if ( $order ) {
                    $line_items = $order->get_items( 'line_item' );
                    foreach ($line_items as $item_id => $item) {
                        $product = $item->get_product();
                        $name = ( $product ) ? $product->get_title() : $item->get_name();
                        $product_id = ( $product ) ? $product->get_id() : 0;
                        $product_list .= $name . ' ';
                    }
                }
                $commission_details = get_post($commission);

                $recipient = get_user_meta($commission_data->vendor->id, '_vendor_paypal_email', true) ? get_user_meta($commission_data->vendor->id, '_vendor_paypal_email', true) : $commission_data->vendor->page_title;
                $commission_amount = get_post_meta( $commission, '_commission_amount', true ) ? get_post_meta( $commission, '_commission_amount', true ) : 0;
                $shipping_amount = get_post_meta( $commission, '_shipping', true ) ? get_post_meta( $commission, '_shipping', true ) : 0;
                $tax_amount = get_post_meta( $commission, '_tax', true ) ? get_post_meta( $commission, '_tax', true ) : 0;
                $commission_total = get_post_meta( $commission, '_commission_total', true ) ? get_post_meta( $commission, '_commission_total', true ) : 0;
                $commission_order = get_post_meta($commission, '_commission_order_id', true) ? wc_get_order(get_post_meta($commission, '_commission_order_id', true)) : false;
                if ($commission_order) $currency = $commission_order->get_currency();
                $commissions_data[] = apply_filters('mvx_vendor_commissions', array(
                    __('Recipient', 'multivendorx')     =>  $recipient,
                    __('Currency', 'multivendorx')      =>  $currency,
                    __('Commission', 'multivendorx')    =>  $commission_amount,
                    __('Shipping', 'multivendorx')      =>  $shipping_amount,
                    __('Tax', 'multivendorx')           =>  $tax_amount,
                    __('Total', 'multivendorx')         =>  $commission_total,
                    __('Status', 'multivendorx')        =>  $commission_staus,
                    __('Products', 'multivendorx')      =>  $product_list,
                    __('Sub Order', 'multivendorx')     =>  $order_id,
                    __('Date', 'multivendorx')          =>  $commission_details->post_modified
                ), $commission_data);
            }
            return rest_ensure_response($commissions_data);
        }
        return $this->mvx_find_specific_commission();
    }

    public function mvx_show_vendor_name() {
        $option_lists = [];
        $vendors = get_mvx_vendors();
        if ($vendors) {
            foreach($vendors as $vendor_key => $vendor_value) {
                $option_lists[] = array(
                    'value' => $vendor_value->id,
                    'label' => $vendor_value->user_data->data->display_name
                );
            }
        }
        return rest_ensure_response($option_lists);
    }

    public function mvx_search_commissions_as_per_vendor_name($request) {
        $vendor_name = $request->get_param('vendor_name') ? ($request->get_param('vendor_name')) : 0;
        return $this->mvx_find_specific_commission( $request, array(), '',  $vendor_name);
    }

    public function mvx_show_commission_from_status_list($request) {
        $commission_status = $request->get_param('commission_status') ? ($request->get_param('commission_status')) : 0;
        return $this->mvx_find_specific_commission( $request, array(), $commission_status );
    }

    public function mvx_search_specific_commission($request) {
        $commission_ids = array();
        $commission_ids[] = $request->get_param('commission_ids') ? ($request->get_param('commission_ids')) : array();
        return $this->mvx_find_specific_commission('', $commission_ids, '', '');
    }

    public function mvx_all_commission_details($request) {
        return $this->mvx_find_specific_commission($request);
    }

    public function mvx_find_specific_commission($request = '', $commission_ids = array(), $status = '', $vendor_name = '') {
        $date_range = $request && $request->get_param('date_range') ? $request->get_param('date_range') : '';

        $commission_list = array();
        // Bydefault last 7 days
        $start_date    = strtotime( '-7 days', strtotime( 'midnight', current_time( 'timestamp' ) ) );
        $end_date      = strtotime( 'midnight', current_time( 'timestamp' ) );
        if ($date_range) {
            $initial_start = $date_range ? $date_range[0] : '';
            $initial_end = $date_range ? $date_range[1] : '';
            $start_date = max( strtotime( '-20 years' ), strtotime( sanitize_text_field( $initial_start ) ) );
            $end_date = strtotime( 'midnight', strtotime( sanitize_text_field( $initial_end ) ) );
        }

        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => $status && $status == 'trash' ? array('trash') : array('publish', 'private', 'draft'),
            'posts_per_page' => -1,
            'fields' => 'ids',
            'date_query' => array(
                'inclusive' => true,
                'after' => array(
                    'year' => date('Y', $start_date),
                    'month' => date('n', $start_date),
                    'day' => date('j', $start_date), //date('1'),
                ),
                'before' => array(
                    'year' => date('Y', $end_date),
                    'month' => date('n', $end_date),
                    'day' => date('j', $end_date),
                ),
            ),
        );

        if (!empty($commission_ids)) {
            $args['post__in']   =  $commission_ids;
        }

        if ($status && $status != 'trash') {
            $args['meta_query'] = array(
                array(
                    'key' => '_paid_status',
                    'value' => wc_clean($status),
                    'compare' => '='
                )
            );
        }

        if ($vendor_name) {
            $vendor = get_mvx_vendor($vendor_name);
            $args['meta_query'] = array(
                array(
                    'key' => '_commission_vendor',
                    'value' => absint($vendor->term_id),
                    'compare' => '='
                )
            );

        }

        $commissions = new WP_Query( apply_filters('mvx_commission_list_args', $args ) );
        if ($commissions->get_posts() && !empty($commissions->get_posts())) {
            foreach ($commissions->get_posts() as $commission_key => $commission_value) {

                $order_id = get_post_meta($commission_value, '_commission_order_id', true);
                $commission_details = get_post($commission_value);

                $edit_url = 'post.php?post=' . $order_id . '&action=edit';

                $edit_commission_url = 'post.php?post=' . $commission_value . '&action=edit';

                $order = wc_get_order($order_id);
                $vendor_order = ( $order ) ? mvx_get_order( $order->get_id() ) : array();
                $product_list = $vendor_list = $net_earning = $vendor_link = '';

                // find vendor 
                $vendor_user_id = get_post_meta($commission_value, '_commission_vendor', true);
                if ($vendor_user_id) {
                    $vendor = get_mvx_vendor_by_term($vendor_user_id);
                    if ($vendor) {
                        $user = get_userdata(absint($vendor->id));
                        $vendor_link = sprintf('?page=%s&ID=%s&name=vendor-personal', 'mvx#&submenu=vendor', $vendor->id);
                        if ($user && in_array('dc_rejected_vendor', $user->roles)) {
                            $vendor_link = sprintf('?page=%s&ID=%s&name=vendor-application', 'mvx#&submenu=vendor', $vendor->id);
                        } else if ($user && in_array('dc_pending_vendor', $user->roles)) {
                            $vendor_link = sprintf('?page=%s&ID=%s&name=vendor-application', 'mvx#&submenu=vendor', $vendor->id);
                        }
                    }
                }
                
                if ( $vendor_order ) {
                    $vendor = $vendor_order->get_vendor();
                    $vendor_list = $vendor ? '<a href="' . esc_url($vendor_link) . '">' . $vendor->page_title . '</a>' : '';
                } else { // BW compatibilities
                    if ($vendor_user_id) {
                        if ($vendor) {
                            $edit_vendor_url = get_edit_user_link($vendor_user_id);
                            $vendor_list = '<a href="' . esc_url($edit_vendor_url) . '">' . $vendor->page_title . '</a>';
                        }
                    }
                }
                // find product 
                if ( $order ) {
                    $line_items = $order->get_items( 'line_item' );
                    foreach ($line_items as $item_id => $item) {
                        $product = $item->get_product();
                        $name = ( $product ) ? $product->get_title() : $item->get_name();
                        $product_id = ( $product ) ? $product->get_id() : 0;
                        $product_list .= '[<a href="' . esc_url(get_permalink($product_id)) . '">' . $name . '</a>]<br>';
                    }
                }

                // find amount
                $commission_amount = get_post_meta($commission_value, '_commission_amount', true) ? wc_price(get_post_meta($commission_value, '_commission_amount', true)) : 0;

                if ( $vendor_order ) {
                    $net_earning = $vendor_order->get_commission_total();
                } else { // BW compatibilities
                    /*$commission_vendor = get_post_meta($commission_value, '_commission_vendor', true);
                    $vendor_user_id = get_term_meta($commission_vendor, '_vendor_user_id', true);
                    $vendor = get_mvx_vendor($vendor_user_id);
                    if ($vendor) {
                        $vendor_total = get_mvx_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order_id));
                        $net_earning = wc_price($vendor_total['total']);
                    }*/
                }

                $action_display = "
                    <div class='mvx-vendor-action-icon'>
                        <i class='mvx-font icon-edit'></i>
                        <i class='mvx-font icon-close'></i>
                    </div>
                ";

                if (get_post_meta($commission_value, '_paid_status', true) == "paid") {
                    $status_display = "<p class='commission-status-paid'>" . __('Paid', 'multivendorx') . "</p>";
                } else {
                    $status_display = "<p class='commission-status-unpaid'>" . ucfirst(get_post_meta($commission_value, '_paid_status', true)) . "</p>";
                }

                $commission_list[] = apply_filters('mvx_commissions_table_columns_data', array(
                    'id'            =>  $commission_value,
                    'commission_id'         =>  '<a href="' . sprintf('?page=%s&CommissionID=%s', 'mvx#&submenu=commission', $commission_value) . '">#' . $commission_value . '</a>',
                    'link'          =>  sprintf('?page=%s&CommissionID=%s', 'mvx#&submenu=commission', $commission_value),
                    'order_id'      =>  '<a href="' . esc_url($edit_url) . '">#' . $order_id . '</a>',
                    'product'       =>  $product_list,
                    'vendor'        =>  $vendor_list,
                    'amount'        =>  $commission_amount,
                    'net_earning'   =>  $net_earning,
                    'status'        =>  $status_display,
                    'date'          =>  $commission_details->post_modified,
                    'action'        =>  $action_display,
                    'edit_net_earning'  => MVX_Commission::commission_totals($commission_value, 'edit')
                ), $commission_value);
            }
        }

        return rest_ensure_response($commission_list);
    }

    public function mvx_update_post_code($request) {
        global $MVX;

        if ( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }

        

        $zone_id = $request->get_param('zone_id') ? $request->get_param('zone_id') : 0;
        $vendor_id = $request->get_param('vendor_id') ? wc_clean($request->get_param('vendor_id')) : 0;
        $_select_zone_postcodes = $request->get_param('value') ? $request->get_param('value') : 0;

        $type = $request->get_param('type') ? $request->get_param('type') : 0;

        $zones = MVX_Shipping_Zone::get_vendor_zone($zone_id, $vendor_id);
        $location = $zones && is_array($zones['locations']) ? $zones['locations'] : array();

        if ($type && $type == 'postcode' && !empty($_select_zone_postcodes)) {
            $postcode_array = array();
            $zone_postcodes = array_map('trim', explode(',', $_select_zone_postcodes));
            foreach ($zone_postcodes as $zone_postcode) {
                $postcode_array[] = array(
                    'code' => $zone_postcode,
                    'type' => 'postcode'
                );
            }

            $location = array_merge($location, $postcode_array);
        }

        if ($type && $type == 'select_state' && !empty($_select_zone_postcodes)) {
            $state_array = array();
            if (!empty($_select_zone_postcodes)) {
                foreach ($_select_zone_postcodes as $zone_statecode) {
                    $state_array[] = array(
                        'code' => $zone_statecode['value'],
                        'type' => 'state'
                    );
                }
            }

            $location = array_merge($location, $state_array);
        }

        
        MVX_Shipping_Zone::save_location($location, $zone_id, $vendor_id);

        $MVX->load_class('shipping-gateway');
        MVX_Shipping_Gateway::load_class('shipping-method');
        $vendor_shipping = new MVX_Vendor_Shipping_Method();
        $vendor_shipping->process_admin_options();

        // clear shipping transient
        WC_Cache_Helper::get_transient_version('shipping', true);

    }

    public function mvx_toggle_shipping_method($request) {
        global $MVX;
        $zone_id = $request->get_param('zone_id') ? $request->get_param('zone_id') : 0;
        $instance_id = $request->get_param('instance_id') ? ($request->get_param('instance_id')) : 0;
        $vendor_id = $request->get_param('vendor_id') ? wc_clean($request->get_param('vendor_id')) : 0;
        $value = $request->get_param('value') ? $request->get_param('value') : 0;


        $data = array(
            'instance_id' => wc_clean($instance_id),
            'zone_id' => absint($zone_id),
            'checked' => ( $value ) ? 1 : 0
        );
        if ( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        $result = MVX_Shipping_Zone::toggle_shipping_method($data, $vendor_id);
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        $message = $data['checked'] ? __('Shipping method enabled successfully', 'multivendorx') : __('Shipping method disabled successfully', 'multivendorx');
        wp_send_json_success($message);
    }

    public function mvx_delete_shipping_method($request) {
        global $MVX;
        $zone_id = $request->get_param('zone_id') ? $request->get_param('zone_id') : 0;
        $instance_id = $request->get_param('instance_id') ? ($request->get_param('instance_id')) : 0;
        $vendor_id = $request->get_param('vendor_id') ? wc_clean($request->get_param('vendor_id')) : 0;

        $data = array(
            'zone_id' => wc_clean($zone_id),
            'instance_id' => wc_clean($instance_id)
        );
        
        if ( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        
        $result = MVX_Shipping_Zone::delete_shipping_methods($data, $vendor_id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'mvx');
        }

        wp_send_json_success(__('Shipping method deleted', 'multivendorx'));
    }

    public function mvx_update_vendor_shipping_method($request) {
        global $MVX;
        $data_details = $request->get_param('data_details') ? $request->get_param('data_details') : 0;
        $change_value = $request->get_param('change_value') ? wc_clean($request->get_param('change_value')) : 0;

        $vendorid = $request->get_param('vendorid') ? wc_clean($request->get_param('vendorid')) : 0;
        $zoneid = $request->get_param('zoneid') ? wc_clean($request->get_param('zoneid')) : 0;

        if ( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }

        do_action( 'mvx_before_update_shipping_method', $data_details );
        $result = MVX_Shipping_Zone::update_shipping_method($data_details);
        $MVX->load_class('shipping-gateway');
        MVX_Shipping_Gateway::load_class('shipping-method');
        $vendor_shipping = new MVX_Vendor_Shipping_Method();
        $vendor_shipping->set_post_data($data_details['settings']);
        $vendor_shipping->process_admin_options();

        // clear shipping transient
        WC_Cache_Helper::get_transient_version('shipping', true);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'MVX');
        }

        wp_send_json_success(__('Shipping method updated', 'multivendorx'));


    }

    public function mvx_add_shipping_option() {
        $option_lists = array();
        $shipping_methods = mvx_get_shipping_methods();
        if ($shipping_methods) {
            foreach ($shipping_methods as $key => $method) {
                $option_lists[] = array(
                    'value' => esc_attr( $method->id ),
                    'label' => esc_attr( $method->get_method_title() )
                );
            }
        }
        return $option_lists;
    }

    public function mvx_add_vendor_shipping_method($request) {
        global $MVX;
        $vendorid = $request->get_param('vendorid') ? absint($request->get_param('vendorid')) : 0;
        $method_id = $request->get_param('method_id') ? wc_clean($request->get_param('method_id')) : 0;
        $zoneid = $request->get_param('zoneid') ? absint($request->get_param('zoneid')) : 0;
        $this->mvx_backend_add_shipping_method($zoneid, $method_id, $vendorid);
    }

     public function mvx_backend_add_shipping_method($zone_id, $method_id, $vendor_id) {
        global $MVX;
        $data = array(
            'zone_id' => $zone_id,
            'method_id' => $method_id
        );
        if ( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        $result = MVX_Shipping_Zone::add_shipping_methods($data, $vendor_id);
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message(), 'mvx');
        }

        wp_send_json_success(__('Shipping method added successfully', 'multivendorx'));
    }

    public function mvx_specific_vendor_shipping_zone($request) {
        global $MVX;
        $vendor_id = $request->get_param('vendor_id') ? absint($request->get_param('vendor_id')) : 0;
        $zone_ids = $request->get_param('zone_id') ? absint($request->get_param('zone_id')) : 0;

        if ( !class_exists( 'MVX_Shipping_Zone' ) ) {
            $MVX->load_vendor_shipping();
        }
        $user_list = array();
        $zones = MVX_Shipping_Zone::get_vendor_zone($zone_ids, $vendor_id);
        if ($zones) {
        //$zone = WC_Shipping_Zones::get_zone(absint($zone_ids));

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

            $state_selct_final = array();
            if ($state_key_by_country) {
                foreach ($state_key_by_country as $key => $value) {
                    $state_selct_final[] = array(
                        'value' => $key,
                        'label' => $value,
                    );
                }
            }


            // display specific state value
            $new_state_list = array();
            if ($state_selct_final) {
                foreach ($state_selct_final as $state_key => $state_value) {
                    if ($states && in_array($state_value['value'], $states)) {
                        $new_state_list[] = $state_selct_final[$state_key];
                    }
                }
            }

            $user_list = array(
                'zones' => $zones,
                'vendor_shipping_methods' => $vendor_shipping_methods,
                'postcodes' => $postcodes,
                'state_select' => $state_selct_final,
                'get_database_state_name'   => $new_state_list
            );
        }
        return rest_ensure_response($user_list);
    }

    public function mvx_specific_vendor_shipping($request) {
        $user_list = array();
        $vendor_id = $request->get_param('vendor_id') ? absint($request->get_param('vendor_id')) : 0;
        $vendor_all_shipping_zones = mvx_get_shipping_zone('', $vendor_id);
        if (!empty($vendor_all_shipping_zones)) {
            foreach ($vendor_all_shipping_zones as $key => $vendor_shipping_zones) {

                $vendor_shipping_methods = $vendor_shipping_zones['shipping_methods'];
                $vendor_shipping_methods_titles = array();
                if ($vendor_shipping_methods) {
                    foreach ($vendor_shipping_methods as $key_child => $shipping_method) {
                        $class_name = 'yes' === $shipping_method['enabled'] ? 'method_enabled' : 'method_disabled';
                        $vendor_shipping_methods_titles[] = "<li class='mvx-shipping-zone-method wc-shipping-zone-method $class_name'>" . $shipping_method['title'] . "</li>";
                    }
                }
                $vendor_shipping_methods_titles = implode('', $vendor_shipping_methods_titles);

                $user_list[] = apply_filters('mvx_backend_list_table_vendors_shipping_columns_data', array(
                        'zone_name' => "<a href='". sprintf('?page=%s&ID=%s&name=%s&zone_id=%s', 'mvx#&submenu=vendor', $vendor_id, 'vendor-shipping', $vendor_shipping_zones['zone_id']) ."'>". $vendor_shipping_zones['zone_name'] ."</a>",
                        'region' => $vendor_shipping_zones['formatted_zone_location'],
                        'shipping_method' => empty($vendor_shipping_methods) ? __('No shipping methods offered to this zone.', 'multivendorx') : $vendor_shipping_methods_titles,
                    ));
            }
        }
        return rest_ensure_response($user_list);
    }

    public function mvx_vendor_list_search() {
        $option_lists = $default_data = array();
        $user_query = new WP_User_Query(array('role' => 'dc_vendor', 'orderby' => 'registered', 'order' => 'ASC'));
        $users = $user_query->get_results();
        foreach($users as $user) {
            $option_lists[] = apply_filters('mvx_backend_vendor_search_list', array(
                'value' => sanitize_text_field($user->data->ID),
                'label' => sanitize_text_field($user->data->display_name)
            ), $user);
        }
        $default_data[] = array('value'   =>  '', 'label'  =>  __('Please select an option', 'multivendorx'));
        return rest_ensure_response(array_merge($default_data, $option_lists));
    }

    public function mvx_specific_search_vendor($request) {
        $vendor_name = $request->get_param('vendor_id') ? $request->get_param('vendor_id') : 0;
        $search_vendor = [];
        $all_vendors = $this->mvx_list_all_vendor('');
        if ($all_vendors) {
            foreach ($all_vendors->data as $vendor_key => $vendor_value) {
                if (stripos($vendor_value['sample_title'], $vendor_name) !== false || stripos($vendor_value['username'], $vendor_name) !== false || stripos($vendor_value['email'], $vendor_name) !== false) {
                    $search_vendor[]    =   $all_vendors->data[$vendor_key];
                }
            }
        }
        return rest_ensure_response($search_vendor);
    }

    public function mvx_all_vendor_followers($request) {
        $user_list = array();
        $vendor_id = $request->get_param('vendor_id') ? absint($request->get_param('vendor_id')) : 0;
        $mvx_vendor_followed_by_customer = get_user_meta( $vendor_id, 'mvx_vendor_followed_by_customer', true ) ? get_user_meta( $vendor_id, 'mvx_vendor_followed_by_customer', true ) : array();
        if ($mvx_vendor_followed_by_customer) {
            foreach ($mvx_vendor_followed_by_customer as $key_folloed => $value_followed) {
                $user_details = get_user_by( 'ID', $value_followed['user_id'] );
                if ( !$user_details ) continue;
                $user_list[] = apply_filters('mvx_backend_list_table_vendors_followers_columns_data', array(
                    'name' => $user_details->data->display_name,
                    'time' => human_time_diff(strtotime($value_followed['timestamp'])),
                ), $user_details);
            }
        }
        return $user_list;
        die;
    }

    public function mvx_create_vendor($request) {
        $fetch_data = $request->get_param('model');
        $all_details = array();
        $userdata = array(
            'user_login' => isset( $fetch_data['user_login'] ) ? sanitize_user( $fetch_data['user_login'] ) : '',
            'user_pass' => isset( $fetch_data['password'] ) ? sanitize_text_field( wp_unslash( $fetch_data['password'] ) ) : '',
            'user_email' => isset( $fetch_data['user_email'] ) ? sanitize_email( $fetch_data['user_email'] ) : '',
            'user_nicename' => isset( $fetch_data['user_nicename'] ) ? sanitize_text_field( wp_unslash( $fetch_data['user_nicename'] ) ) : '',
            'first_name' => isset( $fetch_data['first_name'] ) ? sanitize_text_field( wp_unslash( $fetch_data['first_name'] ) ) : '',
            'last_name' => isset( $fetch_data['last_name'] ) ? sanitize_text_field( wp_unslash( $fetch_data['last_name'] ) ) : '',
            'role' => 'dc_vendor',
        );
        $user_id = wp_insert_user( $userdata ) ;
        if ( is_wp_error( $user_id ) ) {
            $error_string = $user_id->get_error_message();
            $message = $error_string;
            $redirect_to = '';
            $user_id = null;
        } else {
            $email = WC()->mailer()->emails['WC_Email_Vendor_New_Account'];
            $email->trigger( $user_id, $userdata['user_pass'], false);
            if (isset($fetch_data['vendor_profile_image']) && $fetch_data['vendor_profile_image'] != '') update_user_meta($user_id, "_vendor_profile_image", absint( $fetch_data['vendor_profile_image']));
            $message = __( 'Vendor successfully created!', 'multivendorx' );
            $redirect_to = apply_filters('mvx_add_new_vendor_redirect_url', admin_url('admin.php?page=mvx#&submenu=vendor&ID='.$user_id. '&name=vendor-personal'));
        }

        if ($redirect_to) {
            $all_details['redirect_link']   =   $redirect_to;
        }
        $all_details['error'] = $message;
        return $all_details;
        die();
    }

    public function mvx_update_vendor($request) {
        $user_id = $request->get_param('vendor_id') ? absint( $request->get_param('vendor_id') ) : 0;
        $model = $request->get_param('model') ? ( $request->get_param('model') ) : 0;
        $modulename = $request->get_param('modulename') ? ( $request->get_param('modulename') ) : 0;

        $mvx_shipping_by_distance = $mvx_shipping_by_country = array();

        // distnce wise shipping save
        if (isset($model['mvx_shipping_by_distance_rates']) && !empty($model['mvx_shipping_by_distance_rates'])) {
            update_user_meta($user_id, '_mvx_shipping_by_distance_rates', array_filter( array_map( 'wc_clean', $model['mvx_shipping_by_distance_rates'])));
        }

        if (isset($model['mvx_byd_default_cost']) && !empty($model['mvx_byd_default_cost'])) {
            $mvx_shipping_by_distance['_default_cost'] = isset($model['mvx_byd_default_cost']) ? wc_clean($model['mvx_byd_default_cost'] ) : '';
        }

        if (isset($model['mvx_byd_max_distance']) && !empty($model['mvx_byd_max_distance'])) {
            $mvx_shipping_by_distance['_max_distance'] = isset($model['mvx_byd_max_distance']) ? wc_clean($model['mvx_byd_max_distance'] ) : '';
        }

        if (isset($model['mvx_byd_enable_local_pickup']) && !empty($model['mvx_byd_enable_local_pickup'])) {
            $mvx_shipping_by_distance['_enable_local_pickup'] = isset($model['mvx_byd_enable_local_pickup']) ? wc_clean($model['mvx_byd_enable_local_pickup'] ) : '';
        }

        if (isset($model['mvx_byd_local_pickup_cost']) && !empty($model['mvx_byd_local_pickup_cost'])) {
            $mvx_shipping_by_distance['_local_pickup_cost'] = isset($model['mvx_byd_local_pickup_cost']) ? wc_clean($model['mvx_byd_local_pickup_cost'] ) : '';
        }

        if (!empty($mvx_shipping_by_distance)) {
            update_user_meta($user_id, '_mvx_shipping_by_distance', $mvx_shipping_by_distance);
        }
        
        // shipping by country

        if (isset($model['mvx_country_shipping_rates']) && !empty($model['mvx_country_shipping_rates'])) {            
            $mvx_country_rates = $mvx_state_rates = array();
            foreach( $model['mvx_country_shipping_rates'] as $mvx_shipping_rates ) {
                if ( $mvx_shipping_rates['mvx_country_to']['value'] ) {
                    if ( $mvx_shipping_rates['nested_datas'] && !empty( $mvx_shipping_rates['nested_datas'] ) ) {
                        foreach( $mvx_shipping_rates['nested_datas'] as $nested_datas ) {

                            if ( $nested_datas['mvx_state_to'] ) {
                                $mvx_state_rates[$mvx_shipping_rates['mvx_country_to']['value']][$nested_datas['mvx_state_to']['value']] = $nested_datas['mvx_state_to_price'];
                            }

                        }
                    }
                    $mvx_country_rates[$mvx_shipping_rates['mvx_country_to']['value']] = $mvx_shipping_rates['mvx_country_to_price'];
                }
            }
            mvx_update_user_meta( $user_id, '_mvx_country_rates', $mvx_country_rates );
            mvx_update_user_meta( $user_id, '_mvx_state_rates', $mvx_state_rates );
            mvx_update_user_meta( $user_id, '_mvx_country_shipping_rates', $model['mvx_country_shipping_rates'] );
        }

        if (isset($model['mvx_shipping_type_price']) && !empty($model['mvx_shipping_type_price'])) {
            $mvx_shipping_by_country['_mvx_shipping_type_price'] = isset($model['mvx_shipping_type_price']) ? wc_clean($model['mvx_shipping_type_price'] ) : '';
        }

        if (isset($model['mvx_additional_product']) && !empty($model['mvx_additional_product'])) {
            $mvx_shipping_by_country['_mvx_additional_product'] = isset($model['mvx_additional_product']) ? wc_clean($model['mvx_additional_product'] ) : '';
        }

        if (isset($model['mvx_additional_qty']) && !empty($model['mvx_additional_qty'])) {
            $mvx_shipping_by_country['_mvx_additional_qty'] = isset($model['mvx_additional_qty']) ? wc_clean($model['mvx_additional_qty'] ) : '';
        }

        if (isset($model['mvx_byc_free_shipping_amount']) && !empty($model['mvx_byc_free_shipping_amount'])) {
            $mvx_shipping_by_country['_free_shipping_amount'] = isset($model['mvx_byc_free_shipping_amount']) ? wc_clean($model['mvx_byc_free_shipping_amount'] ) : '';
        }

        if (isset($model['mvx_byc_enable_local_pickup']) && !empty($model['mvx_byc_enable_local_pickup'])) {
            $mvx_shipping_by_country['_enable_local_pickup'] = isset($model['mvx_byc_enable_local_pickup']) ? wc_clean($model['mvx_byc_enable_local_pickup'] ) : '';
        }

        if (isset($model['mvx_byc_local_pickup_cost']) && !empty($model['mvx_byc_local_pickup_cost'])) {
            $mvx_shipping_by_country['_local_pickup_cost'] = isset($model['mvx_byc_local_pickup_cost']) ? wc_clean($model['mvx_byc_local_pickup_cost'] ) : '';
        }

        if (!empty($mvx_shipping_by_country)) {
            update_user_meta($user_id, '_mvx_shipping_by_country', array_filter( array_map( 'wc_clean', $mvx_shipping_by_country) ) );
        }

        // vendor personal details


            $vendor = get_mvx_vendor($user_id);
            if ($vendor) {

                // vendor personal
                if ($modulename == 'vendor-personal') {
                    $userdata = array(
                        'ID' => $user_id,
                        /*'user_login' => isset( $model['user_login'] ) ? sanitize_user( $model['user_login'] ) : '',
                        'user_pass' => isset( $model['password'] ) ? sanitize_text_field( wp_unslash( $model['password'] ) ) : '',
                        'user_email' => isset( $model['user_email'] ) ? sanitize_email( $model['user_email'] ) : '',
                        'user_nicename' => isset( $model['user_nicename'] ) ? sanitize_text_field( wp_unslash( $model['user_nicename'] ) ) : '',
                        'display_name' => isset( $model['display_name'] ) && isset($model['display_name']['value']) ? $model['display_name']['value'] : '',
                        'first_name' => isset( $model['first_name'] ) ? sanitize_text_field( wp_unslash( $model['first_name'] ) ) : '',
                        'last_name' => isset( $model['last_name'] ) ? sanitize_text_field( wp_unslash( $model['last_name'] ) ) : '',*/
                    );

                    if (isset($model['user_login']) && !empty($model['user_login'])) {
                        $userdata['user_login'] = isset( $model['user_login'] ) ? sanitize_user( $model['user_login'] ) : '';
                    }
                    if (isset($model['last_name']) && !empty($model['last_name'])) {
                        $userdata['last_name'] = isset( $model['last_name'] ) ? sanitize_text_field( wp_unslash( $model['last_name'] ) ) : '';
                    }
                    if (isset($model['first_name']) && !empty($model['first_name'])) {
                        $userdata['first_name'] = isset( $model['first_name'] ) ? sanitize_text_field( wp_unslash( $model['first_name'] ) ) : '';
                    }
                    if (isset($model['user_nicename']) && !empty($model['user_nicename'])) {
                        isset( $model['user_nicename'] ) ? sanitize_text_field( wp_unslash( $model['user_nicename'] ) ) : '';
                    }

                    if (isset($model['user_email']) && !empty($model['user_email'])) {
                        $userdata['user_email'] = isset( $model['user_email'] ) ? sanitize_email( $model['user_email'] ) : '';
                    }
                    if (isset($model['password']) && !empty($model['password']) && $model['password'] != '$P$BBy/qPu84qp8n2TQ.fFhyJNoru.orT1') {
                        $userdata['user_pass'] = isset( $model['password'] ) ? sanitize_text_field( wp_unslash( $model['password'] ) ) : '';
                    }
                    if (isset($model['display_name']['value']) && !empty($model['display_name']['value'])) {
                        $userdata['display_name'] = isset( $model['display_name'] ) && isset($model['display_name']['value']) ? $model['display_name']['value'] : '';
                    }
                    $user_id = wp_update_user( $userdata ) ;
                }

                if ($modulename == 'vendor-store' || $modulename == 'vendor-social' || $modulename == 'vendor-payments') {
                    foreach($model as $key => $value) {
                        $skip_vendor_update_data = apply_filters('mvx_skipped_vendor_update_keys', array('mvx_commission_type'));
                        if (in_array($key, $skip_vendor_update_data)) continue;

                        if ($value != '') {
                            if ($key == 'vendor_page_title') {
                                if (!$vendor->update_page_title(wc_clean($value))) {
                                    $all_details['error']   =  __('Title Update Error', 'multivendorx');
                                    return $all_details; 
                                }
                            } else if ($key == 'vendor_page_slug') {
                                if (!$vendor->update_page_slug(wc_clean($value))) {
                                    $all_details['error']   =  __('Slug already exists', 'multivendorx');
                                    return $all_details; 
                                }
                            } else if ($key === "vendor_country") {
                                if (isset($value['value'])) {
                                    $country_code = wc_clean( wp_unslash( $value['value'] ) );
                                    $country_data = wc_clean( wp_unslash( WC()->countries->get_countries() ) );
                                    $country_name = ( isset( $country_data[ $country_code ] ) ) ? $country_data[ $country_code ] : $country_code; //To get country name by code
                                    update_user_meta($user_id, '_' . $key, $country_name);
                                    update_user_meta($user_id, '_' . $key . '_code', $country_code);
                                }
                            } else if ($key === "vendor_state") {
                                $country_code = isset( $model['vendor_country'] ) && isset( $model['vendor_country']['value'] ) ? wc_clean( wp_unslash( $model['vendor_country']['value'] ) ) : '';
                                $state_code = wc_clean( wp_unslash( $value['key'] ) );
                                $state_data = wc_clean( wp_unslash( WC()->countries->get_states($country_code) ) );
                                $state_name = ( isset( $state_data[$state_code] ) ) ? $state_data[$state_code] : $state_code; //to get State name by state code
                                update_user_meta($user_id, '_' . $key, $state_name);
                                update_user_meta($user_id, '_' . $key . '_code', $state_code);
                            } else if (substr($key, 0, strlen("vendor_")) === "vendor_") {

                                if (is_array($value)) {
                                    update_user_meta($user_id, "_" . $key, wp_unslash( $value['value'] ) );
                                } else {
                                    update_user_meta($user_id, "_" . $key, wp_unslash( $value ) );
                                }
                                
                            }
                        } else {
                            if (substr($key, 0, strlen("vendor_")) === "vendor_") {
                                delete_user_meta($user_id, "_" . $key);
                            }
                        }
                    }
                }

    }

        // policy details
        if ($modulename == 'vendor-policy') {
            if ( isset( $model['vendor_shipping_policy'] ) ) {
                update_user_meta( $user_id, '_vendor_shipping_policy', stripslashes( html_entity_decode( $model['vendor_shipping_policy'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
            }
            if ( isset( $model['vendor_refund_policy'] ) ) {
                update_user_meta( $user_id, '_vendor_refund_policy', stripslashes( html_entity_decode( $model['vendor_refund_policy'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
            }
            if ( isset( $model['vendor_cancellation_policy'] ) ) {
                update_user_meta( $user_id, '_vendor_cancellation_policy', stripslashes( html_entity_decode( $model['vendor_cancellation_policy'], ENT_QUOTES, get_bloginfo( 'charset' ) ) ) );
            }
        }

        if (isset($model['_store_location']) && !empty($model['_store_location'])) {
            mvx_update_user_meta($user_id, '_store_location', wc_clean($model['_store_location']));
        }
        if (isset($model['_store_lat']) && !empty($model['_store_lat'])) {
            mvx_update_user_meta($user_id, '_store_lat', wc_clean($model['_store_lat']));
        }
        if (isset($model['_store_lng']) && !empty($model['_store_lng'])) {
            mvx_update_user_meta($user_id, '_store_lng', wc_clean($model['_store_lng']));
        }
        if (isset($model['vendor_profile_image']) && !empty($model['vendor_profile_image'])) {
            mvx_update_user_meta($user_id, '_vendor_profile_image', $model['vendor_profile_image']);
        }
        
        do_action('mvx_vendor_details_update', $model, $vendor);

        $all_details['error'] = __('Settings saving ...', 'multivendorx');
        return $all_details; 
        die;
    }

    public function mvx_all_vendor_details($request) {
        $role = $request && $request->get_param('role') ? $request->get_param('role') : '';
        return $this->mvx_list_all_vendor('', $role);
    }

    public function mvx_list_all_vendor($specific_id = array(), $role = '') {
        global $MVX;
        $user_list = array();
        $role_status = false;
        if ($role == 'suspended') {
            $role_status = true;
        }
        $role = $role == 'suspended' ? '' : $role;
        $user_query = new WP_User_Query(array('role__in' => $role ? array($role) : array('dc_vendor', 'dc_pending_vendor', 'dc_rejected_vendor'), 'orderby' => 'registered', 'order' => 'ASC', 'include' => $specific_id));
        $users = $user_query->get_results();
        foreach($users as $user) {
            $vendor = get_mvx_vendor($user->data->ID);
            $product_count = 0;
            $vendor_permalink = '';
            $status = $status_text = "";
            $vendor_link = sprintf('?page=%s&ID=%s&name=vendor-personal', 'mvx#&submenu=vendor', $user->data->ID);
            if ($vendor) {
                $vendor_products = $vendor->get_products_ids();
                $vendor_permalink = $vendor->permalink;
                $product_count = count($vendor_products);
            }
            
            if ($role_status) {
                $is_block = $vendor ? get_user_meta($vendor->id, '_vendor_turn_off', true) : '';
                if (!$is_block) {
                    continue;
                }
            }
            
            if (in_array('dc_vendor', $user->roles)) {
                $is_block = $vendor ? get_user_meta($vendor->id, '_vendor_turn_off', true) : '';
            
                if ($is_block) {
                    $status_text = "Suspended";
                    $status = "<p class='vendor-status suspended-vendor'>" . __('Suspended', 'multivendorx') . "</p>";
                } else {
                    $status_text = "Approved";
                    $status = "<p class='vendor-status approved-vendor'>" . __('Approved', 'multivendorx') . "</p>";
                }
            } else if (in_array('dc_rejected_vendor', $user->roles)) {
                $status_text = "Rejected";
                $status = "<p class='vendor-status rejected-vendor'>" . __('Rejected', 'multivendorx') . "</p>";
                $vendor_link = sprintf('?page=%s&ID=%s&name=vendor-application', 'mvx#&submenu=vendor', $user->data->ID);
            } else if (in_array('dc_pending_vendor', $user->roles)) {
                $status_text = "Pending";
                $status = "<p class='vendor-status pending-vendor'>" . __('Pending', 'multivendorx') . "</p>";
                $vendor_link = sprintf('?page=%s&ID=%s&name=vendor-application', 'mvx#&submenu=vendor', $user->data->ID);
            }

            $vendor_profile_image = get_user_meta($user->data->ID, '_vendor_profile_image', true);
            if (isset($vendor_profile_image)) $image_info = wp_get_attachment_image_src( $vendor_profile_image , array(32, 32) );
            $final_image = isset($image_info[0]) ? $image_info[0] : get_avatar_url($user->data->ID, array('size' => 32));
            $status_url = $status_text == 'Pending' || $status_text == 'Rejected' ? 'vendor-application' : 'vendor-personal';
            $name_display = "<div class='mvx-vendor-icon-name'><img src='". $final_image ."' width='20' height='20' ></img><a href='". sprintf('?page=%s&ID=%s&name=%s', 'mvx#&submenu=vendor', $user->data->ID, $status_url) ."'>" . $user->data->display_name . "</a></div>";

            $action_display = "
            <div class='mvx-vendor-action-icon'>
                <i class='mvx-font icon-edit'></i>
                <i class='mvx-font icon-close'></i>
            </div>";
            $product_count = $vendor ? sprintf('<a href="%1$s">' . $product_count . '</a>', admin_url('edit.php?post_type=product&dc_vendor_shop=' . $vendor->page_title)) : '';
            $mail_to_email = sprintf('<a href="mailto:%1$s">' . $user->data->user_email . '</a>', $user->data->user_email);
            $user_list[] = apply_filters('mvx_vendor_list_table_columns_data', array(
                'ID'            => $user->data->ID,
                'name'          => $name_display,
                'sample_title'  => $user->data->display_name,
                'link'          => $vendor_link,
                'link_shop'     => $vendor ? $vendor->permalink : '',
                'admin_link'    => admin_url('admin.php?page=mvx#&submenu=vendor&ID='. $user->data->ID .'&name=vendor-personal'),
                'email'         => $mail_to_email,
                'registered'    => get_date_from_gmt( $user->data->user_registered ),
                'products'      => $product_count,
                'status'        => $status,
                'status_raw_text'   =>  $status_text,
                'permalink'     => $vendor_permalink,
                'username'      => $user->data->user_login,
                'action'        => $action_display 
            ), $user);
        }
        return rest_ensure_response($user_list);
    }

    public function mvx_save_registration_forms($request) {
        $form_data = json_decode($request->get_param( 'form_data' ), true);
        if (!empty($form_data) && is_array($form_data)) {
            foreach ($form_data as $key => $value) {
                $form_data[$key]['hidden'] = false;
            }
        }
        mvx_update_option('mvx_new_vendor_registration_form_data', $form_data);
        die;
    }

    public function mvx_get_registration_forms_data() {
        $mvx_vendor_registration_form_data = mvx_get_option('mvx_new_vendor_registration_form_data') ? mvx_get_option('mvx_new_vendor_registration_form_data') : [];
        return rest_ensure_response( $mvx_vendor_registration_form_data );
    }

    public function mvx_save_dashpages($req) {
        global $MVX;
        $all_details = array();
        $modulename = $req->get_param('modulename');
        $modulename = str_replace("-", "_", $modulename);
        $get_managements_data = $req->get_param( 'model' );
        $optionname = 'mvx_'.$modulename.'_tab_settings';
        mvx_update_option($optionname, $get_managements_data);
        if ($modulename == 'products_capability') {
            $MVX->vendor_caps->update_mvx_vendor_role_capability();
        }
        $all_details['error'] = __('Settings Saved', 'multivendorx');
        return $all_details;
        die;
    }

    public function mvx_get_module_lists($request, $by_id = '') {
        $module_id = $request->get_param( 'module_id' ) ? $request->get_param( 'module_id' ) : 'all';
        $all_module_lists = mvx_list_all_modules();

        if ($module_id && $module_id == 'all') {
            $response = $all_module_lists ? array_values($all_module_lists) : false;
        } else {
            $get_searchable_data = $set_2nd_search_item_data = $set_3rd_search_item_data = [];
            foreach ($all_module_lists as $key_parent => $value_parent) {
                foreach ($value_parent['options'] as $key_child => $value_child) {
                    if (!empty($by_id)) {
                        if (stripos($value_child['id'], $module_id) !== false) {
                            $get_searchable_data[] = array('id' => $value_child['id'], 'label' => $value_parent['label'], 'parent_key' => $key_parent, 'child_option_key' => $key_child);
                        }
                    } else {
                        if (stripos($value_child['name'], $module_id) !== false) {
                            $get_searchable_data[] = array('id' => $value_child['id'], 'label' => $value_parent['label'], 'parent_key' => $key_parent, 'child_option_key' => $key_child);
                        }
                    }
                }
            }

            foreach ($get_searchable_data as $key1 => $value1) {
                $list_include = wp_list_pluck($set_2nd_search_item_data, 'label');
                if (in_array($value1['label'], $list_include)) {
                    $list_of_datas_module_stack[] = $all_module_lists[$value1['parent_key']]['options'][$value1['child_option_key']];
                } else {
                    $list_of_datas_module_stack = array($all_module_lists[$value1['parent_key']]['options'][$value1['child_option_key']]);
                }
                $set_2nd_search_item_data[] = array(
                    'label' => $value1['label'],
                    'options' => $list_of_datas_module_stack
                );
            }

            foreach ($set_2nd_search_item_data as $keyi => $valuei) {
                $cut_arrays = array_slice($set_2nd_search_item_data, $keyi + 1);
                $listing_search_valus = wp_list_pluck($cut_arrays, 'label');
                if (in_array($valuei['label'], $listing_search_valus)) {
                    $set_3rd_search_item_data[] = $keyi;
                }
            }

            foreach ($set_3rd_search_item_data as $key => $value) {
                unset($set_2nd_search_item_data[$value]);
            }

            $response = array_values($set_2nd_search_item_data);
        }
        return rest_ensure_response( $response );
    }

    public function save_checkbox_module($request) {
        $module_id = $request['module_id'];
        $is_checked = $request['is_checked'];
        $active_module_list = mvx_get_option('mvx_all_active_module_list') ? mvx_get_option('mvx_all_active_module_list') : array();
        if ($module_id && !in_array($module_id, $active_module_list) && $is_checked) {
            array_push($active_module_list, $module_id);
        } elseif ($module_id && in_array($module_id, $active_module_list) && !$is_checked) {
            unset($active_module_list[array_search($module_id, $active_module_list)]);
        }
        mvx_update_option( 'mvx_all_active_module_list', $active_module_list );
        do_action('mvx_after_module_active', $module_id, $is_checked, $active_module_list);
        //if ( in_array( $module_id, apply_filters('mvx_module_enabled_reload', array('marketplace-membership')))) {
            return rest_ensure_response( 'reload' );
        //}
        return rest_ensure_response( 'success' );
    }

    public function mvx_find_individual_vendor_tabs($request) {
        $vendor_id = $request->get_param( 'vendor_id' ) ? $request->get_param( 'vendor_id' ) : 0;
        $user = get_user_by("ID", $vendor_id);

        $marketplace_vendors = [];
        $marketplace_vendors = apply_filters( 'mvx_backend_vendor_tab_list', array(
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
        ) );
        if (in_array('dc_pending_vendor', $user->roles) || in_array('dc_rejected_vendor', $user->roles)) {
            unset($marketplace_vendors[0], $marketplace_vendors[1], $marketplace_vendors[2], $marketplace_vendors[3]);
        }

        if (empty(array_intersect(['dc_pending_vendor', 'dc_rejected_vendor'], $user->roles))) {
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
        }
        return rest_ensure_response(array_values($marketplace_vendors));
    }

    public function save_settings_permission() {
        return true;
    }
}
