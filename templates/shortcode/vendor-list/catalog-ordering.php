<?php
/**
 * Vendor List Map filters
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/shortcode/vendor-list/catalog-ordering.php
 *
 * @package MultiVendorX/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $MVX, $vendor_list;
?>
<div class="mvx-store-map-pagination">
    <p class="mvx-pagination-count mvx-pull-right">
        <?php
        mvx_vendor_list_paging_info();
        ?>
    </p>
    
    <?php mvx_vendor_list_form_wrapper(); ?>

    <?php mvx_vendor_list_order_sort(); ?>
        
    <?php mvx_vendor_list_form_wrapper_end(); ?>
</div>