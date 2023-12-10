jQuery(document).ready(function($){
	jQuery(function($) {
		$( "#tabs-1" ).tabs();
	});	
	jQuery(function($) {
		$( "#accordion-1" ).accordion({
			speed: 'slow',
			heightStyle: "content"		
		});
		$( "#accordion-2" ).accordion({
			speed: 'slow',
			heightStyle: "content"		
		});	
		$( "#accordion-3" ).accordion({
			speed: 'slow',
			heightStyle: "content"		
		});
		$( "#accordion-4" ).accordion({
			speed: 'slow',
			heightStyle: "content"		
		});
	});		
	$("body").on("click", ".msg_stat_click", function(e){		
		var myparent = $(this).parent();		
		$(".msg_stat").slideUp();
		if(!myparent.hasClass('oppned')) {
			$(this).next(".msg_stat").slideDown(300);	
			myparent.addClass('oppned');
                        $(this).children('span').removeClass('la-angle-down').addClass('la-angle-up');
		}
		else {
			myparent.removeClass('oppned');
                        $(this).children('span').removeClass('la-angle-up').addClass('la-angle-down');
		}
	});		
	jQuery("body").on("click", ".msg_stat_click", function(event){
		event.preventDefault();
	});
	$("body").on("click", "._mvx_vendor_message_delete", function(e){
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this).parents('.ui-accordion-header');
		var element_to_be_deleted2 = $('#'+element_to_be_deleted1.attr('aria-controls'));

		var tab_element = $(this).parents('.ui-tabs-panel');		
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var tab_to_refrash = tab_element.attr('data-element');
		var tab_id = tab_element.attr('id');
		var data = {
			action : 'mvx_vendor_announcements_operation',
			security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
			actionmode : 'mark_delete',
			msg_id : msg_id		
		}
		$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data, function(res) {			
			if(	res == 1 ) {
                                window.location.reload();
				element_to_be_deleted1.hide();
				element_to_be_deleted2.hide();
				$("#mvx_msg_tab_to_be_refrash").val('_archive');
				$("#mvx_msg_tab_to_be_refrash2").val('_read');
				$("#mvx_msg_tab_to_be_refrash3").val('_unread');
				var data = {
					action : 'mvx_announcements_refresh_tab_data',
					security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
					tabname : tab_to_refrash					
				}
				$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data, function(res) {
					$("#"+tab_id+' .msg_container').html(res);
					jQuery(function($) {
						$( "#accordion-1" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
						$( "#accordion-2" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});	
						$( "#accordion-3" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
						
					});
					jQuery('#accordion-1').accordion("refresh");
					jQuery('#accordion-2').accordion("refresh");
					jQuery('#accordion-3').accordion("refresh");
					
					lodder.hide();
				});
			}				
		});
	});
	$("div#mvx_msg_tab_1").on("click", "._mvx_vendor_message_read", function(e){			
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this);	
		var element_parent = $(this).parent();
		var tab_element = $(this).parents('.ui-tabs-panel');	
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
				
		var data = {
			action : 'mvx_vendor_announcements_operation',
			security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
			actionmode : 'mark_read',
			msg_id : msg_id			
		}
		$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data, function(res) {			
			if(	res != 0 ) {
                                window.location.reload();
				element_to_be_deleted1.hide();
				element_parent.prepend('<li class="_mvx_vendor_message_unread"><a href="#">'+res+'</a></li>');
				$("#mvx_msg_tab_to_be_refrash").val('_read');
				$("#mvx_msg_tab_to_be_refrash2").val('_unread');
				lodder.hide();				
			}				
		});
	});
	
	$("div#mvx_msg_tab_1").on("click", "._mvx_vendor_message_unread",  function(e){
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this);	
		var element_parent = $(this).parent();
		var tab_element = $(this).parents('.ui-tabs-panel');		
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var data = {
			action : 'mvx_vendor_announcements_operation',
			security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
			actionmode : 'mark_unread',
			msg_id : msg_id			
		}
		$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data, function(res) {				
			if(	res != 0 ) {
                                window.location.reload();
				element_to_be_deleted1.hide();
				element_parent.prepend('<li class="_mvx_vendor_message_read"><a href="#">'+res+'</a></li>');
				$("#mvx_msg_tab_to_be_refrash").val('_read');
				$("#mvx_msg_tab_to_be_refrash2").val('_unread');
				lodder.hide();
			}				
		});
	});
	
	$("div#mvx_msg_tab_2").on("click", "._mvx_vendor_message_unread",  function(e){
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this).parents('.ui-accordion-header');
		var element_to_be_deleted2 = $('#'+element_to_be_deleted1.attr('aria-controls'));
		var element_parent = $(this).parent();

		var tab_element = $(this).parents('.ui-tabs-panel');	
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var tab_to_refrash = tab_element.attr('data-element');
		var tab_id = tab_element.attr('id');
		
		var data = {
			action : 'mvx_vendor_announcements_operation',
			security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
			actionmode : 'mark_unread',
			msg_id : msg_id			
		}
		$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data, function(res) {				
			if(	res != 0 ) {
                                window.location.reload();
				element_to_be_deleted1.remove();
				element_to_be_deleted2.remove();				
				$("#mvx_msg_tab_to_be_refrash2").val('_unread');
				$("#mvx_msg_tab_to_be_refrash").val('_all');
				var data2 = {
					action : 'mvx_announcements_refresh_tab_data',
					security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
					tabname : tab_to_refrash					
				}				
				$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data2, function(res2) {
					$("#"+tab_id+' .msg_container').html(res2);
					jQuery(function($) {							
						$( "#accordion-2" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});						
					});					
					jQuery('#accordion-2').accordion("refresh");					
					lodder.hide();
				});
			}				
		});
	});
	
	$("div#mvx_msg_tab_3").on("click", "._mvx_vendor_message_read", function(e){			
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this).parents('.ui-accordion-header');
		var element_to_be_deleted2 = $('#'+element_to_be_deleted1.attr('aria-controls'));
		var element_parent = $(this).parent();
		
		var tab_element = $(this).parents('.ui-tabs-panel');	
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var tab_to_refrash = tab_element.attr('data-element');
		var tab_id = tab_element.attr('id');	
				
		var data = {
			action : 'mvx_vendor_announcements_operation',
			security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
			actionmode : 'mark_read',
			msg_id : msg_id			
		}
		$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data, function(res) {			
			if(	res != 0 ) {
                                window.location.reload();
				element_to_be_deleted1.remove();
				element_to_be_deleted2.remove();				
				$("#mvx_msg_tab_to_be_refrash").val('_read');
				$("#mvx_msg_tab_to_be_refrash2").val('_all');
				
				var data2 = {
					action : 'mvx_announcements_refresh_tab_data',
					security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
					tabname : tab_to_refrash					
				}				
				$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data2, function(res2) {
					$("#"+tab_id+' .msg_container').html(res2);
					jQuery(function($) {							
						$( "#accordion-3" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});						
					});					
					jQuery('#accordion-3').accordion("refresh");					
					lodder.hide();
				});
				
			}				
		});
	});
	
	$("div#mvx_msg_tab_4").on("click", "._mvx_vendor_message_restore", function(e){			
		var msg_id = $(this).parent().attr('data-element');
		var element_to_be_deleted1 = $(this).parents('.ui-accordion-header');
		var element_to_be_deleted2 = $('#'+element_to_be_deleted1.attr('aria-controls'));
		var element_parent = $(this).parent();
		
		var tab_element = $(this).parents('.ui-tabs-panel');	
		var lodder = tab_element.find('.ajax_loader_class_msg');
		lodder.show();		
		var tab_to_refrash = tab_element.attr('data-element');
		var tab_id = tab_element.attr('id');		
		var data = {
			action : 'mvx_vendor_announcements_operation',
			security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
			actionmode : 'mark_restore',
			msg_id : msg_id			
		}
		$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data, function(res) {			
			if(	res != 0 ) {
                                window.location.reload();
				element_to_be_deleted1.remove();
				element_to_be_deleted2.remove();				
				$("#mvx_msg_tab_to_be_refrash").val('_read');
				$("#mvx_msg_tab_to_be_refrash2").val('_unread');
				$("#mvx_msg_tab_to_be_refrash3").val('_all');
				var data2 = {
					action : 'mvx_announcements_refresh_tab_data',
					security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
					tabname : tab_to_refrash					
				}				
				$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data2, function(res2) {
					$("#"+tab_id+' .msg_container').html(res2);
					jQuery(function($) {							
						$( "#accordion-4" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});						
					});					
					jQuery('#accordion-4').accordion("refresh");					
					lodder.hide();
				});
			}				
		});
	});	
	
	$("#tabs-1 ul.mvx_msg_tab_nav li").click(function(){
		var clicked_tab = $(this).attr('data-element');
		var clicked_tab_anchor = $(this).find('a');
		var target_tab_id = clicked_tab_anchor.attr('href');		
		var tab_to_be_refrash = $("#mvx_msg_tab_to_be_refrash").val();
		var tab_to_be_refrash2 = $("#mvx_msg_tab_to_be_refrash2").val();
		var tab_to_be_refrash3 = $("#mvx_msg_tab_to_be_refrash3").val();
		if( tab_to_be_refrash == clicked_tab || tab_to_be_refrash2 == clicked_tab || tab_to_be_refrash3 == clicked_tab  ) {
			$(target_tab_id+' div.ajax_loader_class_msg').show();
			var data = {
				action : 'mvx_announcements_refresh_tab_data',
				security: mvx_new_vandor_announcements_js_script_data.grant_access_nonce,
				tabname : clicked_tab					
			}
			$.post(mvx_new_vandor_announcements_js_script_data.ajax_url, data, function(res) {
				//console.log(res);	
				$(target_tab_id+' .msg_container').html(res);
					jQuery(function($) {
						$( "#accordion-1" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
						$( "#accordion-2" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});	
						$( "#accordion-3" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
						$( "#accordion-4" ).accordion({
							speed: 'slow',
							heightStyle: "content"		
						});
					});
					jQuery('#accordion-1').accordion("refresh");
					jQuery('#accordion-2').accordion("refresh");
					jQuery('#accordion-3').accordion("refresh");
					jQuery('#accordion-4').accordion("refresh");
				$(target_tab_id+' div.ajax_loader_class_msg').hide();
				if( tab_to_be_refrash == clicked_tab) {
					$("#mvx_announcements_tab_to_be_refrash").val('');
				}
				else if(tab_to_be_refrash2 == clicked_tab) {
					$("#mvx_announcements_tab_to_be_refrash2").val('');
				}
				else if(tab_to_be_refrash3 == clicked_tab) {
					$("#mvx_announcements_tab_to_be_refrash3").val('');
				}
			});			
		}				
	});
	
	$("body").on("click", ".mvx_black_btn_msg_for_nav", function(e){
		var myparent = $(this).parent().parent();
		var myparent2 = $(this).parent();
		var hidden_msg = myparent.find('.mvx_hide_message');		
		var first_num_ele = myparent2.find('span.first_nav_num');
		var second_num_ele = myparent2.find('span.second_nav_num');     
		var first_num = parseInt(first_num_ele.html());
		var second_num = parseInt(second_num_ele.html());		
		var counter = 0;
		if(hidden_msg.length <= 12) {
			first_num_ele.html(second_num);
			$(this).hide();
		}
		else {
			first_num_ele.html( parseInt(first_num)+6);
		}
		hidden_msg.each(function() {
			$(this).removeClass('mvx_hide_message');				
			counter =	Number(counter) + 1;
			if (Number(counter) == 12) {
        return false;
      }				
		});		
	});
	
});
