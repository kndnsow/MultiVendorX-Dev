<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Customer_Refund_Request')) :

    /**
     * Customer New Account
     *
     * An email sent to the customer when they create an account.
     *
     * @class 		WC_Email_Customer_Refund_Request
     * @version		3.5.0
     * @package		MultivendorX/Classes/Emails
     * @author 		MVX
     * @extends 	WC_Email
     */
    class WC_Email_Customer_Refund_Request extends WC_Email {

        public $refund_details;
        public $user_type;
        public $status;
        protected $post_type = 'shop_order';
        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        function __construct() {
            global $MVX;
            $this->id = 'customer_order_refund_request';
            $this->title = __( 'Customer Order Refund Request', 'multivendorx');
            $this->description = __('Customer Order Refund Request.', 'multivendorx');

            $this->template_html = 'emails/customer-order-refund-request.php';
            $this->template_plain = 'emails/plain/customer-order-refund-request.php';
            $this->placeholders = array(
                '{order_date}'   => '',
                '{order_number}' => '',
                '{refund_status}'=> '',
            );

            $this->template_base = $MVX->plugin_path . 'templates/';
            // Call parent constuctor
            parent::__construct();
        }

        /**
         * trigger function.
         *
         * @access public
         * @return void
         */
        function trigger($recepient, $order_id , $refund_details = array(), $user_type = '' ) {
            $this->user_type = $user_type;
            if ( $recepient && $order_id ) {
                $this->object = wc_get_order( $order_id );
                if( apply_filters( "mvx_email_{$this->post_type}_eanble_admin_recepient", true, $this ) ) {
                    $this->recipient = $recepient . ',' .get_option( 'admin_email' );
                }else{
                    $this->recipient = $recepient;
                }
                // refund info
                $refund_status = get_post_meta( $order_id, '_customer_refund_order', true );
                if( $refund_status == 'refund_accept' ) {
                    $this->status = __( 'Accepted', 'multivendorx' );
                }elseif( $refund_status == 'refund_reject') {
                    $this->status = __( 'Rejected', 'multivendorx' );
                }else{
                    $this->status = __( 'Requested', 'multivendorx' );
                }
                $details = array(
                    'refund_reason' => isset( $refund_details['refund_reason'] ) ? $refund_details['refund_reason'] : '',
                    'addi_info' => isset( $refund_details['addi_info'] ) ? $refund_details['addi_info'] : '',
                    'admin_reason' => isset( $refund_details['admin_reason'] ) ? $refund_details['admin_reason'] : '',
                    'status' => $this->status,
                );

                $this->refund_details = $details;
                $this->placeholders['{order_number}'] = $order_id;
                $this->placeholders['{refund_status}'] = $this->status;
            }

            if (!$this->is_enabled() || !$this->get_recipient())
                return;

            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
        
        /**
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('mvx_customer_refund_request_email_subject', __('New Refund {refund_status} for - Order -#{order_number}', 'multivendorx'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('mvx_customer_refund_request_email_heading', __(' Refund {refund_status}! ', 'multivendorx'), $this->object);
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            ob_start();
            wc_get_template($this->template_html, array(
                'email_heading' => $this->get_heading(),
                'order' => $this->object,
                'refund_details' => $this->refund_details,
                'user_type' => $this->user_type,
                'blogname' => $this->get_blogname(),
                'sent_to_admin' => false,
                'plain_text' => false,
                'email'         => $this,
                    ), 'MultiVendorX/', $this->template_base);
            return ob_get_clean();
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain() {
            ob_start();
            wc_get_template($this->template_plain, array(
                'email_heading' => $this->get_heading(),
                'order' => $this->object,
                'refund_details' => $this->refund_details,
                'user_type' => $this->user_type,
                'blogname' => $this->get_blogname(),
                'sent_to_admin' => false,
                'plain_text' => true,
                'email'         => $this,
                    ), 'MultiVendorX/', $this->template_base);
            return ob_get_clean();
        }
    }

endif;
