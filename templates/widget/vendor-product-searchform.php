<?php
/**
 * Vendor Product search form.
 *
 * Override this template by copying it to yourtheme/MultiVendorX/widget/vendor-product-searchform.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version     3.5.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form role="search" method="get" class="mvx-vproduct-search woocommerce-product-search" action="">
	<label class="screen-reader-text" for="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>"><?php esc_html_e( 'Search for:', 'multivendorx' ); ?></label>
	<input type="search" id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="search-field" placeholder="<?php echo esc_attr__( 'Search products&hellip;', 'multivendorx' ); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" />
	<button type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'multivendorx' ); ?>"><?php echo esc_html_x( 'Search', 'submit button', 'multivendorx' ); ?></button>
	<input type="hidden" name="post_type" value="product" />
</form>