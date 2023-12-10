<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-new-question.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $MVX;
$text_align = is_rtl() ? 'right' : 'left';
$question = isset( $question ) ? $question : '';
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p style="text-align:<?php echo $text_align; ?>;" >
	<?php printf( esc_html__( "Hi %s,",  'multivendorx' ), $vendor->page_title ); ?><br><br>
	<?php printf( esc_html__( "A new query has been added by your potential buyer - %s",  'multivendorx' ), $customer_name ); ?><br>
	<?php printf( esc_html__( "Product Name : %s",  'multivendorx' ), $product_name ); ?><br>
	<?php printf( esc_html__( "Query : %s",  'multivendorx' ), $question ); ?><br>
    <?php 
    	$question_link = apply_filters( 'mvx_vendor_question_redirect_link', esc_url( mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_products_qna_endpoint', 'seller_dashbaord', 'products-qna'))) ); 
        printf( esc_html__( "You can approve or reject query from here : %s",  'multivendorx' ), $question_link ); ?><br><br>

        <?php printf( esc_html__( 'Note: Quick replies help to maintain a friendly customer-buyer relationship', 'multivendorx' )); ?>
</p>

<?php do_action( 'mvx_email_footer' ); ?>


