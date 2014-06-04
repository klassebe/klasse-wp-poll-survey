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

			    var urlSaveEntry = $('.admin-url').val() + "admin-ajax.php?action=kwps_save_entry";
			    var urlGetVersionResult = $('.admin-url').val() + "admin-ajax.php?action=kwps_get_result_of_version";
			    var urlGetProfileResult = $('.admin-url').val() + "admin-ajax.php?action=kwps_get_result_profile";
				  var entryData = {
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
										if (entryData.output_type.match('/chart/')) {
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
							$.ajax({
					  			type: "POST",
					  			url: urlGetVersionResult,
					  			data: JSON.stringify(entryData),
					  			contentType: "application/json; charset=utf-8",
					  			dataType: "json",
					  			success: function (data) {
					  				// The class is passed through output type
					  				// so look for that div and append the highchart to it
                    elem.find('.'+entryData.output_type).highcharts(data);
							    },
							    async: false
					  		});			
						elem.find('.kwps-intro').hide();
					};
					var getRawData = function (entryData) {
						$.ajax({
							type: "POST",
							url: urlGetProfileResult,
							data: JSON.stringify(entryData),
							contentType: "application/json; charset=utf-8",
							dataType: "json",
							success: function (data) {
								elem.find('.'+entryData.output_type).text(data[0].message);
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