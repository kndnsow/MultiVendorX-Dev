<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class 		MVX Vendor Order Class
 *
 * @version		3.4.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
class mvx_vendor_order {
    
    public $id;
    public $vendor_id;
    public $order;
    
    /**
     * Get the order if ID is passed, otherwise the order is new and empty.
     *
     * @param  int|object|MVX_Order $order Order to read.
     */
    public function __construct( $order = 0 ) {

        if ( is_numeric( $order ) && $order > 0 ) {
            $this->id   = absint( $order );
        } elseif ( $order instanceof WC_Order ) {
            $this->id = absint( $order->id );
        }else{
            $this->id = 0;
        }
        $this->vendor_id = absint( get_post_meta($this->id, '_vendor_id', true) );
        
        $this->order = wc_get_order( $this->id );
    }
    
    public function get_prop( $prop ) {
        return get_post_meta($this->id, $prop, true);
    }
    
    /**
     * Get order vendor.
     *
     * @since 3.4.0
     * @return object/false Vendor
     */
    public function get_vendor() {
        return is_user_mvx_vendor($this->vendor_id) ? get_mvx_vendor($this->vendor_id) : false;
    }
    
    /**
     * Get vendor commission total.
     *
     * @since 3.4.0
     */
    public function get_commission_total($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        return MVX_Commission::commission_totals($commission_id, $context);
    }
    
    /**
     * Get vendor commission amount.
     *
     * @since 3.4.0
     */
    public function get_commission($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        return MVX_Commission::commission_amount_totals($commission_id, $context);
    }
    
    /**
     * Get formatted commission total.
     *
     * @since 3.4.0
     */
    public function get_formatted_commission_total($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        $commission_amount = get_post_meta( $commission_id, '_commission_amount', true );
        if($commission_amount != MVX_Commission::commission_amount_totals($commission_id, 'edit')){
            return '<del>' . wc_price($commission_amount, array('currency' => $this->order->get_currency())) . '</del> <ins>' . MVX_Commission::commission_amount_totals($commission_id, $context).'</ins>'; 
        }else{
            return MVX_Commission::commission_amount_totals($commission_id, $context);
        }
    }
    
    /**
     * Get commission refunded amount.
     *
     * @since 3.4.0
     */
    public function get_commission_refunded_amount($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        return MVX_Commission::commission_refunded_totals($commission_id, $context);
    }
    
    /**
     * Get items commission refunded amount.
     *
     * @since 3.4.0
     */
    public function get_items_commission_refunded_amount($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        return MVX_Commission::commission_items_refunded_totals($commission_id, $context);
    }
    
    /**
     * Get total commission refunded amount.
     *
     * @since 3.4.7
     */
    public function get_total_commission_refunded_amount($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        return MVX_Commission::commission_refunded_totals($commission_id, $context);
    }
    
    /**
     * Get vendor shipping amount.
     *
     * @since 3.4.0
     */
    public function get_shipping($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        return MVX_Commission::commission_shipping_totals($commission_id, $context);
    }
    
    /**
     * Get vendor tax amount.
     *
     * @since 3.4.0
     */
    public function get_tax($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        return MVX_Commission::commission_tax_totals($commission_id, $context);
    }
    
    /**
     * Get vendor order.
     *
     * @since 3.4.0
     * @return object/false Vendor order
     */
    public function get_order() {
        return $this->order ? $this->order : false;
    }
    
    /**
     * Get formatted order total earned.
     *
     * @since 3.4.3
     */
    public function get_formatted_order_total_earned($context = 'view') {
        $commission_id = $this->get_prop('_commission_id');
        $commission_total = get_post_meta( $commission_id, '_commission_total', true );
        if($commission_total != MVX_Commission::commission_totals($commission_id, 'edit')){
            return '<del>' . wc_price($commission_total, array('currency' => $this->order->get_currency())) . '</del> <ins>' . MVX_Commission::commission_totals($commission_id, $context).'</ins>'; 
        }else{
            return MVX_Commission::commission_totals($commission_id, $context);
        }
    }
    
}


