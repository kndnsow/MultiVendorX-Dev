<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/plain/admin-new-question.php
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
echo sprintf(  __( "Greetings Admin",  'multivendorx' ) ); 
echo '\n\n';
echo sprintf(  __( "A new query has added by your buyer - %s",  'multivendorx' ), $customer_name ); 
echo '\n';
echo sprintf(  __( "Query for : %s",  'multivendorx' ), $vendor->page_title );
echo '\n';
echo sprintf(  __( "Query : %s",  'multivendorx' ), $question );
echo '\n';
$question_link = apply_filters( 'admin_plain_question_redirect_link', admin_url( 'admin.php?page=mvx-to-do' ) ); 
echo sprintf(  __( "You can approve or reject query from here : %s",  'multivendorx' ), $question_link );
echo '\n\n';
echo sprintf( __( 'Note: Quick replies help to maintain a friendly customer-buyer relationship', 'multivendorx'));

echo apply_filters( 'mvx_email_footer_text', get_option( 'mvx_email_footer_text' ) );