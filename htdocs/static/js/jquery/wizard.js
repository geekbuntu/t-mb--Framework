/**
 * This plugin takes an HTML setup
 * form and converts it into a wizard
 * @param int iStep is the current step of the wizard
**/
jQuery.wizard = {
	manageSteps: function(iStep) {
		// Grab the number of steps
		var iSteps    = jQuery('.wizardStep').size();
		// Show and hide steps
		for (var iThisStep = 0; iThisStep < iSteps; iThisStep ++) {
			// Make sure this step is
			// not our current step
			if ((iThisStep + 1) == parseInt(iStep)) {
				if (!jQuery('#wizard-step-' + (iThisStep + 1)).is(':visible')) {
					// Show the step
					jQuery('#wizard-step-' + (iThisStep + 1)).show('slide', {
						direction: 'left'
					}, 1000);
				}
				// Enable step text
				jQuery('#wizard-step-text-' + (iThisStep + 1)).removeClass('step disabled')
				jQuery('#wizard-step-text-' + (iThisStep + 1)).addClass('step');
			} else {
				// See if we actually need
				// to hide this element
				if (jQuery('#wizard-step-' + (iThisStep + 1)).is(':visible')) {
					jQuery('#wizard-step-' + (iThisStep + 1)).hide('slide', {
						direction: 'left'
					}, 1000);
				}
				// Disable the step text
				jQuery('#wizard-step-text-' + (iThisStep + 1)).removeClass('step')
				jQuery('#wizard-step-text-' + (iThisStep + 1)).addClass('step disabled');
			}
		}
		// Create the pretty buttons
		jQuery('.wizardNav').buttonset();
		// Return
		return true;
	}
};