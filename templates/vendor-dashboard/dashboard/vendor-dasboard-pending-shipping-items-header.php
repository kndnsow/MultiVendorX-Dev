<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items-header.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $MVX;
?>
<tr>
	<td align="center" ><?php echo __('Product Name','multivendorx'); ?></td>
	<td  align="center" class="no_display" ><?php echo __('Order Date','multivendorx'); ?><br>
		<span style="font-size:12px;"><?php echo __('dd/mm','multivendorx'); ?></span></td>
	<td  align="center" class="no_display" ><?php echo __('L/B/H/W','multivendorx'); ?></td>
	<td align="left" ><?php echo __('Address','multivendorx'); ?></td>
	<td align="center" class="no_display" ><?php echo __('Charges','multivendorx'); ?></td>
</tr>
