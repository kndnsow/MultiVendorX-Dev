<div class="transaction-details">
	<div class="total-balance-wrap">
		<h4><?php _e('Withdrawable Balance', 'multivendorx');?></h4>
		<div class="mvx_dashboard_widget_total_transaction"><?php echo wc_price($total_amount);  ?></div>
	</div>
    <?php if($transaction_display_array) : ?>
	<ul class="transaction-list">
	<?php 
		foreach ($transaction_display_array as $key => $value) {
                    //print_r($value);
                    
			echo "<li><p>".$value['transaction_date']."<span class='order-id'><a href=" . esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_transaction_details_endpoint', 'seller_dashbaord', 'transaction-details'), $key)) . ">#".$key."</a></span></p><span class='pull-right'>".wc_price($value['total_amount'])."</span></li>";	
		}?>
	</ul>
    <?php endif; ?>
</div>
