<?php
/**
 * Vendor List Map filters
 *
 * This template can be overridden by copying it to yourtheme/MultiVendorX/shortcode/vendor-list/map-filters.php
 *
 * @package MultiVendorX/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $MVX, $vendor_list;
extract($vendor_list);
?>
<input type="hidden" id="mvx_vlist_center_lat" name="mvx_vlist_center_lat" value=""/>
<input type="hidden" id="mvx_vlist_center_lng" name="mvx_vlist_center_lng" value=""/>
<?php if (mvx_mapbox_api_enabled()) { ?>
    <div id="locationText"></div> 
<?php } ?>
<div class="mvx-store-map-filter">
    <?php if (!mvx_mapbox_api_enabled()) { ?>
        <div class="mvx-inp-wrap">
            <input type="text" name="locationText" id="locationText" placeholder="<?php esc_attr_e('Enter Address', 'multivendorx'); ?>" value="<?php echo isset($request['locationText']) ? $request['locationText'] : ''; ?>">
        </div>
    <?php } ?>
    <div class="mvx-inp-wrap">
        <select name="radiusSelect" id="radiusSelect">
            <option value=""><?php esc_html_e('Within', 'multivendorx'); ?></option>
            <?php if($radius) :
            $selected_radius = isset($request['radiusSelect']) ? $request['radiusSelect'] : '';
            foreach ($radius as $value) {
                echo '<option value="'.$value.'" '.selected( esc_attr( $selected_radius ), $value, false ).'>'.$value.'</option>';
            }
            endif;
            ?>
        </select>
    </div>
    <div class="mvx-inp-wrap">
        <select name="distanceSelect" id="distanceSelect">
            <?php $selected_distance = isset($request['distanceSelect']) ? $request['distanceSelect'] : ''; ?>
            <option value="M" <?php echo selected( $selected_distance, "M", false ); ?>><?php esc_html_e('Miles', 'multivendorx'); ?></option>
            <option value="K" <?php echo selected( $selected_distance, "K", false ); ?>><?php esc_html_e('Kilometers', 'multivendorx'); ?></option>
            <option value="N" <?php echo selected( $selected_distance, "N", false ); ?>><?php esc_html_e('Nautical miles', 'multivendorx'); ?></option>
            <?php do_action('mvx_vendor_list_sort_distanceSelect_extra_options'); ?>
        </select>
    </div>
    <?php do_action( 'mvx_vendor_list_vendor_sort_map_extra_filters', $request ); ?>
    <input type="submit" name="vendorListFilter" value="<?php esc_attr_e('Submit', 'multivendorx'); ?>">
</div>
