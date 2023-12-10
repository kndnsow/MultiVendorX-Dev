<?php
/*
 * The template for displaying vendor add product
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/product-manager/add-product.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   3.3.0
 */
global $MVX, $wc_product_attributes;

$current_vendor_id = apply_filters('mvx_current_loggedin_vendor_id', get_current_user_id());

// If vendor does not have product submission cap then show message
if (is_user_logged_in() && is_user_mvx_vendor($current_vendor_id) && !current_user_can('edit_products')) {
    ?>
    <div class="col-md-12">
        <div class="panel panel-default">
            <?php _e('You do not have enough permission to submit a new product. Please contact site administrator.', 'multivendorx'); ?>
        </div>
    </div>
    <?php
    return;
}

?>
<div class="col-md-12 add-product-outer-wrapper">
    <div class="select-product-cat-wrapper">
        <?php $is_new_listing = isset($_REQUEST['new_listing']) ? true : false;
        $is_cats_hier = isset($_REQUEST['cats_hier']) ? true : false;
        if( ( $is_new_listing && $is_cats_hier ) || mvx_is_module_active('spmv') == false && get_mvx_vendor_settings('is_singleproductmultiseller', 'spmv_pages') == false ) {
        ?>
        <!-- New product list categories hierarchically -->
        <div class="select-cat-step-wrapper">
            <div class="cat-step1" >
                <div class="panel panel-default pannel-outer-heading mt-0">
                    <div class="panel-heading d-flex">
                        <h1><span class="primary-color"><span><?php _e( 'Step 1 of', 'multivendorx' );?></span> <?php _e( '2:', 'multivendorx' );?></span> <?php _e('Select a product category', 'multivendorx'); ?></h1>
                        <h3><?php _e('Once a category is assigned to a product, it cannot be altered.', 'multivendorx'); ?></h3>
                    </div>
                    <div class="panel-body panel-content-padding form-horizontal breadcrumb-panel">
                        <div class="product-search-wrapper categories-search-wrapper">
                            <div class="form-text"><?php _e('Search category', 'multivendorx'); ?></div>
                            <div class="form-input">
                                <input id="search-categories-keyword" type="text" placeholder="<?php esc_attr_e('Example: tshirt, music, album etc...', 'multivendorx'); ?>">
                                <ul id="searched-categories-results" class="list-group">
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default pannel-outer-heading mvx-categories-level-panel has-scroller"> 
                        <div class="cat-column-scroller cat-left-scroller"><i class="mvx-font ico-left-arrow-icon"></i></div>
                    <div class="form-horizontal cat-list-holder">
                        <div class="mvx-product-categories-wrap cat-column-wrapper">
                            <div class="mvx-product-cat-level 1-level-cat cat-column" data-level="1"  data-mcs-theme="dark">
                                <ul class="mvx-product-categories 1-level" data-cat-level="1">
                                    <?php echo mvx_list_categories( apply_filters( 'mvx_vendor_product_classify_first_level_categories', array(
                                    'taxonomy' => 'product_cat', 
                                    'hide_empty' => false, 
                                    'html_list' => true,
                                    'cat_link'  => 'javascript:void(0)',
                                    ) ) ); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                        <div class="cat-column-scroller cat-right-scroller"><i class="mvx-font ico-right-arrow-icon"></i></div>
                </div>
            </div>
        </div>
        <?php }else{ ?>
        <!-- List a product by name or gtin -->
        <div class="cat-intro">
            <div class="panel panel-default pannel-outer-heading mt-0"> 
                <div class="panel-body panel-content-padding form-horizontal text-center">
                    <img src="<?php echo $MVX->plugin_url.'assets/images/add-product-graphic.png'; ?>" alt="">
                    <h1 class="heading-underline"><?php _e('List a New Product', 'multivendorx'); ?></h1>
                    <div class="serach-product-cat-wrapper">
                        <h2><?php _e('Search from our existing Product Catalog', 'multivendorx'); ?></h2>
                        <form class="search-pro-by-name-gtin">
                            <input type="text" placeholder="<?php esc_attr_e('Product name, UPC, ISBN ...', 'multivendorx'); ?>" class="form-control inline-input search-product-name-gtin-keyword" required>
                            <button type="button" class="btn btn-default search-product-name-gtin-btn"><?php echo strtoupper(__('Search', 'multivendorx')); ?></button> 
                        </form>
                        <br>
                        <button class="btn btn-default view-all-products-btn"><?php esc_html_e('View All Products', 'multivendorx'); ?></button>
                        <?php 

                         if (get_option('permalink_structure')) {
                            $category_url = '?new_listing=1&cats_hier=1';
                        } else {
                            $category_url = mvx_get_vendor_dashboard_endpoint_url( get_mvx_vendor_settings( 'mvx_add_product_endpoint', 'seller_dashbaord', 'add-product' ) ) . '&new_listing=1&cats_hier=1';
                        }

                        
                        $url = ( get_mvx_vendor_settings('category_pyramid_guide', 'settings_general') == false ) ? esc_url(mvx_get_vendor_dashboard_endpoint_url(get_mvx_vendor_settings('mvx_edit_product_endpoint', 'seller_dashbaord', 'edit-product'))) : $category_url; ?>
                        <p><?php _e('Not in the catalog?', 'multivendorx'); ?> <a href="<?php echo $url; ?>" class="cat-step-btn"><?php _e('Create a new product', 'multivendorx'); ?> <i class="mvx-font ico-right-arrow-icon"></i></a></p>
                    </div>
                </div>
            </div>
            <div class="panel panel-custom mt-15 product-search-panel searched-products-name-gtin-panel">
                <div class="panel-heading d-flex"><?php _e('Your search results:', 'multivendorx'); ?></div>
                <div class="panel-body search-result-holder p-0 searched-result-products-name-gtin"></div>
            </div>
            <div id="result-view-all-products-name"></div>          
        </div>
        <!-- End List a product by name or gtin -->
        <?php } ?>
        <div class="clearfix"></div>
    </div>
</div>
<?php
do_action('mvx_frontend_product_manager_template');