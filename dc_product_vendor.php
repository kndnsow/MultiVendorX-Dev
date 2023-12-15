<?php
/**
 * Plugin Name: MultiVendorX
 * Plugin URI: https://multivendorx.com/
 * Description: A Free Extension That Transforms Your WooCommerce Site into a Marketplace.
 * Author: MultiVendorX
 * Version: 4.1.0
 * Author URI: https://multivendorx.com/
 * Requires at least: 4.4
 * Tested up to: 6.4.2
 * WC requires at least: 3.0
 * WC tested up to: 8.4.0
 *
 * Text Domain: multivendorx
 * Domain Path: /languages/
 */
if (!class_exists('WC_Dependencies_Product_Vendor')) {
    require_once 'includes/class-mvx-dependencies.php';
}
require_once 'includes/mvx-core-functions.php';
require_once 'mvx-config.php';
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('MVX_PLUGIN_TOKEN')) {
    exit;
}

/* Check whether another multi vendor plugin exist */
register_activation_hook(__FILE__, 'mvx_check_if_another_vendor_plugin_exits');
/* Plugin activation hook */
register_activation_hook(__FILE__, 'activate_mvx_plugin');
/* Plugin deactivation hook */
register_deactivation_hook(__FILE__, 'deactivate_mvx_plugin');
/* Remove rewrite rules and then recreate rewrite rules. */
register_activation_hook(__FILE__, 'flush_rewrite_rules');

add_action('init', 'mvx_plugin_init');
add_action('admin_init', 'mvx_delete_woocomerce_transient_redirect_to_mvx_setup', 5);
/**
 * Load setup class 
 */
function mvx_plugin_init() {
    $current_page = filter_input(INPUT_GET, 'page');
    if ($current_page && $current_page == 'mvx-setup') {
        include_once(dirname( __FILE__ ) . '/admin/class-mvx-admin-setup-wizard.php');
    }
}
/**
 * Delete WooCommerce activation redirect transient
 */
function mvx_delete_woocomerce_transient_redirect_to_mvx_setup(){
    if ( get_transient( '_wc_activation_redirect' ) ) {
        delete_transient( '_wc_activation_redirect' );
        return;
    }
    if ( get_transient( '_mvx_activation_redirect' ) ) {
        delete_transient( '_mvx_activation_redirect' );
        if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'mvx-setup' ) ) ) || is_network_admin() || isset( $_GET['activate-multi'] ) || apply_filters( 'mvx_prevent_automatic_wizard_redirect', false ) ) {
                return;
        }
        wp_safe_redirect( admin_url( 'index.php?page=mvx-setup' ) );
	exit;
    }
}
$permalink_structure = get_option('permalink_structure');
if (!class_exists('MVX') && WC_Dependencies_Product_Vendor::is_woocommerce_active() && !empty($permalink_structure)) {
    global $MVX;
    require_once( 'classes/class-mvx.php' );
    /* recheck plugin install */
    add_action('plugins_loaded', 'activate_mvx_plugin');
    /* Initiate plugin main class */
    $MVX = new MVX(__FILE__);
    $GLOBALS['MVX'] = $MVX;
    if (is_admin() && !defined('DOING_AJAX')) {
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'mvx_action_links');
    }
} else {
    add_action('admin_notices', 'mvx_admin_notice');
    function mvx_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e('MultiVendorX plugin requires <a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> plugins to be active! and <a href='. admin_url('options-permalink.php') .'>permalink</a> structure should be configured', 'multivendorx'); ?></p>
        </div>
        <?php
    }
}

function mvx_namespace_approve( $value ) {
	
	$rest_prefix = trailingslashit( rest_get_url_prefix() );
	
	// Allow third party plugins use our authentication methods.
	$mvx_support = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix . 'mvx' ) );
	
	if($value || $mvx_support) $return = true;
	else $return = false;
	
	return $return;
}
add_action ( 'before_woocommerce_init', function () {  
    if ( class_exists ( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility ( 'custom_order_tables', WP_CONTENT_DIR.'/plugins/dc-woocommerce-multi-vendor/dc_product_vendor.php', true );  
    } 
});
