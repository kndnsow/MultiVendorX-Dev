/* global product_qna */

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

    var keyup_timeout;
    $( '#cust_question' ).on( 'keyup' , function () {
        var this_ele_val = $(this).val();
    clearTimeout( keyup_timeout );
    keyup_timeout = setTimeout( function() {
            $( '#qna-result-msg' ).html('');
            block( $( '#cust_qna_form' ) );
            if( this_ele_val.length > 3 ){
                var data = {
                    action: 'mvx_customer_ask_qna_handler',
                    handler: 'search',
                    product_ID: $( '#product_ID' ).val(),
                    keyword: this_ele_val
                };
                $.post(mvx_customer_qna_js_script_data.ajax_url, data, function (response) { 
                    unblock($('#cust_qna_form') );
                    if (response.no_data == 1) {
                        $('#qna-result-msg').html(response.message);
                        $('#qna-result-wrap').html('');
                        if(response.is_user == false){
                            $('#ask-wrap #ask-qna').hide();
                            $('#ask-wrap .no-answer-lbl').html(response.message);
                        }
                        $('#ask-wrap').show();
                    }else{
                        $('#qna-result-wrap').html(response.data);
                    }
                });
            }else{
                $('#ask-wrap').hide();
                var data = {
                    action: 'mvx_customer_ask_qna_handler',
                    handler: 'search',
                    product_ID: $('#product_ID').val(),
                    keyword: ''
                };
                $.post(mvx_customer_qna_js_script_data.ajax_url, data, function (response) {
                    unblock($('#cust_qna_form') );
                    if (response.no_data == 1) {
                        $('#qna-result-msg').html(response.message);
                        $('#qna-result-wrap').html('');
                        $('#ask-wrap').show();
                    }else{
                        $('#qna-result-wrap').html(response.data);
                        $("#qna-result-wrap .qna-item-wrap").not(".load-more-qna").hide();
                        $("#qna-result-wrap .qna-item-wrap").slice(0, 4).show();
                    }
                });
            }
        }, 500);
    });
   
    $('body').on('click', '#ask-qna', function () {
        $('#qna-result-msg').html('');
        block( $('#cust_qna_form') );
        var data = {
            action: 'mvx_customer_ask_qna_handler',
            handler: 'submit',
            customer_qna_data: $('#customerqnaform').serialize()
        };
        $.post(mvx_customer_qna_js_script_data.ajax_url, data, function (response) {
            if (response.no_data == 0) {
//                unblock($('#cust_qna_form') );
//                setTimeout(function(){
//                    $('#ask-wrap').hide();
//                    $('#cust_question').val('');
//                    $('#qna-result-msg').html(response.message);
                    window.location = response.redirect;
//                },3000);
            }
        });
    });
    
    $('body').on('click', 'button.mvx-add-qna-reply', function () { 
        var key = $(this).attr('data-key');
        var reply = $('#qna-reply-'+key).val();
        if (reply === '') {
            return false;
        }
        var data = {
            action: 'mvx_customer_ask_qna_handler',
            handler: 'answer',
            reply: reply,
            key: key
        };
        $.post(mvx_customer_qna_js_script_data.ajax_url, data, function (response) {
            if (response.no_data == 0) {
//                $('#reply-item-'+key).hide();
//                if(response.remain_data == 0){
//                    $('.customer-questions-panel').html('');
//                    $('.customer-questions-panel').html(response.msg);
//                }
//                setTimeout($('#qna-reply-modal-'+key).modal('hide'),3000);
                window.location.reload();
            }
        });
    });

    $('body').on('click', '.mvx_vendor_question .do_verify', function(e){
        e.preventDefault();
        var $this = $(this);
        var question_type = $(this).attr('data-verification');
        var question_id = $(this).attr('data-question_id');
        var data_action = $(this).attr('data-action');
        var product     = $(this).attr('data-product');
         console.log(question_id);
        var data = {
            action   : 'mvx_question_verification_approval',
            question_type : question_type,
            question_id : question_id,
            data_action : data_action,
            product     : product,
            security     : mvx_customer_qna_js_script_data.vendors_nonce
        }   
        $.post(mvx_customer_qna_js_script_data.ajax_url, data, function(response) {
            window.location.reload();
        });
    });
    
    $('body').on('click', 'button.mvx-update-qna-answer', function () { 
        var key = $(this).attr('data-key');
        var answer = $('#qna-answer-'+key).val();
        if (answer === '') {
            return false;
        }
        var data = {
            action: 'mvx_customer_ask_qna_handler',
            handler: 'update_answer',
            answer: answer,
            key: key
        };
        $.post(mvx_customer_qna_js_script_data.ajax_url, data, function (response) {
            if (response.no_data == 0) {
//                $('#reply-item-'+key).hide();
//                if(response.remain_data == 0){
//                    $('.customer-questions-panel').html('');
//                    $('.customer-questions-panel').html(response.msg);
//                }
//                setTimeout($('#qna-reply-modal-'+key).modal('hide'),3000);
                window.location.reload();
            }
        });
    });
    
    $('body').on('click', '.qna-vote .give-vote-btn', function (e) {
        e.preventDefault();
        block( $('#cust_qna_form') );
        var vote = $(this).attr('data-vote');
        var ans_ID = $(this).attr('data-ans');
        if (vote === '') {
            return false;
        }
        var data = {
            action: 'mvx_customer_ask_qna_handler',
            handler: 'vote_answer',
            vote: vote,
            ans_ID: ans_ID
        };
        $.post(mvx_customer_qna_js_script_data.ajax_url, data, function (response) {
            unblock( $('#cust_qna_form') );
            if (response.no_data == 0) {
                setTimeout(function(){
                    //window.location.reload();
                    window.location = response.redirect;
                },1000);
            }
        });
    });
    
    $('body').on('click', '.non_loggedin', function (e) {
        e.preventDefault();
        $('#qna_user_msg_wrap').simplePopup();
    });
    $(document).ready(function(){
        $("#qna-result-wrap .qna-item-wrap").not(".load-more-qna").hide();
        $("#qna-result-wrap .qna-item-wrap").slice(0, 4).show();
    });

    $('body').on('click', '.load-more-qna .load-more-btn', function (e) {
        e.preventDefault();
        $("#qna-result-wrap .qna-item-wrap:hidden").slice(0, 4).slideDown();
        if ($("#qna-result-wrap .qna-item-wrap:hidden").length == 0) {
            $("#qna-result-wrap .load-more-qna").fadeOut('slow');
            $("#qna-result-wrap .qna-item-wrap").not(".load-more-qna").last().css( "border-bottom", "1px solid #e2e2e2" );
        }
        $('html,body').animate({
            scrollTop: $(this).offset().top
        }, 1500);
    });

})(jQuery); 
