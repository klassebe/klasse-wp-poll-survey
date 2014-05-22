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
			elem.find('#kwps-content').on('click', '.kwps-next', function () {
				// Check if an answer option was selected
				// Get the name of the radio buttons
				var getNameOfRadioBtn = elem.find('input:radio').attr('name');
				// Get the value of the selected field
				var selected = elem.find('.kwps-answer-option input:radio[name=' + getNameOfRadioBtn + ']').val();
				console.log("selected:", selected);
				if (selected) {
			    var url = $('#adminUrl').val() + "admin-ajax.php?action=kwps_save_entry";

			    var entry = {
				  		"post_parent": selected,
				  		"_kwps_sort_order": 0
				  	};

				  	$.ajax({
						    type: "POST",
						    url: url,
						    data: JSON.stringify(entry),
						    contentType: "application/json; charset=utf-8",
						    dataType: "json",
						    success: function(data){
						    	// alert(data);
						    	console.log(entry);
						    	// TODO: no data is returned from server!
						    	console.log(data);
						    	var graphCategories = [];
						    	var graphData = [];
						    	$.each(data.entries, function(index, value) {
						    		graphCategories.push(value.answer_option_content);
						    		graphData.push(Math.round((value.entry_count/data[0].total_entries)*100));
						    	});

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
						    failure: function(errMsg) {
						        alert(errMsg);
						    }
						});
				}
				$('.kwps-intro').hide();
				$('.kwps-outro').show();
				$('.kwps-question-group').hide();
			});
			
		});

	};

	// TODO: Has to be attached to the id of every class 'kwps_poll'
	$('.kwps-version').pollPlugin();
});