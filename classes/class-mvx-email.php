<?php
/**
 * MVX Email Class
 *
 * @version		2.2.0
 * @package		MultivendorX
 * @author 		MultiVendorX
 */
 
class MVX_Email {
	
    public function __construct() {		
        global $MVX;
        // Intialize MVX Emails
        add_filter('woocommerce_email_classes', array($this, 'mvx_email_classes'));
        add_action( 'woocommerce_email_customer_details', array( $this, 'mvx_vendor_messages_customer_support' ), 30, 3 );	
        // Intialize MVX Email Footer text settings
        add_filter('woocommerce_get_settings_email', array($this, 'mvx_settings_email'));
        // MVX Email Footer hook
        add_action( 'mvx_email_footer', array( $this, 'mvx_email_footer' ) );
    }
    
    /**
     * Register MVX emails class
     *
     * @access public
     * @return array
     */
    function mvx_email_classes($emails) {
        include( 'emails/class-mvx-email-vendor-new-account.php' );
        include( 'emails/class-mvx-email-admin-new-vendor-account.php' );
        include( 'emails/class-mvx-email-approved-vendor-new-account.php' );
        include( 'emails/class-mvx-email-rejected-vendor-new-account.php' );
        include( 'emails/class-mvx-email-vendor-new-order.php' );
        include( 'emails/class-mvx-email-vendor-notify-shipped.php' );
        include( 'emails/class-mvx-email-vendor-new-product-added.php' );
        include( 'emails/class-mvx-email-vendor-new-question.php' );
        include( 'emails/class-mvx-email-admin-new-question.php' );
        include( 'emails/class-mvx-email-customer-answer.php' );
        include( 'emails/class-mvx-email-admin-added-new-product-to-vendor.php' );
        include( 'emails/class-mvx-email-vendor-new-commission-transaction.php' );
        include( 'emails/class-mvx-email-vendor-direct-bank.php' );
        include( 'emails/class-mvx-email-admin-withdrawal-request.php' );
        include( 'emails/class-mvx-email-vendor-orders-stats-report.php' );
        include( 'emails/class-mvx-email-vendor-contact-widget.php' );
		include( 'emails/class-mvx-email-send-report-abuse.php' );
		include( 'emails/class-mvx-email-vendor-new-announcement.php' );
		include( 'emails/class-mvx-email-customer-order-refund-request.php' );
		include( 'emails/class-mvx-email-vendor-product-rejected.php' );
		include( 'emails/class-mvx-email-suspend-vendor-account.php' );
		include( 'emails/class-mvx-email-vendor-review.php' );
		include( 'emails/class-mvx-email-vendor-followed.php' );
		include( 'emails/class-mvx-email-admin-change-order-status.php' );
		include( 'emails/class-mvx-email-vendor-new-coupon-added.php' );
		include( 'emails/class-mvx-email-vendor-new-coupon-followed-customer.php' );
		include( 'emails/class-mvx-email-vendor-bank-transfer-transaction.php' );
		include( 'emails/class-mvx-email-plugin-deactivated.php' );
		include( 'emails/class-mvx-email-send-site-information.php' );
		include( 'emails/class-mvx-email-admin-vendor-account-deactivation-request.php' );
		include( 'emails/class-mvx-email-vendor-delete-profile.php' );
		include( 'emails/class-mvx-email-vendor-reject-profile.php' );

        $mvx_email = array();
        $mvx_email['WC_Email_Vendor_New_Account'] = new WC_Email_Vendor_New_Account();
        $mvx_email['WC_Email_Admin_New_Vendor_Account'] = new WC_Email_Admin_New_Vendor_Account();
        $mvx_email['WC_Email_Approved_New_Vendor_Account'] = new WC_Email_Approved_New_Vendor_Account();
        $mvx_email['WC_Email_Rejected_New_Vendor_Account'] = new WC_Email_Rejected_New_Vendor_Account();
        $mvx_email['WC_Email_Vendor_New_Order'] = new WC_Email_Vendor_New_Order();
        $mvx_email['WC_Email_Notify_Shipped'] = new WC_Email_Notify_Shipped();
        $mvx_email['WC_Email_Vendor_New_Product_Added'] = new WC_Email_Vendor_New_Product_Added();
        $mvx_email['WC_Email_Vendor_New_Question'] = new WC_Email_Vendor_New_Question();
        $mvx_email['WC_Email_Admin_New_Question'] = new WC_Email_Admin_New_Question();
        $mvx_email['WC_Email_Customer_Answer'] = new WC_Email_Customer_Answer();
        $mvx_email['WC_Email_Admin_Added_New_Product_to_Vendor'] = new WC_Email_Admin_Added_New_Product_to_Vendor();
        $mvx_email['WC_Email_Vendor_Commission_Transactions'] = new WC_Email_Vendor_Commission_Transactions();
        $mvx_email['WC_Email_Vendor_Direct_Bank'] = new WC_Email_Vendor_Direct_Bank();
        $mvx_email['WC_Email_Admin_Widthdrawal_Request'] = new WC_Email_Admin_Widthdrawal_Request();
        $mvx_email['WC_Email_Vendor_Orders_Stats_Report'] = new WC_Email_Vendor_Orders_Stats_Report();
        $mvx_email['WC_Email_Vendor_Contact_Widget'] = new WC_Email_Vendor_Contact_Widget();
		$mvx_email['WC_Email_Send_Report_Abuse'] = new WC_Email_Send_Report_Abuse();
		$mvx_email['WC_Email_Vendor_New_Announcement'] = new WC_Email_Vendor_New_Announcement();
		$mvx_email['WC_Email_Customer_Refund_Request'] = new WC_Email_Customer_Refund_Request();
		$mvx_email['WC_Email_Vendor_Product_Rejected'] = new WC_Email_Vendor_Product_Rejected();
		$mvx_email['WC_Email_Suspend_Vendor_Account'] = new WC_Email_Suspend_Vendor_Account();
		$mvx_email['WC_Email_Vendor_Review'] = new WC_Email_Vendor_Review();
		$mvx_email['WC_Email_Vendor_Followed'] = new WC_Email_Vendor_Followed();
		$mvx_email['WC_Email_Admin_Change_Order_Status'] = new WC_Email_Admin_Change_Order_Status();
		$mvx_email['WC_Email_Vendor_New_Coupon_Added'] = new WC_Email_Vendor_New_Coupon_Added();
		$mvx_email['WC_Email_Vendor_New_Coupon_Added_To_Customer'] = new WC_Email_Vendor_New_Coupon_Added_To_Customer();
		$mvx_email['WC_Email_Vendor_DirectBank_Commission_Transactions'] = new WC_Email_Vendor_DirectBank_Commission_Transactions();
		$mvx_email['WC_Email_Plugin_Deactivated_Mail'] = new WC_Email_Plugin_Deactivated_Mail();
		$mvx_email['WC_Email_Send_Site_Information'] = new WC_Email_Send_Site_Information();
		$mvx_email['WC_Email_Admin_Vendor_Account_Deactivation_Request_Mail'] = new WC_Email_Admin_Vendor_Account_Deactivation_Request_Mail();
		$mvx_email['WC_Email_Vendor_Account_Deletion_Mail'] = new WC_Email_Vendor_Account_Deletion_Mail();
		$mvx_email['WC_Email_Vendor_Account_Deactive_Request_Reject_Mail'] = new WC_Email_Vendor_Account_Deactive_Request_Reject_Mail();

        return array_merge( $emails, apply_filters( 'mvx_email_classes', $mvx_email ) );
    }

    /**
     * Register MVX emails footer text settings
     *
     * @access public
     * @return array
     */
    public function mvx_settings_email($settings) {
    	global $MVX;
        if (!isset($_GET['section'])) {
	        $mvx_footer_settings = array(
		        array(
		            'title'       => __( 'MVX Footer text', 'multivendorx' ),
		            'desc'        => __( 'The text to appear in the footer of MVX emails.', 'multivendorx' ),
		            'id'          => 'mvx_email_footer_text',
		            'css'         => 'width:300px; height: 75px;',
		            'placeholder' => __( 'N/A', 'multivendorx' ),
		            'type'        => 'textarea',
		            /* translators: %s: site name */
		            'default'     => sprintf( __( '%s - Powered by Multivendor X', 'multivendorx' ), get_bloginfo( 'name', 'display' ) ),
		            'autoload'    => false,
		            'desc_tip'    => true,
		        )
	        );
	        array_splice($settings, 11, 0, $mvx_footer_settings);
	    }
        return $settings;
    }

    /**
	 * Get the MVX email footer.
	 */
	public function mvx_email_footer() {
		global $MVX;
		$MVX->template->get_template('emails/email-footer.php');
	}
	
	public function mvx_vendor_messages_customer_support( $order, $sent_to_admin = false, $plain_text = false ) {
		global $MVX;
		$MVX->load_class( 'template' );
		$MVX->template = new MVX_Template();
		$items = $order->get_items( 'line_item' );
		$vendor_array = array();
		$author_id = '';
		$customer_support_details_settings = mvx_get_option('mvx_general_customer_support_details_settings_name');
		$is_csd_by_admin = '';
		
		foreach( $items as $item_id => $item ) {			
			$product_id = wc_get_order_item_meta( $item_id, '_product_id', true );
			if( $product_id ) {				
				$author_id = wc_get_order_item_meta( $item_id, '_vendor_id', true );
				if( empty($author_id) ) {
					$product_vendors = get_mvx_product_vendors($product_id);
					if(isset($product_vendors) && (!empty($product_vendors))) {
						$author_id = $product_vendors->id;
					}
					else {
						$author_id = get_post_field('post_author', $product_id);
					}
				}
				if(isset($vendor_array[$author_id])){
					$vendor_array[$author_id] = $vendor_array[$author_id].','.$item['name'];
				}
				else {
					$vendor_array[$author_id] = $item['name'];
				}								
			}						
		}		
		if($plain_text) {
			
		} else {	
			if(apply_filters('can_vendor_add_message_on_email_and_thankyou_page', true) ) {
				$MVX->template->get_template( 'vendor-message-to-buyer.php', array( 'vendor_array'=>$vendor_array, 'capability_settings'=>$customer_support_details_settings, 'customer_support_details_settings'=>$customer_support_details_settings ));
			} elseif (get_mvx_vendor_settings('is_customer_support_details', 'settings_general')) {
				$MVX->template->get_template( 'customer-support-details-to-buyer.php', array( 'vendor_array'=>$vendor_array, 'capability_settings'=>$customer_support_details_settings, 'customer_support_details_settings'=>$customer_support_details_settings ));
			}
		}		
	}
	
	public function get_custom_support_message_by_vendor_id($vendor_id, $products) {
		global $MVX;
		$html = '';
		$user_meta = get_user_meta( $vendor_id );
		$capability_settings = mvx_get_option('mvx_general_customer_support_details_settings_name');
		ob_start();
		echo '<td valign="top" align="left" style=" background:#f4f4f4; padding:0px 40px"><h3 style="color:#557da1;display:block;font-family:Arial,sans-serif; font-size:16px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left">';
		echo __('Customer Support Details of : ','multivendorx');
		echo '<span style="color:#555;">';
		echo $products;
		echo '</span>';
		echo '<table style="width:100%;vertical-align:top;color:#a4a4a4; padding:10px 0 20px 0" border="0" cellpadding="2" cellspacing="0" >';
		echo '<tr>';
		echo '<td valign="top" align="left" >';
		echo __('Email : ','multivendorx'); 
		echo '</td>';
		echo '<td valign="top" align="left" >: <a style="color:#505050;" href="mailto:'.$user_meta['_vendor_customer_email'][0].'" target="_blank">';
    echo  $user_meta['_vendor_customer_email'][0];
		echo '</a></td>';
		echo '</tr>';		
		echo '<tr><td valign="top" align="left" >';
		echo  __('Phone : ','multivendorx'); 
		echo '</td><td valign="top" align="left" >:';
		echo $user_meta['_vendor_customer_phone'][0];
		echo '</td></tr>';		
		echo '<tr><td valign="top" align="left" >';
		echo __('Return Address of : ','multivendorx');
		echo '</td><td valign="top" align="left" >: <b>';
		echo  $products;
		echo '</b></td></tr>';		
		echo '<tr><td valign="top" align="left" >';
		echo  __('Address Line 1 : ','multivendorx'); 
		echo '</td><td valign="top" align="left" >:';
		echo $user_meta['_vendor_csd_return_address1'][0];
		echo '</td></tr>';
    echo '<tr><td valign="top" align="left" >';
    echo  __('Address Line 2 : ','multivendorx');
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_address2'][0];
    echo '</td></tr>'; 
    echo '<tr><td valign="top" align="left" >';
    echo  __('State : ','multivendorx'); 
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_state'][0];
    echo '</td></tr>'; 
    echo '<tr><td valign="top" align="left" >';
    echo  __('City : ','multivendorx');
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_city'][0];
    echo '</td></tr>'; 
    echo '<tr><td valign="top" align="left" >';
    echo  __('Country : ','multivendorx');  
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_country'][0];
    echo '</td></tr>'; 
    echo '<tr><td valign="top" align="left" >';
    echo  __('Zip Code : ','multivendorx');
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_zip'][0];
    echo '</td></tr>';
		echo '</table></td>'; 	
		$html = ob_get_clean();		
		return $html;
		
	}
	
	public function get_csd_admin_address() {
		global $MVX;
		$html = '';
		$capability_settings = mvx_get_option('mvx_general_customer_support_details_settings_name');		
		ob_start();
		?>
		<table>
			<tr>
				<th colspan="2">
				<?php echo __('Customer Support Details :','multivendorx'); ?>
				</th>				
			</tr>
			<?php if(isset($capability_settings['csd_email'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Email : ','multivendorx'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_email']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_phone'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Phone : ','multivendorx'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_phone']; ?>
				</td>
			</tr>
			<?php }?>
			<tr>
				<th colspan="2">
				<?php echo __('Our Return Address :','multivendorx'); ?>
				</th>				
			</tr>
			
			<?php if(isset($capability_settings['csd_return_address_1'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Address Line 1 : ','multivendorx'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_address_1']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_address_2'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Address Line 2 : ','multivendorx'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_address_2']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_state'])) { ?>
			<tr>
				<td>
					<b><?php echo __('State : ','multivendorx'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_state']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_city'])) { ?>
			<tr>
				<td>
					<b><?php echo __('City : ','multivendorx'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_city']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_country'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Country : ','multivendorx'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_country']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_zipcode'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Zip Code : ','multivendorx'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_zipcode']; ?>
				</td>
			</tr>
			<?php }?>
		</table>				
		<?php	
		$html = ob_get_clean();
		return $html;		
	}
	
	
	
}


