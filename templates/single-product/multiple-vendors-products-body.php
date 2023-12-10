<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/single-product/multiple-vendors-products-body.php.
 *
 * HOWEVER, on occasion MVX will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * 
 * @author 		MultiVendorX
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 2.3.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $MVX, $product;
$single_pro = $product;
if(isset($more_product_array) && is_array($more_product_array) && count($more_product_array) > 0) {
	if(isset($sorting) && !empty($sorting)) {	
	
		if($sorting == 'price') {		
			usort($more_product_array, function($a, $b){return $a['price_val'] - $b['price_val'];});
		}
		elseif($sorting == 'price_high') {		
			usort($more_product_array, function($a, $b){return $a['price_val'] - $b['price_val'];});
			$more_product_array = array_reverse (  $more_product_array);
		}
		elseif($sorting == 'rating') {
			$more_product_array = mvx_sort_by_rating_multiple_product($more_product_array);			
		}
		elseif($sorting == 'rating_low') {
			$more_product_array = mvx_sort_by_rating_multiple_product($more_product_array);
			$more_product_array = array_reverse (  $more_product_array);			
		}
	}
	foreach ($more_product_array as $more_product ) {	
            $_product = wc_get_product($more_product['product_id']);
		?>
		<div class="row rowbody">						
			<div class="rowsub ">
				<div class="vendor_name">
					<a href="<?php echo esc_url($more_product['shop_link']); ?>" class="mvx_seller_name"><?php echo esc_html($more_product['seller_name']); ?></a>
					<?php do_action('mvx_after_singleproductmultivendor_vendor_name', $more_product['product_id'], $more_product);?>
				</div>
				<?php 
				if(isset($more_product['rating_data']) && is_array($more_product['rating_data']) && isset($more_product['rating_data']['avg_rating']) && $more_product['rating_data']['avg_rating']!=0 && $more_product['rating_data']['avg_rating']!=''){ 
					echo wp_kses_post(wc_get_rating_html( $more_product['rating_data']['avg_rating'] ));	
				}else {
					echo "<div class='star-rating'></div>";
				}
				?>
			</div>
			<div class="rowsub">
                <?php echo $_product->get_price_html(); ?>
			</div>
			<div class="rowsub">
				<?php 
                                // reset global $product variable
                                global $product;
                                $product = $_product;
                                woocommerce_template_loop_add_to_cart( array(
                                    'quantity' => 1,
                                ) );
                                ?>
				<a href="<?php echo esc_url(get_permalink($more_product['product_id'])); ?>" class="buttongap button" ><?php echo esc_html_e('Details','multivendorx'); ?></a>
			</div>
			<div style="clear:both;"></div>							
		</div>
		
		
	<?php
	}
        // reset again with global product
        global $product;
        $product = $single_pro;
}
?>