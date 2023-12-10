<?php

if (!defined('ABSPATH')) {
    exit;
}

class MVX_Widget_Vendor_Coupons extends WC_Widget {

    public $vendor_term_id;

    public function __construct() {
        $this->widget_cssclass = 'mvx_vendor_coupons';
        $this->widget_description = __('Displays coupons added by the vendor on the vendor shop page.', 'multivendorx');
        $this->widget_id = 'mvx_vendor_coupons';
        $this->widget_name = __('MVX: Vendor\'s Coupons', 'multivendorx');
        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => __('MVX Vendor Coupons', 'multivendorx'),
                'label' => __('Title', 'multivendorx'),
            ),
        );
        parent::__construct();
    }

    public function widget($args, $instance) {
        global $wp_query, $MVX;
        $store_id = mvx_find_shop_page_vendor();
        $vendor = get_mvx_vendor($store_id);
        if ((!mvx_is_store_page() && !$vendor) || (!$MVX->vendor_caps->vendor_capabilities_settings('is_submit_coupon'))) {
            return;
        }

        $coupon_args = apply_filters( 'mvx_get_vendor_coupon_widget_list_query_args', array(
                'posts_per_page' => -1,
                'offset' => 0,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_type' => 'shop_coupon',
                'author' => $vendor->id,
                'post_status' => array('publish', 'pending', 'draft', 'trash'),
                'suppress_filters' => true
            ), $vendor );
        $vendor_total_coupons = get_posts($coupon_args);

        if( empty( $vendor_total_coupons ) ) return;

        $this->widget_start($args, $instance); 

        do_action($this->widget_cssclass . '_top', $vendor);
	
		$content = '<div class="mvx_store_coupons">';
		
		foreach( $vendor_total_coupons as $vendor_coupon ) {
			$coupon = new WC_Coupon( $vendor_coupon->ID );
			
			if ( $coupon->get_date_expires() && ( current_time( 'timestamp', true ) > $coupon->get_date_expires()->getTimestamp() ) ) continue;
			

			$content .= '<span class="mvx-store-coupon-single tips text_tip" title="' . esc_html( wc_get_coupon_type( $coupon->get_discount_type() ) ) . ': ' . esc_html( wc_format_localized_price( $coupon->get_amount() ) ) . ($coupon->get_date_expires() ? ' ' . __( 'Expiry Date: ', 'multivendorx' ) . $coupon->get_date_expires()->date_i18n( 'F j, Y' ) : '' ) . ' ' . $vendor_coupon->post_excerpt . '">' . $vendor_coupon->post_title . '</span>';
		}
		
		$content .= '</div>';
		
		echo $content;

		do_action($this->widget_cssclass . '_bottom', $vendor);

    	$this->widget_end($args);
    }    

}