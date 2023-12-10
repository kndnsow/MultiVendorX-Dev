<?php

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$minimum_wp_version = '6.0';

if ( version_compare( $GLOBALS['wp_version'], $minimum_wp_version, '<' ) ) {
    /**
     * Outputs for an admin notice about running MVX Blocks on outdated WordPress.
     *
     * @since 1.0.0
     */
    function mvx_blocks_admin_unsupported_wp_notice() {
                ?>
                <div class="notice notice-error is-dismissible">
                        <p><?php esc_html_e( 'MVX Blocks requires a more recent version of WordPress and has been paused. Please update WordPress to continue enjoying WooCommerce Blocks.', 'multivendorx' ); ?></p>
                </div>
                <?php
        
    }
    add_action( 'admin_notices', 'mvx_blocks_admin_unsupported_wp_notice' );
    return;
}

/**
 * Load MVX Blocks package class.
 *
 */
class MVX_Block {
        /**
     * The single instance of the class.
     *
     * @var object
     */
    protected static $instance = null;
        
        /**
     * Constructor
     *
     * @return void
     */
    protected function __construct() {}

    /**
     * Get class instance.
     *
     * @return object Instance.
     */
    final public static function instance() {
        if ( null === static::$instance ) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Init the plugin.
     */
    public function init() {
        if ( ! $this->has_dependencies() ) {
            return;
        }
        $this->define_constants();
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
    }
        
    /**
     * Check dependencies exist.
     *
     * @return boolean
     */
    protected function has_dependencies() {
        return class_exists( 'MVX' ) && function_exists( 'register_block_type' );
    }
        
    /**
     * Setup plugin once all other plugins are loaded.
     *
     * @return void
     */
    public function on_plugins_loaded() {
        if (mvx_is_module_active('mvx-blocks')) {
            $this->includes();
            
            add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'register_blocks_assets' ) );
            add_action( 'enqueue_block_assets', array( __CLASS__, 'enqueue_frontend_assets' ) );
            add_filter( 'block_categories_all', array( __CLASS__, 'register_block_categories_all' ) );
            add_action( 'init', array( __CLASS__, 'register_blocks' ) );
        }
    }
        
    /**
     * Register assets block categories.
     */
    public static function register_block_categories_all( $categories ) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'mvx',
                    'title' => __( 'MultiVendorX', 'multivendorx' ),
                    'icon'  => 'wordpress',
                ),
            )
        );
    }
        
    /**
     * Register assets as needed.
     */
    public static function register_blocks_assets() {
        // Register block styles for both frontend + backend.
        wp_register_style(
                'mvx_blocks-style-css', 
                MVXB_PLUGIN_URL . 'build/style-index.css', 
                is_admin() ? array( 'wp-editor' ) : null, 
                filemtime( MVXB_PLUGIN_PATH . '/build/style-index.css' ) 
        );
        
        // Register block editor script for backend.
        wp_register_script(
                'mvx_blocks-scripts-js', 
                MVXB_PLUGIN_URL . 'build/index.js',
                array( 'wp-blocks', 'wp-i18n', 'wp-element'/*, 'wp-editor'*/, 'wp-components' ),
                filemtime( MVXB_PLUGIN_PATH . '/build/index.js' ), 
                true 
        );

        // Register block editor styles for backend.
        wp_register_style(
                'mvx_blocks-editor-css', 
                MVXB_PLUGIN_URL . 'build/index.css', 
                array( 'wp-edit-blocks' ), 
                filemtime( MVXB_PLUGIN_PATH . '/build/index.css' ) 
        );

        // WP Localized globals
        $params = apply_filters( 'mvx_blocks_scripts_data_params',
        array(
            'pluginDirPath' => MVXB_PLUGIN_PATH,
            'pluginDirUrl'  => MVXB_PLUGIN_URL,
            'allVendors'    => mvx_vendor_list_item(),
            'recapta'       =>  array(
                array(
                    'key'   =>  'v2',
                    'title' =>  __( 'reCAPTCHA v2', 'multivendorx' ),
                ),
                array(
                    'key'   =>  'v3',
                    'title' =>  __( 'reCAPTCHA v3', 'multivendorx' ),
                ),
            )
        ) );
        wp_localize_script( 'mvx_blocks-scripts-js', 'mvx_blocks_scripts_data_params', $params );
    }

    /**
     * Enqueue assets for frontend.
     */
    public static function enqueue_frontend_assets() {
        if ( !wp_script_is( 'mvx_blocks-style-css', 'registered' ) ) {
            wp_register_style(
                'mvx_blocks-style-css', 
                MVXB_PLUGIN_URL . 'build/style-index.css', 
                is_admin() ? array( 'wp-editor' ) : null,
                filemtime( MVXB_PLUGIN_PATH . '/build/style-index.css' ) 
            );
        }
        wp_enqueue_style( 'mvx_blocks-style-css' );
    }
        
    /**
     * Register blocks, hooking up assets and render functions as needed.
     */
    public static function register_blocks() {
        $blocks = [
            'TopRatedVendors',
            'VendorTopProducts',
            'VendorsInfo',
            'VendorCoupons',
            'VendorLocation',
            'VendorOnSellProducts',
            'VendorPolicies',
            'VendorsReview',
            'VendorsContact',
            'VendorRecentProducts',
            'VendorProductsSearch',
            'VendorProductCategories',
            'VendorLists'
        ];
                
        foreach ( $blocks as $class ) {
            require_once 'src/blocks/' . $class . '/' . $class . '.php';
            $instance = new $class();
            $instance->register_block_type();
        }
    }

    /**
     * Define Constants.
     */
    protected function define_constants() {
        if ( ! defined( 'MVXB_ABSPATH' ) ) define( 'MVXB_ABSPATH', dirname( __FILE__ ) . '/' );
        if ( ! defined( 'MVXB_PLUGIN_URL' ) ) define( 'MVXB_PLUGIN_URL', plugin_dir_url(__FILE__) );
        if ( ! defined( 'MVXB_PLUGIN_PATH' ) ) define( 'MVXB_PLUGIN_PATH', untrailingslashit( plugin_dir_path(__FILE__) ) );
        if ( ! defined( 'MVXB_VERSION' ) ) define( 'MVXB_VERSION', '0.0.1' );
    }
        
        /**
     * includes files.
     */
    protected function includes() {
        include_once MVXB_ABSPATH . 'inc/functions.php';
        include_once MVXB_ABSPATH . 'src/blocks/AbstractBlock.php';
    }
}
MVX_Block::instance()->init();
   