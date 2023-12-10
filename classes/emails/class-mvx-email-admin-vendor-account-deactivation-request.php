<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Admin_Vendor_Account_Deactivation_Request_Mail')) :

    class WC_Email_Admin_Vendor_Account_Deactivation_Request_Mail extends WC_Email {

        var $user_login;
        var $user_email;
        var $admin_name;

        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        function __construct() {
            global $MVX;
            $this->id = 'vendor_account_deactivation_request';
            $this->title = __('Vendor Account Deactivation Request', 'multivendorx');
            $this->description = __('Vendor account deactivation request email is sent when a vendor deactivate their account.', 'multivendorx');

            $this->template_html = 'emails/vendor-account-deactivation-request.php';
            $this->template_plain = 'emails/plain/vendor-account-deactivation-request.php';

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
        function trigger($user_id) {

            if ($user_id) {
                $this->object = new WP_User($user_id);
                $this->user_login = stripslashes($this->object->user_login);
                $this->user_email = stripslashes($this->object->user_email);
                $this->find[] = '{vendor_name}';
                $this->replace[] = $this->user_login;
                $admin_email = get_option('admin_email');
                $user = get_user_by( 'email', $admin_email );
                $this->admin_name = $user->display_name;
                $this->recipient = $admin_email; // For admin

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
            return apply_filters('mvx_vendor_account_deactivation_mail_email_subject', __('Vendor Profile Deletion Request for {vendor_name}', 'multivendorx'));
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('mvx_vendor_account_deactivation_mail_email_heading', __('{vendor_name} wants to delete profile', 'multivendorx'));
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
                'user_login' => $this->user_login,
                'admin_name' => $this->admin_name,
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
                'user_login' => $this->user_login,
                'admin_name' => $this->admin_name,
                'blogname' => $this->get_blogname(),
                'sent_to_admin' => false,
                'plain_text' => true,
                'email'         => $this,
                    ), 'MultiVendorX/', $this->template_base);
            return ob_get_clean();
        }

    }

endif;