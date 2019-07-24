jQuery(document).ready(function($) {

	/* Hide recurring field if payment is offline */
	var Payment_type = jQuery('#donation_payment_type');
	var select = this.value;
	
	Payment_type.change(function () {
	    if ($(this).val() == 'online') {
	        $('.fieldset-recurring_donation').show();
	    }
	    else $('.fieldset-recurring_donation').hide();
	});
	
	/* Hide recurring field if payment is offline */
	var Donation_amount_text = jQuery('input:text[name=donation_amount]');
	var Donation_amount_radio = jQuery("input:radio[name=donation_amount]").click(function() {
	    var Donation_amount_value = $(this).val();
	    $( Donation_amount_text ).val( Donation_amount_value );
	});
	
	Donation_amount_text.click(function() {
	    $( Donation_amount_radio ).prop('checked', false);
	});

});