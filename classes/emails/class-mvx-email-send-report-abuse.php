<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Email_Send_Report_Abuse' ) ) :

/**
 * Send_Report_Abuse
 *
 * An email sent to the vendor via customer contact.
 *
 * @class 		WC_Email_Send_Report_Abuse
 * @version		3.3.7
 * @package		MultivendorX/Classes/Emails
 * @extends 	WC_Email
 *
 */
class WC_Email_Send_Report_Abuse extends WC_Email {
    public $object;
    public $vendor;
    /**
     * Constructor
     */
    function __construct() {
        global $MVX;
        $this->id                   = 'mvx_send_report_abuse';
        $this->title 		= __( 'Report Abuse', 'multivendorx' );
        $this->description		= __( 'Report Abuse email via customer.', 'multivendorx');

        $this->template_base = $MVX->plugin_path . 'templates/';
        $this->template_html 	= 'emails/report-abuse-email.php';
        $this->template_plain 	= 'emails/plain/report-abuse-email.php';

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
        $this->vendor = $vendor;
        $this->recipient = get_option('admin_email');
        if( apply_filters( 'mvx_report_abuse_email_goes_to_vendor', false, $this->object ) )
            $this->recipient .= ',' . sanitize_email($vendor->user_data->user_email);
        
        if ( ! $this->is_enabled() || ! $this->get_recipient() ) return;
        if ( isset( $post_data['product_id'] ) ) {
            $product = wc_get_product( absint( $post_data['product_id'] ) );
            
            $this->find[ ]      = '{product_name}';
            $this->replace[ ]   = $product->get_title();
        }
        
        return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }
    
    /**
     * Get email subject.
     *
     * @access  public
     * @return string
     */
    public function get_default_subject() {
        $subject = __( 'Report an abuse for product {product_name}', 'multivendorx');
        if( isset($this->object['subject']) && !empty($this->object['subject']) ){
            $subject = $subject . ' - ' . $this->object['subject'];
        }
        return apply_filters( 'mvx_report_abuse_email_subject', $subject, $this->object );
    }

    /**
     * Get email heading.
     *
     * @access  public
     * @return string
     */
    public function get_default_heading() {
        return apply_filters( 'mvx_report_abuse_email_heading', __( "Report abuse for {product_name}", 'multivendorx'), $this->object );
    }
    
    /**
     * Get email headers.
     *
     * @return string
     */
    public function get_headers() {
        $header = 'Content-Type: ' . $this->get_content_type() . "\r\n";
//        if( apply_filters( 'mvx_report_abuse_email_goes_to_admin', true, $this->object ) ) {
//            $header .= 'Cc: Admin <' . get_option('admin_email') . '>'."\r\n";
//        }
        if ( isset( $this->object['email'] ) ) {
            $header .= 'Reply-to: ' . $this->object['name'] . ' <' . $this->object['email'] . ">\r\n";
        }

        return apply_filters( 'mvx_report_abuse_email_headers', $header, $this->id, $this->object );
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
