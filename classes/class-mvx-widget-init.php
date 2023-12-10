<?php

/**
 * MVX Widget Init Class
 *
 * @version		2.2.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class MVX_Widget_Init {

    public function __construct() {
        add_action('init', array($this, 'mvx_register_store_sidebar'));
        add_action('widgets_init', array($this, 'product_vendor_register_widgets'));
        add_action('wp_dashboard_setup', array($this, 'mvx_rm_meta_boxes'));
    }

    /**
     * Register Store Sidebar
     */
    public function mvx_register_store_sidebar() {
        register_sidebar(
            apply_filters( 'mvx_store_sidebar_args', array(
                        'name'          => __( 'Vendor Store Sidebar', 'multivendorx' ),
                        'id'            => 'sidebar-mvx-store',
                        'before_widget' => '<aside id="%1$s" class="widget sidebar-box clr %2$s">',
                        'after_widget'  => '</aside>',
                        'before_title'  => '<div class="sidebar_heading"><h4 class="widget-title">',
                        'after_title'   => '</h4></div>',
                )
            )
        );
    }

    /**
     * Add vendor widgets
     */
    public function product_vendor_register_widgets() {
        include_once ('widgets/class-mvx-widget-vendor-info.php');
        require_once ('widgets/class-mvx-widget-vendor-list.php');
        require_once ('widgets/class-mvx-widget-vendor-quick-info.php');
        require_once ('widgets/class-mvx-widget-vendor-location.php');
        require_once ('widgets/class-mvx-widget-vendor-product-categories.php');
        require_once ('widgets/class-mvx-widget-vendor-top-rated-products.php');
        require_once ('widgets/class-mvx-widget-vendor-review.php');
        require_once ('widgets/class-mvx-widget-vendor-product-search.php');
        require_once ('widgets/class-mvx-widget-vendor-policies.php');
        require_once ('widgets/class-mvx-widget-vendor-coupons.php');
        require_once ('widgets/class-mvx-widget-vendor-on-sale-products.php');
        require_once ('widgets/class-mvx-widget-vendor-recent-products.php');

        register_widget('DC_Widget_Vendor_Info');
        register_widget('DC_Widget_Vendor_List');
        register_widget('DC_Widget_Quick_Info_Widget');
        register_widget('DC_Woocommerce_Store_Location_Widget');
        register_widget('MVX_Widget_Vendor_Product_Categories');
        register_widget('MVX_Widget_Vendor_Top_Rated_Products');
        register_widget('MVX_Widget_Vendor_Review_Widget');
        register_widget('MVX_Widget_Vendor_Product_Search');
        register_widget('MVX_Widget_Vendor_Policies');
        register_widget('MVX_Widget_Vendor_Coupons');
        register_widget('MVX_Widget_Vendor_On_Sale_Products');
        register_widget('MVX_Widget_Vendor_Recent_Products');
    }

    /**
     * Removing woocommerce widget from vendor dashboard
     */
    public function mvx_rm_meta_boxes() {
        if (is_user_mvx_vendor(get_current_vendor_id())) {
            remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal');
        }
    }

}
