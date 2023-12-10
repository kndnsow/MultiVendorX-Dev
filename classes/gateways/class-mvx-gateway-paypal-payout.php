<?php

if (!defined('ABSPATH')) {
    exit;
}

class MVX_Gateway_Paypal_Payout extends MVX_Payment_Gateway {

    public $id;
    public $gateway_title;
    public $payment_gateway;
    public $message = array();
    private $client_id;
    private $client_secret;
    private $test_mode = false;
    private $payout_mode = 'true';
    private $reciver_email;
    private $api_endpoint;
    private $token_endpoint;
    private $access_token;
    private $token_type;

    public function __construct() {
        $this->id = 'paypal_payout';
        $this->gateway_title = __('Paypal payout', 'multivendorx');
        $this->payment_gateway = $this->id;
        $this->enabled = mvx_is_module_active('paypal-payout') ? 'Enable' : '';
        $this->client_id = get_mvx_global_settings('client_id');
        $this->client_secret = get_mvx_global_settings('client_secret');
        if (get_mvx_global_settings('is_asynchronousmode')) {
            $this->payout_mode = 'false';
        }
        $this->api_endpoint = 'https://api.paypal.com/v1/payments/payouts?sync_mode='.$this->payout_mode;
        $this->token_endpoint = 'https://api.paypal.com/v1/oauth2/token';
        if (get_mvx_global_settings('is_testmode')) {
            $this->test_mode = true;
            $this->api_endpoint = 'https://api.sandbox.paypal.com/v1/payments/payouts?sync_mode='.$this->payout_mode;
            $this->token_endpoint = 'https://api.sandbox.paypal.com/v1/oauth2/token';
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
            $this->generate_access_token();
            $paypal_response = $this->process_paypal_payout();
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
        } else if (!$this->client_id && !$this->client_secret) {
            $this->message[] = array('message' => __('Paypal payout setting is not configured properly please contact site administrator', 'multivendorx'), 'type' => 'error');
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

    private function generate_access_token() {

        // generate access token for paypal payout
        $auth = base64_encode( $this->client_id . ':' . $this->client_secret );
        $response = wp_remote_post( $this->token_endpoint, apply_filters( "mvx_payment_gateway_{$this->id}_http_process_access_token", array(
                        'timeout' => 60,
                        'headers' => array(
                            'Authorization' => "Basic $auth"
                            ),
                        'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
                        'httpversion' => '1.1',
                        'body' => array(
                            'grant_type' => 'client_credentials'
                        )
            ) )
        );
        //extract the response details
        $response_array = json_decode(wp_remote_retrieve_body( $response ));

        $this->access_token = isset($response_array->access_token) ? $response_array->access_token : '';
        $this->token_type = isset($response_array->token_type) ? $response_array->token_type : '';
    }

    private function process_paypal_payout() {
        $api_authorization = "Authorization: {$this->token_type} {$this->access_token}";
        $amount_to_pay = round($this->get_transaction_total() - $this->transfer_charge($this->transaction_mode) - $this->gateway_charge(), 2);
        $note = sprintf(__('Total commissions earned from %1$s as at %2$s on %3$s', 'multivendorx'), get_bloginfo('name'), date('H:i:s'), date('d-m-Y'));
        $request_params = '{
		"sender_batch_header": {
                    "sender_batch_id":"' . uniqid() . '",
                    "email_subject": "You have a payment",
                    "recipient_type": "EMAIL"
                },
                "items": [
                  {
                    "recipient_type": "EMAIL",
                    "amount": {
                      "value": ' . $amount_to_pay . ',
                      "currency": "' . $this->currency . '"
                    },
                    "receiver": "' . $this->reciver_email . '",
                    "note": "' . $note . '",
                    "sender_item_id": "' . $this->vendor->id . '"
                  }
                ]
	}';
        $response = wp_remote_post( $this->api_endpoint, apply_filters( "mvx_payment_gateway_{$this->id}_http_process", array(
                        'timeout' => 60,
                        'headers' => array(
                            'Content-Type' => 'application/json',
                            'Authorization' => "$this->token_type $this->access_token"
                        ),
                        'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
                        'httpversion' => '1.1',
                        'body' => $request_params
            ) )
        );
 
        //extract the response details
        $result_array = json_decode(wp_remote_retrieve_body( $response ));

        $batch_status = $result_array ? $result_array->batch_header->batch_status : '';
        if($this->payout_mode == 'true'){
            $transaction_status = is_array($result_array->items) ? $result_array->items[0]->transaction_status : '';
            if ($batch_status == 'SUCCESS' && $transaction_status == 'SUCCESS') {
                return $result_array;
            } else {
                doProductVendorLOG(json_encode($result_array));
                $this->add_commission_note($this->commissions, __('Payment failed', 'multivendorx'));
                return false;
            }
        }else{
            $batch_payout_status = apply_filters('mvx_paypal_payout_batch_status', array('PENDING', 'PROCESSING', 'SUCCESS', 'NEW'));
            if (in_array($batch_status, $batch_payout_status) ) {
                return $result_array;
            } else {
                doProductVendorLOG(json_encode($result_array));
                $this->add_commission_note($this->commissions, __('Payment failed', 'multivendorx'));
                return false;
            }
        }
    }

}
