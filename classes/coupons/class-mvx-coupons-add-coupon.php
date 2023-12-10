<?php
/**
 * MVX Add coupon setup
 *
 * @package MultiVendorX/classes/coupons
 * @since    3.3.0
 */
defined( 'ABSPATH' ) || exit;

class MVX_Coupons_Add_Coupon {

    protected $coupon_id = '';
    protected $coupon_object = null;
    protected $post_object = null;
    private $edit = false;
    private $no_cap = false;
    private $error_msg = '';

    public function __construct() { 
        global $wp;

        $this->coupon_id = absint( $wp->query_vars[get_mvx_vendor_settings( 'mvx_add_coupon_endpoint', 'seller_dashbaord', 'add-coupon' )] );
        $this->coupon_object = new WC_Coupon();
        if ( $this->coupon_id && $this->coupon_capablity_check( 'edit', $this->coupon_id ) ) {
            $this->post_object = get_post( $this->coupon_id );
            $this->coupon_object = new WC_Coupon( $this->coupon_id );
            $this->edit = true;
        } elseif ( ! $this->coupon_id && $this->coupon_capablity_check( 'add' ) ) {
            $this->post_object = $this->create_coupon_draft( 'shop_coupon' );
            $this->coupon_id = $this->post_object ? $this->post_object->ID : '';
        } else {
            $this->no_cap = true;
        }

        if ( ! $this->no_cap ) {
            do_action( 'mvx_frontend_after_add_coupon_endpoint_load', $this->coupon_id, $this->coupon_object, $this->post_object );
        }
        
    }

    private function coupon_capablity_check( $action = 'add', $coupon_id = '' ) {
        $current_vendor_id = get_current_user_id();
        $current_user_ids = apply_filters( 'mvx_current_coupon_edit' , array( get_current_user_id() ) ,$coupon_id );
        if ( ! $current_vendor_id ) {
            $this->error_msg = __( 'You do not have permission to view this content. Please contact site administrator.', 'multivendorx' );
            return false;
        }
        if ( $coupon_id && !in_array( absint( get_post_field( 'post_author', $coupon_id ) ), $current_user_ids ) ) {
            $this->error_msg = __( 'You do not have permission to view this content. Please contact site administrator.', 'multivendorx' );
            return false;
        }
        switch ( $action ) {
            case 'add':
                if ( ! ( current_vendor_can( 'edit_shop_coupon' ) ) ) {
                    $this->error_msg = __( 'You do not have enough permission to submit a new coupon. Please contact site administrator.', 'multivendorx' );
                    return false;
                }
                return true;
            case 'edit':
                if ( ! ( current_vendor_can( 'edit_shop_coupon' ) && current_vendor_can( 'edit_published_shop_coupons' ) && $coupon_id && apply_filters( 'mvx_vendor_capability_to_edit_coupons' , is_current_vendor_coupon( $coupon_id ) ) ) ) {
                    $this->error_msg = __( 'You do not have permission to view this content. Please contact site administrator.', 'multivendorx' );
                    return false;
                }
                return true;
        }
        return false;
    }

    private function coupon_no_caps_notice() {
        ob_start();
        ?>
        <div class="col-md-12">
            <div class="panel panel-default">
                <?php echo $this->error_msg; ?>
            </div>
        </div><?php
        return;
    }

    private function create_coupon_draft( $post_type ) {
        $current_vendor_id = get_current_user_id();
        $vendor = get_mvx_vendor( $current_vendor_id );
        if ( $vendor ) {
            $post_id = wp_insert_post( array( 'post_title' => __( 'Auto Draft', 'multivendorx' ), 'post_type' => $post_type, 'post_status' => 'auto-draft' ) );
            return get_post( $post_id );
        }
        return false;
    }

    /**
     * 
     * @return integer coupon id
     */
    public function get_the_id() {
        return $this->coupon_id;
    }

    /**
     * Return array of tabs to show.
     * @return array
     */
    public function get_coupon_data_tabs() {
        $tabs = apply_filters( 'mvx_frontend_dashboard_coupon_data_tabs', array(
            'general'           => array(
                'label'    => __( 'General', 'multivendorx' ),
                'target'   => 'general_coupon_data',
                'class'    => array(),
                'priority' => 10,
            ),
            'usage_restriction' => array(
                'label'    => __( 'Usage restriction', 'multivendorx' ),
                'target'   => 'usage_restriction_coupon_data',
                'class'    => array(),
                'priority' => 20,
            ),
            'usage_limit'       => array(
                'label'    => __( 'Usage limits', 'multivendorx' ),
                'target'   => 'usage_limit_coupon_data',
                'class'    => array(),
                'priority' => 30,
            ),
            ) );

        // Sort tabs based on priority.
        uasort( $tabs, array( __CLASS__, 'coupon_data_tabs_sort' ) );
        return $tabs;
    }

    /**
     * Callback to sort coupon data tabs on priority.
     *
     * @since 3.1.0
     * @param int $a First item.
     * @param int $b Second item.
     *
     * @return bool
     */
    private static function coupon_data_tabs_sort( $a, $b ) {
        if ( ! isset( $a['priority'], $b['priority'] ) ) {
            return -1;
        }

        if ( $a['priority'] == $b['priority'] ) {
            return 0;
        }

        return $a['priority'] < $b['priority'] ? -1 : 1;
    }

    public function output() {
        global $MVX;
        
        if ( ! $this->no_cap ) {
            $add_coupon_params = apply_filters( 'mvx_frontend_dashboard_add_coupon_params', array(
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'coupon_id'             => $this->coupon_id,
                'search_products_nonce' => wp_create_nonce( 'search-products' ),
                ) );
            wp_localize_script( 'mvx-advance-coupon', 'add_coupon_params', $add_coupon_params );
            wp_enqueue_script( 'mvx-advance-coupon' );
            do_action( 'mvx_frontend_dashboard_add_coupon_template_load', $this->coupon_id, $this->coupon_object, $this->post_object );
            $MVX->template->get_template( 'vendor-dashboard/coupon-manager/add-coupons.php', array( 'self' => $this, 'coupon' => $this->coupon_object, 'post' => $this->post_object, 'edit_coupon' => $this->edit ) );
        } else {
            $this->coupon_no_caps_notice();
        }
    }

}
