<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Plugin_Deactivated_Mail')) :

    class WC_Email_Plugin_Deactivated_Mail extends WC_Email {

        var $user_login;
        var $user_email;

        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        function __construct() {
            global $MVX;
            $this->id = 'plugin_deactivated_mail';
            $this->title = __('Plugin Deactivated Mail', 'multivendorx');
            $this->description = __('Plugin deactivated email is sent when a user deactivated the mvx-plugin.', 'multivendorx');

            $this->template_html = 'emails/plugin-deactivated-mail.php';
            $this->template_plain = 'emails/plain/plugin-deactivated-mail.php';

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
                $this->recipient = $this->user_email;
            }

            if (!$this->is_enabled() || !$this->get_recipient())
                return;

            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }

        /**
         * Get email headers.
         *
         * @return string
         */
        public function get_headers() {
            $reply_to_mail = 'contact@multivendorx.com';
            $header = 'Content-Type: ' . $this->get_content_type() . "\r\n";
            $header .= 'Reply-to: ' . '<' . $reply_to_mail . ">\r\n";
            return $header;
        }

        /**
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('mvx_plugin_deactivated_mail_email_subject', __('We are sorry for your MultiVendorX deactivation', 'multivendorx'));
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('mvx_plugin_deactivated_mail_email_heading', __('We will be missing you', 'multivendorx'));
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
                'blogname' => $this->get_blogname(),
                'sent_to_admin' => false,
                'plain_text' => true,
                'email'         => $this,
                    ), 'MultiVendorX/', $this->template_base);
            return ob_get_clean();
        }

    }

endif;