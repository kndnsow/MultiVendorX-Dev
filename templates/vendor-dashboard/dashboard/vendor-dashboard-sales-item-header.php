<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/dashboard/vendor-dashboard-sales-item-header.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.3.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $MVX;
?>
<tr>	
    <td align="center" >ID</td>
    <td align="center" ><?php _e('SKU', 'multivendorx'); ?></td>
    <td class="no_display"  align="center" ><?php _e('Sales', 'multivendorx'); ?></td>
    <td class="no_display" align="center" ><?php _e('Discount', 'multivendorx'); ?></td>
    <td align="center" ><?php _e('My Earnings', 'multivendorx'); ?></td>
</tr>