<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/MultiVendorX/vendor-dashboard/shop-front.php
 *
 * @author      MultiVendorX
 * @package MultiVendorX/Templates
 * @version   2.4.5
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $MVX;
$vendor = get_current_vendor();
if (!$vendor) {
    return;
}
$vendor_hide_description = get_user_meta($vendor->id, '_vendor_hide_description', true);
$vendor_hide_email = get_user_meta($vendor->id, '_vendor_hide_email', true);
$vendor_hide_address = get_user_meta($vendor->id, '_vendor_hide_address', true);
$vendor_hide_phone = get_user_meta($vendor->id, '_vendor_hide_phone', true);

$field_type = apply_filters('mvx_vendor_storefront_wpeditor_enabled', true, $vendor->id) ? 'wpeditor' : 'textarea';
$_wp_editor_settings = array('tinymce' => true);
if (!$MVX->vendor_caps->vendor_can('is_upload_files')) {
    $_wp_editor_settings['media_buttons'] = false;
}
$_wp_editor_settings = apply_filters('mvx_vendor_storefront_wp_editor_settings', $_wp_editor_settings);
$store_banner_types = apply_filters('mvx_vendor_storefront_banner_types', array( 
        'single_img' => __( 'Static Image', 'multivendorx' ), 
        'slider' => __( 'Slider', 'multivendorx' ), 
        'video' => __( 'Video', 'multivendorx' ) 
    ));
$vendor_banner_type = get_user_meta($vendor->id, '_vendor_banner_type');
$vendor_video = get_user_meta($vendor->id, '_vendor_video', true);
$image = $vendor->get_image() ? $vendor->get_image() : $MVX->plugin_url . 'assets/images/WP-stdavatar.png';
$banner = $vendor->get_image('banner') ? $vendor->get_image('banner') : $MVX->plugin_url . 'assets/images/banner_placeholder.jpg';
?>
<style>
    .store-map-address{
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 40px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #searchStoreAddress {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 44%;
    }
</style>
<div class="col-md-12">
    <!-- <div class="mvx_headding2 card-header"><?php _e('General', 'multivendorx'); ?></div> -->
    <form method="post" name="shop_settings_form" class="mvx_shop_settings_form form-horizontal">
        <?php do_action('mvx_before_shop_front'); ?>
        <div class="panel panel-default pannel-outer-heading">
            <div class="panel-heading d-flex">
                <h3><?php _e('Store Brand', 'multivendorx'); ?></h3>
            </div>
            <div class="panel-body panel-content-padding form-horizontal">
                <div class="form-group">
                    <label class="control-label col-sm-3 col-md-3"><?php _e('Store Banner Type', 'multivendorx'); ?></label>
                    <div class="col-md-6 col-sm-9">
                       <select class="vendor_banner_type form-control regular-select" name="vendor_banner_type">
                        <?php foreach ($store_banner_types as $banner_type_key => $banner_type_val) { ?>
                            <option value="<?php echo $banner_type_key; ?>" <?php selected(in_array( $banner_type_key, $vendor_banner_type), true); ?>><?php echo $banner_type_val; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3 col-md-3"><?php _e('Store Logo', 'multivendorx'); ?></label>
                    <div class="col-md-6 col-sm-9">
                        <div class="vendor-profile-pic-wraper pull-left">
                            <img id="vendor-profile-img" src="<?php echo esc_url($image); ?>" alt="dp">
                            <div class="mvx-media profile-pic-btn">
                                <button type="button" class="mvx_upload_btn" data-target="vendor-profile"><i class="mvx-font ico-edit-pencil-icon"></i> <?php _e('Store Logo', 'multivendorx'); ?></button>
                            </div>
                            <input type="hidden" name="vendor_image" id="vendor-profile-img-id" class="user-profile-fields" value="<?php echo esc_attr($image); ?>"  />
                        </div>
                    </div>
                </div>

                <div id="slider_images_container" class="custom-panel slider_images_container">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Slider gallery', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <ul class="slider_images">
                                <?php
                                $slider_image_gallery ='';
                                if ( metadata_exists( 'user', $vendor->id, '_vendor_slider' ) ) {
                                    $slider_image_gallery = get_user_meta( $vendor->id, '_vendor_slider', true );
                                } else {
                                    
                                }

                                $attachments = array_filter( explode( ',', $slider_image_gallery ) );
                                $update_meta = false;
                                $updated_gallery_ids = array();

                                if ( ! empty( $attachments ) ) {
                                    foreach ( $attachments as $attachment_id ) {
                                        $attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

                                        // if attachment is empty skip
                                        if ( empty( $attachment ) ) {
                                            $update_meta = true;
                                            continue;
                                        }

                                        echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
                                                ' . $attachment . '
                                                <ul class="actions">
                                                    <li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'multivendorx' ) . '">' . __( 'Delete', 'multivendorx' ) . '</a></li>
                                                </ul>
                                            </li>';

                                        // rebuild ids to be saved
                                        $updated_gallery_ids[] = $attachment_id;
                                    }
                                }
                                ?>
                            </ul>
                        
                            <input type="hidden" id="slider_image_gallery" name="slider_image_gallery" value="<?php echo esc_attr( $slider_image_gallery ); ?>" />
                            <p class="add_slider_images">
                                <a href="#" <?php echo current_user_can( 'upload_files' ) ? '' : 'data-nocaps="true" '; ?>data-choose="<?php esc_attr_e( 'Add images to Slider gallery', 'multivendorx' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'multivendorx' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'multivendorx' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'multivendorx' ); ?>" class="save_gallery_image"><?php _e( 'Add slider gallery images', 'multivendorx' ); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="video_container" class="custom-panel video_container">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Video Link', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="url" name="vendor_video_link" value="<?php echo $vendor_video; ?>"  placeholder="<?php _e('Enter youtube video link here', 'multivendorx'); ?>">
                        </div>  
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default pannel-outer-heading vendor-cover-panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="vendor-cover-wrap">
                            <img id="vendor-cover-img" src="<?php echo esc_url($banner); ?>" alt="banner">

                            <div class="mvx-media cover-pic-button pull-right">
                                <button type="button" class="mvx_upload_btn" data-target="vendor-cover"><i class="mvx-font ico-edit-pencil-icon"></i> <?php _e('Upload Cover Picture', 'multivendorx'); ?></button>
                            </div>
                            <input type="hidden" name="vendor_banner" id="vendor-cover-img-id" class="user-profile-fields" value="<?php echo esc_attr($banner); ?>"  />
                        </div>
                    </div>
                </div>         
            </div>
        </div>

        <div class="panel panel-default panel-pading pannel-outer-heading">
            <div class="panel-heading d-flex">
                <h3><?php _e('General', 'multivendorx'); ?></h3>
            </div>
            <div class="panel-body panel-content-padding">
                <div class="mvx_form1">
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Store Name *', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_page_title" value="<?php echo isset($vendor_page_title['value']) ? $vendor_page_title['value'] : ''; ?>"  placeholder="<?php _e('Enter your Store Name here', 'multivendorx'); ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e(' Store Slug *', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon3">
                                    <?php
                                    $dc_vendors_permalinks_array = mvx_get_option('dc_vendors_permalinks');
                                    if (isset($dc_vendors_permalinks_array['vendor_shop_base']) && !empty($dc_vendors_permalinks_array['vendor_shop_base'])) {
                                        $store_slug = trailingslashit($dc_vendors_permalinks_array['vendor_shop_base']);
                                    } else {
                                        $store_slug = trailingslashit('vendor');
                                    } echo $shop_page_url = trailingslashit(get_home_url());
                                    echo $store_slug;
                                    ?>
                                </span>     
                                <input class="small no_input form-control" id="basic-url" aria-describedby="basic-addon3" type="text" name="vendor_page_slug" value="<?php echo isset($vendor_page_slug['value']) ? $vendor_page_slug['value'] : ''; ?>" placeholder="<?php _e('Enter your Store Name here', 'multivendorx'); ?>">
                            </div>  
                        </div>  
                    </div>  
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Store Description', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <?php $vendor_description = isset($vendor_description['value']) ? $vendor_description['value'] : '';
                            $MVX->mvx_wp_fields->dc_generate_form_field(array("vendor_description" => array('name' => 'vendor_description', 'type' => $field_type, 'class' => 'no_input form-control regular-textarea', 'value' => $vendor_description, 'settings' => $_wp_editor_settings))); ?>
                        </div>
                    </div>
                    <?php if (apply_filters('can_vendor_add_message_on_email_and_thankyou_page', true)) { ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Message to Buyers', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <?php $message_to_buyer = isset($vendor_message_to_buyers['value']) ? $vendor_message_to_buyers['value'] : '';
                            $MVX->mvx_wp_fields->dc_generate_form_field(array("vendor_message_to_buyers" => array('name' => 'vendor_message_to_buyers', 'type' => $field_type, 'class' => 'no_input form-control regular-textarea', 'value' => $message_to_buyer, 'settings' => $_wp_editor_settings))); ?>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Phone', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input form-control" type="text" name="vendor_phone" placeholder="" value="<?php echo isset($vendor_phone['value']) ? $vendor_phone['value'] : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Email *', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">                            
                            <input class="no_input vendor_email form-control" type="text" placeholder="" readonly  value="<?php echo isset($vendor->user_data->user_email) ? $vendor->user_data->user_email : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php esc_html_e('Additional Email', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="no_input vendor_display_email form-control" name="vendor_display_email" type="email"  value="<?php echo isset($vendor->user_data->_vendor_display_email) ? esc_attr($vendor->user_data->_vendor_display_email) : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Address', 'multivendorx'); ?></label>     
                        <div class="col-md-6 col-sm-9">                      
                            <div class="row">
                                <div class="col-md-12">
                                    <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('Address line 1', 'multivendorx'); ?>" name="vendor_address_1"  value="<?php echo isset($vendor_address_1['value']) ? $vendor_address_1['value'] : ''; ?>">
                                    <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('Address line 2', 'multivendorx'); ?>" name="vendor_address_2"  value="<?php echo isset($vendor_address_2['value']) ? $vendor_address_2['value'] : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <select name="vendor_country" id="vendor_country" class="country_to_state user-profile-fields form-control inp-btm-margin regular-select" rel="vendor_country">
                                        <option value=""><?php _e( 'Select a country&hellip;', 'multivendorx' ); ?></option>
                                        <?php $country_code = get_user_meta($vendor->id, '_vendor_country_code', true);
                                            foreach ( WC()->countries->get_allowed_countries() as $key => $value ) {
                                                echo '<option value="' . esc_attr( $key ) . '"' . selected( esc_attr( $country_code ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <?php $country_code = get_user_meta($vendor->id, '_vendor_country_code', true);
                                    $states = WC()->countries->get_states( $country_code ); ?>
                                    <select name="vendor_state" id="vendor_state" class="state_select user-profile-fields form-control inp-btm-margin regular-select" rel="vendor_state">
                                        <option value=""><?php esc_html_e( 'Select a state&hellip;', 'multivendorx' ); ?></option>
                                        <?php $state_code = get_user_meta($vendor->id, '_vendor_state_code', true);
                                        if($states):
                                            foreach ( $states as $ckey => $cvalue ) {
                                                echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $state_code, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
                                            }
                                        endif;
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('City', 'multivendorx'); ?>"  name="vendor_city" value="<?php echo isset($vendor_city['value']) ? $vendor_city['value'] : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('ZIP code', 'multivendorx'); ?>" name="vendor_postcode" value="<?php echo isset($vendor_postcode['value']) ? $vendor_postcode['value'] : ''; ?>">
                                </div>
                                <?php
                                if (apply_filters('is_vendor_add_external_url_field', false)) {
                                    ?>
                                    <div class="col-md-6">
                                        <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('External store URL', 'multivendorx'); ?>" name="vendor_external_store_url" value="<?php echo isset($vendor_external_store_url['value']) ? $vendor_external_store_url['value'] : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <input class="no_input form-control inp-btm-margin" type="text" placeholder="<?php _e('External store URL Label', 'multivendorx'); ?>" name="vendor_external_store_label" value="<?php echo isset($vendor_external_store_label['value']) ? $vendor_external_store_label['value'] : ''; ?>">
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="timezone_string" class="control-label col-sm-3 col-md-3"><?php _e('Timezone', 'multivendorx') ?></label>
                        <div class="col-md-6 col-sm-9">
                            <?php
                            $current_offset = get_user_meta($vendor->id, 'gmt_offset', true);
                            $tzstring = get_user_meta($vendor->id, 'timezone_string', true);
                            // Remove old Etc mappings. Fallback to gmt_offset.
                            if (false !== strpos($tzstring, 'Etc/GMT')) {
                                $tzstring = '';
                            }

                            if (empty($tzstring)) { // Create a UTC+- zone if no timezone string exists
                                $check_zone_info = false;
                                if (0 == $current_offset) {
                                    $tzstring = 'UTC+0';
                                } elseif ($current_offset < 0) {
                                    $tzstring = 'UTC' . $current_offset;
                                } else {
                                    $tzstring = 'UTC+' . $current_offset;
                                }
                            }
                            ?>
                            <select id="timezone_string" name="timezone_string" class="form-control" aria-describedby="timezone-description">
                                <?php echo wp_timezone_choice($tzstring, get_user_locale()); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Store Location', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">  
                            <?php
                            if (mvx_is_module_active('store-location') && get_mvx_vendor_settings('enable_store_map_for_vendor', 'store')) {
                                if (mvx_mapbox_api_enabled()) {
                                    $MVX->library->load_mapbox_api();
                                    $map_style = apply_filters( 'mvx_dashboard_location_widget_map_style', 'mapbox://styles/mapbox/streets-v11'); ?>
                                    <div class="vendor_store_map" id="vendor_store_map" style="width: 100%; height: 300px;"></div>
                                    <div class="form_area">
                                        <?php
                                        $store_location = get_user_meta($vendor->id, '_store_location', true) ? get_user_meta($vendor->id, '_store_location', true) : '';
                                        $store_lat = get_user_meta($vendor->id, '_store_lat', true) ? get_user_meta($vendor->id, '_store_lat', true) : 0;
                                        $store_lng = get_user_meta($vendor->id, '_store_lng', true) ? get_user_meta($vendor->id, '_store_lng', true) : 0;
                                        ?>
                                        <input type="hidden" name="_store_location" id="store_location" value="<?php echo $store_location; ?>">
                                        <input type="hidden" name="_store_lat" id="store_lat" value="<?php echo $store_lat; ?>">
                                        <input type="hidden" name="_store_lng" id="store_lng" value="<?php echo $store_lng; ?>">
                                    </div>
                                    <script>
                                        mapboxgl.accessToken = '<?php echo mvx_mapbox_api_enabled(); ?>';
                                        var map = new mapboxgl.Map({
                                            container: 'vendor_store_map', // container id
                                            style: '<?php echo $map_style ?>',
                                            center: [<?php echo $store_lat ?>, <?php echo $store_lng ?>],
                                            zoom: 5
                                        });

                                        var marker1 = new mapboxgl.Marker()
                                            .setLngLat([<?php echo $store_lat ?>, <?php echo $store_lng ?>])
                                        .addTo(map);
                                        var geocoder = new MapboxGeocoder({
                                            accessToken: mapboxgl.accessToken,
                                            marker: {
                                                color: 'red'
                                            },
                                            mapboxgl: mapboxgl
                                        });
                                        map.on('load', function() {
                                            geocoder.on('result', function(ev) {
                                                document.getElementById("store_location").value = ev.result.place_name;
                                                document.getElementById("store_lat").value = ev.result.center[0];
                                                document.getElementById("store_lng").value = ev.result.center[1];
                                            });
                                        });
                                        map.addControl(geocoder);
                                        map.addControl(new mapboxgl.NavigationControl());
                                    </script>
                                    <?php
                                } elseif (mvx_google_api_enabled()) { ?>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <input type="text" id="searchStoreAddress" class="store-map-address form-control" placeholder="<?php _e('Enter store location', 'multivendorx'); ?>">
                                        </div>
                                    </div>
                                    <div class="vendor_store_map" id="vendor_store_map" style="width: 100%; height: 300px;"></div>
                                    <div class="form_area">
                                        <?php
                                        $store_location = get_user_meta($vendor->id, '_store_location', true) ? get_user_meta($vendor->id, '_store_location', true) : '';
                                        $store_lat = get_user_meta($vendor->id, '_store_lat', true) ? get_user_meta($vendor->id, '_store_lat', true) : 0;
                                        $store_lng = get_user_meta($vendor->id, '_store_lng', true) ? get_user_meta($vendor->id, '_store_lng', true) : 0;
                                        ?>
                                        <input type="hidden" name="_store_location" id="store_location" value="<?php echo $store_location; ?>">
                                        <input type="hidden" name="store_address_components" id="store_address_components" value="">
                                        <input type="hidden" name="_store_lat" id="store_lat" value="<?php echo $store_lat; ?>">
                                        <input type="hidden" name="_store_lng" id="store_lng" value="<?php echo $store_lng; ?>">
                                    </div>
                                    <?php
                                    wp_add_inline_script('mvx-gmaps-api', '(function ($) {
                                        function initialize() {
                                            var latlng = new google.maps.LatLng(' . $store_lat . ',' . $store_lng . ');
                                            var map = new google.maps.Map(document.getElementById("vendor_store_map"), {
                                                center: latlng,
                                                blur : true,
                                                zoom: 15
                                            });
                                            var marker = new google.maps.Marker({
                                                map: map,
                                                position: latlng,
                                                draggable: true,
                                                anchorPoint: new google.maps.Point(0, -29)
                                            });

                                            var input = document.getElementById("searchStoreAddress");
                                            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
                                            var geocoder = new google.maps.Geocoder();
                                            var autocomplete = new google.maps.places.Autocomplete(input);
                                            autocomplete.bindTo("bounds", map);
                                            var infowindow = new google.maps.InfoWindow();   

                                            autocomplete.addListener("place_changed", function() {
                                                infowindow.close();
                                                marker.setVisible(false);
                                                var place = autocomplete.getPlace();
                                                if (!place.geometry) {
                                                    window.alert("Autocomplete returned place contains no geometry");
                                                    return;
                                                }

                                                // If the place has a geometry, then present it on a map.
                                                if (place.geometry.viewport) {
                                                    map.fitBounds(place.geometry.viewport);
                                                } else {
                                                    map.setCenter(place.geometry.location);
                                                    map.setZoom(17);
                                                }

                                                marker.setPosition(place.geometry.location);
                                                marker.setVisible(true);
                                                
                                                bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng(),place.address_components);
                                                infowindow.setContent(place.formatted_address);
                                                infowindow.open(map, marker);
                                                showTooltip(infowindow,marker,place.formatted_address);

                                            });
                                            google.maps.event.addListener(marker, "dragend", function() {
                                                geocoder.geocode({"latLng": marker.getPosition()}, function(results, status) {
                                                    if (status == google.maps.GeocoderStatus.OK) {
                                                        if (results[0]) {    
                                                            bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng(), results[0].address_components);
                                                            infowindow.setContent(results[0].formatted_address);
                                                            infowindow.open(map, marker);
                                                            showTooltip(infowindow,marker,results[0].formatted_address);
                                                            document.getElementById("searchStoreAddress");
                                                        }
                                                    }
                                                });
                                            });
                                        }

                                        function bindDataToForm(address,lat,lng,address_components){
                                            document.getElementById("store_location").value = address;
                                            document.getElementById("store_address_components").value = JSON.stringify(address_components);
                                            document.getElementById("store_lat").value = lat;
                                            document.getElementById("store_lng").value = lng;
                                        }
                                        function showTooltip(infowindow,marker,address){
                                           google.maps.event.addListener(marker, "click", function() { 
                                                infowindow.setContent(address);
                                                infowindow.open(map, marker);
                                            });
                                        }
                                        google.maps.event.addDomListener(window, "load", initialize);
                                  })(jQuery);');
                                }
                            } else {
                                echo trim(__('Please contact your administrator to enable Google map feature.', 'multivendorx'));
                            }?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3"><?php _e('Select Store Details To Hide', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <ul class="select-store-details">
                                <li>
                                    <label>
                                        <input type="checkbox" name="vendor_shop_address_hide" value="Enable"<?php if ($vendor_hide_address == 'Enable') echo 'checked'; ?>><?php esc_html_e('Address', 'multivendorx'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="vendor_shop_phone_hide" value="Enable"<?php if ($vendor_hide_phone == 'Enable') echo 'checked'; ?>><?php esc_html_e('Phone', 'multivendorx'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox" name="vendor_shop_email_hide" value="Enable"<?php if ($vendor_hide_email == 'Enable') echo 'checked'; ?>><?php esc_html_e('Email', 'multivendorx'); ?>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- from group end -->
                    <?php do_action( 'mvx_vendor_add_store', $vendor ); ?>
                </div>
            </div>
        </div>

        <div class="panel panel-default pannel-outer-heading">
            <div class="panel-heading d-flex">
                <h3><?php _e('Social Media', 'multivendorx'); ?></h3>
            </div>
            <div class="panel-body panel-content-padding form-horizontal">
                <div class="mvx_media_block">

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 facebook"><?php _e('Facebook', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="url"   name="vendor_fb_profile" value="<?php echo isset($vendor_fb_profile['value']) ? $vendor_fb_profile['value'] : ''; ?>">
                        </div>  
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 twitter"><?php _e('Twitter', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="url"   name="vendor_twitter_profile" value="<?php echo isset($vendor_twitter_profile['value']) ? $vendor_twitter_profile['value'] : ''; ?>">
                        </div>  
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 linkedin"><?php _e('LinkedIn', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="url"  name="vendor_linkdin_profile" value="<?php echo isset($vendor_linkdin_profile['value']) ? $vendor_linkdin_profile['value'] : ''; ?>">
                        </div>  
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 youtube"><?php _e('YouTube', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="url"   name="vendor_youtube" value="<?php echo isset($vendor_youtube['value']) ? $vendor_youtube['value'] : ''; ?>">
                        </div>  
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 instagram"><?php _e('Instagram', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="url"   name="vendor_instagram" value="<?php echo isset($vendor_instagram['value']) ? $vendor_instagram['value'] : ''; ?>">
                        </div>  
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-md-3 pinterest"><?php _e('Pinterest', 'multivendorx'); ?></label>
                        <div class="col-md-6 col-sm-9">
                            <input class="form-control" type="url" name="vendor_pinterest_profile" value="<?php echo isset($vendor_pinterest_profile['value']) ? $vendor_pinterest_profile['value'] : ''; ?>">
                        </div>  
                    </div>
                    <?php do_action( 'mvx_vendor_add_extra_social_link', $vendor ); ?>
                </div>
            </div>
        </div>    

<?php if (apply_filters('can_vendor_edit_shop_template', false)): ?>
            <div class="panel panel-default panel-pading">
                <div class="panel-heading d-flex">
                    <h3><?php _e('Shop Template', 'multivendorx'); ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="mvx_template_list list-unstyled">
                        <?php
                        $template_options = apply_filters('mvx_vendor_shop_template_options', array('template1' => $MVX->plugin_url . 'assets/images/template1.png', 'template2' => $MVX->plugin_url . 'assets/images/template2.png', 'template3' => $MVX->plugin_url . 'assets/images/template3.png'));
                        $shop_template = get_mvx_vendor_settings('mvx_vendor_shop_template', 'vendor', 'dashboard', 'template1');
                        $shop_template = get_mvx_vendor_settings('can_vendor_edit_shop_template', 'vendor', 'dashboard', false) && get_user_meta($vendor->id, '_shop_template', true) ? get_user_meta($vendor->id, '_shop_template', true) : $shop_template;
                        foreach ($template_options as $template => $template_image):
                            ?>
                            <li>
                                <label>
                                    <input type="radio" <?php checked($template, $shop_template); ?> name="_shop_template" value="<?php echo $template; ?>" />
                                    <i class="dashicons dashicons-yes"></i>
                                    <div class="template-overlay"></div>
                                    <img src="<?php echo $template_image; ?>" />
                                </label>
                            </li>
            <?php endforeach; ?>
                    </ul>                    
                </div>
            </div>    
<?php endif; ?>
<?php do_action('mvx_after_shop_front'); ?>
<?php do_action('other_exta_field_dcmv'); ?>
        <div class="action_div_space"> </div>
        <div class="mvx-action-container">
            <button type="submit" class="btn btn-default" name="store_save"><?php _e('Save Options', 'multivendorx'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
</div>