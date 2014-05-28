'use strict';

jQuery(function($) {

	$('.kwps-outro').hide();
	$('.kwps-question-group').hide();

	$.fn.pollPlugin = function ( options ) {
		return this.each( function ( ) {
			var elem = $( this );
			if (elem.find('.kwps-intro').length == 0) {
				if (elem.find('.kwps-question-group').length == 0) {
					elem.find('.kwps-outro').show();
				} else {
					elem.find('.kwps-question-group').show();
				}
			} else {
				elem.find('.kwps-intro').show();
				elem.find('.kwps-question-group').hide();
			}
			elem.find('.kwps-next').on('click', function () {
				elem.find('.kwps-intro').hide();
				elem.find('.kwps-outro').hide();
				elem.find('.kwps-question-group').show();
			});

			//  Search for the class with the ID in it
			elem.find('.kwps-question-group').on('click', '.kwps-next', function () {
				// Check if an answer option was selected
				// Get the name of the radio buttons
				var getNameOfRadioBtn = elem.find('input:radio').attr('name');
				// Get the value of the selected field
				var selected = elem.find('.kwps-answer-option input[type="radio"][name=' + getNameOfRadioBtn + ']:checked').val();
				if (selected) {
			    var urlSaveEntry = $('.admin-url').val() + "admin-ajax.php?action=kwps_save_entry";
			    var urlGetChartData = $('.admin-url').val() + "admin-ajax.php?action=kwps_get_result_of_version";

			    var entry = {
				  		"post_parent": selected,
				  		"post_status": "publish",
				  		"_kwps_sort_order": 0
				  	};
				  var getChart = {
				  		ID : '',
				  		output_type : ''
				  };

			  	$.ajax({
					    type: "POST",
					    url: urlSaveEntry,
					    data: JSON.stringify(entry),
					    contentType: "application/json; charset=utf-8",
					    dataType: "json",
					    success: function(data) {
					    	console.log(data.data);
					  		getChart.ID = data.data.ID;
					  		getChart.output_type ='bar-chart-per-question';
								getChartData(urlGetChartData, getChart);
					    },
					    failure: function (errMsg) {
					        alert(errMsg);
					    }
					});
					var getChartData = function (urlGetChartData, getChart) {
							$.ajax({
						  			type: "POST",
						  			url: urlGetChartData,
						  			data: JSON.stringify(getChart),
						  			contentType: "application/json; charset=utf-8",
						  			dataType: "json",
						  			success: function (data) {
							    	var graphCategories = [];
							    	var graphData = [];
							    	var totalEntries = data[0][0].total_entries;
							    	$.each(data[0].entries, function(index, value) {
							    		graphCategories.push(value.answer_option_content);
							    		graphData.push(Math.round((value.entry_count/totalEntries)*100));
							    	});
							    	outputPieChart(data, graphCategories, graphData);
							    	
								    },
								    async: false
					  		});
						elem.find('.kwps-intro').hide();
						elem.find('.kwps-question-group').hide();
						elem.find('.kwps-outro').show();
					};
					var disableChartSettings = function () {
						return {
									exporting: {
									    enabled: false
									},
							    legend: {
							        enabled: false
							    },
							    credits: {
							        enabled: false
							    }
							  }
					}
					/* BAR CHART CODE */
					var outputBarChart = function (data, graphCategories, graphData) {
						elem.find('.kwps-chart.chart-bar').highcharts({
						    chart: {
						        type: 'bar'
						    },
						    title: {
						        text: data[0][1].poll_question
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
						    // disableChartSettings(),
						    series: [{
						    		name: 'Votes',
						        data: graphData
						    }]
						});
					};
					/* PIE CHART CODE */
					var outputLineChart = function (data, graphCategories, graphData) {
						elem.find('.kwps-chart.chart-line').highcharts({
	            title: {
	              text: data[0][1].poll_question
	            },
	            xAxis: {
	              categories: graphCategories,
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
				        },
	              plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	              }]
	            },
	            tooltip: {
	              valueSuffix: ' %'
	            },
	        		// disableChartSettings(),
	            series: [{
	              name: 'Votes',
	              data: graphData
	            }]
		        });
					};
					var outputPieChart = function(data, graphCategories, graphData) {
						elem.find('.kwps-chart').highcharts({
			        chart: {
		            plotBackgroundColor: null,
		            plotBorderWidth: null,
		            plotShadow: false
			        },
			        title: {
		            text: data[0][1].poll_question
			        },
			        tooltip: {
			    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			        },
			        plotOptions: {
		            pie: {
	                allowPointSelect: true,
	                cursor: 'pointer',
	                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                      color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
	                }
		            }
			        },
			        // disableChartSettings(),
			        series: [{
		            type: 'pie',
		            name: 'Browser share',
		            data: graphData
			        }]
			    });
					};
				} else {
					alert('Please select an answer!');
				}
				
			});
			
		});

	};

	// TODO: Has to be attached to the id of every class 'kwps_poll'
	$('.kwps-version').pollPlugin();
});