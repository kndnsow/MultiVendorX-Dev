<?php

if (!defined('ABSPATH')) {
    exit;
}

class MVX_Gateway_Paypal_Masspay extends MVX_Payment_Gateway {

    public $id;
    public $gateway_title;
    public $payment_gateway;
    public $message = array();
    private $api_username;
    private $api_password;
    private $api_signature;
    private $test_mode = false;
    private $reciver_email;
    private $api_endpoint;

    public function __construct() {
        $this->id = 'paypal_masspay';
        $this->gateway_title = __('Paypal masspay', 'multivendorx');
        $this->payment_gateway = $this->id;
        $this->enabled = mvx_is_module_active('paypal-masspay') ? 'Enable' : '';
        $this->api_username = get_mvx_global_settings('api_username');
        $this->api_password = get_mvx_global_settings('api_pass');
        $this->api_signature = get_mvx_global_settings('api_signature');
        $this->api_endpoint = 'https://api-3t.paypal.com/nvp';
        if (get_mvx_global_settings('is_testmode')) {
            $this->test_mode = true;
            $this->api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
        }
    }
    
    public function gateway_logo() { global $MVX; return $MVX->plugin_url . 'assets/images/'.$this->id.'.png'; }

    public function process_payment($vendor, $commissions = array(), $transaction_mode = 'auto') {
        $this->vendor = $vendor;
        $this->commissions = $commissions;
        $this->currency = get_woocommerce_currency();
        $this->transaction_mode = $transaction_mode;
        $this->reciver_email = get_user_meta($this->vendor->id, '_vendor_paypal_email', true);
        if ($this->validate_request()) {
            $paypal_response = $this->process_paypal_masspay();
            if ($paypal_response) {
                $this->record_transaction();
                if ($this->transaction_id) {
                    return array('message' => __('New transaction has been initiated', 'multivendorx'), 'type' => 'success', 'transaction_id' => $this->transaction_id);
                }
            } else {
                return false;
            }
        } else {
            return $this->message;
        }
    }

    public function validate_request() {
        global $MVX;
        if ($this->enabled != 'Enable') {
            $this->message[] = array('message' => __('Invalid payment method', 'multivendorx'), 'type' => 'error');
            return false;
        } else if (!$this->api_username && !$this->api_password && !$this->api_signature) {
            $this->message[] = array('message' => __('Paypal masspay setting is not configured properly please contact site administrator', 'multivendorx'), 'type' => 'error');
            return false;
        } else if (!$this->reciver_email) {
            $this->message[] = array('message' => __('Please update your paypal email to receive commission', 'multivendorx'), 'type' => 'error');
            return false;
        }
        if ($this->transaction_mode != 'admin') {
            /* handel thesold time */
            $threshold_time = get_mvx_global_settings('commission_threshold_time') ? get_mvx_global_settings('commission_threshold_time') : 0;
            if ($threshold_time > 0) {
                foreach ($this->commissions as $index => $commission) {
                    if (intval((date('U') - get_the_date('U', $commission)) / (3600 * 24)) < $threshold_time) {
                        unset($this->commissions[$index]);
                    }
                }
            }
            /* handel thesold amount */
            $thesold_amount = get_mvx_global_settings('commission_threshold') ? get_mvx_global_settings('commission_threshold') : 0;
            if ($this->get_transaction_total() > $thesold_amount) {
                return true;
            } else {
                $this->message[] = array('message' => __('Minimum thesold amount to withdrawal commission is ' . $thesold_amount, 'multivendorx'), 'type' => 'error');
                return false;
            }
        }
        return parent::validate_request();
    }

    private function process_paypal_masspay() {
        $nvpheader = "&PWD=" . urlencode($this->api_password) . "&USER=" . urlencode($this->api_username) . "&SIGNATURE=" . urlencode($this->api_signature);
        $amount_to_pay = round($this->get_transaction_total() - $this->transfer_charge($this->transaction_mode) - $this->gateway_charge(), 2);
        $note = sprintf(__('Total commissions earned from %1$s as at %2$s on %3$s', 'multivendorx'), get_bloginfo('name'), date('H:i:s'), date('d-m-Y'));
        $nvpStr = '&L_EMAIL0=' . urlencode($this->reciver_email) . '&L_Amt0=' . urlencode($amount_to_pay) . '&L_UNIQUEID0=' . urlencode($this->vendor->id) . '&L_NOTE0=' . urlencode($note) . '&EMAILSUBJECT=' . urlencode('You have money!') . '&RECEIVERTYPE=' . urlencode('EmailAddress') . '&CURRENCYCODE=' . urlencode($this->currency);
        $nvpStr = $nvpheader . $nvpStr;
        $nvpStr = "&VERSION=" . urlencode(90) . $nvpStr;
        $nvpreq = "METHOD=" . urlencode('MassPay') . $nvpStr;

        //post to PayPal masspay
        $response = wp_remote_post( $this->api_endpoint, apply_filters( "mvx_payment_gateway_{$this->id}_http_process", array(
                        'timeout' => 60,
                        'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
                        'httpversion' => '1.1',
                        'body' => $nvpreq
            ) )
        );
        //extract the response details
        $httpParsedResponseAr = array();
        parse_str(wp_remote_retrieve_body($response), $httpParsedResponseAr);
                        
        $ack = strtoupper($httpParsedResponseAr["ACK"]);
        if ($ack == "SUCCESS" || $ack == "SuccessWithWarning") {
            return $httpParsedResponseAr;
        } else {
            doProductVendorLOG(json_encode($httpParsedResponseAr));
            if (isset($httpParsedResponseAr['L_LONGMESSAGE0'])) {
                $this->add_commission_note($this->commissions, 'Error: ' . $httpParsedResponseAr['L_LONGMESSAGE0']);
            }
            return false;
        }
    }

    public function deformatNVP($nvpstr) {
        $intial = 0;
        $nvpArray = array();
        while (strlen($nvpstr)) {
            //postion of Key
            $keypos = strpos($nvpstr, '=');
            //position of value
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

            /* getting the Key and Value values and storing in a Associative Array */
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        return $nvpArray;
    }

}
