<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Customer_Answer')) :

    /**
     * Customer Answer Email
     *
     * An email sent to the admin  and customer when a vendor post answer.
     *
     * @class 		WC_Email_Customer_Answer
     * @version		2.0.0
     * @package		WooCommerce/Classes/Emails
     * @author 		WooThemes
     * @extends 	WC_Email
     */
    class WC_Email_Customer_Answer extends WC_Email {
        /**
         * Constructor
         */
        function __construct() {
            global $MVX;
            $this->id = 'customer_answer';
            $this->title = __('Customer Answer', 'multivendorx');
            $this->description = __('Answer notification emails are sent when vendor reply a question.', 'multivendorx');

            $this->template_html = 'emails/customer-answer.php';
            $this->template_plain = 'emails/plain/customer-answer.php';
            $this->template_base = $MVX->plugin_path . 'templates/';

            // Call parent constructor
            parent::__construct();
        }

        /**
         * Get email subject.
         *
         * @since  3.1.0
         * @return string
         */
        function trigger( $customer, $answer, $product_id ) {
        $this->answer = $answer;
        $this->product_id = $product_id;
        if($customer && !isset($customer->user_email)) return;
        $this->recipient = $customer->user_email;

        $product = wc_get_product($product_id);
        $this->find[] = '{product_name}';
        $this->product_name = $product->get_name();
        $this->replace[] = $this->product_name;
        
        $this->customer = $customer;
        if ( ! $this->is_enabled() || ! $this->get_recipient() ) return;

        
        return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }
    
    /**
     * Get email subject.
     *
     * @access  public
     * @return string
     */
    public function get_default_subject() {
        $subject = __( 'Product Name - {product_name}', 'multivendorx');
        if( isset($this->object['subject']) && !empty($this->object['subject']) ){
            $subject = $subject . ' - ' . $this->object['subject'];
        }
        return apply_filters( 'mvx_vendor_customer_answer_email_subject', $subject, $this->object );
    }

    /**
     * Get email heading.
     *
     * @access  public
     * @return string
     */
    public function get_default_heading() {
        return apply_filters( 'mvx_vendor_customer_answer_email_heading', __( "Query Response - Answered ", 'multivendorx'), $this->object );
    }
    
    /**
    * Get WordPress blog name.
    *
    * @return string
    */
    public function get_blogname() {
           return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    }

    /**
     * get_content_html function.
     *
     * @access public
     * @return string
     */
    function get_content_html() {
        ob_start();
        wc_get_template( $this->template_html, array(
            'customer'      => $this->customer,
            'email_heading' => $this->get_heading(),
            'answer'        => $this->answer,
            'product_id'    => $this->product_id,
            'plain_text'    => false,
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
        wc_get_template( $this->template_plain, array(
            'customer'      => $this->customer,
            'email_heading' => $this->get_heading(),
            'answer'        => $this->answer,
            'product_id'    => $this->product_id,
            'plain_text'    => true,
            'email'         => $this,
            ), 'MultiVendorX/', $this->template_base);
        return ob_get_clean();
    }

    }
  
    endif;
