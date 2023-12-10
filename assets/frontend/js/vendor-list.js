/* global mvx_vendor_list_script_data */
(function ($) {
    
    // mvx_vendor_list_script_data is required to continue, ensure the object exists
    if ( typeof mvx_vendor_list_script_data === 'undefined' ) {
        return false;
    }

    if (mvx_vendor_list_script_data.mapbox_emable) {
            mapboxgl.accessToken = mvx_vendor_list_script_data.mapbox_emable;
            var geojson = mvx_vendor_list_script_data.stores;
            var map = new mapboxgl.Map({
                container: 'mvx-vendor-list-map', // container id
                style: 'mapbox://styles/mapbox/satellite-v9', // style URL
                center: [0, 0],
                zoom: 1
            });

            // add markers to map
            geojson.forEach(function (marker) {
                // create a HTML element for each feature
                var el = document.createElement('div');
                el.className = 'marker';
                el.style.backgroundImage =
                'url(' + mvx_vendor_list_script_data.map_data.marker_icon +')';
                el.style.width = '38px';
                el.style.height = '50px';
                el.style.cursor = 'pointer';

                var coordinates = [marker.location.lat, marker.location.lng]
                // make a marker for each feature and add it to the map
                new mapboxgl.Marker(el)
                    .setLngLat(coordinates)
                    .setPopup(
                    new mapboxgl.Popup({ offset: 25 }) // add popups
                    .setHTML(
                        marker.info_html
                        )
                    )
                .addTo(map);
            });

            var geocoder = new MapboxGeocoder({
                accessToken: mapboxgl.accessToken,
                marker: {
                    color: 'red'
                },
                mapboxgl: mapboxgl
            });

            var geocoder = new MapboxGeocoder({
                accessToken: mapboxgl.accessToken,
                mapboxgl: mapboxgl
            });

            document.getElementById('locationText').appendChild(geocoder.onAdd(map));

            map.on('load', function() {
                geocoder.on('result', function(ev) {
                    $('#mvx_vlist_center_lat').val(ev.result.center[0]);
                    $('#mvx_vlist_center_lng').val(ev.result.center[1]);
                });
            });

            // full screen control
            map.addControl(new mapboxgl.FullscreenControl());
            // Zoom in out feature
            map.addControl(new mapboxgl.NavigationControl());
            // current location fetch
            map.addControl(
                new mapboxgl.GeolocateControl({
                    positionOptions: {
                        enableHighAccuracy: true
                    },
                    trackUserLocation: true
                })
            );
            var layerList = document.getElementById('menu');
            var inputs = layerList.getElementsByTagName('input');
            // Change map style
            function switchLayer(layer) {
                var layerId = layer.target.id;
                map.setStyle('mapbox://styles/mapbox/' + layerId);
            }

            for (var i = 0; i < inputs.length; i++) {
                inputs[i].onclick = switchLayer;
            }

    } else {
        var markers = [];
        var infoWindow;
        var map;
        var bounds;
        var init_options;
        var styles
        var styledMap
        
        function initialize() { 
    	// Create a map object and specify the DOM element for display.
            var init_options = mvx_vendor_list_script_data.map_data.map_options;
            if(mvx_vendor_list_script_data.map_data.map_style == '1'){
                mvx_vendor_list_script_data.map_data.map_options.mapTypeControlOptions.mapTypeIds.push('mvx_map_style');
                var styles = mvx_vendor_list_script_data.map_data.map_style_data;
                var styledMap = new google.maps.StyledMapType(styles, { name: mvx_vendor_list_script_data.map_data.map_style_title });
            }
            var map = new google.maps.Map(document.getElementById("mvx-vendor-list-map"), init_options);
            if(mvx_vendor_list_script_data.map_data.map_style == '1'){
                map.mapTypes.set('mvx_map_style', styledMap);
                map.setMapTypeId('mvx_map_style');
            }
            
    	infoWindow = new google.maps.InfoWindow();
            bounds = new google.maps.LatLngBounds();
    	// Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    // set center position
                    $('#mvx_vlist_center_lat').val(position.coords.latitude);
                    $('#mvx_vlist_center_lng').val(position.coords.longitude);

                    map.setCenter(pos);
                    map.fitBounds(bounds);
                }, function(error) {
                    handleLocationError(true, infoWindow, map.getCenter(), error);
                });
            } else {
                // Browser doesnt support Geolocation
                handleLocationError(false, infoWindow, map.getCenter(), -1);
            }

            function handleLocationError(browserHasGeolocation, infoWindow, pos, error) {
                if( error == -1 ){
                    alert( mvx_vendor_list_script_data.lang.geolocation_doesnt_support );
                }else{
                    switch( error.code ) {
                        case error.PERMISSION_DENIED:
                        alert( mvx_vendor_list_script_data.lang.geolocation_permission_denied );
                        break;
                        case error.POSITION_UNAVAILABLE:
                        alert( mvx_vendor_list_script_data.lang.geolocation_position_unavailable );
                        break;
                        case error.TIMEOUT:
                        alert( mvx_vendor_list_script_data.lang.geolocation_timeout );
                        break;
                        case error.UNKNOWN_ERROR:
                        alert( mvx_vendor_list_script_data.lang.geolocation_service_failed );
                        break;
                    }
                }
            
                infoWindow.setPosition(pos);
                infoWindow.setContent(browserHasGeolocation ? mvx_vendor_list_script_data.lang.geolocation_service_failed : mvx_vendor_list_script_data.lang.geolocation_doesnt_support);
                infoWindow.open(map);
            }

    	function createMarker(storeInfo) { 
                bounds.extend(new google.maps.LatLng(storeInfo.location.lat, storeInfo.location.lng));
                var marker = new google.maps.Marker({
                    map: map,
                    icon: mvx_vendor_list_script_data.map_data.marker_icon,
                    position: new google.maps.LatLng(storeInfo.location.lat, storeInfo.location.lng),
                    title: storeInfo.store_name
                });
                google.maps.event.addListener(marker, "click", function() {
                    infoWindow.setContent(storeInfo.info_html);
                    infoWindow.open(map, marker);
                });
                markers.push(marker);
            }

    	mvx_vendor_list_script_data.stores.forEach(function(store){
                createMarker(store);
    	}); 
            
            if( mvx_vendor_list_script_data.autocomplete ) {
                var input = document.getElementById("locationText");
                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.addListener("place_changed", function() {
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
                    // set center position
                    $('#mvx_vlist_center_lat').val(place.geometry.location.lat());
                    $('#mvx_vlist_center_lng').val(place.geometry.location.lng());
                    //place.geometry.location.lat(),place.geometry.location.lng()
                });
            } else {
                $('#locationText').on('input', function(){
                    var value = $(this).val();
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({address: value}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var res_location = results[0].geometry.location;
                            map.setCenter(res_location);
                            // set center position
                            $('#mvx_vlist_center_lat').val(res_location.lat());
                            $('#mvx_vlist_center_lng').val(res_location.lng());
                        } else {  
                        }
                    });
                });
            }
        }
        google.maps.event.addDomListener(window, "load", initialize);
    }

})(jQuery);

