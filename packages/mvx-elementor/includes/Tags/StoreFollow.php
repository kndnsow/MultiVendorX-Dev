<?php

class StoreFollow extends MVX_Elementor_TagBase {

    /**
     * Class constructor
     *
     * @since 3.7
     *
     * @param array $data
     */
    public function __construct( $data = [] ) {
        parent::__construct( $data );
    }

    /**
     * Tag name
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_name() {
        return 'mvx-store-follow-tag';
    }

    /**
     * Tag title
     *
     * @since 3.7
     *
     * @return string
     */
    public function get_title() {
        return __( 'Store Follow Button', 'multivendorx' );
    }

    /**
     * Render tag
     *
     * @since 3.7
     *
     * @return void
     */
    public function render() {
    	global $MVX;
    	
        if ( mvx_is_store_page() ) {
            $vendor_id = mvx_find_shop_page_vendor();
            $mvx_customer_follow_vendor = get_user_meta( get_current_user_id(), 'mvx_customer_follow_vendor', true ) ? get_user_meta( get_current_user_id(), 'mvx_customer_follow_vendor', true ) : array();
            $vendor_lists = !empty($mvx_customer_follow_vendor) ? wp_list_pluck( $mvx_customer_follow_vendor, 'user_id' ) : array();
            $follow_status = in_array($vendor_id, $vendor_lists) ? __( 'Unfollow', 'multivendorx' ) : __( 'Follow', 'multivendorx' );
        	echo is_user_logged_in() ? esc_attr($follow_status) : esc_html_e('You must log in to follow', 'multivendorx');
        } else {
            echo esc_html_e( 'Follow', 'multivendorx' );
        }
    }
}
