/*global mvx_order_details_js_script_data, accounting*/
jQuery(function ($) {
    /**
     * Order Items Panel
     */
    var mvx_meta_boxes_order_items = {
        init: function () {
            $('#mvx-order-details')
                    .on('click', 'button.refund-items', this.refund_items)
                    .on('click', '.cancel-action', this.cancel)
                    // Refunds
                    .on('click', 'button.do-api-refund, button.do-manual-refund', this.refunds.do_refund)
                    .on('change', '.refund input.refund_line_total, .refund input.refund_line_tax', this.refunds.input_changed)
                    .on('change keyup', '.mvx-order-refund-items #refund_amount', this.refunds.amount_changed)
                    .on('change', 'input.refund_order_item_qty', this.refunds.refund_quantity_changed)
                    // Qty
                    .on('change', 'input.quantity', this.quantity_changed)
                    // Status
                    .on('click', '#order_status a', this.order_status_changed)

                    // Subtotal/total
                    .on('keyup change', '.split-input :input', function () {
                        var $subtotal = $(this).parent().prev().find(':input');
                        if ($subtotal && ($subtotal.val() === '' || $subtotal.is('.match-total'))) {
                            $subtotal.val($(this).val()).addClass('match-total');
                        }
                    })

                    .on('keyup', '.split-input :input', function () {
                        $(this).removeClass('match-total');
                    });
        },
        block: function () {
            $('#mvx-order-details').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        },
        unblock: function () {
            $('#mvx-order-details').unblock();
        },
        // When the qty is changed, increase or decrease costs
        quantity_changed: function () {
            var $row = $(this).closest('tr.item');
            var qty = $(this).val();
            var o_qty = $(this).attr('data-qty');
            var line_total = $('input.line_total', $row);
            var line_subtotal = $('input.line_subtotal', $row);

            // Totals
            var unit_total = accounting.unformat(line_total.attr('data-total'), mvx_order_details_js_script_data.mon_decimal_point) / o_qty;
            line_total.val(
                    parseFloat(accounting.formatNumber(unit_total * qty, mvx_order_details_js_script_data.rounding_precision, ''))
                    .toString()
                    .replace('.', mvx_order_details_js_script_data.mon_decimal_point)
                    );

            var unit_subtotal = accounting.unformat(line_subtotal.attr('data-subtotal'), mvx_order_details_js_script_data.mon_decimal_point) / o_qty;
            line_subtotal.val(
                    parseFloat(accounting.formatNumber(unit_subtotal * qty, mvx_order_details_js_script_data.rounding_precision, ''))
                    .toString()
                    .replace('.', mvx_order_details_js_script_data.mon_decimal_point)
                    );

            // Taxes
            $('input.line_tax', $row).each(function () {
                var $line_total_tax = $(this);
                var tax_id = $line_total_tax.data('tax_id');
                var unit_total_tax = accounting.unformat($line_total_tax.attr('data-total_tax'), mvx_order_details_js_script_data.mon_decimal_point) / o_qty;
                var $line_subtotal_tax = $('input.line_subtotal_tax[data-tax_id="' + tax_id + '"]', $row);
                var unit_subtotal_tax = accounting.unformat($line_subtotal_tax.attr('data-subtotal_tax'), mvx_order_details_js_script_data.mon_decimal_point) / o_qty;

                if (0 < unit_total_tax) {
                    $line_total_tax.val(
                            parseFloat(accounting.formatNumber(unit_total_tax * qty, mvx_order_details_js_script_data.rounding_precision, ''))
                            .toString()
                            .replace('.', mvx_order_details_js_script_data.mon_decimal_point)
                            );
                }

                if (0 < unit_subtotal_tax) {
                    $line_subtotal_tax.val(
                            parseFloat(accounting.formatNumber(unit_subtotal_tax * qty, mvx_order_details_js_script_data.rounding_precision, ''))
                            .toString()
                            .replace('.', mvx_order_details_js_script_data.mon_decimal_point)
                            );
                }
            });

            $(this).trigger('quantity_changed');
        },
        order_status_changed: function (){
            var selected_status = $(this).data('status'),
                current_status = $('#order_current_status').val(),
                order_id = $('#order_ID').val();
            $(this).parents('.change-status').find('.order-status-text').removeClass (function (index, css) {
               // remove class start with 'wc' and add 
               return (css.match (/(^|\s)wc\S+/g) || []).join(' '); 
             }).addClass(selected_status);
            if( current_status == selected_status ) {
                $('.change-status').removeClass('loaderOverlay');
                $(".dropdown-order-statuses").removeClass('open');
                return false;
            }else if( selected_status == 'wc-cancelled' ){
                var status_cnf = window.confirm(mvx_order_details_js_script_data.i18n_do_cancel);
                if(!status_cnf) return false;
            }else if( current_status == 'wc-cancelled' ){
                return false;
            }
            $('.change-status').addClass('loaderOverlay');
            var data = {
                action: 'mvx_order_status_changed',
                order_id: order_id,
                selected_status: selected_status,
                security: mvx_order_details_js_script_data.grant_access_nonce
            };

            $.post(mvx_order_details_js_script_data.ajax_url, data, function (response) {
                if (response) {
                    $('.order_status_lbl').html('');
                    if( response.status_key == 'wc-cancelled' ){
                        $('.dropdown-order-statuses').hide();
                    }
                    $('.order_status_lbl').html(response.status_name);
                    $('#order_current_status').val(response.status_key);
                    $('.change-status').removeClass('loaderOverlay');
                }
            });
            
        },
        refund_items: function () {
            $('div.mvx-order-refund-items').slideDown();
            $('div.mvx-order-data-row-toggle').not('div.mvx-order-refund-items').slideUp();
            $('div.mvx-order-totals-items').slideUp();
            $('#mvx-order-details').find('div.refund').show();
            //$( '.wc-order-edit-line-item .wc-order-edit-line-item-actions' ).hide();
            return false;
        },
        cancel: function () {
            $('div.mvx-order-data-row-toggle').not('div.mvx-order-actions').slideUp();
            $('div.mvx-order-actions').slideDown();
            $('div.mvx-order-totals-items').slideDown();
            $('#mvx-order-details').find('div.refund').hide();
            //$( '.wc-order-edit-line-item .wc-order-edit-line-item-actions' ).show();

            return false;
        },
        refunds: {

            do_refund: function () {
                mvx_meta_boxes_order_items.block();

                if (window.confirm(mvx_order_details_js_script_data.i18n_do_refund)) {
                    var refund_amount = $('input#refund_amount').val();
                    var refund_reason = $('input#refund_reason').val();
                    var refunded_amount = $('input#refunded_amount').val();

                    // Get line item refunds
                    var line_item_qtys = {};
                    var line_item_totals = {};
                    var line_item_tax_totals = {};

                    $('.refund input.refund_order_item_qty').each(function (index, item) {
                        if ($(item).closest('tr').data('order_item_id')) {
                            if (item.value) {
                                line_item_qtys[ $(item).closest('tr').data('order_item_id') ] = item.value;
                            }
                        }
                    });

                    $('.refund input.refund_line_total').each(function (index, item) {
                        if ($(item).closest('tr').data('order_item_id')) {
                            line_item_totals[ $(item).closest('tr').data('order_item_id') ] = accounting.unformat(item.value, mvx_order_details_js_script_data.mon_decimal_point);
                        }
                    });

                    $('.refund input.refund_line_tax').each(function (index, item) {
                        if ($(item).closest('tr').data('order_item_id')) {
                            var tax_id = $(item).data('tax_id');

                            if (!line_item_tax_totals[ $(item).closest('tr').data('order_item_id') ]) {
                                line_item_tax_totals[ $(item).closest('tr').data('order_item_id') ] = {};
                            }

                            line_item_tax_totals[ $(item).closest('tr').data('order_item_id') ][ tax_id ] = accounting.unformat(item.value, mvx_order_details_js_script_data.mon_decimal_point);
                        }
                    });

                    var data = {
                        action: 'mvx_do_refund',
                        order_id: mvx_order_details_js_script_data.post_id,
                        refund_amount: refund_amount,
                        refunded_amount: refunded_amount,
                        refund_reason: refund_reason,
                        line_item_qtys: JSON.stringify(line_item_qtys, null, ''),
                        line_item_totals: JSON.stringify(line_item_totals, null, ''),
                        line_item_tax_totals: JSON.stringify(line_item_tax_totals, null, ''),
                        api_refund: $(this).is('.do-api-refund'),
                        restock_refunded_items: $('#restock_refunded_items:checked').length ? 'true' : 'false',
                        security: mvx_order_details_js_script_data.order_item_nonce
                    };

                    $.post(mvx_order_details_js_script_data.ajax_url, data, function (response) {
                        if (true === response.success) {
                            // Redirect to same page for show the refunded status
                            window.location.href = window.location.href;
                        } else {
                            window.alert(response.data.error);
                            window.location.href = window.location.href;
                            //wc_meta_boxes_order_items.reload_items();
                            mvx_meta_boxes_order_items.unblock();
                        }
                    });
                } else {
                    mvx_meta_boxes_order_items.unblock();
                }
            },
            input_changed: function () {
                var refund_amount = 0;
                var $items = $('.woocommerce_order_items').find('tr.item, tr.fee, tr.shipping');

                $items.each(function () {
                    var $row = $(this);
                    var refund_cost_fields = $row.find('.refund input:not(.refund_order_item_qty)');

                    refund_cost_fields.each(function (index, el) {
                        refund_amount += parseFloat(accounting.unformat($(el).val() || 0, mvx_order_details_js_script_data.mon_decimal_point));
                    });
                });

                $('#refund_amount')
                        .val(accounting.formatNumber(
                                refund_amount,
                                mvx_order_details_js_script_data.currency_format_num_decimals,
                                '',
                                mvx_order_details_js_script_data.mon_decimal_point
                                ))
                        .change();
            },

            amount_changed: function () {
                var total = accounting.unformat($(this).val(), mvx_order_details_js_script_data.mon_decimal_point);

                $('button .wc-order-refund-amount .amount').text(accounting.formatMoney(total, {
                    symbol: mvx_order_details_js_script_data.currency_format_symbol,
                    decimal: mvx_order_details_js_script_data.currency_format_decimal_sep,
                    thousand: mvx_order_details_js_script_data.currency_format_thousand_sep,
                    precision: mvx_order_details_js_script_data.currency_format_num_decimals,
                    format: mvx_order_details_js_script_data.currency_format
                }));
            },

            // When the refund qty is changed, increase or decrease costs
            refund_quantity_changed: function () {
                var $row = $(this).closest('tr.item');
                var qty = $row.find('input.quantity').val();
                var refund_qty = $(this).val();
                var line_total = $('input.line_total', $row);
                var refund_line_total = $('input.refund_line_total', $row);

                // Totals
                var unit_total = accounting.unformat(line_total.attr('data-total'), mvx_order_details_js_script_data.mon_decimal_point) / qty;

                refund_line_total.val(
                        parseFloat(accounting.formatNumber(unit_total * refund_qty, mvx_order_details_js_script_data.rounding_precision, ''))
                        .toString()
                        .replace('.', mvx_order_details_js_script_data.mon_decimal_point)
                        ).change();

                // Taxes
                $('.refund_line_tax', $row).each(function () {
                    var $refund_line_total_tax = $(this);
                    var tax_id = $refund_line_total_tax.data('tax_id');
                    var line_total_tax = $('input.line_tax[data-tax_id="' + tax_id + '"]', $row);
                    var unit_total_tax = accounting.unformat(line_total_tax.data('total_tax'), mvx_order_details_js_script_data.mon_decimal_point) / qty;

                    if (0 < unit_total_tax) {
                        $refund_line_total_tax.val(
                                parseFloat(accounting.formatNumber(unit_total_tax * refund_qty, mvx_order_details_js_script_data.rounding_precision, ''))
                                .toString()
                                .replace('.', mvx_order_details_js_script_data.mon_decimal_point)
                                ).change();
                    } else {
                        $refund_line_total_tax.val(0).change();
                    }
                });

                // Restock checkbox
                if (refund_qty > 0) {
                    $('#restock_refunded_items').closest('tr').show();
                } else {
                    $('#restock_refunded_items').closest('tr').hide();
                    $('.woocommerce_order_items input.refund_order_item_qty').each(function () {
                        if ($(this).val() > 0) {
                            $('#restock_refunded_items').closest('tr').show();
                        }
                    });
                }

                $(this).trigger('refund_quantity_changed');
            }
        },
    };


    /**
     * Order Downloads Panel
     */
    var mvx_meta_boxes_order_downloads = {
        init: function () {
            $( '.date-picker' ).datepicker( {
                defaultDate: '',
                dateFormat: 'yy-mm-dd',
                numberOfMonths: 1,
                showButtonPanel: true
            } );
            
            $('.order_download_permissions')
                    .on('click', 'button.grant_access', this.grant_access)
                    .on('click', 'button.revoke_access', this.revoke_access)
                    .on('click', '#copy-download-link', this.copy_link)
                    .on('aftercopy', '#copy-download-link', this.copy_success)
                    .on('aftercopyfailure', '#copy-download-link', this.copy_fail);
        },

        grant_access: function () {
            var products = $('#grant_access_id').val();

            if (!products) {
                return;
            }

            $('.order_download_permissions').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            var data = {
                action: 'mvx_grant_access_to_download',
                product_ids: products,
                loop: $('.order_download_permissions .wc-metabox').length,
                order_id: mvx_order_details_js_script_data.post_id,
                security: mvx_order_details_js_script_data.grant_access_nonce
            };

            $.post(mvx_order_details_js_script_data.ajax_url, data, function (response) {

                if (response) {
                    $('.order_download_permissions .wc-metaboxes').append(response);
                } else {
                    window.alert(mvx_order_details_js_script_data.i18n_download_permission_fail);
                }

                $(document.body).trigger('wc-init-datepickers');
                $('#grant_access_id').val('').change();
                $('.order_download_permissions').unblock();
            });

            return false;
        },

        revoke_access: function () {
            if (window.confirm(mvx_order_details_js_script_data.i18n_permission_revoke)) {
                var el = $(this).parent().parent();
                var product = $(this).attr('rel').split(',')[0];
                var file = $(this).attr('rel').split(',')[1];
                var permission_id = $(this).data('permission_id');

                if (product > 0) {
                    $(el).block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });

                    var data = {
                        action: 'woocommerce_revoke_access_to_download',
                        product_id: product,
                        download_id: file,
                        permission_id: permission_id,
                        order_id: mvx_order_details_js_script_data.post_id,
                        security: mvx_order_details_js_script_data.revoke_access_nonce
                    };

                    $.post(mvx_order_details_js_script_data.ajax_url, data, function () {
                        // Success
                        $(el).fadeOut('300', function () {
                            $(el).remove();
                        });
                    });

                } else {
                    $(el).fadeOut('300', function () {
                        $(el).remove();
                    });
                }
            }
            return false;
        },

        /**
         * Copy download link.
         *
         * @param {Object} evt Copy event.
         */
        copy_link: function (evt) { 
            wcClearClipboard();
            wcSetClipboard($(this).attr('href'), $(this));
            evt.preventDefault();
        },

        /**
         * Display a "Copied!" tip when success copying
         */
        copy_success: function () {
            $(this).tipTip({
                'attribute': 'data-tip',
                'activation': 'focus',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 0
            }).focus();
        },

        /**
         * Displays the copy error message when failure copying.
         */
        copy_fail: function () {
            $(this).tipTip({
                'attribute': 'data-tip-failed',
                'activation': 'focus',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 0
            }).focus();
        }
    };

    mvx_meta_boxes_order_items.init();
    mvx_meta_boxes_order_downloads.init();

});


