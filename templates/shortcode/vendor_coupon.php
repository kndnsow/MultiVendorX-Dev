<?php
/**
 * The template for displaying vendor coupon
 *
 * Override this template by copying it to yourtheme/MultiVendorX/shortcode/vendor_coupon.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $MVX;
$user = wp_get_current_user();
$vendor = get_mvx_vendor($user->ID);
if($vendor) {
	echo  '<h3>'.__('Coupons', 'multivendorx').'</h3>';
	if($MVX->vendor_caps->vendor_capabilities_settings('is_submit_coupon')) { 
		if($coupons) {?> 
			<table>
				<tbody>
				<th><?php _e('Coupon Code', 'multivendorx' ) ?></th>
				<th><?php _e('Usage Count', 'multivendorx' ) ?></th>
				<?php
				foreach($coupons as $coupon) {
					$usage_count = get_post_meta($coupon, 'usage_count', true);
					if(!$usage_count) $usage_count = 0;
					$coupon_post = get_post($coupon);
					echo '<tr>';
					echo '<td>'.$coupon_post->post_title.'</td>';
					echo '<td>'.$usage_count.'</td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
			<p><?php echo  __('Submit another coupon by', 'multivendorx').'  <a class="shop_url button button-primary" target="_blank" href='.admin_url( 'edit.php?post_type=shop_coupon' ).'><strong>'.__('Submit Coupons', 'multivendorx').'</strong></a></p>' ?>
		<?php		
		} else {
			echo __('Sorry! You have not created any coupon till now.You can create your product specific coupon from -', 'multivendorx').'<a class="shop_url button button-primary" target="_blank" href='.admin_url( 'edit.php?post_type=shop_coupon' ).'><strong>'.__('Submit Coupons', 'multivendorx').'</strong></a>';
		}
	} else {
		echo __('Sorry ! You do not have the capability to add coupons.', 'multivendorx');
	}
}
?>