<?php
/**
 * The template for displaying single product page vendor tab 
 *
 * Override this template by copying it to yourtheme/MultiVendorX/policies-tab.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.3.0
 */
global $product, $MVX, $post;
$policies = get_mvx_product_policies($product->get_id());
?>
<div class="mvx-product-policies">
    <?php if(isset($policies['shipping_policy']) && !empty($policies['shipping_policy'])){ ?>
    <div class="mvx-shipping-policies policy">
        <h2 class="mvx_policies_heading heading"><?php echo apply_filters('mvx_shipping_policies_heading', esc_html_e('Shipping Policy', 'multivendorx')); ?></h2>
        <div class="mvx_policies_description description" ><?php echo wp_kses_post($policies['shipping_policy']); ?></div>
    </div>
    <?php } if(isset($policies['refund_policy']) && !empty($policies['refund_policy'])){ ?>
    <div class="mvx-refund-policies policy">
        <h2 class="mvx_policies_heading heading heading"><?php echo apply_filters('mvx_refund_policies_heading', esc_html_e('Refund Policy', 'multivendorx')); ?></h2>
        <div class="mvx_policies_description description" ><?php echo wp_kses_post($policies['refund_policy']); ?></div>
    </div>
    <?php } if(isset($policies['cancellation_policy']) && !empty($policies['cancellation_policy'])){ ?>
    <div class="mvx-cancellation-policies policy">
        <h2 class="mvx_policies_heading heading"><?php echo apply_filters('mvx_cancellation_policies_heading', esc_html_e('Cancellation / Return / Exchange Policy', 'multivendorx')); ?></h2>
        <div class="mvx_policies_description description" ><?php echo wp_kses_post($policies['cancellation_policy']); ?></div>
    </div>
    <?php } ?>
</div>