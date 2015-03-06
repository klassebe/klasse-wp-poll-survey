console.log('admin results js file loaded'); 
jQuery(function ($){
	var url = kwpsInfo.siteurl + "admin-ajax.php?action=kwps_get_result_of_version";
	var elem = $('#kwps-results-container');
	var errorElement = $('<div class="error form-invalid"><p><h2>Error:</h2> <span></span></p></div>');

	$('#showResultVersion').change(function (event) {
		var value = getValueOfSelectBox($(event.currentTarget));
		console.info('Changed select box to version ' + value);
		getDataAndShowPieChart(value);
	});

	function init () {
		var value = getValueOfSelectBox();
		console.info('Select box has active version ' + value);
		getDataAndShowPieChart(value);
	}

	function getValueOfSelectBox (currentTarget) {
		var target = currentTarget || $('#showResultVersion');
		return target.val();
	}

	function getDataAndShowPieChart (version) {
		elem.text('loading ...');
		$.ajax({
	        type: "POST",
	        url: url,
	        data: JSON.stringify({ID:version,output_type:'pie-chart-per-question'}),
	        success: function (data) {
	        	if (data) {
	        		showPieChart(data);
	        	} else {
	        		elem.empty();
                    var errorText = errorElement.clone();
                    errorText.find('span').html("No entries yet.");
                    elem.html(errorText);

                }
	        },
	        error: function (data) {
	        	var errorText = errorElement.clone();
	        	errorText.find('span').html(data.responseText);
	        	elem.html(errorText);
	        },
	        dataType: "json",
	        contentType: "application/json",
	        processData: false
	    });
	}

	function showPieChart (data) {
		console.log(data);
		elem.highcharts(data);
	}

	init();
});
