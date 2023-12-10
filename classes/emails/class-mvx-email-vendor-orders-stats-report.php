<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Email_Vendor_Orders_Stats_Report' ) ) :

/**
 * New Order Email
 *
 * An email sent to the vendor with weekly or monthly order stats report.
 *
 * @class 		WC_Email_Vendor_Orders_Stats_Report
 * @version		3.0.0
 * @package		WooCommerce/Classes/Emails
 * @extends 	WC_Email
 *
 * @property DC_Commission $object
 */
class WC_Email_Vendor_Orders_Stats_Report extends WC_Email {
    public $attachments;
    public $report_data;
    public $vendor;
    /**
     * Constructor
     */
    function __construct() {
        global $MVX;
        $this->id                   = 'vendor_orders_stats_report';
        $this->title 		= __( 'Vendor orders stats report', 'multivendorx' );
        $this->description		= __( 'Vendor gets their weekly or monthly order reports.', 'multivendorx');

        $this->template_base = $MVX->plugin_path . 'templates/';
        $this->template_html 	= 'emails/vendor-orders-stats-report.php';
        $this->template_plain 	= 'emails/plain/vendor-orders-stats-report.php';

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
    function trigger( $vendor, $report_data, $attachments  ) {
        $this->report_data = $report_data;
        if($vendor && !isset($vendor->user_data->user_email)) return;
        $this->recipient = $vendor->user_data->user_email;
        $this->vendor = $this->object = $vendor;
        if ( ! $this->is_enabled() || ! $this->get_recipient() ) return;

        $this->find[ ]      = '{WEEKLY/MONTHLY}';
        $this->replace[ ]   = isset($report_data['period']) ? $report_data['period'] : '';

        $this->find[ ]      = '{STORE_NAME}';
        $this->replace[ ]   = $vendor->page_title;
        // Set email attachments
        if(is_array($attachments) && count($attachments) > 0){
            $this->attachments = $attachments;
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
        return apply_filters( 'mvx_vendor_orders_stats_report_email_subject', __( '{STORE_NAME}, your orders report', 'multivendorx'), $this->object );
    }

    /**
     * Get email heading.
     *
     * @access  public
     * @return string
     */
    public function get_default_heading() {
        return apply_filters( 'mvx_vendor_orders_stats_report_email_heading', __( '{STORE_NAME} orders report', 'multivendorx'),$this->object );
    }
    
    /**
     * Get email attachments.
     *
     * @return string
     */
    public function get_attachments() {
        return apply_filters( 'mvx_vendor_orders_stats_report_email_attachments', $this->attachments, $this->id, $this->object );
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
            'report_data'   => $this->report_data,
            'vendor'        =>  $this->vendor,
            'attachments'   =>  $this->attachments,
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
            'report_data'   => $this->report_data,
            'vendor'        =>  $this->vendor,
            'attachments'   =>  $this->attachments,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => true,
            'email'         => $this,
            ), 'MultiVendorX/', $this->template_base);
        return ob_get_clean();
    }
}

endif;
