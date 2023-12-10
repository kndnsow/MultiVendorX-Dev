'use strict';
var couponController = ( function ( $ ) {
    var privateApi = {
        addEventHandlers: function addEventHandlers() {
            $( '#mvx-frontend-dashboard-add-coupon' )
                .on( 'click', '#mvx_frontend_dashboard_coupon_submit, #mvx_frontend_dashboard_coupon_draft', this.saveCoupon )
            ;
            return false;
        },
        setupEnvironment: function setupEnvironment() {
            $( '.date-picker' ).datepicker( {
                defaultDate: '',
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                showButtonPanel: true
            } );
            //Tags section
            $( "#usage_restriction_coupon_data #products, #usage_restriction_coupon_data #exclude_products" ).select2();
            $( "#usage_restriction_coupon_data #product_categories, #usage_restriction_coupon_data #exclude_product_categories" ).select2();
            publicApi.wcEnhancedSelectInit();
            publicApi.wcEnhancedSelectClose();
            return false;
        },
        saveCoupon: function saveCoupon() {
            $( 'form#mvx-frontend-dashboard-add-coupon' ).trigger( 'before_coupon_save' );
            var status = ( this.id === 'mvx_frontend_dashboard_coupon_submit' ) ? 'publish' : ( this.id === 'mvx_frontend_dashboard_coupon_draft' ) ? 'draft' : '';
            $( 'input:hidden[name="status"]' ).val( status );
            $( 'textarea#coupon_description' ).val( publicApi.getTinymceContent( 'coupon_description' ) );
            return true;
        }
    };
    var publicApi = {
        init: function init() {
            privateApi.setupEnvironment( );
            privateApi.addEventHandlers( );
            return false;
        },
        //enhance select init
        wcEnhancedSelectInit: function wcEnhancedSelectInit( ) {
            try {
                // Regular select boxes
                $( ':input.wc-enhanced-select, :input.chosen_select' ).filter( ':not(.enhanced)' ).each( function () {
                    var select2_args = {
                        minimumResultsForSearch: 10,
                        allowClear: $( this ).data( 'allow_clear' ) ? true : false,
                        placeholder: $( this ).data( 'placeholder' )
                    };

                    $( this ).selectWoo( select2_args ).addClass( 'enhanced' );
                } );
                // Ajax product search box
                $( ':input.wc-product-search' ).filter( ':not(.enhanced)' ).each( function () {
                    var select2_args = {
                        allowClear: $( this ).data( 'allow_clear' ) ? true : false,
                        placeholder: $( this ).data( 'placeholder' ),
                        minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                        escapeMarkup: function ( m ) {
                            return m;
                        },
                        ajax: {
                            url: add_coupon_params.ajax_url,
                            dataType: 'json',
                            delay: 250,
                            data: function ( params ) {
                                return {
                                    term: params.term,
                                    action: $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
                                    security: add_coupon_params.search_products_nonce,
                                    exclude: $( this ).data( 'exclude' ),
                                    include: $( this ).data( 'include' ),
                                    limit: $( this ).data( 'limit' )
                                };
                            },
                            processResults: function ( data ) {
                                var terms = [ ];
                                if ( data ) {
                                    $.each( data, function ( id, text ) {
                                        terms.push( { id: id, text: text } );
                                    } );
                                }
                                return {
                                    results: terms
                                };
                            },
                            cache: true
                        }
                    };

                    // select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

                    $( this ).selectWoo( select2_args ).addClass( 'enhanced' );

                    if ( $( this ).data( 'sortable' ) ) {
                        var $select = $( this );
                        var $list = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

                        $list.sortable( {
                            placeholder: 'ui-state-highlight select2-selection__choice',
                            forcePlaceholderSize: true,
                            items: 'li:not(.select2-search__field)',
                            tolerance: 'pointer',
                            stop: function () {
                                $( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function () {
                                    var id = $( this ).data( 'data' ).id;
                                    var option = $select.find( 'option[value="' + id + '"]' )[0];
                                    $select.prepend( option );
                                } );
                            }
                        } );
                        // Keep multiselects ordered alphabetically if they are not sortable.
                    } else if ( $( this ).prop( 'multiple' ) ) {
                        $( this ).on( 'change', function () {
                            var $children = $( this ).children();
                            $children.sort( function ( a, b ) {
                                var atext = a.text.toLowerCase();
                                var btext = b.text.toLowerCase();

                                if ( atext > btext ) {
                                    return 1;
                                }
                                if ( atext < btext ) {
                                    return -1;
                                }
                                return 0;
                            } );
                            $( this ).html( $children );
                        } );
                    }
                } );

            } catch ( err ) {
                // If select2 failed (conflict?) log the error but don't stop other scripts breaking.
                window.console.log( err );
            }
        },
        wcEnhancedSelectClose: function wcEnhancedSelectClose( ) {
            try {
                $( 'html' ).on( 'click', function ( event ) {
                    if ( this === event.target ) {
                        $( '.wc-enhanced-select, :input.wc-product-search' ).filter( '.select2-hidden-accessible' ).selectWoo( 'close' );
                    }
                } );
            } catch ( err ) {
                // If select2 failed (conflict?) log the error but don't stop other scripts breaking.
                window.console.log( err );
            }
        },
        getTinymceContent: function getTinymceContent( editor_id ) {
            if ( $( '#wp-' + editor_id + '-wrap' ).hasClass( 'tmce-active' ) && typeof tinyMCE !== 'undefined' && tinyMCE.get( editor_id ) ) {
                return tinyMCE.get( editor_id ).getContent();
            } else {
                return $( 'textarea#' + editor_id ).val();
            }
        }
    };
    return publicApi;
} )( jQuery );
couponController.init();