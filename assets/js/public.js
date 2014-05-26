'use strict';

jQuery(function($) {

	$('.kwps-outro').hide();
	$('.kwps-question-group').hide();

	$.fn.pollPlugin = function ( options ) {
		return this.each( function ( ) {
			var elem = $( this );

			elem.find('.kwps-next').on('click', function () {
				elem.find('.kwps-intro').hide();
				elem.find('.kwps-outro').hide();
				elem.find('.kwps-question-group').show();
				console.log('next was clicked');
			});

			//  Search for the class with the ID in it
			elem.find('.kwps-question-group').on('click', '.kwps-next', function () {
				// Check if an answer option was selected
				// Get the name of the radio buttons
				var getNameOfRadioBtn = elem.find('input:radio').attr('name');
				// Get the value of the selected field
				var selected = elem.find('.kwps-answer-option input[type="radio"][name=' + getNameOfRadioBtn + ']:checked').val();
				console.log("selected:", selected);
				if (selected) {
			    var urlSaveEntry = $('.admin-url').val() + "admin-ajax.php?action=kwps_save_entry";
			    var urlGetChartData = $('.admin-url').val() + "admin-ajax.php?action=kwps_get_result_of_version";

			    var entry = {
				  		"post_parent": selected,
				  		"_kwps_sort_order": 0
				  	};
				  var getChart = {};

				  	$.ajax({
						    type: "POST",
						    url: urlSaveEntry,
						    data: JSON.stringify(entry),
						    contentType: "application/json; charset=utf-8",
						    dataType: "json",
						    success: function(data) {
							  	if (data) {
							  		getChart.ID = data.ID;
							  		getChart.output_type ='bar-chart-per-question';
							  		console.log('eerste call gelukt');
									} else {
										alert('error in data callback');
									}
						    },
						    failure: function(errMsg) {
						        alert(errMsg);
						    }
						});
				  		$.ajax({
					  			type: "POST",
					  			url: urlGetChartData,
					  			data: JSON.strinify(getChart),
					  			contentType: "application/json; charset=utf-8",
					  			dataType: "json",
					  			success: function (data) {
						    	console.log(data);
						    	var graphCategories = [];
						    	var graphData = [];
						    	$.each(data.entries, function(index, value) {
						    		graphCategories.push(value.answer_option_content);
						    		graphData.push(Math.round((value.entry_count/data[0].total_entries)*100));
						    	});
						    	console.log(getChart);
						    	// BAR CHART CODE
						    	elem.find('.kwps-chart').highcharts({
						            chart: {
						                type: 'bar'
						            },
						            title: {
						                text: data[1].poll_question
						            },
						            xAxis: {
						                categories: graphCategories,
						                title: {
						                    text: null
						                }
						            },
						            yAxis: {
						            	max: 100,
						                min: 0,
						                title: {
						                    text: 'percent',
						                    align: 'high'
						                },
						                labels: {
						                    overflow: 'justify'
						                }
						            },
						            tooltip: {
						                valueSuffix: ' %'
						            },
						            plotOptions: {
						                bar: {
						                    dataLabels: {
						                        enabled: true
						                    }
						                }
						            },
						            exporting: {
												    enabled: false
												},
						            legend: {
						                enabled: false
						            },
						            credits: {
						                enabled: false
						            },
						            series: [{
						            		name: 'Votes',
						                data: graphData
						            }]
						        });
							    },
				  		});
					elem.find('.kwps-intro').hide();
					elem.find('.kwps-question-group').hide();
					elem.find('.kwps-outro').show();
				} else {
					alert('Please select an answer!');
				}
				
			});
			
		});

	};

	// TODO: Has to be attached to the id of every class 'kwps_poll'
	$('.kwps-version').pollPlugin();
});