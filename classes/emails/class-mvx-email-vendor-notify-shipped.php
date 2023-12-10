<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Notify_Shipped')) :

    /**
     * Customer New Account
     *
     * An email sent to the customer when they create an account.
     *
     * @class 		WC_Email_Approved_New_Vendor_Account
     * @version		2.0.0
     * @package		WooCommerce/Classes/Emails
     * @author 		WooThemes
     * @extends 	WC_Email
     */
    class WC_Email_Notify_Shipped extends WC_Email {

        public $vendor_id;
        public $tracking_url;
        public $tracking_id;

        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        function __construct() {
            global $MVX;
            $this->id = 'notify_shipped';
            $this->title = __('Notify as Shipped.', 'multivendorx');
            $this->description = __('Confirm customer that vendor has shipped the order.', 'multivendorx');

            $this->template_html = 'emails/vendor-notify-shipped.php';
            $this->template_plain = 'emails/plain/vendor-notify-shipped.php';

            //$this->subject = __('Your Order on {site_title} has been Shipped', 'multivendorx');
            //$this->heading = __('Welcome to {site_title}', 'multivendorx');
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
        function trigger($order_id, $customer_email, $vendor_id, $param = array()) {

            if ($order_id && $customer_email && $vendor_id) {
                $this->object = new WC_Order($order_id);

                $this->find[] = '{order_date}';
                $this->replace[] = date_i18n(wc_date_format(), strtotime($this->object->get_date_created()));

                $this->find[] = '{order_number}';
                $this->replace[] = $this->object->get_order_number();
                $this->vendor_id = $vendor_id;
                if (isset($param['tracking_id'])) {
                    $this->tracking_id = $param['tracking_id'];
                }
                if (isset($param['tracking_url'])) {
                    $this->tracking_url = $param['tracking_url'];
                }

                //$user = get_user_by( 'id', $user_id );

                $this->recipient = $customer_email;
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
            return apply_filters('mvx_notify_shipped_email_subject', __('Your Order on {site_title} has been Shipped', 'multivendorx'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('mvx_notify_shipped_email_heading', __('Welcome to {site_title}', 'multivendorx'), $this->object);
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
                'vendor_id' => $this->vendor_id,
                'order' => $this->object,
                'tracking_url' => $this->tracking_url,
                'tracking_id' => $this->tracking_id,
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
                'vendor_id' => $this->vendor_id,
                'order' => $this->object,
                'tracking_url' => $this->tracking_url,
                'tracking_id' => $this->tracking_id,
                'blogname' => $this->get_blogname(),
                'sent_to_admin' => false,
                'plain_text' => true,
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
