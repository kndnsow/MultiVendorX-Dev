<?php
if (!defined('ABSPATH'))
    exit;

/**
 * @class 		MVX Commission Post Class-
 *
 * @version		2.2.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class MVX_Commission {

    private $post_type;
    public $file;

    public function __construct() {
        $this->post_type = 'dc_commission';
        $this->register_post_type();
    }

    /**
     * Register commission post type
     *
     * @access public
     * @return void
     */
    function register_post_type() {
        global $MVX;
        if (post_type_exists($this->post_type))
            return;
        $labels = array(
            'name' => _x('Commissions', 'post type general name', 'multivendorx'),
            'singular_name' => _x('Commission', 'post type singular name', 'multivendorx'),
            'add_new' => _x('Add New', $this->post_type, 'multivendorx'),
            'add_new_item' => sprintf(__('Add New %s', 'multivendorx'), __('Commission', 'multivendorx')),
            'edit_item' => sprintf(__('Edit %s', 'multivendorx'), __('Commission', 'multivendorx')),
            'new_item' => sprintf(__('New %s', 'multivendorx'), __('Commission', 'multivendorx')),
            'all_items' => sprintf(__('All %s', 'multivendorx'), __('Commissions', 'multivendorx')),
            'view_item' => sprintf(__('View %s', 'multivendorx'), __('Commission', 'multivendorx')),
            'search_items' => sprintf(__('Search %s', 'multivendorx'), __('Commissions', 'multivendorx')),
            'not_found' => sprintf(__('No %s found', 'multivendorx'), __('Commissions', 'multivendorx')),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'multivendorx'), __('Commissions', 'multivendorx')),
            'parent_item_colon' => '',
            'all_items' => __('Commissions', 'multivendorx'),
            'menu_name' => __('Commissions', 'multivendorx')
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'query_var' => false,
            'rewrite' => true,
            'capability_type' => 'shop_order',
            'create_posts' => false,
            'map_meta_cap' => true,
            'has_archive' => true,
            'hierarchical' => true,
            'supports' => array('title'),
            'menu_position' => 5,
        );

        register_post_type($this->post_type, $args);
    }

    /**
     * Create commission
     * @param int $order_id
     * @param array $args
     * @return int $commission_id
     */
    public static function create_commission($order_id, $args = array()) {
        if ($order_id) {
            $vendor_id = get_post_meta($order_id, '_vendor_id', true);
            // create vendor commission
            $default = array(
                'post_type' => 'dc_commission',
                'post_title' => sprintf(__('Commission - %s', 'multivendorx'), strftime(_x('%B %e, %Y @ %I:%M %p', 'Commission date parsed by strftime', 'multivendorx'), current_time('timestamp'))),
                'post_status' => 'private',
                'ping_status' => 'closed',
                'post_excerpt' => '',
                'post_author' => $vendor_id
            );

            $commission_data = apply_filters('mvx_create_vendor_commission_args', wp_parse_args($args, $default));

            $commission_id = wp_insert_post($commission_data);
            if ($commission_id) {
                // add order id with commission meta
                update_post_meta($commission_id, '_commission_order_id', $order_id);
                update_post_meta($commission_id, '_paid_status', 'unpaid');
                // for BW supports
                $vendor = get_mvx_vendor( $vendor_id );
                update_post_meta($commission_id, '_commission_vendor', $vendor->term_id);
                /**
                 * Action hook to update commission meta data.
                 *
                 * @since 3.4.0
                 */
                do_action('mvx_commission_update_commission_meta', $commission_id);

                self::add_commission_note($commission_id, sprintf(__('Commission for order <a href="%s">(ID : %s)</a> is created.', 'multivendorx'), get_admin_url() . 'post.php?post=' . $order_id . '&action=edit', $order_id), $vendor_id);
                return $commission_id;
            }
        }
        return false;
    }
    
    /**
     * Calculate commission
     * @param int $commission_id
     * @param object $order
     * @param bool $recalculate
     * @return void 
     */
    public static function calculate_commission( $commission_id, $order, $recalculate = false ) {
        global $MVX;
        if ($commission_id && $order) {
            $commission_type = mvx_get_settings_value($MVX->vendor_caps->payment_cap['commission_type']);
            $vendor_id = get_post_meta($order->get_id(), '_vendor_id', true);
             // line item commission
             $commission_amount = $shipping_amount = $tax_amount = $shipping_tax_amount = 0;
             $commission_rates = array();
            // if recalculate is set
            if( $recalculate ) {
                foreach ($order->get_items() as $item_id => $item) {
                    $parent_order_id = wp_get_post_parent_id( $order->get_id() );
                    $parent_order = wc_get_order( $parent_order_id );
                    $variation_id = isset($item['variation_id']) && !empty($item['variation_id']) ? $item['variation_id'] : 0;
                    $item_commission = $MVX->commission->get_item_commission($item['product_id'], $variation_id, $item, $parent_order_id, $item_id);
                    $commission_values = $MVX->commission->get_commission_amount($item['product_id'], $has_vendor->term_id, $variation_id, $item_id, $parent_order);
                    $commission_rate = array('mode' => $MVX->vendor_caps->payment_cap['revenue_sharing_mode'], 'type' => $commission_type);
                    $commission_rate['commission_val'] = isset($commission_values['commission_val']) ? $commission_values['commission_val'] : 0;
                    $commission_rate['commission_fixed'] = isset($commission_values['commission_fixed']) ? $commission_values['commission_fixed'] : 0;
                    
                    wc_update_order_item_meta( $item_id, '_vendor_item_commission', $item_commission );
                    $commission_amount += floatval($item_commission);
                    $commission_rates[$item_id] = $commission_rate;
                }
            } else {
                $commission_rates = get_post_meta($order->get_id(), 'order_items_commission_rates', true);
                foreach ($order->get_items() as $item_id => $item) {
                    $product = $item->get_product();
                    $meta_data = $item->get_meta_data();
                    // get item commission
                    foreach ( $meta_data as $meta ) {
                        if($meta->key == '_vendor_item_commission'){
                            $commission_amount += floatval($meta->value);
                        }
                        if($meta->key == '_vendor_order_item_id'){
                            $order_item_id = absint($meta->value);
                            if(isset($commission_rates[$order_item_id])){
                                $rate = $commission_rates[$order_item_id];
                                $commission_rates[$item_id] = $rate;
                                unset($commission_rates[$order_item_id]); // update with vendor order item id for further use
                            }
                        }
                    }
                }
            }

            // fixed + percentage per vendor's order
            if ($commission_type == 'fixed_with_percentage_per_vendor') {
                $commission_amount = (float) $order->get_total() * ( (float) $MVX->vendor_caps->payment_cap['default_percentage'] / 100 ) + (float) $MVX->vendor_caps->payment_cap['fixed_with_percentage_per_vendor'];
            }
            
            /**
             * Action hook to adjust items commission rates before save.
             *
             * @since 3.4.0
            */
            update_post_meta($order->get_id(), 'order_items_commission_rates', apply_filters('mvx_vendor_order_items_commission_rates', $commission_rates, $order));
            
            // transfer shipping charges
            if ($MVX->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($vendor_id, '_vendor_give_shipping', true)) {
                $shipping_amount = $order->get_shipping_total();
            }
            // transfer tax charges
            foreach ( $order->get_items( 'tax' ) as $key => $tax ) { 
                if ($MVX->vendor_caps->vendor_payment_settings('give_tax') && $MVX->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($vendor_id, '_vendor_give_shipping', true) && !get_user_meta($vendor_id, '_vendor_give_tax', true)) {
                    $tax_amount += $tax->get_tax_total();
                    $shipping_tax_amount = $tax->get_shipping_tax_total();
                } else if ($MVX->vendor_caps->vendor_payment_settings('give_tax') && !get_user_meta($vendor_id, '_vendor_give_tax', true)) {
                    $tax_amount += $tax->get_tax_total();
                    $shipping_tax_amount = 0;
                } else {
                    $tax_amount = 0;
                    $shipping_tax_amount = 0;
                }

                if ($MVX->vendor_caps->vendor_payment_settings('give_tax') && get_mvx_global_settings('commission_calculation_on_tax') ) {
                    $tax_rate_id    = $tax->get_rate_id();
                    $tax_percent    = WC_Tax::get_rate_percent( $tax_rate_id );
                    $tax_rate       = str_replace('%', '', $tax_percent);
                    if ($tax_rate) {
                        $tax_amount = ($commission_amount * $tax_rate) / 100;
                    }
                }
            }
            
            // update commission meta
            if (0 < $order->get_total_discount() && isset($MVX->vendor_caps->payment_cap['commission_include_coupon']))
                update_post_meta($commission_id, '_commission_include_coupon', true);
            if ( 0 < $shipping_amount && $MVX->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($vendor_id, '_vendor_give_shipping', true))
                update_post_meta( $commission_id, '_commission_total_include_shipping', true );
            if ( 0 < $tax_amount && $MVX->vendor_caps->vendor_payment_settings('give_tax') && !get_user_meta($vendor_id, '_vendor_give_tax', true))
                update_post_meta( $commission_id, '_commission_total_include_tax', true );
            
            update_post_meta( $commission_id, '_commission_amount', $commission_amount );
            update_post_meta( $commission_id, '_shipping', $shipping_amount );
            update_post_meta( $commission_id, '_tax', ($tax_amount + $shipping_tax_amount) );
            /**
             * Action hook to update commission meta data.
             *
             * @since 3.4.0
             */
            do_action('mvx_commission_before_save_commission_total', $commission_id);
            $commission_total = (float) $commission_amount + (float) $shipping_amount + (float) $tax_amount + (float) $shipping_tax_amount;
            $commission_total = apply_filters('mvx_commission_total_amount', $commission_total, $commission_id);
            update_post_meta( $commission_id, '_commission_total', $commission_total );
            do_action( 'mvx_commission_after_save_commission_total', $commission_id, $order );

        }
        return false;
    }
    
    /**
     * Get commission status
     * @param int $commission_id
     * @param string $context
     * @return value 
     */
    public static function get_status( $commission_id, $context = 'view' ) {
        if($commission_id){
            $status = get_post_meta($commission_id, '_paid_status', true);
            $status_view = ucfirst(str_replace('_', ' ', $status));
            return $context == 'view' ? $status_view : $status;
        }
    }
    
    /**
     * Calculate commission total including refunds
     * @param int $commission_id
     * @param string $context
     * @return value 
     */
    public static function commission_totals( $commission_id, $context = 'view' ) {
        if($commission_id){
            $order_id = get_post_meta($commission_id, '_commission_order_id', true);
            $order = wc_get_order($order_id);
            $commission_total = get_post_meta( $commission_id, '_commission_total', true );
            // backward compatibility added
            if(!$commission_total){
                $commission_amt = get_post_meta($commission_id, '_commission_amount', true);
                $shipping_amt = get_post_meta($commission_id, '_shipping', true);
                $tax_amt = get_post_meta($commission_id, '_tax', true);
                $commission_total = (floatval($commission_amt) + floatval($shipping_amt) + floatval($tax_amt));
            }
            $commission_refunded_total = get_post_meta( $commission_id, '_commission_refunded', true );
            $total = floatval($commission_total) + floatval($commission_refunded_total);
            if($order)
                return $context == 'view' ? wc_price($total, array('currency' => $order->get_currency())) : $total;
        }
    }
    
    /**
     * Calculate commission amount total including refunds
     * @param int $commission_id
     * @param string $context
     * @return value 
     */
    public static function commission_amount_totals( $commission_id, $context = 'view' ) {
        if($commission_id){
            $order_id = get_post_meta($commission_id, '_commission_order_id', true);
            $order = wc_get_order($order_id);
            $commission_amount = get_post_meta( $commission_id, '_commission_amount', true );
            $commission_refunded_amount = get_post_meta( $commission_id, '_commission_refunded_items', true );
            $total = floatval($commission_amount) + floatval($commission_refunded_amount);
            if($order)
                return $context == 'view' ? wc_price($total, array('currency' => $order->get_currency())) : $total;
        }
    }
    
    /**
     * Calculate commission refunded amount total
     * @param int $commission_id
     * @param string $context
     * @return value 
     */
    public static function commission_refunded_totals( $commission_id, $context = 'view' ) {
        if($commission_id){
            $order_id = get_post_meta($commission_id, '_commission_order_id', true);
            $order = wc_get_order($order_id);
            $commission_refunded = get_post_meta( $commission_id, '_commission_refunded', true );
            return $context == 'view' ? wc_price($commission_refunded, array('currency' => $order->get_currency())) : $commission_refunded;
        }
    }
    
    /**
     * Calculate commission refunded amount total
     * @param int $commission_id
     * @param string $context
     * @return value 
     */
    public static function commission_items_refunded_totals( $commission_id, $context = 'view' ) {
        if($commission_id){
            $order_id = get_post_meta($commission_id, '_commission_order_id', true);
            $order = wc_get_order($order_id);
            $commission_refunded = get_post_meta( $commission_id, '_commission_refunded_items', true );
            return $context == 'view' ? wc_price($commission_refunded, array('currency' => $order->get_currency())) : $commission_refunded;
        }
    }
    
    /**
     * Calculate commission shipping total including refunds
     * @param int $commission_id
     * @param string $context
     * @return value 
     */
    public static function commission_shipping_totals( $commission_id, $context = 'view' ) {
        if($commission_id){
            $order_id = get_post_meta($commission_id, '_commission_order_id', true);
            $order = wc_get_order($order_id);
            $shipping_amount = get_post_meta( $commission_id, '_shipping', true );
            $commission_refunded_shipping = get_post_meta( $commission_id, '_commission_refunded_shipping', true );
            $total = floatval($shipping_amount) + floatval($commission_refunded_shipping);
            return $context == 'view' ? wc_price($total, array('currency' => $order->get_currency())) : $total;
        }
    }
    
    /**
     * Calculate commission tax total including refunds
     * @param int $commission_id
     * @param string $context
     * @return value 
     */
    public static function commission_tax_totals( $commission_id, $context = 'view' ) {
        if($commission_id){
            $order_id = get_post_meta($commission_id, '_commission_order_id', true);
            $order = wc_get_order($order_id);
            $tax_amount = get_post_meta( $commission_id, '_tax', true );
            $commission_refunded_tax = get_post_meta( $commission_id, '_commission_refunded_tax', true );
            $total = floatval($tax_amount) + floatval($commission_refunded_tax);
            $currency = $order ? $order->get_currency() : get_woocommerce_currency();
            return $context == 'view' ? wc_price($total, array('currency' => $currency)) : $total;
        }
    }
    
    /**
     * Get commission totals array
     * @param array $args 
     * @param boolean $check_caps
     * @return array 
     */
    public static function get_commissions_total_data( $args = array(), $vendor_id = 0, $check_caps = true ) {
        global $MVX;
        $default_args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'posts_per_page' => -1,
            'fields' => 'ids',
	);
        
        $args = wp_parse_args( $args, $default_args );
        
        if( isset( $args['meta_query'] ) ) {
            $args['meta_query'][] = array(
                'key' => '_paid_status',
                'value' => array('unpaid', 'partial_refunded'),
                'compare' => 'IN'
            );
        } else {
            $args['meta_query'] = array(
                array(
                    'key' => '_paid_status',
                    'value' => array('unpaid', 'partial_refunded'),
                    'compare' => 'IN'
                ),
            );
        }
   
        $commissions = new WP_Query( $args );
        if( $commissions->get_posts() ) :
            $commission_amount = $shipping_amount = $tax_amount = $total = 0;
            $commission_posts = apply_filters( 'mvx_before_get_commissions_total_data_commission_posts', $commissions->get_posts(), $vendor_id, $args );
            foreach ( $commission_posts as $commission_id ) {
                $commission_amount += self::commission_amount_totals( $commission_id, 'edit' );
                $shipping_amount += self::commission_shipping_totals( $commission_id, 'edit' );
                $tax_amount += self::commission_tax_totals( $commission_id, 'edit' );
            }
            if( $check_caps && $vendor_id ){
                $amount = array(
                    'commission_amount' => $commission_amount,
                );
                if ($MVX->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($vendor_id, '_vendor_give_shipping', true)) {
                    $amount['shipping_amount'] = $shipping_amount;
                } else {
                    $amount['shipping_amount'] = 0;
                }
                if ($MVX->vendor_caps->vendor_payment_settings('give_tax') && !get_user_meta($vendor_id, '_vendor_give_tax', true)) {
                    $amount['tax_amount'] = $tax_amount;
                } else {
                    $amount['tax_amount'] = 0;
                }
                $amount['total'] = $amount['commission_amount'] + $amount['shipping_amount'] + $amount['tax_amount'];
                return $amount;
            }else{
                return array(
                    'commission_amount' => $commission_amount,
                    'shipping_amount' => $shipping_amount,
                    'tax_amount' => $tax_amount,
                    'total' => $commission_amount + $shipping_amount + $tax_amount
                );
            }
        endif;
    }

    public static function add_commission_note($commission_id, $note, $vendor_id = 0) {

        if (!$commission_id) {
            return 0;
        }

        $comment_author = __('MVX', 'multivendorx');
        $comment_author_email = strtolower(__('MVX', 'multivendorx')) . '@';
        $comment_author_email .= isset($_SERVER['HTTP_HOST']) ? str_replace('www.', '', $_SERVER['HTTP_HOST']) : 'noreply.com';
        $comment_author_email = sanitize_email($comment_author_email);

        $commentdata = apply_filters('mvx_new_commission_notes', array(
            'comment_post_ID' => $commission_id,
            'comment_author' => $comment_author,
            'comment_author_email' => $comment_author_email,
            'comment_author_url' => '',
            'comment_content' => $note,
            'comment_agent' => 'MVX',
            'comment_type' => 'commission_note',
            'comment_parent' => 0,
            'comment_approved' => 1,
                ), $commission_id, $vendor_id);
        $comment_id = wp_insert_comment($commentdata);
        if ($vendor_id) {
            add_comment_meta($comment_id, '_vendor_id', $vendor_id);

            do_action('mvx_new_commission_note', $comment_id, $commission_id, $vendor_id);
        }
        return $comment_id;
    }

    public function get_commission_notes($commission_id) {
        global $MVX;
        $args = array(
            'post_id' => $commission_id,
            'type' => 'commission_note',
            'status' => 'approve',
            'orderby' => 'comment_ID'
        );

        remove_filter('comments_clauses', array($MVX, 'exclude_order_comments'), 10, 1);
        $notes = get_comments($args);
        add_filter('comments_clauses', array($MVX, 'exclude_order_comments'), 10, 1);
        return $notes;
    }

    /**
     * Pay commisssion by admin
     * @param array $post_ids
     */
    public function mvx_mark_commission_paid($post_ids) {
        global $MVX;
        $commission_to_pay = array();
        foreach ($post_ids as $post_id) {
            $commission = $this->get_commission($post_id);
            $vendor = $commission->vendor;
            $commission_status = get_post_meta($post_id, '_paid_status', true);
            if (in_array($commission_status, array( 'unpaid', 'partial_refunded' ))) {
                $commission_to_pay[$vendor->term_id][] = $post_id;
            }
        }
        if ($commission_to_pay) {
            foreach ($commission_to_pay as $vendor_term_id => $commissions) {
                $vendor = get_mvx_vendor_by_term($vendor_term_id);
                $payment_method = get_user_meta($vendor->id, '_vendor_payment_mode', true);
                if ($payment_method) {
                    if (array_key_exists($payment_method, $MVX->payment_gateway->payment_gateways)) {
                        $MVX->payment_gateway->payment_gateways[$payment_method]->process_payment($vendor, $commissions, 'admin');
                    }
                }
            }
        }
    }

    /**
     * Get commission details
     * @param  int $commission_id Commission ID
     * @return obj                Commission object
     */
    function get_commission($commission_id = 0) {
        $commission = false;

        if ($commission_id > 0) {
            // Get post data
            $commission = get_post($commission_id);
            $commission_order_id = get_post_meta($commission_id, '_commission_order_id', true);
            $created_via_mvx_order = get_post_meta($commission_order_id, '_created_via', true);
            $vendor_id = get_post_meta($commission_order_id, '_vendor_id', true);
            if($created_via_mvx_order == 'mvx_vendor_order'){
                $order = wc_get_order($commission_order_id);
                $line_items = $order->get_items( 'line_item' );
                $products = array();
                foreach ($line_items as $item_id => $item) {
                    $products[] = $item->get_product_id();
                }
                $vendor = get_mvx_vendor($vendor_id);
                // Get meta data
                $commission->product = $products;
                $commission->vendor = $vendor;
            }else{
                // Get meta data
                $commission->product = get_post_meta($commission_id, '_commission_product', true);
                $commission->vendor = get_mvx_vendor_by_term(get_post_meta($commission_id, '_commission_vendor', true));
            }
            
            $commission->amount = apply_filters('mvx_post_commission_amount', get_post_meta($commission_id, '_commission_amount', true), $commission_id);
            $commission->paid_status = get_post_meta($commission_id, '_paid_status', true);
        }

        return $commission;
    }

    /**
     * Get Unpaid commission totals data
     * @param string $type
     * @return array 
     */
    public static function get_unpaid_commissions_total_data( $type = 'withdrawable' ) {
        global $MVX;
        $vendor = get_mvx_vendor( get_current_user_id() );
        if( !$vendor ) return false;
        $vendor_id = $vendor->id;
        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_commission_vendor',
                    'value' => absint( $vendor->term_id ),
                    'compare' => '='
                ),
                array(
                    'key' => '_paid_status',
                    'value' => array('unpaid', 'partial_refunded'),
                    'compare' => 'IN'
                ),
            ),
	);
   
        $commissions = new WP_Query( apply_filters( 'mvx_get_unpaid_commissions_total_data_query_args', $args, $type, $vendor ) );
        if( $commissions->get_posts() ) :
            $commission_amount = $shipping_amount = $tax_amount = $total = 0;
            $commission_posts = apply_filters( 'mvx_get_unpaid_commissions_total_data_query_posts', $commissions->get_posts(), $vendor );
            foreach ( $commission_posts as $commission_id ) {
                if( $type == 'withdrawable' ){
                    $order_id = mvx_get_commission_order_id( $commission_id );
                    $order = wc_get_order( $order_id );
                    if( $order ) {
                        if ( is_commission_requested_for_withdrawals( $commission_id ) || in_array( $order->get_status('edit'), array( 'on-hold', 'pending', 'failed', 'refunded', 'cancelled', 'draft' ) ) ) {
                            continue; // calculate only available withdrawable balance
                        }
                    }
                }
                $commission_amount += self::commission_amount_totals( $commission_id, 'edit' );
                $shipping_amount += self::commission_shipping_totals( $commission_id, 'edit' );
                $tax_amount += self::commission_tax_totals( $commission_id, 'edit' );
            }
            $check_caps = apply_filters( 'mvx_get_unpaid_commissions_total_data_vendor_check_caps', true, $vendor );
                    
            if( $check_caps && $vendor_id ){
                $amount = array(
                    'commission_amount' => $commission_amount,
                );
                if ($MVX->vendor_caps->vendor_payment_settings('give_shipping') && !get_user_meta($vendor_id, '_vendor_give_shipping', true)) {
                    $amount['shipping_amount'] = $shipping_amount;
                } else {
                    $amount['shipping_amount'] = 0;
                }
                if ($MVX->vendor_caps->vendor_payment_settings('give_tax') && !get_user_meta($vendor_id, '_vendor_give_tax', true)) {
                    $amount['tax_amount'] = $tax_amount;
                } else {
                    $amount['tax_amount'] = 0;
                }
                $amount['total'] = $amount['commission_amount'] + $amount['shipping_amount'] + $amount['tax_amount'];
                return $amount;
            }else{
                return array(
                    'commission_amount' => $commission_amount,
                    'shipping_amount' => $shipping_amount,
                    'tax_amount' => $tax_amount,
                    'total' => $commission_amount + $shipping_amount + $tax_amount
                );
            }
        endif;
    }

}
