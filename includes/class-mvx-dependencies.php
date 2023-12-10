<?php

/**
 * WC Dependency Checker
 *
 */
class WC_Dependencies_Product_Vendor {

    private static $active_plugins;

    public static function init() {
        self::$active_plugins = (array) get_option('active_plugins', array());
        if (is_multisite())
            self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
    }

    /**
     * Check woocommerce exist
     * @return Boolean
     */
    public static function woocommerce_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce/woocommerce.php', self::$active_plugins) || array_key_exists('woocommerce/woocommerce.php', self::$active_plugins);
    }

    /**
     * Check if woocommerce active
     * @return Boolean
     */
    public static function is_woocommerce_active() {
        return self::woocommerce_active_check();
    }
    
    /**
     * Check if Advance frontend manager
     * @return Boolean
     */
    public static function is_advance_frontend_manager_active() {
        if (!self::$active_plugins)
            self::init();
        return in_array('mvx-frontend_product_manager/mvx_frontend_product_manager.php', self::$active_plugins) || array_key_exists('mvx-frontend_product_manager/mvx_frontend_product_manager.php', self::$active_plugins);
    }

    /**
     * Check if Woocommerce Extra Checkout Fields For Brazil plugin active
     * @return Boolean
     */
    public static function woocommerce_extra_checkout_fields_for_brazil_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php', self::$active_plugins) || array_key_exists('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php', self::$active_plugins);
    }

    /**
     * Check if Woocommerce Product Enquiry Form active
     * @return Boolean
     */
    public static function woocommerce_product_enquiry_form_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce-product-enquiry-form/product-enquiry-form.php', self::$active_plugins) || array_key_exists('woocommerce-product-enquiry-form/product-enquiry-form.php', self::$active_plugins);
    }

    // Yoast SEO
    static function fpm_yoast_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('wordpress-seo/wp-seo.php', self::$active_plugins) || array_key_exists('wordpress-seo/wp-seo.php', self::$active_plugins);
        return false;
    }

    // WooCommerce Custom Product Tabs Lite
    static function fpm_wc_tabs_lite_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce-custom-product-tabs-lite/woocommerce-custom-product-tabs-lite.php', self::$active_plugins) || array_key_exists('woocommerce-custom-product-tabs-lite/woocommerce-custom-product-tabs-lite.php', self::$active_plugins);
        return false;
    }

    // WooCommerce Product FEES
    static function fpm_wc_product_fees_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce-product-fees/woocommerce-product-fees.php', self::$active_plugins) || array_key_exists('woocommerce-product-fees/woocommerce-product-fees.php', self::$active_plugins);
        return false;
    }

    // WooCommerce Bulk Discount
    static function fpm_wc_bulk_discount_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('woocommerce-bulk-discount/woocommerce-bulk-discount.php', self::$active_plugins) || array_key_exists('woocommerce-bulk-discount/woocommerce-bulk-discount.php', self::$active_plugins);
        return false;
    }

    // MapPress
    static function fpm_mappress_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('mappress-google-maps-for-wordpress/mappress.php', self::$active_plugins) || array_key_exists('mappress-google-maps-for-wordpress/mappress.php', self::$active_plugins);
        return false;
    }

    // Toolset Types
    static function fpm_toolset_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('types/wpcf.php', self::$active_plugins) || array_key_exists('types/wpcf.php', self::$active_plugins);
        return false;
    }

    // Advanced Custom Field
    static function fpm_acf_plugin_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('advanced-custom-fields/acf.php', self::$active_plugins) || array_key_exists('advanced-custom-fields/acf.php', self::$active_plugins);
        return false;
    }
    
    // Stripe dependency
    static function stripe_dependencies() {
        $dependencies = array('status' => true, 'module' => '');
        if ( version_compare( PHP_VERSION, '5.3.29', '<' )) {
            $dependencies['module'] = 'phpversion';
            $dependencies['status'] = false;
            return $dependencies;
        }
        $modules = array( 'curl', 'mbstring', 'json' );

        foreach($modules as $module){
            if(!extension_loaded($module)){
                $dependencies['module'] = $module;
                $dependencies['status'] = false;
                return $dependencies;
            }
        }
        return $dependencies;
    }

    /**
     * Check Elementor Pro exist
     * @return Boolean
     */
    public static function elementor_pro_active_check() {
        if (!self::$active_plugins)
            self::init();
        return in_array('elementor-pro/elementor-pro.php', self::$active_plugins) || array_key_exists('elementor-pro/elementor-pro.php', self::$active_plugins);
    }
}
