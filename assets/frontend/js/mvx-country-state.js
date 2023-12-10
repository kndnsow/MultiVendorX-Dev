/*global wc_country_select_params */
jQuery( function( $ ) {

    // wc_country_select_params is required to continue, ensure the object exists
    if ( typeof wc_country_select_params === 'undefined' ) {
            return false;
    }
    
        /* State/Country select boxes */
    var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
    states = $.parseJSON( states_json );

    $( document.body ).on( 'change', 'select.country_to_state, input.country_to_state', function() {
            // Grab wrapping element to target only stateboxes in same 'group'
            var $wrapper    = $( this ).closest('.store-address-info, .vendor-preview-form');

            if ( ! $wrapper.length ) {
                    $wrapper = $( this ).closest('.form-row').parent();
            }

            var country     = $( this ).val(),
                    $statebox   = $( '#vendor_state' ),
                    $parent     = $statebox.parent(),
                    input_name  = $statebox.attr( 'name' ),
                    input_id    = $statebox.attr( 'id' ),
                    value       = $statebox.val(),
                    placeholder = $statebox.attr( 'placeholder' ) || $statebox.attr( 'data-placeholder' ) || '';

            if ( states[ country ] ) {
                    if ( $.isEmptyObject( states[ country ] ) ) {
                            $statebox.parents('.vendor_state_wrapper').hide();
                            //$statebox.parent().hide().find( '.select2-container' ).remove();
                            $statebox.replaceWith( '<input type="hidden" class="hidden" name="' + input_name + '" id="' + input_id + '" value="" placeholder="' + placeholder + '" />' );

                            $( document.body ).trigger( 'country_to_state_changed', [ country, $wrapper ] );

                    } else {

                            var options = '',
                                    state = states[ country ];

                            for( var index in state ) {
                                    if ( state.hasOwnProperty( index ) ) {
                                            options = options + '<option value="' + index + '">' + state[ index ] + '</option>';
                                    }
                            }
                            
                            $statebox.parent().show();
                            
                            if ( $statebox.is( 'input' ) ) {
                                    // Change for select
                                    $statebox.replaceWith( '<select name="' + input_name + '" id="' + input_id + '" class="state_select select_box form-control inp-btm-margin regular-select" data-placeholder="' + placeholder + '"></select>' );
                                    //$statebox = $wrapper.find( '#vendor_state' );
                                    $statebox   = $( '#vendor_state' );
                            }
                            
                            $statebox.html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );
                            $statebox.val( value ).change();

                            $( document.body ).trigger( 'country_to_state_changed', [country, $wrapper ] );

                    }
            } else {
                    if ( $statebox.is( 'select' ) ) {

                            $parent.show().find( '.select2-container' ).remove();
                            $statebox.replaceWith( '<input type="text" class="form-control inp-btm-margin regular-text input-text" name="' + input_name + '" id="' + input_id + '" placeholder="' + placeholder + '" style="display:none;" />' );

                            $( document.body ).trigger( 'country_to_state_changed', [country, $wrapper ] );

                    } else if ( $statebox.is( 'input[type="hidden"]' ) ) {

                            $parent.show().find( '.select2-container' ).remove();
                            $statebox.replaceWith( '<input type="text" class="form-control inp-btm-margin regular-text input-text" name="' + input_name + '" id="' + input_id + '" placeholder="' + placeholder + '" style="display:none;" />' );

                            $( document.body ).trigger( 'country_to_state_changed', [country, $wrapper ] );

                    }
            }

            $( document.body ).trigger( 'country_to_state_changing', [country, $wrapper ] );

    }).trigger('change');

    $(function() {
            $( ':input.country_to_state' ).change();
    });

});

