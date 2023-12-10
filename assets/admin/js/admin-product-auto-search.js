/* global ajaxurl, mvx_admin_product_auto_search_js_params */

(function ($) {
    var block = function( $node ) {
        if ( ! is_blocked( $node ) ) {
            $node.addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );
        }
    };
    var is_blocked = function( $node ) {
        return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
    };

    var unblock = function( $node ) {
        $node.removeClass( 'processing' ).unblock();
    };
    /**
     * single product multiple vendor dropdown directive
     * @param object options
     */
    var keyup_timeout;
    $.fn.singleProductMulipleVendor = function (options) {
        $_this = $(this);
        // This is the easiest way to have default options.
        var settings = $.extend({
            // These are the defaults.
            ajaxurl : mvx_admin_product_auto_search_js_params.ajax_url,
            is_admin : true
        }, options);
        var title = this[0].value;
        if (title !== undefined && title !== '') {
            title = title.replace('(Copy)', '');
            this[0].value = title.trim();
        }
        var container = document.createElement('div'); //'<div id="mvx_auto_suggest_product_title"></div>';
        container.setAttribute('id', 'mvx_auto_suggest_product_title');
        this.after(container);
        this.keyup(function () {
            var strtitle = this.value;
            if (strtitle.length >= 3) {
                clearTimeout(keyup_timeout);
                keyup_timeout = setTimeout(function(){
                    block($_this.parents('form'));
                    var data = {
                        action: 'mvx_auto_search_product',
                        security: mvx_admin_product_auto_search_js_params.search_products_nonce,
                        protitle: strtitle,
                        is_admin : settings.is_admin
                    };
                    $.post(settings.ajaxurl, data, function (response) {
                        unblock($_this.parents('form'));
                        container.innerHTML = response.html;
                        if(response.results_count == 0){
                            setTimeout(function() {container.innerHTML = '';}, 2000); 
                        }
                    });
                }, 500);
            } else if (strtitle.length === 0) {
                container.innerHTML = '';
            }
        });
    };
}(jQuery));
