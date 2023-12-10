'use strict';
//global library accessable from all other js
var mvxAfmstoreEditor = ( function ( $ ) {
    var media = null;
    return {
        init: function ( ) {
            media = this.mediaController();
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

            var $imageGalleryIDs = $( '#slider_image_gallery' );

            return {
                init: function ( ) {
                    $( '#slider_images_container' )
                        .on( 'click', '.add_slider_images a', this.addGalleryImages )
                        .on( 'click', '.slider_images a.delete', this.removeGalleryImage );

                    this.initGalleryImagesSort();
                },
                addGalleryImages: function ( event ) {
                    var $el = $( this );
                    var $productImages = $( '#slider_images_container' ).find( 'ul.slider_images' );

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

                    $( '#slider_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function () {
                        var attachmentID = $( this ).attr( 'data-attachment_id' );
                        attachmentIDs = attachmentIDs + attachmentID + ',';
                    } );

                    $imageGalleryIDs.val( attachmentIDs );

                    return false;
                },
                initGalleryImagesSort: function ( ) {
                    // Image ordering.
                    $( '#slider_images_container' )
                        .find( 'ul.slider_images' )
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

                                $( '#slider_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function () {
                                    var attachmentID = $( this ).attr( 'data-attachment_id' );
                                    attachmentIDs = attachmentIDs + attachmentID + ',';
                                } );

                                $imageGalleryIDs.val( attachmentIDs );
                            }
                        } );
                }
            };
        },
        setupEnvironment: function ( ) {
            media.init();           
        },
    };
} )( jQuery );
jQuery( function () {
    mvxAfmstoreEditor.init();
} );
