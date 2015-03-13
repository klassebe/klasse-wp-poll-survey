jQuery(document).ready(function($) {

	//hides and shows grouping form with checkbox
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

	//puts 'Add result' button in the proper location
	var button = $('.kwps-add-result-button').detach();
	$('div.wp-editor-tools').append(button);

	$('.kwps-add-result-button').on('click', function(event) {
		var output ='';

		var allowedTypes = kwpsInfo.testCollectionOutputTypes;

		tb_show('', kwpsInfo.siteurl + '/wp-content/plugins/klasse-wp-poll-survey/classes/show-charts.php?type=image&amp;TB_iframe=true');
		

		var request = $.ajax({
			url: 'admin-ajax.php?action=kwps_get_result_page',
			context: document.body
		});
		request.done(function(request, status, error) {
			$('iframe').contents().find('#kwps-result-page').append(request);
		});
		request.fail(function() {
			alert(kwps_translations['Errors occurred. Please check below for more information.']);
		});

		$.each(allowedTypes, function (key, value) {
			output += '<div id="' + value + '" class="media-item left"><label><h4>' + value.charAt(0).toUpperCase() + value.slice(1).split('-').join(' ') + '</h4><input type="radio" name="results" value="' + value + '"><img class="thumbnail" src="images/' + value + '.png" alt="' + value + '" height="128" width="128"></label></div>';
		});
		kwpsInfo.version_ids.forEach(function (object) {
			$.each(allowedTypes, function (key, value) {
				output += '<div id="' + value + '" class="media-item left versionCharts" data-id="' + object.ID + '"><label><h4>' + value.charAt(0).toUpperCase() + value.slice(1).split('-').join(' ').slice(0, -8) + object.post_title + '</h4><input type="radio" name="results" value="' + value + '"><img class="thumbnail" src="images/' + value + '.png" alt="' + value.slice(0, -8) + object.post_title + '" height="128" width="128"></label></div>';
			});
		});

		var selectedResult;
		var versionChartDiv;
		var versionChartId;
		// Check the iframe if the content is already loaded
		var timer = setInterval( function () {

			findInIFrame('#charts').append(output);
			findInIFrame('input:radio').hide();
			findInIFrame('input:radio').on('click', function (event) {
				findInIFrame('.selected').removeClass();
				$(this).next().addClass('selected');
				selectedResult = $(this).val();
				versionChartDiv = $(event.target).closest('div.versionCharts');
				versionChartId = versionChartDiv.length ? versionChartDiv.data('id') : null;
			});

			findInIFrame('#add-result-to-editor').on('click', function (event) {
				if (selectedResult) {
					$('#collection_outro_content_ifr').contents().find('#tinymce').append('[kwps_result ' + (versionChartDiv.length ? 'level=version id=' + versionChartId + ' ' : ('')) + 'result='+ selectedResult + ']');
					var textarea = $('#collection_outro_content');
					var newText = textarea.val() + '[kwps_result ' + (versionChartDiv.length ? 'level=version id=' + versionChartId + ' ' : ('')) + 'result=' + selectedResult + ']';
					textarea.val(newText);
					tb_remove();
				} else {
					alert('Please select a result view to import');
				}
			});

			if (findInIFrame('#charts').length > 0) {
				clearInterval(timer);
			}
		}, 100); //end timer

		return false;
	});

	var findInIFrame = function (element) {
		return $('iframe').contents().find(element);
	};

	//if test is published there's a textarea on this page instead of tiny mce
	//this sets the textarea size to show the content properly
	var textarea = $('#publishedTextarea');
	if ( textarea.length > 0 ) {
		var parentLength = textarea.closest('div.kwps-content-editor').innerWidth();

		var height = 20;
		textarea.val().split('\n').forEach(function (string, index, array) {
			height += 19;
		});

		textarea.innerWidth(parentLength);
		textarea.innerHeight(height);

	}

});
