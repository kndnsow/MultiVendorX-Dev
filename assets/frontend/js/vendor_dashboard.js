jQuery( document ).ready( function ( $ ) {
    //checkbox_custome_design();
    $( ".hasmenu > a" ).click( function ( e ) {

        if ( $( this ).attr( 'href' ) == '#' )
            e.preventDefault();

        if ( !$( this ).hasClass( "active" ) ) {
            $( ".hasmenu" ).find( "a" ).removeClass( "active" );
            $( this ).addClass( "active" );
            if ( !$( this ).closest( "ul" ).hasClass( 'submenu' ) ) {
                $( ".hasmenu" ).find( "ul" ).slideUp();
            }
            $( this ).next( "ul" ).slideDown();
        } else {
            $( this ).removeClass( "active" );
            $( '.hasmenu' ).find( "ul" ).slideUp();
        }

    } );

    $( ".mvx_tab" ).tabs();

    var sideslider = $( '[data-toggle=collapse-side]' );
    var sel = sideslider.attr( 'data-target' );
    var sel2 = sideslider.attr( 'data-target-2' );
    sideslider.click( function ( event ) {
        // $(this).toggleClass('collapsed');
        $( sel ).toggleClass( 'in' );
        $( sel2 ).toggleClass( 'out' );
        $( '.side-collapse-container' ).toggleClass( 'large' );

        if ( $( window ).width() < 768 ) {
            $( '#page-wrapper' ).toggleClass( 'overlay' );
        }
    } );

    // sidebar menu 
    sibdebarToggle();

    mapNavWrap();

    $( document ).on( 'change', '#mvx_visitor_stats_date_filter', function () {
        mapNavWrap();
    } ); 

    // table responsive 
        
    $( ".responsive-table" ).each( function ( index ) {
        
        var getTh = $( this ).find( 'thead th' );
        var getTr = $( this ).find( 'tbody tr' );
        var getTd = $( this ).find( 'tbody td' );

        for ( var tr = 0; tr < getTr.length; tr++ ) {

            for ( var i = 0; i < getTh.length; i++ ) {
                $( getTd[i + tr * getTh.length] ).attr( 'data-th', $( getTh[i] ).html() );
            }

        }
    } ); 


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

    // category scroller
    $(".mvx-product-cat-level, .mvxCustomScroller").mCustomScrollbar();

    // Wpml duplicate product js
    if( $( '#mvx_product_translations' ).length > 0 ) {
        var data = {
            action   : 'mvx_product_translations',
            proid    : $('#mvx_product_translations').data('product_id'),
            security      : mvx_advance_product_params.dashboard_nonce
        }   
        jQuery.ajax({
            type:       'POST',
            url: mvx_advance_product_params.ajax_url,
            data: data,
            success:    function(response) {
                jQuery('#mvx_product_translations').html(response);
                init_mvx_product_new_translation();
            }
        });
    }
    
    function init_mvx_product_new_translation() {
        $('.mvx_product_new_translation').click(function(e) {
            e.preventDefault();
            jQuery('.add-product-wrapper').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            var data = {
                action        : 'mvx_product_new_translation',
                proid         : $(this).data('proid'),
                trid          : $(this).data('trid'),
                source_lang   : $(this).data('source_lang'),
                lang          : $(this).data('lang'),
                security      : mvx_advance_product_params.dashboard_nonce
            }   
            $.ajax({
                type:       'POST',
                url: mvx_advance_product_params.ajax_url,
                data: data,
                success:    function(response) {
                    if(response) {
                        $response_json = $.parseJSON(response);
                        if($response_json.status) {
                            if( $response_json.redirect ) window.location = $response_json.redirect;    
                        }
                        jQuery('.add-product-wrapper').unblock();
                    }
                }
            });
            return false;
        });
    }

} );


var isMobile = {
    Android: function () {
        return navigator.userAgent.match( /Android/i );
    },
    BlackBerry: function () {
        return navigator.userAgent.match( /BlackBerry/i );
    },
    iOS: function () {
        return navigator.userAgent.match( /iPhone|iPad|iPod/i );
    },
    Opera: function () {
        return navigator.userAgent.match( /Opera Mini/i );
    },
    Windows: function () {
        return navigator.userAgent.match( /IEMobile/i );
    },
    any: function () {
        return ( isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows() );
    } };

function mapNavWrap() {
    jQuery( '.jqvmap-zoomin, .jqvmap-zoomout' ).wrapAll( '<div class="map-nav"></div>' );
}



jQuery( document ).ready( function ( $ ) {
    var window_width = $( window ).width();
    if ( window_width <= 640 ) {
        var active_menu = $( ".mvx_main_menu ul li.hasmenu>a.active" );
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find( 'ul.submenu' );
        submenu.hide();
        active_menu.removeClass( 'active' );
        if ( !active_menu.hasClass( 'responsive_active' ) ) {
            active_menu.addClass( 'responsive_active' );
        }
    }
} );
jQuery( window ).resize( function () {
    var $ = jQuery.noConflict();
    var window_width = jQuery( window ).width();
    if ( window_width <= 640 ) {
        var active_menu = jQuery( ".mvx_main_menu ul li.hasmenu>a.active" );
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find( 'ul.submenu' );
        submenu.hide();
        active_menu.removeClass( 'active' );
        if ( !active_menu.hasClass( 'responsive_active' ) ) {
            active_menu.addClass( 'responsive_active' );
        }
    } else {
        var active_menu = jQuery( ".mvx_main_menu ul li.hasmenu>a.responsive_active" );
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find( 'ul.submenu' );
        submenu.show();
        active_menu.removeClass( 'responsive_active' );
        if ( !active_menu.hasClass( 'active' ) ) {
            active_menu.addClass( 'active' );
        }
    }

    // sidebar menu
    jQuery( function ( $ ) {
        if ( !isMobile.any() ) {
            sibdebarToggle();
        }
    } );


} );

// sidebar menu
function sibdebarToggle() {
    jQuery( '#page-wrapper' ).on( 'click', function ( event ) {
        if ( !jQuery( '.navbar-default.sidebar' ).hasClass( 'in' ) && jQuery( window ).width() <= 768 ) {
            jQuery( '.side-collapse-container' ).toggleClass( 'large' );
            jQuery( '.navbar-default.sidebar' ).addClass( 'in' );
            jQuery( '#page-wrapper' ).removeClass( 'overlay' );
        }
    } );

    if ( jQuery( window ).width() <= 768 ) {
        jQuery( '.sidebar' ).addClass( 'in' );
        jQuery( '#page-wrapper' ).removeClass( 'overlay' );
        jQuery( '.side-collapse-container' ).removeClass( 'large' );
    } else {
        jQuery( '.sidebar' ).removeClass( 'in' );
        jQuery( '#page-wrapper' ).removeClass( 'overlay' );
        jQuery( '.side-collapse-container' ).removeClass( 'large' );
    }
}


function toggleAllCheckBox( self, tableId ) {
    if ( jQuery( self ).is( ':checked' ) ) {
        jQuery( '#' + tableId ).find( 'tbody tr td input[type=checkbox]' ).not( ":disabled" ).prop( 'checked', true );
    } else {
        jQuery( '#' + tableId ).find( 'tbody tr td input[type=checkbox]' ).prop( 'checked', false );
    }
}
