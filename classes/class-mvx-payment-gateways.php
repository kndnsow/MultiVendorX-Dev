<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class MVX_Payment_Gateways {

    /** @var array Array of payment gateway classes. */
    public $payment_gateways;

    public function __construct() {
        $this->load_default_gateways();
        $this->init();
    }

    public function init() {
        $load_gateways = array(
            'MVX_Gateway_Paypal_Masspay',
            'MVX_Gateway_Paypal_Payout',
            'MVX_Gateway_Stripe_Connect',
            'MVX_Gateway_Bank_Transfer',
        );
        $load_gateways = apply_filters('mvx_payment_gateways', $load_gateways);
        foreach ($load_gateways as $gateway) {
            $load_gateway = is_string($gateway) ? new $gateway() : $gateway;
            $this->payment_gateways[ $load_gateway->id ] = $load_gateway;
        }
    }
    
    public function load_default_gateways(){
        require_once 'gateways/class-mvx-gateway-paypal-masspay.php';
        require_once 'gateways/class-mvx-gateway-paypal-payout.php';
        require_once 'gateways/class-mvx-gateway-stripe-connect.php';
        require_once 'gateways/class-mvx-gateway-bank-transfer.php';
    }
}
