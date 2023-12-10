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
extract( $vendor_list );
?>

<div class="vendor_sort">
    <select class="select short" id="vendor_sort_type" name="vendor_sort_type">
        <?php
        $vendor_sort_type = apply_filters('mvx_vendor_list_vendor_sort_type', array(
            'registered' => __('By date', 'multivendorx'),
            'name' => __('By Alphabetically', 'multivendorx'),
            'category' => __('By Category', 'multivendorx'),
            'shipping' => __('By Shipping', 'multivendorx')
        ));
        if ($vendor_sort_type && is_array($vendor_sort_type)) {
            foreach ($vendor_sort_type as $key => $label) {
                $selected = '';
                if (isset($request['vendor_sort_type']) && $request['vendor_sort_type'] == $key) {
                    $selected = 'selected="selected"';
                }
                echo '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
            }
        }
        ?>
    </select>
    <?php
    $product_category = get_terms('product_cat');
    $options_html = '';
    $sort_category = isset($request['vendor_sort_category']) ? $request['vendor_sort_category'] : '';
    foreach ($product_category as $category) {
        if ($category->term_id == $sort_category) {
            $options_html .= '<option value="' . esc_attr($category->term_id) . '" selected="selected">' . esc_html($category->name) . '</option>';
        } else {
            $options_html .= '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
        }
    }
    ?>
    <select name="vendor_country" id="vendor_country" class="country_to_state vendors_sort_shipping_fields form-control regular-select" rel="vendor_country">
        <option value=""><?php _e( 'Select a country&hellip;', 'multivendorx' ); ?></option>
        <?php $country_code = 0;
        foreach ( WC()->countries->get_allowed_countries() as $key => $value ) {
            echo '<option value="' . esc_attr( $key ) . '"' . selected( esc_attr( $country_code ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
        }
        ?>
    </select>
    <!-- Sort by Shipping -->
    <select name="vendor_state" id="vendor_state" class="state_select vendors_sort_shipping_fields form-control regular-select" rel="vendor_state">
        <option value=""><?php esc_html_e( 'Select a state&hellip;', 'multivendorx' ); ?></option>
    </select>
    <input class="vendors_sort_shipping_fields" type="text" placeholder="<?php _e('ZIP code', 'multivendorx'); ?>" name="vendor_postcode_list" value="<?php echo isset($request['vendor_postcode_list']) ? $request['vendor_postcode_list'] : ''; ?>">
    <select name="vendor_sort_category" id="vendor_sort_category" class="select"><?php echo $options_html; ?></select>
    <?php do_action( 'mvx_vendor_list_vendor_sort_extra_attributes', $request ); ?>
    <input value="<?php echo __('Sort', 'multivendorx'); ?>" type="submit">
</div>
