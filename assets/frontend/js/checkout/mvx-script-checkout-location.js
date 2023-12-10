jQuery(document).ready( function($) {
	var map = geocoder = marker = map_marker = infowindow = '';
	
	$mvx_user_location_lat = jQuery("#mvx_user_location_lat").val();
	$mvx_user_location_lng = jQuery("#mvx_user_location_lng").val();
	
	function initialize() {
  		if( !mvx_checkout_map_options.mapbox_emable ) {
			var latlng = new google.maps.LatLng( mvx_checkout_map_options.default_lat, mvx_checkout_map_options.default_lng, 13 );
			map = new google.maps.Map(document.getElementById("mvx-user-locaton-map"), {
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				zoom: parseInt( mvx_checkout_map_options.default_zoom )
			});
			var customIcon = {
				url: mvx_checkout_map_options.store_icon,
				scaledSize: new google.maps.Size( mvx_checkout_map_options.icon_width, mvx_checkout_map_options.icon_height ), // scaled size
			};
			marker = new google.maps.Marker({
					map: map,
					position: latlng,
					animation: google.maps.Animation.DROP,
					icon: customIcon,
					draggable: true,
			});
		
			var mvx_user_location_input = document.getElementById("mvx_user_location");
			geocoder = new google.maps.Geocoder();
			var autocomplete = new google.maps.places.Autocomplete(mvx_user_location_input);
			autocomplete.bindTo("bounds", map);
			infowindow = new google.maps.InfoWindow();   
		
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
					map.setZoom( parseInt( mvx_checkout_map_options.default_zoom ) );
				}
	
				marker.setPosition(place.geometry.location);
				marker.setVisible(true);
	
				bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng(), false);
				infowindow.setContent(place.formatted_address);
				infowindow.open(map, marker);
				showTooltip(infowindow,marker,place.formatted_address);
		
			});
			google.maps.event.addListener(marker, "dragend", function() {
				geocoder.geocode({"latLng": marker.getPosition()}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						if (results[0]) {        
							bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng(), true);
							infowindow.setContent(results[0].formatted_address);
							infowindow.open(map, marker);
							showTooltip(infowindow,marker,results[0].formatted_address);
						}
					}
				});
			});
		} else {
			mapboxgl.accessToken = mvx_checkout_map_options.mapbox_emable;
			var map = new mapboxgl.Map({
			container: 'mvx-user-locaton-map', // container id
			style: 'mapbox://styles/mapbox/streets-v11',
			center: [mvx_checkout_map_options.default_lat, mvx_checkout_map_options.default_lng],
			zoom: parseInt( mvx_checkout_map_options.default_zoom )
			});

			var geocoder_mapbox = new MapboxGeocoder({
				accessToken: mapboxgl.accessToken,
				marker: {
					color: 'red'
				},
				mapboxgl: mapboxgl
			});
			map.on('load', function() {
				geocoder_mapbox.on('result', function(ev) {
					document.getElementById("mvx_user_location").value = ev.result.place_name;
					document.getElementById("mvx_user_location_lat").value = ev.result.center[0];
					document.getElementById("mvx_user_location_lng").value = ev.result.center[1];
					$( document.body ).trigger( 'update_checkout' );
				});
			});
			map.addControl(geocoder_mapbox);
			map.addControl(new mapboxgl.NavigationControl());
		}
	}
	
	function bindDataToForm(address,lat,lng, find_field_refresh) {
		if( find_field_refresh ) {
			 document.getElementById("mvx_user_location").value = address;
		}
		document.getElementById("mvx_user_location_lat").value = lat;
		document.getElementById("mvx_user_location_lng").value = lng;
		
		$( document.body ).trigger( 'update_checkout' );
	}
	function showTooltip(infowindow,marker,address){
	 google.maps.event.addListener(marker, "click", function() { 
				infowindow.setContent(address);
				infowindow.open(map, marker);
		});
	}
	
	function setUser_CurrentLocation() {
		navigator.geolocation.getCurrentPosition( function( position ) {
			$current_location_fetched = true;
			if( !mvx_checkout_map_options.mapbox_emable ) {
				geocoder.geocode( {
						location: {
								lat: position.coords.latitude,
								lng: position.coords.longitude
						}
				}, function ( results, status ) {
						if ( 'OK' === status ) {
							bindDataToForm( results[0].formatted_address, position.coords.latitude, position.coords.longitude, true );
							var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
							marker.setPosition(latlng);
							marker.setVisible(true);
							infowindow.setContent( results[0].formatted_address );
							infowindow.open( map, marker );
							showTooltip( infowindow, marker, results[0].formatted_address );
						}
				} )
			} else {}
		});
	}
	
	if( jQuery("#mvx_user_location_lat").length > 0 ) {
		setTimeout( function() {
			initialize();
			if ( navigator.geolocation ) {
				setUser_CurrentLocation();
			}
		}, 1000 );
	}
});