<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Email_Vendor_Contact_Widget' ) ) :

/**
 * Vendor_Contact_Widget
 *
 * An email sent to the vendor via customer contact.
 *
 * @class 		WC_Email_Vendor_Contact_Widget
 * @version		3.3.2
 * @package		MultivendorX/Classes/Emails
 * @extends 	WC_Email
 *
 */
class WC_Email_Vendor_Contact_Widget extends WC_Email {
    public $object;
    public $vendor;
    /**
     * Constructor
     */
    function __construct() {
        global $MVX;
        $this->id                   = 'vendor_contact_widget_email';
        $this->title 		= __( 'Vendor Contact Email', 'multivendorx' );
        $this->description		= __( 'Vendor contact email via customer.', 'multivendorx');

        $this->template_base = $MVX->plugin_path . 'templates/';
        $this->template_html 	= 'emails/vendor-contact-widget-email.php';
        $this->template_plain 	= 'emails/plain/vendor-contact-widget-email.php';

        // Call parent constructor
        parent::__construct();
    }

    /**
     * trigger function.
     *
     * @access public
     *
     * @param Vendor orders stats report
     */
    function trigger( $vendor, $post_data  ) {
        $this->object = $post_data;
        if($vendor && !isset($vendor->user_data->user_email)) return;
        $this->recipient = $vendor->user_data->user_email;
        
        $this->vendor = $vendor;
        if ( ! $this->is_enabled() || ! $this->get_recipient() ) return;

        $this->find[ ]      = '{site_title}';
        $this->replace[ ]   = $this->get_blogname();

        $this->find[ ]      = '{STORE_NAME}';
        $this->replace[ ]   = $vendor->page_title;
        
        return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }
    
    /**
     * Get email subject.
     *
     * @access  public
     * @return string
     */
    public function get_default_subject() {
        $subject = __( '[{site_title}] Contact Vendor', 'multivendorx');
        if( isset($this->object['subject']) && !empty($this->object['subject']) ){
            $subject = $subject . ' - ' . $this->object['subject'];
        }
        return apply_filters( 'mvx_vendor_contact_widget_email_subject', $subject, $this->object );
    }

    /**
     * Get email heading.
     *
     * @access  public
     * @return string
     */
    public function get_default_heading() {
        return apply_filters( 'mvx_vendor_contact_widget_email_heading', __( "{STORE_NAME}'s Customer contact", 'multivendorx'), $this->object );
    }
    
    /**
     * Get email headers.
     *
     * @return string
     */
    public function get_headers() {
        $header = 'Content-Type: ' . $this->get_content_type() . "\r\n";
        if( apply_filters( 'mvx_vendor_contact_widget_email_goes_to_admin', true, $this->object ) ) {
            $header .= 'Cc: Admin <' . get_option('admin_email') . '>'."\r\n";
        }
        if ( isset( $this->object['email'] ) ) {
            $header .= 'Reply-to: ' . $this->object['name'] . ' <' . $this->object['email'] . ">\r\n";
        }

        return apply_filters( 'mvx_vendor_contact_widget_email_headers', $header, $this->id, $this->object );
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
            'object'   => $this->object,
            'vendor'        =>  $this->vendor,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
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
            'object'   => $this->object,
            'vendor'        =>  $this->vendor,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => true,
            'email'         => $this,
            ), 'MultiVendorX/', $this->template_base);
        return ob_get_clean();
    }
}

endif;
