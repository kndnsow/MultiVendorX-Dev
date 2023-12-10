/* global ajaxurl */

jQuery(document).ready(function($) {
	$('select.mvx_select_vendor').select2({
		minimumInputLength: 1, // only start searching when the user has input 3 or more characters
	});
	
	$( '.delete_vendor_data' ).click(function() {
		var unassign_vendor = {
			action: 'unassign_vendor',
			'product_id': unassign_vendors_data.current_product_id,
			'security': unassign_vendors_data.security
		};
		
		$.post( 'admin-ajax.php', unassign_vendor, function(response) {
			$('.chosen-single span').html('Choose a vendor');
			$('#choose_vendor_ajax option:selected').remove();
			$('.input-commision').val('');
			$('#post_author').val(unassign_vendors_data.current_user_id);
			$('._product_vendors_commission_percentage input').val('');
			$('._product_vendors_commission_fixed_per_qty input').val('');
			$('._product_vendors_commission_percentage input').val('');
			$('._product_vendors_commission_fixed_per_qty input').val('');
			location.reload();
		});
	
	});
        
    if ( $.isFunction($.fn.singleProductMulipleVendor) ) {
        $('input[name=post_title]').singleProductMulipleVendor({
            ajaxurl : 'admin-ajax.php',
        });
    }	
});