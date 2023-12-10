<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/vendor-shipping.php
 *
 * @author 		MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $woocommerce, $MVX, $wpdb;

$vendor = get_mvx_vendor(get_current_user_id());
$vendor_all_shipping_zones = mvx_get_shipping_zone();
$vendor_shipping_data = get_user_meta($vendor->id, 'vendor_shipping_data', true);
?>
<div class="col-md-12">
    <form name="vendor_shipping_form" class="mvx_shipping_form form-horizontal" method="post">
        <?php mvx_vendor_different_type_shipping_options($vendor->id); ?>
        <div id="mvx-vendor-shipping-by-distance-section">
            <?php mvx_vendor_distance_by_shipping_settings($vendor->id); ?>
        </div>
        <div id="mvx-vendor-shipping-by-country-section">
            <?php mvx_vendor_shipping_by_country_settings($vendor->id); ?>
        </div>
        <div id="mvx-vendor-shipping-by-zone-section">
        <div class="panel panel-default panel-pading pannel-outer-heading">
            <div class="panel-heading d-flex">
                <h3><?php esc_html_e('Shipping zones', 'multivendorx'); ?></h3>
            </div>
            <div class="panel-body">
                <div id="mvx_settings_form_shipping_by_zone" class="mvx-content shipping_type by_zone hide_if_shipping_disabled">
                    <table class="table mvx-table shipping-zone-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Zone name', 'multivendorx'); ?></th> 
                                <th><?php esc_html_e('Region(s)', 'multivendorx'); ?></th> 
                                <th><?php esc_html_e('Shipping method(s)', 'multivendorx'); ?></th>
                                <th><?php esc_html_e('Actions', 'multivendorx'); ?></th>
                            </tr>
                        </thead> 
                        <tbody>
                            <?php
                            if (!empty($vendor_all_shipping_zones)) {
                                foreach ($vendor_all_shipping_zones as $key => $vendor_shipping_zones) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="JavaScript:void(0);" data-zone-id="<?php echo esc_attr($vendor_shipping_zones['zone_id']); ?>" class="vendor_edit_zone modify-shipping-methods"><?php echo esc_html($vendor_shipping_zones['zone_name']); ?></a> 
                                        </td> 
                                        <td><?php echo esc_html($vendor_shipping_zones['formatted_zone_location']); ?></td> 
                                        <td>
                                            <div class="mvx-shipping-zone-methods">
                                                <?php
                                                $vendor_shipping_methods = $vendor_shipping_zones['shipping_methods'];
                                                $vendor_shipping_methods_titles = array();
                                                if ($vendor_shipping_methods) :
                                                    foreach ($vendor_shipping_methods as $key => $shipping_method) {
                                                        $class_name = 'yes' === $shipping_method['enabled'] ? 'method_enabled' : 'method_disabled';
                                                        $vendor_shipping_methods_titles[] = "<span class='mvx-shipping-zone-method $class_name'>" . $shipping_method['title'] . "</span>";
                                                    }
                                                endif;
                                                //$vendor_shipping_methods_titles = array_column($vendor_shipping_methods, 'title');
                                                $vendor_shipping_methods_titles = implode(', ', $vendor_shipping_methods_titles);

                                                if (empty($vendor_shipping_methods)) {
                                                    ?>
                                                    <span><?php esc_html_e('No shipping methods offered to this zone.', 'multivendorx'); ?> </span>
                                                <?php } else { ?>
                                                    <?php echo wp_kses_post($vendor_shipping_methods_titles); ?>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-actions">
                                                <span class="view">
                                                    <a href="JavaScript:void(0);" data-zone-id="<?php echo esc_attr($vendor_shipping_zones['zone_id']); ?>" class="vendor_edit_zone modify-shipping-methods" title="<?php esc_html_e('View', 'multivendorx') ?>"><i class="mvx-font ico-eye-icon"></i></a>
                                                </span> 
                                            </div>
                                            <div class="row-actions">
                                            </div>
                                        </td>
                                    </tr>
    <?php }
} else {
    ?>
                                <tr>
                                    <td colspan="3"><?php esc_html_e('No shipping zone found for configuration. Please contact with admin for manage your store shipping', 'multivendorx'); ?></td>
                                </tr>
    <?php }
?>
                        </tbody>
                    </table>
                    <div id="vendor-shipping-methods"></div>
                </div>
            </div>
        </div>
        </div>
        <?php do_action('mvx_before_shipping_form_end_vendor_dashboard'); ?>
        <div class="mvx-action-container">
            <button class="mvx_orange_btn btn btn-default" name="shipping_save"><?php esc_html_e('Save Options', 'multivendorx'); ?></button>
        </div>
        <div class="clear"></div>
    </form>

</div>