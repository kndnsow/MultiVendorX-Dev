jQuery(document).ready(function($) {
	if($('#vendor_payment_mode').val() == 'direct_bank' ) {
		$('.vendor_bank_account_type_wrapper, .vendor_bank_account_number_wrapper, .vendor_bank_name_wrapper, .vendor_aba_routing_number_wrapper, .vendor_bank_address_wrapper, .vendor_destination_currency_wrapper, .vendor_iban_wrapper, .vendor_account_holder_name_wrapper').show();
		$('.vendor_paypal_email_wrapper').hide();
	} else if($('#vendor_payment_mode').val() == 'paypal_masspay' )  {
		$('.vendor_bank_account_type_wrapper, .vendor_bank_account_number_wrapper, .vendor_bank_name_wrapper, .vendor_aba_routing_number_wrapper, .vendor_bank_address_wrapper, .vendor_destination_currency_wrapper, .vendor_iban_wrapper, .vendor_account_holder_name_wrapper').hide();
		$('.vendor_paypal_email_wrapper').show();
	}
	
	$('#vendor_payment_mode').on( 'change', function() {
		if($(this).val() == 'direct_bank' ) {
			$('.vendor_bank_account_type_wrapper, .vendor_bank_account_number_wrapper, .vendor_bank_name_wrapper, .vendor_aba_routing_number_wrapper, .vendor_bank_address_wrapper, .vendor_destination_currency_wrapper, .vendor_iban_wrapper, .vendor_account_holder_name_wrapper').show();
			$('.vendor_paypal_email_wrapper').hide();
		} else if($('#vendor_payment_mode').val() == 'paypal_masspay' ) {
			$('.vendor_bank_account_type_wrapper, .vendor_bank_account_number_wrapper, .vendor_bank_name_wrapper, .vendor_aba_routing_number_wrapper, .vendor_bank_address_wrapper, .vendor_destination_currency_wrapper, .vendor_iban_wrapper, .vendor_account_holder_name_wrapper').hide();
			$('.vendor_paypal_email_wrapper').show();
		}
	});
	
});