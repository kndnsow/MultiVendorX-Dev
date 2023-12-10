<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/vendor-new-question.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */
 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;
$question = isset( $question ) ? $question : '';
echo $email_heading . "\n\n"; 
echo sprintf(  __( "Hi %s,",  'multivendorx' ), $vendor->page_title ); 
echo '\n\n';
echo sprintf(  __( "A new query has been added by your potential buyer - %s",  'multivendorx' ), $customer_name ); 
echo '\n';
echo sprintf(  __( "Product name : %s",  'multivendorx' ), $product_name );
echo '\n';
echo sprintf(  __( "Query : %s",  'multivendorx' ), $question );
echo '\n';
$question_link = apply_filters( 'mvx_vendor_plain_question_redirect_link', esc_url( mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_products_qna_endpoint', 'seller_dashbaord', 'products-qna'))) ); 
echo sprintf(  __( "You can approve or reject query from here : %s",  'multivendorx' ), $question_link );
echo '\n\n';
echo sprintf( __( 'Note: Quick replies help to maintain a friendly customer-buyer relationship', 'multivendorx'));

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );