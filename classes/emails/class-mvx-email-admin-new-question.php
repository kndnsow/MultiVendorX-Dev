<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Admin_New_Question')) :

    /**
     * Vendor New Question Mail
     *
     * An email sent to vendor when a customer ask a Question.
     *
     * @class 		WC_Email_Admin_New_Question
     * @version		2.0.0
     * @package		WooCommerce/Classes/Emails
     * @author 		WooThemes
     * @extends 	WC_Email
     */
    class WC_Email_Admin_New_Question extends WC_Email {
        /**
         * Constructor
         */
        function __construct() {
            global $MVX;
            $this->id = 'admin_new_question';
            $this->title = __('Admin New question', 'multivendorx');
            $this->description = __('New question notification emails are sent when customer ask a question.', 'multivendorx');

            $this->template_html = 'emails/admin-new-question.php';
            $this->template_plain = 'emails/plain/admin-new-question.php';
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
        function trigger( $vendor, $product_id, $cust_question, $cust_id ) {
        $this->question = $cust_question;

        $product = wc_get_product($product_id);
        $this->find[] = '{product_name}';
        $this->product_name = $product->get_name();
        $this->replace[] = $this->product_name;

        $customer = get_user_by("ID", $cust_id);
        $this->customer_name = $customer->data->display_name;

        $this->recipient = get_option('admin_email');
        
        $this->vendor = $vendor;
        if ( ! $this->is_enabled() || ! $this->get_recipient() ) return;

        $this->find[ ]      = '{site_title}';
        $this->replace[ ]   = $this->get_blogname();
        
        return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }
    
    /**
     * Get email subject.
     *
     * @access  public
     * @return string
     */
    public function get_default_subject() {
        $subject = __( ' New Query Received - {product_name}', 'multivendorx');
        if( isset($this->object['subject']) && !empty($this->object['subject']) ){
            $subject = $subject . ' - ' . $this->object['subject'];
        }
        return apply_filters( 'admin_new_question_email_subject', $subject, $this->object );
    }

    /**
     * Get email heading.
     *
     * @access  public
     * @return string
     */
    public function get_default_heading() {
        return apply_filters( 'admin_new_question_email_heading', __('Product Query: {product_name}', 'multivendorx'), $this->object );
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
            'vendor'        =>  $this->vendor,
            'email_heading' => $this->get_heading(),
            'customer_name' => $this->customer_name,
            'product_name' => $this->product_name,
            'question'      => $this->question,
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
            'vendor'        =>  $this->vendor,
            'email_heading' => $this->get_heading(),
            'customer_name' => $this->customer_name,
            'product_name' => $this->product_name,
            'question'      => $this->question,
            'plain_text'    => true,
            'email'         => $this,
            ), 'MultiVendorX/', $this->template_base);
        return ob_get_clean();
    }

    function init_form_fields() {
            global $MVX;
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'multivendorx'),
                    'type' => 'checkbox',
                    'label' => __('Enable this email notification.', 'multivendorx'),
                    'default' => 'yes'
                ),
                'subject' => array(
                    'title' => __('Subject', 'multivendorx'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', 'multivendorx'), $this->get_default_subject()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'heading' => array(
                    'title' => __('Email Heading', 'multivendorx'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the main heading contained within the email notification. Leave it blank to use the default heading: <code>%s</code>.', 'multivendorx'), $this->get_default_heading()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'email_type' => array(
                    'title' => __('Email Type', 'multivendorx'),
                    'type' => 'select',
                    'description' => __('Choose which format of email to be sent.', 'multivendorx'),
                    'default' => 'html',
                    'class' => 'email_type',
                    'options' => array(
                        'plain' => __('Plain Text', 'multivendorx'),
                        'html' => __('HTML', 'multivendorx'),
                        'multipart' => __('Multipart', 'multivendorx'),
                    )
                )
            );
        }

    }
  
    endif;
