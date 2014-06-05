'use strict';

function GetURLParameter(sParam) {
  var sPageURL = window.location.search.substring(1);
  var sURLVariables = sPageURL.split('&');
  for (var i = 0; i < sURLVariables.length; i++)
  {
    var sParameterName = sURLVariables[i].split('=');
    if (sParameterName[0] === sParam)
    {
      return sParameterName[1];
    }
  }
}

jQuery(function($) {
	$('.kwps-page').hide();

	$.fn.pollPlugin = function ( options ) {
		return this.each( function ( ) {
			var elem = $( this );
			if (elem.find('.kwps-intro-result').length === 0) {
				if (elem.find('.kwps-intro').length == 0) {
					if (elem.find('.kwps-question-group').length == 0) {
						elem.find('.kwps-outro').show();
					} else {
						elem.find('.kwps-question-group').show();
					}
				} else {
					elem.find('.kwps-intro').show();
				}
			} else {
				elem.find('.kwps-intro-result').show();
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
        var _kwps_hash = GetURLParameter('kwps_hash');

				//  ONLY CHECK CURRENT VISIBLE PAGE
				for (i = 0; i < kwpsAnswerOptionsLen; i++) {
					/* CHECK FIELD ATTR AND IF ONE OF THEM WAS CHECKED */
					//  Gets the question name per answer option to later check if it has a checked value per name
					getQuestionName = kwpsAnswerOptions[i].children[0].children[0].firstChild.name;
					oneSelectCheck = elem.find('input[type="radio"][name=' + getQuestionName + ']:checked').val();
					if (oneSelectCheck) {
            var data = {
              "post_parent": oneSelectCheck,
              "post_status": "publish",
              "_kwps_sort_order": 0
            };

            if(_kwps_hash) {
              data._kwps_hash = _kwps_hash;
            }

						entries.push(data);
						selected = true;
					} else {
						selected = false;
						break;
					}
				}

				if (selected) {
					elem.find('.kwps-page').hide;

					/* GENERAL AJAX ADMIN URL WITH ACTION KEYWORD INCLUDED */
					var urlAjaxToAdmin = $('.admin-url').val() + "admin-ajax.php?action=";

					/* AVAILABLE SERVER ACTIONS */
			    var actionSaveEntry = "kwps_save_entry";
			    var actionGetVersionResult = "kwps_get_result_of_version";
			    var actionGetBarChartResult = "kwps_get_bar_chart_per_question";
			    var actionGetStackedBarChartResult = "kwps_get_stacked_bar_chart_per_question_group";
			    var actionGetProfileResult = "kwps_get_result_profile";

			    /* OBJECT THAT WILL BE PASSED THROUGHOUT THE ACTION CALLS */
				  var entryData = {
				  		ID : '',
				  		output_type : ''
				  };

				  /* FIRST AJAX CALL TO SAVE ENTRY */
			  	$.ajax({
					    type: "POST",
					    url: urlAjaxToAdmin + actionSaveEntry,
					    data: JSON.stringify(entries),
					    contentType: "application/json; charset=utf-8",
					    dataType: "json",
					    success: function(data) {
					  		entryData.ID = data[0].ID;
								getResults(entryData);
					    },
					    failure: function (errMsg) {
					        alert(errMsg);
					    }
					});

					// Check if there still is a view open that needs to be sent to the DB,
					// If so, then add the variables to an array and then after all is done,
					// get the results from the DB and show outro
					var getResults = function (entryData) {

						var questGroupItems = elem.find('.kwps-question-group');
						var questGroupLen = elem.find('.kwps-question-group').length;

						//  Check if all kwps question group divs are hidden
						if (questGroupItems) {
							for (var i = 0; i < questGroupLen; i++) {
								if (questGroupItems[i].style.cssText === 'display: block;') {
									elem.find('.kwps-outro').hide();
									break;
								} else {
									// Start doing the requests per div class and set it as output type
									$.each(elem.find('.kwps-result'), function (key, value) {
										entryData.output_type = value.classList[1];
										if (entryData.output_type.match('chart')) {
											getChart(entryData);
										} else {
											getRawData(entryData);
										}
									});
									elem.find('.kwps-outro').show();
								}
							}
						}
					};

					var getChart = function (entryData) {
						var urlGetDataForChart;
						switch (entryData.output_type) {
							case 'bar-chart-per-question':
								urlGetDataForChart = urlAjaxToAdmin + actionGetBarChartResult;
								break;
							case 'pie-chart-per-question':
								urlGetDataForChart = urlAjaxToAdmin + actionGetPieChartResult;
								break;
							case 'stacked-bar-chart-per-question-group':
								urlGetDataForChart = urlAjaxToAdmin + actionGetStackedBarChartResult;
								break;
							default:
								alert('No defined chart was found!');
						}

						if (urlGetDataForChart) {
							$.ajax({
				  			type: "POST",
				  			url: urlGetDataForChart,
				  			data: JSON.stringify(entryData),
				  			contentType: "application/json; charset=utf-8",
				  			dataType: "json",
				  			success: function (data) {
				  				// The class is passed through output type
				  				// so look for that div and append the highchart to it
				  				console.log(JSON.stringify(data));
				  				console.log(data);
                  elem.find('.'+entryData.output_type).highcharts(data);
						    },
						    async: false
				  		});
						}
						elem.find('.kwps-intro').hide();
					};
					var getRawData = function (entryData) {
						$.ajax({
							type: "POST",
							url: urlAjaxToAdmin + actionGetProfileResult,
							data: JSON.stringify(entryData),
							contentType: "application/json; charset=utf-8",
							dataType: "json",
							success: function (data) {
								if (data[0] && data[0].message) {
									elem.find('.'+entryData.output_type).text(data[0].message);
								} else {
									elem.find('.'+entryData.output_type).text(data['post_content']);
								}
								
							},
							async: false
						});
						elem.find('.kwps-intro').hide();
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

	$('.kwps-version').pollPlugin();
});