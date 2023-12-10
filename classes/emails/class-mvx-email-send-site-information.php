<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Send_Site_Information')) :

    class WC_Email_Send_Site_Information extends WC_Email {

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
            $this->id = 'send_site_information';
            $this->title = __('Send Site Information', 'multivendorx');
            $this->description = __('Send site information email is sent when a user share the information of their site.', 'multivendorx');

            $this->template_html = 'emails/send-site-information.php';
            $this->template_plain = 'emails/plain/send-site-information.php';

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
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('mvx_sent_site_information_email_subject', __('Thank you for submitting your details, please grab the offer now', 'multivendorx'));
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('mvx_sent_site_information_email_heading', __('Grab MultiVendorX Pro now, offer valid for 2 days', 'multivendorx'));
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