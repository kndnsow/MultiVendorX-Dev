'use strict';
//global library accessable from all other js
var mvxAfmLibrary = ( function ( $ ) {
    return {
        // Date picker fields.
        datePickerSelect: function ( datepicker ) {
            var option = $( datepicker ).is( '.sale_price_dates_from' ) ? 'minDate' : 'maxDate',
                $otherDateField = 'minDate' === option ? $( datepicker ).closest( '.sale_price_dates_fields' ).find( '.sale_price_dates_to' ) : $( datepicker ).closest( '.sale_price_dates_fields' ).find( '.sale_price_dates_from' ),
                date = $( datepicker ).datepicker( 'getDate' );

            $( $otherDateField ).datepicker( 'option', option, date );
            $( datepicker ).change();
        },
        //enhance select init
        wcEnhancedSelectInit: function ( ) {
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
                            url: mvx_advance_product_params.ajax_url,
                            dataType: 'json',
                            delay: 250,
                            data: function ( params ) {
                                return {
                                    term: params.term,
                                    action: $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
                                    security: mvx_advance_product_params.search_products_nonce,
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
        wcEnhancedSelectClose: function ( ) {
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
        qtip: function (){
            // Tool-tips
            $('.img_tip').each(function () {
                $(this).qtip({
                    content: $(this).attr('data-desc'),
                    position: {
                        my: 'top center',
                        at: 'bottom center',
                        viewport: $(window)
                    },
                    show: {
                        event: 'mouseover',
                        solo: true,
                    },
                    hide: {
                        inactive: 6000,
                        fixed: true
                    },
                    style: {
                        classes: 'qtip-dark qtip-shadow qtip-rounded qtip-dc-css',
                        width: 200
                    }
                });
            });
        }
    };
} )( jQuery );
var mvxAfmProductEditor = ( function ( $ ) {
    var state = {
        productType: $( 'select#product-type' ).val(),
        manageStock: $( 'input#_manage_stock' ).is( ':checked' )
    };
    //var library = null;
    var media = null;
    var downloads = null;
    var attributes = null;
    var variations = null;
    return {
        getState: function ( prop ) {
            return state.hasOwnProperty( prop ) ? state[prop] : '';
        },
        setState: function ( prop, newVal ) {
            if ( state.hasOwnProperty( prop ) && newVal ) {
                state[prop] = newVal;
                return true;
            }
            return false;
        },
        init: function ( ) {
            $( '#woocommerce-product-data' )
                .on( 'change', 'select#product-type', this.productTypeChanged.bind( this ) )
                .on( 'change', 'input#_downloadable, input#_virtual', this.updateTabsDisplay.bind( this ) )
                .on( 'click', '.sale_schedule', this.showSalePriceDates )
                .on( 'click', '.cancel_sale_schedule', this.hideSalePriceDates )
                .on( 'change', 'input#_manage_stock', this.stockManagementPreferenceChanged.bind( this ) )
                ;
            $( '.add-product-single' )
                .on( 'click', '.notice-wrapper button.notice-dismiss', this.dismissNotice );
            //save
            $( '#mvx-edit-product-form' ).on( 'click', '#mvx_frontend_dashboard_product_submit, #mvx_frontend_dashboard_product_draft', this.saveProduct.bind( this ) );
            // reset taxonomy on change trigger
            $( '#mvx-edit-product-form' )
                .on( 'change', 'ul.taxonomy-widget input[name^="tax_input"]', this.taxInputChanged.bind( this ) )
                ;
            //library = this.loadLibraryComponents();
            media = this.mediaController();
            downloads = this.downloadsController();
            attributes = this.attributeController();
            //variations = this.variationController();
            this.setupEnvironment();
        },
        mediaController: function ( ) {
            /**
             * wp.media frame object
             *
             * @type {Object}
             */
            var featuredImageFrame = null;
            /**
             * wp.media frame object
             *
             * @type {Object}
             */
            var galleryImagesFrame = null;

            var $imageGalleryIDs = $( '#product_image_gallery' );

            return {
                init: function ( ) {
                    $( '.featured-img' )
                        .on( 'click', '.upload_image_button:not(.remove)', this.addFeaturedImage )
                        .on( 'click', '.upload_image_button.remove', this.removeFeaturedImage );

                    $( '#product_images_container' )
                        .on( 'click', '.add_product_images a', this.addGalleryImages )
                        .on( 'click', '.product_images a.delete', this.removeGalleryImage );

                    this.initGalleryImagesSort();
                },
                addFeaturedImage: function ( event ) {
                    var $button = $( this ),
                        $parent = $button.closest( '.upload_image' );

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( featuredImageFrame ) {
                        featuredImageFrame.open();
                        return;
                    }

                    // Create the media frame.
                    featuredImageFrame = wp.media.frames.featured_image = wp.media( {
                        // Set the title of the modal.
                        title: $button.data( 'title' ),
                        button: {
                            text: $button.data( 'button' )
                        },
                        states: [
                            new wp.media.controller.Library( {
                                title: $button.data( 'title' ),
                                filterable: 'all'
                            } )
                        ]
                    } );

                    // When an image is selected, run a callback.
                    featuredImageFrame.on( 'select', function () {

                        var attachment = featuredImageFrame.state().get( 'selection' ).first().toJSON(),
                            url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

                        $( '.upload_image_id', $parent ).val( attachment.id ).change();
                        $parent.find( '.upload_image_button' ).addClass( 'remove' );
                        $parent.find( 'img' ).eq( 0 ).attr( 'src', url );
                    } );

                    // Finally, open the modal.
                    featuredImageFrame.open();
                },
                removeFeaturedImage: function ( event ) {
                    event.preventDefault();

                    var $parent = $( this ).closest( '.upload_image' );

                    $( '.upload_image_id', $parent ).val( '' ).change();
                    $parent.find( 'img' ).eq( 0 ).attr( 'src', mvx_advance_product_params.woocommerce_placeholder_img_src );
                    $parent.find( '.upload_image_button' ).removeClass( 'remove' );
                },
                addGalleryImages: function ( event ) {
                    var $el = $( this );
                    var $productImages = $( '#product_images_container' ).find( 'ul.product_images' );

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( galleryImagesFrame ) {
                        galleryImagesFrame.open();
                        return;
                    }

                    // Create the media frame.
                    galleryImagesFrame = wp.media.frames.product_gallery = wp.media( {
                        // Set the title of the modal.
                        title: $el.data( 'choose' ),
                        button: {
                            text: $el.data( 'update' )
                        },
                        states: [
                            new wp.media.controller.Library( {
                                title: $el.data( 'choose' ),
                                filterable: 'all',
                                multiple: true
                            } )
                        ]
                    } );

                    // When an image is selected, run a callback.
                    galleryImagesFrame.on( 'select', function () {
                        var selection = galleryImagesFrame.state().get( 'selection' ),
                            attachmentIDs = $imageGalleryIDs.val();

                        selection.map( function ( attachment ) {
                            attachment = attachment.toJSON();

                            if ( attachment.id ) {
                                attachmentIDs = attachmentIDs ? attachmentIDs + ',' + attachment.id : attachment.id;
                                var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                                $productImages.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data( 'delete' ) + '">' + $el.data( 'text' ) + '</a></li></ul></li>' );
                            }
                        } );

                        $imageGalleryIDs.val( attachmentIDs );
                    } );

                    // Finally, open the modal.
                    galleryImagesFrame.open();
                },
                removeGalleryImage: function ( ) {
                    $( this ).closest( 'li.image' ).remove();

                    var attachmentIDs = '';

                    $( '#product_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function () {
                        var attachmentID = $( this ).attr( 'data-attachment_id' );
                        attachmentIDs = attachmentIDs + attachmentID + ',';
                    } );

                    $imageGalleryIDs.val( attachmentIDs );

                    return false;
                },
                initGalleryImagesSort: function ( ) {
                    // Image ordering.
                    $( '#product_images_container' )
                        .find( 'ul.product_images' )
                        .sortable( {
                            items: 'li.image',
                            cursor: 'move',
                            scrollSensitivity: 40,
                            forcePlaceholderSize: true,
                            forceHelperSize: false,
                            helper: 'clone',
                            opacity: 0.65,
                            placeholder: 'product-image-gallery-placeholder',
                            start: function ( event, ui ) {
                                ui.item.css( 'background-color', '#f6f6f6' );
                            },
                            stop: function ( event, ui ) {
                                ui.item.removeAttr( 'style' );
                            },
                            update: function () {
                                var attachmentIDs = '';

                                $( '#product_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function () {
                                    var attachmentID = $( this ).attr( 'data-attachment_id' );
                                    attachmentIDs = attachmentIDs + attachmentID + ',';
                                } );

                                $imageGalleryIDs.val( attachmentIDs );
                            }
                        } );
                }
            };
        },
        downloadsController: function ( ) {
            var downloadable_file_frame;
            var file_path_field;
            return {
                init: function ( ) {
                    $( '#woocommerce-product-data' )
                        .on( 'click', '.downloadable_files a.insert', this.insertDownloadableFiles )
                        .on( 'click', '.downloadable_files a.delete', this.deleteDownloadableFiles )
                        .on( 'click', '.downloadable_files a.upload_file_button', this.fileUploaded )
                        .on( 'downloadable_file_fields_inserted', this.sortDownloadableFiles )
                        ;
                },
                insertDownloadableFiles: function ( ) {
                    $( this ).closest( '.downloadable_files' ).find( 'tbody' ).append( $( this ).data( 'row' ) );
                    return false;
                },
                deleteDownloadableFiles: function ( ) {
                    $( this ).closest( 'tr' ).remove();
                    return false;
                },
                fileUploaded: function ( event ) {
                    var $el = $( this );

                    file_path_field = $el.closest( 'tr' ).find( 'td.file_url input' );

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( downloadable_file_frame ) {
                        downloadable_file_frame.open();
                        return;
                    }

                    var downloadable_file_states = [
                        // Main states.
                        new wp.media.controller.Library( {
                            library: wp.media.query(),
                            multiple: true,
                            title: $el.data( 'choose' ),
                            priority: 20,
                            filterable: 'uploaded'
                        } )
                    ];

                    // Create the media frame.
                    downloadable_file_frame = wp.media.frames.downloadable_file = wp.media( {
                        // Set the title of the modal.
                        title: $el.data( 'choose' ),
                        library: {
                            type: ''
                        },
                        button: {
                            text: $el.data( 'update' )
                        },
                        multiple: true,
                        states: downloadable_file_states
                    } );

                    // When an image is selected, run a callback.
                    downloadable_file_frame.on( 'select', function () {
                        var file_path = '';
                        var selection = downloadable_file_frame.state().get( 'selection' );

                        selection.map( function ( attachment ) {
                            attachment = attachment.toJSON();
                            if ( attachment.url ) {
                                file_path = attachment.url;
                            }
                        } );

                        file_path_field.val( file_path ).change();
                    } );

                    // Set post to 0 and set our custom type.
                    downloadable_file_frame.on( 'ready', function () {
                        downloadable_file_frame.uploader.options.uploader.params = {
                            type: 'downloadable_product'
                        };
                    } );

                    // Finally, open the modal.
                    downloadable_file_frame.open();
                },
                sortDownloadableFiles: function ( ) {
                    $( '.downloadable_files tbody' ).sortable( {
                        items: 'tr',
                        cursor: 'move',
                        axis: 'y',
                        handle: 'span.sortable-icon',
                        scrollSensitivity: 40,
                        forcePlaceholderSize: true,
                        helper: 'clone',
                        opacity: 0.65,
                        placeholder: 'downloadable-files-sortable-placeholder',
                        start: function ( event, ui ) {
                            ui.item.css( 'background-color', '#f6f6f6' );
                        },
                        stop: function ( event, ui ) {
                            ui.item.removeAttr( 'style' );
                        }
                    } );
                }
            };
        },
        setupEnvironment: function ( ) {
            $( 'select#product-type' ).change();
            $( 'input#_manage_stock' ).change();
            mvxAfmLibrary.wcEnhancedSelectInit();
            mvxAfmLibrary.wcEnhancedSelectClose();
            mvxAfmLibrary.qtip();

            media.init();
            downloads.init();
            attributes.init();
            //variations.init();

            this.salePriceDateFieldInit( $( '#woocommerce-product-data' ) );
            //Make download files sortable
            $( '#woocommerce-product-data' ).trigger( 'downloadable_file_fields_inserted' );
            //Tags section
            $( ".multiselect.product_tag" ).select2( {
                tags: mvx_advance_product_params.add_tags,
                tokenSeparators: [ ',' ],
                placeholder: $( this ).data( 'placeholder' ),
            } ).on( "change", function ( ) {
                var isNew = $( this ).find( '[data-select2-tag="true"]' );
                if ( isNew.length ) {
                    var data = {
                        action: 'mvx_product_tag_add',
                        new_tag: isNew.val(),
                        security: mvx_advance_product_params.add_attribute_nonce
                    };
                    $.ajax( {
                        type: 'POST',
                        url: mvx_advance_product_params.ajax_url,
                        data: data,
                        success: function ( response ) {
                            if ( response.status ) { 
                                var option_value = ( response.tag ) ? response.tag.term_id : isNew.val();
                                isNew.replaceWith( '<option selected value="' + option_value + '">' + isNew.val() + '</option>' );
                            } else {
                                if ( response.message != '' ) {
                                    $( '.woocommerce-error,woocommerce-message' ).remove();
                                    $( '#mvx-frontend-dashboard-add-product' ).prepend( '<div class="woocommerce-error" tabindex="-1">' + response.message + '</div>' );
                                    $( '.woocommerce-error' ).focus();
                                }
                                $( '.multiselect.product_tag option[value="' + isNew.val() + '"]' ).remove();
                            }
                        }
                    } );
                }
            } );
            //AFM Tabs library
            $( '#product_data_tabs' ).afmTabInit();
        },
        salePriceDateFieldInit: function ( $wrap ) {
            // Sale price schedule.
            $( '.sale_price_dates_fields', $wrap ).each( function () {
                var $theseSaleDates = $( this );
                var saleScheduleSet = false;
                var $wrap = $theseSaleDates.closest( 'div.form-group-row' ); //, table

                //Initialize datepicker on text input
                /*$theseSaleDates.find( 'input' ).datepicker( {
                    defaultDate: '',
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                    showButtonPanel: true,
                    onSelect: function () {
                        mvxAfmLibrary.datePickerSelect( $( this ) );
                    }
                } ).on( 'change', function () {
                    if ( !$( this ).datepicker( 'getDate' ) ) {
                        var option = $( this ).is( '.sale_price_dates_from' ) ? 'minDate' : 'maxDate',
                            $otherDateField = 'minDate' === option ? $( this ).closest( '.sale_price_dates_fields' ).find( '.sale_price_dates_to' ) : $( this ).closest( '.sale_price_dates_fields' ).find( '.sale_price_dates_from' );
                        $( $otherDateField ).datepicker( 'option', option, null );
                    }
                    return false;
                } );*/

                $theseSaleDates.find( 'input' ).each( function () {
                    if ( '' !== $( this ).val() ) {
                        saleScheduleSet = true;
                    }
                    mvxAfmLibrary.datePickerSelect( $( this ) );
                } );

                if ( saleScheduleSet ) {
                    $wrap.find( '.sale_schedule' ).hide();
                    $wrap.find( '.sale_price_dates_fields' ).show();
                } else {
                    $wrap.find( '.sale_schedule' ).show();
                    $wrap.find( '.sale_price_dates_fields' ).hide();
                }
            } );
        },
        productTypeChanged: function ( ) {
            // Get value.
            var selectVal = $( 'select#product-type' ).val();
            //update state
            this.setState( 'productType', selectVal );

            if ( 'variable' === selectVal ) {
                $( 'input#_manage_stock' ).change();
                $( 'input#_downloadable' ).prop( 'checked', false );
                $( 'input#_virtual' ).removeAttr( 'checked' );
            } else if ( 'grouped' === selectVal ) {
                $( 'input#_downloadable' ).prop( 'checked', false );
                $( 'input#_virtual' ).removeAttr( 'checked' );
            } else if ( 'external' === selectVal ) {
                $( 'input#_downloadable' ).prop( 'checked', false );
                $( 'input#_virtual' ).removeAttr( 'checked' );
            }
            //trigger product type change event before updating tabs display
            $( '#woocommerce-product-data' ).trigger( 'afm-product-type-changed' );

            this.updateTabsDisplay();

            $( 'ul#product_data_tabs li:visible' ).eq( 0 ).find( 'a' ).tab( 'show' );
        },
        updateTabsDisplay: function ( ) {
            var productType = this.getState( 'productType' );

            var isVirtual = $( 'input#_virtual:checked' ).length;
            var isDownloadable = $( 'input#_downloadable:checked' ).length;

            // Hide/Show all with rules.
            var hideClasses = '.hide_if_downloadable, .hide_if_virtual';
            var showClasses = '.show_if_downloadable, .show_if_virtual';

            var defaultProductTypes = JSON.parse( mvx_advance_product_params.default_product_types );
            var productTypes = JSON.parse( mvx_advance_product_params.product_types );
            // Merge defaultProductTypes into productTypes
            // This will ensure .show_if_X and .hide_if_X works even if product type X is disable from settings
            $.extend( productTypes, defaultProductTypes );

            $.each( productTypes, function ( index, value ) {
                hideClasses = hideClasses + ', .hide_if_' + index;
                showClasses = showClasses + ', .show_if_' + index;
            } );

            $( hideClasses ).show();
            $( showClasses ).hide();

            // Shows rules.
            if ( isDownloadable ) {
                $( '.show_if_downloadable' ).show();
            }
            if ( isVirtual ) {
                $( '.show_if_virtual' ).show();
            }

            $( '.show_if_' + productType ).show();

            // Hide rules.
            if ( isDownloadable ) {
                $( '.hide_if_downloadable' ).hide();
            }
            if ( isVirtual ) {
                $( '.hide_if_virtual' ).hide();
            }

            $( '.hide_if_' + productType ).hide();

            $( 'input#_manage_stock' ).change();

            // Hide empty panels/tabs after display.
            $( '.tab-pane' ).each( function () {
                //var $children = $( this ).children( '.row-padding' ).children( '.form-group-row' );
                var $children = $( this ).find( '.row-padding > div' );

                if ( 0 === $children.length ) {
                    return;
                }

                var $invisble = $children.filter( function () {
                    return 'none' === $( this ).css( 'display' );
                } );

                // Hide panel.
                if ( $invisble.length === $children.length ) {
                    var $id = $( this ).prop( 'id' );
                    $( '#product_data_tabs' ).find( 'li a[href="#' + $id + '"]' ).parent().hide();
                }
            } );
            $( '#product_data_tabs' ).trigger( 'tab-display-updated' );
        },
        showSalePriceDates: function ( ) {
            var $wrap = $( this ).closest( 'div.form-group-row' );

            $( this ).hide();
            $wrap.find( '.cancel_sale_schedule' ).show();
            $wrap.find( '.sale_price_dates_fields' ).show();

            return false;
        },
        hideSalePriceDates: function ( ) {
            var $wrap = $( this ).closest( 'div.form-group-row' );

            $( this ).hide();
            $wrap.find( '.sale_schedule' ).show();
            $wrap.find( '.sale_price_dates_fields' ).hide();
            $wrap.find( '.sale_price_dates_fields' ).find( 'input' ).val( '' );

            return false;
        },
        stockManagementPreferenceChanged: function ( ) {
            var productType = this.getState( 'productType' );
            // Get value.
            var manageStock = $( 'input#_manage_stock' ).is( ':checked' );
            //update state
            this.setState( 'manageStock', manageStock );

            if ( manageStock ) {
                $( 'div.stock_fields:not( .hide_if_' + productType + ' )' ).show();
                $( 'div.stock_status_field' ).hide();
            } else {
                $( 'div.stock_fields' ).hide();
                $( 'div.stock_status_field:not( .hide_if_' + productType + ' )' ).show();
            }
        },
        attributeController: function ( ) {
            var self = this;

            return {
                init: function ( ) {
                    var $attributes = $( '.product_attributes' ).find( '.woocommerce_attribute' ).get();

                    $( $attributes ).each( function ( index, el ) {
                        if ( $( el ).is( '.taxonomy' ) ) {
                            $( 'select.attribute_taxonomy' ).find( 'option[value="' + $( el ).data( 'taxonomy' ) + '"]' ).attr( 'disabled', 'disabled' );
                        }
                    } );
                    if ( !mvx_advance_product_params.custom_attribute ) {
                        this.customAttributeCapCheck();
                    }
                    this.initSortableComponent( );
                    //Event Listners
                    $( '#product_attributes_data' )
                        .on( 'change', 'input.attribute_name', this.changeCustomAttributeName )
                        .on( 'click', 'button.select_all_attributes', this.selectAllAttributes )
                        .on( 'click', 'button.select_no_attributes', this.removeAllAttributes )
                        .on( 'click', 'button.add_attribute', this.addAttribute.bind( this ) )
                        .on( 'click', 'a.remove_row', this.removeAttribute.bind( this ) )
                        .on( 'click', '.save_attributes', this.saveAttribute.bind( this ) )
                        .on( 'click', '.expand_all', this.expandAllAttributes )
                        .on( 'click', '.close_all', this.closeAllAttributes )
                        ;
                },
                customAttributeCapCheck: function ( ) {
                    var firstEnableOptionVal = $( 'select.attribute_taxonomy' ).children( ':not([disabled=disabled])' ).first().val();
                    if ( firstEnableOptionVal ) {
                        $( 'select.attribute_taxonomy' ).val( firstEnableOptionVal );
                    } else {
                        $( 'button.add_attribute' ).attr( 'disabled', 'disabled' );
                    }
                },
                initSortableComponent: function ( ) {
                    var ref = this;
                    var options = {
                        items: '.woocommerce_attribute',
                        cursor: 'move',
                        axis: 'y',
                        handle: '.variation-title',
                        scrollSensitivity: 40,
                        forcePlaceholderSize: true,
                        helper: 'clone',
                        opacity: 0.65,
                        placeholder: 'mvx-metabox-sortable-placeholder',
                        start: function ( event, ui ) {
                            ui.item.css( 'background-color', '#f6f6f6' );
                        },
                        stop: function ( event, ui ) {
                            ui.item.removeAttr( 'style' );
                            ref.updateRowIndices();
                        }
                    };
                    $( '.product_attributes' ).sortable( options );
                },
                changeCustomAttributeName: function ( ) {
                    $( this ).closest( '.woocommerce_attribute' ).find( 'strong.attribute_name' ).text( $( this ).val() );
                },
                selectAllAttributes: function ( ) {
                    $( this ).closest( 'td' ).find( 'select option' ).attr( 'selected', 'selected' );
                    $( this ).closest( 'td' ).find( 'select' ).change();
                    return false;
                },
                removeAllAttributes: function ( ) {
                    $( this ).closest( 'td' ).find( 'select option' ).removeAttr( 'selected' );
                    $( this ).closest( 'td' ).find( 'select' ).change();
                    return false;
                },
                updateRowIndices: function ( ) {
                    $( '.product_attributes .woocommerce_attribute' ).each( function ( index, el ) {
                        $( '.attribute_position', el ).val( parseInt( $( el ).index( '.product_attributes .woocommerce_attribute' ), 10 ) );
                    } );
                },
                addAttribute: function ( e ) {
                    var ref = this;
                    var $items = $( '.product_attributes .woocommerce_attribute' ).get();
                    var size = 0;
                    if ( $items.length > 0 ) {
                        //get the heighest attr id
                        size = $items.reduce( function ( a, b ) {
                            var mIndex = parseInt( $( b ).find( '.woocommerce_attribute_data' ).attr( 'id' ).replace( 'attribute_', '' ), 10 );
                            return ( isNaN( mIndex ) || a > mIndex ) ? a : mIndex;
                        }, 0 );
                        //next item index
                        ++size;
                    }
                    var attribute = $( 'select.attribute_taxonomy' ).val();
                    if ( attribute || mvx_advance_product_params.custom_attribute == "1" ) {
                        var $wrapper = $( '#product_attributes_data' );
                        var $attributes = $wrapper.find( '.product_attributes' );
                        var product_type = self.getState( 'productType' );
                        var data = {
                            action: 'mvx_edit_product_attribute',
                            taxonomy: attribute,
                            i: size,
                            security: mvx_advance_product_params.add_attribute_nonce
                        };

                        $wrapper.block( {
                            message: null,
                            overlayCSS: {
                                background: '#fff',
                                opacity: 0.6
                            }
                        } );

                        $.post( mvx_advance_product_params.ajax_url, data, function ( response ) {
                            if ( response != '-1' ) {
                                $attributes.append( response );

                                if ( 'variable' !== product_type ) {
                                    $attributes.find( '.enable_variation' ).hide();
                                }

                                mvxAfmLibrary.wcEnhancedSelectInit();
                                mvxAfmLibrary.qtip();
                                ref.updateRowIndices();
                                //open the added attribute in expanded view
                                $attributes.find( '.woocommerce_attribute' ).last().find( '.variation-title' ).click();

                                $( '#product_attributes_data' ).trigger( 'attribute_added' );
                            }
                            $wrapper.unblock();
                        } );
                    }

                    if ( attribute ) {
                        $( 'select.attribute_taxonomy' ).find( 'option[value="' + attribute + '"]' ).attr( 'disabled', 'disabled' );
                        if ( mvx_advance_product_params.custom_attribute == 1 ) {
                            $( 'select.attribute_taxonomy' ).val( '' );
                        } else {
                            var firstEnableOptionVal = $( 'select.attribute_taxonomy' ).children( ':not([disabled=disabled])' ).first().val();
                            if ( firstEnableOptionVal ) {
                                $( 'select.attribute_taxonomy' ).val( firstEnableOptionVal );
                            } else {
                                $( 'button.add_attribute' ).attr( 'disabled', 'disabled' );
                            }
                        }
                    }

                    return false;
                },
                removeAttribute: function ( e ) {
                    if ( window.confirm( mvx_advance_product_params.remove_attribute ) ) {
                        var $parent = $( e.target ).closest( '.woocommerce_attribute' );

                        if ( $parent.is( '.taxonomy' ) ) {
                            $( 'select.attribute_taxonomy' ).find( 'option[value="' + $parent.data( 'taxonomy' ) + '"]' ).removeAttr( 'disabled' );
                        }
                        $parent.remove();
                        this.updateRowIndices();
                    }
                    return false;
                },
                saveAttribute: function ( e ) {
                    var $wrapper = $( '#woocommerce-product-data' );
                    $wrapper.block( {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    } );

                    var data = {
                        post_id: mvx_advance_product_params.product_id,
                        product_type: self.getState( 'productType' ),
                        data: $( '.product_attributes' ).find( 'input, select, textarea' ).serialize(),
                        action: 'mvx_product_save_attributes',
                        security: mvx_advance_product_params.save_attributes_nonce
                    };

                    $.post( mvx_advance_product_params.ajax_url, data, function () {
                        // Reload variations panel.
                        var this_page = window.location.toString();
                        this_page = this_page.replace( /(?:\/#?|\/\d+\/?)?$/, '/' + mvx_advance_product_params.product_id + '/' );
                        $wrapper.unblock();
                        $wrapper.trigger('mvx_after_save_attribute_triggered');
                        // Load variations panel.
//                        $( '#variable_product_options' ).load( this_page + ' #variable_product_options_inner', function () {
//                            $( '#variable_product_options' ).trigger( 'reload' );
//                        } );
                    } );
                },
                expandAllAttributes: function ( ) {
                    $( this ).closest( '#product_attributes_data' ).find( '.mvx-metabox-wrapper > .mvx-metabox-content' ).collapse( 'show' );
                    return false;
                },
                closeAllAttributes: function ( ) {
                    $( this ).closest( '#product_attributes_data' ).find( '.mvx-metabox-wrapper > .mvx-metabox-content' ).collapse( 'hide' );
                    return false;
                }
            };
        },
        variationController: function ( ) {
            var self = this;

            var actions = null;
            var ajax = null;
            var media = null;
            var pagination = null;
            return {
                init: function ( ) {
                    actions = this.variationActionsController();
                    actions.init();
                    ajax = this.variationAjaxController();
                    ajax.init();
                    media = this.variationMediaController();
                    media.init();
                    pagination = this.variationPaginationController();
                    pagination.init();
                },
                variationActionsController: function ( ) {
                    return {
                        init: function ( ) {
                            $( '#variable_product_options' )
                                .on( 'change', 'input.variable_is_downloadable', this.variableIsDownloadable )
                                .on( 'change', 'input.variable_is_virtual', this.variableIsVirtual )
                                .on( 'change', 'input.variable_manage_stock', this.variableManageStock )
                                .on( 'click', '.woocommerce_variation span.sort.sortable-icon', this.setMenuOrder )
                                .on( 'click', '.variation-title select, .variation-title .remove_row', this.stopToggle )
                                .on( 'click', '.expand_all', this.expandAllVariations )
                                .on( 'click', '.close_all', this.closeAllVariations )
                                .on( 'reload', this.reload );

                            $( 'input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock' ).change();
                            $( '#woocommerce-product-data' ).on( 'woocommerce_variations_loaded', this.variationsLoaded );
                            $( document.body ).on( 'woocommerce_variations_added', this.variationAdded );

                        },
                        initSortableComponent: function ( ) {
                            var options = {
                                items: '.woocommerce_variation',
                                cursor: 'move',
                                axis: 'y',
                                handle: '.variation-title',
                                scrollSensitivity: 40,
                                forcePlaceholderSize: true,
                                helper: 'clone',
                                opacity: 0.65,
                                placeholder: 'variation-sortable-placeholder',
                                start: function ( event, ui ) {
                                    ui.item.css( 'background-color', '#f6f6f6' );
                                },
                                stop: function ( event, ui ) {
                                    ui.item.removeAttr( 'style' );
                                    actions.variationRowIndexes();
                                }
                            };
                            $( '.woocommerce_variations' ).sortable( options );
                        },
                        expandAllVariations: function () {
                          $( this ).closest( '#variable_product_options' ).find( '.mvx-metabox-wrapper > .mvx-metabox-content' ).collapse( 'show' );
                          return false;
                        },
                        closeAllVariations: function () {
                          $( this ).closest( '#variable_product_options' ).find( '.mvx-metabox-wrapper > .mvx-metabox-content' ).collapse( 'hide' );
                          return false;
                        },
                        reload: function ( ) {
                            ajax.loadVariations( 1 );
                            pagination.setPaginav( 0 );
                        },
                        /**
                         * Check if variation is downloadable and show/hide elements
                         */
                        variableIsDownloadable: function () {
                            $( this ).closest( '.woocommerce_variable_attributes' ).find( '.show_if_variation_downloadable' ).hide();

                            if ( $( this ).is( ':checked' ) ) {
                                $( this ).closest( '.woocommerce_variable_attributes' ).find( '.show_if_variation_downloadable' ).show();
                            }
                        },

                        /**
                         * Check if variation is virtual and show/hide elements
                         */
                        variableIsVirtual: function () {
                            $( this ).closest( '.woocommerce_variable_attributes' ).find( '.hide_if_variation_virtual' ).show();

                            if ( $( this ).is( ':checked' ) ) {
                                $( this ).closest( '.woocommerce_variable_attributes' ).find( '.hide_if_variation_virtual' ).hide();
                            }
                        },

                        /**
                         * Check if variation manage stock and show/hide elements
                         */
                        variableManageStock: function () {
                            $( this ).closest( '.woocommerce_variable_attributes' ).find( '.show_if_variation_manage_stock' ).hide();
                            $( this ).closest( '.woocommerce_variable_attributes' ).find( '.hide_if_variation_manage_stock' ).show();

                            if ( $( this ).is( ':checked' ) ) {
                                $( this ).closest( '.woocommerce_variable_attributes' ).find( '.show_if_variation_manage_stock' ).show();
                                $( this ).closest( '.woocommerce_variable_attributes' ).find( '.hide_if_variation_manage_stock' ).hide();
                            }
                        },

                        stopToggle: function ( e ) {
                            e.stopPropagation();
                        },
                        /**
                         * Run actions when variations is loaded
                         *
                         * @param {Object} event
                         * @param {Int} needsUpdate
                         */
                        variationsLoaded: function ( event, needsUpdate ) {
                            needsUpdate = needsUpdate || false;

                            var $wrapper = $( '#woocommerce-product-data' );

                            if ( !needsUpdate ) {
                                // Show/hide downloadable, virtual and stock fields
                                $( 'input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock', $wrapper ).change();
                                // Open sale schedule fields when have some sale price date
                                $( '.woocommerce_variation', $wrapper ).each( function ( index, el ) {
                                    var $el = $( el ),
                                        dateFrom = $( '.sale_price_dates_from', $el ).val(),
                                        dateTo = $( '.sale_price_dates_to', $el ).val();

                                    if ( '' !== dateFrom || '' !== dateTo ) {
                                        $( 'a.sale_schedule', $el ).click();
                                    }
                                } );
                                // Remove variation-needs-update classes
                                $( '.woocommerce_variations .variation-needs-update', $wrapper ).removeClass( 'variation-needs-update' );
                                // Disable cancel and save buttons
                                $( 'button.cancel-variation-changes, button.save-variation-changes', $wrapper ).attr( 'disabled', 'disabled' );
                            }

                            // Datepicker fields
                            self.salePriceDateFieldInit( $wrapper );
                            mvxAfmLibrary.wcEnhancedSelectInit();
                            mvxAfmLibrary.qtip();
                            // Allow sorting
                            actions.initSortableComponent();
                            //Make download files sortable
                            $( '#woocommerce-product-data' ).trigger( 'downloadable_file_fields_inserted' );
                        },
                        /**
                         * Run actions when added a variation
                         *
                         * @param {Object} event
                         * @param {Int} qty
                         */
                        variationAdded: function ( event, qty ) {
                            if ( 1 === qty ) {
                                actions.variationsLoaded( null, true );
                            }
                        },

                        /**
                         * Lets the user manually input menu order to move items around pages
                         */
                        setMenuOrder: function ( event ) {
                            event.preventDefault();
                            var $menuOrder = $( this ).closest( '.woocommerce_variation' ).find( '.variation_menu_order' );
                            var value = window.prompt( mvx_advance_product_params.i18n_enter_menu_order, $menuOrder.val() );
                            var index = parseInt( value, 10 );
                            if ( !isNaN( index ) ) {
                                // Set value, save changes and reload view
                                $menuOrder.val( index ).change();
                                ajax.saveVariations();
                            }
                            //stop collapsible elements toggle
                            return false;
                        },
                        /**
                         * Set menu order
                         */
                        variationRowIndexes: function () {
                            var $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
                                currentPage = parseInt( $wrapper.attr( 'data-page' ), 10 ),
                                offset = parseInt( ( currentPage - 1 ) * mvx_advance_product_params.variations_per_page, 10 );

                            $( '.woocommerce_variations .woocommerce_variation' ).each( function ( index, el ) {
                                $( '.variation_menu_order', el ).val( parseInt( $( el ).index( '.woocommerce_variations .woocommerce_variation' ), 10 ) + 1 + offset ).change();
                            } );
                        }
                    };
                },
                variationAjaxController: function ( ) {
                    return {
                        init: function ( ) {
                            $( 'li.variations_tab a' ).on( 'click', this.initialLoad );

                            $( '#variable_product_options' )
                                .on( 'click', 'button.save-variation-changes', this.saveVariations )
                                .on( 'click', 'button.cancel-variation-changes', this.cancelVariations )
                                .on( 'click', '.remove_variation', this.removeVariation );

                            $( document.body )
                                .on( 'change', '#variable_product_options .woocommerce_variations :input', this.inputChanged )
                                .on( 'change', '.variations-defaults select', this.defaultsChanged );

                            $( 'form#mvx-frontend-dashboard-add-product' ).on( 'submit', this.saveOnSubmit );
                            $( '.collapsable-component-wrapper' ).on( 'click', 'a.do_variation_action', this.doVariationAction );
                        },
                        /**
                         * Check if have some changes before leave the page
                         *
                         * @return {Bool}
                         */
                        checkForChanges: function () {

                            var needUpdate = $( '#variable_product_options' ).find( '.woocommerce_variations .variation-needs-update' );

                            if ( 0 < needUpdate.length ) {
                                if ( window.confirm( mvx_advance_product_params.i18n_edited_variations ) ) {
                                    this.saveChanges();
                                } else {
                                    needUpdate.removeClass( 'variation-needs-update' );
                                    return false;
                                }
                            }

                            return true;
                        },
                        /**
                         * Block edit screen
                         */
                        block: function () {
                            $( '#woocommerce-product-data' ).block( {
                                message: null,
                                overlayCSS: {
                                    background: '#fff',
                                    opacity: 0.6
                                }
                            } );
                        },

                        /**
                         * Unblock edit screen
                         */
                        unblock: function () {
                            $( '#woocommerce-product-data' ).unblock();
                        },

                        /**
                         * Initial load variations
                         *
                         * @return {Bool}
                         */
                        initialLoad: function () {
                            if ( 0 === $( '#variable_product_options' ).find( '.woocommerce_variations .woocommerce_variation' ).length ) {
                                pagination.goToPage();
                            }
                        },
                        /**
                         * Load variations via Ajax
                         *
                         * @param {Int} page (default: 1)
                         * @param {Int} perPage (default: 10)
                         */
                        loadVariations: function ( page, perPage ) {
                            page = page || 1;
                            perPage = perPage || mvx_advance_product_params.variations_per_page;

                            var $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' );

                            ajax.block();

                            $.ajax( {
                                url: mvx_advance_product_params.ajax_url,
                                data: {
                                    action: 'mvx_frontend_dashboard_load_variations',
                                    security: mvx_advance_product_params.load_variations_nonce,
                                    product_id: mvx_advance_product_params.product_id,
                                    attributes: $wrapper.data( 'attributes' ),
                                    page: page,
                                    per_page: perPage
                                },
                                type: 'POST',
                                success: function ( response ) {
                                    $wrapper.empty().append( response ).attr( 'data-page', page );

                                    $( '#woocommerce-product-data' ).trigger( 'woocommerce_variations_loaded' );

                                    ajax.unblock();
                                }
                            } );
                        },
                        /**
                         * Ger variations fields and convert to object
                         *
                         * @param  {Object} fields
                         *
                         * @return {Object}
                         */
                        getVariationsFields: function ( fields ) {
                            var data = $( ':input', fields ).serializeJSON();

                            $( '.variations-defaults select' ).each( function ( index, element ) {
                                var select = $( element );
                                data[ select.attr( 'name' ) ] = select.val();
                            } );

                            return data;
                        },
                        /**
                         * Save variations changes
                         *
                         * @param {Function} callback Called once saving is complete
                         */
                        saveChanges: function ( callback ) {
                            var wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
                                needUpdate = $( '.variation-needs-update', wrapper ),
                                data = { };

                            // Save only with products need update.
                            if ( 0 < needUpdate.length ) {
                                ajax.block();

                                data = this.getVariationsFields( needUpdate );
                                data.action = 'woocommerce_save_variations';
                                data.security = mvx_advance_product_params.save_variations_nonce;
                                data.product_id = mvx_advance_product_params.product_id;
                                data['product-type'] = self.getState( 'productType' );

                                $.ajax( {
                                    url: mvx_advance_product_params.ajax_url,
                                    data: data,
                                    type: 'POST',
                                    success: function ( response ) {
                                        // Allow change page, delete and add new variations
                                        needUpdate.removeClass( 'variation-needs-update' );
                                        $( 'button.cancel-variation-changes, button.save-variation-changes' ).attr( 'disabled', 'disabled' );

                                        $( '#woocommerce-product-data' ).trigger( 'woocommerce_variations_saved' );

                                        if ( typeof callback === 'function' ) {
                                            callback( response );
                                        }

                                        ajax.unblock();
                                    }
                                } );
                            }
                        },
                        /**
                         * Save variations
                         *
                         * @return {Bool}
                         */
                        saveVariations: function () {
                            $( '#variable_product_options' ).trigger( 'woocommerce_variations_save_variations_button' );

                            ajax.saveChanges( function ( error ) {
                                var $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
                                    current = $wrapper.attr( 'data-page' );

                                $( '#variable_product_options' ).find( '#woocommerce_errors' ).remove();

                                if ( error ) {
                                    $wrapper.before( error );
                                }

                                $( '.variations-defaults select' ).each( function () {
                                    $( this ).attr( 'data-current', $( this ).val() );
                                } );

                                pagination.goToPage( current );
                            } );

                            return false;
                        },
                        /**
                         * Save on post form submit
                         */
                        saveOnSubmit: function ( e ) {
                            var needUpdate = $( '#variable_product_options' ).find( '.woocommerce_variations .variation-needs-update' );

                            if ( 0 < needUpdate.length ) {
                                e.preventDefault();
                                $( '#variable_product_options' ).trigger( 'woocommerce_variations_save_variations_on_submit' );
                                ajax.saveChanges( ajax.saveOnSubmitDone );
                            }
                        },

                        /**
                         * After saved, continue with form submission
                         */
                        saveOnSubmitDone: function () {
                            $( 'form#post' ).submit();
                        },
                        /**
                         * Discart changes.
                         *
                         * @return {Bool}
                         */
                        cancelVariations: function () {
                            var current = parseInt( $( '#variable_product_options' ).find( '.woocommerce_variations' ).attr( 'data-page' ), 10 );

                            $( '#variable_product_options' ).find( '.woocommerce_variations .variation-needs-update' ).removeClass( 'variation-needs-update' );
                            $( '.variations-defaults select' ).each( function () {
                                $( this ).val( $( this ).attr( 'data-current' ) );
                            } );

                            pagination.goToPage( current );

                            return false;
                        },
                        /**
                         * Add variation
                         *
                         * @return {Bool}
                         */
                        addVariation: function () {
                            this.block();

                            var data = {
                                action: 'mvx_frontend_dashboard_add_variation',
                                post_id: mvx_advance_product_params.product_id,
                                loop: $( '.woocommerce_variation' ).length,
                                security: mvx_advance_product_params.add_variation_nonce
                            };

                            $.post( mvx_advance_product_params.ajax_url, data, function ( response ) {
                                var variation = $( response );
                                variation.addClass( 'variation-needs-update' );

                                $( '#variable_product_options' ).find( '.woocommerce_variations' ).prepend( variation );
                                $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );

                                $( '#variable_product_options' ).trigger( 'woocommerce_variations_added', 1 );
                                ajax.unblock();
                            } );

                            return false;
                        },
                        /**
                         * Remove variation
                         *
                         * @return {Bool}
                         */
                        removeVariation: function () {
                            ajax.checkForChanges();

                            if ( window.confirm( mvx_advance_product_params.i18n_remove_variation ) ) {
                                var variation = $( this ).attr( 'rel' ),
                                    variationIds = [ ],
                                    data = {
                                        action: 'woocommerce_remove_variations'
                                    };

                                ajax.block();

                                if ( 0 < variation ) {
                                    variationIds.push( variation );

                                    data.variation_ids = variationIds;
                                    data.security = mvx_advance_product_params.delete_variations_nonce;

                                    $.post( mvx_advance_product_params.ajax_url, data, function () {
                                        var $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
                                            currentPage = parseInt( $wrapper.attr( 'data-page' ), 10 ),
                                            totalPages = Math.ceil( ( parseInt( $wrapper.attr( 'data-total' ), 10 ) - 1 ) / mvx_advance_product_params.variations_per_page ),
                                            page = 1;

                                        $( '#woocommerce-product-data' ).trigger( 'woocommerce_variations_removed' );

                                        if ( currentPage === totalPages || currentPage <= totalPages ) {
                                            page = currentPage;
                                        } else if ( currentPage > totalPages && 0 !== totalPages ) {
                                            page = totalPages;
                                        }

                                        pagination.goToPage( page, -1 );
                                    } );

                                } else {
                                    ajax.unblock();
                                }
                            }

                            return false;
                        },

                        /**
                         * Link all variations (or at least try :p)
                         *
                         * @return {Bool}
                         */
                        linkAllVariations: function () {
                            this.checkForChanges();

                            if ( window.confirm( mvx_advance_product_params.i18n_link_all_variations ) ) {
                                this.block();

                                var data = {
                                    action: 'woocommerce_link_all_variations',
                                    post_id: mvx_advance_product_params.product_id,
                                    security: mvx_advance_product_params.link_variation_nonce
                                };

                                $.post( mvx_advance_product_params.ajax_url, data, function ( response ) {
                                    var count = parseInt( response, 10 );

                                    if ( 1 === count ) {
                                        window.alert( count + ' ' + mvx_advance_product_params.i18n_variation_added );
                                    } else if ( 0 === count || count > 1 ) {
                                        window.alert( count + ' ' + mvx_advance_product_params.i18n_variations_added );
                                    } else {
                                        window.alert( mvx_advance_product_params.i18n_no_variations_added );
                                    }

                                    if ( count > 0 ) {
                                        pagination.goToPage( 1, count );
                                        $( '#variable_product_options' ).trigger( 'woocommerce_variations_added', count );
                                    } else {
                                        ajax.unblock();
                                    }
                                } );
                            }

                            return false;
                        },

                        /**
                         * Add new class when have changes in some input
                         */
                        inputChanged: function () {
                            $( this )
                                .closest( '.woocommerce_variation' )
                                .addClass( 'variation-needs-update' );

                            $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );

                            $( '#variable_product_options' ).trigger( 'woocommerce_variations_input_changed' );
                        },

                        /**
                         * Added new .variation-needs-update class when defaults is changed
                         */
                        defaultsChanged: function () {
                            $( this )
                                .closest( '#variable_product_options' )
                                .find( '.woocommerce_variation:first' )
                                .addClass( 'variation-needs-update' );

                            $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );

                            $( '#variable_product_options' ).trigger( 'woocommerce_variations_defaults_changed' );
                        },

                        /**
                         * Actions
                         */
                        doVariationAction: function () {
                            var doVariationAction = $( 'select.variation_actions' ).val(),
                                data = { },
                                changes = 0,
                                value;

                            switch ( doVariationAction ) {
                                case 'add_variation' :
                                    ajax.addVariation();
                                    return;
                                case 'link_all_variations' :
                                    ajax.linkAllVariations();
                                    return;
                                case 'delete_all' :
                                    if ( window.confirm( mvx_advance_product_params.i18n_delete_all_variations ) ) {
                                        if ( window.confirm( mvx_advance_product_params.i18n_last_warning ) ) {
                                            data.allowed = true;
                                            changes = parseInt( $( '#variable_product_options' ).find( '.woocommerce_variations' ).attr( 'data-total' ), 10 ) * -1;
                                        }
                                    }
                                    break;
                                case 'variable_regular_price_increase' :
                                case 'variable_regular_price_decrease' :
                                case 'variable_sale_price_increase' :
                                case 'variable_sale_price_decrease' :
                                    value = window.prompt( mvx_advance_product_params.i18n_enter_a_value_fixed_or_percent );

                                    if ( value !== null ) {
                                        if ( value.indexOf( '%' ) >= 0 ) {
                                            data.value = accounting.unformat( value.replace( /\%/, '' ), mvx_advance_product_params.mon_decimal_point ) + '%';
                                        } else {
                                            data.value = accounting.unformat( value, mvx_advance_product_params.mon_decimal_point );
                                        }
                                    } else {
                                        return;
                                    }
                                    break;
                                case 'variable_regular_price' :
                                case 'variable_sale_price' :
                                case 'variable_stock' :
                                case 'variable_weight' :
                                case 'variable_length' :
                                case 'variable_width' :
                                case 'variable_height' :
                                case 'variable_download_limit' :
                                case 'variable_download_expiry' :
                                    value = window.prompt( mvx_advance_product_params.i18n_enter_a_value );

                                    if ( value !== null ) {
                                        data.value = value;
                                    } else {
                                        return;
                                    }
                                    break;
                                case 'variable_sale_schedule' :
                                    data.date_from = window.prompt( mvx_advance_product_params.i18n_scheduled_sale_start );
                                    data.date_to = window.prompt( mvx_advance_product_params.i18n_scheduled_sale_end );

                                    if ( null === data.date_from ) {
                                        data.date_from = false;
                                    }

                                    if ( null === data.date_to ) {
                                        data.date_to = false;
                                    }

                                    if ( false === data.date_to && false === data.date_from ) {
                                        return;
                                    }
                                    break;
                                default :
                                    $( 'select.variation_actions' ).trigger( doVariationAction );
                                    data = $( 'select.variation_actions' ).triggerHandler( doVariationAction + '_ajax_data', data );
                                    break;
                            }

                            if ( 'delete_all' === doVariationAction && data.allowed ) {
                                $( '#variable_product_options' ).find( '.variation-needs-update' ).removeClass( 'variation-needs-update' );
                            } else {
                                ajax.checkForChanges();
                            }

                            ajax.block();

                            $.ajax( {
                                url: mvx_advance_product_params.ajax_url,
                                data: {
                                    action: 'woocommerce_bulk_edit_variations',
                                    security: mvx_advance_product_params.bulk_edit_variations_nonce,
                                    product_id: mvx_advance_product_params.product_id,
                                    product_type: self.getState( 'productType' ),
                                    bulk_action: doVariationAction,
                                    data: data
                                },
                                type: 'POST',
                                success: function () {
                                    pagination.goToPage( 1, changes );
                                }
                            } );
                        }
                    };
                },
                variationMediaController: function ( ) {
                    /**
                     * wp.media frame object
                     *
                     * @type {Object}
                     */
                    var variableImageFrame = null;

                    /**
                     * Variation image ID
                     *
                     * @type {Int}
                     */
                    var settingVariationImageId = null;

                    /**
                     * Variation image object
                     *
                     * @type {Object}
                     */
                    var settingVariationImage = null;

                    /**
                     * wp.media post ID
                     *
                     * @type {Int}
                     */
                    var wpMediaPostId = wp.media.model.settings.post.id;
                    return {
                        init: function ( ) {
                            $( '#variable_product_options' ).on( 'click', '.upload_image_button', this.addImage );
                            $( 'a.add_media' ).on( 'click', this.restoreWPMediaPostId );
                        },
                        addImage: function ( event ) {
                            var $button = $( this ),
                                postId = $button.attr( 'rel' ),
                                $parent = $button.closest( '.upload_image' );
                            settingVariationImage = $parent;
                            settingVariationImageId = postId;

                            event.preventDefault();

                            if ( $button.is( '.remove' ) ) {

                                $( '.upload_image_id', settingVariationImage ).val( '' ).change();
                                settingVariationImage.find( 'img' ).eq( 0 ).attr( 'src', mvx_advance_product_params.woocommerce_placeholder_img_src );
                                settingVariationImage.find( '.upload_image_button' ).removeClass( 'remove' );

                            } else {

                                // If the media frame already exists, reopen it.
                                if ( variableImageFrame ) {
                                    variableImageFrame.uploader.uploader.param( 'post_id', settingVariationImageId );
                                    variableImageFrame.open();
                                    return;
                                } else {
                                    wp.media.model.settings.post.id = settingVariationImageId;
                                }

                                // Create the media frame.
                                variableImageFrame = wp.media.frames.variable_image = wp.media( {
                                    // Set the title of the modal.
                                    title: mvx_advance_product_params.i18n_choose_image,
                                    button: {
                                        text: mvx_advance_product_params.i18n_set_image
                                    },
                                    states: [
                                        new wp.media.controller.Library( {
                                            title: mvx_advance_product_params.i18n_choose_image,
                                            filterable: 'all'
                                        } )
                                    ]
                                } );

                                // When an image is selected, run a callback.
                                variableImageFrame.on( 'select', function () {

                                    var attachment = variableImageFrame.state().get( 'selection' ).first().toJSON(),
                                        url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                                    $( '.upload_image_id', settingVariationImage ).val( attachment.id ).change();
                                    settingVariationImage.find( '.upload_image_button' ).addClass( 'remove' );
                                    settingVariationImage.find( 'img' ).eq( 0 ).attr( 'src', url );

                                    wp.media.model.settings.post.id = wpMediaPostId;
                                } );

                                // Finally, open the modal.
                                variableImageFrame.open();
                            }
                        },

                        /**
                         * Restore wp.media post ID.
                         */
                        restoreWPMediaPostId: function () {
                            wp.media.model.settings.post.id = wpMediaPostId;
                        }
                    };
                },
                variationPaginationController: function ( ) {
                    return {
                        init: function ( ) {
                            $( document.body )
                                .on( 'woocommerce_variations_added', this.updateSingleQuantity )
                                .on( 'change', '.variations-pagenav .page-selector', this.pageSelector )
                                .on( 'click', '.variations-pagenav .first-page', this.firstPage )
                                .on( 'click', '.variations-pagenav .prev-page', this.prevPage )
                                .on( 'click', '.variations-pagenav .next-page', this.nextPage )
                                .on( 'click', '.variations-pagenav .last-page', this.lastPage )
                                ;
                        },
                        /**
                         * Set variations count
                         *
                         * @param {Int} qty
                         *
                         * @return {Int}
                         */
                        updateVariationsCount: function ( qty ) {
                            var $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
                                total = parseInt( $wrapper.attr( 'data-total' ), 10 ) + qty,
                                $displayingNum = $( '.variations-pagenav .displaying-num' );

                            // Set the new total of variations
                            $wrapper.attr( 'data-total', total );

                            if ( 1 === total ) {
                                $displayingNum.text( mvx_advance_product_params.i18n_variation_count_single.replace( '%qty%', total ) );
                            } else {
                                $displayingNum.text( mvx_advance_product_params.i18n_variation_count_plural.replace( '%qty%', total ) );
                            }

                            return total;
                        },
                        /**
                         * Update variations quantity when add a new variation
                         *
                         * @param {Object} event
                         * @param {Int} qty
                         */
                        updateSingleQuantity: function ( event, qty ) {
                            if ( 1 === qty ) {
                                var $varPageNav = $( '.variations-pagenav' );

                                pagination.updateVariationsCount( qty );

                                if ( $varPageNav.is( ':hidden' ) ) {
                                    $( 'option, optgroup', '.variation_actions' ).show();
                                    $( '.variation_actions' ).val( 'add_variation' );
                                    $( '#variable_product_options' ).find( '.toolbar' ).show();
                                    $varPageNav.show();
                                    $( '.pagination-links', $varPageNav ).hide();
                                }
                            }
                        },
                        /**
                         * Set the pagenav fields
                         *
                         * @param {Int} qty
                         */
                        setPaginav: function ( qty ) {
                            var $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
                                newQty = this.updateVariationsCount( qty ),
                                $toolbar = $( '#variable_product_options' ).find( '.toolbar' ),
                                $variationAction = $( '.variation_actions' ),
                                $pageNav = $( '.variations-pagenav' ),
                                $displayingLinks = $( '.pagination-links', $pageNav ),
                                totalPages = Math.ceil( newQty / mvx_advance_product_params.variations_per_page ),
                                options = '';

                            // Set the new total of pages
                            $wrapper.attr( 'data-total_pages', totalPages );

                            $( '.total-pages', $pageNav ).text( totalPages );

                            // Set the new pagenav options
                            for ( var i = 1; i <= totalPages; i++ ) {
                                options += '<option value="' + i + '">' + i + '</option>';
                            }

                            $( '.page-selector', $pageNav ).empty().html( options );

                            // Show/hide pagenav
                            if ( 0 === newQty ) {
                                $toolbar.not( '.toolbar-top, .toolbar-buttons' ).hide();
                                $pageNav.hide();
                                $( 'option, optgroup', $variationAction ).hide();
                                $( '.variation_actions' ).val( 'add_variation' );
                                $( 'option[data-global="true"]', $variationAction ).show();

                            } else {
                                $toolbar.show();
                                $pageNav.show();
                                $( 'option, optgroup', $variationAction ).show();
                                $( '.variation_actions' ).val( 'add_variation' );

                                // Show/hide links
                                if ( 1 === totalPages ) {
                                    $displayingLinks.hide();
                                } else {
                                    $displayingLinks.show();
                                }
                            }
                        },
                        /**
                         * Check button if enabled and if don't have changes
                         *
                         * @return {Bool}
                         */
                        checkIsEnabled: function ( current ) {
                            return !$( current ).hasClass( 'disabled' );
                        },
                        /**
                         * Change "disabled" class on pagenav
                         */
                        changeClasses: function ( selected, total ) {
                            var $firstPage = $( '.variations-pagenav .first-page' ),
                                $prevPage = $( '.variations-pagenav .prev-page' ),
                                $nextPage = $( '.variations-pagenav .next-page' ),
                                $lastPage = $( '.variations-pagenav .last-page' );

                            if ( 1 === selected ) {
                                $firstPage.addClass( 'disabled' );
                                $prevPage.addClass( 'disabled' );
                            } else {
                                $firstPage.removeClass( 'disabled' );
                                $prevPage.removeClass( 'disabled' );
                            }

                            if ( total === selected ) {
                                $nextPage.addClass( 'disabled' );
                                $lastPage.addClass( 'disabled' );
                            } else {
                                $nextPage.removeClass( 'disabled' );
                                $lastPage.removeClass( 'disabled' );
                            }
                        },
                        /**
                         * Set page
                         */
                        setPage: function ( page ) {
                            $( '.variations-pagenav .page-selector' ).val( page ).first().change();
                        },
                        /**
                         * Navigate on variations pages
                         *
                         * @param {Int} page
                         * @param {Int} qty
                         */
                        goToPage: function ( page, qty ) {
                            page = page || 1;
                            qty = qty || 0;
                            this.setPaginav( qty );
                            this.setPage( page );
                        },
                        /**
                         * Paginav pagination selector
                         */
                        pageSelector: function () {
                            var selected = parseInt( $( this ).val(), 10 ),
                                $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' );

                            $( '.variations-pagenav .page-selector' ).val( selected );
                            ajax.checkForChanges();
                            pagination.changeClasses( selected, parseInt( $wrapper.attr( 'data-total_pages' ), 10 ) );
                            ajax.loadVariations( selected );
                        },
                        /**
                         * Go to first page
                         *
                         * @return {Bool}
                         */
                        firstPage: function () {
                            if ( pagination.checkIsEnabled( this ) ) {
                                pagination.setPage( 1 );
                            }

                            return false;
                        },

                        /**
                         * Go to previous page
                         *
                         * @return {Bool}
                         */
                        prevPage: function () {
                            if ( pagination.checkIsEnabled( this ) ) {
                                var $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
                                    prevPage = parseInt( $wrapper.attr( 'data-page' ), 10 ) - 1,
                                    newPage = ( 0 < prevPage ) ? prevPage : 1;

                                pagination.setPage( newPage );
                            }

                            return false;
                        },

                        /**
                         * Go to next page
                         *
                         * @return {Bool}
                         */
                        nextPage: function () {
                            if ( pagination.checkIsEnabled( this ) ) {
                                var $wrapper = $( '#variable_product_options' ).find( '.woocommerce_variations' ),
                                    totalPages = parseInt( $wrapper.attr( 'data-total_pages' ), 10 ),
                                    nextPage = parseInt( $wrapper.attr( 'data-page' ), 10 ) + 1,
                                    newPage = ( totalPages >= nextPage ) ? nextPage : totalPages;

                                pagination.setPage( newPage );
                            }

                            return false;
                        },

                        /**
                         * Go to last page
                         *
                         * @return {Bool}
                         */
                        lastPage: function () {
                            if ( pagination.checkIsEnabled( this ) ) {
                                var lastPage = $( '#variable_product_options' ).find( '.woocommerce_variations' ).attr( 'data-total_pages' );

                                pagination.setPage( lastPage );
                            }

                            return false;
                        }
                    };
                }
            };
        },
        dismissNotice: function ( ) {
            $( this ).parent().remove();
        },
        getTinymceContent: function ( editor_id ) {
            if ( $( '#wp-' + editor_id + '-wrap' ).hasClass( 'tmce-active' ) && typeof tinyMCE !== 'undefined' && tinyMCE.get( editor_id ) ) {
                return tinyMCE.get( editor_id ).getContent();
            } else {
                return $( 'textarea#' + editor_id ).val();
            }
        },
        saveProduct: function ( e ) { 
            $( 'form#mvx-edit-product-form' ).trigger( 'before_product_save' );
            var status = ( e.target.id === 'mvx_frontend_dashboard_product_submit' ) ? 'publish' : ( e.target.id === 'mvx_frontend_dashboard_product_draft' ) ? 'draft' : '';
            $( 'input:hidden[name="status"]' ).val( status );
            $( 'textarea#product_description' ).val( this.getTinymceContent( 'product_description' ) );
            $( 'textarea#product_excerpt' ).val( this.getTinymceContent( 'product_excerpt' ) );
            return true;
        },
        taxInputChanged: function( e ) { 
            var ischecked= $( this ).is( ':checked' );
            if( !ischecked ) {
                $( this ).prop( 'checked', false );
            }
        }
    };
} )( jQuery );
jQuery( function () {
    mvxAfmProductEditor.init();
} );
