<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/emails/vendor-direct-bank.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $MVX;


do_action( 'woocommerce_email_header', $email_heading, $email );
$amount = floatval(get_post_meta($transaction_id, 'amount', true)) - floatval(get_post_meta($transaction_id, 'transfer_charge', true)) - floatval(get_post_meta($transaction_id, 'gateway_charge', true));
?>
		
<p><?php echo apply_filters( 'mvx_thankyou_transaction_received_text', sprintf(__( 'Hello,<br>We have received a new withdrawal request for %s from you and your request is being processed.The order details are as follows:', 'multivendorx'), get_woocommerce_currency().$amount), $transaction_id ); ?></p>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;"  border="1" bordercolor="#eee">
	<thead>
		<?php $commission_details  = $MVX->transaction->get_transaction_item_details($transaction_id); 
		?>
		<tr>
			<?php
			if(!empty($commission_details['header'])) { ?>
				<tr>
					<?php
						foreach ( $commission_details['header'] as $header_val ) { ?>
							<th style="text-align:left;" class="td" scope="col"><?php echo $header_val; ?></th><?php
						}
					?>
				</tr>	<?php
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
			if(!empty($commission_details['body'])) {
				foreach ( $commission_details['body'] as $commission_detail ) {	?>
					<tr>
						<?php
							foreach($commission_detail as $details) {
								foreach($details as $detail_key => $detail) {
									?>
									<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;" class="td" scope="col"><?php echo $detail; ?></td><?php
								}
							}
						?>
					</tr><?php
				}
			}
			if ( $totals =  $MVX->transaction->get_transaction_item_totals($transaction_id, $vendor) ) {
				foreach ( $totals as $total ) {
					?><tr>
						<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"  class="td" scope="col" colspan="2" ><?php echo $total['label']; ?></td>
						<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;" class="td" scope="col" ><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}
		?>
	</tbody>
</table>
<?php do_action( 'mvx_email_footer' ); ?>