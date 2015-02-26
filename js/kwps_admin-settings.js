jQuery(document).ready(function($) {
	// console.log('loaded kwps_admin-settings.js');

	//hide and show grouping form with checkbox
	var showFormCheckbox = $('#kwps_show_grouping_form');
	if ( showFormCheckbox.prop('checked') ) {
		$('.groupingForm').show();
	} else {
		$('.groupingForm').hide();
	}
	showFormCheckbox.on('change', function (event) {
		var newValue = event.currentTarget.checked;
		if ( newValue ) {
			$('.groupingForm').show();
		} else {
			$('.groupingForm').hide();
		}
	});

	var button = $('.kwps-add-result-button').detach();
	$('div.wp-editor-tools').append(button);

});
