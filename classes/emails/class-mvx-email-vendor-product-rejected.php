<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Vendor_Product_Rejected')) :

    /**
     * Customer New Account
     *
     * An email sent to the customer when admin reject a product.
     *
     * @class 		WC_Email_Vendor_Product_Rejected
     * @version		3.5.1
     * @package		MultivendorX/Classes/Emails
     * @author 		WooThemes
     * @extends 	WC_Email
     */
    class WC_Email_Vendor_Product_Rejected extends WC_Email {

        var $user_login;
        var $user_email;
        var $user_pass;

        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        function __construct() {
            global $MVX;

            $this->id = 'admin_vendor_product_rejected';

            $this->title = __('Vendor Product Rejected', 'multivendorx');
            $this->description = __('Notification emails are sent when a product is rejected by an admin.', 'multivendorx');

            //$this->heading = __('New product submitted: {product_name}', 'multivendorx');
            //$this->subject = __('[{blogname}] New product submitted by {vendor_name} - {product_name}', 'multivendorx');

            $this->template_base = $MVX->plugin_path . 'templates/';
            $this->template_html = 'emails/product-rejected.php';
            $this->template_plain = 'emails/plain/product-rejected.php';


            // Call parent constuctor
            parent::__construct();

        }

        /**
         * trigger function.
         *
         * @access public
         * @return void
         *
         * @param unknown $order_id
         */
        function trigger($post_id, $post, $vendor, $reason) {
            global $MVX;

            if (!$this->is_enabled())
                return;
            
            $this->object = $post;
            $this->find[] = '{product_name}';
            $this->product_name = $post->post_title;
            $this->replace[] = $this->product_name;

            $this->find[] = '{vendor_name}';
            $this->vendor_name = $vendor->page_title;
            $this->replace[] = $this->vendor_name;

            $this->reason = $reason;
            $this->post_id = $post->ID;

            $post_type = get_post_type($this->post_id);
            $this->post_type = $post_type;

            $vendor_email = $vendor->user_data->user_email;
            $this->recipient = $vendor_email;

            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
        
        /**
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('mvx_admin_new_vendor_product_email_subject', __('[{blogname}] Submitted Product Rejected by {vendor_name} - {product_name}', 'multivendorx'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('mvx_admin_new_vendor_product_email_heading', __('Submitted Product Rejected: {product_name}', 'multivendorx'), $this->object);
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
                'product_name' => $this->product_name,
                'vendor_name' => $this->vendor_name,
                'post_id' => $this->post_id,
                'reason' => $this->reason,
                'post_type' => $this->post_type,
                'email_heading' => $this->get_heading(),
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
                'product_name' => $this->product_name,
                'vendor_name' => $this->vendor_name,
                'post_id' => $this->post_id,
                'reason' => $this->reason,
                'post_type' => $this->post_type,
                'email_heading' => $this->get_heading(),
                'email'         => $this,
                    ), 'MultiVendorX/', $this->template_base);

            return ob_get_clean();
        }

        /**
         * Initialise Settings Form Fields
         *
         * @access public
         * @return void
         */
        function init_form_fields() {
            global $MVX;
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'multivendorx'),
                    'type' => 'checkbox',
                    'label' => __('Enable this email notification.', 'multivendorx'),
                    'default' => 'yes'
                ),
                'recipient' => array(
                    'title' => __('Recipient(s)', 'multivendorx'),
                    'type' => 'text',
                    'description' => sprintf(__('Enter recipient(s) (comma separated) for this email. Defaults to <code>%s</code>.', 'multivendorx'), esc_attr(get_option('admin_email'))),
                    'placeholder' => '',
                    'default' => ''
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