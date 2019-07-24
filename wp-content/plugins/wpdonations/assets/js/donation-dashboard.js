jQuery(document).ready(function($) {

	$('.donation-dashboard-action-delete').click(function() {
		var answer = confirm( wpdonations_donation_dashboard.i18n_confirm_delete );

		if (answer)
			return true;

		return false;
	});

});