<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/single-product/multiple-vendors-products.php.
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
if (!defined('ABSPATH')) {
    exit;
}
global $MVX, $post, $wpdb;
if (count($more_product_array) > 0) {
    $i = 0;
    ?>
    <div class="ajax_loader_class_msg"><img src="<?php echo $MVX->plugin_url ?>assets/images/ajax-loader.gif" alt="ajax-loader" /></div>
    <div class="container">		
        <div class="row rowhead">
            <div class="rowsub "><?php echo esc_html_e('Vendor', 'multivendorx'); ?></div>
            <div class="rowsub"><?php echo esc_html_e('Price', 'multivendorx'); ?></div>
            <div class="rowsub">
                <select name="mvx_multiple_product_sorting" id="mvx_multiple_product_sorting" class="mvx_multiple_product_sorting" attrid="<?php echo $post->ID; ?>" >
                    <option value="price"><?php echo esc_html_e('Price Low To High', 'multivendorx'); ?></option>
                    <option value="price_high"><?php echo esc_html_e('Price High To Low', 'multivendorx'); ?></option>
                    <option value="rating"><?php echo esc_html_e('Rating High To Low', 'multivendorx'); ?></option>
                    <option value="rating_low"><?php echo esc_html_e('Rating Low To High', 'multivendorx'); ?></option>
                </select>
            </div>
            <div style="clear:both;"></div>
        </div>			
        <?php
        $MVX->template->get_template('single-product/multiple-vendors-products-body.php', array('more_product_array' => $more_product_array, 'sorting' => 'price'));
        ?>		
    </div>		
    <?php
} else {
    ?>
    <div class="container">
        <div class="row">
    <?php echo esc_html_e('Sorry no more offers available', 'multivendorx'); ?>
        </div>
    </div>	
<?php }
?>

