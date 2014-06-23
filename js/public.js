(function () {
  "use strict";


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

    /* GENERAL AJAX ADMIN URL WITH ACTION KEYWORD INCLUDED */
    // get admin url and strip 'undefined' if it holds it

    var urlAjaxToAdmin = String($('.admin-url').val()).replace('undefined', '') + "admin-ajax.php?action=";
    console.log($('.admin-url').val());

    /* AVAILABLE SERVER ACTIONS */
    var actionSaveEntry = "kwps_save_entry";
    var actionGetVersionResultByEntryId = "kwps_get_result_of_version_by_entry_id";
    var actionGetVersionResult = "kwps_get_result_of_version";

    var urlGetDataForChart = urlAjaxToAdmin + actionGetVersionResult;
    var urlGetDataForChartByEntryId = urlAjaxToAdmin + actionGetVersionResultByEntryId;
    var actionGetProfileResult = "kwps_get_result_profile";

    /* VERSION ID */
    var versionId = elem.find('.kwps-version-id').val();

    /* OBJECT THAT WILL BE PASSED THROUGHOUT THE ACTION CALLS */
    var entryData = {
      ID : '',
      output_type : ''
    };
    /* OTHER SCOPE VARS */
    var selected = false;
    var entries = [];

    var saveEntry = function (entry) {
      if (selected) {
        elem.find('.kwps-page').hide();

        /* FIRST AJAX CALL TO SAVE ENTRY */
        $.ajax({
          type: "POST",
          url: urlAjaxToAdmin + actionSaveEntry,
          data: JSON.stringify(entry),
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
        // Here come all the functions

      } else {
        elem.find('.kwps-page').hide();
        $(this).closest('.kwps-question-group').show();
        alert('Please select an answer for every question!');
      }
    };

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
                getData(entryData, urlGetDataForChartByEntryId);
              } else {
                getRawData(entryData);
              }
            });
            elem.find('.kwps-outro').show();
          }
        }
      }
    };

    var getResultsByVersionId = function (versionId) {
      entryData.ID = versionId;
      // Start doing the requests per div class and set it as output type
      $.each(elem.find('.kwps-result'), function (key, value) {
        entryData.output_type = value.classList[1];
        if (entryData.output_type.match('chart')) {
          getData(entryData, urlGetDataForChart);
        } else {
          getRawData(entryData);
        }
      });
    };

    var getData = function (versionData, urlGetDataForChart) {
        $.ajax({
          type: "POST",
          url: urlGetDataForChart,
          data: JSON.stringify(versionData),
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function (data) {
              elem.find('.'+entryData.output_type).highcharts(data);
            },
            async: false
          });
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
            elem.find('.'+entryData.output_type).html(data[0].message);
          } else {
            elem.find('.'+entryData.output_type).html(data.post_content);
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
    };
    // TODO: When Median scores are necessary make this function
    var outputMedianScore = function () {
      // elem.find('.kwps-result');
    };

    /* CHECK WHICH PAGE NEEDS TO BE DISPLAYED */
      // Hidden input field with version id value is 1
      console.log(elem.find('.kwps-page'));
    if (elem.find('.kwps-page').length !== 0) {
      elem.find('.kwps-page').first().show();
      if (elem.find('.kwps-intro-result').length !== 0) {
        getResultsByVersionId(versionId);
      }
      //   if (elem.find('.kwps-intro').length === 0) {
      //     if (elem.find('.kwps-question-group').length === 0) {
      //       elem.find('.kwps-outro').show();
      //     } else {
      //       elem.find('.kwps-page').first().show();
      //     }
      //   } else {
      //     elem.find('.kwps-intro').show();
      //   }
    } else {
      elem.html('<div class="id-not-found">ID NOT FOUND!</div>');
    }

    /* CLICK EVENTS */
    //  Search for the class with the ID in it
    elem.find('.kwps-question-group').on('click', '.kwps-next', function () {
      /* GET NUMBER OF KWPS ANSWER OPTION DIVs WITHIN THIS KWPS QUESTION GROUP PAGE */
      var that = $(this);
      var questionGroup = that.closest('.kwps-question-group');
      var questionsLen = questionGroup.find('.kwps-question').length;
      // var questionsLen = questionGroup[0].children[1].children.length;
      // Get all questions from question group
      console.log(questionGroup);
      console.log('++++++++++++++++++++++');
      var questions = questionGroup.find('.kwps-question');
      var questionsSib = questions.siblings();
      // var questions = questionGroup[0].children[1].children;
      var answerVal = [];
      console.log(questionsSib);
      console.log('----------------');

      var answerOptions = questions.find('.kwps-answer-option');
      var answerOptionsSib = answerOptions.siblings();
      console.log(answerOptions);
      console.log('*********************');

      var getQuestionName, oneSelectCheck;
      var _kwps_hash = GetURLParameter('kwps_hash');

      //  ONLY CHECK CURRENT VISIBLE PAGE
        /* CHECK FIELD ATTR AND IF ONE OF THEM WAS CHECKED */
        //  Gets the question name per answer option to later check if it has a checked value per name

        getQuestionName = answerOptions.find('input[type="radio"]').attr('name');

        oneSelectCheck = elem.find('input[type="radio"][name=' + getQuestionName + ']:checked').val();
        if (oneSelectCheck) {
          var data = {
            "post_parent": oneSelectCheck,
            "post_status": "draft",
            "_kwps_sort_order": 0
          };

          if(_kwps_hash) {
            data._kwps_hash = _kwps_hash;
          }

          entries.push(data);
          selected = true;
          saveEntry(entries);
        } else {
          selected = false;
          alert('Please select a value per question.');
        }

    });

  elem.find('.kwps-page').on('click', '.kwps-next' , function () {
    $(this).closest('.kwps-page').hide();
    $(this).closest('.kwps-page').next().show();
  });

});

};

$('.kwps-version').pollPlugin();
});

}());
