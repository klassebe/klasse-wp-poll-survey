'use strict';

jQuery(function($) {
	$('.kwps-page').hide();
	// $('.kwps-outro').hide();
	// $('.kwps-question-group').hide();

	$.fn.pollPlugin = function ( options ) {
		return this.each( function ( ) {
			var elem = $( this );
			if (elem.find('.kwps-intro').length == 0) {
				if (elem.find('.kwps-question-group').length == 0) {
					elem.find('.kwps-outro').show();
				} else {
					// elem.find('.kwps-question-group').show();
					$(this).closest('.kwps-page').next().show();
				}
			} else {
				elem.find('.kwps-intro').show();
				// elem.find('.kwps-question-group').hide();
			}
			elem.find('.kwps-next').on('click', function () {
				elem.find('.kwps-intro').hide();
				elem.find('.kwps-outro').hide();
				// Check if there is already a question group being showed
				var questGroup = $(this).closest('.kwps-page');

				if (questGroup.closest('.kwps-question-group').css('display', 'none')) {
					questGroup.next().closest('.kwps-question-group').show();
				} 
			});

			//  Search for the class with the ID in it
			elem.find('.kwps-question-group').on('click', '.kwps-next', function () {

				// Check if an answer option was selected
				// Get the name of the radio buttons
				// var getNameOfRadioBtn = elem.find('input:radio').attr('name');
				/* GET NUMBER OF KWPS ANSWER OPTION DIVs WITHIN THIS KWPS QUESTION GROUP PAGE */
				var that = $(this);
				var questionGroup = that.closest('.kwps-question-group');
				var questionsLen = questionGroup[0].children[1].children.length;
				var questions = questionGroup[0].children[1].children;
				var questionVal = [];
				for (var i = 0; i < questionsLen; i++) {
					questionVal.push(questions[i].children[0]);
				}

				var kwpsAnswerOptions = questionVal;
				var kwpsAnswerOptionsLen = questionVal.length;
				var selected = true;
				var entries = [];
				var getQuestionName, oneSelectCheck;

				//  ONLY CHECK CURRENT VISIBLE PAGE
				for (i = 0; i < kwpsAnswerOptionsLen; i++) {
					/* CHECK FIELD ATTR AND IF ONE OF THEM WAS CHECKED */
					//  Gets the question name per answer option to later check if it has a checked value per name
					getQuestionName = kwpsAnswerOptions[i].children[0].children[0].firstChild.name;
					oneSelectCheck = elem.find('input[type="radio"][name=' + getQuestionName + ']:checked').val();
					if (oneSelectCheck) {
						entries.push({
				  		"post_parent": oneSelectCheck,
				  		"post_status": "publish",
				  		"_kwps_sort_order": 0
				  	});
						selected = true;
					} else {
						selected = false;
						break;
					}
				}

				if (selected) {
					elem.find('.kwps-page').hide;

					// $(this).hide();
					// $(this).next().show();
			    var urlSaveEntry = $('.admin-url').val() + "admin-ajax.php?action=kwps_save_entry";
			    var urlGetChartData = $('.admin-url').val() + "admin-ajax.php?action=kwps_get_result_of_version";
				  var getChart = {
				  		ID : '',
				  		output_type : ''
				  };

			  	$.ajax({
					    type: "POST",
					    url: urlSaveEntry,
					    data: JSON.stringify(entries),
					    contentType: "application/json; charset=utf-8",
					    dataType: "json",
					    success: function(data) {
					  		getChart.ID = data[0].ID;
					  		getChart.output_type ='bar-chart-per-question';
								getChartData(urlGetChartData, getChart);
					    },
					    failure: function (errMsg) {
					        alert(errMsg);
					    }
					});
					// Check if there still is a view open that needs to be sent to the DB,
					// If so, then add the variables to an array and then after all is done,
					// get the results from the DB and show outro
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
                    if (elem.find('.bar-chart')) {
                      outputBarChart(data, graphCategories, graphData);
                    }
                    if (elem.find('.pie-chart')) {
                      outputPieChart(data, graphCategories, graphData);
                    }
                    if (elem.find('.line-chart')) {
                      outputLineChart(data, graphCategories, graphData);
                    }
							    	
							    	
								    },
								    async: false
					  		});
						
						elem.find('.kwps-intro').hide();

						//  Check if all kwps question group divs are hidden
						var questGroupItems = elem.find('.kwps-question-group');
						var questGroupLen = elem.find('.kwps-question-group').length;

						if (questGroupItems) {
							for (var i = 0; i < questGroupLen; i++) {
								console.log(questGroupItems[i].style.cssText);
								if (questGroupItems[i].style.cssText === 'display: block;') {
									elem.find('.kwps-outro').hide();
									break;
								} else {
									elem.find('.kwps-outro').show();
								}
							}
						} 
					};

					/* BAR CHART CODE */
					var outputBarChart = function (data, graphCategories, graphData) {
						elem.find('.kwps-result.bar-chart').highcharts({
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
					};
					/* PIE CHART CODE */
					var outputLineChart = function (data, graphCategories, graphData) {
						elem.find('.kwps-result.line-chart').highcharts({
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
					};
					var outputPieChart = function(data, graphCategories, graphData) {
						elem.find('.kwps-result.pie-chart').highcharts({
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
		            type: 'pie',
		            name: 'Browser share',
		            data: graphData
			        }]
			    });
					};
          var outputQuizRespons = function (data, answer) {
            elem.find('.kwps-result.quiz-respons').html(data.respons);
            if (answer) {
              elem.find('.kwps-result.quiz-respons').html(data.answer);
            }
          }
          var outputMedianScore = function () {
            elem.find('.kwps-result.');
          }
				} else {
					elem.find('.kwps-page').hide();
					$(this).closest('.kwps-question-group').show();
					alert('Please select an answer for every question!');
				}
				
			});
			
		});

	};

	// TODO: Has to be attached to the id of every class 'kwps_poll'
	$('.kwps-version').pollPlugin();
});