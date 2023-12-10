<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Admin_New_Vendor_Account')) :

    /**
     * New Order Email
     *
     * An email sent to the admin when a new order is received/paid for.
     *
     * @class 		WC_Email_New_Order
     * @version		2.0.0
     * @package		WooCommerce/Classes/Emails
     * @author 		WooThemes
     * @extends 	WC_Email
     */
    class WC_Email_Admin_New_Vendor_Account extends WC_Email {

        /**
         * Constructor
         */
        function __construct() {
            global $MVX;
            $this->id = 'admin_new_vendor';
            $this->title = __('Admin New Vendor Account', 'multivendorx');
            $this->description = __('New emails are sent when a user applies to be a vendor.', 'multivendorx');

            $this->template_html = 'emails/admin-new-vendor-account.php';
            $this->template_plain = 'emails/plain/admin-new-vendor-account.php';
            $this->template_base = $MVX->plugin_path . 'templates/';

            // Call parent constructor
            parent::__construct();

            // Other settings
            $this->recipient = $this->get_option('recipient');

            if (!$this->recipient)
                $this->recipient = get_option('admin_email');
        }

        /**
         * trigger function.
         *
         * @access public
         * @return void
         */
        function trigger($user_id, $user_pass = '', $password_generated = false) {

            if ($user_id) {
                $this->object = new WP_User($user_id);
                $this->user_email = stripslashes($this->object->user_email);
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
            return apply_filters('mvx_admin_new_vendor_email_subject', __('[{site_title}] New Vendor Account', 'multivendorx'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('mvx_admin_new_vendor_email_heading', __('New Vendor Account', 'multivendorx'), $this->object);
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
                'user_object' => $this->object,
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
                'user_object' => $this->object,
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
