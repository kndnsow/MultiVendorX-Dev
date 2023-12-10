<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class       MVX_Capabilities
 * @version     1.0.0
 * @package MultiVendorX
 * @author 		MultiVendorX
 */
class MVX_Capabilities {

    public $capability;
    public $payment_cap = array();
    public $mvx_capability = array();

    public function __construct() {
        $this->mvx_capability = array_merge(
                $this->mvx_capability
                , (array) mvx_get_option('mvx_settings_general_tab_settings', array())
                , (array) mvx_get_option('mvx_products_capability_tab_settings', array())
        );
        $this->payment_cap = array_merge(
                $this->payment_cap
                , (array) mvx_get_option('mvx_commissions_tab_settings', array())
                , (array) mvx_get_option('mvx_disbursement_tab_settings', array())
        );

        add_filter('product_type_selector', array(&$this, 'mvx_product_type_selector'), 10, 1);
        add_filter('product_type_options', array(&$this, 'mvx_product_type_options'), 10);
        add_filter('wc_product_sku_enabled', array(&$this, 'mvx_wc_product_sku_enabled'), 30);

        add_action('woocommerce_get_item_data', array(&$this, 'add_sold_by_text_cart'), 30, 2);
        add_action('woocommerce_after_shop_loop_item', array($this, 'mvx_after_add_to_cart_form'), 6);
        /* for single product */
        add_action('woocommerce_product_meta_start', array($this, 'mvx_after_add_to_cart_form'), 25);
        if (defined('MVX_FORCE_VENDOR_CAPS') && MVX_FORCE_VENDOR_CAPS) $this->update_mvx_vendor_role_capability();
    }

    /**
     * Vendor Capability from Product Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_can($cap) {
        if (is_array($this->mvx_capability) && !empty($cap)) {
            if (get_mvx_global_settings($cap)) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_capabilities_settings($cap, $default = array()) {
        if (empty($cap)) {
            return $default;
        }
        if (is_array($this->mvx_capability) && !empty($this->mvx_capability)) {
           if (!isset($this->mvx_capability[$cap]) || empty($this->mvx_capability[$cap])) {
                return $default;
            }
            return $this->mvx_capability[$cap]; 
        }
    }

    /**
     * Vendor Capability from Capability Settings 
     *
     * @param capability
     * @return boolean 
     */
    public function vendor_payment_settings($cap) {
        if (empty($cap)) {
            return false;
        }
        if (is_array($this->payment_cap)) {
            if (!isset($this->payment_cap[$cap]) || empty($this->payment_cap[$cap])) {
                return false;
            }
        }
        return $this->payment_cap[$cap];
    }

    /**
     * Get Vendor Product Types
     *
     * @param product_types
     * @return product_types 
     */
    public function mvx_product_type_selector($product_types) {
        $user = wp_get_current_user();
        if( !is_user_mvx_vendor($user) ) return $product_types;
        if ($product_types) {
            foreach ($product_types as $product_type => $value) {
                $vendor_can = mvx_is_product_type_avaliable($product_type);
                if (!$vendor_can) {
                    unset($product_types[$product_type]);
                }
            }
        }
        return apply_filters('mvx_product_type_selector', $product_types);
    }

    /**
     * Get Vendor Product Types Options
     *
     * @param product_type_options
     * @return product_type_options 
     */
    public function mvx_product_type_options($product_type_options) {
        $user = wp_get_current_user();
        if (is_user_mvx_vendor($user) && $product_type_options) {
            foreach ($product_type_options as $product_type_option => $value) {
                $vendor_can = mvx_is_product_type_avaliable($product_type_option);
                if (!$vendor_can) {
                    unset($product_type_options[$product_type_option]);
                }
            }
        }
        return $product_type_options;
    }

    /**
     * Check if Vendor Product SKU Enable
     *
     * @param state
     * @return boolean 
     */
    public function mvx_wc_product_sku_enabled($state) {
        $user = wp_get_current_user();
        if ( is_user_mvx_vendor($user) ) {
            return apply_filters( 'mvx_vendor_product_sku_enabled', true , $user->ID );
        }
        return $state;
    }

    /**
     * Add Sold by Vendor text
     *
     * @param array, cart_item
     * @return array 
     */
    public function add_sold_by_text_cart($array, $cart_item) {
        if (get_mvx_vendor_settings('display_product_seller', 'settings_general') && apply_filters('mvx_sold_by_text_in_cart_checkout', true, $cart_item['product_id'])) {
            $sold_by_text = apply_filters('mvx_sold_by_text', __('Sold By', 'multivendorx'), $cart_item['product_id']);
            $vendor = get_mvx_product_vendors($cart_item['product_id']);
            if ($vendor) {
                $array = array_merge($array, array(array('name' => $sold_by_text, 'value' => $vendor->page_title)));
                do_action('after_sold_by_text_cart_page', $vendor);
            }
        }
        return $array;
    }

    /**
     * Add Sold by Vendor text
     *
     * @return void 
     */
    public function mvx_after_add_to_cart_form() {
        global $post;
        if (get_mvx_vendor_settings('display_product_seller', 'settings_general') && apply_filters('mvx_sold_by_text_after_products_shop_page', true, $post->ID)) {
            $vendor = get_mvx_product_vendors($post->ID);
            if ($vendor) {
                $sold_by_text = apply_filters('mvx_sold_by_text', __('Sold By', 'multivendorx'), $post->ID);
                echo '<a class="by-vendor-name-link" style="display: block;" href="' . $vendor->permalink . '">' . $sold_by_text . ' ' . $vendor->page_title . '</a>';
                do_action('after_sold_by_text_shop_page', $vendor);
            }
        }
    }

    public function update_mvx_vendor_role_capability() {
        global $wp_roles;
        
        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        $capabilities = $this->get_mvx_vendor_caps();
        foreach ($capabilities as $cap => $is_enable) {
            $wp_roles->add_cap('dc_vendor', $cap, $is_enable);
        }
        do_action('mvx_after_update_vendor_role_capability', $capabilities, $wp_roles);
    }

    /**
     * Set up array of vendor admin capabilities
     * 
     * @since 2.7.6
     * @access public
     * @return arr Vendor capabilities
     */
    public function get_vendor_caps() {
        $caps = array();
        $capability = mvx_get_option('mvx_products_capability_tab_settings', array());
        if ($this->vendor_capabilities_settings('is_upload_files', $capability)) {
            $caps['upload_files'] = true;
        } else {
            $caps['upload_files'] = false;
        }
        if ($this->vendor_capabilities_settings('is_submit_product', $capability)) {
            $caps['delete_product'] = true;
            $caps['delete_products'] = true;
            $caps['edit_products'] = true;
            if(!apply_filters('mvx_is_vendor_edit_non_published_product', false)){
                $caps['edit_product'] = false;
            }else{
                $caps['edit_product'] = true;
            }
            if ($this->vendor_capabilities_settings('is_published_product', $capability)) {
                $caps['publish_products'] = true;
            } else {
                $caps['publish_products'] = false;
            }
            if ($this->vendor_capabilities_settings('is_edit_delete_published_product', $capability)) {
                $caps['edit_published_products'] = true;
                $caps['edit_product'] = true;
                $caps['delete_published_products'] = true;
            } else {
                $caps['edit_published_products'] = false;
                $caps['delete_published_products'] = false;
            }
        } else {
            $caps['edit_product'] = false;
            $caps['delete_product'] = false;
            $caps['edit_products'] = false;
            $caps['delete_products'] = false;
            $caps['publish_products'] = false;
            $caps['edit_published_products'] = false;
            $caps['delete_published_products'] = false;
        }

        if ($this->vendor_capabilities_settings('is_submit_coupon', $capability)) {
            $caps['edit_shop_coupon'] = true;
            $caps['edit_shop_coupons'] = true;
            $caps['delete_shop_coupon'] = true;
            $caps['delete_shop_coupons'] = true;
            if ($this->vendor_capabilities_settings('is_published_coupon', $capability)) {
                $caps['publish_shop_coupons'] = true;
            } else {
                $caps['publish_shop_coupons'] = false;
            }
            if ($this->vendor_capabilities_settings('is_edit_delete_published_coupon', $capability)) {
                $caps['edit_published_shop_coupons'] = true;
                $caps['delete_published_shop_coupons'] = true;
            } else {
                $caps['edit_published_shop_coupons'] = false;
                $caps['delete_published_shop_coupons'] = false;
            }
        } else {
            $caps['edit_shop_coupon'] = false;
            $caps['edit_shop_coupons'] = false;
            $caps['delete_shop_coupon'] = false;
            $caps['delete_shop_coupons'] = false;
            $caps['publish_shop_coupons'] = false;
            $caps['edit_published_shop_coupons'] = false;
            $caps['delete_published_shop_coupons'] = false;
        }
        $caps['edit_shop_orders'] = true;
        return apply_filters('mvx_vendor_capabilities', $caps);
    }

    public function get_mvx_vendor_caps() {
        $caps = array();
        if (get_mvx_global_settings('is_upload_files')) {
            $caps['upload_files'] = true;
        } else {
            $caps['upload_files'] = false;
        }
        if (get_mvx_global_settings('is_submit_product')) {
            $caps['delete_product'] = true;
            $caps['delete_products'] = true;
            $caps['edit_products'] = true;
            if(!apply_filters('mvx_is_vendor_edit_non_published_product', false)){
                $caps['edit_product'] = false;
            }else{
                $caps['edit_product'] = true;
            }
            if (get_mvx_global_settings('is_published_product')) {
                $caps['publish_products'] = true;
            } else {
                $caps['publish_products'] = false;
            }
            if (get_mvx_global_settings('is_edit_delete_published_product')) {
                $caps['edit_published_products'] = true;
                $caps['edit_product'] = true;
                $caps['delete_published_products'] = true;
            } else {
                $caps['edit_published_products'] = false;
                $caps['delete_published_products'] = false;
            }
        } else {
            $caps['edit_product'] = false;
            $caps['delete_product'] = false;
            $caps['edit_products'] = false;
            $caps['delete_products'] = false;
            $caps['publish_products'] = false;
            $caps['edit_published_products'] = false;
            $caps['delete_published_products'] = false;
        }

        if (get_mvx_global_settings('is_submit_coupon')) {
            $caps['edit_shop_coupon'] = true;
            $caps['edit_shop_coupons'] = true;
            $caps['delete_shop_coupon'] = true;
            $caps['delete_shop_coupons'] = true;
            if (get_mvx_global_settings('is_published_coupon')) {
                $caps['publish_shop_coupons'] = true;
            } else {
                $caps['publish_shop_coupons'] = false;
            }
            if (get_mvx_global_settings('is_edit_delete_published_coupon')) {
                $caps['edit_published_shop_coupons'] = true;
                $caps['delete_published_shop_coupons'] = true;
            } else {
                $caps['edit_published_shop_coupons'] = false;
                $caps['delete_published_shop_coupons'] = false;
            }
        } else {
            $caps['edit_shop_coupon'] = false;
            $caps['edit_shop_coupons'] = false;
            $caps['delete_shop_coupon'] = false;
            $caps['delete_shop_coupons'] = false;
            $caps['publish_shop_coupons'] = false;
            $caps['edit_published_shop_coupons'] = false;
            $caps['delete_published_shop_coupons'] = false;
        }
        $caps['edit_shop_orders'] = true;
        return apply_filters('mvx_vendor_capabilities', $caps);
    }
}