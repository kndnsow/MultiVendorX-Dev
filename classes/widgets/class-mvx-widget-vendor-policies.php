<?php

if (!defined('ABSPATH')) {
    exit;
}

class MVX_Widget_Vendor_Policies extends WC_Widget {

    public $vendor_term_id;

    public function __construct() {
        $this->widget_cssclass = 'mvx_vendor_widget_policy';
        $this->widget_description = __('Displays vendor policies on the vendor shop page.', 'multivendorx');
        $this->widget_id = 'mvx_vendor_widget_policy';
        $this->widget_name = __('MVX: Vendor\'s Policies',     'multivendorx');
        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => __('Vendor Policies', 'multivendorx'),
                'label' => __('Title', 'multivendorx'),
            ),
            'shipping' => array(
                'type' => 'checkbox',
                'std' => 1,
                'label' => __('Shipping Policy', 'multivendorx'),
            ),
			'refund'       => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __('Refund Policy', 'multivendorx'),
			),
            'cancel'       => array(
                'type'  => 'checkbox',
                'std'   => 1,
                'label' => __('Cancellation/Return/Exchange Policy', 'multivendorx'),
            ),
        );
        parent::__construct();
    }

    public function widget($args, $instance) {
        global $MVX;
        $content = '';
        $store_id = mvx_find_shop_page_vendor();
        $vendor = get_mvx_vendor($store_id);
        if ((!mvx_is_store_page() && !$vendor)) {
            return;
        }

        $this->widget_start($args, $instance);

        do_action($this->widget_cssclass . '_top', $vendor);
        
        $shipping = isset($instance['shipping']) ? $instance['shipping'] : $this->settings['shipping']['std'];
        $refund = isset( $instance['refund'] ) ? $instance['refund'] : $this->settings['refund']['std'];
        $cancel = isset( $instance['cancel'] ) ? $instance['cancel'] : $this->settings['cancel']['std'];

        $policies = $this->get_mvx_vendor_policies($vendor);

        if(!empty($policies)) {

            $content .= '<div class="mvx-product-policies">';
            if(isset($policies['shipping_policy']) && !empty($policies['shipping_policy']) && $shipping) {
                $content .='<div class="mvx-shipping-policies policy">
                    <h2 class="mvx_policies_heading heading">'. esc_html_e('Shipping Policy', 'multivendorx').'</h2>
                    <div class="mvx_policies_description description" >'.$policies['shipping_policy'].'</div>
                </div>';
            } 
            if(isset($policies['refund_policy']) && !empty($policies['refund_policy']) && $refund){ 
                $content .='<div class="mvx-refund-policies policy">
                    <h2 class="mvx_policies_heading heading heading">'. esc_html_e('Refund Policy', 'multivendorx').'</h2>
                    <div class="mvx_policies_description description">'.$policies['refund_policy'].'</div>
                </div>';
            } 
            if(isset($policies['cancellation_policy']) && !empty($policies['cancellation_policy']) && $cancel){ 
                $content .='<div class="mvx-cancellation-policies policy">
                    <h2 class="mvx_policies_heading heading">'. esc_html_e('Cancellation / Return / Exchange Policy', 'multivendorx').'</h2>
                    <div class="mvx_policies_description description" >'.$policies['cancellation_policy'].'</div>
                </div>';
            }
            $content .='</div>';
        }
        echo $content; 

        do_action($this->widget_cssclass . '_bottom', $vendor);
        
        $this->widget_end($args);
    }

    function get_mvx_vendor_policies($vendor = 0) {
        $policies = array();
        $shipping_policy = get_mvx_vendor_settings('shipping_policy');
        $refund_policy = get_mvx_vendor_settings('refund_policy');
        $cancellation_policy = get_mvx_vendor_settings('cancellation_policy');
        if (apply_filters('mvx_vendor_can_overwrite_policies', true) && $vendor) {
            $shipping_policy = get_user_meta($vendor->id, '_vendor_shipping_policy', true) ? get_user_meta($vendor->id, '_vendor_shipping_policy', true) : $shipping_policy;
            $refund_policy = get_user_meta($vendor->id, '_vendor_refund_policy', true) ? get_user_meta($vendor->id, '_vendor_refund_policy', true) : $refund_policy;
            $cancellation_policy = get_user_meta($vendor->id, '_vendor_cancellation_policy', true) ? get_user_meta($vendor->id, '_vendor_cancellation_policy', true) : $cancellation_policy;
        }
        if (!empty($shipping_policy)) {
            $policies['shipping_policy'] = $shipping_policy;
        }
        if (!empty($refund_policy)) {
            $policies['refund_policy'] = $refund_policy;
        }
        if (!empty($cancellation_policy)) {
            $policies['cancellation_policy'] = $cancellation_policy;
        }
        return $policies;
    }
    
}