jQuery(document).ready(function ($) { 
    /** for add xtore support **/
    var SingleProductMultivendor;
    if(typeof themeSingleProductMultivendor != 'undefined'){
        SingleProductMultivendor = themeSingleProductMultivendor
    } else{
        SingleProductMultivendor = '#tab-singleproductmultivendor';
    }
    /** End support **/
    var total_div = $(SingleProductMultivendor+' .rowbody').length;
    if (parseInt(total_div) > 4) {
        var counter = 0;
        $(SingleProductMultivendor+" .rowbody").each(function () {
            if (parseInt(counter) >= 4) {
                $(this).hide();
            }
            counter = parseInt(counter) + 1;
        });
        var data = {
            action: 'get_loadmorebutton_single_product_multiple_vendors'
        }
        $.post(mvx_single_product_multiple_vendors_script_data.ajax_url, data, function (response) {
            $(SingleProductMultivendor).append(response);
        });
    }
    $('body').on('click', 'button#mvx-load-more-button', function (e) {
        $(SingleProductMultivendor+" .rowbody").each(function () {
            $(this).show('slow');
        });
        $(this).hide('slow');
    });
        $('#mvx_multiple_product_sorting').change(function (e) { 
            $(SingleProductMultivendor+ ' .ajax_loader_class_msg').show();
            var sorting_value = $(this).val();
            var attrid = $(this).attr('attrid'); 
            if (sorting_value != '') {
                var sorting_data = {
                    action: 'single_product_multiple_vendors_sorting',
                    sorting_value: sorting_value,
                    attrid: attrid
                }
                $.post(mvx_single_product_multiple_vendors_script_data.ajax_url, sorting_data, function (response) {
                    $(SingleProductMultivendor+ ' .rowbody').each(function () {
                        $(this).remove();
                    });
                    $(response).insertAfter(SingleProductMultivendor+' .rowhead');
                    var counter2 = 0;
                    var total_div2 = $(SingleProductMultivendor+ ' .rowbody').length;
                    if (parseInt(total_div2) > 4) {
                        if ($(SingleProductMultivendor+ ' #mvx-load-more-button').css('display') != 'none') {
                            $(SingleProductMultivendor+ " .rowbody").each(function () {
                                if (parseInt(counter2) >= 4) {
                                    $(this).hide();
                                }
                                counter2 = parseInt(counter2) + 1;
                            });
                    }
                }
                $(SingleProductMultivendor+ ' .ajax_loader_class_msg').hide();
            });
        }
    });

    $('.goto_more_offer_tab').click(function (e) {
        e.preventDefault();
        if (!$('.singleproductmultivendor_tab').hasClass('active')) {
            $('.singleproductmultivendor_tab a, #tab_singleproductmultivendor').click();
        }
        if ($('.woocommerce-tabs').length > 0) {
            $('html, body').animate({
                scrollTop: $(".woocommerce-tabs").offset().top -120
            }, 1500);
        }
    });

});